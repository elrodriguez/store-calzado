<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\KardexSize;
use App\Models\LocalSale;
use App\Models\PaymentMethod;
use App\Models\Person;
use App\Models\PettyCash;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDocument;
use App\Models\SaleProduct;
use App\Models\Serie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use PDF;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales = (new Sale())->newQuery();

        $search = request()->input('search');

        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $sales->orderBy($attribute, $sort_order);
        } else {
            $sales->latest();
        }

        $current_date = Carbon::now()->format('Y-m-d');

        $isAdmin = Auth::user()->hasRole('admin');
        $sales = $sales->join('people', 'client_id', 'people.id')
            ->join('sale_documents', 'sale_documents.sale_id', 'sales.id')
            ->join('series', 'sale_documents.serie_id', 'series.id')
            ->select(
                'sales.id',
                'people.full_name',
                'total',
                'advancement',
                'total_discount',
                'payments',
                'sales.created_at',
                'sales.local_id',
                'sales.status',
                'series.description AS serie',
                'sale_documents.number'
            )
            ->whereDate('sales.created_at', '=', $current_date)
            ->when(!$isAdmin, function ($q) use ($search) {
                return $q->where('sales.user_id', Auth::id());
            })
            ->when($search, function ($q) use ($search) {
                return $q->whereRaw('CONCAT("series.description","-",sale_documents.number) = ?', [$search])
                    ->orWhere('people.full_name', 'like', '%' . $search . '%');
            })
            ->paginate(10)
            ->onEachSide(2);

        return Inertia::render('Sales/List', [
            'sales' => $sales,
            'filters' => request()->all('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $payments = PaymentMethod::all();
        $client = Person::find(1);
        $documentTypes = DB::table('identity_document_type')->get();
        return Inertia::render('Sales/Create', [
            'payments'      => $payments,
            'client'        => $client,
            'documentTypes' => $documentTypes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate(
            $request,
            [
                'total' => 'required|numeric|min:0|not_in:0',
                'payments.*.type' => 'required',
                'payments.*.amount' => 'required|numeric|min:0|not_in:0|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',
                'client.id' => 'required',
            ],
            [
                'payments.*.type.required' => 'Seleccione un tipo de pago',
                'payments.*.amount.required' => 'Ingrese monto',
            ]
        );

        try {
            $res = DB::transaction(function () use ($request) {

                date_default_timezone_set('America/Lima');

                $local_id = Auth::user()->local_id;
                $serie = Serie::where('document_type_id', '5')
                    ->where('local_id', $local_id)
                    ->first();

                $petty_cash = PettyCash::firstOrCreate([
                    'user_id' => Auth::id(),
                    'state' => 1,
                    'local_sale_id' => $local_id
                ], [
                    'date_opening' => Carbon::now()->format('Y-m-d'),
                    'time_opening' => date('H:i:s'),
                    'income' => 0
                ]);

                $serie_id = $serie->id;

                $sale = Sale::create([
                    'user_id' => Auth::id(),
                    'client_id' => $request->get('client')['id'],
                    'local_id' => $local_id,
                    'total' => $request->get('total'),
                    'advancement' => $request->get('total'),
                    'total_discount' => 0,
                    'payments' => json_encode($request->get('payments')),
                    'petty_cash_id' => $petty_cash->id
                ]);

                $serie = Serie::find($serie_id);

                $document = SaleDocument::create([
                    'sale_id' => $sale->id,
                    'serie_id' => $serie_id,
                    'number' => $serie->number
                ]);

                $serie->increment('number', 1);

                $products = $request->get('products');

                foreach ($products as $produc) {
                    SaleProduct::create([
                        'sale_id' => $sale->id,
                        'product_id' => $produc['id'],
                        'product' => json_encode($produc),
                        'price' => $produc['price'],
                        'discount' => $produc['discount'],
                        'quantity' => $produc['quantity'],
                        'total' => $produc['total']
                    ]);

                    $k = Kardex::create([
                        'date_of_issue' => Carbon::now()->format('Y-m-d'),
                        'motion' => 'sale',
                        'product_id' => $produc['id'],
                        'local_id' => $local_id,
                        'quantity' => - ($produc['quantity']),
                        'document_id' => $document->id,
                        'document_entity' => SaleDocument::class,
                        'description' => 'Venta'
                    ]);

                    KardexSize::create([
                        'kardex_id' => $k->id,
                        'product_id' => $produc['id'],
                        'local_id' => $local_id,
                        'size'      => $produc['size'],
                        'quantity'  => (-$produc['quantity'])
                    ]);

                    $product = Product::find($produc['id']);

                    $tallas = $product->sizes;
                    $n_tallas = [];
                    foreach (json_decode($tallas, true) as $k => $talla) {
                        if ($talla['size'] == $produc['size']) {
                            $n_tallas[$k] = array(
                                'size' => $talla['size'],
                                'quantity' => ($talla['quantity'] - $produc['quantity'])
                            );
                        } else {
                            $n_tallas[$k] = array(
                                'size' => $talla['size'],
                                'quantity' => $talla['quantity']
                            );
                        }
                    }
                    $product->update([
                        'sizes' => json_encode($n_tallas)
                    ]);
                    Product::find($produc['id'])->decrement('stock', $produc['quantity']);
                }
                return $sale;
            });

            return response()->json($res);
        } catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
        //return response()->json(['message' => 'Success']);

        // return redirect()->route('sales.index')
        //     ->with('message', __('Venta creada con éxito'));

    }

    public function destroy(Sale $sale)
    {
        try {
            $res = DB::transaction(function () use ($sale) {

                $sale->update(['status' => false]);

                $document = SaleDocument::where('sale_id', $sale->id)->first();

                $document->update([
                    'status' => false
                ]);

                $products = SaleProduct::where('sale_id', $sale->id)->get();

                foreach ($products as $produc) {

                    $k = Kardex::create([
                        'date_of_issue' => Carbon::now()->format('Y-m-d'),
                        'motion' => 'sale',
                        'product_id' => $produc->product_id,
                        'local_id' => $sale->local_id,
                        'quantity' => $produc->quantity,
                        'document_id' => $document->id,
                        'document_entity' => SaleDocument::class,
                        'description' => 'Anulacion de Venta'
                    ]);

                    KardexSize::create([
                        'kardex_id' => $k->id,
                        'product_id' => $produc->product_id,
                        'local_id' => $sale->local_id,
                        'size'      => json_decode($produc->product)->size,
                        'quantity'  => $produc->quantity
                    ]);

                    $product = Product::find($produc->product_id);

                    $tallas = $product->sizes;
                    $n_tallas = [];
                    foreach (json_decode($tallas, true) as $k => $talla) {
                        if ($talla['size'] == $produc['size']) {
                            $n_tallas[$k] = array(
                                'size' => $talla['size'],
                                'quantity' => ($talla['quantity'] + $produc->quantity)
                            );
                        } else {
                            $n_tallas[$k] = array(
                                'size' => $talla['size'],
                                'quantity' => $talla['quantity']
                            );
                        }
                    }
                    $product->update([
                        'sizes' => json_encode($n_tallas)
                    ]);

                    Product::find($produc->product_id)->increment('stock', $produc->quantity);
                }
                return $sale;
            });

            return redirect()->route('sales.index')
                ->with('message', 'Venta Anulado con éxito.');
        } catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
    }

    public function ticketPdf($id)
    {
        $sale = Sale::find($id);
        $document = SaleDocument::join('series', 'serie_id', 'series.id')
            ->select(
                'series.description',
                'sale_documents.number'
            )
            ->where('sale_documents.sale_id', $sale->id)
            ->first();
        $local = LocalSale::find($sale->local_id);
        $products = SaleProduct::where('sale_id', $sale->id)->get();

        $data = [
            'local' => $local,
            'sale' => $sale,
            'products' => $products,
            'document' => $document
        ];

        $file = public_path('ticket/') . 'ticket.pdf';
        $pdf = PDF::loadView('sales.ticket_pdf', $data);
        $pdf->setPaper(array(0, 0, 273, 500), 'portrait');
        $pdf->save($file);

        return response()->download($file);
    }

    public function printSalesDay($date)
    {
        $header = [
            'user_name'     => Auth::user()->name,
            'date'          => $date,
            'local_name'    => LocalSale::find(Auth::user()->local_id)->description
        ];

        $payments = PaymentMethod::all();

        $sales = SaleProduct::join('sales', 'sale_products.sale_id', 'sales.id')
            ->join('products', 'product_id', 'products.id')
            ->join('sale_documents', 'sale_documents.sale_id', 'sales.id')
            ->join('people', 'client_id', 'people.id')
            ->join('series', 'serie_id', 'series.id')
            ->join('users', 'sales.user_id', 'users.id')
            ->select(
                'sales.id',
                'products.description AS product_description',
                'products.interne',
                'series.description',
                'sale_documents.number',
                'sale_products.product',
                'sale_products.total AS product_total',
                'sales.total',
                'people.full_name',
                'sales.payments',
                'users.name AS user_name',
                DB::raw("(SELECT seller_name FROM petty_cashes WHERE petty_cashes.id=sales.petty_cash_id) AS seller_name")
            )
            ->where(function ($query) {
                if (!Auth::user()->hasRole('admin')) {
                    $query->where('sales.user_id', Auth::id());
                }
            })
            ->whereDate('sales.created_at', $date)
            ->where('sales.status', true)
            ->where('sales.local_id', Auth::user()->local_id)
            ->orderBy('sales.id')
            ->get();

        $products = SaleProduct::join('sales', 'sale_id', 'sales.id')
            ->join('products', 'product_id', 'products.id')
            ->select(
                'products.interne',
                'products.image',
                'products.description',
                'sale_products.product',
                'sale_products.price',
                'sale_products.discount',
                'sale_products.quantity',
                'sale_products.total'
            )
            ->whereDate('sales.created_at', $date)
            ->where(function ($query) {
                if (!Auth::user()->hasRole('admin')) {
                    $query->where('sales.user_id', Auth::id());
                }
            })
            ->where('sales.status', true)
            ->where('sales.local_id', Auth::user()->local_id)
            ->get();

        $status = false;
        //dd($products);
        if (count($sales) > 0) {
            $status = true;

            $file = public_path('ventas/') . 'ventas.pdf';

            $pdf = PDF::loadView('sales.sale_day', [
                'header' => $header,
                'sales' => $sales,
                'payments' => $payments,
                'products' => $products
            ]);

            $pdf->setPaper('A4', 'portrait');
            $pdf->save($file);

            return response()->download($file, 'ventas_' . $date . '.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        } else {
            return response()->json([
                'status' => $status,
                'message' => 'No existen Datos'
            ]);
        }
    }
}
