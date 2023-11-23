<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Sale;
use Maatwebsite\Excel\Row;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemsImport implements OnEachRow, WithValidation, WithHeadingRow, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;

    public function onRow(Row $row)
    {
        // dd($row->toArray());
        $row = $row->toArray();

        if (isset($row['date_encoded'])) {
            $date = Carbon::parse($row['date_encoded']);
        } else {
            $date = Carbon::now();
        }
        
        $item = Item::firstOrCreate([
            'code' => $row['code'],
        ], [
            'brand' => $row['brand'],
            'description' => isset($row['description']) ? $row['description'] : null,
            'cost' => $row['cost'],
            'price' => $row['selling_price'],
            'created_at' => $date,
        ]);

        $stock = $item->tally()->firstOrCreate([
            'number' => isset($row['stock']) ? $row['stock'] : 1
        ]);

        if (isset($row['price_sold']) && !Sale::where('item->code',$row['code'])->exists()) {
            Sale::create([
                'item' => $item,
                'discount' => $item->price - $row['price_sold']
            ]);

            $stock->update([
                'number' => $stock->number < 1 ? 0 : $stock->number - 1
            ]);
        }
    }

    public function rules() :array
    {
        return [
            'code' => 'required',
            'brand' => 'sometimes|required_without:price_sold',
            'description' => 'nullable',
            'cost' => 'sometimes|required_without:price_sold|numeric',
            'selling_price' => 'sometimes|required_without:price_sold|numeric',
            'stock' => 'nullable',
            'date_encoded' => 'nullable',
            'price_sold' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'cost.numeric' => 'Cost should be a number',
            'selling_price.numeric' => 'Cost should be a number',
            'selling_price.required' => 'Selling price field is required',
        ];
    }
}
