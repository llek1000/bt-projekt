<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * Upload file for TinyMCE editor
     */
    public function uploadForEditor(Request $request)
    {
        try {
            // Validate file for TinyMCE
            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
                'category' => 'nullable|string|max:255'
            ], [
                'file.max' => 'Súbor môže mať maximálne 10MB'
            ]);

            $uploadedFile = $request->file('file');
            
            // Generate unique filename
            $fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $uploadedFile->getClientOriginalExtension();

            // Store in editor_files directory
            $filePath = $uploadedFile->storeAs('editor_files', $fileName, 'public');

            $file = File::create([
                'name' => $fileName,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'file_path' => $filePath,
                'mime_type' => $uploadedFile->getMimeType(),
                'file_size' => $uploadedFile->getSize(),
                'category' => $request->get('category'),
                'uploaded_by' => Auth::id()
            ]);

            // Return TinyMCE compatible response
            return response()->json([
                'location' => asset('storage/' . $filePath),
                'filename' => $file->original_name,
                'file_id' => $file->id,
                'download_url' => route('files.download', $file->id)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Nahrávanie súboru zlyhalo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, Article $article)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240',
                'category' => 'nullable|string|max:255'
            ], [
                'file.max' => 'Súbor môže mať maximálne 10MB'
            ]);

            $uploadedFile = $request->file('file');
            $fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $uploadedFile->getClientOriginalExtension();

            $filePath = $uploadedFile->storeAs('article_files/' . $article->id, $fileName, 'public');

            $file = File::create([
                'name' => $fileName,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'file_path' => $filePath,
                'mime_type' => $uploadedFile->getMimeType(),
                'file_size' => $uploadedFile->getSize(),
                'category' => $request->get('category'),
                'article_id' => $article->id,
                'uploaded_by' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['data' => $file, 'message' => 'Súbor bol úspešne nahraný.']);
            }

            return redirect()->back()->with('success', 'Súbor bol úspešne nahraný.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Nahrávanie súboru zlyhalo'], 500);
            }
            return redirect()->back()->with('error', 'Nahrávanie súboru zlyhalo');
        }
    }

    public function download(File $file)
    {
        try {
            if (!Storage::disk('public')->exists($file->file_path)) {
                abort(404, 'Súbor nebol nájdený');
            }

            return response()->download(storage_path('app/public/' . $file->file_path), $file->original_name);
        } catch (\Exception $e) {
            \Log::error('File download failed: ' . $e->getMessage());
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Sťahovanie súboru zlyhalo'], 500);
            }
            abort(500, 'Sťahovanie súboru zlyhalo');
        }
    }

    public function destroy(File $file)
    {
        try {
            // Check if user has permission to delete this file
            if (Auth::id() !== $file->uploaded_by && !Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'Nemáte oprávnenie vymazať tento súbor'], 403);
            }

            Storage::disk('public')->delete($file->file_path);
            $file->delete();

            if (request()->expectsJson()) {
                return response()->json(['message' => 'Súbor bol odstránený.']);
            }

            return redirect()->back()->with('success', 'Súbor bol odstránený.');
        } catch (\Exception $e) {
            \Log::error('File deletion failed: ' . $e->getMessage());
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Vymazanie súboru zlyhalo'], 500);
            }
            return redirect()->back()->with('error', 'Vymazanie súboru zlyhalo');
        }
    }

    /**
     * List files for user's editor
     */
    public function listForEditor(Request $request)
    {
        try {
            $files = File::where('uploaded_by', Auth::id())
                         ->orderBy('created_at', 'desc')
                         ->get();

            return response()->json([
                'data' => $files,
                'message' => 'Súbory boli úspešne načítané'
            ]);
        } catch (\Exception $e) {
            \Log::error('File listing failed: ' . $e->getMessage());
            return response()->json(['message' => 'Načítanie súborov zlyhalo'], 500);
        }
    }
}
