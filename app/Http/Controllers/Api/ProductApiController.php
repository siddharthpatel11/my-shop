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
                'name' => 'required|string|max:255',
                'detail' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'size_id' => 'nullable|array',
                'size_id.*' => 'exists:sizes,id',
                'color_id' => 'nullable|array',
                'color_id.*' => 'exists:colors,id',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // Convert arrays to comma-separated strings
            $validated['size_id'] = isset($validated['size_id'])
                ? implode(',', $validated['size_id'])
                : null;

            $validated['color_id'] = isset($validated['color_id'])
                ? implode(',', $validated['color_id'])
                : null;

            // Handle multiple image uploads
            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $name = time() . '_' . uniqid() . '.' . $image->extension();
                    $image->move(public_path('images/products'), $name);
                    $images[] = $name;
                }
            }

            $validated['image'] = !empty($images) ? implode(',', $images) : null;
            $validated['status'] = 'active';

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => new ProductResource($product->load('category'))
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
                'name' => 'required|string|max:255',
                'detail' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'size_id' => 'nullable|array',
                'size_id.*' => 'exists:sizes,id',
                'color_id' => 'nullable|array',
                'color_id.*' => 'exists:colors,id',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'remove_images' => 'nullable|array',
                'remove_images.*' => 'string',
            ]);

            // Convert arrays to comma-separated strings
            $validated['size_id'] = isset($validated['size_id'])
                ? implode(',', $validated['size_id'])
                : null;

            $validated['color_id'] = isset($validated['color_id'])
                ? implode(',', $validated['color_id'])
                : null;

            // Handle existing images
            $existingImages = $product->image ? explode(',', $product->image) : [];

            // Remove selected images
            if ($request->has('remove_images')) {
                foreach ($request->remove_images as $img) {
                    $path = public_path('images/products/' . $img);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $existingImages = array_diff($existingImages, [$img]);
                }
            }

            // Add new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $name = time() . '_' . uniqid() . '.' . $image->extension();
                    $image->move(public_path('images/products'), $name);
                    $existingImages[] = $name;
                }
            }

            $validated['image'] = !empty($existingImages) ? implode(',', $existingImages) : null;

            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => new ProductResource($product->load('category'))
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
