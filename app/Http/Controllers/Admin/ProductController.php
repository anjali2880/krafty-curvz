<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    private function isResinCategoryOrChild(?int $categoryId): bool
    {
        if (!$categoryId) {
            return false;
        }

        $category = Category::query()->select(['id', 'slug', 'parent_id'])->find($categoryId);
        while ($category) {
            if ($category->slug === 'resin-products') {
                return true;
            }

            if (!$category->parent_id) {
                break;
            }

            $category = Category::query()->select(['id', 'slug', 'parent_id'])->find($category->parent_id);
        }

        return false;
    }

    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images'])->latest();

        if ($request->filled('product_name')) {
            $query->where('name', 'like', '%' . trim((string) $request->input('product_name')) . '%');
        }

        if ($request->filled('status')) {
            $status = (string) $request->input('status');
            if (in_array($status, ['published', 'unpublished'], true)) {
                $query->where('is_active', $status === 'published');
            }
        }

        if ($request->filled('subcategory_id')) {
            $query->where('category_id', $request->integer('subcategory_id'));
        } elseif ($request->filled('category_id')) {
            $mainCategory = Category::with('children')->find($request->integer('category_id'));
            if ($mainCategory) {
                $categoryIds = [$mainCategory->id];
                if ($mainCategory->children->count() > 0) {
                    $categoryIds = array_merge($categoryIds, $mainCategory->children->pluck('id')->toArray());
                }
                $query->whereIn('category_id', $categoryIds);
            }
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::where('is_active', true)
            ->where('parent_id', null)
            ->with('children')
            ->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'max_order_quantity' => 'required|integer|min:1|max:999',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0|max:999999',
            'category_id' => 'required|exists:categories,id',
            'customizable_product' => 'boolean',
            'has_shape_options' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'custom_images' => 'nullable|array',
            'custom_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'nullable|string|max:50|required_with:sizes.*.price',
            'sizes.*.price' => 'nullable|numeric|min:0|required_with:sizes.*.name',
            'shape_options' => 'nullable|array',
            'shape_options.*' => 'nullable|string|max:50',
            'color_options' => 'nullable|array',
            'color_options.*' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ], [
            'images.*.max' => 'Product image size is too large. Maximum allowed size is 2MB.',
            'custom_images.*.max' => 'Customizable product image size is too large. Maximum allowed size is 2MB.',
        ]);

        if (empty($validated['slug'])) {
            unset($validated['slug']);
        }

        $validated['customizable_product'] = $request->boolean('customizable_product');
        $validated['has_shape_options'] = $request->boolean('has_shape_options');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['manage_stock'] = $request->boolean('manage_stock');
        $validated['stock_quantity'] = $validated['manage_stock'] ? 0 : null;

        $images = $request->file('images', []);
        $customImages = $request->file('custom_images', []);
        $sizes = $request->input('sizes', []);
        $shapeOptions = collect($request->input('shape_options', []))
            ->filter(fn ($shape) => is_string($shape) && trim($shape) !== '')
            ->map(fn ($shape) => trim($shape))
            ->unique()
            ->values()
            ->all();
        $colorOptions = collect($request->input('color_options', []))
            ->filter(fn ($color) => is_string($color) && preg_match('/^#([A-Fa-f0-9]{6})$/', trim($color)))
            ->map(fn ($color) => strtoupper(trim($color)))
            ->unique()
            ->values()
            ->all();

        // If shape options are enabled but no valid shapes provided, use default
        if (($validated['has_shape_options'] ?? false) && count($shapeOptions) === 0) {
            $shapeOptions = ['Round'];
        }

        unset($validated['images']);
        unset($validated['sizes']);
        unset($validated['custom_images']);
        unset($validated['shape_options']);
        unset($validated['color_options']);

        if (Schema::hasColumn('products', 'shape_options')) {
            $validated['shape_options'] = $validated['has_shape_options'] ?? false ? $shapeOptions : [];
        }
        if (Schema::hasColumn('products', 'color_options')) {
            $validated['color_options'] = $colorOptions;
        }

        if (!Schema::hasColumn('products', 'has_shape_options')) {
            unset($validated['has_shape_options']);
        }

        $allowCustomCanvasImages = $validated['customizable_product']
            && $this->isResinCategoryOrChild((int) ($validated['category_id'] ?? 0));

        $product = Product::create($validated);

        $hasCanvasImageColumn = Schema::hasColumn('product_images', 'is_canvas_image');
        $sortOrder = 0;
        if (!empty($images)) {
            foreach ($images as $index => $image) {
                $path = $image->store('products', 'public');
                $imagePayload = [
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $sortOrder,
                ];
                if ($hasCanvasImageColumn) {
                    $imagePayload['is_canvas_image'] = false;
                }
                ProductImage::create($imagePayload);
                $sortOrder++;
            }
        }

        if ($allowCustomCanvasImages && !empty($customImages)) {
            foreach ($customImages as $canvasImage) {
                $path = $canvasImage->store('products', 'public');
                $imagePayload = [
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $sortOrder,
                ];
                if ($hasCanvasImageColumn) {
                    $imagePayload['is_canvas_image'] = true;
                }
                ProductImage::create($imagePayload);
                $sortOrder++;
            }
        }

        if (!empty($sizes)) {
            foreach ($sizes as $index => $sizeData) {
                if (!empty($sizeData['name']) && !empty($sizeData['price'])) {
                    ProductSize::create([
                        'product_id' => $product->id,
                        'name' => $sizeData['name'],
                        'price' => $sizeData['price'],
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)->get();
        $product->load(['images', 'sizes']);
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'max_order_quantity' => 'required|integer|min:1|max:999',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0|max:999999',
            'category_id' => 'required|exists:categories,id',
            'customizable_product' => 'boolean',
            'has_shape_options' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'custom_images' => 'nullable|array',
            'custom_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'nullable|string|max:50|required_with:sizes.*.price',
            'sizes.*.price' => 'nullable|numeric|min:0|required_with:sizes.*.name',
            'shape_options' => 'nullable|array',
            'shape_options.*' => 'nullable|string|max:50',
            'color_options' => 'nullable|array',
            'color_options.*' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ], [
            'images.*.max' => 'Product image size is too large. Maximum allowed size is 2MB.',
            'custom_images.*.max' => 'Customizable product image size is too large. Maximum allowed size is 2MB.',
        ]);

        unset($validated['slug']);

        $validated['customizable_product'] = $request->boolean('customizable_product');
        $validated['has_shape_options'] = $request->boolean('has_shape_options');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['manage_stock'] = $request->boolean('manage_stock');
        $validated['stock_quantity'] = $validated['manage_stock'] ? 0 : null;

        $images = $request->file('images', []);
        $customImages = $request->file('custom_images', []);
        $sizes = $request->input('sizes', []);
        $shapeOptions = collect($request->input('shape_options', []))
            ->filter(fn ($shape) => is_string($shape) && trim($shape) !== '')
            ->map(fn ($shape) => trim($shape))
            ->unique()
            ->values()
            ->all();
        $colorOptions = collect($request->input('color_options', []))
            ->filter(fn ($color) => is_string($color) && preg_match('/^#([A-Fa-f0-9]{6})$/', trim($color)))
            ->map(fn ($color) => strtoupper(trim($color)))
            ->unique()
            ->values()
            ->all();

        // If shape options are enabled but no valid shapes provided, use default
        if (($validated['has_shape_options'] ?? false) && count($shapeOptions) === 0) {
            $shapeOptions = ['Round'];
        }

        unset($validated['images']);
        unset($validated['sizes']);
        unset($validated['custom_images']);
        unset($validated['shape_options']);
        unset($validated['color_options']);

        if (Schema::hasColumn('products', 'shape_options')) {
            $validated['shape_options'] = $validated['has_shape_options'] ?? false ? $shapeOptions : [];
        }
        if (Schema::hasColumn('products', 'color_options')) {
            $validated['color_options'] = $colorOptions;
        }

        if (!Schema::hasColumn('products', 'has_shape_options')) {
            unset($validated['has_shape_options']);
        }

        $allowCustomCanvasImages = $validated['customizable_product']
            && $this->isResinCategoryOrChild((int) ($validated['category_id'] ?? 0));

        $product->update($validated);

        $hasCanvasImageColumn = Schema::hasColumn('product_images', 'is_canvas_image');
        $sortOrder = (int) $product->images()->count();
        if (!empty($images)) {
            foreach ($images as $image) {
                $path = $image->store('products', 'public');
                $imagePayload = [
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $sortOrder,
                ];
                if ($hasCanvasImageColumn) {
                    $imagePayload['is_canvas_image'] = false;
                }
                ProductImage::create($imagePayload);
                $sortOrder++;
            }
        }

        if ($allowCustomCanvasImages && !empty($customImages)) {
            foreach ($customImages as $canvasImage) {
                $path = $canvasImage->store('products', 'public');
                $imagePayload = [
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $sortOrder,
                ];
                if ($hasCanvasImageColumn) {
                    $imagePayload['is_canvas_image'] = true;
                }
                ProductImage::create($imagePayload);
                $sortOrder++;
            }
        }

        // Update sizes - delete all and recreate
        $product->sizes()->delete();
        if (!empty($sizes)) {
            foreach ($sizes as $index => $sizeData) {
                if (!empty($sizeData['name']) && !empty($sizeData['price'])) {
                    ProductSize::create([
                        'product_id' => $product->id,
                        'name' => $sizeData['name'],
                        'price' => $sizeData['price'],
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        if ($product->base_image) {
            Storage::disk('public')->delete($product->base_image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function togglePublish(Product $product): RedirectResponse
    {
        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return back()->with(
            'success',
            $product->is_active ? 'Product published successfully.' : 'Product unpublished successfully.'
        );
    }

    public function deleteImage(Product $product, ProductImage $image): RedirectResponse
    {
        if ((int) $image->product_id !== (int) $product->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image deleted successfully.');
    }

    public function setPrimaryImage(Product $product, ProductImage $image): RedirectResponse
    {
        if ((int) $image->product_id !== (int) $product->id) {
            abort(404);
        }

        if (Schema::hasColumn('product_images', 'is_canvas_image') && $image->is_canvas_image) {
            return back()->with('error', 'Canvas images cannot be set as primary product image.');
        }

        $regularImagesQuery = $product->images();
        if (Schema::hasColumn('product_images', 'is_canvas_image')) {
            $regularImagesQuery->where('is_canvas_image', false);
        }

        $regularImagesQuery->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated.');
    }
}
