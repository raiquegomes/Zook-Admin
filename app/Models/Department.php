<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\SoftDeletes;


class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'work_days',
        'office_day',
        'enterprise_id',
        'is_scale',
        'holidays',
        'show_notice_board',
        'show_supplier_count',
        'department_master_id'
    ];

    protected $casts = [
        'work_days' => 'array',
        'holidays' => 'array',
        'show_notice_board' => 'boolean',
        'show_supplier_count' => 'boolean',
    ];

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user', 'department_id', 'user_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'department_master_id');
    }

    public function childDepartments()
    {
        return $this->hasMany(Department::class, 'department_master_id');
    }
}
