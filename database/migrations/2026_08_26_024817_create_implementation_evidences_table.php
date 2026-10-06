<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('implementation_evidences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('implementation_id');
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable(); // image, document, pdf, dll
            $table->timestamps();

            $table->foreign('implementation_id')->references('id')->on('implementations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('implementation_evidences');
    }
};