<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('response_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained('survey_responses')->cascadeOnDelete();
            $table->unsignedSmallInteger('questao_numero');
            $table->string('dimensao', 50);
            $table->unsignedTinyInteger('valor'); // 0-4
            $table->timestamps();

            $table->unique(['survey_response_id', 'questao_numero']);
            $table->index(['survey_response_id', 'dimensao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('response_answers');
    }
};
