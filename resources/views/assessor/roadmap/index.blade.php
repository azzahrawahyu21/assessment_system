@extends('layouts.app')

@section('title', 'Roadmap Transformasi Digital')

@section('content')
<style>
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
  .table-card tbody tr:hover { background: #f8fafc; }
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
  <div class="page-header-card">
    <div>
      <h1 class="fs-4 fw-bold mb-1">Roadmap Transformasi Digital</h1>
      <p class="mb-0 small">Roadmap berdasarkan prioritas perbaikan dari hasil assessment.</p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th width="60" class="text-center">No</th>
            <th>Nama Assessment</th>
            <th class="text-center">Scope</th>
            <th class="text-center">Judgment</th>
            <th class="text-center">Perlu Perbaikan</th>
            <th width="140" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assessments as $index => $assessment)
            <tr>
              <td class="text-center text-muted">{{ $index + 1 }}</td>
              <td>
                <div class="fw-medium">{{ $assessment->name ?? 'Assessment #'.$assessment->id }}</div>
              </td>
              <td class="text-center">
                @if($assessment->is_all_departments)
                  <span class="badge bg-info-subtle text-info">Semua Department</span>
                @else
                  {{ $assessment->department->name ?? '—' }}
                @endif
              </td>
              <td class="text-center">
                @if($assessment->has_judgment)
                  <span class="badge bg-success-subtle text-success">{{ $assessment->total_judged }} objective</span>
                @else
                  <span class="badge bg-secondary-subtle text-secondary">Belum ada</span>
                @endif
              </td>
              <td class="text-center">
                @if($assessment->need_improvement > 0)
                  <span class="badge bg-warning-subtle text-warning">{{ $assessment->need_improvement }} gap</span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td class="text-center">
                @if($assessment->has_judgment)
                  <a href="{{ route('assessor.roadmap.show', $assessment) }}"
                     class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-3">
                    <span class="material-symbols-outlined" style="font-size:1.1rem;">map</span>
                    Lihat
                  </a>
                @else
                  <span class="text-muted small">Lakukan Judgment dulu</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-secondary">
                Belum ada assessment
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