<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\GetBestSellersAction;
use App\Actions\GetHeroBannersAction;
use App\DTOs\GetBestSellersDTO;
use App\DTOs\GetHeroBannersDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\HomePage\GetBestSellersRequest;
use App\Http\Requests\HomePage\GetHeroBannersRequest;
use App\Http\Resources\BestSellerResource;
use App\Http\Resources\HeroBannerResource;
use Illuminate\Http\JsonResponse;

class HomePageController extends Controller
{
    public function __construct(
        private GetBestSellersAction $getBestSellersAction,
        private GetHeroBannersAction $getHeroBannersAction,
    ) {}

    public function bestSeller(GetBestSellersRequest $request): JsonResponse
    {
        $dtos = $this->getBestSellersAction->execute(
            GetBestSellersDTO::fromRequest($request)
        );

        return $this->success(BestSellerResource::collection($dtos));
    }

    public function hero(GetHeroBannersRequest $request): JsonResponse
    {
        $banners = $this->getHeroBannersAction->execute(
            GetHeroBannersDTO::fromRequest($request)
        );

        return $this->success(HeroBannerResource::collection($banners));
    }
}
