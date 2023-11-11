<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\BookStudentController;
class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Exam::all();
        return response()->json($exams);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'teacher_id' => 'required|exists:teachers,id',
            'mark' => 'integer',
            'number' => 'integer',
        ]);
        $data['date'] = Carbon::now('Asia/Damascus')->format('y/m/d');
        $data['status'] ="pending" ;
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
            'status' => 'nullable|string',
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
    public function approveExam(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'admin_id' => 'required|exists:admins,id',
        ]);

        $exam->status = 'approved';
        $exam->admin_id = $data['admin_id'];
        $exam->save();

        $bookStudentRequest = new Request([
            'student_id' => $exam->student_id,
            'book_id' => $exam->book_id,
            'number' => $exam->number,
        ]);

        $bookStudentController = new BookStudentController();
        $bookStudentController->update($bookStudentRequest);

        return response()->json($exam);
    }

    public function getAllPendingExams(Request $request)
    {
        $exams = Exam::where('status', 'pending')->get();
        return response()->json($exams, 200);
    }
}
