<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usamos firstOrCreate para evitar duplicatas se o seeder for rodado múltiplas vezes
        Role::firstOrCreate(['nome' => 'ALUNO'], ['descricao' => 'Usuário discente. Pode submeter certificados.']);
        Role::firstOrCreate(['nome' => 'COORDENADOR'], ['descricao' => 'Coord. de curso. Pode avaliar certificados do seu curso.']);
        Role::firstOrCreate(['nome' => 'SECRETARIA'], ['descricao' => 'Perfil administrativo. Gerencia usuários e certificados.']);
        Role::firstOrCreate(['nome' => 'ADMINISTRADOR'], ['descricao' => 'Super-usuário. Acesso total ao sistema.']);
    }
}