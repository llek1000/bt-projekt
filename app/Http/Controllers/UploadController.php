<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:5120', // max 5 MB
        ]);

        $path = $request->file('file')->store('images', 'public');
        $url  = asset('storage/' . $path);

        return response()->json(['location' => $url]);
    }
}