<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    //return $router->app->version();
    return phpinfo();
});

$router->get("api/leaflets", "LeafletController@getLeaflets");
$router->get("api/banners", "BannerController@getBanners");
$router->get("api/product/{Vendor}/{ItemKey}", "ProductController@getProductDetail");
$router->get("api/productList", "ProductController@getFilterLIst");
$router->get("api/popularItems", "ProductController@getPopularItems");
$router->get("api/vendors", "ProductController@getVendors");

$router->post("api/create","UserController@create");
$router->post("api/login","UserController@login");
$router->post("api/changePassword", "UserController@changePassword");
$router->put("api/updateProfile", "UserController@updateProfile");
$router->post("api/deleteAccount", "UserController@deleteAccount");

$router->get("api/categories", "ProductController@getCategories");
$router->get("api/subCategories", "ProductController@getSubcategories");
$router->get("api/brands", "ProductController@getBrands");

$router->get("api/leafletsPaginate","LeafletController@getAllLeaflets");
$router->get("api/BannersPaginate","BannerController@getAllBanners");

$router->post("api/addToFavourites", "UserController@addToFavourites");
$router->get("api/getFavourites/{Country_ID}/{User_ID}", "UserController@getFavourites");
$router->post("api/removeFavourites","UserController@removeFavourites");
$router->post("api/userDetails", "UserController@userDetails");

$router->get("api/cities/{gov}", "UserController@getCities");
$router->get("api/governate", "UserController@getGovernate");
$router->get("api/logout", "UserController@logout");
$router->get("api/counrties","UserController@getCountries");
$router->post("api/forgotPassword", "UserController@forgotPassword");