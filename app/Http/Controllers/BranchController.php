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

        public function show($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        return response()->json($branch);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'season_start' => 'required|date',
            'season_end' => 'required|date|after_or_equal:season_start',
        ]);

        $branch = Branch::create($request->all());

        return response()->json($branch, 201);
    }



    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'string',
            'season_start' => 'date',
            'season_end' => 'date|after_or_equal:season_start',
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
