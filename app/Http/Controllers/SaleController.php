<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\LocalSale;
use App\Models\PaymentMethod;
use App\Models\Person;
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

        if (request()->has('search')) {
            $sales->whereDate('sales.created_at', request()->input('search'));
        }
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

        $sales = $sales->join('people', 'client_id', 'people.id')
            ->select(
                'sales.id',
                'people.full_name',
                'total',
                'advancement',
                'total_discount',
                'payments',
                'sales.created_at',
                'local_id'
            )
            ->paginate(4)
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
        return Inertia::render('Sales/Create', [
            'payments' => $payments,
            'client' => $client
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
        //dd($request->all());
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

                $serie_id = 1;

                $sale = Sale::create([
                    'user_id' => Auth::id(),
                    'client_id' => $request->get('client')['id'],
                    'local_id' => 1,
                    'total' => $request->get('total'),
                    'advancement' => $request->get('total'),
                    'total_discount' => 0,
                    'payments' => json_encode($request->get('payments'))
                ]);

                $serie = Serie::find($serie_id);

                $document = SaleDocument::create([
                    'sale_id' => $sale->id,
                    'serie_id' => 1,
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
                        'discount' => 0,
                        'quantity' => $produc['quantity'],
                        'total' => $produc['total']
                    ]);

                    Kardex::create([
                        'date_of_issue' => Carbon::now()->format('Y-m-d'),
                        'motion' => 'sale',
                        'product_id' => $produc['id'],
                        'local_id' => 1,
                        'quantity' => - ($produc['quantity']),
                        'document_id' => $document->id,
                        'document_entity' => SaleDocument::class,
                        'description' => 'Venta'
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
            return response()->json(['message' => 'Error']);
        }
        //return response()->json(['message' => 'Success']);

        // return redirect()->route('sales.index')
        //     ->with('message', __('Venta creada con éxito'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        //
    }

    public function ticketPdf($id)
    {
        $sale = Sale::find($id);
        $document = SaleDocument::where('sale_id', $sale->id)->get();
        $local = LocalSale::find($sale->local_id);
        $products = SaleProduct::where('sale_id', $sale->id)->get();

        $data = [
            'local' => $local,
            'sale' => $sale,
            'products' => $products
        ];

        $file = public_path('ticket/') . 'ticket.pdf';
        $pdf = PDF::loadView('sales.ticket_pdf', $data);
        $pdf->setPaper(array(0, 0, 273, 500), 'portrait');
        $pdf->save($file);

        return response()->download($file);
    }
}
