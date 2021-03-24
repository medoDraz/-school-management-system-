<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

//use App\Data_usage;
//use App\Fathers;
//use App\Mothers;
//use App\Teacher;
//use App\Bus_supervisor;
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

    public function nologin()
    {
        return response()->json(['error' => true, 'message' => 'Unauthorised GET method'], 401);
    }

    public function login()
    {


        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'status' => 0])) {
            return response()->json(['error' => true, 'message' => 'Sorry not active account ', 'data' => []], 401);
        } elseif (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'status' => 1])) {

            $user = Auth::user();
            if ($user->roles[0]->status == 0) {
                return response()->json(['error' => true, 'message' => 'Sorry not active role of account ', 'data' => []], 401);
            }

            $success['token'] = $user->createToken('Alsson Admins')->accessToken;
            $user->api_token = $success['token'];
            $user->save();

            //dd($user->roles[0]->status);
            return response()->json(['error' => false, 'message' => 'success', 'token' => $user->api_token], $this->successStatus);
            //return response()->json(['error'=>false , 'message'=>'Login success','data' => $success], $this-> successStatus);
        } else {

            return response()->json(['error' => true, 'message' => 'Unauthorised', 'data' => []], 401);
        }
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request)
    {
        //$user = auth()->user();
        //dd($request);
        $user = User::where('api_token', $request->token)->with('roles')->first();
        //dd($user);
        if (isset($user->status) && $user->status == 1) {
            $permissionsofRole = $user->roles[0]->getAllPermissions()->pluck('name');
            $permissionsofUser = $user->getAllPermissions()->pluck('name');
            //dd($permissionsofUser);
            return response()->json([
                'id' => $user->id,
                'api_token' => $user->api_token,
//            'ask_change_pass'=>$user->ask_change_pass,
                'roles' => $user->roles->toArray(),
//            'school'=>explode(',', $user->school),
                'permissions' => $permissionsofUser,
                'name' => $user->name,
                'avatar' => $user->img ?? 'uploads/0090dc92b48caf0e59c9797a168e0478.png',
//            'introduction'=>$user->title,
            ]);
        }    //return response()->json(['user' => $user], 200);
        else
            return response()->json(['error' => true, 'message' => 'Unauthorised541541', 'data' => []], 401);
    }

    public function changepassword(Request $request)
    {
        $user = User::where('api_token', $request->token)->with('roles')->first();
        //dd($user);
        if (isset($user) && !empty($user)) {
            $validator = Validator::make($request->all(), [
                'password' => 'required|confirmed',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => true, 'message' => "Please fill all fields", 'data' => []], 200);
            }
            $user->password = bcrypt($request->password);
            $user->ask_change_pass = 0;
            $user->save();
            return response()->json(['error' => false, 'message' => 'chang password success', 'data' => $user], $this->successStatus);
        } else
            return response()->json(['error' => true, 'message' => 'Unauthorised', 'data' => []], 401);
    }

    public function datausage(Request $request)
    {


        $user = $request->get('user');
        //dd($user);
        if (isset($user) && !empty($user)) {
            $validator = Validator::make($request->all(), [

                'mega_bytes' => 'required',
                'month' => 'required',
                'type' => 'required',


            ]);
            if ($validator->fails()) {
                return response()->json(['error' => true, 'message' => "Please fill all fields", 'data' => []], 200);
            }
            $usage = Data_usage::firstOrNew(['month' => $request->month]);
            $usage->mega_byte = $request->mega_bytes;
            $usage->month = $request->month;
            $usage->type = $request->type;
            $usage->user_id = $user->id;
            $usage->save();
            return response()->json(['error' => false, 'message' => 'success', 'data' => $usage], $this->successStatus);
        } else
            return response()->json(['error' => true, 'message' => 'Unauthorised', 'data' => []], 401);
    }
}
