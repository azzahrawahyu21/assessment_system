@extends('layouts.app')

@section('title', 'Persetujuan Perencanaan')
@section('page-title', 'Persetujuan Perencanaan')

@section('content')
<style>
  :root {
    --primary: #003fb1;
    --primary-hover: #00349a;
  }

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
  .page-header-card h1 { color: #fff; font-weight: 700; }
  .page-header-card p { color: rgba(255,255,255,.88) !important; }
  .page-header-card .accent {
    position: absolute; top: -90px; right: -70px; width: 260px; height: 260px;
    border-radius: 50%; background: rgba(255,255,255,.12); filter: blur(10px);
  }
  .page-header-card::before {
    content: ""; position: absolute; left: -60px; bottom: -80px; width: 220px; height: 220px;
    border-radius: 50%; background: rgba(255,255,255,.08);
  }
  .page-header-card::after {
    content: ""; position: absolute; top: 20px; right: 120px; width: 120px; height: 120px;
    border-radius: 50%; background: rgba(255,255,255,.06);
  }
  .page-header-card .accent,
  .page-header-card::before,
  .page-header-card::after { pointer-events: none; }

  .stat-card {
    background: #fff; border-radius: 0.75rem; padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); height: 100%;
  }
  .stat-icon {
    width: 44px; height: 44px; border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
  }
  .stat-icon.blue   { background: rgba(0, 63, 177, 0.1); color: var(--primary); }
  .stat-icon.green  { background: rgba(16, 185, 129, 0.1); color: #059669; }
  .stat-icon.orange { background: rgba(245, 158, 11, 0.1); color: #d97706; }
  .stat-icon.purple { background: rgba(139, 92, 246, 0.1); color: #7c3aed; }

  .filter-card {
    background: #fff; border-radius: 0.75rem; padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.25rem;
  }

  .table-card {
    background: #fff; border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;
  }
  .table-card .table { margin-bottom: 0; }
  .table-card thead th {
    background: #f8fafc; font-size: 0.72rem; font-weight: 600;
    letter-spacing: 0.04em; text-transform: uppercase; color: #64748b;
    border-bottom: 1px solid #e2e8f0; padding: 0.9rem 1.25rem; white-space: nowrap;
  }
  .table-card tbody td {
    padding: 1rem 1.25rem; vertical-align: middle;
    border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;
  }
  .table-card tbody tr:last-child td { border-bottom: none; }
  .table-card tbody tr:hover { background: #f8fafc; }

  .badge-status {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 90px; font-size: 0.72rem; font-weight: 600;
    padding: 0.3rem 0.7rem; border-radius: 9999px;
  }
  .badge-pending  { background: #fef3c7; color: #b45309; }
  .badge-approved { background: #d1fae5; color: #047857; }
  .badge-revision { background: #ffedd5; color: #c2410c; }

  .btn-action {
    width: 34px; height: 34px; padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 0.45rem;
  }

  .btn-outline-primary {
    color: var(--primary) !important;
    border-color: var(--primary) !important;
  }
  .btn-outline-primary:hover {
    background-color: rgba(0, 63, 177, 0.06) !important;
  }

  .form-control, .form-select {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.875rem;
  }
  .form-control:focus, .form-select:focus {
    background-color: #fff;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 63, 177, 0.12);
  }

  .role-chip {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.3rem 0.75rem; border-radius: 9999px;
    font-size: 0.75rem; font-weight: 600;
    background: rgba(255,255,255,0.2); color: #fff;
    border: 1px solid rgba(255,255,255,0.35);
  }

    .pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--gray-100);
  }
  .pagination-info {
    font-size: 0.85rem;
    color: var(--gray-500);
  }
  .pagination {
    margin: 0;
    gap: 0.35rem;
  }
  .pagination .page-item .page-link {
    border: none;
    background: var(--gray-50);
    color: var(--gray-700);
    border-radius: 0.55rem !important;
    min-width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all .2s ease;
  }
  .pagination .page-item .page-link:hover {
    background: var(--primary-soft);
    color: var(--primary);
  }
  .pagination .page-item.active .page-link {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
  }
  .pagination .page-item.disabled .page-link {
    background: transparent;
    color: var(--gray-300);
  }
</style>

@php
  $roleLabel = match($role ?? '') {
    'kajur'    => 'Kajur',
    'wadir'    => 'Wadir',
    'direktur' => 'Direktur',
    'keuangan' => 'Keuangan',
    default    => ucfirst($role ?? 'Approver'),
  };
@endphp

<div class="container-fluid px-4 py-3">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <div class="mb-2">
          <span class="role-chip">
            <span class="material-symbols-outlined" style="font-size:1rem">verified_user</span>
            {{ $roleLabel }}
          </span>
        </div>
        <h1 class="fs-4 fw-bold mb-1">Persetujuan Perencanaan</h1>
        <p class="mb-0 small">
          @if(($role ?? '') === 'keuangan')
            Daftar perencanaan yang menunggu proses anggaran.
          @else
            Daftar perencanaan yang menunggu keputusan Anda.
          @endif
        </p>
      </div>
    </div>
  </div>

  {{-- Statistik --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon blue">
          <span class="material-symbols-outlined">assignment</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Total Diproses</p>
          <h4 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon orange">
          <span class="material-symbols-outlined">pending</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Menunggu</p>
          <h4 class="fw-bold mb-0">{{ $stats['pending'] ?? 0 }}</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon green">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Disetujui</p>
          <h4 class="fw-bold mb-0">{{ $stats['approved'] ?? 0 }}</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon purple">
          <span class="material-symbols-outlined">edit_note</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Revisi</p>
          <h4 class="fw-bold mb-0">{{ $stats['revision'] ?? 0 }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- Filter --}}
  <div class="filter-card">
    <form method="GET" action="{{ route('planning-approvals.index') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-6">
          <label class="form-label small text-secondary mb-1">Cari</label>
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
              <span class="material-symbols-outlined text-secondary" style="font-size:1.15rem">search</span>
            </span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control border-start-0"
                   placeholder="Nama / nomor surat perencanaan...">
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary mb-1">Tahun</label>
          <select name="year" class="form-select">
            <option value="">Semua</option>
            @foreach($years ?? [] as $year)
              <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary mb-1">Status</label>
          <select name="approval_status" class="form-select">
            <option value="">Semua</option>
            <option value="pending"  @selected(request('approval_status') === 'pending')>Menunggu</option>
            <option value="approved" @selected(request('approval_status') === 'approved')>Disetujui</option>
            <option value="revision" @selected(request('approval_status') === 'revision')>Revisi</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-outline-primary flex-fill">
            <span class="material-symbols-outlined align-middle" style="font-size:1.1rem">filter_list</span>
            Filter
          </button>
          <a href="{{ route('planning-approvals.index') }}" class="btn btn-light" title="Reset">
            <span class="material-symbols-outlined" style="font-size:1.1rem">refresh</span>
          </a>
        </div>
      </div>
    </form>
  </div>

  {{-- Tabel --}}
  <div class="table-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th width="60" class="text-center">No</th>
            <th>Judul Perencanaan</th>
            <th class="text-center">Unit Kerja</th>
            <th class="text-center">Anggaran</th>
            <th class="text-center">Status Antrian</th>
            <th class="text-center">Diajukan</th>
            <th width="100" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($approvals as $index => $item)
            @php $p = $item->planning; @endphp
            <tr>
              <td class="text-center text-muted">{{ $approvals->firstItem() + $index }}</td>
              <td>
                <div class="fw-medium">{{ $p->name ?? '—' }}</div>
                <div class="small text-secondary">
                  {{ $p->no_letter ?? '' }}
                  @if($p?->planningType)
                    · {{ $p->planningType->name }}
                  @endif
                </div>
              </td>
              <td class="text-center">{{ $p->department->name ?? '—' }}</td>
              <td class="text-center">Rp {{ number_format($p->budget ?? 0, 0, ',', '.') }}</td>
              <td class="text-center">
                @php
                  $badge = match($item->status) {
                    'pending'  => ['badge-pending', 'Menunggu Anda'],
                    'approved' => ['badge-approved', 'Disetujui'],
                    'revision' => ['badge-revision', 'Revisi'],
                    default    => ['badge-pending', $item->status],
                  };
                @endphp
                <span class="badge-status {{ $badge[0] }}">{{ $badge[1] }}</span>
              </td>
              <td class="text-center small text-secondary">
                {{ $p?->created_at?->format('d M Y') ?? '—' }}
              </td>
              <td class="text-center">
                <a href="{{ route('planning-approvals.show', $item->id) }}"
                   class="btn btn-sm btn-outline-primary btn-action"
                   title="Proses">
                  <span class="material-symbols-outlined">visibility</span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-secondary py-5">
                <span class="material-symbols-outlined d-block mb-2" style="font-size:2.5rem;opacity:.4">inbox</span>
                Tidak ada perencanaan yang menunggu keputusan Anda.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($approvals->hasPages())
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Menampilkan <strong>{{ $approvals->firstItem() }}</strong>–<strong>{{ $approvals->lastItem() }}</strong>
          dari <strong>{{ $approvals->total() }}</strong> data
        </div>
        <div>
          {{ $approvals->withQueryString()->links() }}
        </div>
      </div>
    @endif
  </div>
</div>
@endsection