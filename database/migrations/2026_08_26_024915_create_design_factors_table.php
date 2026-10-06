<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_factors', function (Blueprint $table) {
            $table->id('id_df');
            $table->string('code')->unique(); // DF1 - DF11
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['rating', 'single_choice', 'multiple_choice']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_factors');
    }
};