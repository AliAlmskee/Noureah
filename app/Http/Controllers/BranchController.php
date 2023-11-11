<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return response()->json($branches);
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $branch = Branch::create($request->all());

        return response()->json($branch, 201);
    }



    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $branch->update($request->all());

        return response()->json($branch, 200);
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return response()->json([
            'message' => 'Branch deleted successfully.'
        ], 200);
    }
}
