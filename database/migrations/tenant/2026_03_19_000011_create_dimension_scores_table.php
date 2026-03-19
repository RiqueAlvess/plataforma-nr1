<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Star schema fact table for analytical queries
        Schema::create('dimension_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained('survey_responses')->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('dimensao', 50);
            $table->decimal('score', 4, 2); // 0.00 - 4.00
            $table->boolean('dimensao_negativa')->default(false);
            $table->decimal('score_risco', 4, 2); // normalized risk score 0.00 - 1.00
            $table->unsignedTinyInteger('probabilidade'); // 1-4
            $table->unsignedTinyInteger('severidade');    // 1-3
            $table->unsignedTinyInteger('nr');            // NR = probabilidade x severidade
            $table->string('classificacao_risco', 20);   // aceitavel, moderado, importante, critico
            // Demographic dimensions for analytics
            $table->enum('genero', ['masculino', 'feminino', 'outro', 'nao_informado'])->nullable();
            $table->string('faixa_etaria')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'dimensao']);
            $table->index(['campaign_id', 'classificacao_risco']);
            $table->index(['campaign_id', 'genero']);
            $table->index(['campaign_id', 'faixa_etaria']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dimension_scores');
    }
};
