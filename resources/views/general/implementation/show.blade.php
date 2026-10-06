@extends('layouts.app')

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

  .form-card {
    background: #fff; border-radius: 0.75rem; padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.25rem;
  }
  .form-card .card-title {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 1.15rem; font-weight: 600; margin-bottom: 1.25rem;
    padding-bottom: 0.9rem; border-bottom: 1px solid #eef0f2;
  }
  .form-card .card-title .material-symbols-outlined {
    color: var(--primary); font-size: 1.35rem;
  }

  .info-box {
    background: #f0f5ff; border: 1px solid #dbe4ff;
    border-radius: 0.6rem; padding: 1rem 1.25rem;
  }

  .detail-label {
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.05em;
    text-transform: uppercase; color: #6b7280; margin-bottom: 0.25rem;
  }
  .detail-value {
    font-size: 0.95rem; font-weight: 500; color: #1e293b; margin-bottom: 0;
  }

  .evidence-card {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; padding: 0.9rem 1rem;
    display: flex; align-items: center; gap: 0.75rem;
    margin-bottom: 0.6rem;
  }
  .evidence-icon {
    width: 40px; height: 40px; border-radius: 0.5rem;
    background: rgba(0, 63, 177, 0.1); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .evidence-icon .material-symbols-outlined { font-size: 1.25rem; }

  .btn-primary {
    background-color: var(--primary) !important;
    border-color: var(--primary) !important;
  }
  .btn-primary:hover {
    background-color: var(--primary-hover) !important;
  }
  .btn-outline-primary {
    color: var(--primary) !important;
    border-color: var(--primary) !important;
  }
</style>

@php
  $planning = $implementation->planning;
@endphp

<div class="container-fluid px-4 py-3">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Detail Pelaksanaan</h1>
        <p class="mb-0 small">{{ $planning->name ?? '—' }}</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('implementations.edit', $implementation) }}"
           class="btn btn-light d-inline-flex align-items-center gap-2">
          <span class="material-symbols-outlined" style="font-size: 1.15rem;">edit</span>
          Edit
        </a>
        <a href="{{ route('implementations.index') }}"
           class="btn btn-light d-inline-flex align-items-center gap-2">
          <span class="material-symbols-outlined" style="font-size: 1.15rem;">arrow_back</span>
          Kembali
        </a>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-3">
    {{-- Kolom Kiri --}}
    <div class="col-12 col-xl-8">

      {{-- Info Perencanaan --}}
      <div class="form-card">
        <div class="card-title">
          <span class="material-symbols-outlined">assignment</span>
          Informasi Perencanaan
        </div>
        <div class="info-box mb-0">
          <div class="row g-3">
            <div class="col-12">
              <p class="detail-label">Judul Perencanaan</p>
              <p class="detail-value">{{ $planning->name ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <p class="detail-label">No. Surat</p>
              <p class="detail-value">{{ $planning->no_letter ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <p class="detail-label">Tanggal Perencanaan</p>
              <p class="detail-value">{{ $planning->date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <p class="detail-label">Periode</p>
              <p class="detail-value">
                @if(($planning->period ?? '') === 'ganjil')
                  Semester Ganjil
                @elseif(($planning->period ?? '') === 'genap')
                  Semester Genap
                @else
                  {{ $planning->period ?? '—' }}
                @endif
              </p>
            </div>
            <div class="col-md-4">
              <p class="detail-label">Jenis</p>
              <p class="detail-value">{{ $planning->planningType?->name ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <p class="detail-label">Unit Kerja</p>
              <p class="detail-value">{{ $planning->department->name ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <p class="detail-label">Anggaran</p>
              <p class="detail-value text-primary">
                Rp {{ number_format($planning->budget ?? 0, 0, ',', '.') }}
              </p>
            </div>
            @if($planning->funding_source ?? null)
            <div class="col-md-4">
              <p class="detail-label">Sumber Dana</p>
              <p class="detail-value">{{ $planning->funding_source }}</p>
            </div>
            @endif
            @if($planning->objective ?? null)
            <div class="col-12">
              <p class="detail-label">Tujuan</p>
              <p class="detail-value">{{ $planning->objective }}</p>
            </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Detail Pelaksanaan --}}
      <div class="form-card">
        <div class="card-title">
          <span class="material-symbols-outlined">play_circle</span>
          Detail Pelaksanaan
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <p class="detail-label">Tanggal Pelaksanaan</p>
            <p class="detail-value">{{ $implementation->date?->format('d M Y') ?? '—' }}</p>
          </div>
          <div class="col-md-6">
            <p class="detail-label">Dicatat Oleh</p>
            <p class="detail-value">{{ $implementation->creator->name ?? '—' }}</p>
          </div>
          <div class="col-12">
            <p class="detail-label">Hasil / Capaian</p>
            <p class="detail-value" style="white-space: pre-line;">
              {{ $implementation->result ?: '—' }}
            </p>
          </div>
          <div class="col-12">
            <p class="detail-label">Kendala / Hambatan</p>
            <p class="detail-value" style="white-space: pre-line;">
              {{ $implementation->obstacle ?: '—' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- Kolom Kanan --}}
    <div class="col-12 col-xl-4">
      <div class="form-card">
        <div class="card-title">
          <span class="material-symbols-outlined">attach_file</span>
          Bukti Pelaksanaan
          <span class="badge bg-primary ms-auto" style="font-size: 0.7rem;">
            {{ $implementation->evidences->count() }} file
          </span>
        </div>

        @forelse($implementation->evidences as $evidence)
          @php
            $ext = strtolower(pathinfo($evidence->file_name ?? $evidence->file_path, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
            $icon = match(true) {
              $isImage => 'image',
              $ext === 'pdf' => 'picture_as_pdf',
              in_array($ext, ['mp4', 'avi', 'mov'], true) => 'videocam',
              default => 'description',
            };
            $url = asset('storage/' . ltrim($evidence->file_path, '/'));
          @endphp
          <div class="evidence-card">
            <div class="evidence-icon">
              <span class="material-symbols-outlined">{{ $icon }}</span>
            </div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="fw-medium text-truncate" style="font-size: 0.875rem;">
                {{ $evidence->file_name ?? basename($evidence->file_path) }}
              </div>
              <div class="small text-secondary">
                {{ $evidence->file_type ?? strtoupper($ext) }}
              </div>
            </div>
            <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary"
               title="Buka file">
              <span class="material-symbols-outlined" style="font-size: 1.1rem;">open_in_new</span>
            </a>
          </div>
        @empty
          <p class="text-secondary small mb-0 text-center py-3">
            Belum ada bukti yang diunggah.
          </p>
        @endforelse
      </div>

      <div class="form-card">
        <div class="card-title">
          <span class="material-symbols-outlined">info</span>
          Meta
        </div>
        <div class="row g-2">
          <div class="col-6">
            <p class="detail-label">Dibuat</p>
            <p class="detail-value small">
              {{ $implementation->created_at?->format('d M Y H:i') ?? '—' }}
            </p>
          </div>
          <div class="col-6">
            <p class="detail-label">Diperbarui</p>
            <p class="detail-value small">
              {{ $implementation->updated_at?->format('d M Y H:i') ?? '—' }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection