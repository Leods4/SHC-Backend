<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id(); // [cite: 31]
            $table->string('nome'); // [cite: 31]
            $table->string('email')->unique(); // [cite: 31]
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // [cite: 31] (Nome padrão do Laravel para 'senha')
            $table->string('matricula')->unique(); // [cite: 31]
            $table->date('data_nascimento'); // [cite: 31]
            
            // Usando string para o Enum [cite: 5, 29]
            $table->string('tipo'); // [cite: 31] 
            
            $table->json('dados_adicionais')->nullable(); // [cite: 31]
            $table->integer('fase')->nullable(); // [cite: 31]
            
            // Chave estrangeira para Curso [cite: 31, 34]
            $table->foreignId('curso_id')
                  ->nullable() // [cite: 31]
                  ->constrained('cursos')
                  ->onDelete('set null'); // Se um curso for deletado, o usuário permanece, mas sem curso.

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};