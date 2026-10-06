@extends('layouts.app')

@section('content')
<style>
  :root { --primary: #003fb1; }
  .page-header-card {
    padding: 1.75rem 2rem; border-radius: 1rem; margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 40%, #3b82f6 75%, #60a5fa 100%);
    color: #fff; box-shadow: 0 12px 30px rgba(37, 99, 235, 0.25);
  }
  .page-header-card h1 { color: #fff; font-weight: 700; }
  .result-card {
    background: #fff; border-radius: 0.75rem; padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1rem;
  }
  .obj-code {
    display: inline-flex; padding: 0.2rem 0.5rem; border-radius: 0.35rem;
    background: rgba(0,63,177,0.08); color: var(--primary);
    font-size: 0.75rem; font-weight: 700;
  }
  .rating-F { background: #dcfce7; color: #166534; }
  .rating-L { background: #dbeafe; color: #1e40af; }
  .rating-P { background: #fef3c7; color: #92400e; }
  .rating-N { background: #fee2e2; color: #991b1b; }
  .level-pill {
    min-width: 52px; text-align: center; font-weight: 700; font-size: 0.8rem;
  }
</style>

<div class="container-fluid px-4 py-3">
  <div class="page-header-card">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
      <div>
        <p class="small text-uppercase fw-semibold mb-1" style="opacity:.85">Hasil Capability</p>
        <h1 class="fs-4 fw-bold mb-1">{{ $assessment->name }}</h1>
        <p class="mb-0 small" style="opacity:.85">
          Rata-rata % semua responden per level · Rating N / P / L / F
        </p>
      </div>
      <div class="d-flex gap-2 align-self-start">
        <form action="{{ route('assessor.capability.recalculate', $assessment) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-light btn-sm">Hitung Ulang</button>
        </form>
        <a href="{{ route('assessor.capability.show', $assessment) }}" class="btn btn-outline-light btn-sm">Domain</a>
        <a href="{{ route('assessor.capability.index') }}" class="btn btn-outline-light btn-sm">Kembali</a>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="result-card mb-3">
    <div class="small text-secondary mb-1">Responden</div>
    <div>
      Selesai: <strong>{{ $respondents->where('status','completed')->count() }}</strong>
      / {{ $respondents->count() }}
    </div>
  </div>

  @forelse($objectives as $obj)
    @php
      $cobitId = $obj->cobit_id;
      $levelResults = $results->get($cobitId, collect());
    @endphp
    <div class="result-card">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
          <span class="obj-code me-1">{{ $obj->code }}</span>
          <span class="fw-semibold">{{ $obj->cobit->description ?? $obj->domain }}</span>
        </div>
        <span class="badge bg-light text-secondary border">{{ $obj->priority }}</span>
      </div>

      @if($levelResults->isEmpty())
        <p class="text-secondary small mb-0">Belum ada hasil (belum ada responden selesai / hitung ulang).</p>
      @else
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr class="text-secondary small">
                <th>Level</th>
                <th class="text-center">Percentage</th>
                <th class="text-center">Rating</th>
              </tr>
            </thead>
            <tbody>
              @foreach($levelResults->sortBy('level') as $row)
                <tr>
                  <td><span class="level-pill badge bg-primary">L{{ $row->level }}</span></td>
                  <td class="text-center fw-semibold">{{ number_format($row->percentage, 1) }}%</td>
                  <td class="text-center">
                    <span class="badge rating-{{ $row->rating }}">{{ $row->rating }}</span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  @empty
    <div class="text-center text-secondary py-5">Tidak ada objectives.</div>
  @endforelse
</div>
@endsection