@extends('layouts.app')

@section('content')
<style>
  :root { --primary: #003fb1; --primary-hover: #00349a; }

  .page-header-card {
    position: relative; overflow: hidden; padding: 1.75rem 2rem;
    border-radius: 1rem; margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 40%, #3b82f6 75%, #60a5fa 100%);
    color: #fff; box-shadow: 0 12px 30px rgba(37, 99, 235, 0.25);
  }
  .page-header-card h1 { color: #fff; font-weight: 700; }
  .page-header-card p { color: rgba(255,255,255,.88) !important; }

  .table-card {
    background: #fff; border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;
  }
  .table-card thead th {
    background: #f8fafc; font-size: 0.72rem; font-weight: 600;
    letter-spacing: 0.04em; text-transform: uppercase; color: #64748b;
    border-bottom: 1px solid #e2e8f0; padding: 0.9rem 1.25rem;
  }
  .table-card tbody td {
    padding: 1rem 1.25rem; vertical-align: middle;
    border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;
  }
  .table-card tbody tr:last-child td { border-bottom: none; }
  .table-card tbody tr:hover { background: #f8fafc; }

  .btn-action {
    width: 34px; height: 34px; padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 0.5rem;
  }
  .btn-primary {
    background-color: var(--primary) !important;
    border-color: var(--primary) !important;
  }
  .badge-soft-success {
    background: #dcfce7; color: #166534; font-weight: 600;
  }
  .badge-soft-warning {
    background: #fef3c7; color: #92400e; font-weight: 600;
  }
  .pagination-wrapper {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 1rem; margin-top: 1.5rem;
    padding-top: 1.25rem; border-top: 1px solid var(--gray-100);
  }
  .pagination-info {
    font-size: 0.85rem; color: var(--gray-500);
  }
  .pagination {
    margin: 0; gap: 0.35rem;
  }
  .pagination .page-item .page-link {
    border: none; background: var(--gray-50); color: var(--gray-700);
    border-radius: 0.55rem !important; min-width: 38px; height: 38px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.875rem; font-weight: 500; transition: all .2s ease;
  }
  .pagination .page-item .page-link:hover {
    background: var(--primary-soft); color: var(--primary);
  }
  .pagination .page-item.active .page-link {
    background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
  }
  .pagination .page-item.disabled .page-link {
    background: transparent; color: var(--gray-300);
  }
</style>

<div class="container-fluid px-4 py-3">

  <div class="page-header-card">
    <h1 class="fs-4 fw-bold mb-1">Governance &amp; Management Objectives</h1>
    <p class="mb-0 small">Daftar objective yang direkomendasikan berdasarkan Design Factors.</p>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="table-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th width="60" class="text-center">No</th>
            <th>Nama Assessment</th>
            <th class="text-center">Department</th>
            <th class="text-center">Status Objectives</th>
            <th width="140" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assessments as $i => $assessment)
            <tr>
              <td class="text-center text-muted">{{ $i + 1 }}</td>
              <td class="fw-medium">{{ $assessment->name }}</td>
              <td class="text-center">
                @if($assessment->is_all_departments)
                  <span class="badge bg-info-subtle text-info">Semua</span>
                @else
                  {{ $assessment->department->name ?? '-' }}
                @endif
              </td>
              <td class="text-center">
                @if(($assessment->objective_priorities_count ?? 0) > 0)
                  <span class="badge badge-soft-success rounded-pill px-3">
                    {{ $assessment->objective_priorities_count }} objectives
                  </span>
                @else
                  <span class="badge badge-soft-warning rounded-pill px-3">Belum digenerate</span>
                @endif
              </td>
              <td class="text-center">
                <div class="d-inline-flex gap-1">
                  @if(($assessment->objective_priorities_count ?? 0) > 0)
                    <a href="{{ route('assessor.objectives.show', $assessment) }}"
                       class="btn btn-sm btn-outline-primary btn-action" title="Lihat Hasil">
                      <span class="material-symbols-outlined">visibility</span>
                    </a>
                    <form action="{{ route('assessor.objectives.generate', $assessment) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-secondary btn-action" title="Generate Ulang">
                        <span class="material-symbols-outlined">refresh</span>
                      </button>
                    </form>
                  @else
                    <form action="{{ route('assessor.objectives.generate', $assessment) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-primary btn-action" title="Generate">
                        <span class="material-symbols-outlined">auto_awesome</span>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-5 text-secondary">
                <span class="material-symbols-outlined d-block mb-2" style="font-size:2.5rem;opacity:.4">inbox</span>
                Belum ada assessment yang sudah mengisi Design Factor.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    {{-- Pagination --}}
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