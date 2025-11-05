<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // O nome da tabela é 'usuarios' [cite: 50]
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // [cite: 50]
            $table->string('email')->unique(); // [cite: 50]
            $table->string('cpf')->unique(); // [cite: 50]
            $table->string('matricula')->unique(); // [cite: 50]
            $table->date('data_nascimento'); // [cite: 50]
            $table->string('password'); // [cite: 50]
            $table->string('telefone')->nullable(); // [cite: 50]
            
            // Chaves Estrangeiras [cite: 50]
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('curso_id')->nullable()->constrained('cursos');
            
            $table->integer('fase')->nullable(); // [cite: 50]
            
            // Campos padrão do Laravel (adaptados)
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Para SoftDelete [cite: 50, 83]
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};