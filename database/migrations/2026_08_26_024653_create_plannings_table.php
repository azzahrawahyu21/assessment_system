<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plannings', function (Blueprint $table) {
            $table->id('id_plan');
            $table->string('no_letter')->unique();
            $table->string('name');
            $table->date('date');
            $table->enum('period', ['ganjil', 'genap']);
            $table->unsignedBigInteger('planning_type_id');
            $table->text('objective')->nullable();
            $table->text('target')->nullable();
            $table->text('success_indicator')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('funding_source')->nullable();
            $table->text('budget_note')->nullable();
            $table->string('document_path')->nullable(); // path PDF proposal
            $table->enum('status', ['draft', 'submitted', 'revision', 'approved'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable(); // admin_prodi / upa
            $table->unsignedBigInteger('department_id')->nullable();
            $table->timestamps();

            $table->foreign('planning_type_id')->references('id_type')->on('planning_types')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('department_id')->references('id_department')->on('departments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plannings');
    }
};