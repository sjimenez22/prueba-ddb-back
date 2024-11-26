<?php

namespace App\Http\Controllers;

use App\Models\StatusTask;

class StatusTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAll()
    {
        $status = StatusTask::orderBy('c_name', 'asc')->get();
        return response()->json(['data' => $status]);
    }
}
