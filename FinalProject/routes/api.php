<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});

Route::post('/loginadmin',[AdminController::class,'loginadmin']);
Route::post('/sellerinformation',[AdminController::class,'sellerinformation'])->middleware('logged2');
Route::delete("/removeseller/{s_id}", [AdminController::class, 'removeseller']);
Route::post('/addnotice',[AdminController::class,'addnotice']);
Route::get('/allnotice',[AdminController::class,'noticeinfo']);
Route::delete('/deletenotice/{n_id}',[AdminController::class,'deleteNotice']);
Route::post("/editnotice", [AdminController::class, "editnotice"]);
Route::post("/updatenotice/name", [AdminController::class, "updatenotice"])->middleware('logged2');
Route::post('/logoutadmin',[AdminController::class,'logoutadmin']);




Route::post('/registration',[CustomerController::class,'createcustomer']);
Route::post('/login',[CustomerController::class,'logincustomer']);
Route::post('/logoutcustomer',[CustomerController::class,'logout']);
Route::get('/all',[CustomerController::class,'forall']);
Route::get('/productdetails',[CustomerController::class,'productdetails'])->middleware('logged');
Route::post('/addtocart',[CustomerController::class,'addtocart'])->middleware('logged');
Route::post('/cartitem',[CustomerController::class,'cartitem'])->middleware('logged');
Route::delete("/deletecartitem/{cart_id}", [CustomerController::class, 'deleteCartItem'])->middleware('logged');
Route::get('/delete/{cart_id}',[CustomerController::class,'destroy']);
Route::post('/confirmorder',[CustomerController::class,'confirmorder']);
Route::post('/myorder',[CustomerController::class,'myorder'])->middleware('logged');


Route::get('/search/{key}',[CustomerController::class,'search']);


Route::post('/profileview',[CustomerController::class,'profileview']);




Route::post('/sellerregistration',[SellerController::class,'createseller']);
Route::post('/loginseller',[SellerController::class,'loginseller']);
Route::post('/createproduct',[SellerController::class,'createproduct'])->middleware('logged1');
Route::get('/sellerdetails',[SellerController::class,'sellerdetails'])->middleware('logged1');
Route::post('/productitem',[SellerController::class,'productitem'])->middleware('logged1');
Route::delete("/deleteproductitem/{P_id}", [SellerController::class, 'deleteProductItem']);
Route::post('/orderseller',[SellerController::class,'orderseller'])->middleware('logged1');
Route::post("/editproduct", [SellerController::class, "editproduct"]);
Route::post("/updateproduct/name", [SellerController::class, "updateproduct"])->middleware('logged1');
Route::post('/logoutseller',[SellerController::class,'logout']);