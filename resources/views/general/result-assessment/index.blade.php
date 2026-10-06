@extends('layouts.app')

@section('title', 'Hasil Assessment')

@section('content')
<style>
  :root {
    --primary: #003fb1;
    --card-bg: #ffffff;
    --border: #e2e8f0;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
  }

  .result-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
    color: #fff;
    background: linear-gradient(135deg, #0b3cc1 0%, #2563eb 50%, #60a5fa 100%);
    box-shadow: 0 18px 40px rgba(37, 99, 235, 0.22);
  }
  .result-hero::after {
    content: "";
    position: absolute;
    right: -40px; top: -40px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,.12);
  }
  .result-hero h1 {
    color: #fff;
    font-weight: 700;
    font-size: 1.35rem;
    margin-bottom: 0.25rem;
  }
  .result-hero p {
    color: rgba(255,255,255,.88);
    margin: 0;
    font-size: 0.875rem;
  }

  .selector-card {
    background: var(--card-bg);
    border-radius: 1rem;
    border: 1px solid var(--border);
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
  }

  .stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.15rem 1.25rem;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    height: 100%;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
  }
  .stat-card .stat-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--text-secondary);
    margin-bottom: 0.3rem;
  }
  .stat-card .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
  }

  .content-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 1rem;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    overflow: hidden;
    margin-bottom: 1.5rem;
  }
  .content-card .card-header-custom {
    background: #f8fafc;
    border-bottom: 1px solid var(--border);
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .content-card .card-header-custom h6 {
    margin: 0;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-primary);
  }
  .content-card .card-body-custom {
    padding: 1.5rem;
  }

  /* Table */
  .result-table {
    width: 100%;
    margin: 0;
  }
  .result-table thead th {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 0.85rem 0.9rem;
    white-space: nowrap;
  }
  .result-table tbody td {
    padding: 0.9rem 0.9rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.875rem;
  }
  .result-table tbody tr:last-child td { border-bottom: none; }
  .result-table tbody tr:hover { background: #f8fafc; }

  .domain-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.75rem;
    font-weight: 700;
    color: #1e40af;
    background: #eff6ff;
    padding: 0.2rem 0.5rem;
    border-radius: 0.35rem;
  }

  .pill {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    white-space: nowrap;
  }
  .pill-blue   { background: #eff6ff; color: #1d4ed8; }
  .pill-green  { background: #ecfdf5; color: #047857; }
  .pill-amber  { background: #fffbeb; color: #b45309; }
  .pill-red    { background: #fef2f2; color: #b91c1c; }
  .pill-gray   { background: #f1f5f9; color: #475569; }

  .level-badge {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    min-width: 48px;
  }
  .level-num {
    font-size: 1.05rem;
    font-weight: 800;
    line-height: 1;
  }
  .level-label {
    font-size: 0.6rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    opacity: 0.85;
  }

  .badge-priority {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.22rem 0.55rem;
    border-radius: 9999px;
  }
  .p-very-high { background: #fee2e2; color: #b91c1c; }
  .p-high      { background: #ffedd5; color: #c2410c; }
  .p-medium    { background: #fef9c3; color: #a16207; }
  .p-low       { background: #f1f5f9; color: #475569; }
  .p-none      { background: #e2e8f0; color: #64748b; }

  .info-row {
    display: flex;
    margin-bottom: 0.6rem;
    font-size: 0.9rem;
  }
  .info-row .label {
    width: 180px;
    font-weight: 500;
    color: #475569;
    flex-shrink: 0;
  }
  .info-row .sep { width: 16px; color: #94a3b8; }
  .info-row .value { font-weight: 600; color: #0f172a; }

  .btn-download {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.6rem 1.2rem;
    border-radius: 0.55rem;
    font-weight: 600;
    font-size: 0.85rem;
    border: none;
    text-decoration: none;
    transition: all 0.2s;
  }
  .btn-pdf {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: #fff;
  }
  .btn-pdf:hover {
    background: linear-gradient(135deg, #b91c1c, #dc2626);
    color: #fff;
    transform: translateY(-1px);
  }
  .btn-excel {
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff;
  }
  .btn-excel:hover {
    background: linear-gradient(135deg, #047857, #059669);
    color: #fff;
    transform: translateY(-1px);
  }

  .empty-state {
    padding: 3.5rem 1.5rem;
    text-align: center;
    color: #94a3b8;
  }
  .empty-state .material-symbols-outlined {
    font-size: 3rem;
    opacity: 0.35;
    display: block;
    margin-bottom: 0.75rem;
  }

  .legend-item {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    color: #64748b;
  }

  @media (max-width: 767.98px) {
    .result-hero { padding: 1.25rem 1.25rem; }
    .result-hero h1 { font-size: 1.2rem; }
    .info-row .label { width: 140px; }
  }
</style>

@php
  $levelNames = [
    0 => 'Incomplete',
    1 => 'Performed',
    2 => 'Managed',
    3 => 'Established',
    4 => 'Predictable',
    5 => 'Optimizing',
  ];
@endphp

<div class="container-fluid px-3 px-md-4 py-3">

  {{-- ==================== HERO ==================== --}}
  <div class="result-hero">
    <div class="position-relative">
      <p class="small text-uppercase fw-semibold mb-1" style="letter-spacing:.06em;opacity:.85">
        Hasil Assessment · COBIT 2019
      </p>
      <h1>Hasil Assessment</h1>
      <p>
        Lihat gap analysis dan laporan assessment sesuai unit Anda.
      </p>
    </div>
  </div>

  {{-- ==================== DROPDOWN SELECTOR ==================== --}}
  <div class="selector-card">
    <form method="GET" action="{{ route('result-assessment.index') }}" id="assessmentForm">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-8 col-lg-9">
          <label class="form-label fw-semibold small text-secondary mb-1">
            Pilih Assessment
          </label>
          <select name="assessment_id"
                  class="form-select form-select-lg"
                  style="border-radius:0.65rem; font-size:0.95rem;"
                  onchange="document.getElementById('assessmentForm').submit()">
            <option value="">— Pilih Assessment —</option>
            @foreach($assessments as $item)
              <option value="{{ $item->id }}"
                      @selected(isset($assessment) && $assessment->id == $item->id)>
                {{ $item->name ?? $item->title ?? 'Assessment #'.$item->id }}
                @if($item->department)
                  — {{ $item->department->name }}
                @endif
                @if($item->year)
                  ({{ $item->year }})
                @endif
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-4 col-lg-3">
          <button type="submit" class="btn btn-primary w-100" style="border-radius:0.65rem; padding:0.7rem;">
            Tampilkan
          </button>
        </div>
      </div>
    </form>

    @if($assessments->isEmpty())
      <div class="mt-3 small text-muted">
        Belum ada assessment yang tersedia untuk role Anda.
      </div>
    @endif
  </div>

  {{-- ==================== KONTEN HASIL ==================== --}}
  @if(isset($assessment) && $assessment)

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-label">Total Domain</div>
          <div class="stat-value">{{ $totalDomains ?? 0 }}</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-label">Gap Tertutup</div>
          <div class="stat-value text-success">{{ $gapClosed ?? 0 }}</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-label">Masih Ada Gap</div>
          <div class="stat-value text-danger">{{ $gapOpen ?? 0 }}</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-label">Rata-rata Gap</div>
          <div class="stat-value {{ ($avgGap ?? 0) > 0 ? 'text-danger' : (($avgGap ?? 0) < 0 ? 'text-primary' : 'text-success') }}">
            {{ ($avgGap ?? 0) > 0 ? '+' : '' }}{{ $avgGap ?? 0 }}
          </div>
        </div>
      </div>
    </div>

    {{-- ==================== GRAFIK ==================== --}}
    <div class="content-card">
      <div class="card-header-custom">
        <div>
          <h6 class="mb-0">Grafik Perbandingan Capability Level</h6>
          <p class="small text-muted mb-0 mt-1">Level Rekomendasi vs Level Assessor per Domain</p>
        </div>
        <div class="d-flex gap-3">
          <div class="d-flex align-items-center gap-1">
            <span style="width:12px;height:12px;border-radius:3px;background:#3b82f6;display:inline-block"></span>
            <span class="small text-muted">Rekomendasi</span>
          </div>
          <div class="d-flex align-items-center gap-1">
            <span style="width:12px;height:12px;border-radius:3px;background:#10b981;display:inline-block"></span>
            <span class="small text-muted">Assessor</span>
          </div>
        </div>
      </div>
      <div class="card-body-custom">
        <div style="position:relative; height:340px; width:100%;">
          <canvas id="gapChart"></canvas>
        </div>
      </div>
    </div>

    {{-- Legend --}}
    <div class="d-flex flex-wrap gap-3 mb-3">
      <div class="legend-item">
        <span class="pill pill-green">0</span>
        <span>Sesuai</span>
      </div>
      <div class="legend-item">
        <span class="pill pill-red">+N</span>
        <span>Assessor lebih tinggi</span>
      </div>
      <div class="legend-item">
        <span class="pill pill-blue">−N</span>
        <span>Assessor lebih rendah</span>
      </div>
    </div>

    {{-- ==================== TABEL GAP ANALYSIS ==================== --}}
    <div class="content-card">
      <div class="card-header-custom">
        <h6>Detail Gap Analysis</h6>
      </div>
      <div class="table-responsive">
        <table class="table result-table align-middle mb-0">
          <thead>
            <tr>
              <th width="45" class="text-center">No</th>
              <th class="text-center" width="90">Kode</th>
              <th>Domain / Process</th>
              <th class="text-center">Level<br>Rekomendasi</th>
              <th class="text-center">Level<br>Assessor</th>
              <th class="text-center">Gap</th>
              <th class="text-center">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($domains ?? [] as $i => $domain)
              @php
                $recLevel = $domain->recommended_level ?? null;
                $assLevel = $domain->assessor_level ?? null;
                $gap      = $domain->gap ?? null;
              @endphp
              <tr>
                <td class="text-center text-muted">{{ $i + 1 }}</td>
                <td class="text-center">
                  <span class="domain-code">{{ $domain->code ?? '-' }}</span>
                </td>
                <td>
                  <div class="fw-semibold" style="color:#0f172a">
                    {{ $domain->name ?? $domain->description ?? '-' }}
                  </div>
                </td>
                <td class="text-center">
                  @if($recLevel !== null)
                    <div class="level-badge">
                      <span class="level-num text-primary">{{ $recLevel }}</span>
                      <span class="level-label text-muted">{{ $levelNames[$recLevel] ?? '-' }}</span>
                    </div>
                  @else
                    <span class="pill pill-gray">-</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($assLevel !== null)
                    <div class="level-badge">
                      <span class="level-num" style="color:#0f172a">{{ $assLevel }}</span>
                      <span class="level-label text-muted">{{ $levelNames[$assLevel] ?? '-' }}</span>
                    </div>
                  @else
                    <span class="pill pill-gray">-</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($gap === null)
                    <span class="pill pill-gray">Belum</span>
                  @elseif($gap === 0)
                    <span class="pill pill-green">0</span>
                  @elseif($gap > 0)
                    <span class="pill pill-red">+{{ $gap }}</span>
                  @else
                    <span class="pill pill-blue">{{ $gap }}</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($gap === null)
                    <span class="pill pill-gray">Belum dinilai</span>
                  @elseif($gap === 0)
                    <span class="pill pill-green">
                      <span class="material-symbols-outlined" style="font-size:0.9rem">check_circle</span>
                      Sesuai
                    </span>
                  @elseif($gap > 0)
                    <span class="pill pill-amber">
                      <span class="material-symbols-outlined" style="font-size:0.9rem">trending_up</span>
                      Lebih Tinggi
                    </span>
                  @else
                    <span class="pill pill-red">
                      <span class="material-symbols-outlined" style="font-size:0.9rem">trending_down</span>
                      Lebih Rendah
                    </span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7">
                  <div class="empty-state">
                    <span class="material-symbols-outlined">analytics</span>
                    Belum ada data domain untuk gap analysis.
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- ==================== LAPORAN RINGKAS ==================== --}}
    <div class="content-card">
      <div class="card-header-custom">
        <h6>Ringkasan Laporan Assessment</h6>
      </div>
      <div class="card-body-custom">

        <div class="row g-4">
          <div class="col-md-6">
            <div class="info-row">
              <div class="label">Assessment</div>
              <div class="sep">:</div>
              <div class="value">{{ $assessment->name ?? '-' }}</div>
            </div>
            <div class="info-row">
              <div class="label">Unit / Prodi</div>
              <div class="sep">:</div>
              <div class="value">{{ $assessment->department->name ?? '-' }}</div>
            </div>
            <div class="info-row">
              <div class="label">Tahun</div>
              <div class="sep">:</div>
              <div class="value">{{ $assessment->year ?? $year ?? '-' }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-row">
              <div class="label">Current Capability</div>
              <div class="sep">:</div>
              <div class="value">{{ number_format($currentCapability ?? 0, 2) }}</div>
            </div>
            <div class="info-row">
              <div class="label">Target Capability</div>
              <div class="sep">:</div>
              <div class="value">{{ number_format($targetCapability ?? 0, 2) }}</div>
            </div>
            <div class="info-row">
              <div class="label">Average Gap</div>
              <div class="sep">:</div>
              <div class="value">{{ number_format($averageGap ?? 0, 2) }}</div>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-3 pt-3 border-top">
          <div class="col-6 col-md-3">
            <div class="small text-secondary">Objectives Assessed</div>
            <div class="fw-bold fs-5">{{ $objectivesAssessed ?? 0 }}</div>
          </div>
          <div class="col-6 col-md-3">
            <div class="small text-secondary">Very High Priority</div>
            <div class="fw-bold fs-5 text-danger">{{ $veryHighPriority ?? 0 }}</div>
          </div>
          <div class="col-6 col-md-3">
            <div class="small text-secondary">High Priority</div>
            <div class="fw-bold fs-5 text-warning">{{ $highPriority ?? 0 }}</div>
          </div>
          <div class="col-6 col-md-3">
            <div class="small text-secondary">Medium Priority</div>
            <div class="fw-bold fs-5 text-primary">{{ $mediumPriority ?? 0 }}</div>
          </div>
        </div>

        {{-- Tombol Download --}}
        <div class="d-flex flex-wrap gap-3 mt-4 pt-3 border-top">
            <a href="{{ route('result-assessment.pdf', $assessment) }}"
                class="btn-download btn-pdf" target="_blank">
                <span class="material-symbols-outlined" style="font-size:1.15rem;">picture_as_pdf</span>
                Download PDF
            </a>
            <a href="{{ route('result-assessment.excel', $assessment) }}"
                class="btn-download btn-excel" target="_blank">
                <span class="material-symbols-outlined" style="font-size:1.15rem;">table_view</span>
                Export Excel
            </a>
        </div>
      </div>
    </div>

    {{-- ==================== TABEL DETAIL LAPORAN ==================== --}}
    @if(isset($items) && $items->isNotEmpty())
    <div class="content-card">
      <div class="card-header-custom">
        <h6>Detail Hasil Setiap Domain</h6>
      </div>
      <div class="table-responsive">
        <table class="table result-table align-middle mb-0">
          <thead>
            <tr>
              <th class="text-center">Kode</th>
              <th>Nama Domain</th>
              <th class="text-center">Recommended</th>
              <th class="text-center">Achieved</th>
              <th class="text-center">Gap</th>
              <th class="text-center">Priority</th>
              <th class="text-center">Catatan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $item)
              <tr>
                <td class="text-center fw-semibold">{{ $item->code }}</td>
                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->recommended ?? '—' }}</td>
                <td class="text-center">{{ $item->achieved ?? '—' }}</td>
                <td class="text-center fw-medium">{{ $item->gap }}</td>
                <td class="text-center">
                  @php
                    $pClass = match($item->priority_label ?? '') {
                      'VERY HIGH' => 'p-very-high',
                      'HIGH'      => 'p-high',
                      'MEDIUM'    => 'p-medium',
                      'LOW'       => 'p-low',
                      default     => 'p-none',
                    };
                  @endphp
                  <span class="badge-priority {{ $pClass }}">{{ $item->priority_label ?? '—' }}</span>
                </td>
                <td class="small text-muted">{{ $item->notes ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

  @else
    {{-- Empty state saat belum pilih assessment --}}
    <div class="content-card">
      <div class="empty-state">
        <span class="material-symbols-outlined">query_stats</span>
        <div class="fw-medium mb-1" style="color:#64748b">Pilih Assessment terlebih dahulu</div>
        <div class="small">Gunakan dropdown di atas untuk menampilkan gap analysis dan laporan.</div>
      </div>
    </div>
  @endif

</div>

{{-- Chart.js --}}
@if(isset($assessment) && $assessment && !empty($chartData))
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const domains = @json($chartData ?? []);
  const filtered = domains.filter(d => d.recommended_level !== null || d.assessor_level !== null);
  const labels = filtered.map(d => d.code);
  const recommendedData = filtered.map(d => d.recommended_level);
  const assessorData = filtered.map(d => d.assessor_level);

  const canvas = document.getElementById('gapChart');
  if (!canvas) return;

  new Chart(canvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Level Rekomendasi',
          data: recommendedData,
          backgroundColor: 'rgba(59, 130, 246, 0.75)',
          borderColor: 'rgba(37, 99, 235, 1)',
          borderWidth: 1,
          borderRadius: 4,
          barPercentage: 0.7,
          categoryPercentage: 0.7
        },
        {
          label: 'Level Assessor',
          data: assessorData,
          backgroundColor: 'rgba(16, 185, 129, 0.75)',
          borderColor: 'rgba(5, 150, 105, 1)',
          borderWidth: 1,
          borderRadius: 4,
          barPercentage: 0.7,
          categoryPercentage: 0.7
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0f172a',
          titleFont: { size: 13, weight: '600' },
          bodyFont: { size: 12 },
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            title: function (ctx) {
              if (!ctx.length) return '';
              const d = filtered[ctx[0].dataIndex];
              return d.code + ' — ' + d.name;
            },
            afterBody: function (ctx) {
              if (!ctx.length) return '';
              const gap = filtered[ctx[0].dataIndex].gap;
              if (gap === null) return 'Gap: Belum dinilai';
              if (gap === 0) return 'Gap: 0 (Sesuai)';
              return 'Gap: ' + (gap > 0 ? '+' + gap : gap);
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          min: 0,
          max: 5,
          ticks: {
            stepSize: 1,
            callback: function (value) {
              const names = {
                0: '0 Incomplete', 1: '1 Performed', 2: '2 Managed',
                3: '3 Established', 4: '4 Predictable', 5: '5 Optimizing'
              };
              return names[value] ?? value;
            },
            font: { size: 11 }
          },
          grid: { color: 'rgba(148, 163, 184, 0.2)' },
          title: {
            display: true,
            text: 'Capability Level',
            font: { size: 12, weight: '600' },
            color: '#64748b'
          }
        },
        x: {
          ticks: {
            font: { size: 11, weight: '600' },
            color: '#334155',
            maxRotation: 45
          },
          grid: { display: false },
          title: {
            display: true,
            text: 'Domain / Process',
            font: { size: 12, weight: '600' },
            color: '#64748b'
          }
        }
      }
    }
  });
});
</script>
@endif
@endsection