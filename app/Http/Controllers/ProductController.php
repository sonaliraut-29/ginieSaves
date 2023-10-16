<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Product;
use DB,Exception;

class ProductController extends Controller
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

    public function getProductDetail(Request $request) {
        $params = $request->all();
        
        $Vendor = "";
        $Item_Key = "";

        try {
            $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Item_Details] @Vendor='".$Vendor."', @Item_Key='".$Item_Key."'");
            
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }

    }

    public function getFilterLIst(Request $request) {
        $params = $request->all();
        $category1 = '';
        $category2 = '';
        $category3 = '';
        $price_from = 0;
        $price_to = 99999;
        $vendor = '';
        $brand = '';
        $exclude_accessory = 0;
        $only_discounted = 0;
        $available_only = 0;

        $search_text = '';
        $order_by = 'ORDER BY NEWID()';
        $offset_rows = 0;
        $page_size = 20;

        try {
            if(sizeof($params) > 0) {
                foreach ($params as $key => $value) {
                    if("brand" == $key) {
                        $splitBrands = explode(',', $params["brand"]);
                        $concatBrands = '';
                        if(sizeof($splitBrands) > 1) {
                            for($i = 0; $i < sizeof($splitBrands); $i++ ) {
                                if($i !== sizeof($splitBrands) -1 ) {
                                    $concatBrands .= "''".$splitBrands[$i]."'',";
                                } else {
                                    $concatBrands .= "''".$splitBrands[$i]."''";
                                }
                            }
                        } else {
                            $concatBrands = "''".$params['brand']."''";
                        }
                    }

                    if("vendor" == $key) {
                        $splitVendors = explode(',', $params["vendor"]);
                        $concatVendors = '';
                        if(sizeof($splitVendors) > 1) {
                            for($i = 0; $i < sizeof($splitVendors); $i++ ) {
                                if($i !== sizeof($splitVendors) -1 ) {
                                    $concatVendors .= "''".$splitVendors[$i]."'',";
                                } else {
                                    $concatVendors .= "''".$splitVendors[$i]."''";
                                }
                            }
                        } else {
                            $concatVendors = "''".$params['vendor']."''";
                        }
                    }

                    switch($key) {
                        case "category1";
                            $category1 = $params["category1"];
                        break;

                        case "category1";
                            $category2 = $params["category2"];
                        break;

                        case "category3";
                            $category3 = $params["category3"];
                        break;

                        case "price_from";
                            $price_from = $params["price_from"];
                        break;

                        case "price_to";
                            $price_to = $params["price_to"];
                        break;

                        case "vendor";
                            $vendor = $concatVendors;
                        break;

                        case "brand";
                            $brand = $concatBrands;
                        break;

                        case "exclude_accessory";
                            $exclude_accessory = $params["exclude_accessory"];
                        break;

                        case "only_discounted";
                            $only_discounted = $params["only_discounted"];
                        break;

                        case "available_only";
                            $available_only = $params["available_only"];
                        break;

                        case "search_text";
                            $search_text = $params["search_text"];
                        break;

                        case "order_by";
                            $order_by = " ORDER BY ".$params["order_by"]. " ". $params["sort"];
                        break;

                        case "offset_rows";
                            $offset_rows = (int)$params["offset_rows"];
                        break;

                        case "page_size";
                            $page_size = (int)$params["page_size"];
                        break;
                    }
                    
                }
            }
            
            $arrData = DB::select("EXEC [dbo].[sp_proc_get_items] @category1='".$category1."', @category2='".$category2."',@category3='". $category3 ."',@price_from=". $price_from .",@price_to=". $price_to .",@vendor='". $vendor ."',@brand='". $brand ."',@exclude_accessory=".$exclude_accessory.",@only_discounted=".$only_discounted.",@available_only=".$available_only.",@search_text='".$search_text."',@order_by='".$order_by."',@offset_rows=".$offset_rows.",@page_size=".$page_size."");
            
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }   
    }


    public function getPopularItems(Request $request) {
        try {
            $arrData = DB::select("EXEC [dbo].[sp_proc_get_Popular_Items] @num_of_rows_required=10");
            
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }   
    }

    public function getCategories(Request $request) {
        try {
            $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Category]");
            
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function getSubcategories(Request $request) {
        $params = $request->all();
        $Category = "*";

        $errors = [];

        try {
            if(sizeof($params) > 0) {
                foreach ($params as $key => $value) {
                    switch($key) {
                        case "Category";
                            $Category = $params["Category"];
                             if($Category == "") {
                                $errors = $errors->push((object)['Category' => "Category can not be empty"]);
                            }
                        break;
                    }
                    
                }
            }
            if(sizeof($errors) > 0 ) {
                return response()->json(['data' => $errors, 'status' => 400, "success" => false]);
            } else {
                $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Sub_Catagory] @Category='".$Category."'");
                return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
            }
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }
}
