<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Tally;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $sales = Sale::orderBy('created_at', 'desc')->paginate(3);
        // // dd($sales->setCollection($sales->groupBy(function ($q) {
        // //                         return Carbon::parse($q->created_at)->diffForHumans();
        // //                     })));

        // $gSales = $sales->setCollection($sales->groupBy(function ($q) {
        //     return Carbon::parse($q->created_at)->diffForHumans();
        // }));

        // if ($request->wantsJson()) {
        //     return $gSales;
        // }

        return inertia('Items/Sales', [
            'sales' => Sale::orderBy('created_at', 'desc')->get()->groupBy(function ($q) {
                return Carbon::parse($q->created_at)->diffForHumans();
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Item $item, Request $request)
    {
        // dd($item, $request);
        Sale::create([
            'item' => $item,
            'discount' => $request->discount,
        ]);

        $tally = Tally::where('item_id',$item->id)->first();
        
        $tally->update([
            'number' => $tally->number - 1
        ]);

        return redirect()->back();
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
}
