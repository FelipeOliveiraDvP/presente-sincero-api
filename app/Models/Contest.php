<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The soft delete date column.
     * 
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Enable timestamps.
     * 
     * @var boolean
     */
    protected $timestatmps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'winner_id',
        'title',
        'start_date',
        'contest_date',
        'price',
        'quantity',
        'short_description',
        'full_description',
        'whatsapp_number',
        'whatsapp_group',
        'numbers',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];
}
