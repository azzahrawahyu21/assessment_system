<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('cobit_id');
            $table->unsignedTinyInteger('level'); // 0-5
            $table->decimal('percentage', 5, 2); // rata-rata % semua responden
            $table->enum('rating', ['N', 'P', 'L', 'F'])->nullable(); // Not / Partially / Largely / Fully Achieved
            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('cobit_id')->references('id_cobit')->on('cobits')->onDelete('cascade');
            $table->unique(['assessment_id', 'cobit_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};