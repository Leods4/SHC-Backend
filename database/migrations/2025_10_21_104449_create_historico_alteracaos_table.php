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
        Schema::create('historico_alteracoes', function (Blueprint $table) {
            $table->id(); // [cite: 34]

            // Chave estrangeira para o usuário responsável [cite: 34, 35]
            $table->foreignId('responsavel_id')
                  ->nullable()
                  ->constrained('usuarios')
                  ->onDelete('set null'); // Mantém o histórico mesmo se o responsável for deletado

            // Campos polimórficos [cite: 34]
            $table->morphs('historicoable'); // Cria 'historicoable_type' e 'historicoable_id'

            $table->json('alteracao'); // [cite: 34]
            $table->text('observacao')->nullable(); // [cite: 34]
            
            // O campo 'data_alteracao' [cite: 34] é coberto pelo 'created_at' do timestamps()
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_alteracoes');
    }
};