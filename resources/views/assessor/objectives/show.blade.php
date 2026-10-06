@extends('layouts.app')

@section('content')
<style>
  :root { --primary: #003fb1; }

  .page-header-card {
    position: relative; overflow: hidden; padding: 1.75rem 2rem;
    border-radius: 1rem; margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 40%, #3b82f6 75%, #60a5fa 100%);
    color: #fff; box-shadow: 0 12px 30px rgba(37, 99, 235, 0.25);
  }
  .page-header-card h1 { color: #fff; font-weight: 700; }
  .page-header-card p { color: rgba(255,255,255,.88) !important; }

  .stat-card {
    background: #fff; border-radius: 0.75rem; padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: center;
  }
  .stat-card .num { font-size: 1.75rem; font-weight: 700; }
  .stat-high { color: #dc2626; }
  .stat-med  { color: #d97706; }
  .stat-low  { color: #64748b; }

  .priority-section { margin-bottom: 1.5rem; }
  .priority-header {
    display: flex; align-items: center; gap: 0.5rem;
    font-weight: 700; font-size: 0.95rem; margin-bottom: 0.75rem;
  }
  .priority-badge {
    display: inline-flex; padding: 0.2rem 0.65rem; border-radius: 99px;
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.03em; text-transform: uppercase;
  }
  .badge-high   { background: #fee2e2; color: #b91c1c; }
  .badge-medium { background: #fef3c7; color: #b45309; }
  .badge-low    { background: #f1f5f9; color: #475569; }

  .obj-card {
    background: #fff; border-radius: 0.75rem; padding: 1rem 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 0.65rem;
    display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    border-left: 4px solid transparent; transition: .15s;
  }
  .obj-card:hover { background: #f8fafc; }
  .obj-card.high   { border-left-color: #dc2626; }
  .obj-card.medium { border-left-color: #d97706; }
  .obj-card.low    { border-left-color: #94a3b8; }

  .obj-code {
    display: inline-flex; padding: 0.2rem 0.5rem; border-radius: 0.35rem;
    background: rgba(0,63,177,0.08); color: var(--primary);
    font-size: 0.75rem; font-weight: 700; margin-right: 0.5rem;
  }
  .domain-chip {
    font-size: 0.7rem; padding: 0.15rem 0.45rem; border-radius: 0.3rem;
    background: #f1f5f9; color: #64748b; font-weight: 600;
  }
  .score-pill {
    min-width: 42px; height: 36px; border-radius: 0.45rem;
    background: var(--primary); color: #fff; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem;
  }

  .btn-primary {
    background-color: var(--primary) !important;
    border-color: var(--primary) !important;
  }
</style>

<div class="container-fluid px-4 py-3">

  <div class="page-header-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
      <div>
        <p class="small text-uppercase fw-semibold mb-1" style="letter-spacing:0.04em;opacity:.85">
          Assessor · Objectives
        </p>
        <h1 class="fs-4 fw-bold mb-1">Recommended Objectives</h1>
        <p class="mb-0 small" style="opacity:.85">{{ $assessment->name }}</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <form action="{{ route('assessor.objectives.generate', $assessment) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-light btn-sm d-inline-flex align-items-center gap-1">
            <span class="material-symbols-outlined" style="font-size:1.1rem">refresh</span>
            Generate Ulang
          </button>
        </form>
        <a href="{{ route('assessor.objectives.index') }}" class="btn btn-outline-light btn-sm">Kembali</a>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Stats --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="num text-primary">{{ $stats['total'] }}</div>
        <div class="small text-secondary">Total Objectives</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="num stat-high">{{ $stats['high'] }}</div>
        <div class="small text-secondary">High Priority</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="num stat-med">{{ $stats['medium'] }}</div>
        <div class="small text-secondary">Medium Priority</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="num stat-low">{{ $stats['low'] }}</div>
        <div class="small text-secondary">Low Priority</div>
      </div>
    </div>
  </div>

  {{-- Lists by priority --}}
  @foreach(['high' => 'High Priority', 'medium' => 'Medium Priority', 'low' => 'Low Priority'] as $key => $label)
    @if($grouped[$key]->isNotEmpty())
      <div class="priority-section">
        <div class="priority-header">
          <span class="priority-badge badge-{{ $key }}">{{ $label }}</span>
          <span class="text-secondary small">{{ $grouped[$key]->count() }} objectives</span>
        </div>

        @foreach($grouped[$key] as $row)
          <div class="obj-card {{ $key }}">
            <div>
              <span class="obj-code">{{ $row->code }}</span>
              <span class="fw-medium">
                {{ $row->cobit->description ?? $row->cobit->code ?? '-' }}
              </span>
              @if($row->domain)
                <span class="domain-chip ms-2">{{ $row->domain }}</span>
              @endif
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="priority-badge badge-{{ $key }}">{{ $key }}</span>
              <span class="score-pill">{{ number_format($row->score, 1) }}</span>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  @endforeach

  <div class="d-flex justify-content-end gap-2 mt-2 mb-3">
    <a href="{{ route('assessor.objectives.index') }}" class="btn btn-light px-4">Kembali</a>
    <a href="#" class="btn btn-primary px-4">
      Lanjut Capability Assessment
    </a>
  </div>
</div>
@endsection