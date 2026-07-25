<?php

namespace App\Models;

use App\Support\OptimizedImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'vape_device_type',
        'slug',
        'description',
        'seo_title',
        'seo_description',
        'buying_price',
        'price',
        'compare_price',
        'stock',
        'sku',
        'image',
        'image_alt',
        'advance_delivery_charge',
        'warranty_type',
        'warranty_duration',
        'warranty_details',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'buying_price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'advance_delivery_charge' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getRouteKey(): mixed
    {
        return $this->slug ?: $this->getKey();
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        $query = $this->where('slug', $value);

        if (ctype_digit((string) $value)) {
            $query->orWhere($this->getKeyName(), $value);
        }

        return $query->first();
    }

    public function hasWarranty(): bool
    {
        return $this->warranty_type && $this->warranty_type !== 'none';
    }

    public function isAgeRestricted(): bool
    {
        $category = $this->relationLoaded('category') ? $this->category : $this->category()->with('parent')->first();
        $slugs = collect([$category?->slug, $category?->parent?->slug])->filter();

        return $slugs->contains(fn (string $slug) => str_contains($slug, 'vape'));
    }

    public function getWarrantyLabelAttribute(): string
    {
        return match ($this->warranty_type) {
            'guarantee' => 'Guarantee',
            'service_warranty' => 'Service Warranty',
            'replacement_warranty' => 'Replacement Warranty',
            'brand_warranty' => 'Brand Warranty',
            default => 'No Warranty',
        };
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function festivals(): BelongsToMany
    {
        return $this->belongsToMany(Festival::class)->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class)->orderBy('sort_order')->orderBy('id');
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class)->orderBy('sort_order')->orderBy('id');
    }

    public function flavors(): HasMany
    {
        return $this->hasMany(ProductFlavor::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return OptimizedImage::url('assets/images/product/product-01.jpg');
        }

        return OptimizedImage::url($this->image);
    }
}
