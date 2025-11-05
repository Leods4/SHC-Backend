<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCategoriaRequest;
use App\Http\Requests\Api\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\Response;

class CategoriaController extends Controller
{
    public function __construct()
    {
        // Aplica a CategoriaPolicy automaticamente [cite: 111]
        $this->authorizeResource(Categoria::class, 'categoria');
    }

    public function index()
    {
        // (GET /api/categorias) [cite: 111]
        return Categoria::all();
    }

    public function store(StoreCategoriaRequest $request)
    {
        // (POST /api/categorias) [cite: 111]
        $categoria = Categoria::create($request->validated());
        return response()->json($categoria, Response::HTTP_CREATED);
    }

    public function show(Categoria $categoria)
    {
        return response()->json($categoria);
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        // (PUT /api/categorias/{id}) [cite: 112]
        $categoria->update($request->validated());
        return response()->json($categoria);
    }

    public function destroy(Categoria $categoria)
    {
        // (DELETE /api/categorias/{id}) [cite: 112]
        $categoria->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}