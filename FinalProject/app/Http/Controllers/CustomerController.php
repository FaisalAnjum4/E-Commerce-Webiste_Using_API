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
use App\Models\ProductModel;
use App\Models\ConfirmOrderModel;
use Illuminate\Support\Facades\DB;
use session;
use App\Mail\Mail;


class CustomerController extends Controller
{

    function forall(){
        $product = ProductModel::all();
        return response()->json($product);
    }
    //
    function createcustomer(Request $req){
        $validator = Validator::make($req->all(),[
           
                "c_name"=>"required",
                "c_email"=>"required|unique:customers,c_email",
                "c_phone"=>"required",
                "c_address"=>"required",
                'c_password' => 'required|min:8|',
                "c_conf_password"=>"required|same:c_password"
            
        ],
        [
            "c_name.required"=>"Please provide your name",
            "c_email.required"=>"Please provide your email",
            "c_phone.required"=>"Please provide your phone",
            "c_email.unique"=>"All ready have an account!!",
            "c_password.required"=>"Please provide your password here!",
            "c_password.min"=>"Password must be 8 characters",
            "c_conf_password.required"=>"Please provide same password",
            "c_conf_password.same"=>"Password mismatch",
    
            
        ]
    

    );
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $customer = new Customer();
        $customer->c_name = $req->c_name;
        $customer->c_email =$req->c_email;
        $customer->c_phone =$req->c_phone;
        $customer->c_address =$req->c_address;
        $customer->c_password = $req->c_password;
        $customer->save();
        Mail::to("$req->c_email")->send(new Mail("$req->c_name"));
        return response()->json(
            [
                "msg"=>"Added Successfully",
                "data"=>$customer      
            ]
        );
    }

    function logincustomer(Request $req){
        $validator = Validator::make($req->all(),[
           
            
            "c_email"=>"required|exists:customers,c_email",
            "c_password" => "required|min:8|"
           
        
        ],
    [
        "c_email.required"=>"Please provide your email",
        "c_email.exists"=>"This email is invalid. Please Check!",
        "c_password.required"=>"Please provide your password here!",
        "c_password.min"=>"Password must be 8 characters",

        
    ]
);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $user = Customer::where('c_email',$req->c_email)->where('c_password',$req->c_password)->first();
        if($user){
            $key = Str::random(20);
            $token = new Token();
            $token->Tkey = $key;
            $token->U_id = $user->c_id;
            $token->created_at = new Datetime();
            $token->save();
            return response()->json([
                "status"=>200,
                "key"=>$key,
                "U_id"=>$user->c_id
            ]);
        }
        return response()->json(["msg"=>"Username password invalid"],404);

    }

    function logout(Request $req){
        $tk = $req->token;
        $tk1 = $req->header("Authorization");
        if($tk){
        $token = Token::where('Tkey',$tk)->first();
        $token->expired_at = new Datetime();
        $token->save();
        return response()->json([ "status"=>200,"msg"=>"Logged out","tk1"=>$tk1]);
    }
     else{
        return response()->json(["msg"=>"Logout invalid"],404);
     }
}


    function productdetails(){
        $product = ProductModel::all();
        return response()->json($product);


    }
 
        function addtocart(Request $req){
            
            $token = Token::where('Tkey',$req->Tkey)->first();
       if($token){
          $cart_table = new CartModel();
          $cart_table->P_id =$req->P_id;
          $cart_table->c_id = $token->U_id;
          $cart_table->save();
          return response()->json($cart_table);
       }
       else{
        return response()->json(["msg"=>"You are logged out"]);
       }

    }

//     public function destroy($Cart_id) {
//     UMSCart::destroy($Cart_id);
//      return redirect()->back();
//  }

    function cartitem(Request $req){
       
  
        $token = Token::where('Tkey',$req->Tkey)->first();
        
        if($token){
 
    $carts=DB::table('carts')
  ->join('products','carts.P_id','=','products.P_id')
  //->join('token','carts.c_id','=','token.U_id')
  ->where('carts.c_id',$token->U_id)
  ->select('products.*','carts.*')
  ->get();
    return response()->json($carts);
        }       

}


 public function deleteCartItem($cart_id)
    {
        $cartItemDelete = CartModel::find($cart_id);
        if($cartItemDelete)
        {
            $cartItemDelete->delete();
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
   


    function confirmorder(Request $req){
   
       

         $token = Token::where('Tkey',$req->Tkey)->first();
         $carts = CartModel::where('c_id',$token->U_id)->get();
        
       foreach($carts as $cartsorder){
            $confirmorders = new ConfirmOrderModel;
            $confirmorders ->Shipping_place = $req->Shipping_place;
            $confirmorders->Payment_type = $req->Payment_type;
            $confirmorders->P_id = $cartsorder['P_id'];
            $confirmorders->C_id = $cartsorder['c_id'];
            $confirmorders->save();
           CartModel::where('c_id',$token->U_id)->delete();
          
      }
     
      return response()->json( $confirmorders);
    }


    function myorder(Request $req){
       
  
        $token = Token::where('Tkey',$req->Tkey)->first();
        
        if($token){
 
    $confirmorder=DB::table('confirm_order')
  ->join('products','confirm_order.P_id','=','products.P_id')
  ->join('customers','confirm_order.C_id','=','customers.c_id')
  //->join('token','carts.c_id','=','token.U_id')
  ->where('confirm_order.C_id',$token->U_id)
  ->select('products.*','confirm_order.*','customers.*')
  ->get();
    return response()->json($confirmorder);
        }       

}

function search( $key){
    $productsearch=ProductModel::where('P_name','like','%'.$key.'%')->get();
    //return view('Product.search',['product'=>$data]);
    return response()->json($productsearch);
}


function profileview(Request $req){
       
  
    $token = Token::where('Tkey',$req->Tkey)->first();
    
    if($token){

$customers=DB::table('customers')
->where('customers.c_id',$token->U_id)
->select('customers.*')
->get();
return response()->json($customers);
    }       

}

}
