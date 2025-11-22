<?php

namespace Database\Seeders;

use App\Enums\TipoUsuario;
use App\Models\Configuracao;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Cursos Básicos
        $cursoAds = Curso::create([
            'nome' => 'Análise e Desenvolvimento de Sistemas',
            'horas_necessarias' => 180 // Exemplo
        ]);

        $cursoDireito = Curso::create([
            'nome' => 'Direito',
            'horas_necessarias' => 300
        ]);

        // 2. Criar Administrador (Para primeiro acesso)
        User::create([
            'nome' => 'Administrador do Sistema',
            'email' => 'admin@fmp.edu.br',
            'cpf' => '000.000.000-00',
            'password' => Hash::make('admin123'), // Senha inicial
            'tipo' => TipoUsuario::ADMINISTRADOR,
        ]);

        // 3. Criar uma Secretaria
        User::create([
            'nome' => 'Secretaria Acadêmica',
            'email' => 'secretaria@fmp.edu.br',
            'cpf' => '111.111.111-11',
            'password' => Hash::make('sec123'),
            'tipo' => TipoUsuario::SECRETARIA,
        ]);

        // 4. Criar um Coordenador (ADS)
        User::create([
            'nome' => 'Coordenador ADS',
            'email' => 'coord.ads@fmp.edu.br',
            'cpf' => '222.222.222-22',
            'password' => Hash::make('coord123'),
            'tipo' => TipoUsuario::COORDENADOR,
            'curso_id' => $cursoAds->id,
        ]);

        // 5. Configurações Iniciais
        Configuracao::create(['chave' => 'modo_manutencao', 'valor' => 'false']);
        Configuracao::create(['chave' => 'total_horas_padrao', 'valor' => '200']);

        // 6. Categorias Iniciais
        $categorias = [
            'Curso Extracurricular',
            'Participação em Eventos/Palestras',
            'Iniciação Científica',
            'Estágio Não Obrigatório',
            'Monitoria',
            'Voluntariado'
        ];

        foreach ($categorias as $cat) {
            \App\Models\Categoria::create(['nome' => $cat]);
        }
    }
}
