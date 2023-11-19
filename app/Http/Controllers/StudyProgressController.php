<?php

namespace App\Http\Controllers;

use App\Models\StudyProgress;
use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\BookStudent;
use App\Models\Student;
use App\Models\Version;
class StudyProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studyProgresses = StudyProgress::all();
        return response()->json($studyProgresses);
    }

    /**
     * Store a newly created resource in storage.
     */
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
            $finished[$page - $startPage  ] = 1;
        }

        $studyProgress->finished = $finished;

        $studyProgress->save();

        $newRequest = new Request([
            'student_id' => $studyProgress->student_id,
            'folderid' => $studyProgress->folder_id,
        ]);

       $percentage =  $this->calculatePercentage($newRequest);
        if($percentage!=100)
        {
            autochangethefolder($request->student_id);

        }
        return response()->json($studyProgress);
    }

    public function destroy(StudyProgress $studyProgress)
    {
        $studyProgress->delete();

        return response()->json(null, 204);
    }



    public function autochangethefolder($student_id)
    {
        $student =Student::find($student_id);



        $folder = Folder::find( $student->current_folder_id );
        $nextfolder = Folder::find( $student->current_folder_id + 1  );

        if($folder->version_id ==$nextfolder->version_id )
        {
            $student->current_folder_id =   $student->current_folder_id  + 1 ;

        }
        else
        {

            autochangetheBook($student_id);

        }
    }
    public function    autochangetheBook($student_id){
        $student =Student::find($student_id);
        $folder = Folder::find( $student->current_folder_id );
        $version = Version::find( $folder->version_id );
        $book = Book::find( $version->book_id);

        if($book_id==1)
        {
            $student->current_folder_id = 5;
            $student->save();
            $studyProgressController = new StudyProgressController();
            $studyProgressRequest = new Request([
                'student_id' => $student->id,
                'folder_id' => $student->current_folder_id,
            ]);

            $studyProgressController->store($studyProgressRequest);

            $bookStudentController = new BookStudentController();
            $folder = Folder::find(5);
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

        $totalPages = $version->no_pages;
        $percentage = ($totalPages > 0) ? (100 * $completedPagesCount / $totalPages) : 0;

        $bookStudent = BookStudent::where('student_id', $request->student_id)
            ->where('version_id', $version->id)
            ->first();

        if ($bookStudent) {
            $bookStudent->percentage_finished = $percentage;
            $bookStudent->save();

            return response()->json($percentage);
        } else {
            return response()->json(['error' => 'BookStudent record not found'], 404);
        }
    }










    public function init()
    {


    }
}
