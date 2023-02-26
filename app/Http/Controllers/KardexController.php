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
        $establisment = LocalSale::all();

        $kardexes = Kardex::join('products', 'kardexes.product_id', 'products.id')
            ->join('local_sales', 'kardexes.local_id', 'local_sales.id')
            ->select(
                'products.*',
                'local_sales.id AS local_id',
                'local_sales.description AS local_names'
            )
            ->selectRaw('SUM(quantity) AS kardex_stock')
            ->groupBy(
                'products.id',
                'local_sales.description',
                'local_sales.id'
            )
            ->where('products.interne', '=', request()->input('search'))
            ->where('products.description', 'Like', '%' . request()->input('search') . '%')
            ->orderBy('local_sales.description')
            ->paginate(20);

        return Inertia::render('Kardex/List', [
            'establisment' => $establisment,
            'kardexes' => $kardexes,
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
