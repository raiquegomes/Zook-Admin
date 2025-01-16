<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListProductsCount extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'enterprise_id', 'type'];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
