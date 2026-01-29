<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Size::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
                // ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'deleted');
        }

        $sizes = $query->orderBy('created_at', 'asc')->paginate(5)->withQueryString();

        return view('sizes.index', compact('sizes'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('sizes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name',
            // 'code' => 'nullable|string|max:10',
        ]);

        $validated['status'] = 'active';
        Size::create($validated);

        return redirect()->route('sizes.index')
            ->with('success', 'Size created successfully.');
    }

    // public function show(Size $size): View
    // {
    //     return view('sizes.show', compact('size'));
    // }

    public function edit(Size $size): View
    {
        return view('sizes.edit', compact('size'));
    }

    public function update(Request $request, Size $size): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name,' . $size->id,
            // 'code' => 'nullable|string|max:10',
        ]);

        $size->update($validated);

        return redirect()->route('sizes.index')
            ->with('success', 'Size updated successfully.');
    }

    public function destroy(Size $size): RedirectResponse
    {
        $size->update(['status' => 'deleted']);
        $size->delete();
        return redirect()->route('sizes.index')
            ->with('success', 'Size deleted successfully.');
    }

    public function toggleStatus(Size $size): RedirectResponse
    {
        $newStatus = $size->status === 'active' ? 'inactive' : 'active';
        $size->update(['status' => $newStatus]);

        return redirect()->route('sizes.index')
            ->with('success', "Size status changed to {$newStatus} successfully.");
    }
}
