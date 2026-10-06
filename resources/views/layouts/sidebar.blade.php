@php
    $role = Auth::user()->role->name ?? '';
@endphp

<aside class="app-sidebar" id="appSidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo-pnm.png') }}" alt="Logo DIGI-Campus" class="brand-logo">
        <div class="brand-text">
            <div class="brand-name">DIGI-Campus</div>
            <div class="brand-tagline">Smart Campus</div>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="sidebar-nav">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon material-symbols-outlined">dashboard</span>
            <span class="nav-label">Dashboard</span>
        </a>

        {{-- ==================== ADMIN ==================== --}}
        @if($role === 'administrator')
            <div class="menu-section">Master Data</div>

            <a href="{{ route('administrator.users.index') }}"
               class="nav-link {{ request()->routeIs('administrator.users.*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">group</span>
                <span class="nav-label">Pengguna</span>
            </a>

            <a href="{{ route('administrator.departments.index') }}"
               class="nav-link {{ request()->routeIs('administrator.departments.*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">apartment</span>
                <span class="nav-label">Unit</span>
            </a>

            <a href="{{ route('planning-types.index') }}"
               class="nav-link {{ request()->routeIs('planning-types.*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">category</span>
                <span class="nav-label">Jenis Perencanaan</span>
            </a>

            <a href="{{ route('administrator.cobit-domains.index') }}"
               class="nav-link {{ request()->routeIs('administrator.cobit-domains.*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">hub</span>
                <span class="nav-label">Evaluasi COBIT 2019</span>
            </a>
        @endif

        {{-- ==================== KAPRODI ==================== --}}
        @if($role === 'kaprodi')
            <div class="menu-section">Master Data</div>
            <a href="{{ route('planning-types.index') }}"
               class="nav-link {{ request()->routeIs('planning-types.*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">category</span>
                <span class="nav-label">Jenis Perencanaan</span>
            </a>
        @endif

        {{-- ==================== PRODI / UPA ==================== --}}
        @if(in_array($role, ['admin_prodi', 'upa'], true))
            <div class="menu-section">Kegiatan</div>

            <a href="{{ route('plannings.index') }}"
               class="nav-link {{ request()->routeIs('plannings.*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">edit_calendar</span>
                <span class="nav-label">Perencanaan</span>
            </a>

            <a href="{{ route('implementations.index') }}"
               class="nav-link {{ request()->routeIs('implementations.*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">task_alt</span>
                <span class="nav-label">Pelaksanaan</span>
            </a>

            <a href="{{ route('capability.fill.index') }}"
               class="nav-link {{ request()->routeIs('capability.fill*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">assignment</span>
                <span class="nav-label">Penilaian Capability</span>
            </a>
        @endif

        {{-- ==================== DOSEN / KAPRODI ==================== --}}
        @if(in_array($role, ['dosen', 'kaprodi'], true))
            <div class="menu-section">Kegiatan</div>
            <a href="{{ route('capability.fill.index') }}"
               class="nav-link {{ request()->routeIs('capability.fill*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">assignment</span>
                <span class="nav-label">Penilaian Capability</span>
            </a>
        @endif

        {{-- ==================== APPROVER ==================== --}}
        @if(in_array($role, ['kajur', 'wadir', 'direktur', 'keuangan'], true))
            <div class="menu-section">Persetujuan</div>
            <a href="{{ route('planning-approvals.index') }}"
               class="nav-link {{ request()->routeIs('planning-approvals.*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">approval</span>
                <span class="nav-label">Persetujuan</span>
            </a>
        @endif

        {{-- ==================== HASIL ==================== --}}
        @if(in_array($role, ['administrator', 'admin_prodi', 'upa', 'kajur', 'wadir', 'direktur', 'kaprodi', 'dosen'], true))
            <div class="menu-section">Hasil</div>
            <a href="{{ route('result-assessment.index') }}"
               class="nav-link {{ request()->routeIs('result-assessment.*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">analytics</span>
                <span class="nav-label">Hasil Assessment</span>
            </a>
        @endif

        {{-- ==================== ASSESSOR ==================== --}}
        @if($role === 'assessor')
            <div class="menu-section">Assessment</div>

            <a href="{{ route('assessor.assessments.index') }}"
               class="nav-link {{ request()->routeIs('assessor.assessments*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">assignment</span>
                <span class="nav-label">Assessment</span>
            </a>

            <a href="{{ route('assessor.design-factors.index') }}"
               class="nav-link {{ request()->routeIs('assessor.design-factors*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">tune</span>
                <span class="nav-label">Design Factor</span>
            </a>

            <a href="{{ route('assessor.objectives.index') }}"
               class="nav-link {{ request()->routeIs('assessor.objectives*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">flag</span>
                <span class="nav-label">Objectives</span>
            </a>

            <a href="{{ route('assessor.capability.index') }}"
               class="nav-link {{ request()->routeIs('assessor.capability*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">verified</span>
                <span class="nav-label">Capability Assessment</span>
            </a>

            <div class="menu-section">Analisis</div>

            <a href="{{ route('assessor.gap-analysis.index') }}"
               class="nav-link {{ request()->routeIs('assessor.gap-analysis*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">compare_arrows</span>
                <span class="nav-label">Gap Analysis</span>
            </a>

            <a href="{{ route('assessor.priority.index') }}"
               class="nav-link {{ request()->routeIs('assessor.priority*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">priority_high</span>
                <span class="nav-label">Prioritas</span>
            </a>

            <a href="{{ route('assessor.roadmap.index') }}"
               class="nav-link {{ request()->routeIs('assessor.roadmap*') ? 'active' : '' }}">
                <span class="nav-icon material-symbols-outlined">map</span>
                <span class="nav-label">Roadmap</span>
            </a>
        @endif
    </nav>
</aside>

<style>
/* ===== Sidebar Container ===== */
.app-sidebar {
    display: flex;
    flex-direction: column;
    background: linear-gradient(160deg, 
        #f8fafc 0%, 
        #f1f5f9 30%, 
        #eff6ff 65%, 
        #e0f2fe 100%
    );
    border-right: 1px solid #eef2f7;
    box-shadow: 1px 0 0 rgba(15, 23, 42, 0.02);
}


/* ===== Brand (Logo kiri + teks kanan) ===== */
.sidebar-brand {
    display: flex;
    flex-direction: row;          /* horizontal */
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1.1rem 1.15rem;
    border-bottom: 1px solid #f1f5f9;
    min-height: 72px;
    background: linear-gradient(180deg, 
        rgba(248, 250, 252, 0.9) 0%, 
        rgba(239, 246, 255, 0.7) 100%
    );
}


.brand-logo {
    height: 45px;
    width: auto;
    object-fit: contain;
    flex-shrink: 0;
}


.brand-text {
    line-height: 1.2;
    min-width: 0;
}


.brand-name {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
}


.brand-tagline {
    font-size: 0.8rem;
    color: #94a3b8;
    font-weight: 500;
    margin-top: 0.1rem;
}


/* ===== Navigation ===== */
.sidebar-nav {
    flex-grow: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 0.85rem 0.7rem 1.25rem;
}


/* Custom scrollbar */
.sidebar-nav::-webkit-scrollbar {
    width: 4px;
}
.sidebar-nav::-webkit-scrollbar-track {
    background: transparent;
}
.sidebar-nav::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 999px;
}
.sidebar-nav::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}


/* ===== Menu Section ===== */
.menu-section {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 1.15rem 0.85rem 0.4rem;
}


/* ===== Nav Link ===== */
.sidebar-nav .nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.7rem 0.85rem;
    border-radius: 0.7rem;
    color: #475569;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.18s ease;
    margin-bottom: 3px;
    position: relative;
    border: 1px solid transparent;
}


.sidebar-nav .nav-link:hover {
    background: #f8fafc;
    color: #1d4ed8;
    border-color: #eef2f7;
}


.sidebar-nav .nav-link.active {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
    color: #ffffff !important;
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.28);
    border-color: transparent;
}


.sidebar-nav .nav-link.active .nav-icon {
    color: #ffffff;
}


.sidebar-nav .nav-icon {
    font-size: 1.25rem;
    width: 24px;
    text-align: center;
    flex-shrink: 0;
    color: #64748b;
    transition: color 0.18s ease;
}


.sidebar-nav .nav-link:hover .nav-icon {
    color: #1d4ed8;
}


.sidebar-nav .nav-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* Active indicator bar (opsional, kiri) */
.sidebar-nav .nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 55%;
    background: rgba(255, 255, 255, 0.85);
    border-radius: 0 4px 4px 0;
}
</style>