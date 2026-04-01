<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected array $hiddenLocalizationFields = ['name_gu', 'name_hi', 'name_sa', 'name_bn', 'detail_gu', 'detail_hi', 'detail_sa', 'detail_bn', 'deleted_at'];

    /**
     * Get a paginated list of active products with their related details.
     * The translation accessors will automatically localize the response based on the ?lang= query param.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category'])
            ->where('status', 'active');

        // Optional filtering by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Optional search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Search across English/Hindi/Gujarati fields
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('name_hi', 'LIKE', "%{$search}%")
                    ->orWhere('name_gu', 'LIKE', "%{$search}%")
                    ->orWhere('name_sa', 'LIKE', "%{$search}%")
                    ->orWhere('name_bn', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(12);

        // Customize the resource payload
        $products->getCollection()->transform(function ($product) {

            // Forcefully override the raw db output with the dynamically calculated accessor (translation)
            $product->setAttribute('name', $product->name);
            $product->setAttribute('detail', $product->detail);
            $product->makeHidden($this->hiddenLocalizationFields);

            if ($product->category) {
                $product->category->setAttribute('name', $product->category->name);
                $product->category->makeHidden($this->hiddenLocalizationFields);
            }

            // Resolve Colors
            $colorIds = $product->colorIds();
            $colors = Color::whereIn('id', $colorIds)->get();
            $colors->each(function ($color) {
                $color->setAttribute('name', $color->name);
                $color->makeHidden($this->hiddenLocalizationFields);
            });
            $product->setAttribute('available_colors', $colors);

            // Resolve Sizes
            $sizeIds = $product->sizeIds();
            $sizes = Size::whereIn('id', $sizeIds)->get();
            $sizes->each(function ($size) {
                $size->setAttribute('name', $size->name);
                $size->makeHidden($this->hiddenLocalizationFields);
            });
            $product->setAttribute('available_sizes', $sizes);

            return $product;
        });

        return response()->json([
            'status' => 'success',
            'locale_applied' => app()->getLocale(),
            'data' => $products
        ]);
    }

    /**
     * Retrieve full details of a single product.
     */
    public function show($id)
    {
        $product = Product::with(['category'])
            ->where('status', 'active')
            ->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found or inactive.'
            ], 404);
        }

        // Forcefully override the raw db output with the dynamically calculated accessor (translation)
        $product->setAttribute('name', $product->name);
        $product->setAttribute('detail', $product->detail);
        $product->makeHidden($this->hiddenLocalizationFields);

        if ($product->category) {
            $product->category->setAttribute('name', $product->category->name);
            $product->category->makeHidden($this->hiddenLocalizationFields);
        }

        $colorIds = $product->colorIds();
        $colors = Color::whereIn('id', $colorIds)->get();
        $colors->each(function ($color) {
            $color->setAttribute('name', $color->name);
            $color->makeHidden($this->hiddenLocalizationFields);
        });
        $product->setAttribute('available_colors', $colors);

        $sizeIds = $product->sizeIds();
        $sizes = Size::whereIn('id', $sizeIds)->get();
        $sizes->each(function ($size) {
            $size->setAttribute('name', $size->name);
            $size->makeHidden($this->hiddenLocalizationFields);
        });
        $product->setAttribute('available_sizes', $sizes);

        return response()->json([
            'status' => 'success',
            'locale_applied' => app()->getLocale(),
            'data' => $product
        ]);
    }
}
