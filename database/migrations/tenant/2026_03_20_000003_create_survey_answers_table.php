<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained('survey_responses')->cascadeOnDelete();
            $table->unsignedSmallInteger('pergunta_numero');
            $table->unsignedTinyInteger('resposta'); // 0-4
            $table->timestamps();

            $table->unique(['survey_response_id', 'pergunta_numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
    }
};
