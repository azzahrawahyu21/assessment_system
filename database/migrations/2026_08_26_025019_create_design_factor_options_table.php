<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_factor_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('design_factor_id');
            $table->string('label');
            $table->string('value')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('design_factor_id')->references('id_df')->on('design_factors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_factor_options');
    }
};