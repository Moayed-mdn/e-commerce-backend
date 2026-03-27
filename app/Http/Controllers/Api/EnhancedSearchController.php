<?php
// app/Http/Controllers/Api/EnhancedSearchController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnhancedSearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = Product::with([
                'category',
                'brand',
                'tags',
                'activeVariants.attributes',
                'images'
            ])->where('is_active', true);

            // Apply filters
            $query->withFilters($request->all());

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSorts = ['name', 'price', 'created_at', 'popularity'];
            if (in_array($sortBy, $allowedSorts)) {
                if ($sortBy === 'price') {
                    $query->join('product_variants', function($join) {
                        $join->on('products.id', '=', 'product_variants.product_id')
                             ->where('product_variants.is_active', true);
                    })->orderBy('product_variants.price', $sortOrder)
                      ->select('products.*');
                } elseif ($sortBy === 'popularity') {
                    $query->withCount(['orderItems as sales_count' => function($q) {
                        $q->whereHas('order', function($q) {
                            $q->where('payment_status', 'paid');
                        });
                    }])->orderBy('sales_count', $sortOrder);
                } else {
                    $query->orderBy($sortBy, $sortOrder);
                }
            }

            $perPage = $request->get('per_page', 12);
            $products = $query->paginate($perPage);

            return response()->json([
                'data' => $products->items(),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
                'filters' => $this->getAvailableFilters($request)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function popularProducts()
    {
        try {
            $products = Product::popular(10)
                ->with(['category', 'brand', 'activeVariants', 'images'])
                ->where('is_active', true)
                ->get();

            return response()->json([
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch popular products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function filterOptions()
    {
        try {
            $brands = Brand::where('is_active', true)
                ->withCount(['products' => function($q) {
                    $q->where('products.is_active', true); // Specify table name
                }])
                ->having('products_count', '>', 0)
                ->get();
    
            $categories = Category::withCount(['products' => function($q) {
                    $q->where('products.is_active', true); // Specify table name
                }])
                ->having('products_count', '>', 0)
                ->get();
    
            $tags = Tag::withCount(['products' => function($q) {
                    $q->where('products.is_active', true); // Specify table name
                }])
                ->having('products_count', '>', 0)
                ->get();
    
            $priceRange = [
                'min' => DB::table('product_variants')->where('is_active', true)->min('price'),
                'max' => DB::table('product_variants')->where('is_active', true)->max('price')
            ];
    
            return response()->json([
                'data' => [
                    'brands' => $brands,
                    'categories' => $categories,
                    'tags' => $tags,
                    'price_range' => $priceRange
                ]
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch filter options',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getAvailableFilters(Request $request)
    {
        $filters = [];

        if ($request->has('search')) {
            $filters['search'] = $request->search;
        }
        if ($request->has('brand')) {
            $filters['brand'] = Brand::whereIn('id', (array)$request->brand)->get();
        }
        if ($request->has('category')) {
            $filters['category'] = Category::whereIn('id', (array)$request->category)->get();
        }
        if ($request->has('tags')) {
            $filters['tags'] = Tag::whereIn('slug', (array)$request->tags)->get();
        }
        if ($request->has('min_price')) {
            $filters['min_price'] = $request->min_price;
        }
        if ($request->has('max_price')) {
            $filters['max_price'] = $request->max_price;
        }

        return $filters;
    }
}