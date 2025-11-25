<?php

namespace Database\Seeders;

use App\Enums\TipoUsuario;
use App\Models\Configuracao;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Cursos Básicos
        $cursoAds = Curso::create([
            'nome' => 'Análise e Desenvolvimento de Sistemas',
            'horas_necessarias' => 180
        ]);

        $cursoDireito = Curso::create([
            'nome' => 'Direito',
            'horas_necessarias' => 300
        ]);

        // 2. Admin
        User::create([
            'nome' => 'Administrador do Sistema',
            'email' => 'admin@fmp.edu.br',
            'cpf' => '000.000.000-00',
            'data_nascimento' => '1990-01-01',
            'password' => 'admin123',
            'tipo' => TipoUsuario::ADMINISTRADOR,
        ]);

        // 3. Secretaria
        User::create([
            'nome' => 'Secretaria Acadêmica',
            'email' => 'secretaria@fmp.edu.br',
            'cpf' => '111.111.111-11',
            'data_nascimento' => '1992-05-10',
            'password' => 'sec123',
            'tipo' => TipoUsuario::SECRETARIA,
        ]);

        // 4. Coordenador (ADS)
        User::create([
            'nome' => 'Coordenador ADS',
            'email' => 'coord.ads@fmp.edu.br',
            'cpf' => '222.222.222-22',
            'data_nascimento' => '1985-08-20',
            'password' => 'coord123',
            'tipo' => TipoUsuario::COORDENADOR,
            'curso_id' => $cursoAds->id,
        ]);

        // 5. Aluno
        User::create([
            'nome' => 'Aluno Teste',
            'email' => 'aluno@fmp.edu.br',
            'cpf' => '333.333.333-33',
            'data_nascimento' => '2003-04-15',
            'matricula' => '20250001',
            'password' => 'aluno123',
            'tipo' => TipoUsuario::ALUNO,
            'avatar_url' => 'https://ui-avatars.com/api/?name=Aluno+Teste',
            'curso_id' => $cursoAds->id,
            'fase' => 3,
        ]);

        // 6. Configurações iniciais
        Configuracao::create(['chave' => 'modo_manutencao', 'valor' => 'false']);
        Configuracao::create(['chave' => 'total_horas_padrao', 'valor' => '200']);

        // 7. Categorias
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
