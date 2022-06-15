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
        'slug',
        'start_date',
        'contest_date',
        'max_reserve_days',
        'show_percentage',
        'use_custom_percentage',
        'paid_percentage',
        'custom_percentage',
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

    /**
     * Cast attributes into other types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'double',
        'paid_percentage' => 'double',
        'custom_percentage' => 'double',
        'show_percentage' => 'boolean',
        'use_custom_percentage' => 'boolean',
    ];

    public function bank_accounts()
    {
        return $this->belongsToMany(BankAccount::class, 'contest_bank', 'contest_id', 'bank_id');
    }


    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'contest_id', 'id');
    }


    public function sales()
    {
        return $this->hasMany(Sale::class, 'contest_id', 'id');
    }
}
