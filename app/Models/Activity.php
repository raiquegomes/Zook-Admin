<?php

namespace App\Models;

use App\Enums\ActivityStatus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'frequency', 'specific_day', 'enterprise_id', 'order_number', 'status'];
    protected $casts = [
        'status' => ActivityStatus::class,
    ];
    public function rules()
    {
        return $this->hasMany(ActivityRules::class);
    }

    public function userActivities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'activity_department', 'activity_id', 'department_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

}
