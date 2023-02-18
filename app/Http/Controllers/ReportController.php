<?php

namespace App\Http\Controllers;

use App\Models\LocalSale;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use PDF;
use SebastianBergmann\Type\TrueType;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Reports/List', [
            'filters' => request()->all('search'),
            'locals' => LocalSale::all(),
        ]);
    }

    public function sales_report($start, $end, $download)
    {
        $sales = Sale::whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->get();

        date_default_timezone_set('America/Lima');
        $date = date('Y-m-d H:i:s');
        $year = date('Y'); //obtiene el año actual en formato de 4 dígitos
        $month = date('m'); //obtiene el mes actual en formato de 2 dígitos
        $day = date('d'); //obtiene el día actual en formato de 2 dígitos
        $time = date('H:i'); //obtiene la hora y los minutos actuales en formato de 24 horas separados por dos puntos
        $date = $day . "/" . $month . "/" . $year . " a las  " . $time;
        $start = date('d-m-Y', strtotime($start));
        $end = date('d-m-Y', strtotime($end));
        if ($start == $end) {
            $file = public_path('ticket/') . 'reporteVentas_' . $start . '.pdf';
        } else {
            $file = public_path('ticket/') . 'reporteVentas_' . $start . '_al_' . $end . '.pdf';
        }
        $start = date('d/m/Y', strtotime($start));
        $end = date('d/m/Y', strtotime($end));
        if ($download == "false") {
            return view('reports.sales_report', ['sales' => $sales, 'start' => $start, 'end' => $end, 'date' => $date, 'print' => true]);
        } else {
            $pdf = PDF::loadView('reports.sales_report', ['sales' => $sales, 'start' => $start, 'end' => $end, 'date' => $date, 'print' => false]);
            // (Optional) Setup the paper size and orientation
            $pdf->setPaper('A4', 'landscape');
            $pdf->save($file);

            return response()->download($file);
        }
    }

    public function getImage($product_id)
    {
        return Product::where('id', $product_id)->select('image')->get()->first()->image;
    }

    public function inventory_report($download)
    {

        $products = Product::where('stock', '>', 0)->get();
        date_default_timezone_set('America/Lima');
        $date = date('Y-m-d H:i:s');
        $year = date('Y'); //obtiene el año actual en formato de 4 dígitos
        $month = date('m'); //obtiene el mes actual en formato de 2 dígitos
        $day = date('d'); //obtiene el día actual en formato de 2 dígitos
        $time = date('H:i'); //obtiene la hora y los minutos actuales en formato de 24 horas separados por dos puntos
        $date = $day . "/" . $month . "/" . $year . " a las  " . $time;

        if ($download == "false") {
            return view('reports.inventory_report', ['products' => $products, 'date' => $date, 'print' => true]);
        } else {
            $date = $day . "-" . $month . "-" . $year;
            $file = public_path('ticket/') . 'reporteInventario_' . $date . '.pdf';
            $pdf = PDF::loadView('reports.inventory_report', ['products' => $products, 'date' => $date, 'print' => false]);
            // (Optional) Setup the paper size and orientation
            $pdf->setPaper('A4', 'landscape');
            $pdf->save($file);

            return response()->download($file);
        }
    }
}
