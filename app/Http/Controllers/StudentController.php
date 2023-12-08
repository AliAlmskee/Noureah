<?php

namespace App\Http\Controllers;

use App\Models\BookStudent;
use App\Models\Student;
use App\Models\Folder;
use App\Models\Book;
use App\Models\Branch;
use App\Models\Version;
use App\Models\StudyProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Test;


class StudentController extends Controller
{

    //admin id
    public function index(Request $request)
    {
        $id = $request->query('id');
        $perPage = 2;
        $currentPage = $request->query('page', 1);
        $book_id = $request->query('book_id');

        if ($id) {
            $students = Student::where('branch_id', $id)->get();
        } else {
            $students = Student::all();
        }

        foreach ($students as $key => $student) {
            $folder = Folder::find($student->current_folder_id);
            $version = Version::find($folder->version_id);
            $book = Book::find($version->book_id);

            if ($book_id && $book_id != $book->id) {
                unset($students[$key]);
                continue;
            }

            $student->branch_name = Branch::find($student->branch_id)->name;
            $foldername = $folder->name;
            $versionname = $version->name;
            $bookname = $book->name;

            unset($student->created_at);
            unset($student->updated_at);
            unset($student->days_inrow);
            unset($student->branch_id);

            $student->folder_name = $foldername;
            $student->version_name = $versionname;
            $student->book_name = $bookname;
        }

        $pagedStudents = new LengthAwarePaginator(
            $students->forPage($currentPage, $perPage),
            $students->count(),
            $perPage,
            $currentPage,
        );

          $studentsData = $pagedStudents->items();
            return response()->json($studentsData);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'version_id' => 'required|between:1,4',
        ]);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $studentData = $request->all();
        $admin = Auth::user();
        if($admin->branch_id){
        $studentData['branch_id'] =$admin->branch_id;}
        else
        {
            if (!$request->has('branch_id') || !in_array($request->branch_id, [1, 2, 3])) {
                return response()->json(['error' => 'required coorect branch_id'], 400);
            }
            $studentData['branch_id']=$request->branch_id ;

        }

        if(  $studentData['version_id'] > 4 || $studentData['version_id'] < 1  )
        { return response()->json('error version_id  must be between 1 and 4 ', 400);  }


        $studentData['key'] = $this->generateRandomKey();

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
        return response()->json($student, 201);
    }
    public function update(Request $request, $id)
    {
        $student = Student::find($id);

        if ($request->has('branch_id')) {
            $branchId = $request->input('branch_id');
            $student->branch_id = $branchId;
        }

        if ($request->has('name')) {
            $name = $request->input('name');
            $student->name = $name;
        }
        if ($request->has('color')) {
            $color = $request->input('color');
            $student->color = $color;
        }
        $student->save();

        return response()->json(['message' => 'Student updated successfully']);
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


        if ($request->hasFile('photo')) {
            if ($student->photo != null && file_exists(public_path('profile_images/' . $student->photo))) {
                unlink(public_path('profile_images/' . $student->photo));
            }}


        $this->validate($request, [
            'photo' => 'required|image',
        ]);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $newImage = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('profile_images'), $newImage);
        } else {
            $newImage = null;
        }
        $student->photo = $newImage;
        $student->save();

        return response()->json(['message' => 'Photo changed successfully', 'data' => $newImage], 200);
    }

    public function addFormerStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'folder_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $studentData['key'] =  $this->generateRandomKey();
        $admin = Auth::user();
        if($admin->branch_id){
        $studentData['branch_id'] =$admin->branch_id;

         }
        else
        {
            if (!$request->has('branch_id') || !in_array($request->branch_id, [1, 2, 3])) {
                return response()->json(['error' => 'required corect branch_id'], 400);
            }
            $studentData['branch_id']=$request->branch_id ;

        }
        $student = Student::create([
            'name' => $request->input('name'),
            'branch_id' =>$studentData['branch_id'] ,
            'current_folder_id' => $request->input('folder_id'),
            'key' => $studentData['key'],
        ]);

        $studyProgressController = new StudyProgressController();
        $studyProgressRequest = new Request([
            'student_id' => $student->id,
            'folder_id' => $student->current_folder_id,
        ]);

        $studyProgressController->store($studyProgressRequest);

        $version_id = Folder::find($request->folder_id)->version_id;
        $bookStudentController = new BookStudentController();
        $bookStudentRequest = new Request([
            'student_id' => $student->id,
            'version_id' =>  $version_id,
        ]);

        $bookStudentResponse = $bookStudentController->store($bookStudentRequest);

        $version = Version::find($version_id);
        $folders = Folder::where('version_id', $version->id)->get();

        foreach ($folders as $folder) {
            if ($folder->id < $request->folder_id) {
                $request2 = new Request([
                    'folder_id' => $folder->id,
                    'student_id' =>  $student->id,
                ]);

                $this->addFinishedFolder($request2);
            }
        }
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

        $bookStudent->percentage_finished = 100;


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

    public function getId($key)
    {
        $student = Student::where('key', $key)->first();
        $folder = Folder::find($student->current_folder_id);

        if ($student) {
            $result = [
                'id' => $student->id,
                'name' => $student->name,
                'photo' => $student->photo,
                'color' => $student->color,
                'folder_id' => $student->current_folder_id,
                'version_id' => $folder->version_id,

            ];
            return response()->json( $result);
        }

        return response()->json(['message' => 'No student match'], 404);
     }




   private function generateRandomKey($length = 4)
   {
       $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
       $randomKey = '';

       do {
           $randomKey = '';

           for ($i = 0; $i < $length; $i++) {
               $randomKey .= $characters[rand(0, strlen($characters) - 1)];
           }
       } while (Student::where('key', $randomKey)->exists());

       return $randomKey;
   }

   public function search(Request $request)
   {
         $id = $request->query('id');
       $subname = $request->query('name');
       $isall = $request->query('isAll');

       if (empty($subname)) {
           return response()->json(['error' => 'Search query is empty']);
       }

       if ($id) {
        $students = Student::where('branch_id', $id)->get();
    } else {
        $students = Student::all();
    }
       foreach ($students as $student) {
           $name = $student->name;
           $relevanceScore = $this->calculateRelevanceScore($name, $subname);
           $student->relevanceScore = $relevanceScore;
       }

       $filteredStudents = $students->filter(function ($student) {
           return $student->relevanceScore >= 40;
       });

       $sortedStudents = $filteredStudents->sortByDesc('relevanceScore');
       if(!$isall){
       $mostRelevantStudents = $sortedStudents->take(4);
       $newstudent = $mostRelevantStudents->map(function ($student) {
        return [
            'id' => $student['id'],
            'name' => $student['name']
        ];
    })->values();}
    else
    {

        $mostRelevantStudents = $sortedStudents;
        $newstudent = $mostRelevantStudents;
        foreach ($newstudent as  $student) {
            $folder = Folder::find($student->current_folder_id);
            $version = Version::find($folder->version_id);
            $book = Book::find($version->book_id);
            $student->branch_name = Branch::find($student->branch_id)->name;
            $foldername = $folder->name;
            $versionname = $version->name;
            $bookname = $book->name;

            unset($student->key);
            unset($student->current_folder_id);
            unset($student->color);
            unset($student->created_at);
            unset($student->updated_at);
            unset($student->days_inrow);
            unset($student->branch_id);
            unset($student->relevanceScore);

            $student->folder_name = $foldername;
            $student->version_name = $versionname;
            $student->book_name = $bookname;
        }

        $newstudent =$newstudent->values();


    }

       return response()->json($newstudent);
   }


        function calculateRelevanceScore($name, $subname)
        {
            $name = mb_strtolower($name);
            $subname = mb_strtolower($subname);

            similar_text($name, $subname, $percentage);

            return $percentage;
        }




        public function profile(Request $request)
        {
            $student_id = $request->query('student_id');
            $student = Student::find($student_id);

            if ($student) {
                $folder = Folder::find($student->current_folder_id);
                $version = Version::find($folder->version_id);
                $book = Book::find($version->book_id);

                $student->branch_name = Branch::find($student->branch_id)->name;
                $foldername = $folder->name;
                $versionname = $version->name;
                $bookname = $book->name;

                unset($student->key);
                unset($student->current_folder_id);
                unset($student->color);
                unset($student->created_at);
                unset($student->updated_at);
                unset($student->branch_id);
                unset($student->created_at);
                unset($student->updated_at);
                unset($student->photo);
                unset($student->previous_consistency);
                $student->folder_name = $foldername;
                $student->version_name = $versionname;
                $student->book_name = $bookname;
            }

            $messageController = app()->make(MessageController::class);
            $messageRequest = Request::create('/messages', 'GET', ['student_id' => $student_id]);
            $messageResponse = $messageController->index($messageRequest);

            $emojiController = app()->make(EmojiController::class);
            $emojiRequest = Request::create('/emojis/student', 'GET', ['student_id' => $student_id]);
            $emojiResponse = $emojiController->emojis_student($emojiRequest);

            $response = [
                'student' =>   $student ,
                'emojis' => $emojiResponse->getData()->emojis ?? [],
                'thanks_messages' => $messageResponse->getData()->thanks_messages ?? [],
            ];

            return response()->json($response, 200);
        }


            //need testing
            public function top3(Request $request)
            {
                $student_id = $request->query('student_id');
                $student = Student::find($student_id);

                $folder = Folder::find($student->current_folder_id);
                $version = Version::find($folder->version_id);
                $book = Book::find($version->book_id);
                $origin_book_id = $book->id;

                $students = Student::where('branch_id', $student->branch_id)->get();
                $weekAgo = Carbon::now()->subWeek();
                $topStudents = [];

                foreach ($students as $otherStudent) {
                    $otherFolder = Folder::find($otherStudent->current_folder_id);
                    $otherVersion = Version::find($otherFolder->version_id);
                    $otherBook = Book::find($otherVersion->book_id);
                    $otherBookId = $otherBook->id;

                    if ($otherBookId == $origin_book_id) {
                        $tests = Test::where('student_id', $otherStudent->id)
                                     ->where('date', '>=', $weekAgo)
                                     ->get();

                        $numberOfPages = 0;

                        foreach ($tests as $test) {
                            $numberOfPages += $test->no_pages;
                        }

                        $topStudents[$numberOfPages] = [
                            'id' => $otherStudent->id,
                            'photo' => $otherStudent->photo,
                            'name' => $otherStudent->name,
                            'numberOfPages' => $numberOfPages,
                        ];
                    }
                }

              //  arsort($topStudents);
              ksort($topStudents);
              krsort($topStudents);

                $current_student_rank = 1;
                $current_numberOfPages = 0;
                foreach( $topStudents as $other_student ) {
                     if($other_student['id'] == $student_id)
                     {
                        $current_numberOfPages = $other_student['numberOfPages'];
                        break ;
                     }
                      $current_student_rank++;
                }

                $topStudents = array_slice($topStudents, 0, 3, true);

                $response = [
                    'current_numberOfPages' => $current_numberOfPages,
                    'current_student_rank' => $current_student_rank,
                    'top_students' => array_values($topStudents),
                ];

                return response()->json($response);
            }




            public function get_delta($id)
            {
                $student = Student::find($id);
                $prev = $student->previous_consistency ;
                $curr = $student->current_consistency ;

                if( $prev < $curr )
                {
                    return response()->json(['massage' => "ثابري الى الأمام",'color' =>"#12FF50"]);

                }

                if( $prev > $curr )
                {
                    return response()->json(['massage' => "إسعي الى الافضل",'color' =>"#DD3333"]);

                }
                return response()->json(['massage' => "أنت في ثبات",'color' =>"#DDDD12"]);



            }
}
