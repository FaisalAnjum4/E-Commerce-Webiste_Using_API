<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Datetime;
use App\Models\Token;
use App\Models\TokenSellerModel;
use App\Models\CartModel;
use App\Models\SellerModel;
use App\Models\ProductModel;
use App\Models\ConfirmOrderModel;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    function createseller(Request $req){
        $validator = Validator::make($req->all(),[
           
                "s_name"=>"required",
                "s_email"=>"required|unique:sellers,s_email",
                "s_phone"=>"required",
                "s_address"=>"required",
                's_password' => 'required|min:8|',
                "s_conf_password"=>"required|same:s_password"
            
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $seller = new SellerModel();
        $seller->s_name = $req->s_name;
        $seller->s_email =$req->s_email;
        $seller->s_phone =$req->s_phone;
        $seller->s_address =$req->s_address;
        $seller->s_password = $req->s_password;
        $seller->save();
        return response()->json(
            [
                "msg"=>"Added Successfully",
                "data"=>$seller      
            ]
        );
    }


    function loginseller(Request $req){

        $validator = Validator::make($req->all(),[
           
            
            "s_email"=>"required|exists:sellers,s_email",
            "s_password" => "required|min:8|"
           
        
        ],
    [
        "s_email.required"=>" Provide your email",
        "s_email.exists"=>"Give your own email",
        "s_password.required"=>"Please provide your password",
        "s_password.min"=>"Password must be 8 characters",

        
    ]
);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        } 
        $user = SellerModel::where('s_email',$req->s_email)->where('s_password',$req->s_password)->first();
        if($user){
            $key = Str::random(20);
            $token = new TokenSellerModel();
            $token->Tkey = $key;
            $token->S_id = $user->s_id;
            $token->created_at = new Datetime();
            $token->save();
            return response()->json([
                "status"=>200,
                "key"=>$key,
                "U_id"=>$user->s_id
            ]);
        }
        return response()->json(["msg"=>"Username password invalid"],404);

    }



    function createproduct(Request $req){


        $validator = Validator::make($req->all(),[
           
           
            "P_name"=>"required",

            "P_description"=>"required",

            "P_price"=>"required",

            "P_photo"=>"required|mimes:jpg,png,jpeg"
        
    ]);
    if($validator->fails()){
        return response()->json($validator->errors());
    }
    $token = TokenSellerModel::where('Tkey',$req->header("Authorization"))->first();
    if($token){
        $product_table = new ProductModel();
        $product_table->P_name = $req->P_name;
        $product_table->P_description =$req->P_description;
        $product_table->P_price = $req->P_price;
       
              $file =$req->file('P_photo');
              $extension = $file->getClientOriginalExtension();
              $filename = time().'.'.$extension;
              $file ->move('storage/profile_images/',$filename);
              $product_table->P_image = $filename;

              $product_table->s_id = $token->S_id;
              
        $product_table->save();
        return response()->json($product_table);
    }
    else{
     return response()->json(["msg"=>"You are logged out"]);
    }


}


function sellerdetails(){
    $seller = SellerModel::all();
    return response()->json($seller);
 }



 function productitem(Request $req){
       
  
    $token = TokenSellerModel::where('Tkey',$req->Tkey)->first();
    
    if($token){

$productsSeller=DB::table('products')
->join('sellers','products.s_id','=','sellers.s_id')
//->join('token','carts.c_id','=','token.U_id')
->where('products.s_id',$token->S_id)
->select('products.*','sellers.*')
->get();
return response()->json($productsSeller);
    }       

}

public function deleteProductItem($P_id)
{
    $productDelete = ProductModel::find($P_id);
    if($productDelete)
    {
        $productDelete->delete();
        return response()->json([
            "status" => 200,
            "message" => "Item deleted successfully",
        ]);
    }
    else
    {
        return response()->json([
            "status" => 404,
            "message" => "Student ID not found",
        ]);
    }
}


function orderseller(Request $req){
       
  
    $token = TokenSellerModel::where('Tkey',$req->header("Authorization"))->first();
    
    if($token){

$confirmorderseller=DB::table('confirm_order')
->join('products','confirm_order.P_id','=','products.P_id')
->join('customers','confirm_order.C_id','=','customers.c_id')
//->join('token','carts.c_id','=','token.U_id')
->where('products.s_id',$token->S_id)
->select('products.*','confirm_order.*','customers.*')
->get();
return response()->json($confirmorderseller);
    }       

}


function logout(Request $req){
    $tk = $req->token;
    $tk1 = $req->header("Authorization");
    if($tk){
    $token = TokenSellerModel::where('Tkey',$tk)->first();
    $token->expired_at = new Datetime();
    $token->save();
    return response()->json([ "status"=>200,"msg"=>"Logged out","tk1"=>$tk1]);
}
 else{
    return response()->json(["msg"=>"Logout invalid"],404);
 }
}






public function editproduct(Request $req)

{
    $product = ProductModel::find($req->P_id);
    if($product)
        {
            return response()->json([
                "status" => 200,
                "product" => $product
            ]);
        }
        else
        {
            return response()->json([
                "status" => 404,
                "message" => "No Product Id Found"
            ]);
        }

}

public function updateProduct(Request  $req)
{
    
       $updateproduct = ProductModel::find($req->P_id);


          
            //$updateproduct = new course();

           $updateproduct->P_name= $req->P_name;
           $updateproduct->P_description = $req->P_description;
           $updateproduct->P_price = $req->P_price;
      
           if($req->hasfile('P_Photo'))
           {
               $destination = 'storage/profile_images/'.$updateproduct->P_photo;
                if(File::exists($destination))
               {
                   File::delete($destination);
   
               }
               $file =$req->file('P_photo');
               $extension = $file->getClientOriginalExtension();
               $filename = time().'.'.$extension;
               $file ->move('storage/profile_images/',$filename);
               $updateproduct->P_image = $filename;
           }

           $updateproduct->save();

            return response()->json([
                "status" => 200,
                "message" => "Course update successfully",
                "data" =>$updateproduct,
                
            ]);
        
}


}