<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // max 5 MB
            ]);

            $file = $request->file('file');

            // Generovanie unikátneho názvu súboru
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Uloženie súboru do storage/app/public/images
            $path = $file->storeAs('images', $filename, 'public');

            // Generovanie URL pre prístup k súboru
            $url = asset('storage/' . $path);

            return response()->json([
                'location' => $url,
                'filename' => $filename,
                'path' => $path,
                'message' => 'Obrázok bol úspešne nahraný'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Neplatný súbor',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Chyba pri nahrávaní súboru',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
