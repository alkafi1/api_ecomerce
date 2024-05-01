<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {


        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'min:6'],
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $user = User::create($validator->validated());
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => true,
                'message' => 'User register succesfull.',
                'user' => $user,
                'token' => $token,
            ];
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function login(Request $request)
    {

        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            // Attempt to authenticate the user
            if (!Auth::attempt($request->only('email', 'password'))) {
                throw ValidationException::withMessages(['email' => ['Email Or Password dont match.']]);
            }

            // Retrieve the authenticated user
            $user = Auth::user();

            // Create a token for the authenticated user
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => true,
                'message' => 'User login succesfull.',
                'user' => $user,
                'token' => $token,
            ];
        } catch (ValidationException $e) {
            // Return validation errors if authentication fails
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Return a generic error message for other exceptions
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Return user profile data
        return response()->json(['user' => $user], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
            'data' => [],
        ], 200);
    }
}
