<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_judgments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('cobit_id');          // id_cobit
            $table->unsignedTinyInteger('achieved_level');   // 0-5
            $table->text('notes')->nullable();
            $table->string('evidence_path')->nullable();     // path file evidence
            $table->timestamps();

            $table->foreign('assessment_id')
                  ->references('id')
                  ->on('assessments')
                  ->onDelete('cascade');

            $table->foreign('cobit_id')
                  ->references('id_cobit')
                  ->on('cobits')
                  ->onDelete('cascade');

            $table->unique(['assessment_id', 'cobit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_judgments');
    }
};