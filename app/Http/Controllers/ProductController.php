<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\KardexSize;
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
        $establishments = LocalSale::all();

        return Inertia::render('Products/List', [
            'products' => $products,
            'establishments' => $establishments,
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

        $k = Kardex::create([
            'date_of_issue' => Carbon::now()->format('Y-m-d'),
            'motion' => 'purchase',
            'product_id' => $pr->id,
            'local_id' => $request->get('local_id'),
            'quantity' => $total,
            'description' => 'Stock Inicial'
        ]);

        foreach ($request->get('sizes') as $row) {
            KardexSize::create([
                'kardex_id'         => $k->id,
                'product_id'        => $pr->id,
                'local_id'          => $request->get('local_id'),
                'size'              => $row['size'],
                'quantity'          => $row['quantity']
            ]);
        }

        return redirect()->route('products.create')
            ->with('message', __('Producto creado con éxito'));
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

        if ($total < Product::find($product->id)->stock) {
            Kardex::create([
                'date_of_issue' => Carbon::now()->format('Y-m-d'),
                'motion' => 'purchase',
                'product_id' => $product->id,
                'local_id' => 1,
                'quantity' => - (Product::find($product->id)->stock),
                'description' => 'Stock Modificado'
            ]);
        }

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
        return response()->json($products);
    }

    public function showdetails($id)
    {
        $product = Product::where('id', $id)->first();
        return response()->json($product);
    }

    public function saveInput(Request $request)
    {
        //dd($request->get('type'));
        $p_id = $request->get('product_id');
        $t_id = $request->get('type');
        $this->validate(
            $request,
            [
                'product_id' => 'required',
                'description' => 'required',
                'sizes.*.size' => ['required', 'size_existence:' . $p_id . ',' . $t_id],
                'sizes.*.quantity' => 'required|numeric',
            ],
            [
                'sizes.*.size.required' => 'Ingrese Talla',
                'sizes.*.quantity.required' => 'Ingrese Cantidad',
            ]
        );

        $product = Product::find($request->get('product_id'));
        $tallas = $product->sizes;
        $new_sizes = [];

        $t = 0;


        $kardex = Kardex::create([
            'date_of_issue' => Carbon::now()->format('Y-m-d'),
            'motion'        => $request->get('type') == 1 ? $request->get('motion') : 'Sale',
            'product_id'    => $request->get('product_id'),
            'local_id'      => $request->get('local_id'),
            'quantity'      => $request->get('type') == 1 ? $t : -$t,
            'description'   => $request->get('description')
        ]);

        $c = 0;
        $pro_sizes = [];

        foreach (json_decode($tallas, true) as $k => $talla) {
            $pro_sizes[$k] = array(
                'size' => $talla['size'],
                'quantity' => $talla['quantity']
            );
        }
        foreach ($request->get('sizes') as $g => $row) {
            $t = $t + $row['quantity'];

            $key = array_search($row['size'], array_column($pro_sizes, 'size'));
            if ($key === false) {
                array_push($pro_sizes, [
                    'size' => $row['size'],
                    'quantity' => 0
                ]);
            }
        }

        foreach ($request->get('sizes') as  $row) {
            if ($request->get('type') == 1) {
                $c = $row['quantity'];
            } else {
                $c = -$row['quantity'];
            }
            KardexSize::create([
                'kardex_id'         => $kardex->id,
                'product_id'        => $request->get('product_id'),
                'local_id'          => $request->get('local_id'),
                'size'              => $row['size'],
                'quantity'          => $c
            ]);

            for ($r = 0; $r < count($pro_sizes); $r++) {
                if ($pro_sizes[$r]['size'] == $row['size']) {
                    if ($request->get('type') == 1) {
                        $pro_sizes[$r]['quantity'] = $pro_sizes[$r]['quantity'] + $row['quantity'];
                    } elseif ($pro_sizes[$r]['quantity'] > $row['quantity']) {
                        $pro_sizes[$r]['quantity'] = $pro_sizes[$r]['quantity'] - $row['quantity'];
                    } else {
                        $pro_sizes[$r]['quantity'] = 0;
                    }
                }
            }
        }

        $pt = 0;
        for ($r = 0; $r < count($pro_sizes); $r++) {
            $pt = $pt + $pro_sizes[$r]['quantity'];
        }

        $product->update([
            'sizes' => json_encode($pro_sizes),
            'stock' => $pt
        ]);
    }
}
