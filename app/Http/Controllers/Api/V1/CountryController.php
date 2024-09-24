<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function getCountryList()
    {
        try{
            $page = request()->input('page', 1);
            $search = request()->input('search', '');
            $limit = request()->input('limit', 10);


            $countries_query = DB::table('countries')->orderByDesc('priority');

            if($search !== ""){
                $countries_query->where('country', 'LIKE', "%{$search}%");
            }

            $countries = $countries_query->paginate($limit, ['*'], 'page', $page);

            return ApiResponse::success($countries);

        }catch (Exception $e){
            return ApiResponse::error('Failed to retrieve countries', 500, $e->getMessage());
        }
    }

}
