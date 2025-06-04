<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function store(Request $request, Article $article)
    {
        return $this->executeWithTransaction(function() use ($request, $article) {
            // Validate request using parent method
            $errors = $this->validateRequest($request, [
                'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // max 10MB
                'category' => 'nullable|string|max:255'
            ], [
                'file.required' => 'Súbor je povinný',
                'file.mimes' => 'Povolené sú len PDF, DOC a DOCX súbory',
                'file.max' => 'Súbor môže mať maximálne 10MB'
            ]);

            if ($errors) {
                return $this->error('Neplatné údaje', $errors, 422);
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
                'article_id' => $article->id,
                'category' => $request->category

            ]);

            if ($request->expectsJson()) {
                return $this->success(['file' => $file], 'Súbor bol úspešne nahraný');
            }

            return redirect()->back()->with('success', 'Súbor bol úspešne nahraný.');
        }, 'Nahrávanie súboru zlyhalo');
    }

    public function download(File $file)
    {
        try {
            if (!Storage::disk('public')->exists($file->file_path)) {
                if (request()->expectsJson()) {
                    return $this->error('Súbor nebol nájdený', null, 404);
                }
                abort(404, 'Súbor nebol nájdený.');
            }

            return response()->download(storage_path('app/public/' . $file->file_path), $file->original_name);
        } catch (\Exception $e) {
            return $this->handleException($e, 'File download failed', 'Sťahovanie súboru zlyhalo');
        }
    }

    public function destroy(File $file)
    {
        return $this->executeWithTransaction(function() use ($file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();

            if (request()->expectsJson()) {
                return $this->success([], 'Súbor bol odstránený');
            }

            return redirect()->back()->with('success', 'Súbor bol odstránený.');
        }, 'Odstránenie súboru zlyhalo');
    }
}
