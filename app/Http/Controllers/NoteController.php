<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;

class NoteController extends Controller
{
    public function index()
    {
        $note = Note::all();

        return NoteResource::collection($note);
    }

    public function store(StoreNoteRequest $request)
    {
        try {
            $note = Note::create($request->all());

            return response()->json([
                'message' => 'Created SuccessFully',
                'data' => NoteResource::make($note),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateNoteRequest $request, $noteId)
    {
        try {
            $note = Note::find($noteId);
            if (! $note) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $note->update($request->all());

            return response()->json([
                'message' => 'Updated SuccessFully',
                'data' => NoteResource::make($note),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($noteId)
    {
        $note = Note::find($noteId);
        if (! $note) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return NoteResource::make($note);
    }

    public function delete($noteId)
    {
        try {
            $note = Note::find($noteId);
            if (! $note) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            $note->delete();

            return response()->json([
                'message' => 'Deleted SuccessFully',
                'data' => NoteResource::make($note),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
