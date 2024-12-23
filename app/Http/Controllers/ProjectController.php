<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Providers\JwtServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{

    protected $jwtService;

    public function __construct(JwtServiceProvider $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('user')->orderby('pk_project', 'desc')->get();
        return response()->json(['data' => $projects]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($this->jwtService->haveAccess($request)) {
            $validator = Validator::make($request->all(), [
                'c_name' => 'required|string|min:3|max:255',
                'fk_user' => 'required|numeric|exists:users,pk_user'
            ], [
                'c_name.required' => 'El nombre es requerido.',
                'c_name.string' => 'El nombre debe ser un texto.',
                'c_name.min' => 'El nombre debe tener mínimo 3 carácteres.',
                'c_name.max' => 'El nombre debe tener máximo 255 carácteres.',
                'fk_user.required' => 'El usuario es requerido.',
                'fk_user.numeric' => 'El usuario debe ser un número.',
                'fk_user.exists' => 'El usuario no existe.'
            ]);

            if ($validator->fails()) return response()->json(['message' => $validator->errors()], 400);

            $project = Project::create($request->all());
            $project->load('user');

            return response()->json(['data' => $project, 'message' => 'Proyecto creado.'], 201);
        } else {
            return response()->json(['message' => 'No tienes acceso.'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::find($id);
        if (!$project) return response()->json(['data' => null, 'message' => 'El proyecto no existe.'], 404);

        $project->load('user');
        return response()->json(['data' => $project]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($this->jwtService->haveAccess($request)) {
            $validator = Validator::make($request->all(), [
                'c_name' => 'required|string|min:3|max:255'
            ], [
                'c_name.required' => 'El nombre es requerido.',
                'c_name.string' => 'El nombre debe ser un texto.',
                'c_name.min' => 'El nombre debe tener mínimo 3 carácteres.',
                'c_name.max' => 'El nombre debe tener máximo 255 carácteres.'
            ]);

            if ($validator->fails()) return response()->json(['message' => $validator->errors()], 400);

            $project = Project::find($id);
            if (!$project) return response()->json(['data' => null, 'message' => 'El proyecto no existe.'], 404);

            $project->c_name = $request->c_name;
            $project->update();

            return response()->json(['data' => $project]);
        } else {
            return response()->json(['message' => 'No tienes acceso.'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if ($this->jwtService->haveAccess($request)) {
            $project = Project::find($id);
            $project->load('tasks');
            if (!$project) return response()->json(['data' => null, 'message' => 'El proyecto no existe.'], 404);

            if ($project->tasks && count($project->tasks) > 0) return response()->json(['data' => null, 'message' => 'El proyecto no se puede eliminar ya que tiene tareas asignadas.'], 403);

            $project->delete();
            return response()->json(['data' => $project, 'message' => 'Proyecto eliminado.']);
        } else {
            return response()->json(['message' => 'No tienes acceso.'], 401);
        }
    }
}
