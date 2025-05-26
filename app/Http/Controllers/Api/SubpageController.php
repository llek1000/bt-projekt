<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subpage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SubpageController extends Controller
{
    public function index()
    {
        return Subpage::all();
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subpage = Subpage::create($validator->validated());

        return response()->json(['data' => $subpage, 'message' => 'Podstránka úspešne vytvorená.'], 201);
    }

    public function show($id)
    {
        return Subpage::findOrFail($id);
    }

    public function update(Request $request, Subpage $subpage) 
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subpage->update($validator->validated());

        return response()->json(['data' => $subpage, 'message' => 'Podstránka úspešne aktualizovaná.']);
    }

    public function destroy($id)
    {
        $subpage = Subpage::findOrFail($id);
        $subpage->delete();
        return response()->json(null, 204);
    }

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => ['message' => $validator->errors()->first()]], 422);
        }

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            $path = $file->storeAs('public/uploads/images', $filename);

            if ($path) {
                return response()->json([
                    'url' => Storage::url(str_replace('public/', '', $path))
                ]);
            }

            return response()->json(['error' => ['message' => 'Nepodarilo sa uložiť obrázok.']], 500);
        }

        return response()->json(['error' => ['message' => 'Žiadny súbor na nahratie.']], 400);
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

    public function uploadImageForTinyMCE(Request $request) // Metóda pre TinyMCE
    {
        $fileKey = 'file';
        if (!$request->hasFile($fileKey) && $request->hasFile('blob')) {
            $fileKey = 'blob';
        }

        $validator = Validator::make($request->all(), [
            $fileKey => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => ['message' => $validator->errors()->first()]], 422);
        }

        if ($request->hasFile($fileKey)) {
            $file = $request->file($fileKey);
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/uploads/images', $filename);

            if ($path) {
                $relative = str_replace('public/', '', $path); 
                $url = asset("storage/{$relative}");           
                return response()->json(['location' => $url]);
            }
            return response()->json(['error' => ['message' => 'Nepodarilo sa uložiť obrázok.']], 500);
        }
        return response()->json(['error' => ['message' => 'Žiadny súbor na nahratie.']], 400);
    }
}