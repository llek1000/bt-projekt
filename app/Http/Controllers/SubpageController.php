<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subpage;
use Illuminate\Http\Request;

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
}