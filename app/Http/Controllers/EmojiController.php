<?php

namespace App\Http\Controllers;
use App\Models\emoji;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmojiController extends Controller
{

    public function index()
    {
        $emojis = Emoji::all();

        $emojisData = $emojis->map(function ($emoji) {
            return [
                'id' => $emoji->id,
                'emoji' => $emoji->emoji,
                'branch_id' => $emoji->branch_id,
            ];
        });

        return response()->json($emojisData);
    }
    public function store(Request $request)
    {

            $request->validate([
                'emoji' => 'required|image',
            ]);
            $admin = Auth::user();
            if($admin->branch_id){
            $bid =$admin->branch_id;}
            else
            {
                if (!$request->has('branch_id') || !in_array($request->branch_id, [1, 2, 3,4])) {
                    return response()->json(['error' => 'required coorect branch_id'], 400);
                }
                $bid=$request->branch_id ;

            }
            $emojiImage = $request->file('emoji');
            $newEmojiImage = time() . '_' . $emojiImage->getClientOriginalName();
            $emojiImage->move(public_path('emojis'), $newEmojiImage);

            $emoji = new emoji();
            $emoji->emoji = $newEmojiImage;
            $emoji->admin_id = $admin->id;
            $emoji->branch_id = $bid;
            $emoji->save();

            return response()->json(['message' => 'Emoji created successfully', 'data' => $emoji], 201);

    }

    public function getEmojisByBranch($branch_id)
    {
        $emojis = Emoji::where('branch_id', $branch_id)->get();

        $emojisData = $emojis->map(function ($emoji2) {
            return [
                'id' => $emoji2->id,
                'emoji' => $emoji2->emoji,
                'branch_id' => $emoji2->branch_id,
            ];
        });

        return response()->json($emojisData);
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
            $student_id = $request->query('student_id');
            $emojiIds = Test::where('student_id', $student_id)->pluck('emoji_id')->filter()->toArray();
            $emojis = Emoji::whereIn('id', $emojiIds)->pluck('emoji');

            return response()->json(['emojis' => $emojis], 200);
        }


          public function getImage($id)
        {
            $emoji = Emoji::find($id);

            if ($emoji) {
                $imagePath = public_path('emojis/' . $emoji->emoji);

                if (file_exists($imagePath)) {
                    return response()->file($imagePath);
                }

                return response()->json(['error' => 'Emoji not found'], 404);
            }

            return response()->json(['error' => 'Emoji not found'], 404);
        }

}
