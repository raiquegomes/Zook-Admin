<?php

namespace App\Models;

use App\Enums\UserActivityUser;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'assigned_date',
        'status',
    ];

    protected $casts = [
        'status' => UserActivityUser::class,
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
}
