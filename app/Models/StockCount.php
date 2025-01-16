<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ListProductsCount;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Filament\Facades\Filament;

class StockCount extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'filial_id', 'date', 'enterprise_id', 'count_type'];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function products()
    {
        return $this->hasMany(StockProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function filial(): BelongsTo
    {
        return $this->belongsTo(Filial::class)->where('enterprise_id', Filament::getTenant()->id);
    }
}
