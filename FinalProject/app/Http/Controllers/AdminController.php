<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Datetime;
use App\Models\Token;
use App\Models\CartModel;
use App\Models\SellerModel;
use App\Models\NoticeModel;
use App\Models\ProductModel;
use App\Models\ConfirmOrderModel;
use Illuminate\Support\Facades\DB;
use session;


use App\Models\AdminModel;
use App\Models\AdminTokenModel;


class AdminController extends Controller
{
    //
    function loginadmin(Request $req){
        $validator = Validator::make($req->all(),[
           
            
            "a_email"=>"required|exists:admins,a_email",
            "a_password" => "required|min:8|"
           
        
        ],
    [
        "a_email.required"=>"Please provide your email",
        "a_email.exists"=>"This email is invalid. Please Check!",
        "a_password.required"=>"Please provide your password here!",
        "a_password.min"=>"Password must be 8 characters",

        
    ]
);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $user = AdminModel::where('a_email',$req->a_email)->where('a_password',$req->a_password)->first();
        if($user){
            $key = Str::random(20);
            $token = new AdminTokenModel();
            $token->Tkey = $key;
            $token->A_id = $user->a_id;
            $token->created_at = new Datetime();
            $token->save();
            return response()->json([
                "status"=>200,
                "key"=>$key,
                "U_id"=>$user->a_id
            ]);
        }
        return response()->json(["msg"=>"Username password invalid"],404);

    }



    function sellerinformation(Request $req){
       
  
        $token = AdminTokenModel::where('Tkey',$req->Tkey)->first();
        
        if($token){
    
            $seller = SellerModel::all();
            return response()->json($seller);
        }       
    
    }



    public function removeseller($s_id)
{
    $sellerremove = SellerModel::find($s_id);
    if($sellerremove)
    {
        $sellerremove->delete();
        return response()->json([
            "status" => 200,
            "message" => "Item deleted successfully",
        ]);
    }
    else
    {
        return response()->json([
            "status" => 404,
            "message" => "Seller not found",
        ]);
    }
}





function addnotice(Request $req){
            
    $token = AdminTokenModel::where('Tkey',$req->Tkey)->first();
if($token){
  $notice = new NoticeModel();
  $notice->n_notice =$req->n_notice;
  $notice->a_id = $token->A_id;
  $notice->created_at = new Datetime();
  $notice->save();
  return response()->json($notice);
}
else{
return response()->json(["msg"=>"You are logged out"]);
}

}



function noticeinfo(){
    $allnotice = NoticeModel::all();
    return response()->json($allnotice);
}

public function deleteNotice($n_id)
{
    $noticeDelete = NoticeModel::find($n_id);
    if($noticeDelete)
    {
        $noticeDelete->delete();
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



public function editnotice(Request $req)

{
    $editnotice = NoticeModel::find($req->n_id);
    if($editnotice)
        {
            return response()->json([
                "status" => 200,
                "editnotice" => $editnotice
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

public function updatenotice(Request  $req)
{
    
       $updatenotice = NoticeModel::find($req->n_id);


           $updatenotice->n_notice= $req->n_notice;
           $updatenotice->save();

            return response()->json([
                "status" => 200,
                "message" => "Course update successfully",
                "data" =>$updatenotice,
                
            ]);
        
}


function logoutadmin(Request $req){
    $tk = $req->token;
    $tk1 = $req->header("Authorization");
    if($tk){
    $token = AdminTokenModel::where('Tkey',$tk)->first();
    $token->expired_at = new Datetime();
    $token->save();
    return response()->json([ "status"=>200,"msg"=>"Logged out","tk1"=>$tk1]);
}
 else{
    return response()->json(["msg"=>"Logout invalid"],404);
 }
}

}
