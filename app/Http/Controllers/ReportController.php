<?php

namespace App\Http\Controllers;

use App\Models\LocalSale;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use PDF;

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
    public function roport_sale()
    {
        return Inertia::render('Reports/List', [
            'filters' => request()->all('search'),
            'locals' => LocalSale::all(),
        ]);
    }

    public function roport_inventory()
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

        if ($start == $end) {
            $file = public_path('ticket/') . 'reporteVentas_' . $start . '.pdf';
        } else {
            $file = public_path('ticket/') . 'reporteVentas_' . $start . '_al_' . $end . '.pdf';
        }

        if ($download == "false") {
            return view('reports.sales_report', ['sales' => $sales, 'start' => $start, 'end' => $end]);
        } else {
            $pdf = PDF::loadView('reports.sales_report', ['sales' => $sales, 'start' => $start, 'end' => $end]);
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
        $date = new Date();

        if ($download == "false") {
            return view('reports.inventory_report', ['products' => $products, 'date' => $date]);
        } else {
            $file = public_path('ticket/') . 'reporteInventario_' . $date . '.pdf';
            $pdf = PDF::loadView('reports.sales_report', ['products' => $products, 'date' => $date]);
            // (Optional) Setup the paper size and orientation
            $pdf->setPaper('A4', 'landscape');
            $pdf->save($file);

            return response()->download($file);
        }
    }
}
