<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Product;

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
}
