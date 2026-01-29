<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Color::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('hex_code', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'deleted');
        }

        $colors = $query->orderBy('created_at','asc')->paginate(5)->withQueryString();

        return view('colors.index', compact('colors'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('colors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
            'hex_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $validated['status'] = 'active';
        Color::create($validated);

        return redirect()->route('colors.index')
            ->with('success', 'Color created successfully.');
    }

    // public function show(Color $color): View
    // {
    //     return view('colors.show', compact('color'));
    // }

    public function edit(Color $color): View
    {
        return view('colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:colors,name,'.$color->id,
            'hex_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $color->update($validated);

        return redirect()->route('colors.index')
            ->with('success', 'Color updated successfully.');
    }

    public function destroy(Color $color): RedirectResponse
    {
        $color->update(['status' => 'deleted']);
        $color->delete();
        return redirect()->route('colors.index')
            ->with('success', 'Color deleted successfully.');
    }

    public function toggleStatus(Color $color): RedirectResponse
    {
        $newStatus = $color->status === 'active' ? 'inactive' : 'active';
        $color->update(['status' => $newStatus]);

        return redirect()->route('colors.index')
            ->with('success', "Color status changed to {$newStatus} successfully.");
    }
}
