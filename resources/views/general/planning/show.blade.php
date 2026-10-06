@extends('layouts.app')

@section('title', 'Detail Perencanaan')
@section('page-title', 'Detail Perencanaan')

@section('content')
<style>
  :root {
    --primary: #003fb1;
    --primary-hover: #00349a;
  }

  .page-header-card {
    position: relative;
    overflow: hidden;
    padding: 1.75rem 2rem;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 40%, #3b82f6 75%, #60a5fa 100%);
    color: #fff;
    box-shadow: 0 12px 30px rgba(37, 99, 235, 0.25);
  }
  .page-header-card h1 { color: #fff; font-weight: 700; }
  .page-header-card p { color: rgba(255,255,255,.88) !important; }
  .page-header-card .accent {
    position: absolute; top: -90px; right: -70px; width: 260px; height: 260px;
    border-radius: 50%; background: rgba(255,255,255,.12); filter: blur(10px);
  }
  .page-header-card::before {
    content: ""; position: absolute; left: -60px; bottom: -80px; width: 220px; height: 220px;
    border-radius: 50%; background: rgba(255,255,255,.08);
  }
  .page-header-card::after {
    content: ""; position: absolute; top: 20px; right: 120px; width: 120px; height: 120px;
    border-radius: 50%; background: rgba(255,255,255,.06);
  }
  .page-header-card .accent,
  .page-header-card::before,
  .page-header-card::after { pointer-events: none; }

  .info-card {
    background: #fff;
    border-radius: 0.75rem;
    padding: 1.5rem 1.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 1.25rem;
  }
  .info-card .card-title {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 1.05rem; font-weight: 600; margin-bottom: 1.35rem;
    padding-bottom: 0.9rem; border-bottom: 1px solid #eef0f2; color: #1e293b;
  }
  .info-card .card-title .material-symbols-outlined {
    color: var(--primary); font-size: 1.3rem;
  }

  .label-sm {
    font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em;
    text-transform: uppercase; color: #94a3b8; margin-bottom: 0.3rem;
  }
  .value-text {
    font-size: 0.95rem; font-weight: 500; color: #1e293b;
    margin-bottom: 0; line-height: 1.5;
  }
  .value-text.muted { color: #94a3b8; font-weight: 400; }

  .status-badge {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.3rem 0.85rem; border-radius: 9999px;
    font-size: 0.75rem; font-weight: 600;
  }
  .status-draft     { background: #f1f5f9; color: #64748b; }
  .status-submitted { background: #dbeafe; color: #1d4ed8; }
  .status-revision  { background: #fef3c7; color: #b45309; }
  .status-approved  { background: #d1fae5; color: #047857; }

  .meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.35rem 1.5rem;
  }
  @media (max-width: 576px) {
    .meta-grid { grid-template-columns: 1fr; }
  }

  .detail-block { margin-bottom: 1.5rem; }
  .detail-block:last-child { margin-bottom: 0; }

  .budget-amount {
    font-size: 1.35rem; font-weight: 700; color: var(--primary);
    letter-spacing: -0.02em;
  }

  .document-box {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; padding: 0.95rem 1.15rem;
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.65rem; transition: all 0.15s ease;
  }
  .document-box:hover {
    border-color: #bfdbfe; background: #f0f7ff;
  }

  .empty-state {
    text-align: center; padding: 1.5rem 1rem; color: #94a3b8;
  }
  .empty-state .material-symbols-outlined {
    font-size: 1.9rem; display: block; margin-bottom: 0.4rem; opacity: 0.7;
  }

  .btn-primary {
    background-color: var(--primary) !important;
    border-color: var(--primary) !important;
  }
  .btn-primary:hover { background-color: var(--primary-hover) !important; }
  .btn-outline-primary {
    color: var(--primary) !important;
    border-color: var(--primary) !important;
  }
  .btn-outline-primary:hover {
    background-color: rgba(0, 63, 177, 0.06) !important;
  }

  .meta-row {
    display: flex; flex-direction: column; gap: 0.15rem;
  }
</style>

<div class="container-fluid px-0">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
          @php
            $statusClass = match($planning->status) {
              'draft'     => 'status-draft',
              'submitted' => 'status-submitted',
              'revision'  => 'status-revision',
              'approved'  => 'status-approved',
              default     => 'status-draft',
            };
            $statusLabel = match($planning->status) {
              'draft'     => 'Draft',
              'submitted' => 'Terkirim',
              'revision'  => 'Revisi',
              'approved'  => 'Disetujui',
              default     => $planning->status,
            };
          @endphp
          <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>
        <h1 class="fs-4 fw-bold mb-1">{{ $planning->name }}</h1>
        <p class="mb-0 small">
          {{ $planning->no_letter }}
          @if($planning->planningType)
            <span class="mx-1">·</span>
            {{ $planning->planningType->name }}
          @endif
          @if($planning->period)
            <span class="mx-1">·</span>
            {{ $planning->period === 'ganjil' ? 'Semester Ganjil' : 'Semester Genap' }}
          @endif
        </p>
      </div>

      <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('plannings.index') }}" class="btn btn-light d-inline-flex align-items-center gap-1 px-3">
          <span class="material-symbols-outlined" style="font-size: 1.15rem;">arrow_back</span>
          Kembali
        </a>

        @if(in_array($planning->status, ['draft', 'revision']))
          <a href="{{ route('plannings.edit', $planning) }}" class="btn btn-light d-inline-flex align-items-center gap-1 px-3">
            <span class="material-symbols-outlined" style="font-size: 1.15rem;">edit</span>
            Edit
          </a>

          <form action="{{ route('plannings.submit', $planning) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-light d-inline-flex align-items-center gap-1 px-3"
                    onclick="return confirm('Ajukan perencanaan ini ke Kajur?')">
              <span class="material-symbols-outlined" style="font-size: 1.15rem;">send</span>
              Ajukan
            </button>
          </form>
        @endif
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-3">
    {{-- Kolom Kiri --}}
    <div class="col-12 col-xl-8">

      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">assignment_turned_in</span>
          Informasi Perencanaan
        </div>

        <div class="meta-grid">
          <div class="meta-row">
            <p class="label-sm">Nomor Surat</p>
            <p class="value-text">{{ $planning->no_letter }}</p>
          </div>
          <div class="meta-row">
            <p class="label-sm">Nama Perencanaan</p>
            <p class="value-text">{{ $planning->name }}</p>
          </div>
          <div class="meta-row">
            <p class="label-sm">Tanggal</p>
            <p class="value-text">{{ $planning->date?->format('d M Y') ?? '—' }}</p>
          </div>
          <div class="meta-row">
            <p class="label-sm">Periode</p>
            <p class="value-text">
              {{ $planning->period === 'ganjil' ? 'Semester Ganjil' : ($planning->period === 'genap' ? 'Semester Genap' : '—') }}
            </p>
          </div>
          <div class="meta-row">
            <p class="label-sm">Jenis Perencanaan</p>
            <p class="value-text">{{ $planning->planningType->name ?? '—' }}</p>
          </div>
          <div class="meta-row">
            <p class="label-sm">Unit Kerja</p>
            <p class="value-text">{{ $planning->department->name ?? '—' }}</p>
          </div>
          <div class="meta-row">
            <p class="label-sm">Dibuat Oleh</p>
            <p class="value-text">{{ $planning->creator->name ?? '—' }}</p>
          </div>
          <div class="meta-row">
            <p class="label-sm">Dibuat Pada</p>
            <p class="value-text">{{ $planning->created_at?->format('d M Y, H:i') ?? '—' }}</p>
          </div>
        </div>
      </div>

      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">description</span>
          Detail Perencanaan
        </div>

        <div class="detail-block">
          <p class="label-sm">Tujuan</p>
          <p class="value-text" style="white-space: pre-line;">{{ $planning->objective ?: '—' }}</p>
        </div>

        <div class="detail-block">
          <p class="label-sm">Sasaran</p>
          <p class="value-text">{{ $planning->target ?: '—' }}</p>
        </div>

        <div class="detail-block">
          <p class="label-sm">Indikator Keberhasilan</p>
          <p class="value-text" style="white-space: pre-line;">{{ $planning->success_indicator ?: '—' }}</p>
        </div>
      </div>

      {{-- Riwayat Persetujuan (jika ada) --}}
      @if($planning->approvals && $planning->approvals->count())
        <div class="info-card">
          <div class="card-title">
            <span class="material-symbols-outlined">approval</span>
            Riwayat Persetujuan
          </div>
          <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
              <thead>
                <tr class="text-secondary small">
                  <th>Approver</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                </tr>
              </thead>
              <tbody>
                @foreach($planning->approvals as $approval)
                  <tr>
                    <td class="small fw-medium">{{ $approval->approver->name ?? '—' }}</td>
                    <td class="small text-secondary">{{ $approval->approver->role->name ?? '—' }}</td>
                    <td>
                      @php
                        $aClass = match($approval->status) {
                          'pending'  => 'status-submitted',
                          'approved' => 'status-approved',
                          'revision' => 'status-revision',
                          'rejected' => 'status-revision',
                          default    => 'status-draft',
                        };
                      @endphp
                      <span class="status-badge {{ $aClass }}">{{ ucfirst($approval->status) }}</span>
                    </td>
                    <td class="small text-secondary">
                      {{ $approval->updated_at?->format('d M Y, H:i') ?? '—' }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </div>

    {{-- Kolom Kanan --}}
    <div class="col-12 col-xl-4">

      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">payments</span>
          Anggaran
        </div>

        <div class="detail-block">
          <p class="label-sm">Estimasi Biaya</p>
          <p class="budget-amount mb-0">
            Rp {{ number_format($planning->budget ?? 0, 0, ',', '.') }}
          </p>
        </div>

        <div class="detail-block">
          <p class="label-sm">Sumber Dana</p>
          <p class="value-text">{{ $planning->funding_source ?: '—' }}</p>
        </div>

        <div class="detail-block">
          <p class="label-sm">Keterangan Anggaran</p>
          <p class="value-text {{ empty($planning->budget_note) ? 'muted' : '' }}">
            {{ $planning->budget_note ?: 'Tidak ada keterangan' }}
          </p>
        </div>
      </div>

      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">attach_file</span>
          Dokumen
        </div>

        @if($planning->document_path)
          @php
            $ext = strtolower(pathinfo($planning->document_path, PATHINFO_EXTENSION));
            $icon = match(true) {
              in_array($ext, ['doc', 'docx']) => ['description', 'text-primary'],
              in_array($ext, ['xls', 'xlsx']) => ['table_chart', 'text-success'],
              default => ['picture_as_pdf', 'text-danger'],
            };
            $fileUrl = asset('storage/' . ltrim($planning->document_path, '/'));
          @endphp

          <a href="{{ $fileUrl }}" target="_blank" class="text-decoration-none text-dark">
            <div class="document-box">
              <div class="d-flex align-items-center gap-2 overflow-hidden">
                <span class="material-symbols-outlined {{ $icon[1] }} flex-shrink-0">{{ $icon[0] }}</span>
                <div class="overflow-hidden">
                  <p class="small mb-0 fw-medium text-truncate">{{ basename($planning->document_path) }}</p>
                  <p class="text-secondary mb-0" style="font-size: 0.7rem;">Klik untuk unduh / lihat</p>
                </div>
              </div>
              <span class="material-symbols-outlined text-secondary flex-shrink-0" style="font-size: 1.2rem;">download</span>
            </div>
          </a>
        @else
          <div class="empty-state">
            <span class="material-symbols-outlined">folder_off</span>
            <p class="small mb-0">Tidak ada dokumen</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection