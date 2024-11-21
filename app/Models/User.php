<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements HasTenants, HasAvatar
{
    use HasFactory, Notifiable,  HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class);
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function performances()
    {
        return $this->hasMany(Performance::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url("$this->avatar_url") : null;
    }

    public function enterprises(): BelongsToMany
    {
        return $this->belongsToMany(Enterprise::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->enterprises;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->enterprises()->whereKey($tenant)->exists();
    }

    public function removeFromEnterprise($enterpriseId): bool
{
    $enterprise = $this->enterprises()->find($enterpriseId);

    if (!$enterprise) {
        return false; // Retorna false se a empresa não estiver vinculada
    }

    return $this->enterprises()->detach($enterpriseId) > 0; // Remove o vínculo e retorna true se bem-sucedido
}
}
