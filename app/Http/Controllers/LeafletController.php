<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Leaflet;
use DB,Exception;


class LeafletController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //

    public function getLeaflets(Request $request) {
        $params = $request->all();
        
        // $count  = Leaflet::count();
        // $itemPerPage = 5;
        // $offSet = 0;

        // if(sizeof($params) > 1 && array_key_exists("page", $params)) {
        //     $itemPerPage = array_key_exists("limit", $params) ? $params["limit"]:5;
        //     $offSet = ($params["page"] * $itemPerPage) - $itemPerPage;
        // }
        // $arrLeaflets = Leaflet::get()->take($itemPerPage)->skip($offSet)->toArray();
        //return response()->json(['data' => $arrLeaflets,"totalCount" => $count]);
        
        $days_tolerance = 0;
        $num_of_rows_required = 0;
        try {
            if(sizeof($params) > 0) { 
                $days_tolerance = $params["days_tolerance"];
                $num_of_rows_required = $params["num_of_rows_required"];
                $arrData = DB::select("EXEC [dbo].[sp_proc_get_Leaflets] @days_tolerance='".$days_tolerance."', @num_of_rows_required='".$num_of_rows_required."'");
                
                return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            }
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }

    }
}
