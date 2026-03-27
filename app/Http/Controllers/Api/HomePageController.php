<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BestSellerResource;
use App\Http\Resources\HeroBannerResource;
use App\Models\Category;
use App\Services\BestSellerService;
use App\Services\HomePageService;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function __construct(protected HomePageService $homePageService,protected BestSellerService $bestSellerService)
    {
        
    }

    public function bestSeller()
    {
        $dtos = $this->bestSellerService->getCachedAllParents(20);

       
        return $this->dataSuccessResponse(BestSellerResource::collection($dtos));
    }

    public function hero()
    {
        $banners = $this->homePageService->hero();

        return $this->dataSuccessResponse(HeroBannerResource::collection($banners));
    }

}