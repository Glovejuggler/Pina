<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tally;
use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreItemRequest;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Item::query()
                ->with('tally')
                ->filter($request->only(['search']))
                // ->select('code', 'name', 'description', 'price', DB::raw('COUNT(*) as count'))
                // ->groupBy('code', 'name', 'description', 'price')
                ->paginate(10)
                ->withQueryString();

        if ($request->wantsJson()) {
            return $items;
        }

        return inertia('Items/Index', [
            'items' => $items,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Set count value of an item
     */
    public function setCount(Request $request)
    {
        if ($request->count < 0) {
            return redirect()->back();
        }

        $item = $request->item;

        $tally = Tally::where('item_id', $item['id'])->first();

        $tally->update([
            'number' => $request->count
        ]);

        return redirect()->back();
    }

    /**
     * Export
     */
    public function export()
    {
        return (new ItemsExport)->download('Items.xlsx');
    }

    /**
     * Import
     */
    public function import(Request $request)
    {
        $import = new ItemsImport();
        $import->import($request->file('file'));

        // dd($import->failures(), $import->errors());

        return redirect()->back();

        // try {
        //     $import->import($request->file('file'));
        // } catch (\MaatWebsite\Excel\Validators\ValidationException $e)
        // {
        //     $failures = $e->failures();

        //     dd($failures);
        // }

        // dd($failures);
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
    public function store(StoreItemRequest $request)
    {
        $item = new Item($request->validated());

        if ($request->image) {
            $file = $request->image;
            $name = $file->hashName();
            $path = $file->storeAs('/images', $name, 'public');

            $item->image = $path;
        }
        $item->save();
        
        if (!Tally::where('item_id',$item->id)->exists()) {
            Tally::create([
                'item_id' => $item->id,
                'number' => 1
            ]);
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(StoreItemRequest $request, Item $item)
    {
        // dd($request);
        $item->update($request->validated());
        if ($request->image) {
            $file = $request->image;
            $name = $file->hashName();
            $path = $file->storeAs('/images', $name, 'public');

            $item->image = $path;
            $item->save();
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        Tally::where('item_id',$item->id)->delete();
        $item->delete();

        return redirect()->back();
    }
}
