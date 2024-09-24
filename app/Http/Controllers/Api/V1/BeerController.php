<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeerController extends Controller
{
    public function getBeerList(Request $request)
    {
        try{
            $search = request()->input('search', '');

            $beer_query = DB::table('beers');

            if($search !== ""){
                $beer_query->where('beer_name', 'LIKE', "%{$search}%");
            }

            $beers = $beer_query->get();

            return ApiResponse::success($beers);

        }catch (Exception $e){
            return ApiResponse::error('Failed to retrieve beers', 500, $e->getMessage());
        }
    }

}
