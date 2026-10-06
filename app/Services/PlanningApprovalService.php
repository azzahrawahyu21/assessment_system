<?php

namespace App\Services;

use App\Models\Planning;
use App\Models\PlanningApproval;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlanningApprovalService
{
    /**
     * admin_prodi: kajur → wadir → direktur → keuangan
     * upa:         wadir → direktur → keuangan
     */
    public function chainFor(Planning $planning): array
    {
        $creatorRole = $planning->creator?->role?->name
            ?? User::with('role')->find($planning->created_by)?->role?->name;

        if ($creatorRole === 'upa') {
            return ['wadir', 'direktur', 'keuangan'];
        }

        return ['kajur', 'wadir', 'direktur', 'keuangan'];
    }

    public function start(Planning $planning): void
    {
        PlanningApproval::where('planning_id', $planning->id_plan)
            ->whereIn('status', ['pending', 'revision'])
            ->delete();

        $chain     = $this->chainFor($planning);
        $firstRole = $chain[0];
        $approver  = $this->findApprover($firstRole, $planning->department_id);

        if (! $approver) {
            throw new \RuntimeException("Tidak ditemukan user dengan role {$firstRole}.");
        }

        PlanningApproval::create([
            'planning_id' => $planning->id_plan,
            'approver_id' => $approver->id,
            'status'      => 'pending',
        ]);

        $planning->update(['status' => 'submitted']);
    }

    public function approve(PlanningApproval $approval, ?string $note = null): void
    {
        $approval->update([
            'status'      => 'approved',
            'note'        => $note,
            'approved_at' => now(),
        ]);

        $planning = $approval->planning()->with(['creator.role'])->first();
        $chain    = $this->chainFor($planning);

        // Role approver saat ini (bukan sequence)
        $currentRole = $approval->approver?->role?->name
            ?? $approval->loadMissing('approver.role')->approver?->role?->name;

        $currentIndex = array_search($currentRole, $chain, true);

        // Role tidak ada di rantai → biarkan submitted
        if ($currentIndex === false) {
            $planning->update(['status' => 'submitted']);
            return;
        }

        $nextIndex = $currentIndex + 1;

        // Masih ada level berikutnya
        if (isset($chain[$nextIndex])) {
            $nextRole = $chain[$nextIndex];
            $approver = $this->findApprover($nextRole, $planning->department_id);

            if (! $approver) {
                throw new \RuntimeException("Tidak ditemukan user dengan role {$nextRole}.");
            }

            // Jangan buat pending ganda untuk orang yang sama
            $exists = PlanningApproval::where('planning_id', $planning->id_plan)
                ->where('approver_id', $approver->id)
                ->where('status', 'pending')
                ->exists();

            if (! $exists) {
                PlanningApproval::create([
                    'planning_id' => $planning->id_plan,
                    'approver_id' => $approver->id,
                    'status'      => 'pending',
                ]);
            }

            $planning->update(['status' => 'submitted']);
            return;
        }

        // Sudah level terakhir (harusnya keuangan lewat processFinance)
        $planning->update(['status' => 'approved']);
    }

    public function revise(PlanningApproval $approval, string $note): void
    {
        $approval->update([
            'status'      => 'revision',
            'note'        => $note,
            'approved_at' => now(),
        ]);

        PlanningApproval::where('planning_id', $approval->planning_id)
            ->where('status', 'pending')
            ->where('id', '!=', $approval->id)
            ->update([
                'status'      => 'revision',
                'note'        => 'Dibatalkan karena revisi di level sebelumnya',
                'approved_at' => now(),
            ]);

        $approval->planning->update(['status' => 'revision']);
    }

    // public function processFinance(PlanningApproval $approval, ?string $note = null): void
    // {
    //     $approval->update([
    //         'status'      => 'approved',
    //         'note'        => $note ?? 'Anggaran diproses',
    //         'approved_at' => now(),
    //     ]);

    //     $approval->planning->update(['status' => 'approved']);
    // }
    public function processFinance(PlanningApproval $approval, ?string $note = null): void
    {
        $approval->update([
            'status'      => 'approved',
            'note'        => $note ?? 'Anggaran diproses',
            'approved_at' => now(),
        ]);

        $planning = $approval->planning;
        $planning->update(['status' => 'approved']);

        // Generate TTD otomatis
        if (! $planning->digitalSignature()->exists()) {
            try {
                app(DigitalSignatureService::class)
                    ->generate($planning->fresh(['creator', 'department', 'approvals.approver.role']), Auth::user());
            } catch (\Throwable $e) {
                Log::warning('Auto TTD gagal: ' . $e->getMessage());
            }
        }
    }

    protected function findApprover(string $roleName, $departmentId = null): ?User
    {
        $query = User::whereHas('role', fn ($q) => $q->where('name', $roleName));

        if ($roleName === 'kajur' && $departmentId) {
            // Coba cari yang sesuai department dulu
            $user = (clone $query)->where('department_id', $departmentId)->first();
            
            if ($user) {
                return $user;
            }

            // Fallback: ambil kajur mana saja (opsional)
            Log::warning("Kajur department {$departmentId} tidak ditemukan, menggunakan fallback");
        }

        return $query->first();
    }
}