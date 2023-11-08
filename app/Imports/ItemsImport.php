<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Sale;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemsImport implements OnEachRow, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    // public function model(array $row)
    // {
    //     return new Item([
    //         'code' => $row['code'],
    //         'name' => $row['name'],
    //         'brand' => $row['brand'],
    //         'description' => $row['description'],
    //         'cost' => $row['cost'],
    //         'price' => $row['price'],
    //     ]);
    // }

    public function onRow(Row $row)
    {
        // $rowIndex = $row->getIndex();
        $row = $row->toArray();
        
        $item = Item::firstOrCreate([
            'code' => $row['code'],
        ], [
            'brand' => $row['brand'],
            'description' => $row['description'],
            'cost' => $row['cost'],
            'price' => $row['selling_price'],
            'created_at' => $row['date_encoded'] == null ? Carbon::now() : Carbon::parse($row['date_encoded']),
        ]);

        $stock = $item->tally()->firstOrCreate([
            'number' => $row['stock'] ? $row['stock'] : 1
        ]);

        if ($row['price_sold'] && !Sale::where('item->code',$row['code'])->exists()) {
            Sale::create([
                'item' => $item,
                'discount' => $item->price - $row['price_sold']
            ]);

            $stock->update([
                'number' => $stock->number < 1 ? 0 : $stock->number - 1
            ]);
        }

        // if (Item::where('code',$row['code'])->exists()) {
        //     $item = Item::where('code',$row['code'])->first();

        //     $item->update([
        //         'brand' => $row['brand'],
        //         'description' => $row['description'],
        //         'cost' => $row['cost'],
        //         'price' => $row['selling_price'],
        //         'created_at' => $row['date_encoded'] ? Carbon::parse($row['date_encoded']) : $item->created_at,
        //     ]);

        //     $stock = $item->tally()->firstOrCreate([
        //         'number' => $row['stock'] ? $row['stock'] : 1
        //     ]);

        //     if ($row['discount']) {
        //         // something about checking if there is a sale of this item already
        //         // if not then create the sale
        //     }
        // } else {
        //     $item = Item::create([
        //         'code' => $row['code'],
        //         'brand' => $row['brand'],
        //         'description' => $row['description'],
        //         'cost' => $row['cost'],
        //         'price' => $row['selling_price'],
        //         'created_at' => $row['date_encoded'] ? Carbon::parse($row['date_encoded']) : $item->created_at,
        //     ]);

        //     $stock = $item->tally()->firstOrCreate([
        //         'number' => $row['stock'] ? $row['stock'] : 1
        //     ]);
        // }
    }
}
