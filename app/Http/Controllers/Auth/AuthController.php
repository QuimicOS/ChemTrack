<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // if (!$user->hasVerifiedEmail()) {
            //     return response()->json(['error' => 'Email not verified'], 403);
            // }

            // Check the user's role and create token accordingly
            if ($user->role === 'Administrator') {
                $token = $user->createToken('AdminToken')->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'message' => 'Login successful, Welcome Administrator!',
                    'role' => 'Administrator',
                ], 200);
            }

            if ($user->role === 'Professor') {
                $token = $user->createToken('ProfessorToken')->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'message' => 'Login successful',
                    'role' => 'Professor',
                ], 200);
            }

            if ($user->role === 'Staff') {
                $token = $user->createToken('StaffToken')->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'message' => 'Login successful',
                    'role' => 'Staff',
                ], 200);
            }

            // If the role does not match any predefined roles
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['error' => 'User not found'], 404);
    }




    public function logout(Request $request)
    {
        // Delete the current token for logout
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json([
    //         'message' => 'Logged out successfully'
    //     ], 200);
    // }




        $request->validate([
            'token' => 'required|string',
        ]);

        // Attempt to find and delete the provided token
        $token = PersonalAccessToken::findToken($request->token);

        if ($token) {
            $token->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        }

        return response()->json(['error' => 'Invalid token'], 400);
        }
}
