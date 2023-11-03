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

                $concatCategory = '*';
                if($params && array_key_exists("Category", $params)) {
                    $splitCategory = explode('|', $params["Category"]);
                    $concatCategory = '';
                    if(sizeof($splitCategory) > 1) {
                        for($i = 0; $i < sizeof($splitCategory); $i++ ) {
                            $newCategory = str_replace('_and_', ' & ',$splitCategory[$i]);
                            $newCategory = str_replace('and', '&',$newCategory);
                            if($i !== sizeof($splitCategory) -1 ) {
                                $concatCategory .= "''".$newCategory."'',";
                            } else {
                                $concatCategory .= "''".$newCategory."''";
                            }
                        }
                    } else if(sizeof($splitCategory) ==1 && "*" !== $splitCategory[0]) {
                        $newCategory = str_replace('_and_', ' & ',$params['Category']);
                        $newCategory = str_replace('and', '&',$newCategory);

                        $concatCategory = "''".$newCategory."''";
                    } else {
                        $concatCategory = "*";
                    }
                }

                $concatVendors = '*';
                if($params && array_key_exists("Vendor", $params)) {
                    $splitVendors = explode(',', $params["Vendor"]);
                    $concatVendors = '';
                    if(sizeof($splitVendors) > 1) {
                        for($i = 0; $i < sizeof($splitVendors); $i++ ) {
                            if($i !== sizeof($splitVendors) -1 ) {
                                $concatVendors .= "''".$splitVendors[$i]."'',";
                            } else {
                                $concatVendors .= "''".$splitVendors[$i]."''";
                            }
                        }
                    } else if(sizeof($splitVendors) ==1 && "*" !== $splitVendors[0]) {
                        $concatVendors = "''".$params['Vendor']."''";
                    } else {
                        $concatVendors ="*";
                    }
                }
                
                $days_tolerance = $params["days_tolerance"];
                $num_of_rows_required = $params["num_of_rows_required"];
                $Start_offset = $params["Start_offset"];
                $Vendor = $concatVendors;
                $Category =  $concatCategory;
                
                
                $exec = "EXEC [dbo].[sp_proc_Get_Leaflets_N_Filters] @days_tolerance=".$days_tolerance.", @num_of_rows_required=".$num_of_rows_required.", @Start_offset='".$Start_offset."', @Vendor='".$Vendor."', @Category='".$Category."'";

                $pdo = \DB::connection()->getPdo();
                $sql = $exec;
                $stmt = $pdo->query($sql);
                $stmt->execute();
                $rowset1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);
               
                $stmt->nextRowset();
                $rowset2 = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
                if(sizeof($rowset2) > 0) {
                    $rowset2 = $rowset2[0]["Num_of_Rows"];
                }
                // print_r($exec );
                return response()->json(['data' => $rowset1, "totalCount" => $rowset2,'status' => 200, "success" => true]);
            }
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }
}
