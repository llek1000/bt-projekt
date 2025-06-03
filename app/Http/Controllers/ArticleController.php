<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Article::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'title'               => 'required|string|max:255',
            'content'             => 'required|string',
            'conference_year_id'  => 'required|exists:conference_years,id',
        ]);
        if($v->fails()){
            return response()->json(['errors'=>$v->errors()],422);
        }
        $article = Article::create($v->validated());
        return response()->json(['data'=>$article],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return response()->json($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $v = Validator::make($request->all(), [
            'title'               => 'sometimes|required|string|max:255',
            'content'             => 'sometimes|required|string',
            'conference_year_id'  => 'sometimes|required|exists:conference_years,id',
        ]);
        if($v->fails()){
            return response()->json(['errors'=>$v->errors()],422);
        }
        $article->update($v->validated());
        return response()->json(['data'=>$article]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json(null,204);
    }

    /**
     * Handler pre TinyMCE image upload
     */
    public function uploadImageForTinyMCE(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>['message'=>$validator->errors()->first()]], 422);
        }
        $file = $request->file('file');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('public/uploads/images', $filename);
        if (! $path) {
            return response()->json(['error'=>['message'=>'Nepodarilo sa uložiť obrázok.']], 500);
        }
        $relative = str_replace('public/','',$path);
        $url = asset("storage/{$relative}");
        return response()->json(['location'=>$url]);
    }
}
