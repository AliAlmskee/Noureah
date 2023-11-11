<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::all();
        return response()->json($teachers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'branch_id' => 'required|exists:branches,id'
        ]);

        $teacher = Teacher::create($request->all());

        return response()->json($teacher, 201);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required',
            'branch_id' => 'required|exists:branches,id'
        ]);

        $teacher->update($request->all());

        return response()->json($teacher, 200);
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return response()->json([
            'message' => 'Teacher deleted successfully.'
        ], 200);
    }
}
