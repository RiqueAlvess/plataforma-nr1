<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leader_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unidade_id')->constrained('unidades')->cascadeOnDelete();
            $table->foreignId('setor_id')->nullable()->constrained('setores')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'unidade_id', 'setor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leader_permissions');
    }
};
