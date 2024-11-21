<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repositor extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
