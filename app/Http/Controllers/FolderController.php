<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $folders = Folder::all();
        return response()->json($folders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_page' => 'required|integer',
            'end_page' => 'required|integer',
            'version_id' => 'required|exists:versions,id',
        ]);

        $folder = Folder::create($data);

        return response()->json($folder, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Folder $folder)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_page' => 'required|integer',
            'end_page' => 'required|integer',
            'version_id' => 'required|exists:versions,id',
        ]);

        $folder->update($data);

        return response()->json($folder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        $folder->delete();

        return response()->json(null, 204);
    }
}
