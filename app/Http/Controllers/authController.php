<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\UserResource;
use App\Models\User;

class authController extends Controller
{
    public function login(Request $request) 
    {
        $credentials = $request->validate([
            'perpustakaan' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            return (new UserResource($user))->additional([
                'token' => $user->createToken('myAppToken')->plainTextToken,
            ]);
        }

        return response()->json([
            'message' => 'Your credential does not match.',
        ], Response::HTTP_OK);
    }

    public function logout(Request $request) 
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'You have successfully logged out'
        ], Response::HTTP_OK);
    }
}
