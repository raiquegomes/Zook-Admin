<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnterpriseUser extends Model
{
    use HasFactory;

    protected $table = 'enterprise_user';

    protected $fillable = ['enterprise_id', 'user_id'];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
