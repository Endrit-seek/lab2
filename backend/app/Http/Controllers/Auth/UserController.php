<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Password;

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
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function forgotPassword(Request $request){
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            // Send reset link
            $status = Password::sendResetLink($request->only('email'));
            
            // Check the status of the reset link sending
            if ($status == Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'Password reset link sent to your email.',
                ]);
            } else {
                // Handle the case where the reset link could not be sent
                return response()->json([
                    'error' => 'Failed to send password reset link.',
                    'status' => $status, // Provide the status code for further context
                ], 400);
            }
        } catch (\Exception $e) {
            // Catch any exceptions and return a detailed error message
            return response()->json([
                'error' => 'An unexpected error occurred while sending the password reset link.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function resetPassword(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Reset the user's password
        $status = Password::reset([
            'token' => $request->token,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ], function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();

            // Fire a password reset event
            event(new \Illuminate\Auth\Events\PasswordReset($user));
        });

        // Check the status of the password reset
        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password has been reset successfully.',
            ]);
        } else {
            return response()->json([
                'error' => 'Failed to reset password.',
                'status' => $status, // Provide the status code for further context
            ], 400);
        }
    }
}
