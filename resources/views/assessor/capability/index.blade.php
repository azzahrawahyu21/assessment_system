@extends('layouts.app')

@section('content')
<style>
  :root {
    --primary: #003fb1;
    --primary-soft: rgba(0, 63, 177, 0.08);
  }

  .cap-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.85rem 2rem;
    margin-bottom: 1.5rem;
    color: #fff;
    background: linear-gradient(135deg, #0b3cc1 0%, #2563eb 45%, #60a5fa 100%);
    box-shadow: 0 18px 40px rgba(37, 99, 235, 0.22);
  }
  .cap-hero::after {
    content: "";
    position: absolute;
    right: -60px; top: -60px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.12);
  }
  .cap-hero h1 { color: #fff; font-weight: 700; letter-spacing: -0.02em; }
  .cap-hero p { color: rgba(255,255,255,.88); margin: 0; }

  .cap-card {
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    overflow: hidden;
  }
  .cap-table { margin: 0; }
  .cap-table thead th {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 0.95rem 1.25rem;
  }
  .cap-table tbody td {
    padding: 1.05rem 1.25rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.92rem;
  }
  .cap-table tbody tr:last-child td { border-bottom: none; }
  .cap-table tbody tr { transition: background .15s ease; }
  .cap-table tbody tr:hover { background: #f8fafc; }

  .name-cell { font-weight: 600; color: #0f172a; }
  .muted { color: #94a3b8; }

  .pill {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .28rem .7rem;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 700;
  }
  .pill-blue { background: #eff6ff; color: #1d4ed8; }
  .pill-green { background: #ecfdf5; color: #047857; }
  .pill-amber { background: #fffbeb; color: #b45309; }
  .pill-gray { background: #f1f5f9; color: #475569; }
  .pill-info { background: #e0f2fe; color: #0369a1; }

  .btn-icon {
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: .65rem;
    padding: 0;
  }
  .btn-primary {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
  }
  .empty-state {
    padding: 3.5rem 1rem;
    text-align: center;
    color: #94a3b8;
  }
  .empty-state .material-symbols-outlined {
    font-size: 2.75rem;
    opacity: .4;
    display: block;
    margin-bottom: .5rem;
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

@php $asRespondent = $asRespondent ?? false; @endphp

<div class="container-fluid px-4 py-3">
  <div class="cap-hero">
    <div class="position-relative">
      <p class="small text-uppercase fw-semibold mb-1" style="letter-spacing:.06em;opacity:.85">
        {{ $asRespondent ? 'Responden' : 'Assessor' }} · Capability
      </p>
      <h1 class="fs-4 fw-bold mb-1">Capability Assessment</h1>
      <p class="small">
        @if($asRespondent)
          Daftar assessment yang perlu Anda isi (Ya / Tidak per pernyataan).
        @else
          Pilih assessment, mulai penilaian, lalu pantau domain & hasil kapabilitas.
        @endif
      </p>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="cap-card">
    <div class="table-responsive">
      <table class="table cap-table align-middle">
        <thead>
          <tr>
            <th width="60" class="text-center">No</th>
            <th>Nama Assessment</th>
            <th class="text-center">Department</th>
            @unless($asRespondent)
              <th class="text-center">Objectives</th>
              <th class="text-center">Responden</th>
            @else
              <th class="text-center">Status</th>
            @endunless
            <th width="140" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assessments as $i => $assessment)
            @php $resp = ($respondentsMap ?? collect())->get($assessment->id); @endphp
            <tr>
              <td class="text-center muted">{{ $i + 1 }}</td>
              <td class="name-cell">{{ $assessment->name }}</td>
              <td class="text-center">
                @if($assessment->is_all_department ?? $assessment->is_all_departments ?? false)
                  <span class="pill pill-info">Semua</span>
                @else
                  {{ $assessment->department->name ?? '-' }}
                @endif
              </td>

              @unless($asRespondent)
                <td class="text-center">
                  <span class="pill pill-blue">{{ $assessment->objective_priorities_count ?? 0 }}</span>
                </td>
                <td class="text-center">
                  <span class="pill pill-green">
                    {{ $assessment->completed_respondents_count ?? 0 }} / {{ $assessment->respondents_count ?? 0 }}
                  </span>
                </td>
              @else
                <td class="text-center">
                  @if($resp && $resp->status === 'completed')
                    <span class="pill pill-green">Selesai</span>
                  @elseif($resp && $resp->status === 'in_progress')
                    <span class="pill pill-amber">Draft</span>
                  @else
                    <span class="pill pill-gray">Belum</span>
                  @endif
                </td>
              @endunless

              <td class="text-center">
                <div class="d-inline-flex gap-1">
                  @if($asRespondent)
                    <a href="{{ route('capability.fill.show', $assessment) }}"
                       class="btn btn-sm btn-primary btn-icon" title="Isi Assessment">
                      <span class="material-symbols-outlined" style="font-size:1.15rem">edit</span>
                    </a>
                  @else
                    @if(($assessment->respondents_count ?? 0) === 0)
                      <form action="{{ route('assessor.capability.start', $assessment) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary btn-icon" title="Mulai">
                          <span class="material-symbols-outlined" style="font-size:1.2rem">play_arrow</span>
                        </button>
                      </form>
                    @else
                      <a href="{{ route('assessor.capability.show', $assessment) }}"
                        class="btn btn-sm btn-outline-primary btn-icon" title="Domain">
                        <span class="material-symbols-outlined" style="font-size:1.15rem">list</span>
                      </a>
                      <a href="{{ route('assessor.capability.result', $assessment) }}"
                        class="btn btn-sm btn-outline-secondary btn-icon" title="Hasil">
                        <span class="material-symbols-outlined" style="font-size:1.15rem">analytics</span>
                      </a>

                      {{-- Tombol penilaian assessor hanya muncul jika semua responden sudah selesai --}}
                      @if(($assessment->respondents_count ?? 0) > 0 
                          && ($assessment->completed_respondents_count ?? 0) === ($assessment->respondents_count ?? 0))
                        <a href="{{ route('assessor.capability.judge', $assessment) }}"
                          class="btn btn-sm btn-success btn-icon" title="Nilai Assessor">
                          <span class="material-symbols-outlined" style="font-size:1.15rem">fact_check</span>
                        </a>
                      @endif
                    @endif
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6">
                <div class="empty-state">
                  <span class="material-symbols-outlined">inbox</span>
                  Belum ada assessment.
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>
</div>
@endsection