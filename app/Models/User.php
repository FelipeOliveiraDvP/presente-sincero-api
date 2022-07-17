<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'name',
        'whatsapp',
        'email',
        'password',
        'username',
        'avatar',
        'role',
        'mp_access_token',
        'seller_approved',
        'blocked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'deleted_at',
    ];

    /**
     * Cast attributes into other types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'seller_approved' => 'boolean',
        'blocked' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role', 'id');
    }
}
