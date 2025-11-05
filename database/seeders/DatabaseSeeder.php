<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chama os seeders na ordem correta
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            // (Opcional: Seeders de Categoria e Curso podem vir aqui)
        ]);
    }
}