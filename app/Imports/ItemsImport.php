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
            'supplier' => $row['supplier'],
            'brand' => $row['brand'],
            'description' => $row['description'],
            'cost' => $row['cost'],
            'price' => $row['selling_price'],
            'created_at' => $row['date_encoded'] == null ? Carbon::now() : Carbon::createFromFormat('m-d-y',$row['date_encoded'])->startOfDay(),
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
    }
}