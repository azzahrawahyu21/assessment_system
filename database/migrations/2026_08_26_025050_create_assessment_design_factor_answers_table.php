<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_df_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('design_factor_id');
            $table->string('answer_value')->nullable();      // rating / single choice
            $table->json('answer_values')->nullable();       // multiple choice
            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('design_factor_id')->references('id_df')->on('design_factors')->onDelete('cascade');
            $table->unique(['assessment_id', 'design_factor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_df_answers');
    }
};