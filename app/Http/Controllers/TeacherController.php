<?php

namespace App\Http\Controllers;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->query('id');

        if ($id) {
            $teachers = Teacher::where('branch_id', $id)->get();
        } else {
            $teachers = Teacher::all();
        }

        foreach ($teachers as $teacher) {
            $teacher->branch_name = $teacher->branch->name;
            unset($teacher->branch_id);
            unset($teacher->created_at);
            unset($teacher->updated_at);

        }

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
