<?php

namespace App\Services;

use App\Models\DigitalSignature;
use App\Models\Planning;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DigitalSignatureService
{
    public function generate(Planning $planning, User $user): DigitalSignature
    {
        if ($planning->digitalSignature) {
            return $planning->digitalSignature;
        }

        $approvals = $planning->approvals()
            ->with('approver.role')
            ->where('status', 'approved')
            ->get();

        $wadir = $approvals->first(fn ($a) =>
            strtolower($a->approver->role->name ?? '') === 'wadir'
        );

        $direktur = $approvals->first(fn ($a) =>
            strtolower($a->approver->role->name ?? '') === 'direktur'
        );

        $token = \Illuminate\Support\Str::random(48);
        $year  = now()->format('Y');
        $month = strtoupper(now()->format('M')); // atau pakai Romawi jika mau
        $seq   = DigitalSignature::whereYear('created_at', now()->year)->count() + 1;
        $documentNumber = sprintf('PNM/PERENC/%s/%s/%03d', $year, $month, $seq);

        return DigitalSignature::create([
            'planning_id'        => $planning->id_plan,
            'document_number'    => $documentNumber,
            'document_date'      => now()->toDateString(),
            'token'              => $token,
            'hash'               => hash('sha256', $planning->id_plan . '|' . $token . '|' . now()->timestamp),
            'director_name'      => $direktur?->approver?->name ?? 'Direktur',
            'director_signed_at' => $direktur?->approved_at ?? now(),
            'wadir_name'         => $wadir?->approver?->name ?? 'Wakil Direktur 1',
            'wadir_signed_at'    => $wadir?->approved_at ?? now(),
            'status'             => 'signed',
            'generated_by'       => $user->id,
        ]);
    }

    public function generateDocumentNumber(): string
    {
        $year  = now()->year;
        $month = now()->month;
        $roman = $this->toRoman($month);

        $last = DigitalSignature::whereYear('document_date', $year)
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($last) {
            $parts = explode('/', $last->document_number);
            $sequence = (int) end($parts) + 1;
        }

        return sprintf('PNM/PERENC/%d/%s/%03d', $year, $roman, $sequence);
    }

    protected function toRoman(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $romans[$month] ?? 'I';
    }

    protected function generateHash(Planning $planning, string $documentNumber): string
    {
        $payload = implode('|', [
            $planning->id_plan,
            $documentNumber,
            $planning->title ?? '',
            $planning->created_by,
            now()->toDateString(),
        ]);

        return hash('sha256', $payload);
    }
}