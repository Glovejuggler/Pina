<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Count;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand', 'description', 'cost', 'price', 'code', 'image'
    ];

    public function tally()
    {
        return $this->hasOne(Tally::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('code','like','%'.$search.'%')
                        ->orWhere('brand','like','%'.$search.'%')
                        ->orWhere('description','like','%'.$search.'%')
                        ->orWhere(DB::raw("brand || ' ' || description"), 'like', '%'.$search.'%');
            });
        });
    }
}
