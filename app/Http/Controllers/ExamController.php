<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Book;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\BookStudentController;
class ExamController extends Controller
{


    public function index(Request $request)
    {

        $status = $request->query('status');;
        $exams = Exam::where('status', $status)->get();
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

        return response()->json($exams);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'teacher_id' => 'required|exists:teachers,id',
            'mark' => 'integer',
            'number' => 'required|integer',
            'date' => 'nullable|date_format:Y/m/d',
        ]);
        if($request->number> Book::find($request->book_id )->no_exams ||$request->book_id ==1 )
        {
            return response()->json("invalid number of exam");

        }
        $data['status'] ="Pending" ;
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
            'admin_id' => 'required|exists:admins,id',
        ]);

        $exam = Exam::findOrFail($data['exam_id']);
        $exam->status = 'Approved';
        $exam->admin_id = $data['admin_id'];
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
