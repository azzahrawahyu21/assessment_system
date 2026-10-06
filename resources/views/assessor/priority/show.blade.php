@extends('layouts.app')

@section('title', 'Prioritas Perbaikan - ' . ($assessment->name ?? ''))

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

  .priority-card {
    background: #fff; border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden;
  }
  .priority-card .card-header {
    background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    padding: 1rem 1.5rem; font-weight: 700; font-size: 0.95rem;
    letter-spacing: 0.03em; text-transform: uppercase; color: #334155;
  }
  .table-priority thead th {
    background: #f1f5f9; font-size: 0.75rem; font-weight: 600;
    text-transform: uppercase; color: #64748b; letter-spacing: 0.04em;
    padding: 0.85rem 1rem; border-bottom: 1px solid #e2e8f0;
  }
  .table-priority tbody td {
    padding: 0.9rem 1rem; vertical-align: middle;
    border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;
  }
  .table-priority tbody tr:last-child td { border-bottom: none; }
  .table-priority tbody tr:hover { background: #f8fafc; }

  .priority-badge {
    display: inline-block; padding: 0.25rem 0.7rem;
    border-radius: 9999px; font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.03em;
  }
  .priority-very-high { background: #fee2e2; color: #b91c1c; }
  .priority-high      { background: #ffedd5; color: #c2410c; }
  .priority-medium    { background: #fef9c3; color: #a16207; }
  .priority-low       { background: #f1f5f9; color: #475569; }
  .priority-none      { background: #e2e8f0; color: #64748b; }

  .status-dot {
    display: inline-block; width: 10px; height: 10px;
    border-radius: 50%; margin-right: 6px;
  }
</style>

<div class="container-fluid px-4 py-3">
  <div class="page-header-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Prioritas Perbaikan</h1>
        <p class="mb-0 small">
          {{ $assessment->name ?? 'Assessment #'.$assessment->id }}
          @if($assessment->department)
            · {{ $assessment->department->name }}
          @elseif($assessment->is_all_department)
            · Semua Department
          @endif
        </p>
      </div>
      <a href="{{ route('assessor.priority.index') }}" class="btn btn-light btn-sm d-inline-flex align-items-center gap-1">
        <span class="material-symbols-outlined" style="font-size:1.1rem;">arrow_back</span>
        Kembali
      </a>
    </div>
  </div>

  <div class="priority-card">
    <div class="table-responsive">
      <table class="table table-priority mb-0">
        <thead>
          <tr>
            <th class="text-center">Domain</th>
            <th class="text-center">Gap</th>
            <th class="text-center">Risk</th>
            <th class="text-center">Impact</th>
            <th class="text-center">Priority</th>
            <th class="text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $item)
            <tr>
              <td>
                <div class="fw-semibold">{{ $item->code }}</div>
                <div class="small text-muted">{{ $item->name }}</div>
              </td>
              <td class="text-center fw-medium">{{ $item->gap }}</td>
              <td class="text-center">{{ $item->risk }}</td>
              <td class="text-center">{{ $item->impact }}</td>
              <td class="text-center">
                @php
                  $badgeClass = match($item->priority_label) {
                    'VERY HIGH' => 'priority-very-high',
                    'HIGH'      => 'priority-high',
                    'MEDIUM'    => 'priority-medium',
                    'LOW'       => 'priority-low',
                    default     => 'priority-none',
                  };
                @endphp
                <span class="priority-badge {{ $badgeClass }}">{{ $item->priority_label }}</span>
              </td>
              <td class="text-center">
                <span class="status-dot" style="background: {{ $item->dot_color }}"></span>
                {{ $item->status_label }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-secondary">
                Belum ada objective pada assessment ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($items->isNotEmpty())
    <div class="mt-3 text-end">
      <a href="{{ route('assessor.roadmap.show', $assessment) }}"
         class="btn btn-primary d-inline-flex align-items-center gap-2">
        <span class="material-symbols-outlined">map</span>
        Lihat Roadmap
      </a>
    </div>
  @endif
</div>
@endsection