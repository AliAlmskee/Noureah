<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\StudyProgressController ;
use Illuminate\Support\Facades\Validator;
class TestController extends Controller
{
    public function index()
    {
        $tests = Test::all();
        return response()->json($tests);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'student_id' => 'required|exists:students,id',
            'folder_id' => 'required|exists:folders,id',
            'no_mistakes' => 'integer',
            'time_in_minutes' => 'integer',
            'is_special' => 'boolean',
            'mark' => 'integer',
            'pages' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $data['no_pages'] = count($data['pages']);
    //    $data['date'] = Carbon::now('Asia/Damascus')->format('y/m/d');

        if ($data['no_pages'] < 5) {
            return response()->json(['error' => 'The pages must be at least 5.'], 422);
        }

        $test = Test::create($data);

        $studyProgressController = new StudyProgressController();
        $studyProgressRequest = new Request([
            'student_id' => $data['student_id'],
            'folder_id' => $data['folder_id'],
            'finished' => $data['pages'],
        ]);

        $result = $studyProgressController->update($studyProgressRequest);
        if ($result->getData() === "page is not in this folder ") {
            return "page is not in this folder";
        }
        return response()->json($test, 201);
    }




    public function update(Request $request, Test $test)
    {
        $data = $request->validate([
            'teacher_id' => 'exists:teachers,id',
            'student_id' => 'exists:students,id',
            'folder_id' => 'exists:folders,id',
            'no_mistakes' => 'integer',
            'no_pages' => 'integer',
            'time_in_minutes' => 'integer',
            'is_special' => 'boolean',
            'mark' => 'integer',
            'pages' => 'array',
            'date' => 'date',
        ]);

        $test->update($data);

        return response()->json($test);
    }

    public function destroy(Test $test)
    {
        $test->delete();

        return response()->json(null, 204);
    }
}
