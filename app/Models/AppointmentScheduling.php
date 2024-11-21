<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class AppointmentScheduling extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'scheduled_date', 'status', 'enterprise_id'];
    protected $attributes = [
        'status' => 'pendente',  // Pode ser 'pending', 'confirmed', etc.
    ];
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_appointment_scheling', 'appointment_scheduling_id', 'department_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'appointment_scheduling_user')
                    ->withPivot('completed_at', 'balance_id');
    }

}
