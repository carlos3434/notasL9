<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Http\Requests\NoteRequest;

class NoteController extends Controller
{
    public function index()
    {
        return Note::all();
    }

    public function show(Note $note)
    {
        return $note;
    }

    public function store(NoteRequest $request)
    {
        $note = Note::create($request->all());

        return response()->json($note, 201);
    }

    public function update(Request $request, Note $note)
    {
        $note->update($request->all());

        return response()->json($note, 200);
    }

    public function destroy(Note $note)
    {
        $note->delete();

        return response()->json(null, 204);
    }
}
