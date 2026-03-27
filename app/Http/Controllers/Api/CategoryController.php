<?php
// app/Http/Controllers/Api/CategoryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Category::with(['children', 'parent'])
                ->withCount(['products' => function($q) {
                    $q->where('is_active', true);
                }]);

            // Get root categories by default
            if ($request->has('parent_id')) {
                if ($request->parent_id === 'null') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $request->parent_id);
                }
            } else {
                $query->whereNull('parent_id');
            }

            $categories = $query->get();

            return new CategoryCollection($categories);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch categories',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Category $category)
    {
        try {
            $category->load(['children', 'parent']);
            $category->loadCount(['products' => function($q) {
                $q->where('is_active', true);
            }]);

            return new CategoryResource($category);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch category',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function breadcrumb(Category $category)
    {   
        return response()->json([
            'data' => $category->breadcrumb
        ]);

    }
}