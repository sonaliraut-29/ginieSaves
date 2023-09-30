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
    return $router->app->version();
});

$router->get("api/leaflets", "LeafletController@getLeaflets");
$router->get("api/banners", "BannerController@getBanners");
$router->get("api/product/{id}", "ProductController@getProductDetail");
$router->get("api/productList", "ProductController@getFilterLIst");
$router->get("api/popularItems", "ProductController@getPopularItems");