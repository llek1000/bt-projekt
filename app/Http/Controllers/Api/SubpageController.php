<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subpage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubpageController extends Controller
{
    public function index()
    {
        return Subpage::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'conference_year_id' => 'required|exists:conference_years,id',
        ]);

        $subpage = Subpage::create($validated);
        return response()->json($subpage, 201);
    }

    public function show($id)
    {
        return Subpage::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $subpage = Subpage::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'conference_year_id' => 'required|exists:conference_years,id',
        ]);

        $subpage->update($validated);
        return response()->json($subpage);
    }

    public function destroy($id)
    {
        $subpage = Subpage::findOrFail($id);
        $subpage->delete();
        return response()->json(null, 204);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('upload')->store('images', 'public');
        $url = Storage::url($path);

        return response()->json([
            'url' => $url
        ]);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:doc,docx,pdf|max:2048',
        ]);

        $path = $request->file('file')->store('documents', 'public');
        $url = Storage::url($path);

        return response()->json([
            'url' => $url
        ]);
    }
}