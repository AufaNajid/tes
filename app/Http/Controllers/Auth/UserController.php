<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = User::all();
        return response()->json($user);
    }
    public function register(Request $request)
    {

        //validation
        $request->validate([
            "name" => "required|string",
            "email" => "required|string|email|unique:users",
            "password" => "required|confirmed"
        ]);

        //user
        User::create([
            "name" => $request->name,
            "email"=> $request->email,
            "password"=> bcrypt($request->password)
        ]);

        return response()-> json([
            "status"=> true,
            "massage"=> "User registared succesfully",
            "data"=> []
        ]);

    }
    public function login(Request $request)
    {
        $request->validate([
            "email"=>"required|email|string",
            "password"=>"required"
        ]);

        $user = User::where("email", $request->email)->first();

        if(!empty($user)){
            if(Hash::check($request->password, $user->password)){
                $token = $user->createToken("mytoken")->plainTextToken;

                return response()->json([
                    "status"=> true,
                    "massage"=>"User logged in",
                    "token" => $token,
                    "data"=>[]
                ]);

            }else{
                return response()->json([
                    "status"=> false,
                    "massage"=> "Invalid password",
                    "data"=> []
                ]);
            }

        }else{
            return response()->json([
                "status"=> false,
                "massage"=> "Email doesn't match with records",
                "data"=> []
            ]);
        }
    }

    public function profile(Request $request)
    {
        $userData = auth()->user();
        $isMitra = $userData->status == 'mitra';

        return response()->json([
            "status" => true,
            "message" => "Profile Information",
            "data" => $userData,
            "is_mitra" => $isMitra,
            "id" => auth()->user()->id
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "massage" =>"User logged out",
            "data"=>[]
        ]);
    }

    public function datauser()
    {
        return view('/user', [
            "title" => "user",
        ]);
    }

}
