@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- Welcome Card --}}
<style>
  .page-header-card {
    position: relative;
    overflow: hidden;
    padding: 1.75rem 2rem;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 40%, #3b82f6 75%, #60a5fa 100%);
    color: #fff;
    box-shadow: 0 12px 30px rgba(37, 99, 235, 0.25);
  }
  .page-header-card h4 {
    color: #fff;
    font-weight: 700;
  }
  .page-header-card p {
    color: rgba(255, 255, 255, 0.88) !important;
  }
  .page-header-card .accent {
    position: absolute;
    top: -90px;
    right: -70px;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    filter: blur(10px);
  }
</style>

<div class="page-header-card">
  <div class="accent"></div>

  <div class="position-relative d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
      <h4 class="fw-bold mb-1">Selamat datang, {{ $user->name }}</h4>
      <p class="mb-1 small">
        {{ $role }}
        @if($user->department)
          · {{ $user->department->name }}
        @endif
      </p>
      <p class="mb-0 small opacity-90">
        {{ now()->translatedFormat('l, d F Y') }}
      </p>
    </div>

    @if(in_array($role, ['admin_prodi', 'upa']))
      <a href="#"
         class="btn btn-light btn-sm d-inline-flex align-items-center gap-1 fw-semibold text-primary shadow-sm">
        <span class="material-symbols-outlined" style="font-size:1.1rem">add</span>
        Buat Perencanaan
      </a>
    @endif
  </div>
</div>

{{-- ==================== ADMIN DASHBOARD ==================== --}}
@if($role === 'administrator')
<style>
  :root {
    /* --dash-bg: linear-gradient(135deg, #f0f4ff 0%, #f8fafc 40%, #f0fdf4 100%); */
    --card-bg: #ffffff;
    --card-border: rgba(148, 163, 184, 0.15);
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.03);
    --shadow-hover: 0 10px 25px rgba(0,63,177,0.08), 0 4px 10px rgba(0,0,0,0.04);
    --radius: 1rem;
    --radius-sm: 0.75rem;
  }

  .admin-dashboard {
    background: var(--dash-bg);
    border-radius: 1.25rem;
    padding: 1.5rem;
    margin: -0.5rem -0.75rem 0;
  }

  /* ===== STAT CARDS ===== */
  .dash-stat {
    background: var(--card-bg);
    border-radius: var(--radius);
    padding: 1.35rem 1.4rem;
    box-shadow: var(--shadow-sm);
    height: 100%;
    border: 1px solid var(--card-border);
    position: relative;
    overflow: hidden;
    transition: all 0.25s ease;
  }
  .dash-stat:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
  }
  .dash-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 4px; height: 100%;
    border-radius: 4px 0 0 4px;
  }
  .dash-stat.blue::before   { background: linear-gradient(180deg, #003fb1, #3b82f6); }
  .dash-stat.cyan::before   { background: linear-gradient(180deg, #0891b2, #22d3ee); }
  .dash-stat.green::before  { background: linear-gradient(180deg, #059669, #34d399); }
  .dash-stat.purple::before { background: linear-gradient(180deg, #7c3aed, #a78bfa); }
  .dash-stat.orange::before { background: linear-gradient(180deg, #d97706, #fbbf24); }

  .dash-stat .icon {
    width: 46px; height: 46px;
    border-radius: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.85rem;
  }
  .dash-stat .icon.blue   { background: linear-gradient(135deg, rgba(0,63,177,0.12), rgba(59,130,246,0.08)); color: #003fb1; }
  .dash-stat .icon.cyan   { background: linear-gradient(135deg, rgba(8,145,178,0.12), rgba(34,211,238,0.08)); color: #0891b2; }
  .dash-stat .icon.green  { background: linear-gradient(135deg, rgba(5,150,105,0.12), rgba(52,211,153,0.08)); color: #059669; }
  .dash-stat .icon.purple { background: linear-gradient(135deg, rgba(124,58,237,0.12), rgba(167,139,250,0.08)); color: #7c3aed; }
  .dash-stat .icon.orange { background: linear-gradient(135deg, rgba(217,119,6,0.12), rgba(251,191,36,0.08)); color: #d97706; }

  .dash-stat .label {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 0.2rem;
    font-weight: 500;
  }
  .dash-stat .value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
    line-height: 1.2;
  }

  /* ===== STATUS SUMMARY ===== */
  .status-card {
    background: var(--card-bg);
    border-radius: var(--radius-sm);
    padding: 1.1rem 1.25rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--card-border);
    height: 100%;
    display: flex;
    align-items: center;
    gap: 0.9rem;
    transition: all 0.2s ease;
  }
  .status-card:hover {
    box-shadow: var(--shadow-md);
  }
  .status-dot {
    width: 12px; height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.04);
  }
  .dot-approved  { background: #10b981; }
  .dot-submitted  { background: #3b82f6; }
  .dot-draft      { background: #94a3b8; }
  .dot-revision   { background: #f59e0b; }
  .dot-rejected   { background: #ef4444; }

  .status-card .label {
    font-size: 0.78rem;
    color: #64748b;
    margin-bottom: 0.15rem;
  }
  .status-card .value {
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1.2;
  }

  /* ===== CONTENT CARDS ===== */
  .dash-card {
    background: var(--card-bg);
    border-radius: var(--radius);
    padding: 1.4rem 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--card-border);
    height: 100%;
    transition: box-shadow 0.25s ease;
  }
  .dash-card:hover {
    box-shadow: var(--shadow-md);
  }
  .dash-card h6 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 0;
  }

  /* ===== Modern Table ===== */
  .modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }
  .modern-table thead th {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #94a3b8;
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
  }
  .modern-table tbody tr {
    transition: background 0.15s;
  }
  .modern-table tbody tr:hover {
    background: #f8fafc;
  }
  .modern-table td {
    padding: 0.9rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  .modern-table tbody tr:last-child td {
    border-bottom: none;
  }

  .title-link {
    color: #0f172a;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
    transition: color 0.15s;
  }
  .title-link:hover {
    color: #003fb1;
  }

  /* Badge status modern */
  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.28rem 0.65rem;
    border-radius: 999px;
  }
  .status-badge .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
  }
  .badge-approved  { background: #d1fae5; color: #065f46; }
  .badge-approved .dot  { background: #10b981; }
  .badge-submitted { background: #dbeafe; color: #1e40af; }
  .badge-submitted .dot { background: #3b82f6; }
  .badge-revision  { background: #fef3c7; color: #92400e; }
  .badge-revision .dot  { background: #f59e0b; }
  .badge-rejected  { background: #fee2e2; color: #991b1b; }
  .badge-rejected .dot  { background: #ef4444; }
  .badge-draft     { background: #f1f5f9; color: #475569; }
  .badge-draft .dot     { background: #94a3b8; }

  /* Activity */
  .activity-item {
    display: flex;
    gap: 0.9rem;
    padding: 0.9rem 0;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
  }
  .activity-item:last-child {
    border-bottom: none;
  }
  .activity-item:hover {
    background: #f8fafc;
    margin: 0 -0.75rem;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    border-radius: 0.5rem;
  }
  .activity-icon {
    width: 38px; height: 38px;
    border-radius: 0.65rem;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #64748b;
  }
  .activity-item .desc {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 0.15rem;
  }
  .activity-item .meta {
    font-size: 0.72rem;
    color: #94a3b8;
  }

  .link-see-all {
    font-size: 0.8rem;
    font-weight: 500;
    color: #003fb1;
    text-decoration: none;
    transition: color 0.15s;
  }
  .link-see-all:hover {
    color: #002a80;
  }

  /* Empty state */
  .empty-state {
    text-align: center;
    padding: 2.5rem 1rem;
    color: #94a3b8;
  }
  .empty-state .material-symbols-outlined {
    font-size: 2.2rem;
    opacity: 0.4;
    margin-bottom: 0.5rem;
  }
</style>

<div class="admin-dashboard">

  {{-- Statistik utama --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat blue">
        <div class="icon blue">
          <span class="material-symbols-outlined" style="font-size:1.3rem">group</span>
        </div>
        <p class="label mb-0">Total User</p>
        <div class="value">{{ $stats['total_users'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat cyan">
        <div class="icon cyan">
          <span class="material-symbols-outlined" style="font-size:1.3rem">apartment</span>
        </div>
        <p class="label mb-0">Unit / Prodi</p>
        <div class="value">{{ $stats['total_departments'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat blue">
        <div class="icon blue">
          <span class="material-symbols-outlined" style="font-size:1.3rem">edit_calendar</span>
        </div>
        <p class="label mb-0">Perencanaan</p>
        <div class="value">{{ $stats['total_planning'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat green">
        <div class="icon green">
          <span class="material-symbols-outlined" style="font-size:1.3rem">task_alt</span>
        </div>
        <p class="label mb-0">Pelaksanaan</p>
        <div class="value">{{ $stats['total_implementations'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat purple">
        <div class="icon purple">
          <span class="material-symbols-outlined" style="font-size:1.3rem">assignment</span>
        </div>
        <p class="label mb-0">Assessment</p>
        <div class="value">{{ $stats['total_assessments'] ?? 0 }}</div>
      </div>
    </div>
  </div>

  {{-- Ringkasan status perencanaan --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md">
      <div class="status-card">
        <span class="status-dot dot-approved"></span>
        <div>
          <p class="label mb-0">Disetujui</p>
          <div class="value text-success">{{ $stats['approved'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md">
      <div class="status-card">
        <span class="status-dot dot-submitted"></span>
        <div>
          <p class="label mb-0">Menunggu</p>
          <div class="value text-primary">{{ $stats['pending'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md">
      <div class="status-card">
        <span class="status-dot dot-draft"></span>
        <div>
          <p class="label mb-0">Draft</p>
          <div class="value">{{ $stats['draft'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md">
      <div class="status-card">
        <span class="status-dot dot-revision"></span>
        <div>
          <p class="label mb-0">Revisi</p>
          <div class="value text-warning">{{ $stats['revision'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md">
      <div class="status-card">
        <span class="status-dot dot-rejected"></span>
        <div>
          <p class="label mb-0">Ditolak</p>
          <div class="value text-danger">{{ $stats['rejected'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabel perencanaan + Aktivitas --}}
  <div class="row g-3">
    <div class="col-12 col-lg-7">
      <div class="dash-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0">Perencanaan Terbaru</h6>
          <a href="{{ route('plannings.index') }}" class="link-see-all">Lihat semua →</a>
        </div>

        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th class="text-center">Judul</th>
                <th class="text-center">Unit</th>
                <th class="text-center">Status</th>
                <th class="text-center">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentPlannings as $p)
                <tr>
                  <td>
                    <a href="{{ route('plannings.show', $p) }}" class="title-link">
                      {{ $p->title ?? $p->name ?? '-' }}
                    </a>
                  </td>
                  <td class="small text-secondary text-center">
                    {{ $p->department->name ?? '-' }}
                  </td>
                  <td class="text-center">
                    @php
                      $status = $p->status ?? 'Draft';
                      $badgeClass = match(strtolower($status)) {
                        'approved'  => 'badge-approved',
                        'submitted' => 'badge-submitted',
                        'revision'  => 'badge-revision',
                        'rejected'  => 'badge-rejected',
                        default     => 'badge-draft',
                      };
                    @endphp
                    <span class="status-badge {{ $badgeClass }}">
                      <span class="dot"></span>
                      {{ $status }}
                    </span>
                  </td>
                  <td class="small text-secondary text-center">
                    {{ $p->created_at?->format('d M Y') }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4">
                    <div class="empty-state">
                      <div class="material-symbols-outlined">inbox</div>
                      <div class="small">Belum ada perencanaan.</div>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="dash-card">
        <h6 class="mb-3">Aktivitas Terbaru</h6>
        @forelse($recentActivities as $act)
          <div class="activity-item">
            <div class="activity-icon">
              <span class="material-symbols-outlined" style="font-size:1.15rem">{{ $act->icon ?? 'info' }}</span>
            </div>
            <div class="flex-grow-1">
              <p class="desc mb-0">{{ $act->description ?? $act->text }}</p>
              <p class="meta mb-0">
                {{ $act->user->name ?? $act->user ?? '-' }} · {{ $act->created_at?->diffForHumans() ?? $act->time }}
              </p>
            </div>
          </div>
        @empty
          <div class="empty-state">
            <div class="material-symbols-outlined">history</div>
            <div class="small">Belum ada aktivitas.</div>
          </div>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endif

{{-- ==================== DOSEN ==================== --}}
@if($role === 'dosen')
<style>
  :root {
    /* --dash-bg: linear-gradient(135deg, #f0f4ff 0%, #f8fafc 45%, #f0fdf4 100%); */
    --card-bg: #ffffff;
    --card-border: rgba(148, 163, 184, 0.15);
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.03);
    --shadow-hover: 0 10px 25px rgba(0,63,177,0.08), 0 4px 10px rgba(0,0,0,0.04);
    --radius: 1rem;
    --radius-sm: 0.75rem;
  }

  .dosen-dashboard {
    background: var(--dash-bg);
    border-radius: 1.25rem;
    padding: 1.5rem;
    margin: -0.5rem -0.75rem 0;
  }

  /* ===== STAT CARDS ===== */
  .dash-stat {
    background: var(--card-bg);
    border-radius: var(--radius);
    padding: 1.25rem 1.35rem;
    box-shadow: var(--shadow-sm);
    height: 100%;
    border: 1px solid var(--card-border);
    position: relative;
    overflow: hidden;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .dash-stat:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
  }
  .dash-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 4px; height: 100%;
    border-radius: 4px 0 0 4px;
  }
  .dash-stat.orange::before { background: linear-gradient(180deg, #d97706, #fbbf24); }
  .dash-stat.green::before  { background: linear-gradient(180deg, #059669, #34d399); }
  .dash-stat.blue::before   { background: linear-gradient(180deg, #003fb1, #3b82f6); }
  .dash-stat.purple::before { background: linear-gradient(180deg, #7c3aed, #a78bfa); }

  .dash-stat .icon {
    width: 46px; height: 46px;
    border-radius: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .dash-stat .icon.orange { background: linear-gradient(135deg, rgba(217,119,6,0.12), rgba(251,191,36,0.08)); color: #d97706; }
  .dash-stat .icon.green  { background: linear-gradient(135deg, rgba(5,150,105,0.12), rgba(52,211,153,0.08)); color: #059669; }
  .dash-stat .icon.blue   { background: linear-gradient(135deg, rgba(0,63,177,0.12), rgba(59,130,246,0.08)); color: #003fb1; }
  .dash-stat .icon.purple { background: linear-gradient(135deg, rgba(124,58,237,0.12), rgba(167,139,250,0.08)); color: #7c3aed; }

  .dash-stat .label {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 0.15rem;
    font-weight: 500;
  }
  .dash-stat .value {
    font-size: 1.45rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
    line-height: 1.2;
  }

  /* ===== CONTENT CARD ===== */
  .dash-card {
    background: var(--card-bg);
    border-radius: var(--radius);
    padding: 1.4rem 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--card-border);
    height: 100%;
    transition: box-shadow 0.25s ease;
  }
  .dash-card:hover {
    box-shadow: var(--shadow-md);
  }
  .dash-card h6 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 0;
  }

  /* Table */
  .dash-table {
    --bs-table-bg: transparent;
  }
  .dash-table thead th {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #94a3b8;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 0.75rem;
  }
  .dash-table tbody td {
    padding: 0.9rem 0.5rem;
    border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
  }
  .dash-table tbody tr:last-child td {
    border-bottom: none;
  }
  /* .dash-table tbody tr:hover {
    background: #f8fafc;
  } */

  /* Badge */
  .badge-status {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.3em 0.7em;
    border-radius: 999px;
  }

  /* Button */
  .btn-start {
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.35rem 1rem;
    border-radius: 0.5rem;
    background: linear-gradient(135deg, #003fb1, #2563eb);
    border: none;
    color: #fff;
    transition: all 0.2s ease;
  }
  /* .btn-start:hover {
    background: linear-gradient(135deg, #002a80, #1d4ed8);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,63,177,0.25);
    color: #fff;
  } */

  /* Link */
  .link-see-all {
    font-size: 0.8rem;
    font-weight: 500;
    color: #003fb1;
    text-decoration: none;
    transition: color 0.15s;
  }
  .link-see-all:hover {
    color: #002a80;
  }
</style>

<div class="dosen-dashboard">

  {{-- Statistik --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="dash-stat orange">
        <div class="icon orange">
          <span class="material-symbols-outlined" style="font-size:1.25rem">pending_actions</span>
        </div>
        <div>
          <p class="label mb-0">Penilaian Terbuka</p>
          <div class="value">{{ $stats['open_assessments'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="dash-stat green">
        <div class="icon green">
          <span class="material-symbols-outlined" style="font-size:1.25rem">task_alt</span>
        </div>
        <div>
          <p class="label mb-0">Hasil Tersedia</p>
          <div class="value">{{ $stats['my_results'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="dash-stat blue">
        <div class="icon blue">
          <span class="material-symbols-outlined" style="font-size:1.25rem">send</span>
        </div>
        <div>
          <p class="label mb-0">Sudah Disubmit</p>
          <div class="value">{{ $stats['submitted'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="dash-stat purple">
        <div class="icon purple">
          <span class="material-symbols-outlined" style="font-size:1.25rem">star</span>
        </div>
        <div>
          <p class="label mb-0">Rata-rata Skor</p>
          <div class="value">{{ number_format($stats['avg_score'] ?? 0, 1) }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabel penilaian terbuka --}}
  <div class="row g-3">
    <div class="col-12">
      <div class="dash-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0">Penilaian yang Perlu Diisi</h6>
          <a href="{{ route('capability.fill.index') }}" class="link-see-all">Lihat semua →</a>
        </div>

        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle dash-table">
            <thead>
              <tr>
                <th class="text-center">Kegiatan</th>
                <th class="text-center">Unit</th>
                <th class="text-center">Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
            @forelse($openAssessments ?? [] as $item)
              <tr>
                <td class="fw-medium small">
                  {{ $item->assessment->title 
                      ?? $item->assessment->name 
                      ?? 'Penilaian #' . $item->assessment_id 
                      ?? '-' }}
                </td>
                <td class="small text-secondary text-center">
                  {{ $item->assessment->department->name ?? '-' }}
                </td>
                <td class="text-center">
                  @php
                    $statusLabel = match(strtolower($item->status ?? '')) {
                      'pending'     => 'Belum Diisi',
                      'in_progress' => 'Sedang Diisi',
                      'completed'   => 'Sudah Disubmit',
                      default       => ucfirst($item->status ?? 'Terbuka'),
                    };

                    $badgeClass = match(strtolower($item->status ?? '')) {
                      'pending'     => 'bg-warning-subtle text-warning',
                      'in_progress' => 'bg-info-subtle text-info',
                      'completed'   => 'bg-success-subtle text-success',
                      default       => 'bg-secondary-subtle text-secondary',
                    };
                  @endphp

                  <span class="badge badge-status {{ $badgeClass }}">
                    {{ $statusLabel }}
                  </span>
                </td>
                <td class="text-center">
                  <a href="#" class="btn btn-sm btn-start">
                    Mulai
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-secondary py-4 small">
                  Tidak ada penilaian terbuka saat ini.
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

{{-- ==================== PRODI / UPA / KAPRODI ==================== --}}
@if(in_array($role, ['admin_prodi', 'upa', 'kaprodi']))
<style>
  /* ===== Modern Dashboard Variables ===== */
  :root {
    --dash-bg: #f1f5f9;
    --card-bg: #ffffff;
    --border: #e2e8f0;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --blue: #003fb1;
    --blue-light: #e0eaff;
    --green: #059669;
    --green-light: #d1fae5;
    --orange: #d97706;
    --orange-light: #fef3c7;
    --red: #dc2626;
    --red-light: #fee2e2;
    --purple: #7c3aed;
    --purple-light: #ede9fe;
    --cyan: #0891b2;
    --cyan-light: #cffafe;
    --gray: #64748b;
    --gray-light: #f1f5f9;
  }

  .dashboard-section {
    /* background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); */
    border-radius: 1rem;
    padding: 1.5rem;
    margin: -0.5rem;
  }

  .dash-stat {
    background: var(--card-bg);
    border-radius: 1rem;
    padding: 1.25rem 1.35rem;
    height: 100%;
    border: 1px solid var(--border);
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
  }
  .dash-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border-color: transparent;
  }
  .dash-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 4px; height: 100%;
    border-radius: 1rem 0 0 1rem;
  }
  .dash-stat.blue::before   { background: var(--blue); }
  .dash-stat.gray::before   { background: var(--gray); }
  .dash-stat.cyan::before   { background: var(--cyan); }
  .dash-stat.green::before  { background: var(--green); }
  .dash-stat.orange::before { background: var(--orange); }
  .dash-stat.red::before    { background: var(--red); }
  .dash-stat.purple::before { background: var(--purple); }

  .dash-stat .icon {
    width: 46px; height: 46px;
    border-radius: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 1.25rem;
  }
  .dash-stat .icon.blue   { background: var(--blue-light);   color: var(--blue); }
  .dash-stat .icon.gray   { background: var(--gray-light);   color: var(--gray); }
  .dash-stat .icon.cyan   { background: var(--cyan-light);   color: var(--cyan); }
  .dash-stat .icon.green  { background: var(--green-light);  color: var(--green); }
  .dash-stat .icon.orange { background: var(--orange-light); color: var(--orange); }
  .dash-stat .icon.red    { background: var(--red-light);    color: var(--red); }
  .dash-stat .icon.purple { background: var(--purple-light); color: var(--purple); }

  .dash-stat .stat-label {
    font-size: 0.78rem;
    color: var(--text-secondary);
    margin-bottom: 0.15rem;
    font-weight: 500;
  }
  .dash-stat .stat-value {
    font-size: 1.45rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
  }

  .dash-card {
    background: var(--card-bg);
    border-radius: 1rem;
    padding: 1.4rem 1.5rem;
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    height: 100%;
  }
  .dash-card .card-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.15rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid #f1f5f9;
  }
  .dash-card h6 {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
  }
  .dash-card .view-all {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--blue);
    text-decoration: none;
    transition: color 0.2s;
  }
  .dash-card .view-all:hover { color: #002a7a; }

  .modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }
  .modern-table thead th {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--text-secondary);
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
  }
  .modern-table tbody tr { transition: background 0.15s; }
  .modern-table tbody tr:hover { background: #f8fafc; }
  .modern-table td {
    padding: 0.85rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  .modern-table tbody tr:last-child td { border-bottom: none; }

  .modern-table .title-link {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
  }
  .modern-table .title-link:hover { color: var(--blue); }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
  }
  .status-badge .dot {
    width: 6px; height: 6px; border-radius: 50%;
  }
  .badge-approved  { background: #d1fae5; color: #065f46; }
  .badge-approved .dot  { background: #10b981; }
  .badge-submitted { background: #dbeafe; color: #1e40af; }
  .badge-submitted .dot { background: #3b82f6; }
  .badge-revision  { background: #fef3c7; color: #92400e; }
  .badge-revision .dot  { background: #f59e0b; }
  .badge-rejected  { background: #fee2e2; color: #991b1b; }
  .badge-rejected .dot  { background: #ef4444; }
  .badge-draft     { background: #f1f5f9; color: #475569; }
  .badge-draft .dot     { background: #94a3b8; }

  .activity-item {
    display: flex;
    gap: 0.9rem;
    padding: 0.85rem 0;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
  }
  .activity-item:last-child { border-bottom: none; }
  .activity-item:hover {
    background: #f8fafc;
    margin: 0 -0.75rem;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    border-radius: 0.5rem;
  }
  .activity-icon {
    width: 38px; height: 38px;
    border-radius: 0.65rem;
    background: linear-gradient(135deg, #e0eaff, #f0f4ff);
    color: var(--blue);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .activity-content .desc {
    font-size: 0.84rem;
    font-weight: 500;
    color: var(--text-primary);
    line-height: 1.35;
    margin: 0;
  }
  .activity-content .time {
    font-size: 0.72rem;
    color: var(--text-secondary);
    margin-top: 0.15rem;
  }

  .empty-state {
    text-align: center;
    padding: 2.5rem 1rem;
    color: var(--text-secondary);
  }
  .empty-state .material-symbols-outlined {
    font-size: 2.2rem;
    opacity: 0.4;
    margin-bottom: 0.5rem;
  }
</style>

<div class="dashboard-section">

  {{-- Statistik Utama --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="dash-stat blue d-flex align-items-center gap-3">
        <div class="icon blue">
          <span class="material-symbols-outlined">edit_calendar</span>
        </div>
        <div>
          <div class="stat-label">Total Rencana</div>
          <div class="stat-value">{{ $stats['total_planning'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="dash-stat gray d-flex align-items-center gap-3">
        <div class="icon gray">
          <span class="material-symbols-outlined">draft</span>
        </div>
        <div>
          <div class="stat-label">Draft</div>
          <div class="stat-value">{{ $stats['draft'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="dash-stat cyan d-flex align-items-center gap-3">
        <div class="icon cyan">
          <span class="material-symbols-outlined">send</span>
        </div>
        <div>
          <div class="stat-label">Diajukan</div>
          <div class="stat-value">{{ $stats['submitted'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="dash-stat green d-flex align-items-center gap-3">
        <div class="icon green">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div>
          <div class="stat-label">Disetujui</div>
          <div class="stat-value">{{ $stats['approved'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Status Tambahan --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="dash-stat orange d-flex align-items-center gap-3">
        <div class="icon orange">
          <span class="material-symbols-outlined">rate_review</span>
        </div>
        <div>
          <div class="stat-label">Revisi</div>
          <div class="stat-value">{{ $stats['revision'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="dash-stat red d-flex align-items-center gap-3">
        <div class="icon red">
          <span class="material-symbols-outlined">cancel</span>
        </div>
        <div>
          <div class="stat-label">Ditolak</div>
          <div class="stat-value">{{ $stats['rejected'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="dash-stat purple d-flex align-items-center gap-3">
        <div class="icon purple">
          <span class="material-symbols-outlined">task_alt</span>
        </div>
        <div>
          <div class="stat-label">Pelaksanaan</div>
          <div class="stat-value">{{ $stats['total_implementations'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="dash-stat blue d-flex align-items-center gap-3">
        <div class="icon blue">
          <span class="material-symbols-outlined">assignment</span>
        </div>
        <div>
          <div class="stat-label">Assessment</div>
          <div class="stat-value">{{ $stats['total_assessments'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabel + Aktivitas --}}
  <div class="row g-3">
    <div class="col-12 col-lg-7">
      <div class="dash-card">
        <div class="card-header-custom">
          <h6>Rencana Kegiatan Terbaru</h6>
          <a href="{{ route('plannings.index') }}" class="view-all">Lihat semua →</a>
        </div>

        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th class="text-center">Judul</th>
                <th class="text-center">Status</th>
                {{-- <th class="text-center">Tahun</th> --}}
                <th class="text-center">Tanggal</th>
              </tr>
            </thead>
            <tbody>
              @forelse($myPlannings as $p)
                <tr>
                  <td>
                    <a href="{{ route('plannings.show', $p) }}" class="title-link">
                      {{ $p->title ?? $p->name ?? '-' }}
                    </a>
                  </td>
                  <td class="text-center">
                    @php
                      $status = $p->status ?? 'Draft';
                      $badgeClass = match(strtolower($status)) {
                        'approved'  => 'badge-approved',
                        'submitted' => 'badge-submitted',
                        'revision'  => 'badge-revision',
                        'rejected'  => 'badge-rejected',
                        default     => 'badge-draft',
                      };
                    @endphp
                    <span class="status-badge {{ $badgeClass }}">
                      <span class="dot"></span>
                      {{ $status }}
                    </span>
                  </td>
                  {{-- <td class="small text-secondary text-center">{{ $p->year ?? '-' }}</td> --}}
                  <td class="small text-secondary text-center">
                    {{ $p->created_at?->translatedFormat('d M Y') }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4">
                    <div class="empty-state">
                      <div class="material-symbols-outlined">inbox</div>
                      <div class="small">Belum ada perencanaan.</div>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="dash-card">
        <div class="card-header-custom">
          <h6>Aktivitas Terbaru</h6>
        </div>

        @forelse($recentActivities as $act)
          <div class="activity-item">
            <div class="activity-icon">
              <span class="material-symbols-outlined" style="font-size:1.15rem">
                {{ $act->icon ?? 'info' }}
              </span>
            </div>
            <div class="activity-content">
              <p class="desc">{{ $act->description ?? $act->text }}</p>
              <p class="time">
                {{ $act->created_at?->diffForHumans() ?? $act->time }}
              </p>
            </div>
          </div>
        @empty
          <div class="empty-state">
            <div class="material-symbols-outlined">history</div>
            <div class="small">Belum ada aktivitas.</div>
          </div>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endif

{{-- ==================== APPROVER ==================== --}}
@if(in_array($role, ['kajur', 'wadir', 'direktur', 'keuangan']))
<style>
  /* ===== Modern Dashboard Variables ===== */
  :root {
    /* --dash-bg: #f1f5f9; */
    --card-bg: #ffffff;
    --border: #e2e8f0;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --blue: #003fb1;
    --blue-light: #e0eaff;
    --green: #059669;
    --green-light: #d1fae5;
    --orange: #d97706;
    --orange-light: #fef3c7;
    --red: #dc2626;
    --red-light: #fee2e2;
    --purple: #7c3aed;
    --purple-light: #ede9fe;
  }

  .dashboard-section {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 1rem;
    padding: 1.5rem;
    margin: -0.5rem;
  }

  /* ===== Stat Cards ===== */
  .dash-stat {
    background: var(--card-bg);
    border-radius: 1rem;
    padding: 1.3rem 1.35rem;
    height: 100%;
    border: 1px solid var(--border);
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
  }

  .dash-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border-color: transparent;
  }

  .dash-stat::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    border-radius: 1rem 0 0 1rem;
  }

  .dash-stat.orange::before { background: var(--orange); }
  .dash-stat.green::before  { background: var(--green); }
  .dash-stat.red::before    { background: var(--red); }
  .dash-stat.purple::before { background: var(--purple); }
  .dash-stat.blue::before   { background: var(--blue); }

  .dash-stat .icon {
    width: 44px;
    height: 44px;
    border-radius: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.85rem;
  }

  .dash-stat .icon.orange { background: var(--orange-light); color: var(--orange); }
  .dash-stat .icon.green  { background: var(--green-light);  color: var(--green); }
  .dash-stat .icon.red    { background: var(--red-light);    color: var(--red); }
  .dash-stat .icon.purple { background: var(--purple-light); color: var(--purple); }
  .dash-stat .icon.blue   { background: var(--blue-light);   color: var(--blue); }

  .dash-stat .stat-label {
    font-size: 0.78rem;
    color: var(--text-secondary);
    margin-bottom: 0.2rem;
    font-weight: 500;
  }

  .dash-stat .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
  }

  .dash-stat .stat-value.warning { color: var(--orange); }
  .dash-stat .stat-value.success { color: var(--green); }
  .dash-stat .stat-value.danger  { color: var(--red); }

  /* ===== Content Cards ===== */
  .dash-card {
    background: var(--card-bg);
    border-radius: 1rem;
    padding: 1.4rem 1.5rem;
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    height: 100%;
  }

  .dash-card .card-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.15rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid #f1f5f9;
  }

  .dash-card h6 {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
  }

  .dash-card .view-all {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--blue);
    text-decoration: none;
    transition: color 0.2s;
  }

  .dash-card .view-all:hover {
    color: #002a7a;
  }

  /* ===== Modern Table ===== */
  .modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .modern-table thead th {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--text-secondary);
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
  }

  .modern-table tbody tr {
    transition: background 0.15s;
  }

  .modern-table tbody tr:hover {
    background: #f8fafc;
  }

  .modern-table td {
    padding: 0.9rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }

  .modern-table tbody tr:last-child td {
    border-bottom: none;
  }

  .modern-table .title-main {
    font-weight: 500;
    font-size: 0.875rem;
    color: var(--text-primary);
  }

  .modern-table .title-sub {
    font-size: 0.72rem;
    color: var(--text-secondary);
    margin-top: 0.15rem;
  }

  /* ===== Button ===== */
  .btn-proses {
    background: var(--blue);
    color: white;
    font-size: 0.78rem;
    font-weight: 500;
    padding: 0.35rem 0.9rem;
    border-radius: 0.5rem;
    border: none;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
  }

  /* ===== Activity / History ===== */
  .activity-item {
    display: flex;
    gap: 0.9rem;
    padding: 0.9rem 0;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
  }

  .activity-item:last-child {
    border-bottom: none;
  }

  .activity-item:hover {
    background: #f8fafc;
    margin: 0 -0.75rem;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    border-radius: 0.5rem;
  }

  .activity-icon {
    width: 38px;
    height: 38px;
    border-radius: 0.65rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .activity-icon.approved {
    background: var(--green-light);
    color: var(--green);
  }

  .activity-icon.rejected {
    background: var(--red-light);
    color: var(--red);
  }

  .activity-icon.revision {
    background: var(--orange-light);
    color: var(--orange);
  }

  .activity-content .desc {
    font-size: 0.84rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
    line-height: 1.35;
  }

  .activity-content .meta {
    font-size: 0.72rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
  }

  /* Status Badge */
  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.22rem 0.55rem;
    border-radius: 999px;
  }

  .status-badge .dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
  }

  .badge-approved  { background: #d1fae5; color: #065f46; }
  .badge-approved .dot  { background: #10b981; }

  .badge-rejected  { background: #fee2e2; color: #991b1b; }
  .badge-rejected .dot  { background: #ef4444; }

  .badge-revision  { background: #fef3c7; color: #92400e; }
  .badge-revision .dot  { background: #f59e0b; }

  /* Empty state */
  .empty-state {
    text-align: center;
    padding: 2.5rem 1rem;
    color: var(--text-secondary);
  }

  .empty-state .material-symbols-outlined {
    font-size: 2.2rem;
    opacity: 0.4;
    margin-bottom: 0.5rem;
  }
</style>

<div class="dashboard-section">

  {{-- ===== Statistik ===== --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat orange">
        <div class="icon orange">
          <span class="material-symbols-outlined">pending_actions</span>
        </div>
        <div class="stat-label">Menunggu</div>
        <div class="stat-value warning">{{ $stats['pending_approval'] ?? 0 }}</div>
      </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat green">
        <div class="icon green">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div class="stat-label">Disetujui</div>
        <div class="stat-value success">{{ $stats['approved_by_me'] ?? 0 }}</div>
      </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat red">
        <div class="icon red">
          <span class="material-symbols-outlined">cancel</span>
        </div>
        <div class="stat-label">Ditolak</div>
        <div class="stat-value danger">{{ $stats['rejected_by_me'] ?? 0 }}</div>
      </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat purple">
        <div class="icon purple">
          <span class="material-symbols-outlined">rate_review</span>
        </div>
        <div class="stat-label">Revisi</div>
        <div class="stat-value">{{ $stats['revision_by_me'] ?? 0 }}</div>
      </div>
    </div>

    <div class="col-6 col-md-4 col-xl">
      <div class="dash-stat blue">
        <div class="icon blue">
          <span class="material-symbols-outlined">done_all</span>
        </div>
        <div class="stat-label">Total Diproses</div>
        <div class="stat-value">{{ $stats['total_processed'] ?? 0 }}</div>
      </div>
    </div>
  </div>

  {{-- ===== Antrian + Riwayat ===== --}}
  <div class="row g-3">
    <div class="col-12 col-lg-7">
      <div class="dash-card">
        <div class="card-header-custom">
          <h6>Antrian Persetujuan</h6>
          <a href="#" class="view-all">Lihat semua →</a>
        </div>

        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th class="text-center">Kegiatan</th>
                <th class="text-center">Unit</th>
                <th class="text-center">Pengaju</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pendingApprovals as $approval)
                <tr>
                  <td>
                    <div class="title-main">
                        {{ $approval->planning->title ?? $approval->planning->name ?? '-' }}
                    </div>
                    <div class="title-sub">
                        {{ $approval->created_at?->diffForHumans() }}
                    </div>
                  </td>
                  <td class="small text-secondary text-center">
                    {{ $approval->planning->department->name ?? '-' }}
                  </td>
                  <td class="small text-center">
                    {{ $approval->planning->creator->name ?? '-' }}
                  </td>
                  <td class="text-center">
                    <a class="btn-proses">Proses</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4">
                    <div class="empty-state">
                      <div class="material-symbols-outlined">inbox</div>
                      <div class="small">Tidak ada antrian persetujuan.</div>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="dash-card">
        <div class="card-header-custom">
          <h6>Riwayat Keputusan</h6>
        </div>

        @forelse($recentProcessed ?? [] as $item)
          @php
            $action = $item->action ?? '';
            $iconClass = match($action) {
              'Approved' => 'approved',
              'Rejected' => 'rejected',
              default    => 'revision',
            };
            $badgeClass = match($action) {
              'Approved' => 'badge-approved',
              'Rejected' => 'badge-rejected',
              default    => 'badge-revision',
            };
            $iconName = match($action) {
              'Approved' => 'check_circle',
              'Rejected' => 'cancel',
              default    => 'rate_review',
            };
          @endphp

          <div class="activity-item">
            <div class="activity-icon {{ $iconClass }}">
              <span class="material-symbols-outlined" style="font-size:1.15rem">
                {{ $iconName }}
              </span>
            </div>
            <div class="activity-content flex-grow-1">
              <div class="desc">{{ $item->title ?? '-' }}</div>
              <div class="meta">
                <span class="status-badge {{ $badgeClass }}">
                  <span class="dot"></span>
                  {{ $action }}
                </span>
                <span>·</span>
                <span>{{ $item->dept ?? '-' }}</span>
                <span>·</span>
                <span>{{ $item->updated_at?->translatedFormat('d M Y') ?? '-' }}</span>
              </div>
            </div>
          </div>
        @empty
          <div class="empty-state">
            <div class="material-symbols-outlined">history</div>
            <div class="small">Belum ada riwayat keputusan.</div>
          </div>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endif

{{-- ==================== ASSESSOR DASHBOARD ==================== --}}
@if($role === 'assessor')
<style>
  /* Stat cards dengan aksen warna */
  .a-stat {
    background: #fff;
    border-radius: 1rem;
    padding: 1.35rem 1.4rem;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    height: 100%;
    border: 1px solid transparent;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .a-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
  }
  .a-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    border-radius: 1rem 1rem 0 0;
  }
  .a-stat.blue::before   { background: linear-gradient(90deg, #1d4ed8, #3b82f6); }
  .a-stat.orange::before { background: linear-gradient(90deg, #ea580c, #f97316); }
  .a-stat.green::before  { background: linear-gradient(90deg, #059669, #10b981); }
  .a-stat.purple::before { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
  .a-stat.cyan::before   { background: linear-gradient(90deg, #0891b2, #22d3ee); }
  .a-stat.teal::before   { background: linear-gradient(90deg, #0d9488, #2dd4bf); }
  .a-stat.indigo::before { background: linear-gradient(90deg, #4f46e5, #818cf8); }
  .a-stat.red::before    { background: linear-gradient(90deg, #dc2626, #f87171); }

  .a-stat .icon {
    width: 48px; height: 48px; border-radius: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .a-stat.blue .icon   { background: rgba(29,78,216,0.12); color: #1d4ed8; }
  .a-stat.orange .icon { background: rgba(234,88,12,0.12); color: #ea580c; }
  .a-stat.green .icon  { background: rgba(5,150,105,0.12); color: #059669; }
  .a-stat.purple .icon { background: rgba(124,58,237,0.12); color: #7c3aed; }
  .a-stat.cyan .icon   { background: rgba(8,145,178,0.12); color: #0891b2; }
  .a-stat.teal .icon   { background: rgba(13,148,136,0.12); color: #0d9488; }
  .a-stat.indigo .icon { background: rgba(79,70,229,0.12); color: #4f46e5; }
  .a-stat.red .icon    { background: rgba(220,38,38,0.12); color: #dc2626; }

  .a-stat .label {
    font-size: 0.8rem;
    font-weight: 500;
    color: #64748b;
    margin-bottom: 0.15rem;
  }
  .a-stat .value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
  }

  /* Stage cards */
  .stage-card {
    background: #fff;
    border-radius: 0.9rem;
    padding: 1.1rem 1.15rem;
    border: 1px solid #e2e8f0;
    height: 100%;
    transition: all 0.2s;
  }
  .stage-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.08);
  }
  .stage-card .stage-icon {
    width: 40px; height: 40px; border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.65rem;
  }
  .stage-card .stage-label {
    font-size: 0.78rem;
    font-weight: 500;
    color: #64748b;
  }
  .stage-card .stage-value {
    font-size: 1.35rem;
    font-weight: 700;
    color: #0f172a;
  }

  /* Main content cards */
  .a-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    border: 1px solid #f1f5f9;
    height: 100%;
    overflow: hidden;
  }
  .a-card .a-card-header {
    padding: 1.1rem 1.35rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .a-card .a-card-header h6 {
    font-size: 0.95rem;
    font-weight: 650;
    margin: 0;
    color: #0f172a;
  }
  .a-card .a-card-body {
    padding: 0.5rem 0;
  }

  .badge-status {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 88px; font-size: 0.7rem; font-weight: 650;
    padding: 0.28rem 0.65rem; border-radius: 9999px;
  }
  .badge-draft     { background: #f1f5f9; color: #475569; }
  .badge-progress  { background: #fef3c7; color: #b45309; }
  .badge-completed { background: #d1fae5; color: #047857; }
  .badge-df        { background: #dbeafe; color: #1d4ed8; }

  .quick-link {
    display: flex; align-items: center; gap: 0.85rem;
    padding: 0.9rem 1.1rem;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
    text-decoration: none; color: #1e293b;
    transition: all 0.18s;
  }
  .quick-link:hover {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
    color: #1d4ed8;
    transform: translateX(4px);
  }
  .quick-link .ql-icon {
    width: 40px; height: 40px; border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .ql-icon.blue   { background: rgba(29,78,216,0.12); color: #1d4ed8; }
  .ql-icon.green  { background: rgba(5,150,105,0.12); color: #059669; }
  .ql-icon.orange { background: rgba(234,88,12,0.12); color: #ea580c; }
  .ql-icon.red    { background: rgba(220,38,38,0.12); color: #dc2626; }
  .ql-icon.purple { background: rgba(124,58,237,0.12); color: #7c3aed; }

  .table-assessor thead th {
    font-size: 0.7rem; font-weight: 650; text-transform: uppercase;
    letter-spacing: 0.04em; color: #94a3b8;
    border-bottom: 1px solid #f1f5f9; padding: 0.75rem 1.25rem;
  }
  .table-assessor tbody td {
    padding: 0.95rem 1.25rem; vertical-align: middle;
    border-bottom: 1px solid #f8fafc; font-size: 0.875rem;
  }
  .table-assessor tbody tr:last-child td { border-bottom: none; }
  .table-assessor tbody tr:hover { background: #f8fafc; }
</style>

{{-- Statistik utama --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="a-stat blue">
      <div class="d-flex align-items-center gap-3">
        <div class="icon">
          <span class="material-symbols-outlined" style="font-size:1.35rem">assignment</span>
        </div>
        <div>
          <div class="label">Total Assessment</div>
          <div class="value">{{ $stats['total_assessments'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="a-stat orange">
      <div class="d-flex align-items-center gap-3">
        <div class="icon">
          <span class="material-symbols-outlined" style="font-size:1.35rem">pending</span>
        </div>
        <div>
          <div class="label">Berjalan</div>
          <div class="value">{{ $stats['in_progress'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="a-stat green">
      <div class="d-flex align-items-center gap-3">
        <div class="icon">
          <span class="material-symbols-outlined" style="font-size:1.35rem">check_circle</span>
        </div>
        <div>
          <div class="label">Selesai</div>
          <div class="value">{{ $stats['completed'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="a-stat purple">
      <div class="d-flex align-items-center gap-3">
        <div class="icon">
          <span class="material-symbols-outlined" style="font-size:1.35rem">draft</span>
        </div>
        <div>
          <div class="label">Draft</div>
          <div class="value">{{ $stats['draft'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Ringkasan tahap proses --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stage-card">
      <div class="stage-icon" style="background:rgba(8,145,178,0.12);color:#0891b2">
        <span class="material-symbols-outlined" style="font-size:1.2rem">tune</span>
      </div>
      <div class="stage-label">Design Factor</div>
      <div class="stage-value">{{ $stats['df_done'] ?? 0 }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stage-card">
      <div class="stage-icon" style="background:rgba(79,70,229,0.12);color:#4f46e5">
        <span class="material-symbols-outlined" style="font-size:1.2rem">flag</span>
      </div>
      <div class="stage-label">Objectives</div>
      <div class="stage-value">{{ $stats['objectives_done'] ?? 0 }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stage-card">
      <div class="stage-icon" style="background:rgba(13,148,136,0.12);color:#0d9488">
        <span class="material-symbols-outlined" style="font-size:1.2rem">verified</span>
      </div>
      <div class="stage-label">Capability</div>
      <div class="stage-value">{{ $stats['capability_done'] ?? 0 }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stage-card">
      <div class="stage-icon" style="background:rgba(234,88,12,0.12);color:#ea580c">
        <span class="material-symbols-outlined" style="font-size:1.2rem">compare_arrows</span>
      </div>
      <div class="stage-label">Gap Analysis</div>
      <div class="stage-value">{{ $stats['gap_done'] ?? 0 }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stage-card">
      <div class="stage-icon" style="background:rgba(220,38,38,0.12);color:#dc2626">
        <span class="material-symbols-outlined" style="font-size:1.2rem">priority_high</span>
      </div>
      <div class="stage-label">Prioritas</div>
      <div class="stage-value">{{ $stats['priority_done'] ?? 0 }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stage-card">
      <div class="stage-icon" style="background:rgba(5,150,105,0.12);color:#059669">
        <span class="material-symbols-outlined" style="font-size:1.2rem">map</span>
      </div>
      <div class="stage-label">Roadmap</div>
      <div class="stage-value">{{ $stats['roadmap_done'] ?? 0 }}</div>
    </div>
  </div>
</div>

{{-- Assessment terbaru + Quick links --}}
<div class="row g-3 mb-4">
  <div class="col-12 col-lg-8">
    <div class="a-card">
      <div class="a-card-header">
        <h6>Assessment Terbaru</h6>
        <a href="{{ route('assessor.assessments.index') }}" class="small text-primary text-decoration-none fw-semibold">
          Lihat semua →
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-assessor mb-0 align-middle">
          <thead>
            <tr>
              <th class="text-center">Nama Assessment</th>
              <th class="text-center">Scope</th>
              <th class="text-center">Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentAssessments ?? [] as $a)
              <tr>
                <td>
                  <div class="fw-semibold" style="font-size:0.9rem">{{ $a->name ?? 'Assessment #'.$a->id }}</div>
                  <div class="text-secondary" style="font-size:0.72rem">
                    {{ $a->created_at?->translatedFormat('d M Y') }}
                  </div>
                </td>
                <td class="text-center small">
                  @if($a->is_all_department)
                    <span class="badge bg-info-subtle text-info">Semua</span>
                  @else
                    {{ $a->department->name ?? '—' }}
                  @endif
                </td>
                <td class="text-center">
                  @php
                    $statusClass = match($a->status) {
                      'draft'                => 'badge-draft',
                      'design_factor_filled' => 'badge-df',
                      'in_progress'          => 'badge-progress',
                      'closed'               => 'badge-completed',
                      default                => 'badge-draft',
                    };
                    $statusLabel = match($a->status) {
                      'draft'                => 'Draft',
                      'design_factor_filled' => 'DF Terisi',
                      'in_progress'          => 'Berjalan',
                      'closed'               => 'Selesai',
                      default                => $a->status,
                    };
                  @endphp
                  <span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
                </td>
                <td class="text-center">
                  @if($a->status === 'closed')
                    <a href="{{ route('assessor.assessments.laporan', $a) }}"
                       class="btn btn-sm btn-success rounded-pill px-3">
                      Laporan
                    </a>
                  @else
                    <a href="{{ route('assessor.design-factors.index', ['assessment_id' => $a->id]) }}"
                       class="btn btn-sm btn-primary rounded-pill px-3">
                      Lanjutkan
                    </a>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-secondary py-5 small">
                  Belum ada assessment.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="a-card">
      <div class="a-card-header">
        <h6>Akses Cepat</h6>
      </div>
      <div class="p-3 d-flex flex-column gap-2">
        <a href="{{ route('assessor.assessments.index') }}" class="quick-link">
          <div class="ql-icon blue">
            <span class="material-symbols-outlined" style="font-size:1.2rem">assignment</span>
          </div>
          <div>
            <div class="fw-semibold small">Assessment</div>
            <div class="text-secondary" style="font-size:0.72rem">Kelola assessment</div>
          </div>
        </a>
        <a href="{{ route('assessor.capability.index') }}" class="quick-link">
          <div class="ql-icon green">
            <span class="material-symbols-outlined" style="font-size:1.2rem">verified</span>
          </div>
          <div>
            <div class="fw-semibold small">Capability</div>
            <div class="text-secondary" style="font-size:0.72rem">Penilaian kapabilitas</div>
          </div>
        </a>
        <a href="{{ route('assessor.gap-analysis.index') }}" class="quick-link">
          <div class="ql-icon orange">
            <span class="material-symbols-outlined" style="font-size:1.2rem">compare_arrows</span>
          </div>
          <div>
            <div class="fw-semibold small">Gap Analysis</div>
            <div class="text-secondary" style="font-size:0.72rem">Analisis kesenjangan</div>
          </div>
        </a>
        <a href="{{ route('assessor.priority.index') }}" class="quick-link">
          <div class="ql-icon red">
            <span class="material-symbols-outlined" style="font-size:1.2rem">priority_high</span>
          </div>
          <div>
            <div class="fw-semibold small">Prioritas</div>
            <div class="text-secondary" style="font-size:0.72rem">Prioritas perbaikan</div>
          </div>
        </a>
        <a href="{{ route('assessor.roadmap.index') }}" class="quick-link">
          <div class="ql-icon purple">
            <span class="material-symbols-outlined" style="font-size:1.2rem">map</span>
          </div>
          <div>
            <div class="fw-semibold small">Roadmap</div>
            <div class="text-secondary" style="font-size:0.72rem">Roadmap transformasi</div>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Hasil Assessment --}}
@if(isset($latestResults) && $latestResults->count())
@php
  $levelNames = [
    0 => 'Incomplete',
    1 => 'Performed',
    2 => 'Managed',
    3 => 'Established',
    4 => 'Predictable',
    5 => 'Optimizing',
  ];

  $scaleOf = function (?float $percent): array {
      $percent = $percent ?? 0;
      if ($percent >= 85) return ['code' => 'F', 'label' => 'Fully Achieved', 'class' => 'score-high'];
      if ($percent >= 50) return ['code' => 'L', 'label' => 'Largely Achieved', 'class' => 'score-mid'];
      if ($percent >= 15) return ['code' => 'P', 'label' => 'Partially Achieved', 'class' => 'score-low'];
      return ['code' => 'N', 'label' => 'Not Achieved', 'class' => 'score-poor'];
  };
@endphp

<style>
  .result-table-card {
    background: #fff;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
    border: 1px solid #f1f5f9;
  }
  .result-table-card .table {
    margin-bottom: 0;
  }
  .result-table-card thead th {
    background: #f8fafc;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    padding: 0.9rem 1.25rem;
    white-space: nowrap;
  }
  .result-table-card tbody td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
  }
  .result-table-card tbody tr:last-child td {
    border-bottom: none;
  }
  .result-table-card tbody tr:hover {
    background: #f8fafc;
  }

  .level-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 700;
  }
  .level-0 { background: #fee2e2; color: #b91c1c; }
  .level-1 { background: #ffedd5; color: #c2410c; }
  .level-2 { background: #fef3c7; color: #b45309; }
  .level-3 { background: #dbeafe; color: #1d4ed8; }
  .level-4 { background: #d1fae5; color: #047857; }
  .level-5 { background: #ede9fe; color: #6d28d9; }

  .score-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 42px;
    height: 28px;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 700;
  }
  .score-high { background: #d1fae5; color: #047857; }
  .score-mid  { background: #dbeafe; color: #1d4ed8; }
  .score-low  { background: #fef3c7; color: #b45309; }
  .score-poor { background: #fee2e2; color: #b91c1c; }

  .status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 90px;
    height: 28px;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
  }
  .status-ok   { background: #d1fae5; color: #047857; }
  .status-wait { background: #fef3c7; color: #b45309; }
</style>

<div class="result-table-card">
  <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
    <h6 class="fw-semibold mb-0">Hasil Assessment Terbaru</h6>
    <a href="#" class="small text-primary text-decoration-none">
      Lihat semua
    </a>
  </div>

  <div class="table-responsive">
    <table class="table text-center">
      <thead>
        <tr>
          <th class="text-start ps-4">Perencanaan</th>
          <th>Unit Kerja</th>
          <th>Tahun</th>
          <th>Capaian Level</th>
          <th>Skala</th>
          <th>Status</th>
          <th class="pe-4">Tanggal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($latestResults as $item)
          @php
            $assessment = $item->assessment;
            $planning   = $assessment->implementation->planning ?? null;
            $title      = $planning->title ?? 'Assessment #'.($assessment->id ?? '-');
            $department = $planning->department->name ?? '—';
            $year       = $planning->year
                          ?? optional($assessment->closed_at)->format('Y')
                          ?? $item->created_at?->format('Y')
                          ?? '—';
            $level      = (int) ($item->capability_level ?? 0);
            $score      = (float) ($item->total_score ?? $item->readiness_score ?? 0);
            $scale      = $scaleOf($score);
            $status     = $assessment->status ?? 'Teruji';
            $date       = optional($assessment->closed_at)->translatedFormat('d M Y')
                          ?? $item->created_at?->translatedFormat('d M Y')
                          ?? '—';
          @endphp
          <tr>
            <td class="text-start ps-4 fw-medium">{{ $title }}</td>
            <td class="small text-secondary">{{ $department }}</td>
            <td>{{ $year }}</td>
            <td>
              <span class="level-pill level-{{ $level }}">
                Level {{ $level }} · {{ $levelNames[$level] ?? '' }}
              </span>
            </td>
            <td>
              <span class="score-badge {{ $scale['class'] }}"
                    title="{{ $scale['label'] }} ({{ number_format($score, 1) }}%)">
                {{ $scale['code'] }}
              </span>
            </td>
            <td>
              <span class="status-pill {{ in_array($status, ['Teruji', 'closed']) ? 'status-ok' : 'status-wait' }}">
                {{ $status === 'closed' ? 'Ditutup' : $status }}
              </span>
            </td>
            <td class="pe-4 small text-secondary">{{ $date }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection

