<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SizeResource;
use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SizeApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        //search functionality
        $query = Size::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%$search%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'deleted');
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $sizes = $query->orderBy('created_at', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => SizeResource::collection($sizes->items()),
            'meta' => [
                'current_page' => $sizes->currentPage(),
                'last_page' => $sizes->lastPage(),
                'per_page' => $sizes->perPage(),
                'total' => $sizes->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:sizes,name',
            ]);
            $validated['status'] = 'active';
            $size = Size::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Size created successfully',
                'data' => new SizeResource($size),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }
    public function show(Size $size): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new SizeResource($size)
        ]);
    }

    public function update(Request $request, Size $size): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:sizes,name,' . $size->id,
            ]);

            $size->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Size updated successfully.',
                'data' => new SizeResource($size)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }
    public function destroy(Size $size, Request $request): JsonResponse
    {
        // Validate that id is provided
        $request->validate([
            'id' => 'required|integer|exists:sizes,id',
        ]);

        $size = Size::findOrFail($request->id);

        $size->update(['status' => 'deleted']);
        $size->delete();

        return response()->json([
            'success' => true,
            'message' => 'Size deleted successfully.'
        ]);
    }
    public function toggleStatus(Size $size): JsonResponse
    {
        $newStatus = $size->status === 'active' ? 'inactive' : 'active';
        $size->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Size status changed to {$newStatus} successfully.",
            'data' => new SizeResource($size)
        ]);
    }
}
