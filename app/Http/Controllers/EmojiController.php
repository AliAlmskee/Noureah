<?php

namespace App\Http\Controllers;
use App\Models\emoji;
use App\Models\Admin;
use App\Models\Test;
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
        $emojis = Emoji::where('branch_id', $branch_id)->pluck('emoji');

        return response()->json(['data' => $emojis], 200);
    }

        public function delete($id)
        {
            $emoji = Emoji::findOrFail($id);

            $emojiImagePath = public_path('emojis') . '/' . $emoji->emoji;
            if (file_exists($emojiImagePath)) {
                unlink($emojiImagePath);
            }

            $emoji->delete();

            return response()->json(['message' => 'Emoji deleted successfully'], 200);
        }

        public function emojis_student(Request $request)
        {
            $student_id = $request->input('student_id');
            $emojis = Test::where('student_id', $student_id)->pluck('emoji_id')->filter()->toArray();
            $emojiIds = array_values($emojis);
            return response()->json(['emojis' => $emojiIds], 200);
        }



          public function getImage($id)
        {
            $emoji = Emoji::find($id);

            if ($emoji) {
                $imagePath = public_path('emojis/' . $emoji->emoji);

                if (file_exists($imagePath)) {
                    return response()->file($imagePath);
                }

                return response()->json(['error' => 'Image not found'], 404);
            }

            return response()->json(['error' => 'Emoji not found'], 404);
        }

}
