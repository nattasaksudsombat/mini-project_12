<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_address_id',
        'status',
        'payment_status',
        'subtotal',
        'shipping_fee',
        'discount',
        'total_amount',
        'total_price',
        'tracking_number',
        'slip_image',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the customer address for the order.
     */
    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Get the order items (alias for compatibility)
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Scope a query to only include orders with a specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include orders with a specific payment status.
     */
    public function scopePaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Get the total amount attribute (accessor).
     */
    public function getTotalAttribute()
    {
        return $this->subtotal + $this->shipping_fee - $this->discount;
    }

    /**
     * Get status label in Thai.
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'รอดำเนินการ',
            'processing' => 'กำลังดำเนินการ',
            'shipped' => 'จัดส่งแล้ว',
            'delivered' => 'ส่งสำเร็จ',
            'cancelled' => 'ยกเลิก',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get payment status label in Thai.
     */
    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'รอชำระเงิน',
            'paid' => 'ชำระเงินแล้ว',
            'refunded' => 'คืนเงินแล้ว',
        ];

        return $labels[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get payment status badge color.
     */
    public function getPaymentStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'paid' => 'success',
            'refunded' => 'info',
        ];

        return $colors[$this->payment_status] ?? 'secondary';
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if order can be edited.
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if order is paid.
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if order is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}