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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('active')->nullable()->default(1)->comment('0: Desativado, 1: Ativado');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->date('deadline')->nullable();
            $table->string('status')->nullable();
            $table->integer('priority')->nullable()->comment('0: Baixa, 1: Média, 2: Alta');
            $table->string('tag')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
