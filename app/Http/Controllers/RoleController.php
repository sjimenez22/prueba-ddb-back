<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAll()
    {
        $roles = Role::orderBy('c_name', 'asc')->get();
        return response()->json(['data' => $roles]);
    }
}
