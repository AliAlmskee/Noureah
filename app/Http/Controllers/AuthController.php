<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'password' => 'required|string|min:6',
            'name' => 'required|string',
            'branch_id' => 'nullable',
            'role' => 'required|in:SuperAdmin,Admin',
        ]);

        $user = new User();
        $user->password = bcrypt($validatedData['password']);
        $user->name = $validatedData['name'];
        $user->role = $validatedData['role'];

     if ($user->role === 'Admin') {
        $user->branch_id = $validatedData['branch_id'];
        }

        $user->save();
        return response()->json(['message' => 'Registration successful']);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        if (auth()->attempt(['name' => $validatedData['name'], 'password' => $validatedData['password']])) {
            $user = auth()->user();
            $token = $user->createToken('AuthToken')->plainTextToken;

            return response()->json(['Role' =>  $user->role,'branch_id'  =>  $user->branch_id , 'token' => $token]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successful']);
    }

    public function getAuthenticatedUserId()
    {
        return Auth::id();
    }


    public function test()
    {
        return 352;
    }

    public function test2()
    {
        return 3252;
    }
}
