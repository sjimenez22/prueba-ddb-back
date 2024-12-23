<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Providers\JwtServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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
        $users = User::with('role')->get();
        return response()->json(['data' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($this->jwtService->haveAccess($request)) {
            $validator = Validator::make($request->all(), [
                'c_name' => 'required|string|min:5|max:255',
                'c_email' => 'required|string|email',
                'c_password' => 'required|string|min:7',
                'fk_role' => 'required|numeric|exists:roles,pk_role'
            ], [
                'c_name.required' => 'El nombre es requerido.',
                'c_name.string' => 'El nombre debe ser un texto.',
                'c_name.min' => 'El nombre debe tener mínimo 5 carácteres.',
                'c_name.max' => 'El nombre debe tener máximo 255 carácteres.',
                'c_email.required' => 'El correo es requerido.',
                'c_email.string' => 'El correo debe ser un texto.',
                'c_email.email' => 'El correo debe tener un formato de correo electrónico valido.',
                'c_password.required' => 'La contraseña es requerida.',
                'c_password.string' => 'La contraseña debe ser un texto.',
                'c_password.min' => 'La contraseña debe tener mínimo 7 carácteres.',
                'fk_role.required' => 'El rol es requerido.',
                'fk_role.numeric' => 'El rol debe ser un número.',
                'fk_role.exists' => 'El rol no existe.'
            ]);

            if ($validator->fails()) return response()->json(['message' => $validator->errors()], 400);

            $validateUser = User::where('c_email', $request->c_email)->first();
            if ($validateUser) return response()->json(['message' => 'El usuario ya se encuentra registrado.'], 202);

            $password = Hash::make($request->c_password);

            $user = new User();
            $user->c_name = $request->c_name;
            $user->c_email = $request->c_email;
            $user->c_password = $password;
            $user->fk_role = $request->fk_role;
            $user->save();

            return response()->json(['data' => $user, 'message' => 'Usuario creado.'], 201);
        } else {
            return response()->json(['message' => 'No tienes acceso.'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        if ($this->jwtService->haveAccess($request)) {
            $user = User::find($id);
            if (!$user) return response()->json(['data' => null, 'message' => 'El usuario no existe.'], 404);

            $user->load('role');
            return response()->json(['data' => $user]);
        } else {
            return response()->json(['message' => 'No tienes acceso.'], 401);
        }
    }

    /**
     * Get user by email
     */
    public function getUserByEmail(string $email)
    {
        $user = User::with('role')->where('c_email', $email)->first();
        if (!$user) return response()->json(['data' => null, 'message' => 'El usuario no existe.'], 404);
        return response()->json(['data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($this->jwtService->haveAccess($request)) {
            $validator = Validator::make($request->all(), [
                'c_name' => 'string|min:5|max:255',
                'c_email' => 'string|email',
                'c_password' => 'string|min:7',
                'fk_role' => 'numeric|exists:roles,pk_role'
            ], [
                'c_name.string' => 'El nombre debe ser un texto.',
                'c_name.min' => 'El nombre debe tener mínimo 5 carácteres.',
                'c_name.max' => 'El nombre debe tener máximo 255 carácteres.',
                'c_email.string' => 'El correo debe ser un texto.',
                'c_email.email' => 'El correo debe tener un formato de correo electrónico valido.',
                'c_password.string' => 'La contraseña debe ser un texto.',
                'c_password.min' => 'La contraseña debe tener mínimo 7 carácteres.',
                'fk_role.numeric' => 'El rol debe ser un número.',
                'fk_role.exists' => 'El rol no existe.'
            ]);

            if ($validator->fails()) return response()->json(['message' => $validator->errors()], 400);

            $user = User::find($id);
            if (!$user) return response()->json(['data' => null, 'message' => 'El usuario no existe.'], 404);

            if ($request->fk_role) {
                $role = Role::find($request->fk_role);
                if (!$role) return response()->json(['data' => null, 'message' => 'El rol no existe.'], 404);
            }

            $user->c_name = $request->c_name ?? $user->c_name;
            $user->c_email = $request->c_email ?? $user->c_email;
            $user->c_password = Hash::make($request->c_password) ?? $user->c_password;
            $user->fk_role = $request->fk_role ?? $user->fk_role;
            $user->update();

            return response()->json(['data' => $user]);
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
            $user = User::find($id);
            $user->load('projects', 'tasksCreator', 'tasksResponsible');
            if (!$user) return response()->json(['data' => null, 'message' => 'El usuario no existe.'], 404);

            if (count($user->projects) > 0) return response()->json(['data' => null, 'message' => 'El usuario no se puede eliminar ya que tiene proyectos asignados.'], 403);

            if (count($user->tasksCreator) > 0) return response()->json(['data' => null, 'message' => 'El usuario no se puede eliminar ya que tiene tareas creadas.'], 403);

            if (count($user->tasksResponsible) > 0) return response()->json(['data' => null, 'message' => 'El usuario no se puede eliminar ya que tiene tareas a cargo.'], 403);

            $user->delete();
            return response()->json(['data' => $user, 'message' => 'Usuario eliminado.']);
        } else {
            return response()->json(['message' => 'No tienes acceso.'], 401);
        }
    }
}
