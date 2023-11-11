<?php

namespace App\Http\Controllers;
use App\Models\emoji;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
class EmojiController extends Controller
{
    public function store(Request $request)
    {

            $request->validate([
                'emoji' => 'required|image',
                'admin_id' => 'required|exists:admins,id',
            ]);

            $admin = Admin::findOrFail($request->admin_id);


            $emojiImage = $request->file('emoji');
            $newEmojiImage = time() . '_' . $emojiImage->getClientOriginalName();
            $emojiImage->move(public_path('emojis'), $newEmojiImage);

            $emoji = new emoji();
            $emoji->emoji = $newEmojiImage;
            $emoji->admin_id = $request->admin_id;
            $emoji->branch_id = $admin->branch_id;
            $emoji->save();

            return response()->json(['message' => 'Emoji created successfully', 'data' => $emoji], 201);

    }
    public function getEmojisByBranch($branch_id)
{
    $emojis = Emoji::where('branch_id', $branch_id)->get();

    return response()->json(['data' => $emojis], 200);
}
}
