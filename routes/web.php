<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\General\AuthController;
use App\Http\Controllers\General\DashboardController;
use App\Http\Controllers\General\PlanningController;
use App\Http\Controllers\General\PlanningApprovalController;
use App\Http\Controllers\General\SignatureVerifyController;
use App\Http\Controllers\General\ImplementationController;
use App\Http\Controllers\General\CapabilityFillController;
use App\Http\Controllers\General\ResultAssessmentController;
use App\Http\Controllers\General\ProfileController;

use App\Http\Controllers\Administrator\PlanningTypeController;
use App\Http\Controllers\Administrator\UserController;
use App\Http\Controllers\Administrator\CobitDomainController;
use App\Http\Controllers\Administrator\DepartmentController;

use App\Http\Controllers\Assessor\AssessmentController;
use App\Http\Controllers\Assessor\DesignFactorController;
use App\Http\Controllers\Assessor\ObjectiveController;
use App\Http\Controllers\Assessor\CapabilityController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen User (Administrator)
    Route::prefix('administrator')->name('administrator.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('cobit-domains', CobitDomainController::class)->parameters(['cobit-domains' => 'cobitDomain']);
        Route::resource('departments', DepartmentController::class);
    });

    // Perencanaan
    Route::prefix('plannings')->name('plannings.')->group(function () {
        Route::get('/',                [PlanningController::class, 'index'])->name('index');
        Route::get('/create',          [PlanningController::class, 'create'])->name('create');
        Route::post('/',               [PlanningController::class, 'store'])->name('store');
        Route::get('/{planning}',      [PlanningController::class, 'show'])->name('show');
        Route::get('/{planning}/edit', [PlanningController::class, 'edit'])->name('edit');
        Route::put('/{planning}',      [PlanningController::class, 'update'])->name('update');
        Route::delete('/{planning}',   [PlanningController::class, 'destroy'])->name('destroy');
        Route::post('/{planning}/submit', [PlanningController::class, 'submit'])->name('submit');
        Route::get('/{planning}/proposal',  [PlanningController::class, 'proposal'])->name('proposal');
    });

    // Jenis Perencanaan (Master Data)
    Route::prefix('planning-types')->name('planning-types.')->group(function () {
        Route::get('/',                      [PlanningTypeController::class, 'index'])->name('index');
        Route::get('/create',                [PlanningTypeController::class, 'create'])->name('create');
        Route::post('/',                     [PlanningTypeController::class, 'store'])->name('store');
        Route::get('/{planningType}',        [PlanningTypeController::class, 'show'])->name('show');
        Route::get('/{planningType}/edit',   [PlanningTypeController::class, 'edit'])->name('edit');
        Route::put('/{planningType}',        [PlanningTypeController::class, 'update'])->name('update');
        Route::delete('/{planningType}',     [PlanningTypeController::class, 'destroy'])->name('destroy');
    });

    // Planning-Approvals
    Route::prefix('planning-approvals')->name('planning-approvals.')->group(function (){
        Route::get('/',                         [PlanningApprovalController::class, 'index'])->name('index');
        Route::get('/{planningApproval}',       [PlanningApprovalController::class, 'show'])->name('show');
        Route::put('/{planningApproval}',       [PlanningApprovalController::class, 'update'])->name('update');
    });

    // Implementation
    Route::prefix('implementations')->name('implementations.')->group(function () {
        Route::get('/',                          [ImplementationController::class, 'index'])->name('index');
        Route::get('/create/{planning}',         [ImplementationController::class, 'create'])->name('create');
        Route::post('/',                         [ImplementationController::class, 'store'])->name('store');
        Route::get('/{implementation}',          [ImplementationController::class, 'show'])->name('show');
        Route::get('/{implementation}/edit',     [ImplementationController::class, 'edit'])->name('edit');
        Route::put('/{implementation}',          [ImplementationController::class, 'update'])->name('update');
        Route::delete('/{implementation}',       [ImplementationController::class, 'destroy'])->name('destroy');
        Route::delete('/evidences/{evidence}',   [ImplementationController::class, 'destroyEvidence'])->name('evidences.destroy');
    });
    Route::get('/verify-signature/{token}', [SignatureVerifyController::class, 'show'])->name('signature.verify');

    Route::prefix('assessor')->name('assessor.')->group(function () {
        // Assessment
        Route::prefix('assessments')->name('assessments.')->group(function () {
            Route::get('/',              [AssessmentController::class, 'index'])->name('index');
            Route::get('/create',        [AssessmentController::class, 'create'])->name('create');
            Route::post('/',             [AssessmentController::class, 'store'])->name('store');
            Route::get('/{assessment}',  [AssessmentController::class, 'show'])->name('show');
        });
        
        // Laporan
        Route::get('/assessments/{assessment}/laporan', [AssessmentController::class, 'laporan'])->name('assessments.laporan');
        Route::get('/assessments/{assessment}/laporan/pdf', [AssessmentController::class, 'laporanPdf'])->name('assessments.laporan.pdf');
        Route::get('/assessments/{assessment}/laporan/excel', [AssessmentController::class, 'laporanExcel'])->name('assessments.laporan.excel');

        // Design Factor
        Route::prefix('design-factors')->name('design-factors.')->group(function () {
            Route::get('/',                    [DesignFactorController::class, 'index'])->name('index');
            Route::get('/create',              [DesignFactorController::class, 'create'])->name('create');
            Route::post('/',                   [DesignFactorController::class, 'store'])->name('store');
            Route::get('/{assessment}',        [DesignFactorController::class, 'show'])->name('show');
            Route::get('/{assessment}/result', [DesignFactorController::class, 'result'])->name('result');
            Route::get('/{designFactor}/edit', [DesignFactorController::class, 'edit'])->name('edit');
            Route::put('/{designFactor}',      [DesignFactorController::class, 'update'])->name('update');
        });

        // Objective
        Route::prefix('objectives')->name('objectives.')->group(function () {
            Route::get('/', [ObjectiveController::class, 'index'])->name('index');
            Route::post('/{assessment}/generate', [ObjectiveController::class, 'generateObjectives'])->name('generate');
            Route::get('/{assessment}', [ObjectiveController::class, 'show'])->name('show');
        });

        Route::prefix('capability')->name('capability.')->group(function () {
            Route::get('/',                          [CapabilityController::class, 'index'])->name('index');
            Route::post('/{assessment}/start',       [CapabilityController::class, 'start'])->name('start');
            Route::get('/{assessment}',              [CapabilityController::class, 'show'])->name('show');
            Route::get('/{assessment}/result',       [CapabilityController::class, 'result'])->name('result');
            Route::post('/{assessment}/recalculate', [CapabilityController::class, 'recalculate'])->name('recalculate');
            Route::get('/{assessment}/judge',        [CapabilityController::class, 'judge'])->name('judge');
            Route::post('/{assessment}/judge',       [CapabilityController::class, 'storeJudge'])->name('judge.store');
        });

        Route::prefix('gap-analysis')->name('gap-analysis.')->group(function () {
            Route::get('/',                     [CapabilityController::class, 'gapIndex'])->name('index');
            Route::get('/{assessment}',         [CapabilityController::class, 'gap'])->name('show');
        });

        // Prioritas
        Route::get('/priority', [CapabilityController::class, 'priorityIndex'])->name('priority.index');
        Route::get('/priority/{assessment}', [CapabilityController::class, 'priority'])->name('priority.show');

        // Roadmap
        Route::get('/roadmap', [CapabilityController::class, 'roadmapIndex'])->name('roadmap.index');
        Route::get('/roadmap/{assessment}', [CapabilityController::class, 'roadmap'])->name('roadmap.show');
    });

    Route::prefix('capability')->name('capability.fill.')->group(function () {
        Route::get('/',                                    [CapabilityFillController::class, 'index'])->name('index');
        Route::get('/{assessment}',                        [CapabilityFillController::class, 'show'])->name('show');
        Route::get('/{assessment}/{cobit}',                [CapabilityFillController::class, 'fill'])->name('fill');
        Route::post('/{assessment}/{cobit}',               [CapabilityFillController::class, 'store'])->name('store');
        Route::post('/{assessment}/complete',              [CapabilityFillController::class, 'complete'])->name('complete');
    });

    Route::get('/result-assessment',                    [ResultAssessmentController::class, 'index'])->name('result-assessment.index');
    Route::get('/result-assessment/{assessment}/pdf',   [ResultAssessmentController::class, 'laporanPdf'])->name('result-assessment.pdf');
    Route::get('/result-assessment/{assessment}/excel', [ResultAssessmentController::class, 'laporanExcel'])->name('result-assessment.excel');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

});