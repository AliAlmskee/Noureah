<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Test;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $student_id = $request->query('student_id');

        if (!$student_id) {
            return response()->json(['error' => 'Missing student_id'], 400);
        }

        $tests = Test::where('student_id', $student_id)->get();

        if ($tests->isEmpty()) {
            return response()->json(['error' => 'No tests found for the student'], 404);
        }

        $thanks_messages = $tests->map(function ($test) {
            $messages = Message::where('test_id', $test->id)->get();
            $filtered_messages = $messages->filter(function ($message) {
                return $message->thanks_message !== null;
            });
            return $filtered_messages->pluck('thanks_message')->all();
        })->filter();

        return response()->json(['thanks_messages' => $thanks_messages]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'thanks_message' => 'nullable|string',
        ]);

        $admin = Auth::user();
        if($admin){
        $data['admin_id'] =$admin->id;}

        $message = Message::create($data);

        return response()->json($message, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        $data = $request->validate([
            'test_id' => 'exists:tests,id',
            'thanks_message' => 'nullable|string',
        ]);
        $admin = Auth::user();
        $data['admin_id'] = $admin->id;

        $message->update($data);

        return response()->json($message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return response()->json(null, 204);
    }





}
