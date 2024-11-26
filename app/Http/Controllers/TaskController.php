<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\StatusTask;
use App\Models\Task;
use App\Models\User;
use App\Providers\JwtServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    protected $jwtService;

    public function __construct(JwtServiceProvider $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->jwtService->haveAccess($request)) {
            $tasks = Task::with('project', 'statusTask', 'userResponsible', 'userCreator')->orderBy('d_completion', 'asc')->get();
            return response()->json(['data' => $tasks]);
        } else {
            return response()->json(['message' => 'No tienes acceso.'], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($this->jwtService->haveAccess($request)) {
            $validator = Validator::make($request->all(), [
                'c_name' => 'required|string|min:3|max:255',
                'c_description' => 'required|string|min:3',
                'd_completion' => 'required|date',
                'fk_project' => 'required|numeric|exists:projects,pk_project',
                'fk_status' => 'required|numeric|exists:status_tasks,pk_status',
                'fk_user_responsible' => 'required|numeric|exists:users,pk_user',
                'fk_user_creator' => 'required|numeric|exists:users,pk_user'
            ], [
                'c_name.required' => 'El nombre es requerido.',
                'c_name.string' => 'El nombre debe ser un texto.',
                'c_name.min' => 'El nombre debe tener mínimo 3 carácteres.',
                'c_name.max' => 'El nombre debe tener máximo 255 carácteres.',
                'c_description.required' => 'La descripción es requerida.',
                'c_description.string' => 'La descripción debe ser un texto.',
                'c_description.min' => 'La descripción debe tener mínimo 3 carácteres.',
                'd_completion.required' => 'La fecha es requerida.',
                'd_completion.date' => 'La fecha debe tener un formato tipo fecha valida.',
                'fk_project.required' => 'El proyecto es requerido.',
                'fk_project.numeric' => 'El proyecto debe ser un número.',
                'fk_project.exists' => 'El proyecto no existe.',
                'fk_status.required' => 'El estado de la tarea es requerido.',
                'fk_status.numeric' => 'El estado de la tarea debe ser un número.',
                'fk_status.exists' => 'El estado de la tarea no existe.',
                'fk_user_responsible.required' => 'El usuario responsable es requerido.',
                'fk_user_responsible.numeric' => 'El usuario responsable debe ser un número.',
                'fk_user_responsible.exists' => 'El usuario responsable no existe.',
                'fk_user_creator.required' => 'El usuario creador es requerido.',
                'fk_user_creator.numeric' => 'El usuario creador debe ser un número.',
                'fk_user_creator.exists' => 'El usuario creador no existe.',
            ]);

            if ($validator->fails()) return response()->json(['message' => $validator->errors()], 400);

            $task = Task::create($request->all());
            $task->load('project', 'statusTask', 'userResponsible', 'userCreator');

            return response()->json(['data' => $task, 'message' => 'Tarea creada.'], 201);
        } else {
            return response()->json(['message' => 'No tienes acceso.'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::find($id);
        if (!$task) return response()->json(['data' => null, 'message' => 'La tarea no existe.'], 404);

        $task->load('project', 'statusTask', 'userResponsible', 'userCreator');
        return response()->json(['data' => $task]);
    }

    /**
     * Get all tasks by user
     */
    public function getTasksByUser(string $idUser)
    {
        $user = User::find($idUser);
        if (!$user) return response()->json(['data' => null, 'message' => 'El usuario no existe.'], 404);

        $tasks = Task::where('fk_user_responsible', $idUser)->orderBy('d_completion', 'asc')->get();

        $tasks->load('project', 'statusTask', 'userResponsible', 'userCreator');
        return response()->json(['data' => $tasks]);
    }

    /**
     * Get all tasks by project
     */
    public function getTasksByProject(string $idProject)
    {
        $project = Project::find($idProject);
        if (!$project) return response()->json(['data' => null, 'message' => 'El proyecto no existe.'], 404);

        $tasks = Task::where('fk_project', $idProject)->orderBy('d_completion', 'asc')->get();

        $tasks->load('statusTask', 'userResponsible', 'userCreator');
        return response()->json(['data' => $tasks]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'c_name' => 'string|min:3|max:255',
            'c_description' => 'string|min:3',
            'd_completion' => 'date',
            'fk_project' => 'numeric|exists:projects,pk_project',
            'fk_status' => 'numeric|exists:status_tasks,pk_status',
            'fk_user_responsible' => 'numeric|exists:users,pk_user'
        ], [
            'c_name.string' => 'El nombre debe ser un texto.',
            'c_name.min' => 'El nombre debe tener mínimo 3 carácteres.',
            'c_name.max' => 'El nombre debe tener máximo 255 carácteres.',
            'c_description.string' => 'La descripción debe ser un texto.',
            'c_description.min' => 'La descripción debe tener mínimo 3 carácteres.',
            'd_completion.date' => 'La fecha debe tener un formato tipo fecha valida.',
            'fk_project.numeric' => 'El proyecto debe ser un número.',
            'fk_project.exists' => 'El proyecto no existe.',
            'fk_status.numeric' => 'El estado de la tarea debe ser un número.',
            'fk_status.exists' => 'El estado de la tarea no existe.',
            'fk_user_responsible.numeric' => 'El usuario responsable debe ser un número.',
            'fk_user_responsible.exists' => 'El usuario responsable no existe.'
        ]);

        if ($validator->fails()) return response()->json(['message' => $validator->errors()], 400);

        $task = Task::find($id);
        if (!$task) return response()->json(['data' => null, 'message' => 'La tarea no existe.'], 404);

        if ($request->fk_project) {
            $project = Project::find($request->fk_project);
            if (!$project) return response()->json(['data' => null, 'message' => 'El proyecto no existe.'], 404);
        }

        if ($request->fk_status) {
            $status = StatusTask::find($request->fk_status);
            if (!$status) return response()->json(['data' => null, 'message' => 'El estado de la tarea no existe.'], 404);
        }

        if ($request->fk_user_responsible) {
            $userResponsible = User::find($request->fk_user_responsible);
            if (!$userResponsible) return response()->json(['data' => null, 'message' => 'El usuario responsable no existe.'], 404);
        }

        $task->c_name = $request->c_name ?? $task->c_name;
        $task->c_description = $request->c_description ?? $task->c_description;
        $task->d_completion = $request->d_completion ?? $task->d_completion;
        $task->fk_project = $request->fk_project ?? $task->fk_project;
        $task->fk_status = $request->fk_status ?? $task->fk_status;
        $task->fk_user_responsible = $request->fk_user_responsible ?? $task->fk_user_responsible;
        $task->update();

        $task->load('project', 'statusTask', 'userResponsible', 'userCreator');
        return response()->json(['data' => $task]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if ($this->jwtService->haveAccess($request)) {
            $task = Task::find($id);
            if (!$task) return response()->json(['data' => null, 'message' => 'La tarea no existe.'], 404);

            $task->delete();
            return response()->json(['data' => $task, 'message' => 'Tarea eliminada.']);
        } else {
            return response()->json(['message' => 'No tienes acceso.'], 401);
        }
    }
}
