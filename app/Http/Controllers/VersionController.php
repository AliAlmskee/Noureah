<?php

namespace App\Http\Controllers;

use App\Models\Version;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($book_id)
    {
        $versions = Version::where('book_id', $book_id)->get();
        return response()->json($versions);
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(Version $version)
    {
        return response()->json($version);
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Version $version)
    {
        $version->delete();

        return response()->json(null, 204);
    }
}
