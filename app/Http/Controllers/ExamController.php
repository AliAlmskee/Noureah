<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Book;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\BookStudentController;
use App\Models\Folder;
use App\Models\Version;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{


    public function index(Request $request)
    {
        $status = $request->query('status');
        $perPage = 15;
        $exams = Exam::where('status', $status)
            ->latest()
            ->paginate($perPage);

        foreach ($exams as $exam) {
            $exam->student_name = Student::find($exam->student_id)->name;
            $exam->book_name = Book::find($exam->book_id)->name;
            $exam->teacher_name = Teacher::find($exam->teacher_id)->name;

            unset($exam->student_id);
            unset($exam->teacher_id);
            unset($exam->book_id);
            unset($exam->created_at);
            unset($exam->updated_at);
        }

        $examsData = $exams->items();

        return response()->json($examsData);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'teacher_id' => 'required|exists:teachers,id',
            'mark' => 'integer',
            'number' => 'required|integer',
            'date' => 'nullable|date_format:Y-m-d',
        ]);
            $student = Student::find($request->student_id);
            $folder = Folder::find($student->current_folder_id);
            $version = Version::find($folder->version_id);
        if($request->number> Book::find( $version->book_id )->no_exams || $version->book_id ==1 )
        {
            return response()->json("invalid number of exam");

        }
        $data['status'] ="Pending" ;
        $data['book_id'] =$version->book_id ;

        $exam = Exam::create($data);

        return response()->json($exam, 201);
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'student_id' => 'exists:students,id',
            'book_id' => 'exists:books,id',
            'teacher_id' => 'exists:teachers,id',
            'mark' => 'integer',
            'date' => 'date',
            'admin_id' => 'nullable|exists:admins,id',
        ]);

        $exam->update($data);

        return response()->json($exam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();

        return response()->json(null, 204);
    }
    public function approveExam(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $exam = Exam::findOrFail($data['exam_id']);
        $exam->status = 'Approved';
        $exam->admin_id = auth()->id();
        $exam->save();

        $bookStudentRequest = new Request([
            'student_id' => $exam->student_id,
            'book_id' => $exam->version_id,
            'number' => $exam->number,
        ]);

        $bookStudentController = new BookStudentController();
        $bookStudentController->update($bookStudentRequest);

        return response()->json($exam);
    }



}
