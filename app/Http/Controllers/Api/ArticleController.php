<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
   
    public function index()
    {
        return response()->json(Article::all());
    }

  
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

    
    public function show(Article $article)
    {
        return response()->json($article);
    }

   
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

   
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json(null,204);
    }
}
