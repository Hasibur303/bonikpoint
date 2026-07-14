<?php

namespace App\Models;

use App\Support\OptimizedImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;

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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function offerProducts(): Collection
    {
        $productIds = $this->products()->pluck('products.id');
        $categoryIds = $this->categoryIdsForOffer();

        if ($productIds->isEmpty() && empty($categoryIds)) {
            return new Collection();
        }

        return Product::with('category')
            ->where('is_active', true)
            ->where(function ($query) use ($productIds, $categoryIds) {
                $query->whereIn('id', $productIds);

                if (! empty($categoryIds)) {
                    $query->orWhereIn('category_id', $categoryIds);
                }
            })
            ->latest()
            ->get();
    }

    public function includesProduct(Product $product): bool
    {
        if ($this->products()->whereKey($product->id)->exists()) {
            return true;
        }

        return in_array($product->category_id, $this->categoryIdsForOffer(), true);
    }

    public function categoryIdsForOffer(): array
    {
        return $this->categories()
            ->with('children:id,parent_id')
            ->get()
            ->flatMap(fn (Category $category) => $category->children->pluck('id')->push($category->id))
            ->unique()
            ->values()
            ->all();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getBannerUrlAttribute(): string
    {
        if (! $this->banner) {
            return OptimizedImage::url('assets/images/slider/slider-item-1.png');
        }

        return OptimizedImage::url($this->banner);
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
