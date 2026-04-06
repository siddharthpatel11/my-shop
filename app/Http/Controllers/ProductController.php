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
use App\Models\ProductImage;
use Intervention\Image\Laravel\Facades\Image;

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

        /* ===== COLOR-WISE IMAGES (NEW NESTED STRUCTURE) ===== */
        $productImages = [];
        $allStoredNames = [];

        if ($request->has('image_data')) {
            foreach ($request->image_data as $index => $variant) {
                // Get row-specific variant details
                $colorId = $variant['color_id'] ?? null;
                $sizeId  = $variant['size_id'] ?? null;
                $price   = $variant['price'] ?? null;
                $stock   = $variant['stock'] ?? 0;

                // Handle multiple files for this specific row
                if (isset($variant['files']) && is_array($variant['files'])) {
                    foreach ($variant['files'] as $file) {
                        $name = time() . '_' . uniqid() . '.' . $file->extension();

                        // RESIZE and SAVE via Intervention
                        $path = public_path('images/products/' . $name);
                        Image::read($file)->scale(width: 1200)->save($path);

                        $allStoredNames[] = $name;

                        ProductImage::create([
                            'product_id' => null, // Will set after product create
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

        $data['image'] = implode(',', $allStoredNames);

        if ($request->hasFile('seo_meta_image')) {
            $name = 'seo_' . time() . '_' . uniqid() . '.' . $request->seo_meta_image->extension();
            $path = public_path('images/products/' . $name);
            Image::read($request->seo_meta_image)->scale(width: 1200)->save($path);
            $data['seo_meta_image'] = $name;
        }

        if ($request->hasFile('og_meta_image')) {
            $name = 'og_' . time() . '_' . uniqid() . '.' . $request->og_meta_image->extension();
            $path = public_path('images/products/' . $name);
            Image::read($request->og_meta_image)->scale(width: 1200)->save($path);
            $data['og_meta_image'] = $name;
        }

        $product = Product::create($data);

        // Update temp product_id in images
        ProductImage::where('product_id', null)->whereIn('image', $allStoredNames)->update(['product_id' => $product->id]);

        // Sync main image string with table to ensure consistency
        $allImages = ProductImage::where('product_id', $product->id)->pluck('image')->toArray();
        $product->update(['image' => implode(',', $allImages)]);

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

        /* ===== HANDLE REMOVED IMAGES ===== */
        $existingImages = $product->image ? explode(',', $product->image) : [];

        if ($request->remove_images) {
            foreach ($request->remove_images as $imgName) {
                // Delete file
                if (file_exists(public_path('images/products/' . $imgName))) {
                    unlink(public_path('images/products/' . $imgName));
                }
                // Delete from table
                ProductImage::where('product_id', $product->id)->where('image', $imgName)->delete();
                // Remove from local array
                $existingImages = array_diff($existingImages, [$imgName]);
            }
        }

        /* ===== IMAGE UPDATE LOGIC (NESTED) ===== */
        $allStoredNames = $existingImages; // Start with non-deleted existing images

        // 1. Update EXISTING variant records
        if ($request->existing_image_colors) {
            foreach ($request->existing_image_colors as $imgName => $colorId) {
                ProductImage::where('product_id', $product->id)
                    ->where('image', $imgName)
                    ->update([
                        'color_id' => $colorId ?: null,
                        'size_id'  => $request->existing_image_sizes[$imgName] ?? null,
                        'price'    => $request->existing_image_prices[$imgName] ?? null,
                        'stock'    => $request->existing_image_stocks[$imgName] ?? 0,
                    ]);
            }
        }

        // 2. Process NEW images (potentially multiple per variant row)
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

                        ProductImage::create([
                            'product_id' => $product->id,
                            'color_id'   => $colorId,
                            'size_id'    => $sizeId,
                            'image'      => $name,
                            'price'      => $price,
                            'stock'      => $stock,
                            'sort_order' => 100 + $index
                        ]);
                    }
                }
            }
        }

        // 3. Sync product main image column
        $product->update(['image' => implode(',', $allStoredNames)]);

        /* ===== SEO & OG IMAGES ===== */
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
            $path = public_path('images/products/' . $name);
            Image::read($request->seo_meta_image)->scale(width: 1200)->save($path);
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
            $path = public_path('images/products/' . $name);
            Image::read($request->og_meta_image)->scale(width: 1200)->save($path);
            $data['og_meta_image'] = $name;
        }

        $product->update($data);

        // Final sync of main image string from table record to prevent duplication/stale data
        $allImages = ProductImage::where('product_id', $product->id)->orderBy('sort_order')->pluck('image')->toArray();
        $product->update(['image' => implode(',', $allImages)]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /* ================= DELETE ================= */
    public function destroy(Product $product): RedirectResponse
    {
        // Delete all associated images
        foreach ($product->images as $img) {
            if (file_exists(public_path('images/products/' . $img->image))) {
                unlink(public_path('images/products/' . $img->image));
            }
            $img->delete();
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
