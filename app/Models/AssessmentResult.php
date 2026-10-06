<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentResult extends Model
{
    protected $fillable = [
        'assessment_id',
        'cobit_id',
        'level',
        'percentage',
        'rating',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function cobit(): BelongsTo
    {
        return $this->belongsTo(Cobit::class, 'cobit_id', 'id_cobit');
    }

    public static function calculateRating(float $percentage): string
    {
        if ($percentage >= 85) {
            return 'F'; // Fully Achieved
        } elseif ($percentage >= 50) {
            return 'L'; // Largely Achieved
        } elseif ($percentage >= 15) {
            return 'P'; // Partially Achieved
        }

        return 'N'; // Not Achieved
    }
}