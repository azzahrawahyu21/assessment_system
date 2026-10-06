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
  .badge-review { background: #fef3c7; color: #b45309; }
  .badge-approved { background: #d1fae5; color: #047857; }
  .badge-revision { background: #fee2e2; color: #b91c1c; }

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

  /* Modal Delete */
  .modal-delete .modal-content {
    border: none; border-radius: 1rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
  }
  .modal-delete .modal-icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: #fef2f2; color: #dc2626;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
  }
  .modal-delete .modal-icon .material-symbols-outlined { font-size: 2rem; }
</style>

<div class="container-fluid px-4 py-3">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Perencanaan</h1>
        <p class="mb-0 small">Daftar seluruh data perencanaan kegiatan institusi.</p>
      </div>
      <a href="{{ route('plannings.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3">
        <span class="material-symbols-outlined" style="font-size: 1.2rem;">add</span>
        Tambah Perencanaan
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
          <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon orange">
          <span class="material-symbols-outlined">edit_note</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Direvisi</p>
          <h4 class="fw-bold mb-0">{{ $stats['revision'] }}</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center gap-3">
        <div class="stat-icon blue">
          <span class="material-symbols-outlined">send</span>
        </div>
        <div>
          <p class="text-secondary small mb-0">Terkirim</p>
          <h4 class="fw-bold mb-0">{{ $stats['submitted'] }}</h4>
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
          <h4 class="fw-bold mb-0">{{ $stats['approved'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- Filter --}}
  <div class="filter-card">
    <form action="{{ route('plannings.index') }}" method="GET">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label small text-secondary mb-1">Cari</label>
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
              <span class="material-symbols-outlined text-secondary" style="font-size: 1.15rem;">search</span>
            </span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control border-start-0" placeholder="Nama perencanaan...">
          </div>
        </div>
        <div class="col-md-2">
          <label class="form-label small text-secondary mb-1">Tahun</label>
          <select name="year" class="form-select">
            <option value="">Semua</option>
            @foreach($years as $year)
              <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small text-secondary mb-1">Status</label>
            <select name="status" class="form-select">
            <option value="">Semua</option>
            <option value="draft"     {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Terkirim</option>
            <option value="revision"  {{ request('status') == 'revision' ? 'selected' : '' }}>Revisi</option>
            <option value="approved"  {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
            </select>
        </div>
        {{-- <div class="col-md-3">
          <label class="form-label small text-secondary mb-1">Unit Kerja</label>
          <select name="department_id" class="form-select">
            <option value="">Semua</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id_department }}"
                {{ request('department_id') == $dept->id_department ? 'selected' : '' }}>
                {{ $dept->name }}
                </option>
            @endforeach
          </select>
        </div> --}}
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-outline-primary flex-fill">
            <span class="material-symbols-outlined align-middle" style="font-size: 1.1rem;">filter_list</span>
            Filter
          </button>
          <a href="{{ route('plannings.index') }}" class="btn btn-light" title="Reset">
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
                <th>Judul</th>
                <th class="text-center">Tanggal</th>
                {{-- <th class="text-center">Unit Kerja</th> --}}
                <th class="text-center">Anggaran</th>
                <th class="text-center">Status</th>
                <th class="text-center">Proposal</th>
                <th width="180" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plannings as $index => $planning)
                <tr>
                    <td class="text-center text-muted">{{ $plannings->firstItem() + $index }}</td>

                    {{-- Judul + No Surat --}}
                    <td>
                    <div class="fw-medium">{{ $planning->name }}</div>
                    <div class="small text-secondary">{{ $planning->no_letter }}</div>
                    </td>

                    {{-- Tanggal --}}
                    <td class="text-center small text-secondary">
                    {{ $planning->date?->format('d M Y') ?? '—' }}
                    </td>

                    {{-- Unit Kerja dari relasi department --}}
                    {{-- <td class="text-center">
                    {{ $planning->department->name ?? '—' }}
                    </td> --}}

                    {{-- Anggaran --}}
                    <td class="text-center">
                    Rp {{ number_format($planning->budget ?? 0, 0, ',', '.') }}
                    </td>

                    {{-- Status --}}
                    <td class="text-center">
                    @php
                        $statusClass = match($planning->status) {
                        'draft'     => 'badge-draft',
                        'submitted' => 'badge-review',
                        'revision'  => 'badge-revision',
                        'approved'  => 'badge-approved',
                        default     => 'badge-draft',
                        };
                        $statusLabel = match($planning->status) {
                        'draft'     => 'Draft',
                        'submitted' => 'Terkirim',
                        'revision'  => 'Revisi',
                        'approved'  => 'Disetujui',
                        default     => $planning->status,
                        };
                    @endphp
                    <span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>

                    <td class="text-center">
                    @if(strtolower($planning->status) === 'approved')
                        <a href="{{ route('plannings.proposal', $planning->id_plan) }}"
                        class="btn btn-sm btn-outline-primary btn-action"
                        title="Lihat Proposal + TTD"
                        target="_blank">
                        <span class="material-symbols-outlined">description</span>
                        </a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="text-center">
                    <div class="d-inline-flex gap-1 justify-content-center flex-wrap">

                        @php
                        $status = strtolower(trim((string) $planning->status));
                        @endphp

                        {{-- Lihat --}}
                        <a href="{{ route('plannings.show', $planning->id_plan) }}"
                        class="btn btn-sm btn-outline-secondary btn-action"
                        title="Lihat">
                        <span class="material-symbols-outlined">visibility</span>
                        </a>

                        {{-- Edit: sembunyikan jika approved --}}
                        @if($status !== 'approved')
                        <a href="{{ route('plannings.edit', $planning->id_plan) }}"
                            class="btn btn-sm btn-outline-primary btn-action"
                            title="Edit">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        @endif

                        @if(in_array($status, ['draft', 'revision'], true))
                        <form action="{{ route('plannings.submit', $planning->id_plan) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Ajukan perencanaan ini ke Kajur?')">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm btn-outline-success btn-action"
                                    title="Ajukan">
                            <span class="material-symbols-outlined">send</span>
                            </button>
                        </form>
                        @endif

                        @if($status !== 'approved')
                        <button type="button"
                                class="btn btn-sm btn-outline-danger btn-action btn-delete"
                                title="Hapus"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal"
                                data-name="{{ $planning->name }}"
                                data-url="{{ route('plannings.destroy', $planning->id_plan) }}">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                        @endif
                    </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-secondary">
                    <span class="material-symbols-outlined d-block mb-2" style="font-size: 2.5rem; opacity:.4;">inbox</span>
                    Belum ada data perencanaan
                    </td>
                </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        @if($plannings->hasPages())
          <div class="pagination-wrapper">
            <div class="pagination-info">
              Menampilkan <strong>{{ $plannings->firstItem() }}</strong>–<strong>{{ $plannings->lastItem() }}</strong>
              dari <strong>{{ $plannings->total() }}</strong> data
            </div>
            <div>
              {{ $plannings->withQueryString()->links() }}
            </div>
          </div>
        @endif
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade modal-delete" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-4 p-md-5">
        <div class="modal-icon">
          <span class="material-symbols-outlined">warning</span>
        </div>
        <h5 class="fw-bold mb-2">Hapus Perencanaan?</h5>
        <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus</p>
        <p class="fw-semibold text-dark mb-4" id="deletePlanningName">—</p>

        <form id="deleteForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger px-4">
              <span class="material-symbols-outlined me-1" style="font-size:18px;vertical-align:middle;">delete</span>
              Ya, Hapus
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function () {
      document.getElementById('deletePlanningName').textContent = `"${this.dataset.name}"`;
      document.getElementById('deleteForm').action = this.dataset.url;
    });
  });
</script>
@endpush