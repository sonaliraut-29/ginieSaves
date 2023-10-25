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
        
        $days_tolerance = 0;
        $num_of_rows_required = 0;
        
        try {
            if(sizeof($params) > 0) { 
                $days_tolerance = $params["days_tolerance"];
                $num_of_rows_required = $params["num_of_rows_required"];
                $arrData = DB::select("EXEC [dbo].[sp_proc_get_leaflets] @days_tolerance='".$days_tolerance."', @num_of_rows_required='".$num_of_rows_required."'");
                
                return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            }
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function getAllLeaflets(Request $request) {
        $params = $request->all();

        $days_tolerance = 0;
        $num_of_rows_required=0;
        $Start_offset = 0;
        $Vendor = "*";
        $Category = "*";
        try {
            if(sizeof($params) > 0) { 
                $days_tolerance = $params["days_tolerance"];
                $num_of_rows_required = $params["num_of_rows_required"];
                $Start_offset = $params["Start_offset"];
                $Vendor = $params && array_key_exists("Vendor", $params) ? $params["Vendor"]:"*";
                $Category =  $params && array_key_exists("Category", $params)  ? $params["Category"]:"*";
                
                
                $exec = "EXEC [dbo].[sp_proc_Get_Leaflets_N_Filters] @days_tolerance=".$days_tolerance.", @num_of_rows_required=".$num_of_rows_required.", @Start_offset='".$Start_offset."', @Vendor='".$Vendor."', @Category='".$Category."'";

                $pdo = \DB::connection()->getPdo();
                $sql = $exec;
                $stmt = $pdo->query($sql);
                $stmt->execute();
                $rowset1 = $stmt->fetchAll();
               
                // $stmt->nextRowset();
                // $rowset2 = $stmt->fetchAll();
    
                // if(sizeof($rowset2) > 0) {
                //     $rowset2 = $rowset2[0]["Total_Items_Found"];
                // }
                
                return response()->json(['data' => $rowset1, 'status' => 200, "success" => true]);
            }
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }
}
