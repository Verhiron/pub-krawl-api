<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    public function getCityList()
    {
        $cities = DB::table('cities')->get();
        return ApiResponse::success($cities);
    }


    public function cityByCountry($country){
        $cities = DB::select('CALL getAllCitiesByCountry(?)', [$country]);
        return ApiResponse::success($cities);
    }

    public function getCities($country, Request $request){
        try{
            $search = request()->input('search', '');

            $country_id = DB::table('countries')->where('iso_code', '=', $country)->value('country_id');

            if(!$country_id){
                throw new Exception('Country not found');
            }

            $cities_query = DB::table('cities')->where('country_id', '=', $country_id);

            if($search !== ""){
                $cities_query->where('city_name', 'LIKE', "%{$search}%");
            }

            $cities = $cities_query->get();

            return ApiResponse::success($cities);

        }catch (Exception $e){
            return ApiResponse::error('Failed to retrieve city', 500, $e->getMessage());
        }
    }


}
