<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BestSellerResource;
use App\Http\Resources\HeroBannerResource;
use App\Services\BestSellerService;
use App\Services\HomePageService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class HomePageController extends Controller
{
    public function __construct(
        protected HomePageService $homePageService,
        protected BestSellerService $bestSellerService
    ) {
    }

    public function bestSeller(): JsonResponse
    {
        $dtos = $this->bestSellerService->getCachedAllParents(20);

        return ApiResponse::success(BestSellerResource::collection($dtos));
    }

    public function hero(): JsonResponse
    {
        $banners = $this->homePageService->hero();

        return ApiResponse::success(HeroBannerResource::collection($banners));
    }
}