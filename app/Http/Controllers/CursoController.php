<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Http\Requests\StoreCursoRequest;
use App\Http\Requests\UpdateCursoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CursoController extends Controller
{
    /**
     * Lista todos os cursos.
     * Rota: GET /api/cursos
     */
    public function index()
    {
        Gate::authorize('viewAny', Curso::class);

        $cursos = Curso::paginate(15);
        return response()->json($cursos);
    }

    /**
     * Cria um novo curso.
     * Rota: POST /api/cursos
     */
    public function store(StoreCursoRequest $request)
    {
        Gate::authorize('create', Curso::class);

        $curso = Curso::create($request->validated());

        return response()->json($curso, 201);
    }

    /**
     * Exibe um curso específico.
     * Rota: GET /api/cursos/{curso}
     */
    public function show(Curso $curso)
    {
        Gate::authorize('view', $curso);
        
        return response()->json($curso);
    }

    /**
     * Atualiza um curso específico.
     * Rota: PUT /api/cursos/{curso}
     */
    public function update(UpdateCursoRequest $request, Curso $curso)
    {
        Gate::authorize('update', $curso);

        $curso->update($request->validated());

        return response()->json($curso);
    }

    /**
     * Deleta um curso.
     * Rota: DELETE /api/cursos/{curso}
     */
    public function destroy(Curso $curso)
    {
        Gate::authorize('delete', $curso);

        $curso->delete();

        return response()->json(null, 204);
    }
}
