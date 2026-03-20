<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('email_hash', 64);
            $table->string('token', 64)->unique();
            $table->enum('envio_status', ['pendente', 'enviado', 'erro'])->default('pendente');
            $table->enum('resposta_status', ['pendente', 'respondido'])->default('pendente');
            $table->timestamp('enviado_em')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'email_hash']);
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_invites');
    }
};
