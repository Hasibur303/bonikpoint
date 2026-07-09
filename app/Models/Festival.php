<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Festival extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner',
        'discount_percentage',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_percentage' => 'decimal:2',
            'is_active' => 'boolean',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getBannerUrlAttribute(): string
    {
        if (! $this->banner) {
            return asset('assets/images/slider/slider-item-1.png');
        }

        if (str_starts_with($this->banner, 'assets/')) {
            return asset($this->banner);
        }

        return Storage::url($this->banner);
    }

    public function discountedPrice(Product $product): float
    {
        $discount = min(100, max(0, (float) $this->discount_percentage));

        return round((float) $product->price * (1 - ($discount / 100)), 2);
    }

    public function isRunning(): bool
    {
        $today = now()->toDateString();

        return $this->is_active
            && (! $this->starts_at || $this->starts_at->toDateString() <= $today)
            && (! $this->ends_at || $this->ends_at->toDateString() >= $today);
    }
}
