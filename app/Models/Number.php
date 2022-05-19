<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    use HasFactory;

    protected $fillable = [
        'contest_id',
        'price',
        'start',
        'quantity',
        'per_customer',
        'numbers',
    ];

    protected $casts = [
        'numbers' => 'json'
    ];
}
