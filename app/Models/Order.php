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
        'parcel_id',
        'steadfast_consignment_id',
        'steadfast_tracking_code',
        'steadfast_status',
        'steadfast_cod_amount',
        'steadfast_submitted_at',
        'steadfast_last_synced_at',
        'steadfast_last_error',
        'customer_name',
        'email',
        'mobile',
        'address',
        'city',
        'thana',
        'status',
        'is_offline_sale',
        'requires_courier',
        'offline_payment_collected',
        'subtotal',
        'shipping',
        'total',
        'adjustment_type',
        'adjustment_value',
        'discount_amount',
        'extra_charge_amount',
        'adjustment_reason',
        'adjustment_note',
        'adjusted_by',
        'adjusted_at',
        'advance_delivery_required',
        'delivery_area',
        'delivery_charge_payment_option',
        'delivery_payment_method',
        'delivery_payment_mobile',
        'delivery_transaction_id',
        'delivery_payment_proof',
        'notes',
        'cancellation_note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
            'adjustment_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'extra_charge_amount' => 'decimal:2',
            'steadfast_cod_amount' => 'decimal:2',
            'advance_delivery_required' => 'boolean',
            'is_offline_sale' => 'boolean',
            'requires_courier' => 'boolean',
            'offline_payment_collected' => 'boolean',
            'steadfast_submitted_at' => 'datetime',
            'steadfast_last_synced_at' => 'datetime',
            'adjusted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adjustedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
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
        if ($this->is_offline_sale && $this->offline_payment_collected) {
            return (float) $this->total;
        }

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

    public function hasAdjustment(): bool
    {
        return (float) $this->discount_amount > 0 || (float) $this->extra_charge_amount > 0;
    }

    public function originalTotal(): float
    {
        return (float) $this->subtotal + (float) $this->shipping;
    }

    public function adjustmentLabel(): ?string
    {
        return match ($this->adjustment_type) {
            'fixed_discount' => 'Special discount',
            'percentage_discount' => 'Special discount ('.rtrim(rtrim(number_format((float) $this->adjustment_value, 2, '.', ''), '0'), '.').'%)',
            'extra_charge' => 'Additional charge',
            default => null,
        };
    }

    public function hasSteadfastShipment(): bool
    {
        return filled($this->steadfast_consignment_id);
    }

    public function customerCanEditDetails(): bool
    {
        return in_array($this->status, ['waiting_delivery_charge', 'pending'], true)
            && ! $this->hasSteadfastShipment();
    }

    public function canSendToSteadfast(): bool
    {
        return (! $this->is_offline_sale || $this->requires_courier)
            && $this->status === 'confirmed'
            && ! $this->hasSteadfastShipment();
    }
}
