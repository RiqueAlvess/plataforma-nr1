<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('csv_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nome_arquivo');
            $table->string('status')->default('pendente'); // pendente, processando, concluido, erro
            $table->integer('total_registros')->default(0);
            $table->integer('registros_importados')->default(0);
            $table->integer('registros_com_erro')->default(0);
            $table->text('mensagem_erro')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('csv_imports');
    }
};
