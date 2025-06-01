<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        $departments = Department::orderBy('name')->get();
        return response()->json([
            'data'    => $departments,
            'message' => 'Departments retrieved'
        ]);
    }
}