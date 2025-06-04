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

            $errors = $this->validateRequest($request, [
                'file' => 'required|file|max:10240', // 10MB max
                'category' => 'nullable|string|max:255'
            ], [
                'file.max' => 'Súbor môže mať maximálne 10MB'
            ]);

            if ($errors) {
                return response()->json(['errors' => $errors], 422);
            }

            $uploadedFile = $request->file('file');


            $fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $uploadedFile->getClientOriginalExtension();


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


            return response()->json([
                'location' => asset('storage/' . $filePath),
                'filename' => $file->original_name,
                'file_id' => $file->id,
                'download_url' => route('files.download', $file->id)
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, 'File upload failed', 'Nahrávanie súboru zlyhalo: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request, Article $article)
    {
        try {
            $errors = $this->validateRequest($request, [
                'file' => 'required|file|max:10240',
                'category' => 'nullable|string|max:255'
            ], [
                'file.max' => 'Súbor môže mať maximálne 10MB'
            ]);

            if ($errors) {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => $errors], 422);
                }
                return redirect()->back()->withErrors($errors);
            }

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

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->handleException($e, 'File upload failed', 'Nahrávanie súboru zlyhalo', 500);
            }
            $this->handleException($e, 'File upload failed', 'Nahrávanie súboru zlyhalo', 500);
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
            if (request()->expectsJson()) {
                return $this->handleException($e, 'File download failed', 'Sťahovanie súboru zlyhalo', 500);
            }
            $this->handleException($e, 'File download failed', 'Sťahovanie súboru zlyhalo', 500);
            abort(500, 'Sťahovanie súboru zlyhalo');
        }
    }

    public function destroy(File $file)
    {
        try {

            if (Auth::id() !== $file->uploaded_by && !$this->hasRole(Auth::user(), 'admin')) {
                return response()->json(['message' => 'Nemáte oprávnenie vymazať tento súbor'], 403);
            }

            Storage::disk('public')->delete($file->file_path);
            $file->delete();

            if (request()->expectsJson()) {
                return response()->json(['message' => 'Súbor bol odstránený.']);
            }

            return redirect()->back()->with('success', 'Súbor bol odstránený.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return $this->handleException($e, 'File deletion failed', 'Vymazanie súboru zlyhalo', 500);
            }
            $this->handleException($e, 'File deletion failed', 'Vymazanie súboru zlyhalo', 500);
            return redirect()->back()->with('error', 'Vymazanie súboru zlyhalo');
        }
    }


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
            return $this->handleException($e, 'File listing failed', 'Načítanie súborov zlyhalo', 500);
        }
    }
}
