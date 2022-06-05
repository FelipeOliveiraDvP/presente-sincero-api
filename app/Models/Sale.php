<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    /**
     * Disable the timestamps.
     * 
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'contest_id',
        'quantity',
        'price',
    ];

    /**
     * Cast attributes into other types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'double'
    ];
}
