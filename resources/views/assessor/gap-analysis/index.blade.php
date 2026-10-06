@extends('layouts.app')

@section('content')
<style>
  :root {
    --primary: #003fb1;
  }

  .cap-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.5rem 1.5rem;
    margin-bottom: 1.25rem;
    color: #fff;
    background: linear-gradient(135deg, #0b3cc1 0%, #2563eb 45%, #60a5fa 100%);
    box-shadow: 0 18px 40px rgba(37, 99, 235, 0.22);
  }
  .cap-hero::after {
    content: "";
    position: absolute;
    right: -50px; top: -50px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,.12);
  }
  .cap-hero h1 {
    color: #fff;
    font-weight: 700;
    letter-spacing: -0.02em;
    font-size: 1.35rem;
  }
  .cap-hero p { color: rgba(255,255,255,.88); margin: 0; font-size: 0.875rem; }

  .cap-card {
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    overflow: hidden;
  }

  .cap-table { margin: 0; min-width: 780px; }
  .cap-table thead th {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 0.85rem 1rem;
    white-space: nowrap;
  }
  .cap-table tbody td {
    padding: 0.95rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.875rem;
  }
  .cap-table tbody tr:last-child td { border-bottom: none; }
  .cap-table tbody tr:hover { background: #f8fafc; }

  .name-cell { font-weight: 600; color: #0f172a; }
  .muted { color: #94a3b8; }

  .pill {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    padding: .25rem .6rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 700;
    white-space: nowrap;
  }
  .pill-blue  { background: #eff6ff; color: #1d4ed8; }
  .pill-green { background: #ecfdf5; color: #047857; }
  .pill-amber { background: #fffbeb; color: #b45309; }
  .pill-red   { background: #fef2f2; color: #b91c1c; }
  .pill-gray  { background: #f1f5f9; color: #475569; }
  .pill-info  { background: #e0f2fe; color: #0369a1; }

  .btn-icon {
    width: 34px; height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: .6rem;
    padding: 0;
  }

  .empty-state {
    padding: 3rem 1rem;
    text-align: center;
    color: #94a3b8;
  }
  .empty-state .material-symbols-outlined {
    font-size: 2.5rem;
    opacity: .4;
    display: block;
    margin-bottom: .5rem;
  }

  /* ===== Responsive ===== */
  @media (max-width: 767.98px) {
    .cap-hero {
      padding: 1.25rem 1.15rem;
      border-radius: 1rem;
    }
    .cap-hero h1 { font-size: 1.2rem; }
    .cap-hero p { font-size: 0.8rem; }

    .table-responsive {
      margin: 0 -0.25rem;
      border-radius: 0;
    }
  }

  @media (min-width: 768px) {
    .cap-hero {
      padding: 1.75rem 2rem;
    }
    .cap-hero h1 { font-size: 1.5rem; }
  }
</style>

<div class="container-fluid px-3 px-md-4 py-3">

  <div class="cap-hero">
    <div class="position-relative">
      <p class="small text-uppercase fw-semibold mb-1" style="letter-spacing:.06em;opacity:.85">
        Assessor · Analisis
      </p>
      <h1 class="fw-bold mb-1">Gap Analysis</h1>
      <p class="mb-0">
        Daftar assessment yang sudah dinilai assessor. Klik Detail untuk melihat perbandingan Level Rekomendasi vs Level Assessor.
      </p>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="cap-card">
    <div class="table-responsive">
      <table class="table cap-table align-middle mb-0">
        <thead>
          <tr>
            <th width="50" class="text-center">No</th>
            <th>Nama Assessment</th>
            <th class="text-center">Department</th>
            <th class="text-center">Objectives</th>
            <th class="text-center">Responden</th>
            <th class="text-center">Gap Tertutup</th>
            <th class="text-center">Masih Gap</th>
            <th class="text-center">Status</th>
            <th width="80" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assessments as $i => $assessment)
            <tr>
              <td class="text-center muted">{{ $i + 1 }}</td>

              <td>
                <div class="name-cell">{{ $assessment->name }}</div>
              </td>

              <td class="text-center">
                @if($assessment->is_all_departments ?? false)
                  <span class="pill pill-info">Semua</span>
                @else
                  <span class="text-nowrap">{{ $assessment->department->name ?? '-' }}</span>
                @endif
              </td>

              <td class="text-center">
                <span class="pill pill-blue">{{ $assessment->objective_priorities_count ?? 0 }}</span>
              </td>

              <td class="text-center">
                <span class="pill pill-green">
                  {{ $assessment->completed_respondents_count ?? 0 }}/{{ $assessment->respondents_count ?? 0 }}
                </span>
              </td>

              <td class="text-center">
                @if($assessment->has_judgment)
                  <span class="pill pill-green">{{ $assessment->gap_closed }}</span>
                @else
                  <span class="pill pill-gray">-</span>
                @endif
              </td>

              <td class="text-center">
                @if($assessment->has_judgment)
                  @if($assessment->gap_open > 0)
                    <span class="pill pill-red">{{ $assessment->gap_open }}</span>
                  @else
                    <span class="pill pill-green">0</span>
                  @endif
                @else
                  <span class="pill pill-gray">-</span>
                @endif
              </td>

              <td class="text-center">
                @if(!$assessment->has_judgment)
                  <span class="pill pill-gray">Belum dinilai</span>
                @elseif($assessment->gap_open === 0)
                  <span class="pill pill-green">Gap Tertutup</span>
                @else
                  <span class="pill pill-amber">Ada Gap</span>
                @endif
              </td>

              <td class="text-center">
                @if($assessment->has_judgment)
                  <a href="{{ route('assessor.gap-analysis.show', $assessment) }}"
                     class="btn btn-sm btn-primary btn-icon" title="Detail Gap Analysis">
                    <span class="material-symbols-outlined" style="font-size:1.1rem">visibility</span>
                  </a>
                @else
                  <a href="{{ route('assessor.capability.judge', $assessment) }}"
                     class="btn btn-sm btn-outline-secondary btn-icon" title="Isi Penilaian Assessor dulu">
                    <span class="material-symbols-outlined" style="font-size:1.1rem">fact_check</span>
                  </a>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9">
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