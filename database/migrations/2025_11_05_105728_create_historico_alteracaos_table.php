<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nome da tabela conforme documentação
        Schema::create('historico_alteracoes', function (Blueprint $table) {
            $table->id();
            
            // Chave estrangeira para o usuário que fez a ação
            $table->foreignId('responsavel_id')->nullable()->constrained('usuarios'); //
            
            // Relacionamento Polimórfico
            $table->string('historicoable_type'); // Ex: 'App\Models\Certificado'
            $table->unsignedBigInteger('historicoable_id'); // Ex: 1
            
            // Dados da Alteração
            $table->string('acao'); // Ex: 'CREATED', 'UPDATED', 'DELETED'
            $table->json('alteracao_antes')->nullable(); // JSON com estado anterior
            $table->json('alteracao_depois')->nullable(); // JSON com estado posterior
            $table->text('observacao')->nullable(); //
            
            $table->timestamp('data_alteracao')->useCurrent(); //
            
            // Index para performance
            $table->index(['historicoable_type', 'historicoable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_alteracoes');
    }
};