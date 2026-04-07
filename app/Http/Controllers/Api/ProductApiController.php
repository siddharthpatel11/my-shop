<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Laravel\Facades\Image;

class ProductApiController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('detail', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['active', 'inactive']);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $products = $query->orderBy('created_at', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:products,name',
                'detail' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'size_id' => 'nullable|array',
                'size_id.*' => 'exists:sizes,id',
                'color_id' => 'nullable|array',
                'color_id.*' => 'exists:colors,id',

                // Variant-based image data
                'image_data' => 'nullable|array',
                'image_data.*.files' => 'nullable|array',
                'image_data.*.files.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'image_data.*.color_id' => 'nullable|exists:colors,id',
                'image_data.*.size_id' => 'nullable|exists:sizes,id',
                'image_data.*.price' => 'nullable|numeric|min:0',
                'image_data.*.stock' => 'nullable|integer|min:0',
            ], [
                'name.unique' => 'A product with this name already exists. Please choose a different name.',
            ]);

            // Convert core arrays to comma-separated strings
            $validated['size_id'] = isset($validated['size_id']) ? implode(',', $validated['size_id']) : null;
            $validated['color_id'] = isset($validated['color_id']) ? implode(',', $validated['color_id']) : null;

            $validated['status'] = 'active';
            $product = Product::create($validated);

            $allStoredNames = [];

            // Handle multiple variant rows
            if ($request->has('image_data')) {
                foreach ($request->image_data as $index => $variant) {
                    $colorId = $variant['color_id'] ?? null;
                    $sizeId  = $variant['size_id'] ?? null;
                    $price   = $variant['price'] ?? null;
                    $stock   = $variant['stock'] ?? 0;

                    if (isset($variant['files']) && is_array($variant['files'])) {
                        foreach ($variant['files'] as $file) {
                            $name = time() . '_' . uniqid() . '.' . $file->extension();

                            // RESIZE and SAVE via Intervention
                            $path = public_path('images/products/' . $name);
                            Image::read($file)->scale(width: 1200)->save($path);

                            $allStoredNames[] = $name;

                            \App\Models\ProductImage::create([
                                'product_id' => $product->id,
                                'color_id'   => $colorId,
                                'size_id'    => $sizeId,
                                'image'      => $name,
                                'price'      => $price,
                                'stock'      => $stock,
                                'sort_order' => $index
                            ]);
                        }
                    }
                }
            }

            // Update main product image column with full gallery string
            if (!empty($allStoredNames)) {
                $product->update(['image' => implode(',', $allStoredNames)]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => new ProductResource($product->load(['category', 'images', 'images.color', 'images.size']))
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ProductResource($product->load('category'))
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:products,name,' . $product->id,
                'detail' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'size_id' => 'nullable|array',
                'size_id.*' => 'exists:sizes,id',
                'color_id' => 'nullable|array',
                'color_id.*' => 'exists:colors,id',

                // Existing variant updates
                'existing_image_data' => 'nullable|array',
                'existing_image_data.*.color_id' => 'nullable|exists:colors,id',
                'existing_image_data.*.size_id' => 'nullable|exists:sizes,id',
                'existing_image_data.*.price' => 'nullable|numeric|min:0',
                'existing_image_data.*.stock' => 'nullable|integer|min:0',

                // Inline files for existing variants
                'existing_variant_files' => 'nullable|array',
                'existing_variant_files.*' => 'nullable|array',
                'existing_variant_files.*.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',

                // New variant images
                'image_data' => 'nullable|array',
                'image_data.*.files' => 'nullable|array',
                'image_data.*.files.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'image_data.*.color_id' => 'nullable|exists:colors,id',
                'image_data.*.size_id' => 'nullable|exists:sizes,id',
                'image_data.*.price' => 'nullable|numeric|min:0',
                'image_data.*.stock' => 'nullable|integer|min:0',

                'remove_images' => 'nullable|array',
                'remove_images.*' => 'string',
            ], [
                'name.unique' => 'A product with this name already exists. Please choose a different name.',
            ]);

            // Convert core arrays to comma-separated strings
            $validated['size_id'] = isset($validated['size_id']) ? implode(',', $validated['size_id']) : null;
            $validated['color_id'] = isset($validated['color_id']) ? implode(',', $validated['color_id']) : null;

            // 1. Handle image removals
            if ($request->has('remove_images')) {
                foreach ($request->remove_images as $imgName) {
                    $path = public_path('images/products/' . $imgName);
                    if (file_exists($path)) unlink($path);
                    \App\Models\ProductImage::where('product_id', $product->id)->where('image', $imgName)->delete();
                }
            }

            // 2. Update existing variant metadata & process inline files
            if ($request->has('existing_image_data')) {
                foreach ($request->existing_image_data as $imgId => $meta) {
                    \App\Models\ProductImage::where('id', $imgId)->update([
                        'color_id' => $meta['color_id'] ?: null,
                        'size_id'  => $meta['size_id'] ?? null,
                        'price'    => $meta['price'] ?? null,
                        'original_price' => $meta['original_price'] ?? null,
                        'stock'    => $meta['stock'] ?? 0,
                    ]);

                    // Add new files to this specific variant
                    if ($request->hasFile("existing_variant_files.{$imgId}")) {
                        $files = $request->file("existing_variant_files.{$imgId}");
                        foreach ($files as $file) {
                            $name = time() . '_' . uniqid() . '.' . $file->extension();
                            $path = public_path('images/products/' . $name);
                            Image::read($file)->scale(width: 1200)->save($path);

                            \App\Models\ProductImage::create([
                                'product_id' => $product->id,
                                'color_id'   => $meta['color_id'] ?: null,
                                'size_id'    => $meta['size_id'] ?? null,
                                'image'      => $name,
                                'price'      => $meta['price'] ?? null,
                                'original_price' => $meta['original_price'] ?? null,
                                'stock'      => $meta['stock'] ?? 0,
                                'sort_order' => 110
                            ]);
                        }
                    }
                }
            }

            // 3. Process entirely new variant rows
            if ($request->has('image_data')) {
                foreach ($request->image_data as $index => $variant) {
                    $colorId = $variant['color_id'] ?? null;
                    $sizeId  = $variant['size_id'] ?? null;
                    $price   = $variant['price'] ?? null;
                    $stock   = $variant['stock'] ?? 0;

                    if (isset($variant['files']) && is_array($variant['files'])) {
                        foreach ($variant['files'] as $file) {
                            $name = time() . '_' . uniqid() . '.' . $file->extension();
                            $path = public_path('images/products/' . $name);
                            Image::read($file)->scale(width: 1200)->save($path);

                            \App\Models\ProductImage::create([
                                'product_id' => $product->id,
                                'color_id'   => $colorId,
                                'size_id'    => $sizeId,
                                'image'      => $name,
                                'price'      => $price,
                                'stock'      => $stock,
                                'sort_order' => 200 + $index
                            ]);
                        }
                    }
                }
            }

            // Sync main images column
            $allImages = \App\Models\ProductImage::where('product_id', $product->id)->orderBy('sort_order')->pluck('image')->toArray();
            $validated['image'] = implode(',', $allImages);

            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => new ProductResource($product->load(['category', 'images', 'images.color', 'images.size']))
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified product (soft delete).
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        // Check if ID is in query parameter
        // if ($request->has('id')) {
        $request->validate([
            'id' => 'required|integer|exists:products,id'
        ]);

        $product = Product::findOrFail($request->id);
        // }

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        // Delete images
        if ($product->image) {
            foreach (explode(',', $product->image) as $img) {
                $path = public_path('images/products/' . $img);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        $product->update(['status' => 'deleted']);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }

    /**
     * Toggle product status between active and inactive.
     */
    public function toggleStatus(Product $product): JsonResponse
    {
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Product status changed to {$newStatus} successfully.",
            'data' => new ProductResource($product->load('category'))
        ]);
    }

    /**
     * Get dropdown data for creating/editing products.
     */
    public function formData(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => Category::where('status', 'active')
                    ->orderBy('name')
                    ->get(['id', 'name']),
                'sizes' => Size::where('status', 'active')
                    ->orderBy('name')
                    ->get(['id', 'name']),
                'colors' => Color::where('status', 'active')
                    ->orderBy('name')
                    ->get(['id', 'name', 'hex_code']),
            ]
        ]);
    }
}
