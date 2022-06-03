<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The database table.
     * 
     * @var string
     */
    protected $table = 'bank_accounts';

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
        'name',
        'cc',
        'agency',
        'dv',
        'key',
        'main',
        'type',
    ];

    /**
     * Cast attributes into other types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'main' => 'boolean'
    ];
}
