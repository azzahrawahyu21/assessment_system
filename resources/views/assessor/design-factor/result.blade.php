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

  .result-card {
    background: #fff; border-radius: 0.75rem; padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.25rem;
    height: 100%;
  }
  .result-card .card-title {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 1.05rem; font-weight: 600; margin-bottom: 1.15rem;
    padding-bottom: 0.85rem; border-bottom: 1px solid #eef0f2;
  }

  .df-code {
    display: inline-flex; padding: 0.25rem 0.55rem; border-radius: 0.35rem;
    background: rgba(0,63,177,0.08); color: var(--primary);
    font-size: 0.72rem; font-weight: 700;
  }

  .strategy-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.6rem 1.1rem; border-radius: 0.55rem;
    background: rgba(0,63,177,0.08); border: 1.5px solid rgba(0,63,177,0.2);
    color: var(--primary); font-weight: 600; font-size: 0.95rem;
  }

  .answer-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; padding: 0.7rem 0; border-bottom: 1px solid #f1f5f9;
  }
  .answer-row:last-child { border-bottom: none; }

  .score-pill {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; border-radius: 0.45rem;
    background: var(--primary); color: #fff; font-weight: 700; font-size: 0.9rem;
  }

  .chip {
    display: inline-flex; padding: 0.35rem 0.75rem; border-radius: 99px;
    background: rgba(0,63,177,0.08); color: var(--primary);
    font-size: 0.85rem; font-weight: 500; margin: 0.2rem;
  }

  /* ===== Tombol Modern ===== */
  .btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.7rem 1.4rem;
    border-radius: 0.65rem;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
  }

  .btn-back {
    background: #f1f5f9;
    color: #475569;
  }
  .btn-back:hover {
    background: #e2e8f0;
    color: #1e293b;
  }

  .btn-generate {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);
    color: #fff !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
  }
  .btn-generate:hover {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #2563eb 100%);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
    transform: translateY(-2px);
    color: #fff !important;
  }
  .btn-generate:active {
    transform: translateY(0);
  }
  .btn-generate .material-symbols-outlined {
    font-size: 1.2rem;
  }

  .action-bar {
    background: #fff;
    border-radius: 0.75rem;
    padding: 1.1rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
  }
</style>

<div class="container-fluid px-4 py-3">

  <div class="page-header-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
      <div>
        <p class="small text-uppercase fw-semibold mb-1" style="letter-spacing:0.04em;opacity:.85">
          Assessor · Hasil
        </p>
        <h1 class="fs-4 fw-bold mb-1">Hasil COBIT 2019 Design Factors</h1>
        <p class="mb-0 small" style="opacity:.85">
          {{ $assessment->name }}
        </p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('assessor.design-factors.show', $assessment) }}"
           class="btn btn-light btn-sm d-inline-flex align-items-center gap-1">
          <span class="material-symbols-outlined" style="font-size:1.1rem">edit</span>
          Edit Ulang
        </a>
        <a href="{{ route('assessor.design-factors.index') }}"
           class="btn btn-outline-light btn-sm">Kembali</a>
      </div>
    </div>
  </div>

  @foreach($designFactors as $df)
    @php
      $dfAnswers = $answers->get($df->id, collect());
    @endphp

    <div class="result-card">
      <div class="card-title">
        <span class="df-code">{{ $df->code }}</span>
        {{ $df->name }}
      </div>

      @if($dfAnswers->isEmpty())
        <p class="small text-secondary mb-0">Belum diisi.</p>

      @elseif($df->input_type === 'single_choice')
        @php $ans = $dfAnswers->first(); @endphp
        <div class="strategy-badge">
          <span class="material-symbols-outlined" style="font-size:1.2rem">check_circle</span>
          {{ $ans->option->name ?? '-' }}
        </div>
        @if($ans->option?->description)
          <p class="small text-secondary mt-2 mb-0">{{ $ans->option->description }}</p>
        @endif

      @elseif($df->input_type === 'multiple_choice')
        <div class="d-flex flex-wrap">
          @foreach($dfAnswers as $ans)
            <span class="chip">{{ $ans->option->name ?? '-' }}</span>
          @endforeach
        </div>

      @elseif($df->input_type === 'rating')
        @foreach($dfAnswers as $ans)
          <div class="answer-row">
            <div>
              @if($ans->option?->code)
                <span class="df-code me-1">{{ $ans->option->code }}</span>
              @endif
              <span class="fw-medium">{{ $ans->option->name ?? '-' }}</span>
            </div>
            <span class="score-pill">{{ (int) $ans->value }}</span>
          </div>
        @endforeach
      @endif
    </div>
  @endforeach

  {{-- Action Bar --}}
  <div class="action-bar mt-2 mb-3">
    <a href="{{ route('assessor.design-factors.index') }}" class="btn-action btn-back">
      <span class="material-symbols-outlined" style="font-size:1.15rem;">arrow_back</span>
      Kembali
    </a>

    <form action="{{ route('assessor.objectives.generate', $assessment) }}" method="POST" class="d-inline">
      @csrf
      <button type="submit" class="btn-action btn-generate">
        <span class="material-symbols-outlined">auto_awesome</span>
        Generate Objectives
      </button>
    </form>
  </div>

</div>
@endsection