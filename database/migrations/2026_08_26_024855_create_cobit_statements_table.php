<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobit_statements', function (Blueprint $table) {
            $table->id('id_statement');
            $table->unsignedBigInteger('cobit_id');
            $table->unsignedTinyInteger('level'); // 0-5
            $table->text('statement');
            $table->timestamps();

            $table->foreign('cobit_id')->references('id_cobit')->on('cobits')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobit_statements');
    }
};