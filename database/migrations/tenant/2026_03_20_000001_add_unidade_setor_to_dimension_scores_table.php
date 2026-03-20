<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dimension_scores', function (Blueprint $table) {
            $table->unsignedBigInteger('unidade_id')->nullable()->after('faixa_etaria');
            $table->unsignedBigInteger('setor_id')->nullable()->after('unidade_id');

            $table->index(['campaign_id', 'unidade_id']);
            $table->index(['campaign_id', 'setor_id']);
        });
    }

    public function down(): void
    {
        Schema::table('dimension_scores', function (Blueprint $table) {
            $table->dropIndex(['campaign_id', 'unidade_id']);
            $table->dropIndex(['campaign_id', 'setor_id']);
            $table->dropColumn(['unidade_id', 'setor_id']);
        });
    }
};
