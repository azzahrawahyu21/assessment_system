@extends('layouts.app')

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
  .page-header-card .btn-primary {
    background: #fff !important; color: #2563eb !important; border: none !important;
    font-weight: 600; transition: .25s;
  }
  .page-header-card .btn-primary:hover {
    background: #eff6ff !important; color: #1d4ed8 !important; transform: translateY(-2px);
  }
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
  .stat-icon.blue { background: rgba(0, 63, 177, 0.1); color: var(--primary); }
  .stat-icon.green { background: rgba(16, 185, 129, 0.1); color: #059669; }
  .stat-icon.orange { background: rgba(245, 158, 11, 0.1); color: #d97706; }
  .stat-icon.red { background: rgba(239, 68, 68, 0.1); color: #dc2626; }

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
    border-bottom: 1px solid #e2e8f0; padding: 0.9rem 1.25rem;
    vertical-align: middle; white-space: nowrap;
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
  .badge-draft { background: #f1f5f9; color: #475569; }
  .badge-sent { background: #dbeafe; color: #1d4ed8; }
  .badge-progress { background: #fef3c7; color: #b45309; }
  .badge-completed { background: #d1fae5; color: #047857; }

  .btn-action {
    width: 34px; height: 34px; padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 0.5rem;
  }
  .btn-action .material-symbols-outlined { font-size: 1.15rem; }

  .btn-primary { background-color: var(--primary) !important; border-color: var(--primary) !important; }
  .btn-primary:hover { background-color: var(--primary-hover) !important; border-color: var(--primary-hover) !important; }
  .btn-outline-primary { color: var(--primary) !important; border-color: var(--primary) !important; }
  .btn-outline-primary:hover { background-color: rgba(0, 63, 177, 0.06) !important; }

  .form-control, .form-select {
    background-color: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; font-size: 0.875rem;
  }
  .form-control:focus, .form-select:focus {
    background-color: #fff; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 63, 177, 0.12);
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

<div class="container-fluid px-4 py-3">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Assessment</h1>
        <p class="mb-0 small">Daftar assessment yang dibuat oleh assessor.</p>
      </div>
      <a href="{{ route('assessor.assessments.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3">
        <span class="material-symbols-outlined" style="font-size: 1.2rem;">add</span>
        Tambah Assessment
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Statistik --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon blue">
          <span class="material-symbols-outlined">assignment</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Total</p>
          <h4 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon orange">
          <span class="material-symbols-outlined">edit_note</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Draft</p>
          <h4 class="fw-bold mb-0">{{ $stats['draft'] ?? 0 }}</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon blue">
          <span class="material-symbols-outlined">send</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Berjalan</p>
          <h4 class="fw-bold mb-0">{{ $stats['in_progress'] ?? 0 }}</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon green">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Selesai</p>
          <h4 class="fw-bold mb-0">{{ $stats['completed'] ?? 0 }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- Filter --}}
  <div class="filter-card">
    <form action="{{ route('assessor.assessments.index') }}" method="GET">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label small text-secondary mb-1">Cari</label>
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
              <span class="material-symbols-outlined text-secondary" style="font-size: 1.15rem;">search</span>
            </span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control border-start-0" placeholder="Nama assessment...">
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary mb-1">Prodi/UPA</label>
          <select name="department_id" class="form-select">
            <option value="">Semua</option>
            @foreach($departments ?? [] as $dept)
                <option value="{{ $dept->id_department }}"
                    {{ request('department_id') == $dept->id_department ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small text-secondary mb-1">Status</label>
          <select name="status" class="form-select">
            <option value="">Semua</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="design_factor_filled" {{ request('status') == 'design_factor_filled' ? 'selected' : '' }}>DF Terisi</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Berjalan</option>
            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Selesai</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-outline-primary flex-fill">
            <span class="material-symbols-outlined align-middle" style="font-size: 1.1rem;">filter_list</span>
            Filter
          </button>
          <a href="{{ route('assessor.assessments.index') }}" class="btn btn-light" title="Reset">
            <span class="material-symbols-outlined" style="font-size: 1.1rem;">refresh</span>
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
            <th>Nama Assessment</th>
            <th class="text-center">Yang Diassessment</th>
            <th class="text-center">Status</th>
            <th width="140" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assessments as $index => $assessment)
            <tr>
              <td class="text-center text-muted">{{ $assessments->firstItem() + $index }}</td>
              <td>
                <div class="fw-medium">{{ $assessment->name ?? 'Assessment #'.$assessment->id }}</div>
              </td>
            <td class="text-center">
            @if($assessment->is_all_department)
                <span class="badge bg-info-subtle text-info">Semua Department</span>
            @else
                {{ $assessment->department->name ?? '—' }}
            @endif
            </td>
            <td class="text-center">
            @php
                $statusClass = match($assessment->status) {
                'draft'                 => 'badge-draft',
                'design_factor_filled'  => 'badge-sent',
                'in_progress'           => 'badge-progress',
                'closed'                => 'badge-completed',
                default                 => 'badge-draft',
                };
                $statusLabel = match($assessment->status) {
                'draft'                 => 'Draft',
                'design_factor_filled'  => 'DF Terisi',
                'in_progress'           => 'Berjalan',
                'closed'                => 'Selesai',
                default                 => $assessment->status,
                };
            @endphp
            <span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
            </td>
              {{-- <td class="text-center">
                <a href="{{ route('assessor.design-factors.index', ['assessment_id' => $assessment->id]) }}"
                   class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-3">
                  <span class="material-symbols-outlined" style="font-size: 1.1rem;">play_arrow</span>
                  Mulai
                </a>
              </td> --}}
              <td class="text-center">
                <div class="d-flex justify-content-center gap-1 flex-wrap">
                  {{-- Tombol Mulai (selalu ada selama belum closed) --}}
                  @if($assessment->status !== 'closed')
                    <a href="{{ route('assessor.design-factors.index', ['assessment_id' => $assessment->id]) }}"
                      class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-2"
                      title="Mulai">
                      <span class="material-symbols-outlined" style="font-size: 1.1rem;">play_arrow</span>
                      Mulai
                    </a>
                  @endif

                  {{-- Tombol Laporan (muncul jika status closed / sudah selesai) --}}
                  @if($assessment->status === 'closed')
                    <a href="{{ route('assessor.assessments.laporan', $assessment) }}"
                      class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 px-2"
                      title="Laporan">
                      <span class="material-symbols-outlined" style="font-size: 1.1rem;">description</span>
                      Laporan
                    </a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-5 text-secondary">
                <span class="material-symbols-outlined d-block mb-2" style="font-size: 2.5rem; opacity:.4;">inbox</span>
                Belum ada data assessment
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($assessments->hasPages())
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Menampilkan <strong>{{ $assessments->firstItem() }}</strong>–<strong>{{ $assessments->lastItem() }}</strong>
          dari <strong>{{ $assessments->total() }}</strong> data
        </div>
        <div>
          {{ $assessments->withQueryString()->links() }}
        </div>
      </div>
    @endif
  </div>
</div>
@endsection