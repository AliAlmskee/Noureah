<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $user->password = Hash::make($validatedData['password']);

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
            'phone' => 'required',
            'password' => 'required',
        ]);

        if (auth()->attempt(['phone' => $validatedData['phone'], 'password' => $validatedData['password']])) {
            $user = auth()->user();
            $token = $user->createToken('AuthToken')->plainTextToken;

            return response()->json(['Role' =>  $user->role, 'branch_id' =>  $user->branch_id, 'token' => $token]);
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


    public function changepassword(Request $request)
    {
        $validatedData = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:6',
        ]);
        $user = Auth::user();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 401);
        }

            User::find( $user->id)->update(['password'=>   Hash::make($request->new_password)]);
             return response()->json(['message'=> ' password changed succsefully'],200);


    }














}
