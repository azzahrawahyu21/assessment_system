<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_judgments', function (Blueprint $table) {
            $table->unsignedTinyInteger('recommended_level')
                ->nullable()
                ->after('cobit_id');

            $table->json('activities')
                ->nullable()
                ->after('recommended_level');

            $table->json('evidence_paths')
                ->nullable()
                ->after('activities');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_judgments', function (Blueprint $table) {
            $table->dropColumn([
                'recommended_level',
                'activities',
                'evidence_paths',
            ]);
        });
    }
};