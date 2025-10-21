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
        Schema::create('certificados', function (Blueprint $table) {
            $table->id(); // [cite: 32]
            
            // Chave estrangeira para Usuario [cite: 32, 35]
            $table->foreignId('usuario_id')
                  ->constrained('usuarios')
                  ->onDelete('cascade'); // Se o usuário for deletado, seus certificados também são.

            // Usando strings para os Enums [cite: 6, 29, 30]
            $table->string('categoria'); // [cite: 32]
            $table->string('status')->default('ENTREGUE'); // [cite: 33, 6]

            $table->integer('carga_horaria_solicitada'); // [cite: 33]
            $table->integer('horas_validadas')->default(0); // [cite: 33]
            $table->string('nome_certificado'); // [cite: 33]
            $table->string('instituicao'); // [cite: 33]
            $table->date('data_emissao'); // [cite: 33]
            $table->text('observacao')->nullable(); // [cite: 33]
            $table->string('arquivo'); // [cite: 33, 20] (Armazena o caminho do arquivo)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};