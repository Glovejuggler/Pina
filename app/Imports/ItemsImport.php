<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Item;
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
            'name' => $row['name'],
            'brand' => $row['brand'],
            'description' => $row['description'],
            'cost' => $row['cost'],
            'price' => $row['selling_price'],
            'created_at' => $row['date_encoded'] == null ? Carbon::now() : Carbon::parse($row['date_encoded']),
        ]);

        $stock = $item->tally()->firstOrCreate([
            'number' => $row['stock']
        ]);
    }
}
