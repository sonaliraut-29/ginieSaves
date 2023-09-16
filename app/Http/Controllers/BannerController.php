<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Banner;

class BannerController extends Controller
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

    public function getBanners(Request $request) {
        $params = $request->all();
        
        $count  = Banner::count();
        $itemPerPage = 5;
        $offSet = 0;

        if(sizeof($params) > 1 && array_key_exists("page", $params)) {
            $itemPerPage = array_key_exists("limit", $params) ? $params["limit"]:5;
            $offSet = ($params["page"] * $itemPerPage) - $itemPerPage;
        }

        $arrBanners = Banner::get()->take($itemPerPage)->skip($offSet)->toArray();

        return response()->json(['data' => $arrBanners,"totalCount" => $count]);

    }
}
