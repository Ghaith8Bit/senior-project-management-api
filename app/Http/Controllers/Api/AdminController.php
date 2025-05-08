<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectSuggestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function listSuggestions()
    {
        $suggestions = ProjectSuggestion::where('status', 'pending')
            ->whereDoesntHave('student.project')
            ->with('student')
            ->get();

        return response()->json(['data' => $suggestions]);
    }

    public function acceptSuggestion($id)
    {
        $suggestion = ProjectSuggestion::with('student')->findOrFail($id);

        // Ensure student doesn't already have a project
        if ($suggestion->student->project) {
            return response()->json(['message' => 'Student already has an approved project.'], 400);
        }

        // Create the project
        $project = Project::create([
            'student_id' => $suggestion->student_id,
            'project_suggestion_id' => $suggestion->id,
        ]);

        $suggestion->status = 'approved';
        $suggestion->save();

        ProjectSuggestion::where('student_id', $suggestion->student_id)
            ->where('status', '!=', 'approved')
            ->delete();

        return response()->json(['message' => 'Suggestion approved and project created.', 'data' => $project]);
    }

    public function rejectSuggestion($id)
    {
        $suggestion = ProjectSuggestion::findOrFail($id);
        $suggestion->update(['status' => 'rejected']);

        return response()->json(['message' => 'Suggestion rejected.']);
    }

    public function assignSupervisor(Request $request, $projectId)
    {
        $data = $request->validate([
            'supervisor_id' => 'required|exists:users,id'
        ]);

        $supervisor = \App\Models\User::findOrFail($data['supervisor_id']);

        if ($supervisor->role !== 'supervisor') {
            return response()->json(['message' => 'The selected user is not a supervisor.'], 400);
        }

        $project = Project::findOrFail($projectId);
        $project->supervisor_id = $supervisor->id;
        $project->save();

        return response()->json(['message' => 'Supervisor assigned successfully.']);
    }

    public function setFinalRating(Request $request, $projectId)
    {
        $data = $request->validate([
            'final_rating' => 'required|integer|min:0|max:100'
        ]);

        $project = Project::findOrFail($projectId);
        $project->final_rating = $data['final_rating'];
        $project->save();

        return response()->json(['message' => 'Final rating set successfully.']);
    }

    public function listProjects()
    {
        $projects = Project::with(['student', 'supervisor'])->get();
        return response()->json(['data' => $projects]);
    }

    public function viewProject($id)
    {
        $project = Project::with(['student', 'supervisor', 'notes', 'progressUpdates'])->findOrFail($id);
        return response()->json(['data' => $project]);
    }

    public function listSupervisors()
    {
        $supervisors = User::where('role', 'supervisor')->get();
        return response()->json(['data' => $supervisors]);
    }

    public function listStudents()
    {
        $students = User::where('role', 'student')->get();
        return response()->json(['data' => $students]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|in:student,supervisor,admin',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return response()->json([
            'token' => $user->createToken("api-token")->plainTextToken,
            'role' => $user->role,
        ]);
    }
}
