<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = ['repositor_id', 'date', 'responses', 'score', 'enterprise_id'];

    protected $casts = [
        'responses' => 'array',
    ];

    public function repositor()
    {
        return $this->belongsTo(Repositor::class);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
