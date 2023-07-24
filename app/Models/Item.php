<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Count;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'code', 'image'
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
                        ->orWhere('name','like','%'.$search.'%');
            });
        });
    }
}
