<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\LocalSale;
use App\Models\PaymentMethod;
use App\Models\PettyCash;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
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

        $date = date_time_format();
        $start = Carbon::parse($start)->format('d-m-Y');
        $end = Carbon::parse($end)->format('d-m-Y');
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
            $pdf->setPaper('A4', 'landscape');
            $pdf->save($file);

            return response()->download($file);
        }
    }

    public function sales_report_dates($start = null, $end = null, $local_id = 0, $consulta = false)
    {

        $date_ = date('Y-m-d');

        $date = date_time_format();
        $start = $start == null ? $date_ : $start;
        $end = $end == null ? $date_ : $end;
        $payments = PaymentMethod::all();

        if ($local_id == 0 || $local_id == null) {
            $sales = $this->getSales($start, $end);

            if ($consulta) {
                return response()->json([
                    'payments' => $payments,
                    'sales' => $sales
                ]);
            } else {
                return Inertia::render('Reports/SaleReportByDates', [
                    'locals' => LocalSale::all(),
                    'sales' => $sales,
                    'date' => $date,
                    'start' => $start,
                    'end' => $end,
                    'payments' => $payments,
                ]);
            }
        } else {
            $sales = $this->getSales($start, $end, $local_id);
            return response()->json([
                'payments' => $payments,
                'sales' => $sales
            ]);
        }

        // return view('reports.sales_report', ['sales' => $sales, 'start' => $start, 'end' => $end, 'date' => $date, 'print' => true]);

    }
    public function getSales($start, $end, $local_id = 0)
    {
        $sales = [];
        if ($local_id == 0) {
            $sales = Sale::join('local_sales', 'sales.local_id', 'local_sales.id')
                ->join('sale_products', 'sale_products.sale_id', 'sales.id')
                ->join('products', 'products.id', 'sale_products.product_id')
                ->select(
                    'sales.id',
                    'sales.created_at',
                    'sales.local_id',
                    'sales.payments',
                    'local_sales.description AS local_description',
                    'products.interne',
                    'products.description as product_description',
                    'products.image',
                    'sale_products.product as product'
                )
                ->whereDate('sales.created_at', '>=', $start)
                ->whereDate('sales.created_at', '<=', $end)
                ->where('sales.status', '=', 1)
                ->orderBy('sales.id')
                ->get();
        } else {
            $sales = Sale::join('local_sales', 'sales.local_id', 'local_sales.id')
                ->join('sale_products', 'sale_products.sale_id', 'sales.id')
                ->join('products', 'products.id', 'sale_products.product_id')
                ->select(
                    'sales.id',
                    'sales.created_at',
                    'sales.local_id',
                    'sales.payments',
                    'local_sales.description AS local_description',
                    'products.interne',
                    'products.description as product_description',
                    'products.image',
                    'sale_products.product as product'
                )
                ->whereDate('sales.created_at', '>=', $start)
                ->whereDate('sales.created_at', '<=', $end)
                ->where('sales.status', '=', 1)
                ->where('sales.local_id', '=', $local_id)
                ->orderBy('sales.id')
                ->get();
        }
        $arraySale = [];
        foreach ($sales as $k => $sale) {
            $arraySale[$k] = [
                'id'                        => $sale->id,
                'created_at'                => $sale->created_at,
                'local_id'                 => $sale->local_id,
                'payments'                  => $sale->payments,
                'interne'                   => $sale->interne,
                'product_description'       => $sale->product_description,
                'image'                     => $sale->image,
                'product'                   => $sale->product
            ];
        }

        return $arraySale;
    }

    public function PettyCashReport($petty_cash_id)
    {
        $petty_cash = PettyCash::find($petty_cash_id);
        $sales = Sale::join('local_sales', 'sales.local_id', 'local_sales.id')
            ->join('sale_products', 'sale_products.sale_id', 'sales.id')
            ->join('products', 'products.id', 'sale_products.product_id')
            ->select('sales.*', 'products.interne', 'products.description as product_description', 'products.image', 'sale_products.product as product')
            ->where('sales.petty_cash_id', '=', $petty_cash_id)
            ->where('sales.status', '=', 1)
            ->orderBy('id', 'desc')->orderBy('sale_products.id', 'desc')
            ->get();
        $expenses = Expense::where('petty_cash_id', $petty_cash_id)->get();

        return Inertia::render('Reports/PettyCashReport', [
            'locals' => LocalSale::all(),
            'sales' => $sales,
            'petty_cash' => $petty_cash,
            'date' => $petty_cash->date_opening . $petty_cash->time_opening,
            'start' => $petty_cash->date_closed,
            'end' => $petty_cash->date_opening,
            'expenses' => $expenses
        ]);
    }

    public function getImage($product_id)
    {
        return Product::where('id', $product_id)->select('image')->first()->image;
    }

    public function getLocal($local_id)
    {
        return LocalSale::where('id', $local_id)->select('description')->first()->description;
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
        //dd($products);
        date_default_timezone_set('America/Lima');
        $date = date('Y-m-d H:i:s');
        $year = date('Y'); //obtiene el año actual en formato de 4 dígitos
        $month = date('m'); //obtiene el mes actual en formato de 2 dígitos
        $day = date('d'); //obtiene el día actual en formato de 2 dígitos
        $time = date('H:i'); //obtiene la hora y los minutos actuales en formato de 24 horas separados por dos puntos
        $date = $day . "/" . $month . "/" . $year . " a las  " . $time;

        return view('reports.inventory_report_by_local', ['products' => $products, 'local' => $local, 'date' => $date, 'print' => true]);
    }

    public function reportPaymentMethodTotals()
    {
        return Inertia::render('Reports/PaymentMethodTotals', [
            'locals' => LocalSale::all(),
        ]);
    }
    public function dataPaymentMethodTotals(Request $request)
    {

        $payments = Sale::select('payments')->where('local_id', $request->input('local_id'))
            ->whereDate('created_at', '>=', $request->input('start'))
            ->whereDate('created_at', '<=', $request->input('end'))
            ->get();

        //$array = json_decode($payments, true);
        $sumas = array();

        // Recorrer los pagos
        foreach ($payments as $pago) {
            // Decodificar el pago como un array asociativo
            $datos_pago = json_decode($pago['payments'], true);

            // Obtener el tipo y el monto del pago
            $tipo_pago = $datos_pago[0]['type'];
            $monto_pago = $datos_pago[0]['amount'];

            // Si el tipo de pago no está en el array de sumas, inicializarlo en 0
            if (!isset($sumas[$tipo_pago])) {
                $sumas[$tipo_pago] = 0;
            }

            // Sumar el monto del pago al tipo de pago correspondiente
            $sumas[$tipo_pago] += $monto_pago;
        }

        // Imprimir las sumas de cada tipo de pago
        $c = 0;

        $payments_sum = [];
        foreach ($sumas as $tipo_pago => $suma) {
            $payments_sum[$c] = [
                'type' => $tipo_pago,
                'amount' => $suma
            ];
            $c++;
        }

        $method_payments = PaymentMethod::all();

        $array_payments = [];

        $total = 0;

        foreach ($method_payments as $key => $method) {
            $encontrado = false;
            foreach ($payments_sum as $payment) {
                if ($method->id == $payment['type']) {
                    $array_payments[$key] = [
                        'id'            => $method->id,
                        'description'   => $method->description,
                        'amount'        => $payment['amount']
                    ];
                    $total = $total + $payment['amount'];
                    $encontrado = true;
                    break;
                }
            }
            if (!$encontrado) {
                $array_payments[$key] = [
                    'id'            => $method->id,
                    'description'   => $method->description,
                    'amount'        => 0
                ];
            }
        }

        return response()->json([
            'payments'  => $array_payments,
            'total'     => $total
        ]);
    }
}
