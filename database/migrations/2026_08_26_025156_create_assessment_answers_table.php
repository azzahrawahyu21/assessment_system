<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('respondent_id');
            $table->unsignedBigInteger('cobit_statement_id');
            $table->boolean('answer'); // true = Ya, false = Tidak
            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('respondent_id')->references('id')->on('assessment_respondents')->onDelete('cascade');
            $table->foreign('cobit_statement_id')->references('id_statement')->on('cobit_statements')->onDelete('cascade');
            $table->unique(['respondent_id', 'cobit_statement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_answers');
    }
};