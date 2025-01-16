<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Facades\Filament;

class OperatorPdv extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'enterprise_id', 'filial_id', 'is_active'];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function filial(): BelongsTo
    {
        return $this->belongsTo(Filial::class)->where('enterprise_id', Filament::getTenant()->id);
    }
}
