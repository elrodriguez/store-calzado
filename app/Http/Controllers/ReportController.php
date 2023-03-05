<?php

namespace App\Http\Controllers;

use App\Models\LocalSale;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
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
        return Inertia::render('Reports/List');
    }

    public function sales_report()
    {
        return Inertia::render('Reports/SaleReport', [
            'filters' => request()->all('search'),
            'locals' => LocalSale::all(),
        ]);
    }

    public function sales_report_export($start, $end, $download)
    {
        $sales = Sale::whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)->orderBy('id', 'desc')->orderBy('created_at', 'desc')
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

    public function sales_report_dates($start = null, $end = null, $local_id = 0, $consulta = false)
    {

        date_default_timezone_set('America/Lima');
        $date = date('Y-m-d H:i:s');
        $date_ = date('Y-m-d');
        $year = date('Y'); //obtiene el año actual en formato de 4 dígitos
        $month = date('m'); //obtiene el mes actual en formato de 2 dígitos
        $day = date('d'); //obtiene el día actual en formato de 2 dígitos
        $time = date('H:i'); //obtiene la hora y los minutos actuales en formato de 24 horas separados por dos puntos
        $date = $day . "/" . $month . "/" . $year . " a las  " . $time;
        $start = $start == null ? $date_ : $start;
        $end = $end == null ? $date_ : $end;
        if ($local_id == 0 || $local_id == null) {
            $sales = $this->getSales($start, $end);

            if ($consulta) {
                return response()->json($sales);
            } else {
                return Inertia::render('Reports/SaleReportByDates', [
                    'locals' => LocalSale::all(),
                    'sales' => $sales,
                    'date' => $date,
                    'start' => $start,
                    'end' => $end,
                ]);
            }
        } else {
            $sales = $this->getSales($start, $end, $local_id);
            // $start = date('d-m-Y', strtotime($start));
            // $end = date('d-m-Y', strtotime($end));

            // $start = date('d/m/Y', strtotime($start));
            // $end = date('d/m/Y', strtotime($end));

            return response()->json($sales);
        }

        // return view('reports.sales_report', ['sales' => $sales, 'start' => $start, 'end' => $end, 'date' => $date, 'print' => true]);

    }
    public function getSales($start, $end, $local_id = 0)
    {
        if ($local_id==0) {
            return Sale::join('local_sales', 'sales.local_id', 'local_sales.id')
                ->join('sale_products', 'sale_products.sale_id', 'sales.id')
                ->join('products', 'products.id', 'sale_products.product_id')
                ->select('sales.*', 'products.interne', 'products.description as product_description', 'products.image', 'sale_products.product as product')
                ->whereDate('sales.created_at', '>=', $start)
                ->whereDate('sales.created_at', '<=', $end)
                ->orderBy('id', 'desc')->orderBy('sale_products.id', 'desc')
                ->get();
        } else {
            return Sale::join('local_sales', 'sales.local_id', 'local_sales.id')
                ->join('sale_products', 'sale_products.sale_id', 'sales.id')
                ->join('products', 'products.id', 'sale_products.product_id')
                ->select('sales.*', 'products.interne', 'products.description as product_description', 'products.image', 'sale_products.product as product')
                ->whereDate('sales.created_at', '>=', $start)
                ->whereDate('sales.created_at', '<=', $end)
                ->where('sales.local_id', '=', $local_id)
                ->orderBy('id', 'desc')->orderBy('sale_products.id', 'desc')
                ->get();
        }
    }
    public function getImage($product_id)
    {
        return Product::where('id', $product_id)->select('image')->get()->first()->image;
    }

    public function getLocal($local_id)
    {
        return LocalSale::where('id', $local_id)->select('description')->get()->first()->description;
    }

    public function inventory_report_export()
    {

        $products = Product::where('stock', '>', 0)->get();
        date_default_timezone_set('America/Lima');
        $date = date('Y-m-d H:i:s');
        $year = date('Y'); //obtiene el año actual en formato de 4 dígitos
        $month = date('m'); //obtiene el mes actual en formato de 2 dígitos
        $day = date('d'); //obtiene el día actual en formato de 2 dígitos
        $time = date('H:i'); //obtiene la hora y los minutos actuales en formato de 24 horas separados por dos puntos
        $date = $day . "/" . $month . "/" . $year . " a las  " . $time;

        return view('reports.inventory_report', ['products' => $products, 'date' => $date, 'print' => true]);

    }

    public function inventory_report_by_local($local_id)
    {
        $products = Product::where('products.stock', '>', 0)
            ->join('kardex_sizes', 'kardex_sizes.product_id', 'products.id')
            ->select('size', 'products.id', 'products.interne', 'products.description', 'products.sale_prices', 'products.purchase_prices', DB::raw('SUM(quantity) as quantity'))
            ->groupBy('size', 'products.id', 'products.interne', 'products.description', 'products.sale_prices', 'products.purchase_prices')
            ->where('kardex_sizes.local_id', '=', $local_id)
            ->orderBy('products.id', 'asc')
            ->orderBy('size', 'asc')
            ->get();
        $local = LocalSale::where('id', $local_id)->get()->first();
        //dd($local);
        date_default_timezone_set('America/Lima');
        $date = date('Y-m-d H:i:s');
        $year = date('Y'); //obtiene el año actual en formato de 4 dígitos
        $month = date('m'); //obtiene el mes actual en formato de 2 dígitos
        $day = date('d'); //obtiene el día actual en formato de 2 dígitos
        $time = date('H:i'); //obtiene la hora y los minutos actuales en formato de 24 horas separados por dos puntos
        $date = $day . "/" . $month . "/" . $year . " a las  " . $time;

        return view('reports.inventory_report_by_local', ['products' => $products, 'local' => $local, 'date' => $date, 'print' => true]);
    }
}
