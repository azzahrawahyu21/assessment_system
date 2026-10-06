<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('department_id')->nullable(); // null jika is_all_department = true
            $table->boolean('is_all_department')->default(false);
            $table->enum('status', ['draft', 'design_factor_filled', 'in_progress', 'closed'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable(); // assessor
            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id_department')->on('departments')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};