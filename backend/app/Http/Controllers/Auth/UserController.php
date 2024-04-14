<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function register(Request $request){
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create a new user
        $user = new User([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);
        $user->save();

        // Retrieve the "guest" role
        $guestRole = Role::where('name', 'guest')->first();

        // Attach the "guest" role to the user
        if ($guestRole) {
            $user->roles()->attach($guestRole);
        }

        // Return a JSON response indicating success
        return response()->json([
            'message' => 'User created and associated with the guest role.',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request){
        
       // Validate the request data
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }
        
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // Retrieve the authenticated user
        $user = auth()->user();

        // Return the token along with user information
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function refreshToken(){
        return response()->json([
            'token' => JWTAuth::parseToken()->refresh(),
        ]);
    }

    public function logout(){
        return 'logout';
    }

    public function forgotPassword(){
        return 'forgotPassword';
    }
}
