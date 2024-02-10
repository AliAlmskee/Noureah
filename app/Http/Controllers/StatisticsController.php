<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Branch;

class StatisticsController extends Controller
{


    public function n2(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate);
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $id = $request->input('id');
        $controller = new StudentController();

        $request = Request::create('/students', 'GET', ['id' => $id]);
        $response = $controller->index($request);
        $students = $response->getData();

        foreach ($students as $student) {
            unset($student->photo);
            unset($student->previous_consistency);
            unset($student->max_consistency);
            unset($student->current_consistency);

            $tests = Test::where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->where('student_id', $student->id)
                ->get();

            $total_pages = 0;
            $total_specials = 0;

            $total_min = 0;
            $uniqueDates = [];
            foreach ($tests as $test) {
                $total_pages += $test->no_pages;
                $total_min+= $test->time_in_minutes;
                $date = Carbon::parse($test->date)->toDateString();
                if (!in_array($date, $uniqueDates)) {
                    $uniqueDates[] = $date;
                }

                if ($test->is_special) {
                    $total_specials++;
                }
            }

            $student->total_pages = $total_pages;
            $student->total_specials = $total_specials;

            $student->num_days = count($uniqueDates);
            $student->total_min = $total_min;

            $exams = Exam::where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->where('student_id', $student->id)
                ->get();

            $total_exams = 0;

            foreach ($exams as $exam) {
                if ($exam->status === 'Approved') {
                    $total_exams++;
                }
            }

            $student->total_exams = $total_exams;
        }

        return response()->json($students);
    }








    public function teachers_statistics(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate);
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $id = $request->input('id');

        if($id){
      $teachers = Teacher::where('branch_id',$id)->get();}
      else
      {
        $teachers = Teacher::all();
    }

        foreach ($teachers as $teacher) {
          //  unset($student->previous_consistency);


            $tests = Test::where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->where('teacher_id', $teacher->id)
                ->get();

            $total_pages = 0;
            $total_min = 0;
            $uniqueDates = [];

            foreach ($tests as $test) {
                $total_pages += $test->no_pages;
                $total_min+= $test->time_in_minutes;
                $date = Carbon::parse($test->date)->toDateString();
                if (!in_array($date, $uniqueDates)) {
                    $uniqueDates[] = $date;
                }            }
            $teacher->total_pages = $total_pages;
            $teacher->num_days = count($uniqueDates);
            $teacher->total_min = $total_min;

            $branch = Branch::find($teacher->branch_id);
            $teacher->branch_name = $branch->name;

            unset($teacher->branch_id);





        }



        return response()->json($teachers);









    }
























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


}
