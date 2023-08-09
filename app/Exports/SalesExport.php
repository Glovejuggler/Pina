<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Sale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromView, ShouldAutoSize, WithStyles
{
    use Exportable;

    public function for($period = null, $date = null)
    {
        $this->period = $period;
        $this->date = Carbon::parse($date);

        return $this;
    }
    
    public function view(): View
    {
        if ($this->date && $this->period) {
            if ($this->period == 'weekly') {
                $sales = Sale::whereDate('created_at','>=',$this->date->startOfWeek(Carbon::SUNDAY))
                                ->whereDate('created_at','<=',$this->date->endOfWeek(Carbon::SATURDAY))
                                ->get();
                $headDate = $this->date->startOfWeek(Carbon::SUNDAY)->format('F j, Y').' to '.$this->date->endOfWeek(Carbon::SATURDAY)->format('F j, Y');
            } elseif ($this->period == 'monthly') {
                $sales = Sale::whereMonth('created_at', $this->date->month)->get();
                $headDate = $this->date->format('F Y');
            } elseif ($this->period == 'daily') {
                $sales = Sale::whereDate('created_at', $this->date)->get();
                $headDate = $this->date->format('F j, Y');
            }
        } else {
            $sales = Sale::whereDate('created_at',now())->get();
            $headDate = now()->format('F j, Y');
        }

        return view('exports.sales', [
            'sales' => $sales,
            'total' => $sales->sum('net'),
            'headDate' => $headDate
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}