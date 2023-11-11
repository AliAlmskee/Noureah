<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = Message::all();
        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'admin_id' => 'required|exists:admins,id',
            'thanks_message' => 'nullable|string',
        ]);

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
            'admin_id' => 'exists:admins,id',
            'thanks_message' => 'nullable|string',
        ]);

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
