<?php

namespace App\Http\Controllers;

use App\Models\StudyProgress;
use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\BookStudent;
use App\Models\Student;
use App\Models\Version;
use App\Models\Book;
use App\Models\Test;
use Carbon\Carbon;

class StudyProgressController extends Controller
{
   
    public function index()
    {
        $studyProgresses = StudyProgress::all();
        return response()->json($studyProgresses);
    }

 
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'folder_id' => 'required|exists:folders,id',
        ]);

        $folder = Folder::findOrFail($data['folder_id']);
        $data['finished'] = str_repeat('0', $folder['end_page'] - $folder['start_page'] + 1);

        $studyProgress = StudyProgress::create($data);



        return response()->json($studyProgress, 201);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'exists:students,id',
            'folder_id' => 'exists:folders,id',
            'finished' => 'array',
        ]);

        $studyProgress = StudyProgress::where('student_id', $data['student_id'])
            ->where('folder_id', $data['folder_id'])
            ->firstOrFail();

        $folder = Folder::findOrFail($data['folder_id']);

        $startPage = $folder->start_page;
        $endPage = $folder->end_page;
        $finished = $studyProgress->finished;
        foreach ($data['finished'] as $page) {
           if( $startPage > $page   ||  $page >   $endPage )
           return response()->json("page is not in this folder ");
        }

        foreach ($data['finished'] as $page) {
            if( $finished[$page - $startPage  ] == 1)
            {
                return response()->json("alrady done ! ");
            }
            $finished[$page - $startPage  ] = 1;
        }

        $studyProgress->finished = $finished;

        $studyProgress->save();

        $newRequest = new Request([
            'student_id' => $studyProgress->student_id,
            'folderid' => $studyProgress->folder_id,
        ]);

       $this->calculatePercentage($newRequest);

       // if($percentage!=100.0)
       // {
        //    autochangethefolder($request->student_id);

       // }
        return response()->json($studyProgress);
    }

    public function destroy(StudyProgress $studyProgress)
    {
        $studyProgress->delete();

        return response()->json(null, 204);
    }

    public function finishedPages(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'start_page' => 'required|integer',
            'end_page' => 'required|integer',
        ]);

        $student = Student::find($request->input('student_id'));
        $studyProgress = StudyProgress::where('student_id', $request->input('student_id'))
            ->where('folder_id', $student->current_folder_id)
            ->firstOrFail();

        $startPage = $request->input('start_page');
        $endPage = $request->input('end_page');

        $folder =$student->currentFolder;
        $firstPage = $folder->start_page;
        $finalPage = $folder->end_page;

        if ($startPage < $firstPage || $endPage > $finalPage) {
            return response()->json("Page is not in this folder", 400);
        }

        $finished = $studyProgress->finished;

        $finishedArray = str_split($finished);

        for ($i = $startPage; $i <= $endPage; $i++) {
            $finishedArray[$i - $firstPage] = '1';
        }


        $finished = implode('', $finishedArray);

        $studyProgress->finished = $finished;

        $studyProgress->save();
         $newRequest = new Request([
            'student_id' => $studyProgress->student_id,
            'folderid' => $studyProgress->folder_id,
        ]);
        $percentage =  $this->calculatePercentage($newRequest);
         if($percentage==100)
        {
           $this->autochangethefolder($request->student_id);
        }
        return response()->json(["message" => $percentage], 200);
      }

    public function autochangethefolder($student_id)
    {
        $student =Student::find($student_id);

        $folder =$student->currentFolder;
        $nextfolder = Folder::find( $student->current_folder_id + 1  );

        if($folder->version_id ==$nextfolder->version_id )
        {
            $student->current_folder_id =   $student->current_folder_id  + 1 ;
        }
        else
        {
            $this->autochangetheBook($student_id);
        }
    }
    public function    autochangetheBook($student_id){
        $student =Student::find($student_id);
        $folder =$student->currentFolder;
        $version = $folder->version;
        $book =$version->book;

        if($book->id===1)
        {
            $nextFolder= $book->versions->flatMap(function ($version) {
                return $version->folders;
            })->count() + 1;
            $student->current_folder_id =   $nextFolder;
            $student->save();
            $studyProgressController = new StudyProgressController();
            $studyProgressRequest = new Request([
                'student_id' => $student->id,
                'folder_id' => $student->current_folder_id,
            ]);

            $studyProgressController->store($studyProgressRequest);

            $bookStudentController = new BookStudentController();
            $folder = Folder::find($nextFolder);
            $bookStudentRequest = new Request([
                'student_id' => $student->id,
                'version_id' =>  $folder->version_id ,
            ]);

            $bookStudentResponse = $bookStudentController->store($bookStudentRequest);

        }

    }
    public function changetheBook(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'version_id' => 'required|exists:versions,id',
        ]);

        $student_id = $request->student_id;
        $version_id = $request->version_id;
        if( $version_id  <= 5 ) {
            return response()->json('invalid');
        }
        $student =Student::find($student_id);
        $version = Version::find($version_id);

        // StudyProgress دور لازم ما يكون في

        $q = BookStudent::where('version_id', $version_id)
        ->where('student_id', $student_id)
        ->first();

        if($q) {
        return response()->json("The student has already studied this book.");
        }

        $folders = $version->folders()->pluck('id')->toArray();
       // return response()->json( $folders );
        $min_folder_id = min($folders);
        $student->current_folder_id =   $min_folder_id ;
        $student->save();
        $studyProgressController = new StudyProgressController();
        $studyProgressRequest = new Request([
            'student_id' => $student_id,
            'folder_id' => $min_folder_id,
        ]);

        $studyProgressResponse = $studyProgressController->store($studyProgressRequest);

        $bookStudentController = new BookStudentController();

        $bookStudentRequest = new Request([
            'student_id' => $request->student_id,
            'version_id' => $version_id,
        ]);

        $bookStudentResponse = $bookStudentController->store($bookStudentRequest);


        return response()->json(['message' => 'Book changed successfully']);
    }

// reqierd  "folderid" and  "student_id"
    public function calculatePercentage(Request $request)
    {
        $folder = Folder::find($request->folderid);

        if (!$folder) {
            return response()->json(['error' => 'Folder not found'], 404);
        }

        $version = $folder->version;

        if (!$version) {
            return response()->json(['error' => 'Version not found'], 404);
        }

        $folderIds = $version->folders()->pluck('id')->toArray();

        $completedPagesCount = 0;

        foreach ($folderIds as $folderId) {
            $studyProgress = StudyProgress::where('student_id', $request->student_id)
                ->where('folder_id', $folderId)
                ->first();

            if ($studyProgress) {
                $completedPagesCount += substr_count($studyProgress->finished, '1');
            }
        }

        $totalPages = $version->no_pages  ;
        $percentage = ($totalPages > 0) ? (100 * $completedPagesCount / $totalPages) : 0;

        $bookStudent = BookStudent::where('student_id', $request->student_id)
            ->where('version_id', $version->id)
            ->first();

        if ($bookStudent) {
            $bookStudent->percentage_finished = $percentage;
            $bookStudent->save();

            return $percentage;
        } else {
            return response()->json(['error' => 'BookStudent record not found'], 404);
        }
    }


    public function get_start_end_page($student_id)
    {
        $student = Student::find( $student_id );
        if ($student) {
            $folder =$student->currentFolder;
            return response()->json(['start' => $folder->start_page,'end' => $folder->end_page],200);

        }
        return response()->json(['message' => 'Student not found ']);

    }


        public function pagescolors(Request $request)
        {
            $student_id = $request->query('student_id');
            $folder_id = $request->query('folder_id');

            $student = Student::find($student_id);
            if($student->current_folder_id != $folder_id) {
                return response()->json(['message' => 'Red']);
            }
            if (!$student_id || !$folder_id) {
                return response()->json(['error' => 'Missing student_id or folder_id'], 400);
            }

            $folder = Folder::find($folder_id);
            $studyProgress = StudyProgress::where('student_id', $student_id)
                ->where('folder_id', $folder_id)
                ->first();

            if (!$folder || !$studyProgress) {
                return response()->json(['error' => 'Folder or study progress not found'], 404);
            }

            $tests = Test::where('student_id', $student_id)
                ->where('folder_id', $folder_id)
                //->whereDate('date', Carbon::now()->format('Y-m-d'))
                ->get();

            $finished = $studyProgress->finished;
            $colors = [];

            for ($i = 0; $i < strlen($finished); $i++) {
                $page = $folder->start_page + $i;
                $test = $tests->first(function ($test) use ($page) {
                    return in_array($page, $test->pages);
                });

                if ($test) {
                    $colors[] = $test->color;
                } else {
                    $colors[] = null;
                }
            }

            return response()->json([  'start_page'=>$folder->start_page,'finished' => $finished, 'colors' => $colors]);
        }
}
