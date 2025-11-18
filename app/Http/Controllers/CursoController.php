<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Http\Resources\CursoResource;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    /**
     * Lista os cursos para popular o <select> no front-end.
     * Usado no cadastro de alunos (Secretaria) e perfil.
     */
    public function index()
    {
        $cursos = Curso::orderBy('nome')->get();
        return CursoResource::collection($cursos);
    }
}
