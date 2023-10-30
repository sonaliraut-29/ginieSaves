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

    public function getProductDetail(Request $request,$Vendor, $ItemKey) {

        try {
            $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Item_Details] @Vendor='".$Vendor."', @Item_Key='".$ItemKey."'");
            
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }

    }

    public function getFilterLIst(Request $request) {
        $params = $request->all();
        $user_id = null;
        $category = '';
        $sub_category = '';
        // $category3 = '';
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
                    $concatBrands = '';
                    if("brand" == $key && "" !== $params["brand"]) {
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
                    $concatCategory = '';
                    if("category" == $key && "" !== $params["category"]) {
                        $splitCategory = explode('|', $params["category"]);
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
                        } else {
                            $newCategory = str_replace('_and_', ' & ',$params['category']);
                            $newCategory = str_replace('and', '&',$newCategory);

                            $concatCategory = "''".$newCategory."''";
                        }
                    }
                    $concatSubCategory = "";
                    if("sub_category" == $key && "" !== $params["sub_category"]) {
                        $splitSubCategory = explode('|', $params["sub_category"]);
                        $concatSubCategory = '';
                        if(sizeof($splitSubCategory) > 1) {
                            for($i = 0; $i < sizeof($splitSubCategory); $i++ ) {
                                $newCategory = str_replace('_and_', ' & ',$splitSubCategory[$i]);
                                $newCategory = str_replace('and', '&',$newCategory);
                                if($i !== sizeof($splitSubCategory) -1 ) {
                                    $concatSubCategory .= "''".$newCategory."'',";
                                } else {
                                    $concatSubCategory .= "''".$newCategory."''";
                                }
                            }
                        } else {
                            $newCategory = str_replace('_and_', ' & ',$params['sub_category']);
                            $newCategory = str_replace('and', '&',$newCategory);
                            $concatSubCategory = "''".$newCategory."''";
                        }
                    }
                    $concatVendors = '';
                    if("vendor" == $key && "" !== $params["vendor"]) {
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
                        case 'user_id':
                            $user_id = $params['user_id'];
                        break;
                        case "category";
                            $category = $concatCategory;
                        break;

                        case "sub_category";
                            $sub_category = $concatSubCategory;
                        break;

                        // case "category3";
                        //     $category3 = $params["category3"];
                        // break;

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
                            if("Discount_Percent" == $params["order_by"] ) {
                                $order_by = " ORDER BY Discount_Percent ".$params["sort"].", Discounted_Price ".$params["sort"];
                            } else if("Brand" == $params["order_by"] ) {
                                $order_by = " ORDER BY Brand ".$params["sort"].", Discounted_Price ".$params["sort"];
                            } else {

                                $order_by = " ORDER BY ".$params["order_by"]. " ". $params["sort"];
                            }
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
            if($user_id !== "") {
                $exec = "EXEC [dbo].[sp_proc_get_items] @user_id=".$user_id.", @category='".$category."', @sub_category='".$sub_category."',@price_from=". $price_from .",@price_to=". $price_to .",@vendor='". $vendor ."',@brand='". $brand ."',@exclude_accessory=".$exclude_accessory.",@only_discounted=".$only_discounted.",@available_only=".$available_only.",@search_text='".$search_text."',@order_by='".$order_by."',@offset_rows=".$offset_rows.",@page_size=".$page_size;
            } else {
                $exec = "EXEC [dbo].[sp_proc_get_items] @user_id='".$user_id."', @category='".$category."', @sub_category='".$sub_category."',@price_from=". $price_from .",@price_to=". $price_to .",@vendor='". $vendor ."',@brand='". $brand ."',@exclude_accessory=".$exclude_accessory.",@only_discounted=".$only_discounted.",@available_only=".$available_only.",@search_text='".$search_text."',@order_by='".$order_by."',@offset_rows=".$offset_rows.",@page_size=".$page_size;
            }
        //    print_r($exec );
        //     return response()->json(['data' =>$exec, 'status' => 400, "success" => false]);

            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare($exec,[\PDO::ATTR_CURSOR=>\PDO::CURSOR_SCROLL]);
            // $stmt = $pdo->query($exec);
            $stmt->execute();
            $rowset1 = $stmt->fetchAll();
           
            $stmt->nextRowset();
            $rowset2 = $stmt->fetchAll();

            $rowset3 = 0;
            $rowset4 = 99999;
            $rowset5 = 0;
            if(sizeof($rowset2) > 0) {
                $rowset3 = $rowset2[0]["Selling_Price_Min"];
                $rowset4 = $rowset2[0]["Selling_Price_Max"];
                $rowset5 = $rowset2[0]["Total_Items_Found"];
            }
            
            return response()->json(['data' => $rowset1,"totalCount" => $rowset5, "min_price" => $rowset3, "max_price" => $rowset4, 'status' => 200, "success" => true]);
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

    public function getVendors(Request $request) {
        try {
            $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Vendors]");
            
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            print_r($e->getMessage());
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }

    public function getBrands(Request $request) {
        
        try {
            $arrData = DB::select("EXEC [dbo].[sp_proc_Get_Brands]");
            
            return response()->json(['data' => $arrData, 'status' => 200, "success" => true]);
        } catch(Exception $e) {
            
            return response()->json(['data' => $e->getMessage(), 'status' => 400, "success" => false]);
        }
    }
}
