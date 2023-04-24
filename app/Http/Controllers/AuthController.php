<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //Registrations
    public function register(Request $request){
        // register validation rules
        $rules = [
        'name' => 'required|string',
        'email' => 'required|string|unique:users',
        'password' => 'required|string|min:6',
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()){
        return response()->json($validator->errors(),400);
    }
    //create a new user in the database table(in users table)
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make ($request->password)
    ]);
    $token = $user->createToken('Personal Access Token')->plainTextToken;
    $response =['user' => $user, 'token' => $token];
    return response()->json($response,200);
    }
    //login
    public function login(Request $request){
        //find user email in user table or database table
        $user = User::where('email',$request->email)->first();
        //if user email & password is correct
        if($user && Hash::check($request->password, $user->password)){
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $response = ['user' => $user, 'token' => $token];
            return response()->json($response,200);
        }
        //if user email & password is incorrect
        $response = ['message' => 'Invalid email or password'];
        return response()->json($response,400);
    }
}
