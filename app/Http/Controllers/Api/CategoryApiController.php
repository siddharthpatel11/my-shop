<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CategoryApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();

        //Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%$search%");
        }

        //Statud filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'deleted');
        }

        //Pagination
        $perPage = $request->input('per_page', 15);
        $catagories = $query->orderBy('created_at', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($catagories->items()),
            'meta' => [
                'current_page' => $catagories->currentPage(),
                'last_page' => $catagories->lastPage(),
                'per_page' => $catagories->perPage(),
                'total' => $catagories->total(),
            ]
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
            ]);

            $validated['status'] = 'active';
            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data' => new CategoryResource($category)
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
     * Display the specified category.
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category)
        ]);
    }
    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            ]);
            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data' => new CategoryResource($category)
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
     * Remove the specified category.
     */
    public function destroy(Category $category, Request $request): JsonResponse
    {
        // Validate that id is provided
        $request->validate([
            'id' => 'required|integer|exists:categories,id'
        ]);

        $category = Category::findOrFail($request->id);

        $category->update(['status' => 'deleted']);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ]);
    }
    /**
     * Toggle category status between active and inactive.
     */
    public function toggleStatus(Category $category): JsonResponse
    {
        $newStatus = $category->status === 'active' ? 'inactive' : 'active';
        $category->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Category status changed to {$newStatus} successfully.",
            'data' => new CategoryResource($category)
        ]);
    }
}
