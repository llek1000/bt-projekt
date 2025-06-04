<?php
namespace App\Http\Controllers;

use App\Models\EditorAssignment;
use Illuminate\Http\Request;

class EditorAssignmentController extends Controller
{
    public function index($year)
    {
        $assignments = EditorAssignment::with('user')
            ->where('conference_year_id', $year)
            ->get();

        return response()->json([
            'data' => $assignments,
            'message' => 'Editor assignments retrieved successfully'
        ]);
    }

    public function store(Request $r, $year)
    {
        $r->validate(['user_id' => 'required|exists:users,id']);
        
        $assignment = EditorAssignment::create([
            'user_id' => $r->user_id,
            'conference_year_id' => $year
        ]);

        return response()->json([
            'data' => $assignment->load('user'),
            'message' => 'Editor assignment created successfully'
        ], 201);
    }

    public function destroy($year, EditorAssignment $assignment)
    {
        $assignment->delete();
        return response()->json([
            'message' => 'Editor assignment deleted successfully'
        ], 204);
    }

    /**
     * Get assignments for the current authenticated user
     */
    public function myAssignments(Request $request)
    {
        $user = $request->user();
        
        $assignments = EditorAssignment::with(['conferenceYear'])
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'data' => $assignments,
            'message' => 'My assignments retrieved successfully'
        ]);
    }
}