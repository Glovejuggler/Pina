<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Tally;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->date) {
            $sales = Sale::orderBy('created_at', 'desc')->paginate(5);
        } else {
            $sales = Sale::whereDate('created_at', Carbon::parse($request->date))->orderBy('created_at', 'desc')->paginate(5);
        }
        // // dd($sales->setCollection($sales->groupBy(function ($q) {
        // //                         return Carbon::parse($q->created_at)->diffForHumans();
        // //                     })));

        // Sale::orderBy('created_at', 'desc')->get()->groupBy(function ($q) {
        //         return Carbon::parse($q->created_at)->diffForHumans();
        //     })

        // $gSales = $sales->setCollection($sales->groupBy(function ($q) {
        //     return Carbon::parse($q->created_at)->diffForHumans();
        // }));

        // if ($request->wantsJson()) {
        //     return $gSales;
        // }
        $period = $request->period;

        if ($period == 'weekly') {
            $sales->setCollection($sales->groupBy(function ($q) {
                return Carbon::parse($q->created_at)->startOFWeek(Carbon::SUNDAY)->format('F j, Y').' to '.Carbon::parse($q->created_at)->endOfWeek(Carbon::SATURDAY)->format('F j, Y');
            }));
        } elseif ($period == 'monthly') {
            $sales->setCollection($sales->groupBy(function ($q) {
                return Carbon::parse($q->created_at)->format('F Y');
            }));
        } else {
            $sales->setCollection($sales->groupBy(function ($q) {
                return Carbon::parse($q->created_at)->format('F j, Y');
            }));
        }
        

        return inertia('Items/Sales', [
            'sales' => $sales->withQueryString(),
            'filters' => $request->only(['period', 'date'])
        ]);
    }

    /**
     * View the sales report of a certain period/date
     */
    public function view(Request $request)
    {
        // dd($request);
        $date = Carbon::parse($request->date);
        $period = $request->period;

        if ($date && $period) {
            if ($period == 'weekly') {
                $sales = Sale::whereDate('created_at','>=',$date->startOfWeek(Carbon::SUNDAY))
                                ->whereDate('created_at','<=',$date->endOfWeek(Carbon::SATURDAY))
                                ->get();
                $headDate = $date->startOfWeek(Carbon::SUNDAY)->format('F j, Y').' to '.$date->endOfWeek(Carbon::SATURDAY)->format('F j, Y');
            } elseif ($period == 'monthly') {
                $sales = Sale::whereMonth('created_at',$date->month)->get();
                $headDate = $date->format('F Y');
            } elseif ($period == 'daily') {
                $sales = Sale::whereDate('created_at',$date)->get();
                $headDate = $date->format('F j, Y');
            }
        } else {
            $sales = Sale::whereDate('created_at',$date)->get();
            $headDate = $date->format('F j, Y');
        }

        return inertia('Items/SaleView', [
            'sales' => $sales,
            'headDate' => $headDate,
            'info' => [
                'period' => $period,
                'date' => $date
            ]
        ]);
    }

    /**
     * Excel export
     */
    public function export($period = null, $date = null)
    {
        // dd($period, $date);
        if ($period && $date) {
            if ($period == 'weekly') {
                $titlePrefix = Carbon::parse($date)->startOfWeek(Carbon::SUNDAY)->format('F j, Y').' to '.Carbon::parse($date)->endOfWeek(Carbon::SATURDAY)->format('F j, Y');
            } elseif ($period == 'monthly') {
                $titlePrefix = Carbon::parse($date)->format('F Y');
            } elseif ($period == 'daily') {
                $titlePrefix = Carbon::parse($date)->format('F j, Y');
            }
        } else {
            $titlePrefix = now()->format('F j, Y');
        }

        return (new SalesExport)->for($period, $date)->download($titlePrefix.' '.'Sales.xlsx');
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
        $request->validate([
            'priceSold' => 'required|numeric'
        ]);

        Sale::create([
            'item' => $item,
            'discount' => $item->price - $request->priceSold,
        ]);

        $item->tally->update([
            'number' => $item->tally->number - 1
        ]);

        return redirect()->back();
    }

    /**
     * 
     */
    public function check(Request $request)
    {
        // dd($request)
        $error = null;
        if (!Item::where('code',$request->code)->exists()) {
            $error = 'Item does not exist';
        }

        if ($error) {
            return redirect()->back()->withErrors([
                'item' => $error
            ])->withItem([
                'item' => null
            ]);
        } else {
            return redirect()->back()->withItem([
                'item' => Item::with('tally')->where('code',$request->code)->first()
            ]);
        }
    }

    /**
     * Sell item through the dashboard
     */
    public function sell(Request $request)
    {
        // dd($request);
        $request->validate([
            'code' => 'required',
            'priceSold' => 'required'
        ]);
        
        $item = Item::where('code', $request->code)->first();

        Sale::create([
            'item' => $item,
            'discount' => $item->price - $request->priceSold,
            'created_at' => $request->date
        ]);

        $item->tally->update([
            'number' => $item->tally->number - 1
        ]);

        return redirect()->back();
    }

    /**
     * Batch selling of items
     */
    public function batchSell(Request $request)
    {
        $errorReport = collect([]);
        $successReport = collect([]);

        foreach($request->items as $newItem) {
            // dd($newItem);
            $item = Item::where('code', $newItem['code'])->first();

            if ($item) {
                $tally = Tally::where('item_id',$item->id)->first();

                if ($tally->number > 0) {
                    // $newSale = Sale::create([
                    //     'item' => $item, 
                    //     'discount' => $newItem['discount'],
                    // ]);

                    // $tally->update([
                    //     'number' => $tally->number - 1
                    // ]);
                    $successReport->push([
                        'item' => $newItem,
                        'remarks' => 'Success'
                    ]);
                    // dd('exist stock');
                } else {
                    $errorReport->push([
                        'item' => $newItem,
                        'remarks' => 'No stocks left'
                    ]);
                    // dd('exist no stock');
                }
            } else {
                // dd('no');
                $errorReport->push([
                    'item' => $newItem,
                    'remarks' => 'dne'
                ]);
            }
        }

        dd($successReport, $errorReport);

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
