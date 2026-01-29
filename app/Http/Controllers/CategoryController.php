<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'deleted');
        }

        $categories = $query->orderBy('created_at','asc')->paginate(5)->withQueryString();

        return view('categories.index', compact('categories'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            // 'description' => 'nullable|string',
        ]);

        $validated['status'] = 'active';
        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    // public function show(Category $category): View
    // {
    //     return view('categories.show', compact('category'));
    // }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            // 'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->update(['status' => 'deleted']);
        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleStatus(Category $category): RedirectResponse
    {
        $newStatus = $category->status === 'active' ? 'inactive' : 'active';
        $category->update(['status' => $newStatus]);

        return redirect()->route('categories.index')
            ->with('success', "Category status changed to {$newStatus} successfully.");
    }
}
