@extends('layouts.app')

@section('title', 'Roadmap - ' . ($assessment->name ?? ''))

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

  .roadmap-card {
    background: #fff; border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden;
  }
  .roadmap-card .card-header {
    background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    padding: 1rem 1.5rem; font-weight: 700; font-size: 0.95rem;
    letter-spacing: 0.03em; text-transform: uppercase; color: #334155;
  }

  /* Legend */
  .phase-legend {
    display: flex; flex-wrap: wrap; gap: 1.25rem;
    padding: 1.1rem 1.5rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
  }
  .phase-legend-item {
    display: flex; align-items: center; gap: 0.45rem;
    font-size: 0.84rem; font-weight: 600; color: #475569;
  }
  .phase-dot {
    width: 11px; height: 11px; border-radius: 50%;
  }
  .phase-dot.short  { background: #ef4444; }
  .phase-dot.medium { background: #f97316; }
  .phase-dot.long   { background: #3b82f6; }

  /* ===== Zigzag Flow ===== */
  .roadmap-flow {
    padding: 2rem 1.25rem 2.5rem;
  }

  .flow-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    margin-bottom: 0.25rem;
  }

  /* Baris genap → ke kanan */
  .flow-row.is-right { flex-direction: row; }

  /* Baris ganjil → ke kiri */
  .flow-row.is-left  { flex-direction: row-reverse; }

  .flow-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }

  /* Card */
.roadmap-box {
    background: #fff;
    border: 1.5px solid #cbd5e1;
    border-radius: 0.75rem;
    padding: 1.15rem 1.1rem;
    min-width: 200px;
    max-width: 240px;
    width: 100%;
    text-align: center;
    transition: all 0.2s ease;
  }
  .roadmap-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.08);
  }
  .roadmap-box.phase-short {
    border-color: #fca5a5;
    background: linear-gradient(to bottom, #fff 55%, #fef2f2);
  }
  .roadmap-box.phase-medium {
    border-color: #fdba74;
    background: linear-gradient(to bottom, #fff 55%, #fff7ed);
  }
  .roadmap-box.phase-long {
    border-color: #93c5fd;
    background: linear-gradient(to bottom, #fff 55%, #eff6ff);
  }
  .roadmap-box.no-gap {
    opacity: 0.78;
    border-style: dashed;
  }

  .roadmap-box .code {
    font-weight: 700;
    font-size: 1.05rem;
    color: #1e40af;
  }
  .roadmap-box .name {
    font-size: 0.82rem;
    color: #475569;
    margin-top: 0.35rem;
    line-height: 1.4;
    min-height: 2.3em;
  }
  .roadmap-box .meta {
    margin-top: 0.55rem;
    display: flex;
    justify-content: center;
    gap: 0.4rem;
    flex-wrap: wrap;
  }
  .badge-phase {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.18rem 0.55rem;
    border-radius: 9999px;
    letter-spacing: 0.02em;
  }
  .badge-short  { background: #fee2e2; color: #b91c1c; }
  .badge-medium { background: #ffedd5; color: #c2410c; }
  .badge-long   { background: #dbeafe; color: #1d4ed8; }

  .badge-gap {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.18rem 0.5rem;
    border-radius: 9999px;
    background: #f1f5f9;
    color: #64748b;
  }
  .roadmap-box.has-gap .badge-gap {
    background: #ffedd5;
    color: #c2410c;
  }

  /* Panah horizontal */
  .flow-arrow {
    color: #94a3b8;
    display: flex;
    align-items: center;
    flex-shrink: 0;
  }
  .flow-arrow .material-symbols-outlined {
    font-size: 1.55rem;
  }

  /* Panah turun */
  .flow-down-wrap {
    display: flex;
    margin: 0.15rem 0 0.5rem;
  }
  .flow-down-wrap.from-right {
    justify-content: flex-end;
    padding-right: 8%;
  }
  .flow-down-wrap.from-left {
    justify-content: flex-start;
    padding-left: 8%;
  }
  .flow-down {
    color: #94a3b8;
  }
  .flow-down .material-symbols-outlined {
    font-size: 1.6rem;
  }

  /* Responsive: di HP jadi vertikal ke bawah */
  @media (max-width: 991.98px) {
    .flow-row {
      flex-direction: column !important;
      gap: 0.4rem;
    }
    .flow-item {
      flex-direction: column;
    }
    .flow-arrow {
      transform: rotate(90deg);
      margin: 0.1rem 0;
    }
    .flow-down-wrap {
      display: none;
    }
    .roadmap-box {
      min-width: 210px;
      max-width: 260px;
    }
  }
</style>

<div class="container-fluid px-4 py-3">
  <div class="page-header-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Roadmap Transformasi Digital</h1>
        <p class="mb-0 small">
          {{ $assessment->name ?? 'Assessment #'.$assessment->id }}
          @if($assessment->department)
            · {{ $assessment->department->name }}
          @elseif($assessment->is_all_department)
            · Semua Department
          @endif
        </p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('assessor.priority.show', $assessment) }}"
           class="btn btn-light btn-sm d-inline-flex align-items-center gap-1">
          <span class="material-symbols-outlined" style="font-size:1.1rem;">priority_high</span>
          Prioritas
        </a>
        <a href="{{ route('assessor.roadmap.index') }}"
           class="btn btn-light btn-sm d-inline-flex align-items-center gap-1">
          <span class="material-symbols-outlined" style="font-size:1.1rem;">arrow_back</span>
          Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="roadmap-card">
    {{-- Legend --}}
    <div class="phase-legend">
      <div class="phase-legend-item">
        <span class="phase-dot short"></span> 2026 Short Term
      </div>
      <div class="phase-legend-item">
        <span class="phase-dot medium"></span> 2027 Medium Term
      </div>
      <div class="phase-legend-item">
        <span class="phase-dot long"></span> 2028 Long Term
      </div>
    </div>

    @php
      // Gabungkan semua item + tandai fase
      $allItems = collect();

      foreach ($shortTerm as $item) {
          $item->phase = 'short';
          $item->phase_label = '2026';
          $allItems->push($item);
      }
      foreach ($mediumTerm as $item) {
          $item->phase = 'medium';
          $item->phase_label = '2027';
          $allItems->push($item);
      }
      foreach ($longTerm as $item) {
          $item->phase = 'long';
          $item->phase_label = '2028';
          $allItems->push($item);
      }

      $perRow = 3; // jumlah card per baris
      $rows = $allItems->chunk($perRow)->values();
    @endphp

    @if($allItems->isEmpty())
      <div class="text-center py-5 text-secondary">
        Belum ada objective pada assessment ini.
      </div>
    @else
      <div class="roadmap-flow">
        @foreach($rows as $rowIndex => $rowItems)
          @php
            $isRight = $rowIndex % 2 === 0; // baris 0,2,4... ke kanan
          @endphp

          {{-- Baris card --}}
          <div class="flow-row {{ $isRight ? 'is-right' : 'is-left' }}">
            @foreach($rowItems as $item)
              <div class="flow-item">
                {{-- Card --}}
                <div class="roadmap-box phase-{{ $item->phase }} {{ $item->has_gap ? 'has-gap' : 'no-gap' }}">
                  <div class="code">{{ $item->code }}</div>
                  <div class="name">{{ $item->name }}</div>
                  <div class="meta">
                    <span class="badge-phase badge-{{ $item->phase }}">{{ $item->phase_label }}</span>
                    <span class="badge-gap">
                      {{ $item->has_gap ? 'Gap '.$item->gap : 'Tercapai' }}
                    </span>
                  </div>
                </div>

                {{-- Panah horizontal (kecuali card terakhir di baris) --}}
                @if(!$loop->last)
                  <div class="flow-arrow">
                    <span class="material-symbols-outlined">
                      {{ $isRight ? 'arrow_forward' : 'arrow_back' }}
                    </span>
                  </div>
                @endif
              </div>
            @endforeach
          </div>

          {{-- Panah turun ke baris berikutnya --}}
          @if(!$loop->last)
            <div class="flow-down-wrap {{ $isRight ? 'from-right' : 'from-left' }}">
              <div class="flow-down">
                <span class="material-symbols-outlined">arrow_downward</span>
              </div>
            </div>
          @endif
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection