<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'order_number',
        'customer_name',
        'email',
        'mobile',
        'address',
        'city',
        'status',
        'subtotal',
        'shipping',
        'total',
        'advance_delivery_required',
        'delivery_area',
        'delivery_charge_payment_option',
        'delivery_payment_method',
        'delivery_payment_mobile',
        'delivery_transaction_id',
        'delivery_payment_proof',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
            'advance_delivery_required' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function paidAmount(): float
    {
        if ($this->status === 'delivered') {
            return (float) $this->total;
        }

        if ($this->advance_delivery_required && $this->delivery_charge_payment_option === 'pay_now') {
            return min((float) $this->shipping, (float) $this->total);
        }

        return 0;
    }

    public function dueAmount(): float
    {
        if (in_array($this->status, ['delivered', 'cancelled'], true)) {
            return 0;
        }

        return max((float) $this->total - $this->paidAmount(), 0);
    }
}
