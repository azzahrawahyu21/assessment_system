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
    font-size: 1.3rem;
  }
  .cap-hero p { color: rgba(255,255,255,.88); margin: 0; font-size: 0.85rem; }

  .cap-card {
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    overflow: hidden;
  }

  .stat-card {
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 1rem;
    padding: 1.1rem 1.25rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    height: 100%;
  }
  .stat-card .stat-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #64748b;
  }
  .stat-card .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
  }

  .cap-table { margin: 0; min-width: 720px; }
  .cap-table thead th {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 0.85rem 0.9rem;
    white-space: nowrap;
  }
  .cap-table tbody td {
    padding: 0.9rem 0.9rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.875rem;
  }
  .cap-table tbody tr:last-child td { border-bottom: none; }
  .cap-table tbody tr:hover { background: #f8fafc; }

  .name-cell { font-weight: 600; color: #0f172a; }
  .domain-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.75rem;
    font-weight: 700;
    color: #1e40af;
    background: #eff6ff;
    padding: 0.18rem 0.45rem;
    border-radius: 0.35rem;
    white-space: nowrap;
  }
  .muted { color: #94a3b8; }

  .pill {
    display: inline-flex;
    align-items: center;
    gap: .2rem;
    padding: .22rem .55rem;
    border-radius: 999px;
    font-size: .7rem;
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

  .legend-item {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    color: #64748b;
  }

  /* ===== Responsive ===== */
  @media (max-width: 575.98px) {
    .cap-hero {
      padding: 1.2rem 1.1rem;
      border-radius: 1rem;
    }
    .cap-hero h1 { font-size: 1.15rem; }
    .stat-card .stat-value { font-size: 1.35rem; }
  }

  @media (max-width: 767.98px) {
    .cap-hero {
      padding: 1.35rem 1.25rem;
    }
    .hero-actions {
      margin-top: 0.75rem;
    }
  }

  @media (min-width: 768px) {
    .cap-hero {
      padding: 1.75rem 2rem;
    }
    .cap-hero h1 { font-size: 1.45rem; }
  }
</style>

{{-- @php
  $levelNames = [
    0 => 'Incomplete',
    1 => 'Performed',
    2 => 'Managed',
    3 => 'Established',
    4 => 'Predictable',
    5 => 'Optimizing',
  ];

  $totalDomains = $domains->count() ?? 0;
  $gapClosed    = $domains->where('gap', 0)->count() ?? 0;
  $gapOpen      = $domains->filter(fn($d) => $d->gap !== null && $d->gap != 0)->count() ?? 0;
  $avgGap       = $totalDomains > 0 ? round($domains->avg('gap') ?? 0, 2) : 0;
@endphp --}}
@php
  $levelNames = [
    0 => 'Incomplete',
    1 => 'Performed',
    2 => 'Managed',
    3 => 'Established',
    4 => 'Predictable',
    5 => 'Optimizing',
  ];

  $totalDomains = $domains->count() ?? 0;
  $gapClosed    = $domains->where('gap', 0)->count() ?? 0;
  $gapOpen      = $domains->filter(fn($d) => $d->gap !== null && $d->gap != 0)->count() ?? 0;
  $avgGap       = $totalDomains > 0 ? round($domains->avg('gap') ?? 0, 2) : 0;
  $chartData = $domains->map(function ($d) {
      return [
          'code'              => $d->code ?? '-',
          'name'              => $d->name ?? '-',
          'recommended_level' => $d->recommended_level !== null
              ? (int) $d->recommended_level
              : null,
          'assessor_level'    => $d->assessor_level !== null
              ? (int) $d->assessor_level
              : null,
          'gap'               => $d->gap !== null
              ? (int) $d->gap
              : null,
      ];
  })->values()->toArray();
@endphp

<div class="container-fluid px-3 px-md-4 py-3">

  {{-- Hero --}}
  <div class="cap-hero">
    <div class="position-relative">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2">
        <div class="flex-grow-1">
          <p class="small text-uppercase fw-semibold mb-1" style="letter-spacing:.06em;opacity:.85">
            Assessor · Gap Analysis
          </p>
          <h1 class="fw-bold mb-1">{{ $assessment->name ?? 'Assessment' }}</h1>
          <p class="mb-0">
            Perbandingan Level Rekomendasi vs Level Assessor (COBIT 2019).
          </p>
        </div>
        <div class="d-flex gap-2 hero-actions">
          <a href="{{ route('assessor.gap-analysis.index') }}"
             class="btn btn-sm btn-light btn-icon" title="Kembali">
            <span class="material-symbols-outlined" style="font-size:1.15rem">arrow_back</span>
          </a>
          <a href="{{ route('assessor.capability.result', $assessment) }}"
             class="btn btn-sm btn-light btn-icon" title="Hasil Capability">
            <span class="material-symbols-outlined" style="font-size:1.15rem">analytics</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-label">Total Domain</div>
        <div class="stat-value">{{ $totalDomains }}</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-label">Gap Tertutup</div>
        <div class="stat-value text-success">{{ $gapClosed }}</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-label">Masih Ada Gap</div>
        <div class="stat-value text-danger">{{ $gapOpen }}</div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="stat-card">
        <div class="stat-label">Rata-rata Gap</div>
        <div class="stat-value {{ $avgGap > 0 ? 'text-danger' : ($avgGap < 0 ? 'text-primary' : 'text-success') }}">
          {{ $avgGap > 0 ? '+' : '' }}{{ $avgGap }}
        </div>
      </div>
    </div>
  </div>

  {{-- Legend --}}
  <div class="d-flex flex-wrap gap-2 gap-md-3 mb-3">
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

  {{-- Table --}}
  <div class="cap-card">
    <div class="table-responsive">
      <table class="table cap-table align-middle mb-0">
        <thead>
          <tr>
            <th width="45" class="text-center">No</th>
            <th class="text-center" width="90">Kode</th>
            <th class="text-center">Domain / Process</th>
            <th class="text-center">Level<br>Rekomendasi</th>
            <th class="text-center">Level<br>Assessor</th>
            <th class="text-center">Gap</th>
            <th class="text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($domains ?? [] as $i => $domain)
            @php
              $recLevel = $domain->recommended_level;
              $assLevel = $domain->assessor_level;
              $gap      = $domain->gap;
            @endphp
            <tr>
              <td class="text-center muted">{{ $i + 1 }}</td>

              <td class="text-center">
                <span class="domain-code text-center">{{ $domain->code ?? '-' }}</span>
              </td>

              <td>
                <div class="name-cell">
                    @if(!empty($domain->description))
                        <div class="text-start">
                            {{ $domain->description }}
                        </div>
                    @endif
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

  {{-- Catatan --}}
  <div class="mt-3">
    <p class="small text-muted mb-0">
      <strong>Catatan COBIT 2019:</strong>
      Capability Level diukur dari 0 (Incomplete) hingga 5 (Optimizing).
      Gap = Level Assessor − Level Rekomendasi.
      Nilai positif = assessor menilai lebih tinggi; negatif = lebih rendah; 0 = sesuai.
    </p>
    <br><br>
  </div>

  {{-- ==================== GRAFIK GAP ANALYSIS ==================== --}}
  <div class="cap-card mb-4">
    <div class="p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
          <h6 class="fw-bold mb-0" style="color:#0f172a">Grafik Perbandingan Capability Level</h6>
          <p class="small text-muted mb-0">Level Rekomendasi vs Level Assessor per Domain (COBIT 2019)</p>
        </div>
        <div class="d-flex gap-3">
          <div class="d-flex align-items-center gap-1">
            <span style="width:14px;height:14px;border-radius:3px;background:#3b82f6;display:inline-block"></span>
            <span class="small text-muted">Rekomendasi</span>
          </div>
          <div class="d-flex align-items-center gap-1">
            <span style="width:14px;height:14px;border-radius:3px;background:#10b981;display:inline-block"></span>
            <span class="small text-muted">Assessor</span>
          </div>
        </div>
      </div>

      <div style="position:relative; height:340px; width:100%;">
        <canvas id="gapChart"></canvas>
      </div>
    </div>
  </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const domains = @json($chartData);
    const filtered = domains.filter(function (domain) {return domain.recommended_level !== null || domain.assessor_level !== null;});
    const labels = filtered.map(function (domain) {return domain.code;});
    const recommendedData = filtered.map(function (domain) {return domain.recommended_level;});
    const assessorData = filtered.map(function (domain) {return domain.assessor_level;});
    const canvas = document.getElementById('gapChart');
    if (!canvas) {
        return;
    }
    const ctx = canvas.getContext('2d');

    new Chart(ctx, {type: 'bar', data: {labels: labels,
            datasets: [
                {label: 'Level Rekomendasi',
                 data: recommendedData,
                 backgroundColor: 'rgba(59, 130, 246, 0.75)',
                 borderColor: 'rgba(37, 99, 235, 1)',
                 borderWidth: 1,
                 borderRadius: 4,
                 barPercentage: 0.7,
                 categoryPercentage: 0.7
                },
                {label: 'Level Assessor',
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

        options: {responsive: true, maintainAspectRatio: false,
            interaction: {mode: 'index', intersect: false},
            plugins: {
                legend: {display: false},
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: {size: 13, weight: '600'},
                    bodyFont: {size: 12},
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        title: function (context) {
                            if (!context.length) {
                                return '';
                            }
                            const index = context[0].dataIndex;
                            const domain = filtered[index];
                            return domain.code + ' — ' + domain.name;
                        },
                        afterBody: function (context) {
                            if (!context.length) {
                                return '';
                            }
                            const index = context[0].dataIndex;
                            const domain = filtered[index];
                            const gap = domain.gap;
                            if (gap === null) {
                                return 'Gap: Belum dinilai';
                            }
                            if (gap === 0) {
                                return 'Gap: 0 (Sesuai)';
                            }
                            return 'Gap: ' +
                                (gap > 0 ? '+' + gap : gap);
                        }
                    }
                }
            },

            scales: {
                y: {beginAtZero: true, min: 0, max: 5,
                    ticks: {
                        stepSize: 1,
                        callback: function (value) {
                            const levelNames = {
                                0: '0 Incomplete',
                                1: '1 Performed',
                                2: '2 Managed',
                                3: '3 Established',
                                4: '4 Predictable',
                                5: '5 Optimizing'
                            };
                            return levelNames[value] ?? value;
                        },
                        font: {size: 11}
                    },

                    grid: {color: 'rgba(148, 163, 184, 0.2)'},
                    title: {
                        display: true,
                        text: 'Capability Level',
                        font: {size: 12, weight: '600'},
                        color: '#64748b'
                    }
                },

                x: {
                    ticks: {
                        font: {size: 11, weight: '600'},
                        color: '#334155',
                        maxRotation: 45,
                        minRotation: 0
                    },
                    grid: {display: false},
                    title: {
                        display: true,
                        text: 'Domain / Process',
                        font: {size: 12,weight: '600'},
                        color: '#64748b'
                    }
                }
            }
        }
    });
});
</script>
@endsection