<?php
namespace App\Http\Controllers;

use App\Models\EditorAssignment;
use Illuminate\Http\Request;

class EditorAssignmentController extends Controller
{
    public function index($year)
    {
        return EditorAssignment::with('user')
            ->where('conference_year_id',$year)->get();
    }

    public function store(Request $r, $year)
    {
        $r->validate(['user_id'=>'required|exists:users,id']);
        return EditorAssignment::create([
            'user_id'=>$r->user_id,
            'conference_year_id'=>$year
        ]);
    }

    public function destroy($year, EditorAssignment $assignment)
    {
        $assignment->delete();
        return response()->noContent();
    }
}