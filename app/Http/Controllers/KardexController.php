<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\KardexSize;
use App\Models\LocalSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KardexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $establishments = LocalSale::all();
        $local_id = request()->input('local_id');
        if($local_id!=null && $local_id!=0){
            $kardexes = Kardex::join('products', 'kardexes.product_id', 'products.id')
            ->join('local_sales', 'kardexes.local_id', 'local_sales.id')
            ->select(
                'products.*',
                'local_sales.id AS local_id',
                'local_sales.description AS local_names'
            )
            ->selectRaw('CONVERT(SUM(quantity), SIGNED INTEGER) AS kardex_stock')
            ->groupBy(
                'products.id',
                'local_sales.description',
                'local_sales.id'
            )
            ->where('products.interne', '=', request()->input('search'))
            ->orWhere('products.description', 'Like', '%' . request()->input('search') . '%')
            ->where(function ($query) use ($local_id) {
                $query->where('kardexes.local_id', $local_id);
            })
            ->orderBy('local_sales.description')
            ->paginate(20);
        }else{
            $kardexes = Kardex::join('products', 'kardexes.product_id', 'products.id')
            ->join('local_sales', 'kardexes.local_id', 'local_sales.id')
            ->select(
                'products.*',
                'local_sales.id AS local_id',
                'local_sales.description AS local_names'
            )
            ->selectRaw('CONVERT(SUM(quantity), SIGNED INTEGER) AS kardex_stock')
            ->groupBy(
                'products.id',
                'local_sales.description',
                'local_sales.id'
            )
            ->where('products.interne', '=', request()->input('search'))
            ->orWhere('products.description', 'Like', '%' . request()->input('search') . '%')
            ->orderBy('local_sales.description')
            ->paginate(20);
        }


        return Inertia::render('Kardex/List', [
            'establishments' => $establishments,
            'kardexes' => $kardexes,
            'local_id' => $local_id,
            'filters' => request()->all('search')
        ]);
    }

    public function kardexDeailsSises(Request $request)
    {
        $kardex_sizes = KardexSize::select(
            'kardex_sizes.size',
            DB::raw('SUM(kardex_sizes.quantity) AS total')
        )
            ->where('local_id', $request->get('local_id'))
            ->where('product_id', $request->get('product_id'))
            ->groupBy('kardex_sizes.size')
            ->get();

        return response()->json($kardex_sizes);
    }
}
