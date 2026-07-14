<?php

namespace App\Models;

use App\Support\OptimizedImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'description',
        'seo_title',
        'seo_description',
        'image',
        'image_alt',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function getPublicUrlAttribute(): string
    {
        if ($this->parent_id) {
            return route('categories.subcategory', [
                'parent' => $this->parent?->slug ?? $this->parent()->value('slug'),
                'category' => $this->slug,
            ]);
        }

        return route('categories.show', ['category' => $this->slug]);
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return OptimizedImage::url('assets/images/page-banner.jpg');
        }

        return OptimizedImage::url($this->image);
    }
}
