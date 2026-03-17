<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');

        // Status filter
        if (!$request->filled('status')) {
            $query->whereIn('status', ['active', 'inactive']);
        } else {
            $query->where('status', $request->status);
        }

        // Search
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

        $products = $query
            ->orderBy('created_at', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Needed for index view
        $sizes  = Size::all();
        $colors = Color::all();

        return view('products.index', compact('products', 'sizes', 'colors'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    // public function index(Request $request): View
    // {
    //     $query = Product::query();

    //     // Search functionality
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'LIKE', "%{$search}%")
    //                 ->orWhere('detail', 'LIKE', "%{$search}%")
    //                 ->orWhere('category', 'LIKE', "%{$search}%");
    //         });
    //     }

    //     // Status filter
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     $products = $query->latest()->paginate(5)->withQueryString();

    //     return view('products.index', compact('products'))
    //         ->with('i', (request()->input('page', 1) - 1) * 5);
    // }

    public function create(): View
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $sizes      = Size::where('status', 'active')->orderBy('name')->get();
        $colors     = Color::where('status', 'active')->orderBy('name')->get();

        return view('products.create', compact('categories', 'sizes', 'colors'));
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // MULTIPLE → string
        $data['size_id']  = implode(',', $request->size_id ?? []);
        $data['color_id'] = implode(',', $request->color_id ?? []);

        /* ===== MULTIPLE IMAGES ===== */
        $images = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $name = time() . '_' . uniqid() . '.' . $img->extension();
                $img->move(public_path('images/products'), $name);
                $images[] = $name;
            }
        }

        $data['image'] = implode(',', $images);

        if ($request->hasFile('seo_meta_image')) {
            $name = 'seo_' . time() . '_' . uniqid() . '.' . $request->seo_meta_image->extension();
            $request->seo_meta_image->move(public_path('images/products'), $name);
            $data['seo_meta_image'] = $name;
        }

        if ($request->hasFile('og_meta_image')) {
            $name = 'og_' . time() . '_' . uniqid() . '.' . $request->og_meta_image->extension();
            $request->og_meta_image->move(public_path('images/products'), $name);
            $data['og_meta_image'] = $name;
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /* ================= SHOW ================= */
    public function show(Product $product): View
    {
        $sizes  = Size::all();
        $colors = Color::all();

        return view('products.show', compact('product', 'sizes', 'colors'));
    }

    /* ================= EDIT ================= */
    public function edit(Product $product): View
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $sizes      = Size::where('status', 'active')->orderBy('name')->get();
        $colors     = Color::where('status', 'active')->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'sizes', 'colors'));
    }

    /* ================= UPDATE ================= */
    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        // MULTIPLE → string
        $data['size_id']  = implode(',', $request->size_id ?? []);
        $data['color_id'] = implode(',', $request->color_id ?? []);

        /* ===== EXISTING IMAGES ===== */
        $existingImages = $product->image
            ? explode(',', $product->image)
            : [];

        // remove selected images
        if ($request->remove_images) {
            foreach ($request->remove_images as $img) {
                if (file_exists(public_path('images/products/' . $img))) {
                    unlink(public_path('images/products/' . $img));
                }
                $existingImages = array_diff($existingImages, [$img]);
            }
        }

        // add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $name = time() . '_' . uniqid() . '.' . $img->extension();
                $img->move(public_path('images/products'), $name);
                $existingImages[] = $name;
            }
        }

        $data['image'] = implode(',', $existingImages);

        if ($request->remove_seo_image) {
            if ($product->seo_meta_image && file_exists(public_path('images/products/' . $product->seo_meta_image))) {
                unlink(public_path('images/products/' . $product->seo_meta_image));
            }
            $data['seo_meta_image'] = null;
        }

        if ($request->hasFile('seo_meta_image')) {
            if ($product->seo_meta_image && file_exists(public_path('images/products/' . $product->seo_meta_image))) {
                unlink(public_path('images/products/' . $product->seo_meta_image));
            }
            $name = 'seo_' . time() . '_' . uniqid() . '.' . $request->seo_meta_image->extension();
            $request->seo_meta_image->move(public_path('images/products'), $name);
            $data['seo_meta_image'] = $name;
        }

        if ($request->remove_og_image) {
            if ($product->og_meta_image && file_exists(public_path('images/products/' . $product->og_meta_image))) {
                unlink(public_path('images/products/' . $product->og_meta_image));
            }
            $data['og_meta_image'] = null;
        }

        if ($request->hasFile('og_meta_image')) {
            if ($product->og_meta_image && file_exists(public_path('images/products/' . $product->og_meta_image))) {
                unlink(public_path('images/products/' . $product->og_meta_image));
            }
            $name = 'og_' . time() . '_' . uniqid() . '.' . $request->og_meta_image->extension();
            $request->og_meta_image->move(public_path('images/products'), $name);
            $data['og_meta_image'] = $name;
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /* ================= DELETE ================= */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            foreach (explode(',', $product->image) as $img) {
                if (file_exists(public_path('images/products/' . $img))) {
                    unlink(public_path('images/products/' . $img));
                }
            }
        }

        if ($product->seo_meta_image && file_exists(public_path('images/products/' . $product->seo_meta_image))) {
            unlink(public_path('images/products/' . $product->seo_meta_image));
        }

        if ($product->og_meta_image && file_exists(public_path('images/products/' . $product->og_meta_image))) {
            unlink(public_path('images/products/' . $product->og_meta_image));
        }

        $product->update(['status' => 'deleted']);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product marked as deleted successfully.');
    }

    /* ================= STATUS TOGGLE ================= */
    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product status updated successfully.');
    }

    /**
     * Check if product name exists (for AJAX validation)
     */
    public function checkName(Request $request): \Illuminate\Http\JsonResponse
    {
        $name = $request->name;
        $id = $request->id;

        $exists = Product::where('name', $name)
            ->when($id, function ($query) use ($id) {
                return $query->where('id', '!=', $id);
            })
            ->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'A product with this name already exists. Please choose a different name.' : ''
        ]);
    }
}
