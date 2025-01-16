<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_count_id',
        'name',
        'boning_stock',
        'cashier_stock',
        'quality'
    ];

    public function stockCount()
    {
        return $this->belongsTo(StockCount::class);
    }
}
