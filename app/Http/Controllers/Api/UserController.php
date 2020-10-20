<?php


namespace App\Http\Controllers\Api;


use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;

class UserController extends \App\Http\Controllers\Controller
{

    public function store(StoreUserRequest $request)
    {


        if ($request->validated()) {
                 User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
              ])->save();

            $cred = $request->only("email","password");
            $token = auth('api')->attempt($cred);
            return response()->json(["user" => ["name"=>$request->name,"email"=>$request->email],
                                                "token"=>  $token ],
                Response::HTTP_CREATED);
        }
            return response("",Response::HTTP_INTERNAL_SERVER_ERROR);

    }


    public function authenticate(Request $request)
    {
            $cred = $request->only("email","password");
            $token = auth('api')->attempt($cred);
            $user = auth('api')->user();
            if($token)
            return response()->json(["user" => ["name"=>$user->name,"email"=>$user->email],
                "token"=>  $token ],
                Response::HTTP_OK);

            return response()->json([ "message" => "login failed","errors"=>["Invalid"=> ["Invalid credentials"]]],Response::HTTP_UNAUTHORIZED);
        }

        public function  read(Request $request)
        {
            $token = $request->bearerToken();
            $user = auth('api')->user();
                return response()->json(["user" => ["name"=>$user->name,"email"=>$user->email],
                    "token"=>  $token ],
                    Response::HTTP_OK);

        }

        public function logout()
        {
            Auth::logout();

            return response("",Response::HTTP_OK);
        }



//        public function validate(Request $request)
//        {
//            $user = auth('api')->user();
//            $token = $request->bearerToken();
//            return response()->json(["user" => ["name"=>$user->name,"email"=>$user->email],
//                "token"=>  $token ],
//                Response::HTTP_OK);
//
//        }


}
