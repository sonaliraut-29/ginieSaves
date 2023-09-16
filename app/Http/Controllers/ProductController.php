<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Product;
use DB;

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

    public function getProductDetail(Request $request, $id) {
        $params = $request->all();
        
        $objProduct = Product::find($id);

        return response()->json(['data' => $objProduct]);

    }

    public function getFilterLIst(Request $request) {
        $params = $request->all();
        $category1 = '';
        $category2 = '';
        $category3 = '';
        $price_from = '';
        $price_to = '';
        $vendor = '';
        $brand = '';
        $exclude_accessory = '';
        $only_discounted = '';
        $available_only = '';

        $search_text = '';
        $order_by = '';
        $offset_rows = '';
        $page_size = '';

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
                        $offset_rows = $params["offset_rows"];
                    break;

                    case "page_size";
                        $page_size = $params["page_size"];
                    break;
                }
                
            }
        }
        
        // print_r("EXEC [dbo].[sp_proc_get_items] @category1='".$category1."', @category2='".$category2."',@category3='". $category3 ."',@price_from='". $price_from ."',@price_to='". $price_to ."',@vendor='". $vendor ."',@brand='". $brand ."',@exclude_accessory='".$exclude_accessory."',@only_discounted='".$only_discounted."',@available_only='".$available_only."',@search_text='".$search_text."',@order_by='".$order_by."',@offset_rows='".$offset_rows."',@page_size='".$page_size."'");
        $arrData = DB::select("EXEC [dbo].[sp_proc_get_items] @category1='".$category1."', @category2='".$category2."',@category3='". $category3 ."',@price_from='". $price_from ."',@price_to='". $price_to ."',@vendor='". $vendor ."',@brand='". $brand ."',@exclude_accessory='".$exclude_accessory."',@only_discounted='".$only_discounted."',@available_only='".$available_only."',@search_text='".$search_text."',@order_by='".$order_by."',@offset_rows='".$offset_rows."',@page_size='".$page_size."'");
		
        return response()->json(['data' => $arrData]);
        
    }
}
