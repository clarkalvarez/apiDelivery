<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\User; 
use App\Models\Order; 
use App\Models\CustomerOrder; 
use Illuminate\Support\Facades\Auth; 
use Validator;
class UserController extends Controller 
{
public $successStatus = 200;
/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['access_token' => $success], 201); 
        } 
        else{ 
            return response()->json(['message'=>'“Invalid credentials'], 401); 
        } 
    }
/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|unique:users,email', 
            'password' => 'required', 
           // 'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
                    return response()->json(['message'=>$validator->errors()], 400);            
                }
        $input = $request->all(); 
                $input['password'] = bcrypt($input['password']); 
                $user = User::create($input); 
                $success['token'] =  $user->createToken('MyApp')-> accessToken; 
                $success['name'] =  $user->name;
                return  response()->json(['message'=>'User Successfully Registered'], 401); 
        //return response()->json(['success'=>$success], $this-> successStatus); 
    }
/** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function orders(Request $request) 
    { 
        $header = $request->header('Authorization');
        $user = Auth::user();  
        $order_id= $request->header('order_id');
        $quantity= $request->header('quantity');
        $orders=Order::findOrFail($order_id);
        $stock = $orders->available_stock ;
      
        if($stock== 0)
        {
            $message = "Out of Stock";
            $status=400;
        }
        else if($stock < $quantity)
        {
            $message = "Failed to order this product due to unavailability of the stock";
            $status=400;
        }
        else
        {
            $status=201;
            $stock = (int)$stock - (int)$quantity;
            $message = "You have successfully ordered this product";
             $inputs = [
                'user_id'=>Auth::user()->id,
                'order_id' =>$order_id,
                'quantity'=>$stock,
        
            ];
            Order::where('id', $order_id)->update(['available_stock' =>  $stock]);
            CustomerOrder::insert($inputs);
        }

        return response()->json([

            'message' => $message,
            
        ], $status); 
    } 
 
}
