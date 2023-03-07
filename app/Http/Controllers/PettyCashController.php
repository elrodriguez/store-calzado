<?php

namespace App\Http\Controllers;

use App\Models\LocalSale;
use App\Models\PettyCash;
use App\Models\Sale;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PettyCashController extends Controller
{
    public function index()
    {
        $pettycashes = (new PettyCash())->newQuery();

        if (request()->has('search')) {
            $search = request()->input('search');
            if ($search['date_start'] && $search['date_end']) {
                $pettycashes->whereBetween('date_opening', [$search['date_start'], $search['date_end']]);
            } else if ($search['date_start']) {
                $pettycashes->whereDate('date_opening', $search['date_start'])->get();
            }
        }
        // if (request()->query('sort')) {
        //     $attribute = request()->query('sort');
        //     $sort_order = 'ASC';
        //     if (strncmp($attribute, '-', 1) === 0) {
        //         $sort_order = 'DESC';
        //         $attribute = substr($attribute, 1);
        //     }
        //     $pettycashes->orderBy('petty_cashes.' . $attribute, $sort_order);
        // } else {
        //     $pettycashes->latest();
        // }

        $pettycashes = $pettycashes->join('users', 'petty_cashes.user_id', 'users.id')
            ->select(
                'petty_cashes.id',
                'petty_cashes.date_opening',
                'petty_cashes.time_opening',
                'petty_cashes.date_closed',
                'petty_cashes.time_closed',
                'petty_cashes.beginning_balance',
                'petty_cashes.final_balance',
                'petty_cashes.income',
                'petty_cashes.state',
                'petty_cashes.reference_number',
                'petty_cashes.local_sale_id',
                'users.name AS name_user'
            )
            ->orderBy('petty_cashes.created_at', 'DESC')
            ->paginate(10)
            ->onEachSide(2);

        return Inertia::render('Pettycashes/List', [
            'pettycashes' => $pettycashes,
            'filters' => request()->all('search'),
            'locals' => LocalSale::all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'local_id' => 'required',
        ]);

        PettyCash::create([
            'user_id' => Auth::id(),
            'date_opening' => Carbon::now()->format('Y-m-d'),
            'time_opening' => date('H:i:s'),
            //'income' => $request->input('starting_amount'),
            'beginning_balance' => $request->input('starting_amount'),
            'state' => '1',
            'local_sale_id' => $request->input('local_id'),
        ]);

        return redirect()->route('pettycash.index')
            ->with('message', __('Caja Chica creado con éxito'));
    }

    public function destroy(PettyCash $product)
    {
        $product->delete();
        return redirect()->route('pettycash.index')
            ->with('message', __('Caja Chica eliminado con éxito'));
    }

    public function close_petty($petty_id)
    {
        $v = PettyCash::find($petty_id)->state;
        if($v){
        try {
            $amount = Sale::where('petty_cash_id', $petty_id)->sum('total');
            PettyCash::where('id', $petty_id)->update([
                'state' => false,
                'date_closed' => Carbon::now()->format('Y-m-d'),
                'time_closed' => date('H:i:s'),
                'final_balance' => PettyCash::where('id', $petty_id)->select('beginning_balance')->first()->beginning_balance + $amount,
                'income' => $amount
            ]);
            return true;
        } catch (Exception $e) {
            return "<script>console.log(" . $e->getMessage() . ");</script>";
        }
    }
    }
}
