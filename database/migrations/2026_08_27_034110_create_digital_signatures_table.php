<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_id')->constrained()->cascadeOnDelete();
            $table->string('document_number')->unique();
            $table->date('document_date');
            $table->string('token', 64)->unique();
            $table->string('hash', 64);
            $table->string('director_name')->nullable();
            $table->string('wadir_name')->nullable();
            $table->timestamp('director_signed_at')->nullable();
            $table->timestamp('wadir_signed_at')->nullable();
            $table->enum('status', ['signed', 'revoked'])->default('signed');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('planning_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};