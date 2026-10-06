<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Models\Assessment;
use App\Models\AssessmentRespondent;
use App\Models\AssessmentJudgment;
use App\Models\Implementation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::with(['role', 'department'])->findOrFail(Auth::id());
        $role = $user->role?->name;

        $data = [
            'user'              => $user,
            'role'              => $role,
            'stats'             => [],
            'recentPlannings'   => collect(),
            'recentActivities'  => collect(),
            'myPlannings'       => collect(),
            'openAssessments'   => collect(),
            'pendingApprovals'  => collect(),
            'recentProcessed'   => collect(),
            'latestResults'     => collect(),
            'recentAssessments' => collect(),
        ];

        match ($role) {
            'administrator', 'Admin' =>
                $data = array_merge($data, $this->adminDashboard()),

            'dosen', 'Dosen' =>
                $data = array_merge($data, $this->dosenDashboard($user)),

            'admin_prodi', 'prodi', 'Prodi', 'upa', 'UPA', 'kaprodi', 'Kaprodi' =>
                $data = array_merge($data, $this->adminProdiDashboard($user)),
                    
            'assessor', 'Assessor' =>
                $data = array_merge($data, $this->assessorDashboard($user)),

            'kajur', 'wadir', 'direktur', 'keuangan' =>
                $data = array_merge($data, $this->approverDashboard($user)),
            // ... role lain jika ada
            default => null,
        };

        return view('general.dashboard', $data);
    }

    protected function adminDashboard(): array
    {
        return [
            'stats' => [
                'total_users'           => User::count(),
                'total_departments'     => \App\Models\Department::count(),
                'total_planning'        => Planning::count(),
                'total_implementations' => Implementation::count(),
                'total_assessments'     => Assessment::count(),
                'approved'              => Planning::where('status', 'Approved')->count(),
                'pending'               => Planning::where('status', 'Submitted')->count(),
                'draft'                 => Planning::where('status', 'Draft')->count(),
                'revision'              => Planning::where('status', 'Revision')->count(),
                'rejected'              => Planning::where('status', 'Rejected')->count(),
            ],
            'recentPlannings' => Planning::with('department')->latest()->take(5)->get(),
        ];
    }

    /**
     * Dashboard khusus Assessor
     */
    protected function assessorDashboard(User $user): array
    {
        $base = Assessment::where('created_by', $user->id);

        $stats = [
            'total_assessments' => (clone $base)->count(),
            'draft'             => (clone $base)->where('status', 'draft')->count(),
            'in_progress'       => (clone $base)->whereIn('status', [
                                        'design_factor_filled',
                                        'in_progress',
                                    ])->count(),
            'completed'         => (clone $base)->where('status', 'closed')->count(),

            // Tahap proses
            'df_done'           => (clone $base)->whereIn('status', [
                                        'design_factor_filled',
                                        'in_progress',
                                        'closed',
                                    ])->count(),

            'objectives_done'   => (clone $base)
                                    ->whereHas('assessmentObjectives')
                                    ->count(),

            'capability_done'   => (clone $base)
                                    ->whereHas('respondents', function ($q) {
                                        $q->where('status', 'completed');
                                    })
                                    ->count(),

            'gap_done'          => (clone $base)
                                    ->whereHas('judgments')
                                    ->count(),

            // Prioritas & Roadmap dianggap selesai jika sudah ada judgment
            // atau status sudah closed
            'priority_done'     => (clone $base)
                                    ->whereHas('judgments')
                                    ->count(),

            'roadmap_done'      => (clone $base)
                                    ->where('status', 'closed')
                                    ->count(),
        ];

        $recentAssessments = Assessment::with('department')
            ->where('created_by', $user->id)
            ->latest()
            ->take(6)
            ->get();

        return [
            'stats'             => $stats,
            'recentAssessments' => $recentAssessments,
        ];
    }

    protected function adminProdiDashboard(User $user): array
    {
        // Jika kaprodi → filter berdasarkan department, selain itu berdasarkan created_by
        $isKaprodi = in_array(strtolower($user->role?->name ?? ''), ['kaprodi']);

        $query = Planning::query();

        if ($isKaprodi) {
            $query->where('department_id', $user->department_id);
        } else {
            $query->where('created_by', $user->id);
        }

        return [
            'stats' => [
                'total_planning'        => (clone $query)->count(),
                'draft'                 => (clone $query)->whereRaw('LOWER(status) = ?', ['draft'])->count(),
                'submitted'             => (clone $query)->whereRaw('LOWER(status) = ?', ['submitted'])->count(),
                'revision'              => (clone $query)->whereRaw('LOWER(status) = ?', ['revision'])->count(),
                'approved'              => (clone $query)->whereRaw('LOWER(status) = ?', ['approved'])->count(),
                'rejected'              => (clone $query)->whereRaw('LOWER(status) = ?', ['rejected'])->count(),
                'total_implementations' => $isKaprodi
                    ? Implementation::whereHas('planning', fn($q) => $q->where('department_id', $user->department_id))->count()
                    : Implementation::where('created_by', $user->id)->count(),
                'total_assessments'     => $isKaprodi
                    ? Assessment::where('department_id', $user->department_id)->count()
                    : Assessment::where('created_by', $user->id)->count(),
            ],
            'myPlannings' => (clone $query)
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    protected function kaprodiDashboard(User $user): array
    {
        return [
            'stats' => [
                'pending_questionnaires' => AssessmentRespondent::where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->count(),
                'total_planning' => Planning::where('department_id', $user->department_id)->count(),
            ],
        ];
    }

    // protected function approverDashboard(User $user): array
    // {
    //     return [
    //         'stats' => [
    //             'pending_approval' => \App\Models\PlanningApproval::where('approver_id', $user->id)
    //                 ->where('status', 'pending')
    //                 ->count(),
    //             'approved_by_me'   => \App\Models\PlanningApproval::where('approver_id', $user->id)
    //                 ->where('status', 'approved')
    //                 ->count(),
    //             'revision_by_me'   => \App\Models\PlanningApproval::where('approver_id', $user->id)
    //                 ->where('status', 'revision')
    //                 ->count(),
    //             'rejected_by_me'   => \App\Models\PlanningApproval::where('approver_id', $user->id)
    //                 ->where('status', 'rejected')
    //                 ->count(),
    //             'total_processed'  => \App\Models\PlanningApproval::where('approver_id', $user->id)
    //                 ->whereIn('status', ['approved', 'rejected', 'revision'])
    //                 ->count(),
    //         ],
    //         'pendingApprovals' => \App\Models\PlanningApproval::with(['planning.department', 'planning.creator'])
    //             ->where('approver_id', $user->id)
    //             ->where('status', 'pending')
    //             ->latest()
    //             ->take(5)
    //             ->get(),
    //     ];
    // }
    protected function approverDashboard(User $user): array
    {
        $approvalQuery = \App\Models\PlanningApproval::query()
            ->where('approver_id', $user->id);

        return [
            'stats' => [
                'pending_approval' => (clone $approvalQuery)
                    ->where('status', 'pending')
                    ->count(),
                'approved_by_me' => (clone $approvalQuery)
                    ->where('status', 'approved')
                    ->count(),
                'revision_by_me' => (clone $approvalQuery)
                    ->where('status', 'revision')
                    ->count(),
                'rejected_by_me' => (clone $approvalQuery)
                    ->where('status', 'rejected')
                    ->count(),
                'total_processed' => (clone $approvalQuery)
                    ->whereIn('status', ['approved', 'rejected', 'revision'])
                    ->count(),
            ],
            
            'pendingApprovals' => (clone $approvalQuery)
                ->with(['planning','planning.department','planning.creator',])
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    // protected function dosenDashboard(User $user): array
    // {
    //     return [
    //         'stats' => [
    //             'open_assessments' => AssessmentRespondent::where('user_id', $user->id)
    //                 ->whereIn('status', ['pending', 'in_progress'])
    //                 ->count(),
    //             'submitted'        => AssessmentRespondent::where('user_id', $user->id)
    //                 ->where('status', 'completed')
    //                 ->count(),
    //             'my_results'       => AssessmentRespondent::where('user_id', $user->id)
    //                 ->where('status', 'completed')
    //                 ->count(),
    //             'avg_score'        => 0, // isi jika ada perhitungan skor
    //         ],
    //         'openAssessments' => AssessmentRespondent::with(['assessment'])
    //             ->where('user_id', $user->id)
    //             ->whereIn('status', ['pending', 'in_progress'])
    //             ->latest()
    //             ->take(5)
    //             ->get(),
    //     ];
    // }
    protected function dosenDashboard(User $user): array
    {
        return [
            'stats' => [
                'open_assessments' => AssessmentRespondent::where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->count(),
                'submitted'        => AssessmentRespondent::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'my_results'       => AssessmentRespondent::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'avg_score'        => 0,
            ],
            'openAssessments' => AssessmentRespondent::with([
                    'assessment.department',   // yang pasti ada dari kode sebelumnya
                ])
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}