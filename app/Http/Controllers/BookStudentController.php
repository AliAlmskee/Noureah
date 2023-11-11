<?php

namespace App\Http\Controllers;

use App\Models\BookStudent;
use App\Models\Book;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookStudentController extends Controller
{
    public function index()
    {
        $bookStudents = BookStudent::all();
        return response()->json($bookStudents);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'version_id' => 'required|exists:books,id',
        ]);
        $version = Version::findOrFail($data['version_id']);
        $book = Book::findOrFail( $version->book_id);
        $data['assigned_finished'] = str_repeat('0', $book->no_exams);

        $bookStudent = BookStudent::create($data);

        return response()->json($bookStudent, 201);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'book_id' => 'required|integer',
            'number' => 'required|integer',
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
        $bookStudent->setAttribute('assigned_finished', $assignedFinished);
        $bookStudent->save();

        return response()->json($bookStudent);
    }
    public function destroy(BookStudent $bookStudent)
    {
        $bookStudent->delete();

        return response()->json(null, 204);
    }
}
