<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{

    public function sortallStudentsByPages(Request $request)
    {
        $startDateTime = $request->input('startDateTime');
        $endDateTime = $request->input('endDateTime');

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime);
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $endDateTime);

        $bookId = $request->input('book_id');
        $branchId = $request->input('branch_id');

        $students = DB::table('tests')
            ->join('students', 'tests.student_id', '=', 'students.id')
            ->select('students.id as student_id', DB::raw('SUM(tests.no_pages) as total_pages'))
            ->whereBetween('tests.created_at', [$startDateTime, $endDateTime]);

        if (!is_null($branchId)) {
            $students->where('students.branch_id', $branchId);
        }
        if (!is_null($bookId)) {
            $students->join('folders', 'tests.folder_id', '=', 'folders.id')
                     ->join('versions', 'folders.version_id', '=', 'versions.id')
                     ->where('versions.book_id', $bookId);
        }
        $students = $students->groupBy('students.id')
            ->orderBy('total_pages', 'desc')
            ->get();

        return response()->json(['students' => $students]);
    }



    public function sortallStudentsBySpecialTests(Request $request)
    {
        $startDateTime = $request->input('startDateTime');
        $endDateTime = $request->input('endDateTime');

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime);
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $endDateTime);
        if (is_null($startDateTime) || is_null($endDateTime)) {
            return response()->json(['error' => 'Start and end date/time inputs are required.']);
        }

        $bookId = $request->input('book_id');
        $branchId = $request->input('branch_id');

        $query = Test::select('student_id', DB::raw('COUNT(*) as special_test_count'))
        ->where('is_special', true)
        ->whereBetween('tests.created_at', [$startDateTime, $endDateTime]);

        if (!is_null($bookId)) {
            $query->where('book_id', $bookId);
        }
        if (!is_null($branchId)) {
            $query->join('students', 'tests.student_id', '=', 'students.id')
                ->where('students.branch_id', $branchId);
        }
        $students = $query->groupBy('student_id')
            ->orderBy('special_test_count', 'desc')
            ->get();

        return response()->json(['students' => $students]);
    }


    public function sortallStudentsByApprovedExams(Request $request)
    {
        $startDateTime = $request->input('startDateTime');
        $endDateTime = $request->input('endDateTime');

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime);
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $endDateTime);

        if (is_null($startDateTime) || is_null($endDateTime)) {
            return response()->json(['error' => 'Start and end date/time inputs are required.']);
        }

        $bookId = $request->input('book_id');
        $branchId = $request->input('branch_id');

        $query = Exam::select('student_id', DB::raw('COUNT(*) as approved_exam_count'))
            ->where('status', 'approved')
            ->whereBetween('exams.created_at', [$startDateTime, $endDateTime]);

        if (!is_null($bookId)) {
            $query->where('book_id', $bookId);
        }
        if (!is_null($branchId)) {
            $query->join('students', 'exams.student_id', '=', 'students.id')
                ->where('students.branch_id', $branchId);
        }

        $students = $query->groupBy('student_id')
            ->orderBy('approved_exam_count', 'desc')
            ->get();

        return response()->json(['students' => $students]);
    }







}
