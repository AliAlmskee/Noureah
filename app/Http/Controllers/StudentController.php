<?php

namespace App\Http\Controllers;

use App\Models\BookStudent;
use App\Models\Student;
use App\Models\StudyProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;


class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'version_id' => 'required|between:1,4',
        ]);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $studentData = $request->all();

        if(  $studentData['version_id'] > 4 || $studentData['version_id'] < 1  )
        { return response()->json('error version_id  must be between 1 and 4 ', 400);  }


        $studentData['key'] = mt_rand(1000, 9999);

        while (Student::where('key', $studentData['key'])->exists()) {
            $studentData['key'] = mt_rand(1000, 9999);
        }

        $studentData['current_folder_id'] = $studentData['version_id'];
        $student = Student::create($studentData);
   //     make this another function and call it here
        $studyProgressController = new StudyProgressController();
        $studyProgressRequest = new Request([
            'student_id' => $student->id,
            'folder_id' => $student->current_folder_id,
        ]);

        $studyProgressController->store($studyProgressRequest);
    //   .          make this another function and call it here

        $bookStudentController = new BookStudentController();
        $bookStudentRequest = new Request([
            'student_id' => $student->id,
            'version_id' => $studentData['version_id'],
        ]);

        $bookStudentResponse = $bookStudentController->store($bookStudentRequest);
      //  .
        return response()->json([$student, $bookStudentResponse], 201);
    }


    public function update(Request $request, $id)
    {

    }
    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.'
        ], 200);
    }

    public function changephoto(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        // Delete old profile image
        if ($student->photo != null && Storage::exists($student->photo)) {
            Storage::delete($student->photo);
        }

        // Validate and upload new photo
        $this->validate($request, [
            'photo' => 'required|image',
        ]);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $newPhotoPath = 'profile_images/' . time() . '_' . Str::slug($photo->getClientOriginalName());
            Storage::putFileAs('public', $photo, $newPhotoPath);
        } else {
            $newPhotoPath = null;
        }

        // Update student record with new photo path
        $student->photo = $newPhotoPath;
        $student->save();

        return response()->json(['message' => 'Photo changed successfully', 'data' => $student], 200);
    }

    public function changeFolder(Request $request, $id)
    {
        $student = Student::findOrFail($id);


    }



        public function addFormerStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'branch_id' => 'required|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $student['key'] = mt_rand(1000, 9999);
        while (Student::where('key', $studentData['key'])->exists()) {
            $studentData['key'] = mt_rand(1000, 9999);
        }
        $student = Student::create([
            'name' => $request->input('name'),
            'branch_id' => $request->input('branch_id'),
        ]);

        return response()->json($student, 201);
    }

    public function addFinishedBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'version_id' => 'required',
            'student_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $bookStudent = BookStudent::where('student_id', $request->student_id)
            ->where('version_id', $request->version_id)
            ->first();
        if (!$bookStudent) {
            $bookStudentController = new BookStudentController();
            $bookStudentRequest = new Request([
                'student_id' =>$request->student_id,
                'version_id' => $request->version_id,
            ]);

            $bookStudentResponse = $bookStudentController->store($bookStudentRequest);

            $bookStudent = BookStudent::where('student_id', $request->student_id)
                ->where('version_id', $request->version_id)
                ->first();
        }

        $bookStudent->percentage_finished = 1;


        $bookStudent['assigned_finished'] = str_repeat('1', strlen($bookStudent['assigned_finished']));
        $bookStudent->save();

        return response()->json([
            'message' => 'Finished book has been added successfully',
            'data' => $bookStudent,
        ]);
    }



    public function addFinishedFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folder_id' => 'required',
            'student_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $studyProgress = StudyProgress::where('student_id', $request->student_id)
            ->where('folder_id', $request->folder_id)
            ->first();

        if (!$studyProgress) {
            $studyProgressController = new StudyProgressController();
            $studyProgressRequest = new Request([
                'student_id' => $request->student_id,
                'folder_id' => $request->folder_id,
            ]);
            $studyProgressController->store($studyProgressRequest);
            $studyProgress = StudyProgress::where('student_id', $request->student_id)
                ->where('folder_id', $request->folder_id)
                ->first();
        }

        $studyProgress['finished'] = str_repeat('1', strlen($studyProgress['finished']));

        $studyProgress->save();

        return response()->json([
            'message' => 'Finished folder has been added successfully',
            'data' => $studyProgress,
        ]);
    }


}
