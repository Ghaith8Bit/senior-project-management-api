<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgressUpdate;
use App\Models\Project;
use App\Models\ProjectSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function suggestProject(Request $request)
    {
        $user = Auth::user();

        if ($user->project) {
            return response()->json([
                'message' => 'You already have an approved project and cannot suggest a new one.'
            ], 400);
        }

        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $suggestion = ProjectSuggestion::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'student_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Project suggestion submitted successfully.',
            'data' => $suggestion
        ]);
    }

    public function viewProject()
    {
        $project = Auth::user()->project;

        if (!$project) {
            return response()->json([
                'message' => 'No project assigned yet.'
            ], 404);
        }

        return response()->json([
            'data' => $project
        ]);
    }

    public function submitProgressUpdate(Request $request)
    {
        $data = $request->validate([
            'update_text' => 'required|string',
        ]);

        $project = Auth::user()->project;

        if (!$project) {
            return response()->json([
                'message' => 'No project assigned yet.'
            ], 404);
        }

        // Create progress update
        $update = ProgressUpdate::create([
            'project_id' => $project->id,
            'student_id' => Auth::id(),
            'update_text' => $data['update_text'],
        ]);

        return response()->json([
            'message' => 'Progress update submitted successfully.',
            'data' => $update
        ]);
    }

    public function viewProgressUpdates()
    {
        $project = Auth::user()->project;

        if (!$project) {
            return response()->json(['message' => 'No project found for this student.'], 404);
        }

        $updates = $project->progressUpdates()->latest()->get();

        return response()->json(['data' => $updates]);
    }


    public function viewNotes()
    {
        $project = Auth::user()->project;

        if (!$project) {
            return response()->json([
                'message' => 'No project assigned yet.'
            ], 404);
        }

        $notes = $project->notes;

        if ($notes->isEmpty()) {
            return response()->json([
                'message' => 'No notes available for this project.'
            ], 404);
        }

        return response()->json([
            'data' => $notes
        ]);
    }
}
