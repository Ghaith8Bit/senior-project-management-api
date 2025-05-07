<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\ProgressUpdate;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SupervisorController extends Controller
{
    public function myProjects()
    {
        $projects = Auth::user()->supervisedProjects()->with('student')->get();

        return response()->json(['data' => $projects]);
    }

    public function showProject($id)
    {
        $project = Project::with(['student', 'notes', 'progressUpdates'])->findOrFail($id);
        Gate::authorize('is-project-supervisor', $project);

        return response()->json(['data' => $project]);
    }

    public function addNote(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        Gate::authorize('is-project-supervisor', $project);

        $data = $request->validate([
            'note' => 'required|string',
        ]);

        $note = Note::create([
            'project_id' => $project->id,
            'supervisor_id' => Auth::id(),
            'note_text' => $data['note'],
        ]);

        return response()->json(['message' => 'Note added.', 'data' => $note]);
    }

    public function rateProgress(Request $request, $id)
    {
        $update = ProgressUpdate::findOrFail($id);
        Gate::authorize('is-project-supervisor', $update->project);

        $data = $request->validate([
            'progress_rating' => 'required|integer|min:0|max:100',
        ]);

        $update->progress_rating = $data['progress_rating'];
        $update->save();

        return response()->json(['message' => 'Progress rated successfully.']);
    }
}
