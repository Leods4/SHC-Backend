<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nome', 'ADMINISTRADOR')->first();
        
        if ($adminRole) {
            Usuario::firstOrCreate(
                ['cpf' => '00000000000'], // CPF único para o admin
                [
                    'nome' => 'Admin do Sistema',
                    'email' => 'admin@sistema.com',
                    'matricula' => 'ADMIN',
                    'data_nascimento' => '2000-01-01', // Senha padrão será 20000101
                    'password' => Hash::make('20000101'),
                    'role_id' => $adminRole->id,
                    // curso_id e fase são nullable
                ]
            );
        }
    }
}