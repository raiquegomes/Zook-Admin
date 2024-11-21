<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_activity_id',
        'name',
        'path',
    ];

    public function useractivity()
    {
        return $this->hasMany(UserActivity::class);
    }
}
