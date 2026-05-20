<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'legacy_slug',
        'description',
        'price',
        'sale_price',
        'max_order_quantity',
        'manage_stock',
        'stock_quantity',
        'category_id',
        'customizable_product',
        'has_shape_options',
        'base_image',
        'shape_options',
        'color_options',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'max_order_quantity' => 'integer',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'integer',
            'customizable_product' => 'boolean',
            'has_shape_options' => 'boolean',
            'shape_options' => 'array',
            'color_options' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $source = $product->slug ?: $product->name;
            $product->slug = static::buildUniqueSeoSlug($source, $product->category_id);
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('slug') && !empty($product->slug)) {
                $product->slug = static::buildUniqueSeoSlug($product->slug, $product->category_id, $product->id);
            }
        });
    }

    public static function buildUniqueSeoSlug(string $source, ?int $categoryId = null, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'product';
        }

        if (!static::slugExists($base, $ignoreId)) {
            return $base;
        }

        $categorySuffix = '';
        if ($categoryId) {
            $category = Category::query()->select(['id', 'slug'])->find($categoryId);
            $categorySuffix = $category ? Str::slug((string) $category->slug) : '';
        }

        if ($categorySuffix !== '') {
            $candidate = $base . '-' . $categorySuffix;
            if (!static::slugExists($candidate, $ignoreId)) {
                return $candidate;
            }
        }

        $counter = 2;
        $stem = $categorySuffix !== '' ? $base . '-' . $categorySuffix : $base;
        do {
            $candidate = $stem . '-' . $counter;
            $counter++;
        } while (static::slugExists($candidate, $ignoreId));

        return $candidate;
    }

    private static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return static::query()
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function getEffectivePriceAttribute(): string
    {
        return $this->sale_price ?? $this->price;
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $imageQuery = $this->images();
        if (Schema::hasColumn('product_images', 'is_canvas_image')) {
            $imageQuery->where('is_canvas_image', false);
        }

        $primary = (clone $imageQuery)->where('is_primary', true)->first();
        if ($primary) {
            return asset('storage/' . $primary->image_path);
        }

        $first = (clone $imageQuery)->orderBy('sort_order')->first();
        if ($first) {
            return asset('storage/' . $first->image_path);
        }
        if (!empty($this->base_image)) {
            return asset('storage/' . $this->base_image);
        }
        return asset('images/no-image.png');
    }

    public function getIsInStockAttribute(): bool
    {
        if (!$this->manage_stock) {
            return true;
        }

        return (int) ($this->stock_quantity ?? 0) > 0;
    }

    public function belongsToMainCategorySlug(string $mainCategorySlug): bool
    {
        if (!$this->category_id) {
            return false;
        }

        $currentCategory = Category::query()
            ->select(['id', 'slug', 'parent_id'])
            ->find($this->category_id);

        while ($currentCategory) {
            if ($currentCategory->slug === $mainCategorySlug) {
                return true;
            }

            if (empty($currentCategory->parent_id)) {
                break;
            }

            $currentCategory = Category::query()
                ->select(['id', 'slug', 'parent_id'])
                ->find($currentCategory->parent_id);
        }

        return false;
    }

    public function canUseResinOrderType(): bool
    {
        return $this->customizable_product && $this->belongsToMainCategorySlug('resin-products');
    }

}
