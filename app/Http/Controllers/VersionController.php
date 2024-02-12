<?php

namespace App\Http\Controllers;

use App\Models\Version;
use Illuminate\Http\Request;

class VersionController extends Controller
{
  
    public function index($book_id)
    {
        $versions = Version::where('book_id', $book_id)->get();
        return response()->json($versions);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'book_id' => 'required|exists:books,id',
            'no_pages' => 'required|integer',
        ]);

        $version = Version::create($data);

        return response()->json($version, 201);
    }

 
    public function show(Version $version)
    {
        return response()->json($version);
    }


    public function update(Request $request, Version $version)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'book_id' => 'required|exists:books,id',
            'no_pages' => 'required|integer',
        ]);

        $version->update($data);

        return response()->json($version);
    }

    
    public function destroy(Version $version)
    {
        $version->delete();

        return response()->json(null, 204);
    }
}
