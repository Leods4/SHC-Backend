<?php

use App\Models\Certificado; //
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            
            // Chaves Estrangeiras
            $table->foreignId('aluno_id')->constrained('usuarios'); //
            $table->foreignId('categoria_id')->constrained('categorias'); //
            
            // Dados do Certificado
            $table->string('nome_certificado'); //
            $table->string('instituicao'); //
            $table->date('data_emissao'); //
            $table->integer('carga_horaria_solicitada'); //
            
            // Controle e Avaliação
            $table->string('status')->default(Certificado::STATUS_ENTREGUE); //
            $table->integer('horas_validadas')->nullable(); //
            $table->string('observacao')->nullable(); //
            
            // Arquivo (Caminho privado)
            $table->string('arquivo'); //
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};