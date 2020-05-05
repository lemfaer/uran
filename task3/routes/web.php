<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    "reset" => false,
    "confirm" => false,
    "verify" => false,
]);

Route::get("/logout", "Auth\LoginController@logout")->name("logout");

Route::get("/", "MainController@list");

Route::get("/category/{name}", "MainController@category");

Route::get("/page-{n}", "MainController@list")
    ->where(["n" => "^[1-9][0-9]*$"])
    ->name("list_pagination");

Route::get("/category/{name}/page-{n}", "MainController@category")
    ->where(["n" => "^[1-9][0-9]*$"])
    ->name("list_pagination_2");

Route::get("/product/{id}", "ProductController@view")
    ->where(["id" => "\d+"])
    ->name("product_view");
