<?php

namespace App\Exports;

use App\Models\Item;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ItemsExport implements FromView
{
    use Exportable;
    
    public function view(): View
    {
        return view('exports.items', [
            'items' => Item::all()
        ]);
    }
}
