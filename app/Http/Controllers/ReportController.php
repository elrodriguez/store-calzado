<?php

namespace App\Http\Controllers;

use App\Models\LocalSale;
use App\Models\Sale;
use App\Models\SaleProduct;
use Illuminate\Http\Request;
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

    public function sales_report($start, $end)
    {
        $sales = Sale::whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->get();

        $file = public_path('ticket/') . 'ticket.pdf';
        $pdf = PDF::loadView('reports.sales_report', ['sales' => $sales]);
        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'landscape');
        $pdf->save($file);

        return response()->download($file);
    }
}
