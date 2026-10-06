@extends('layouts.app')

@section('title', 'Laporan Assessment - ' . ($assessment->name ?? ''))

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

  .report-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    overflow: hidden;
    margin-bottom: 1.25rem;
    border: 1px solid #e2e8f0;
  }
  .report-card .card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 1rem 1.5rem;
    font-weight: 700;
    font-size: 0.95rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #334155;
  }
  .report-card .card-body { padding: 1.75rem 1.75rem; }

  .info-row {
    display: flex;
    margin-bottom: 0.65rem;
    font-size: 0.95rem;
  }
  .info-row .label {
    width: 200px;
    font-weight: 500;
    color: #475569;
    flex-shrink: 0;
  }
  .info-row .sep {
    width: 20px;
    color: #94a3b8;
  }
  .info-row .value {
    font-weight: 600;
    color: #0f172a;
  }

  .metric-block {
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px dashed #e2e8f0;
  }

  .btn-download {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.4rem;
    border-radius: 0.6rem;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
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

  .table-report thead th {
    background: #f1f5f9;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.04em;
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
  }
  .table-report tbody td {
    padding: 0.85rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
  }
  .table-report tbody tr:last-child td { border-bottom: none; }

  .badge-priority {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 9999px;
  }
  .p-very-high { background: #fee2e2; color: #b91c1c; }
  .p-high      { background: #ffedd5; color: #c2410c; }
  .p-medium    { background: #fef9c3; color: #a16207; }
  .p-low       { background: #f1f5f9; color: #475569; }
  .p-none      { background: #e2e8f0; color: #64748b; }
</style>

<div class="container-fluid px-4 py-3">

  <div class="page-header-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Laporan Assessment</h1>
        <p class="mb-0 small">{{ $assessment->name ?? 'Assessment #'.$assessment->id }}</p>
      </div>
      <a href="{{ route('assessor.assessments.index') }}"
         class="btn btn-light btn-sm d-inline-flex align-items-center gap-1">
        <span class="material-symbols-outlined" style="font-size:1.1rem;">arrow_back</span>
        Kembali
      </a>
    </div>
  </div>

  {{-- Kartu Ringkasan sesuai gambar --}}
  <div class="report-card">
    {{-- <div class="card-header">
      LAPORAN ASSESSMENT COBIT 2019
    </div> --}}
    <div class="card-body">

      <div class="info-row">
        <div class="label">Institusi</div>
        <div class="sep">:</div>
        <div class="value">{{ $institusi }}</div>
      </div>
      <div class="info-row">
        <div class="label">Tahun Assessment</div>
        <div class="sep">:</div>
        <div class="value">{{ $year }}</div>
      </div>

      <div class="metric-block">
        <div class="info-row">
          <div class="label">Current Capability</div>
          <div class="sep">:</div>
          <div class="value">{{ number_format($currentCapability, 2) }}</div>
        </div>
        <div class="info-row">
          <div class="label">Target Capability</div>
          <div class="sep">:</div>
          <div class="value">{{ number_format($targetCapability, 2) }}</div>
        </div>
        <div class="info-row">
          <div class="label">Average Gap</div>
          <div class="sep">:</div>
          <div class="value">{{ number_format($averageGap, 2) }}</div>
        </div>
      </div>

      <div class="metric-block">
        <div class="info-row">
          <div class="label">Objectives Assessed</div>
          <div class="sep">:</div>
          <div class="value">{{ $objectivesAssessed }}</div>
        </div>
        <div class="info-row">
          <div class="label">Very High Priority</div>
          <div class="sep">:</div>
          <div class="value">{{ $veryHighPriority }}</div>
        </div>
        <div class="info-row">
          <div class="label">High Priority</div>
          <div class="sep">:</div>
          <div class="value">{{ $highPriority }}</div>
        </div>
        <div class="info-row">
          <div class="label">Medium Priority</div>
          <div class="sep">:</div>
          <div class="value">{{ $mediumPriority }}</div>
        </div>
      </div>

      {{-- Tombol Download --}}
      <div class="d-flex flex-wrap gap-3 mt-4 pt-3 border-top">
        <a href="{{ route('assessor.assessments.laporan.pdf', $assessment) }}"
           class="btn-download btn-pdf">
          <span class="material-symbols-outlined" style="font-size:1.2rem;">picture_as_pdf</span>
          DOWNLOAD PDF
        </a>
        <a href="{{ route('assessor.assessments.laporan.excel', $assessment) }}"
           class="btn-download btn-excel">
          <span class="material-symbols-outlined" style="font-size:1.2rem;">table_view</span>
          EXPORT EXCEL
        </a>
      </div>
    </div>
  </div>

  {{-- Tabel Detail --}}
  <div class="report-card">
    <div class="card-header">Detail Hasil Setiap Domain</div>
    <div class="table-responsive">
      <table class="table table-report mb-0">
        <thead>
          <tr>
            <th class="text-center">Kode</th>
            <th class="text-center">Nama Domain</th>
            <th class="text-center">Recommended</th>
            <th class="text-center">Achieved</th>
            <th class="text-center">Gap</th>
            <th class="text-center">Priority</th>
            <th class="text-center">Catatan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $item)
            <tr>
              <td class="text-center fw-semibold">{{ $item->code }}</td>
              <td class="text-start">{{ $item->name }}</td>
              <td class="text-center">{{ $item->recommended ?? '—' }}</td>
              <td class="text-center">{{ $item->achieved ?? '—' }}</td>
              <td class="text-center fw-medium">{{ $item->gap }}</td>
              <td class="text-center">
                @php
                  $pClass = match($item->priority_label) {
                    'VERY HIGH' => 'p-very-high',
                    'HIGH'      => 'p-high',
                    'MEDIUM'    => 'p-medium',
                    'LOW'       => 'p-low',
                    default     => 'p-none',
                  };
                @endphp
                <span class="badge-priority {{ $pClass }}">{{ $item->priority_label }}</span>
              </td>
              <td class="small text-muted">{{ $item->notes ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-secondary">
                Belum ada data.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection