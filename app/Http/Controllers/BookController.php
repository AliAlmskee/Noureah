<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::all();
        return response()->json($books);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'no_exams' => 'required|integer',
        ]);

        $book = Book::create($data);

        return response()->json($book, 201);
    }


    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'name' => 'string|max:255',
            'no_exams' => 'integer',
        ]);

        $book->update($data);

        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json(null, 204);
    }
}
