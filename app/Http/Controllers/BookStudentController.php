<?php

namespace App\Http\Controllers;

use App\Models\BookStudent;
use App\Models\Book;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Folder;
use App\Models\Student;
use App\Models\Exam;

class BookStudentController extends Controller
{
    public function index(Request $request)
    {
        $student_id = $request->query('student_id');

        $student = Student::find($student_id);
        if(!$student)
        {        return response()->json('error');
        }
        $folder = $student->currentFolder;


        $bookStudents = BookStudent::where('student_id',$student_id)->where('version_id',$folder->version_id)->first();

        return response()->json(['finished' => $bookStudents->assigned_finished]);

    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'version_id' => 'required|exists:versions,id',
        ]);
        $version = Version::findOrFail($data['version_id']);
        $book =  $version->book;
        $data['assigned_finished'] = str_repeat('0', $book->no_exams);

        $bookStudent = BookStudent::create($data);

        return response()->json($bookStudent, 201);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'integer',
            'book_id' => 'integer',
            'number' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $bookStudent = BookStudent::where('student_id', $request->student_id)
            ->where('book_id', $request->book_id)
            ->first();

        if (!$bookStudent) {
            return response()->json(['error' => 'BookStudent not found'], 404);
        }

        $assignedFinished = $bookStudent->assigned_finished;
        $assignedFinished[$request->number - 1] = 1;
        $bookStudent->assigned_finished=$assignedFinished;
        $bookStudent->save();

        return response()->json($bookStudent);
    }
    public function destroy(BookStudent $bookStudent)
    {
        $bookStudent->delete();

        return response()->json(null, 204);
    }


    public function bookStatus($student_id)
    {
        $booksStudent = BookStudent::where('student_id', $student_id)->get();
        $finished = [];
        $curr = null;
        $unfinished = range(1, 8);

        foreach ($booksStudent as $bookStudent) {
            if ($bookStudent->percentage_finished == 100) {
                $book_id = Version::find($bookStudent->version_id)->book_id;
                $finished[] = ['book_id' => $book_id, 'version_id' => $bookStudent->version_id];
                $key = array_search($book_id, $unfinished);
                if ($key !== false) {
                    unset($unfinished[$key]);
                }
            } else {
                $curr = ['book_id' => Version::find($bookStudent->version_id)->book_id,
                'version_id' => $bookStudent->version_id,
                'curr_progress'=>$bookStudent->percentage_finished];
            }
        }

        return ['finished' => $finished, 'curr' => $curr, 'unfinished' => array_values($unfinished)];
    }



    public function versionStatus($student_id)
    {
        $booksStudent = BookStudent::where('student_id', $student_id)->get();
        $finished = [];
        $curr = null;
        $unfinished = range(1, 8);

        foreach ($booksStudent as $bookStudent) {
            if ($bookStudent->percentage_finished == 100) {
                $finished[] =  Version::find($bookStudent->version_id)->id;
                $key = array_search( Version::find($bookStudent->version_id)->id, $unfinished);
                if ($key !== false) {
                    unset($unfinished[$key]);
                }
            } else {

                $curr = Version::find($bookStudent->version_id)->id;
            }
        }

        return ['finished' => $finished, 'curr' => $curr, 'unfinished' => array_values($unfinished)];
    }


    public function exam_status($student_id,$version_id)
    {
        $bookStudent = BookStudent::where('student_id', $student_id)
        ->where('version_id', $version_id)
        ->first();

        if(!$bookStudent)
        {
            return response()->json("wrong input ");


        }
        $version = Version::find($version_id);
        $exams = Exam::where('student_id', $student_id)
        ->where('book_id', $version->book_id)
        ->where('status', "Pending")
        ->get();

        $approved = $bookStudent->assigned_finished;
        $pending = str_repeat('0', strlen($approved));


        foreach($exams as $exam)
        {
            $pending[$exam->number - 1 ]=1;

        }

        $response = [
            "pending" => $pending,
            "approved" => $approved
        ];
        return response()->json($response);

    }

}
