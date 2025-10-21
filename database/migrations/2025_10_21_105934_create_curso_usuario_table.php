<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Esta tabela serve como pivot para a relação N:N (Muitos-para-Muitos)
     * [cite_start]entre Usuários (Coordenadores) e Cursos, conforme o diagrama[cite: 35].
     */
    public function up(): void
    {
        Schema::create('curso_usuario', function (Blueprint $table) {
            
            // Chave estrangeira para o curso
            $table->foreignId('curso_id')
                  ->constrained('cursos')
                  ->onDelete('cascade'); // Se o curso for deletado, a associação é removida.

            // Chave estrangeira para o usuário (coordenador)
            $table->foreignId('usuario_id')
                  ->constrained('usuarios')
                  ->onDelete('cascade'); // Se o usuário for deletado, a associação é removida.

            // Define uma chave primária composta.
            // Isso impede que o mesmo usuário seja coordenador do mesmo curso múltiplas vezes.
            $table->primary(['curso_id', 'usuario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_usuario');
    }
};