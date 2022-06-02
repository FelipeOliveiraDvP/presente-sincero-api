<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Disable the timestamps.
     * 
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Define the primary key as string.
     * 
     * @var string
     */
    public $keyType = 'string';

    /**
     * Disable the key auto incrementing.
     * 
     * @var boolean
     */
    public $incrementing = false;
}
