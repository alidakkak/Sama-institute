<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\DeviceToken;
use App\Models\Note;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index()
    {
        $note = Note::all();

        return NoteResource::collection($note);
    }

    /// API For Flutter To Get Nots
    public function getNote($semesterID)
    {
        $studentID = auth::guard('api_student')->user()->id;
        $note = Note::where('student_id', $studentID)
            ->where('semester_id', $semesterID)
            ->orderBy('created_at', 'desc')
            ->get();

        return NoteResource::collection($note);
    }

    public function store(StoreNoteRequest $request)
    {
        try {
            $note = Note::create($request->all());

            $title = 'New Note Added';
            $body = 'A new note has been added for the student.';
            /// Device Key
            $FcmToken = DeviceToken::where('student_id', $note->student_id)->pluck('token')->toArray();

            $data = ['note_id' => $note->id];

            FirebaseService::BasicSendNotification($title, $body, $FcmToken, $data);

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
