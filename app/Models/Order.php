<?php

namespace App\Models;

use App\Events\PaymentProcessing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The database table.
   * 
   * @var string
   */
  protected $table = 'orders';

  /**
   * Enable timestamps.
   * 
   * @var boolean
   */
  protected $timestatmps = true;

  /**
   * The soft delete date column.
   * 
   * @var array
   */
  protected $dates = ['deleted_at'];

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'contest_id',
    'user_id',
    'total',
    'numbers',
    'status',
    'transaction_code',
    'confirmed_at',
  ];

  /**
   * Cast attributes into other types.
   * 
   * @var array<string, string>
   */
  protected $casts = [
    'total' => 'double'
  ];

  public function contest()
  {
    return $this->belongsTo(Contest::class, 'contest_id', 'id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}
