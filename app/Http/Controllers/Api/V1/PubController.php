<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PubController extends Controller
{

    public function index()
    {
        $pubs = DB::table('pubs')->get();
        return ApiResponse::success($pubs);
    }

    public function getPubByCity($city, Request $request){
        try{
            $search = request()->input('search', '');

            $pub_query = DB::table('pubs')->where('city', '=', $city);

            if($search !== ""){
                $pub_query->where('pub_name', 'LIKE', "%{$search}%");
            }

            $cities = $pub_query->get();

            return ApiResponse::success($cities);

        }catch (Exception $e){
            return ApiResponse::error('Failed to retrieve city', 500, $e->getMessage());
        }
    }

//    public function getMainPubContent($country_iso, Request $request)
//    {
//        try {
//            // Validate the country ISO code
//            if (empty($country_iso) || !is_string($country_iso)) {
//                return ApiResponse::error('Invalid country ISO code');
//            }
//
//            $country_data = DB::table('countries')
//                ->where('iso_code', $country_iso)
//                ->select('country_id', 'country', 'country_img')
//                ->first();
//
//            // Handle case where country data is not found
//            if (!$country_data) {
//                return ApiResponse::error('Country not found');
//            }
//
//            $cities = DB::table('cities')
//                ->where('country_id', $country_data->country_id)
//                ->get();
//
//            $city = $request->input('city');
//            $rating = $request->input('rating');
//            $pub_name = $request->input('search');
//            $sort_by = $request->input('sort', 'mp');
//            $page = (int) $request->input('page', 1);
//
//            $valid_sort_options = ['mp', 'rating'];
//            if (!in_array(strtolower($sort_by), $valid_sort_options)) {
//                $sort_by = 'mp'; // Default to 'mp' if invalid
//            }
//
//
//            $pub_query = DB::table('country_pub_view')
//                ->where('iso_code', $country_iso);
//
//            if (!empty($city)) {
//                $pub_query->where('city_id', $city);
//            }
//
//            if (!empty($rating) && is_numeric($rating)) {
//                $pub_query->where('overall_rating', '>=', (float) $rating);
//            }
//
//            if (!empty($pub_name)) {
//                $pub_query->where('pub_name', 'LIKE', "%{$pub_name}%");
//            }
//
//            $sort_by = strtolower($sort_by);
//            $sort_by_column = $sort_by === 'mp' ? 'review_count' : 'overall_rating';
//            $pub_query->orderBy($sort_by_column, 'DESC');
//
//
//            $pub_data = $pub_query->paginate(5, ['*'], 'page', $page);
//
//            $response = [
//                'pub_data' => $pub_data,
//                'cities' => $cities,
//                'country_data' => $country_data,
//            ];
//
//            return ApiResponse::success($response);
//
//        } catch (Exception $e) {
////            Log::error('Error in getMainPubContent: ' . $e->getMessage());
//            // Return a generic error message
//            return ApiResponse::error('An unexpected error occurred', 404);
//        }
//    }


    public function getMainPubContent($country_iso, Request $request)
    {
        try {
            // Validate the country ISO code
            if (empty($country_iso) || !is_string($country_iso)) {
                return ApiResponse::error('Invalid country ISO code', 400);
            }

            // Retrieve input parameters
            $city = $request->input('city');
            $rating = $request->input('rating');
            $pub_name = $request->input('search');
            $sort_by = $request->input('sort', 'mp');
            $page = (int) $request->input('page', 1);

            // Validate sort option
            $valid_sort_options = ['mp', 'hr'];
            if (!in_array(strtolower($sort_by), $valid_sort_options)) {
                $sort_by = 'mp'; // Default to 'mp' if invalid
            }

            // Initialize cities and pub_query
            $pub_query = DB::table('country_pub_view');

            if ($country_iso === "global") {
                $cities = DB::table('cities')->get();

            } else {

                $country_data = DB::table('countries')
                    ->where('iso_code', $country_iso)
                    ->select('country_id', 'country', 'country_img')
                    ->first();

                // Handle case where country data is not found
                if (!$country_data) {
                    return ApiResponse::error('Country not found', 404);
                }

                // Fetch cities for the specific country
                $cities = DB::table('cities')
                    ->where('country_id', $country_data->country_id)
                    ->get();

                // Filter pubs by country ISO code
                $pub_query->where('iso_code', $country_iso);
            }

            // Apply filters to the query
            if (!empty($city)) {
                $pub_query->where('city_id', $city);
            }

            if (!empty($rating) && is_numeric($rating)) {
                $pub_query->where('overall_rating', '>=', (float) $rating);
            }

            if (!empty($pub_name)) {
                $pub_query->where('pub_name', 'LIKE', "%{$pub_name}%");
            }

            // Apply sorting
            $sort_by_column = strtolower($sort_by) === 'mp' ? 'review_count' : 'overall_rating';
            $pub_query->orderBy($sort_by_column, 'DESC');

            // Paginate results
            $pub_data = $pub_query->paginate(5, ['*'], 'page', $page);

            $global_data = [
                    'country' => 'Worldwide',
                    'country_id' => '9999',
                    'country_img' => '/images/nasa-unsplash.jpg'
            ];

            // Prepare response data
            $response = [
                'pub_data' => $pub_data,
                'cities' => $cities,
                'country_data' => $country_iso === "global" ? $global_data : $country_data,
            ];

            return ApiResponse::success($response);

        } catch (Exception $e) {
            // Log the error for debugging purposes (uncomment if logging is set up)
            // Log::error('Error in getMainPubContent: ' . $e->getMessage());

            // Return a generic error message
            return ApiResponse::error('An unexpected error occurred', 500);
        }
    }


    public function getPubBySlug($slug){


        if (empty($slug) || !is_string($slug)) {
            return ApiResponse::error('Invalid slug', 400);
        }

        $pub_id = DB::table('pubs')->where('slug', $slug)->select('pub_id')->first();
        $pub_reviews = DB::table('pub_reviews')->where('pub', $pub_id->pub_id)->select('review_reference', 'created_at')->get();
        $pub_details = DB::table('country_pub_view')->where('slug', $slug)->first();
        $pub_details_extended = DB::table('pub_info')->where('pub', $pub_id->pub_id)->select('average_atmosphere_rating', 'average_aesthetic_rating', 'average_beer_selection_rating', 'average_value_rating', 'average_furniture_rating', 'average_bathroom_rating', 'overall_rating')->first();


        $response = [
            "pub_reviews"=>$pub_reviews,
            "pub_details"=>$pub_details,
            "pub_details_ratings"=>$pub_details_extended,
        ];


//        $pub_details = DB::table('pub_info')->where('pub_id', $pub->pub_info)->first();

        return ApiResponse::success($response);
    }


    public function generateSlug(){
        $string = strtolower("Test Pub 4");


        $string = preg_replace('/[^a-z0-9-]/', '-', $string);

        // Remove multiple consecutive hyphens
        $string = preg_replace('/-+/', '-', $string);

        // Trim hyphens from the beginning and end
        $string = trim($string, '-');

        $unique_suffix = bin2hex(random_bytes(5));
        $time_stamp = time();

        $slug = $string . "-" . $unique_suffix;

        return $slug;
    }


    }

