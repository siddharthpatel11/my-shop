<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ColorApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Color::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('hex_code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'deleted');
        }

        $perPage = $request->input('per_page', 15);
        $colors = $query->orderBy('created_at', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $colors->items(),
            'meta' => [
                'current_page' => $colors->currentPage(),
                'last_page' => $colors->lastPage(),
                'per_page' => $colors->perPage(),
                'total' => $colors->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:colors,name',
                'hex_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            $validated['status'] = 'active';
            $color = Color::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Color created successfully.',
                'data' => $color
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Color $color): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $color
        ]);
    }

    public function update(Request $request, Color $color): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:colors,name,' . $color->id,
                'hex_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            $color->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Color updated successfully.',
                'data' => $color
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy(Color $color, Request $request): JsonResponse
    {
        // Validate that id is provided
        $request->validate([
            'id' => 'required|integer|exists:colors,id',
        ]);

        $color = Color::findOrFail($request->id);

        $color->update(['status' => 'deleted']);
        $color->delete();

        return response()->json([
            'success' => true,
            'message' => 'Color deleted successfully.'
        ]);
    }

    public function toggleStatus(Color $color): JsonResponse
    {
        $newStatus = $color->status === 'active' ? 'inactive' : 'active';
        $color->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Color status changed to {$newStatus} successfully.",
            'data' => $color
        ]);
    }
}
