<?php

namespace App\Models;

use Carbon\Carbon;
use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['item', 'discount'];

    protected $casts = ['item' => Json::class];

    protected $appends = ['net'];

    public function getNetAttribute()
    {
        return $this->item['price'] - $this->discount;
    }
}
