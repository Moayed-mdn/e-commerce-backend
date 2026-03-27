<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
{
    $query = Product::query()
        ->when($request->q,fn($query,$q) => $query->where('name','like',"%$q%"));

    
    $filterQuery = clone $query;
    $productIds = $filterQuery->select('id');

    $filters = DB::table('product_variants')
        ->whereIn('product_id',$productIds)
        ->selectRaw("
            MIN(price) AS min_price,
            MAX(price) AS max_price,
            MIN(manufacture_date) AS earliest_manufacture,
            MAX(expiry_date) AS  latest_expiry
        ")->first();

    // dd();
   

        // ->paginate(15)
        // ->withQueryString();
    $paginator = $query->paginate($request->per_page ?? 15);

    return response()->json([
        "data" => $paginator->items(),
        "pagination" => [
            "current_page" => $paginator->currentPage(),
            "last_page" => $paginator->lastPage(),
            "total" => $paginator->total(),
            "per_page" => $paginator->perPage(),
            "next_page_url" => $paginator->nextPageUrl(),
            "prev_page_url" => $paginator->previousPageUrl()
        ],
        "filters" => $filters
        
    ]);
}
    
}
