<?php

namespace App\Models;

use App\Enums\UserActivityUser;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'assigned_date',
        'status',
        'attachments',
        'observation',
        'observation_reject',
        'department_master_id'
    ];

    protected $casts = [
        'status' => UserActivityUser::class,
        'attachments' => 'array',
    ];

    public function files()
    {
        return $this->hasMany(UserActivityFile::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departmentMaster()
    {
        return $this->belongsTo(Departament::class, 'department_master_id');
    }
}
