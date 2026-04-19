<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->when($request->q, fn ($query, $q) => $query->where('name', 'like', "%$q%"));


        $filterQuery = clone $query;
        $productIds = $filterQuery->select('id');

        $filters = DB::table('product_variants')
            ->whereIn('product_id', $productIds)
            ->selectRaw("
            MIN(price) AS min_price,
            MAX(price) AS max_price,
            MIN(manufacture_date) AS earliest_manufacture,
            MAX(expiry_date) AS  latest_expiry
        ")->first();

        $paginator = $query->paginate($request->per_page ?? 15);

        return ApiResponse::paginated(
            paginator: $paginator,
            data: $paginator->items(),
            additionalMeta: [
                'filters' => $filters
            ]
        );
    }
}