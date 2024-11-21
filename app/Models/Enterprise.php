<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Enterprise extends Model
{
    use HasFactory;

    protected $fillable = ['cnpj', 'name', 'fantasy_name', 'email', 'slug', 'is_active', 'master_user_id', 'token_invitation'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enterprise_user', 'enterprise_id', 'user_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function appointmentschedulings(): HasMany
    {
        return $this->hasMany(AppointmentScheduling::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

}
