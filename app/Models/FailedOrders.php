<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedOrders extends Model
{
    use HasFactory;

    /**
     * The table id.
     * 
     * @var int
     */
    public $id;

    /**
     * The order id.
     * 
     * @var int
     */
    public $order_id;

    /**
     * The contest id.
     * 
     * @var int
     */
    public $contest_id;

    /**
     * The customer id.
     * 
     * @var int
     */
    public $customer_id;

    /**
     * The error description.
     * 
     * @var string
     */
    public $cause;

    /**
     * The current order status.
     * 
     * @var enum[PROCESSING, PENDING, CANCELED, CONFIRMED]
     */
    public $current_order_status;

    /**
     * The expected next order status.
     * 
     * @var enum[PROCESSING, PENDING, CANCELED, CONFIRMED]
     */
    public $next_order_status;


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

    // protected $guarded = [];

    // protected $dispatchesEvents = [
    //   'created' => PaymentProcessing::class,
    // ];

    public function contest()
    {
        return $this->belongsTo(Contest::class, 'contest_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
