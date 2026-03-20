<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('survey_invite_id')->constrained('survey_invites')->cascadeOnDelete();
            $table->enum('genero', ['masculino', 'feminino', 'outro', 'nao_informado'])->nullable();
            $table->string('faixa_etaria')->nullable();
            $table->boolean('consentimento_aceito')->default(false);
            $table->timestamp('respondido_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
