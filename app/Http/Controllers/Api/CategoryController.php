<?php
// app/Http/Controllers/Api/CategoryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): CategoryCollection
    {
        $query = Category::with(['children', 'parent'])
            ->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }]);

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
    }

    public function show(Category $category): CategoryResource
    {
        $category->load(['children', 'parent']);
        $category->loadCount(['products' => function ($q) {
            $q->where('is_active', true);
        }]);

        return new CategoryResource($category);
    }

    public function breadcrumb(Category $category): JsonResponse
    {
        return ApiResponse::success($category->breadcrumb);
    }
}