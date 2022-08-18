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
    protected $table = 'failed_orders';

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
        'order_id',
        'customer_id',
        'contest_id',
        'cause',
        'current_order_status',
        'next_order_status',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class, 'contest_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
