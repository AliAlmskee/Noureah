<?php

namespace App\Http\Controllers;

use App\Http\Controllers\StudyProgressController ;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->query('id');
        $perPage = 15;

        if ($id) {
            $tests = Test::whereHas('student', function ($query) use ($id) {
                $query->where('branch_id', $id);
            })->latest()->paginate($perPage);
        } else {
            $tests = Test::latest()->paginate($perPage);
        }

        foreach ($tests as $test) {
            $test->teacher_name = $test->teacher->name ?? null;
            $test->student_name =$test->student->name;
            $test->folder_name = $test->folder->name;
            if ($test->emoji_id) {
                $test->emoji = $test->emoji->emoji;
                unset($test->emoji_id);
            }
            unset($test->teacher_id);
            unset($test->student_id);
            unset($test->folder_id);
            unset($test->created_at);
            unset($test->updated_at);

        }
        $testsData = $tests->items();

        return response()->json($testsData);
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
            'emoji_id' =>'nullable|exists:emoji,id',
            'date' => 'date_format:Y-m-d',
            'message' =>'nullable|string' ,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $student =Student::find( $request->student_id);
        if($student->current_folder_id !=  $request->folder_id)
        {
            return response()->json('folder_id not match the currecnt one ');

        }

        $data['no_pages'] = count($data['pages']);
        if($request->emoji_id){
        $data['emoji_id'] = $request->emoji_id ;}
        $data['date'] = $request->date;
        $data['color'] = $student->color;
        if ($data['no_pages'] < 5) {
            return response()->json(['error' => 'The pages must be at least 5.'], 422);
        }


        $studyProgressController = new StudyProgressController();
        $studyProgressRequest = new Request([
            'student_id' => $data['student_id'],
            'folder_id' => $data['folder_id'],
            'finished' => $data['pages'],
        ]);

        $result = $studyProgressController->update($studyProgressRequest);
        if ($result->getData() === "page is not in this folder ") {
            return response()->json(['message' => 'page is not in this folder']);
        }
        if ($result->getData() == "alrady done ! ") {
            return response()->json(['message' => 'already done!']);
        }
        $test = Test::create($data);
        
        if ($request->message!=null) {
            $massageController = new MessageController();
        
            $messageData = [
                'test_id' => $test->id,
                'thanks_message' => $request->message,
            ];
        
            $messageRequest = new Request($messageData);
        
            $response = $massageController->store($messageRequest);
        }
        return response()->json($test, 201);
    }





 

    public function destroy(Test $test)
    {
        $test->delete();

        return response()->json(null, 204);
    }

    public function testforstudent($id, $folder_id)
    {
        $tests = Test::where('student_id', $id)->where('folder_id', $folder_id)->get();
    
        $student = Student::find($id);
        foreach ($tests as $test) {
            $test->teacher_name = $test->teacher->name;
            $test->folder_name = $test->folder->name;
    
            if ($test->emoji_id) {
                $test->emojiUrl = $test->emoji->emoji;
                unset($test->emoji_id);
            }
            unset($test->emoji_id);
            unset($test->teacher_id);
            unset($test->student_id);
            unset($test->folder_id);
            unset($test->created_at);
            unset($test->updated_at);
        }
    
        $branch = Branch::find($student->branch_id);
        $season_start = $branch->season_start;
        $season_end = $branch->season_end;
    
        $response = [
            'tests' => $tests,
            'season_start' => $season_start,
            'season_end' => $season_end,
        ];
    
        return response()->json($response);
    }





}
