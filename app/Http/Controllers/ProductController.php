<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\LocalSale;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = (new Product())->newQuery();
        if (request()->has('search')) {
            $products->where('description', 'Like', '%' . request()->input('search') . '%');
        }
        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $products->orderBy($attribute, $sort_order);
        } else {
            $products->latest();
        }

        $products = $products->paginate(10)->onEachSide(2);

        return Inertia::render('Products/List', [
            'products' => $products,
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
        return Inertia::render('Products/Create', [
            'establishments' => LocalSale::all()
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
                'interne' => 'required|unique:products,interne',
                'description' => 'required',
                'purchase_prices' => 'required',
                'sale_prices.high' => 'required',
                'sizes.*.size' => 'required|numeric',
                'sizes.*.quantity' => 'required|numeric',
            ],
            [
                'sizes.*.size.required' => 'Ingrese Talla',
                'sizes.*.quantity.required' => 'Ingrese Cantidad',
            ]
        );
        $path = 'img/imagen-no-disponible.jpeg';
        $destination = 'uploads/products';
        $file = $request->file('image');
        if ($file) {
            $original_name = strtolower(trim($file->getClientOriginalName()));
            $file_name = time() . rand(100, 999) . $original_name;
            $path = $request->file('image')->storeAs(
                $destination,
                $file_name,
                'public'
            );
        }
        $total = 0;
        foreach ($request->get('sizes') as $k => $item) {
            $total = $total + $item['quantity'];
        }

        //dd($request->get('sale_prices'));
        $pr = Product::create([
            'usine' => $request->get('usine'),
            'interne'  => $request->get('interne'),
            'description'  => $request->get('description'),
            'image'  => $path,
            'purchase_prices'  => $request->get('purchase_prices'),
            'sale_prices'  => json_encode($request->get('sale_prices')),
            'sizes'  => json_encode($request->get('sizes')),
            'stock_min'  => 1,
            'stock'  => $total
        ]);

        Kardex::create([
            'date_of_issue' => Carbon::now()->format('Y-m-d'),
            'motion' => 'purchase',
            'product_id' => $pr->id,
            'local_id' => $request->get('local_id'),
            'quantity' => $total,
            'description' => 'Stock Inicial'
        ]);

        return redirect()->route('products.create')
            ->with('message', __('Producto creado con éxito'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //dd($request->all());
        $this->validate($request, [
            $request,
            [
                'interne' => 'required|unique:products,interne,' . $product->id,
                'description' => 'required',
                'purchase_prices' => 'required',
                'sale_prices.high' => 'required',
                'sizes.*.size' => 'required|numeric',
                'sizes.*.quantity' => 'required|numeric',
            ],
            [
                'sizes.*.size.required' => 'Ingrese Talla',
                'sizes.*.quantity.required' => 'Ingrese Cantidad',
            ]
        ]);

        $path = 'img/imagen-no-disponible.jpeg';
        $destination = 'uploads/products';
        $file = $request->file('image');

        if ($file) {
            $original_name = strtolower(trim($file->getClientOriginalName()));
            $file_name = time() . rand(100, 999) . $original_name;
            $path = $request->file('image')->storeAs(
                $destination,
                $file_name,
                'public'
            );
        }

        $total = 0;

        foreach ($request->get('sizes') as $k => $item) {
            $total = $total + $item['quantity'];
        }

        if ($total != Product::find($product->id)->stock) {
            Kardex::create([
                'date_of_issue' => Carbon::now()->format('Y-m-d'),
                'motion' => 'purchase',
                'product_id' => $product->id,
                'local_id' => 1,
                'quantity' => - (Product::find($product->id)->stock),
                'description' => 'Stock Modificado'
            ]);
        }


        //dd($request->get('sale_prices'));
        $product->update([
            'usine' => $request->get('usine'),
            'interne'  => $request->get('interne'),
            'description'  => $request->get('description'),
            'image'  => $path,
            'purchase_prices'  => $request->get('purchase_prices'),
            'sale_prices'  => json_encode($request->get('sale_prices')),
            'sizes'  => json_encode($request->get('sizes')),
            'stock_min'  => 1,
            'stock'  => $total
        ]);


        Kardex::create([
            'date_of_issue' => Carbon::now()->format('Y-m-d'),
            'motion' => 'purchase',
            'product_id' => $product->id,
            'local_id' => 1,
            'quantity' => $total,
            'description' => 'Stock Modificado'
        ]);

        return redirect()->route('products.edit', $product->id)
            ->with('message', __('Producto editado con éxito'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('message', __('Producto eliminado con éxito'));
    }

    public function searchProduct(Request $request)
    {
        $search = $request->get('search');
        $products = Product::where('interne', $search)
            ->orWhere('description', 'like', '%' . $search . '%')->get();
        // return  redirect()->back()->with('products', $products);
        return response()->json($products);

        // return Inertia('Sales/Create', [
        //     'products' => $products
        // ]);

        return response()->json($products);
    }

    public function showdetails($id)
    {
        $product = Product::where('id', $id)->first();
        return response()->json($product);
    }
}
