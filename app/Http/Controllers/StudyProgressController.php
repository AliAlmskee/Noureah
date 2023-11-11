<?php

namespace App\Http\Controllers;

use App\Models\StudyProgress;
use Illuminate\Http\Request;
use App\Models\Folder;
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

    // call this functoin in another function and if the return is ''page is not in this folder '' return "page is not in this folder "
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

        return response()->json($studyProgress);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyProgress $studyProgress)
    {
        $studyProgress->delete();

        return response()->json(null, 204);
    }




    public function init()
    {


    }
}
