<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanceledCouponClosing extends Model
{
    use HasFactory;

    protected $fillable = ['operator_pdv_id', 'user_id', 'closing_date', 'enterprise_id', 'attachments', 'filial_id', 'valor'];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function operator()
    {
        return $this->belongsTo(OperatorPdv::class, 'operator_pdv_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
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
