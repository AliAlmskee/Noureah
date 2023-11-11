<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class AdminController extends Controller
{



    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'password' => 'required',
                'branch_id' => 'required|exists:branches,id',
            ]);

            $data['password'] = Hash::make($data['password']);

            $admin = Admin::create($data);

            return response()->json($admin, 201);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to create admin.'], 500);
        }
    }


}
