<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Services\Category\CategoryService;
use App\Models\Category;

use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function __construct(
        private CategoryService $categoryService,
    ) {}

    public function index(Request $request): CategoryCollection
    {
        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null') {
                $categories = $this->categoryService->getRootCategories($request->input('type'));
            } else {
                $categories = $this->categoryService->getChildCategories((int) $request->parent_id);
            }
        } else {
            $categories = $this->categoryService->getRootCategories($request->input('type'));
        }

        return new CategoryCollection($categories);
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    public function breadcrumb(Category $category)
    {
        return $this->success($this->categoryService->getBreadcrumb($category));
    }
}