<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCursoRequest;
use App\Http\Requests\Api\UpdateCursoRequest;
use App\Models\Curso;
use Illuminate\Http\Response;

class CursoController extends Controller
{
    public function __construct()
    {
        // Aplica a CursoPolicy automaticamente [cite: 109]
        $this->authorizeResource(Curso::class, 'curso');
    }

    public function index()
    {
        // (GET /api/cursos) [cite: 109]
        return Curso::all();
    }

    public function store(StoreCursoRequest $request)
    {
        // (POST /api/cursos) [cite: 109]
        $curso = Curso::create($request->validated());
        return response()->json($curso, Response::HTTP_CREATED);
    }

    public function show(Curso $curso)
    {
        // (GET /api/cursos/{id}) [cite: 109]
        return response()->json($curso);
    }

    public function update(UpdateCursoRequest $request, Curso $curso)
    {
        // (PUT /api/cursos/{id}) [cite: 110]
        $curso->update($request->validated());
        return response()->json($curso);
    }

    public function destroy(Curso $curso)
    {
        // (DELETE /api/cursos/{id}) [cite: 110]
        $curso->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}