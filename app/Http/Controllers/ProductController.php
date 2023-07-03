<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\KardexSize;
use App\Models\LocalSale;
use App\Models\Product;
use App\Models\ProductEstablishmentPrice;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        //dd(request()->all());
        if (request()->has('search')) {
            $products->where('interne', '=', request()->input('search'))
                ->orWhere('description', 'Like', '%' . request()->input('search') . '%');
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
            'establishments' => LocalSale::all(),
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
        // $path = 'img' . DIRECTORY_SEPARATOR . 'imagen-no-disponible.jpeg';
        // $destination = 'uploads' . DIRECTORY_SEPARATOR . 'products';
        $path = 'img/imagen-no-disponible.jpeg';
        $destination = 'uploads/products';
        $file = $request->file('image');
        if ($file) {
            $original_name = strtolower(trim($file->getClientOriginalName()));
            $original_name = str_replace(" ", "_", $original_name);
            $extension = $file->getClientOriginalExtension();
            $file_name = date('YmdHis') . '.' . $extension;
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
            'interne' => $request->get('interne'),
            'description' => $request->get('description'),
            'image' => $path,
            'purchase_prices' => $request->get('purchase_prices'),
            'sale_prices' => json_encode($request->get('sale_prices')),
            'sizes' => json_encode($request->get('sizes')),
            'stock_min' => 1,
            'stock' => $total,
        ]);

        $k = Kardex::create([
            'date_of_issue' => Carbon::now()->format('Y-m-d'),
            'motion' => 'purchase',
            'product_id' => $pr->id,
            'local_id' => $request->get('local_id'),
            'quantity' => $total,
            'description' => 'Stock Inicial',
        ]);

        foreach ($request->get('sizes') as $row) {
            KardexSize::create([
                'kardex_id' => $k->id,
                'product_id' => $pr->id,
                'local_id' => $request->get('local_id'),
                'size' => $row['size'],
                'quantity' => $row['quantity'],
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
            'product' => $product,
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
            'interne' => 'required|unique:products,interne,' . $product->id,
            'description' => 'required',
            'purchase_prices' => 'required',
            'sale_prices.high' => 'required',
        ]);

        $product->usine = $request->get('usine');
        $product->interne = $request->get('interne');
        $product->description = $request->get('description');
        $product->purchase_prices = $request->get('purchase_prices');
        $product->sale_prices = $request->get('sale_prices');
        $product->sizes = $request->get('sizes');
        $product->save();

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
        $local_id = Auth::user()->local_id;
        $search = $request->get('search');

        $success = false;

        $products = DB::table('products as t1')
            ->select(
                't1.*',
                DB::raw(`
                SELECT
                    JSON_ARRAYAGG(JSON_OBJECT('size', size, 'quantity', quantity_sum)) AS productos
                    FROM (
                        SELECT size, SUM(quantity) AS quantity_sum
                        FROM kardex_sizes
                        WHERE kardex_sizes.product_id=t1.id AND local_id = ` . $local_id . `GROUP BY size
                    ) AS local_sizes`)
            )
            ->selectSub(function ($query) use ($local_id) {
                $query->from('product_establishment_prices')
                    ->selectRaw("JSON_OBJECT('high', high, 'under', under,'medium',MEDIUM)")
                    ->whereColumn('product_establishment_prices.product_id', 't1.id')
                    ->where('product_establishment_prices.local_id', $local_id);
            }, 'local_prices')
            ->leftJoin('kardexes', 't1.id', '=', 'kardexes.product_id')
            ->where(function ($query) use ($search) {
                $query->where('t1.interne', '=', $search)
                    ->orWhere('t1.description', 'like', '%' . $search . '%');
            })
            ->where('kardexes.local_id', '=', $local_id)
            ->groupBy('t1.id')
            ->get();

        if (count($products) > 0) {
            $success = true;
        }
        return response()->json([
            'success' => $success,
            'products' => $products,
        ]);
    }

    public function searchScanerProduct(Request $request)
    {
        $search = $request->get('search');
        $local_id = Auth::user()->local_id;

        $product = DB::table('products as t1')
            ->select(
                't1.*',
                DB::raw(`
                SELECT
                    JSON_ARRAYAGG(JSON_OBJECT('size', size, 'quantity', quantity_sum)) AS productos
                    FROM (
                        SELECT size, SUM(quantity) AS quantity_sum
                        FROM kardex_sizes
                        WHERE kardex_sizes.product_id=t1.id AND local_id = ` . $local_id . `GROUP BY size
                    ) AS local_sizes`)
            )
            ->selectSub(function ($query) use ($local_id) {
                $query->from('product_establishment_prices')
                    ->selectRaw("JSON_OBJECT('high', high, 'under', under,'medium',MEDIUM)")
                    ->whereColumn('product_establishment_prices.product_id', 't1.id')
                    ->where('product_establishment_prices.local_id', $local_id);
            }, 'local_prices')

            ->leftJoin('kardexes', 't1.id', '=', 'kardexes.product_id')
            ->where(function ($query) use ($search) {
                $query->where('t1.interne', '=', $search);
            })
            ->where('kardexes.local_id', '=', $local_id)
            ->groupBy('t1.id')
            ->first();

        return response()->json($product);
    }

    public function showdetails($id)
    {
        $product = Product::where('id', $id)->first();
        return response()->json($product);
    }

    public function saveInput(Request $request)
    {
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
        foreach ($request->get('sizes') as $item) {
            $t += $item['quantity'];
        }

        $kardex = Kardex::create([
            'date_of_issue' => Carbon::now()->format('Y-m-d'),
            'motion' => $request->get('type') == 1 ? $request->get('motion') : 'Sale',
            'product_id' => $request->get('product_id'),
            'local_id' => $request->get('local_id'),
            'quantity' => $request->get('type') == 1 ? $t : -$t,
            'description' => $request->get('description'),
        ]);

        $c = 0;
        $pro_sizes = [];

        foreach (json_decode($tallas, true) as $k => $talla) {
            $pro_sizes[$k] = array(
                'size' => $talla['size'],
                'quantity' => $talla['quantity'],
            );
        }
        foreach ($request->get('sizes') as $g => $row) {
            $t = $t + $row['quantity'];

            $key = array_search($row['size'], array_column($pro_sizes, 'size'));
            if ($key === false) {
                array_push($pro_sizes, [
                    'size' => $row['size'],
                    'quantity' => 0,
                ]);
            }
        }

        foreach ($request->get('sizes') as $row) {
            if ($request->get('type') == 1) {
                $c = $row['quantity'];
            } else {
                $c = -$row['quantity'];
            }
            KardexSize::create([
                'kardex_id' => $kardex->id,
                'product_id' => $request->get('product_id'),
                'local_id' => $request->get('local_id'),
                'size' => $row['size'],
                'quantity' => $c,
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
            'stock' => $pt,
        ]);
    }

    public function imageUpload(Request $request)
    {

        $destination = 'uploads/products';
        $base64Image = $request->get('image');

        $product_id = $request->get('product_id');
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        if (PHP_OS == 'WINNT') {
            $tempFile = tempnam(sys_get_temp_dir(), 'img');
        } else {
            $tempFile = tempnam('/var/www/html/store-calzado/public', 'img');
        }
        file_put_contents($tempFile, $fileData);
        $mime = mime_content_type($tempFile);

        $name = uniqid('', true) . '.' . str_replace('image/', '', $mime);
        $file = new UploadedFile(realpath($tempFile), $name, $mime, null, true);

        $path = null;
        if ($file) {
            $original_name = strtolower(trim($file->getClientOriginalName()));
            $file_name = time() . rand(100, 999) . $original_name;
            $path = Storage::disk('public')->putFileAs($destination, $file, $file_name);
        }
        $product = [];
        if ($product_id) {
            Product::find($product_id)->update([
                'image' => $path,
            ]);
            $product = Product::find($product_id);
        }
        return response()->json([
            'product' => $product,
            'path' => $path,
        ]);
    }

    public function savePrices(Request $request)
    {

        $this->validate(
            $request,
            [
                'product_id' => 'required',
                'locals.*.local_price1' => 'required',
                'locals.*.local_price2' => 'required',
                'locals.*.local_price3' => 'required',
            ],
            [
                'locals.*.local_price1.required' => 'Ingresar precio normal',
                'locals.*.local_price2.required' => 'Ingresar precio medio',
                'locals.*.local_price3.required' => 'Ingresar precio minimo',
            ]
        );

        foreach ($request->get('locals') as $local) {
            ProductEstablishmentPrice::updateOrCreate(
                [
                    'local_id' => $local['local_id'],
                    'product_id' => $request->get('product_id'),
                ], // Atributos para buscar un registro existente
                [
                    'high' => $local['local_price1'],
                    'medium' => $local['local_price2'],
                    'under' => $local['local_price3'],
                ] // Atributos para establecer en el registro si se actualiza o se crea uno nuevo
            );
        }
    }

    public function getPricesProduct($id)
    {
        $prices = ProductEstablishmentPrice::join('local_sales', 'local_id', 'local_sales.id')
            ->select(
                'local_sales.description',
                'product_id',
                'local_id',
                'high',
                'under',
                'medium'
            )
            ->where('product_id', $id)
            ->get();
        return response()->json($prices);
    }

    public function searchProductAll(Request $request)
    {

        $search = $request->get('search');
        $products = Product::where('interne', $search)
            ->orWhere('description', 'like', '%' . $search . '%')
            ->get();
        $success = false;

        if (count($products) > 0) {
            $success = true;
        }
        return response()->json([
            'success' => $success,
            'products' => $products,
        ]);
    }

    public function getProductByLocal(Request $request)
    {
        $local_id = $request->get('local_id_origin');
        $product_id = $request->get('product_id');

        $sizes = KardexSize::where('product_id', $product_id)
            ->where('local_id', $local_id)
            ->select('size', DB::raw('CONVERT(sum(quantity), SIGNED INTEGER) AS quantity_relocate'))
            ->selectRaw('CONVERT(sum(quantity), SIGNED INTEGER) quantity')
            ->groupBy('size')
            ->get();

        return response()->json($sizes);
    }

    public function saveRelocate(Request $request)
    {

        $this->validate($request, [
            'local_id_destiny' => 'required',
            'local_id_origin' => 'required',
            'description' => 'required',
            'item.*.quantity_relocate' => 'required',
        ], [
            'local_id_destiny.required' => 'El local destino es obligatorio',
            'description.required' => 'Debe ingresar el Motivo del traslado',
        ]);

        try {
            DB::beginTransaction();
            $tallas = $request->get('sizes');

            $t = 0;
            foreach ($request->get('sizes') as $item) {
                if ($item['quantity'] >= $item['quantity_relocate']) {
                    $t += $item['quantity_relocate'];
                } else {
                    return Inertia::location('/products/');
                    break;
                }
            }

            $kardex_id_origin = 0;
            $kardex_id_destiny = 0;

            if ($t > 0) {
                $kardex = Kardex::create([
                    'date_of_issue' => Carbon::now()->format('Y-m-d'),
                    'motion' => 'relocate',
                    'product_id' => $request->get('product_id'),
                    'local_id' => $request->get('local_id_origin'),
                    'quantity' => -$t,
                    'description' => $request->get('description'),
                ]);

                $kardex_id_origin = $kardex->id;

                $kardex = Kardex::create([
                    'date_of_issue' => Carbon::now()->format('Y-m-d'),
                    'motion' => 'relocate',
                    'product_id' => $request->get('product_id'),
                    'local_id' => $request->get('local_id_destiny'),
                    'quantity' => $t,
                    'description' => $request->get('description'),
                ]);

                $kardex_id_destiny = $kardex->id;

                foreach ($tallas as $talla) {
                    if ($talla['quantity_relocate'] > 0) {
                        KardexSize::create([
                            'kardex_id' => $kardex_id_origin,
                            'product_id' => $request->get('product_id'),
                            'local_id' => $request->get('local_id_origin'),
                            'size' => $talla['size'],
                            'quantity' => -$talla['quantity_relocate'],
                        ]);
                        KardexSize::create([
                            'kardex_id' => $kardex_id_destiny,
                            'product_id' => $request->get('product_id'),
                            'local_id' => $request->get('local_id_destiny'),
                            'size' => $talla['size'],
                            'quantity' => $talla['quantity_relocate'],
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
