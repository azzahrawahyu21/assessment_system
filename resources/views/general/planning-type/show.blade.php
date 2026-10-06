@extends('layouts.app')

@section('title', 'Detail Jenis Perencanaan')
@section('page-title', 'Detail Jenis Perencanaan')

@section('content')
<style>
  :root { --primary: #003fb1; --primary-hover: #00349a; }

  .page-header-card {
    position: relative; overflow: hidden; padding: 1.75rem 2rem; border-radius: 1rem;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 40%, #3b82f6 75%, #60a5fa 100%);
    color: #fff; box-shadow: 0 12px 30px rgba(37, 99, 235, 0.25);
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
  .page-header-card .accent, .page-header-card::before, .page-header-card::after { pointer-events: none; }

  .form-card {
    background: #fff; border-radius: 0.75rem; padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.25rem;
  }
  .form-card .card-title {
    display: flex; align-items: center; gap: 0.5rem; font-size: 1.15rem;
    font-weight: 600; margin-bottom: 1.25rem; padding-bottom: 0.9rem;
    border-bottom: 1px solid #eef0f2;
  }
  .form-card .card-title .material-symbols-outlined { color: var(--primary); font-size: 1.35rem; }

  .info-label {
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.05em;
    text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;
  }
  .info-value { font-size: 0.95rem; color: #1e293b; }

  .btn-primary { background-color: var(--primary) !important; border-color: var(--primary) !important; }
</style>

<div class="container-fluid px-0">

  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">{{ $planningType->name }}</h1>
        <p class="mb-0 small">Detail jenis perencanaan.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('planning-types.edit', $planningType) }}"
           class="btn btn-light d-inline-flex align-items-center gap-2">
          <span class="material-symbols-outlined" style="font-size:1.15rem">edit</span>
          Edit
        </a>
        <a href="{{ route('planning-types.index') }}"
           class="btn btn-light d-inline-flex align-items-center gap-2">
          <span class="material-symbols-outlined" style="font-size:1.15rem">arrow_back</span>
          Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="form-card">
    <div class="card-title">
      <span class="material-symbols-outlined">info</span>
      Informasi Jenis
    </div>

    <div class="row g-4">
      <div class="col-md-6">
        <div class="info-label">Nama Jenis</div>
        <div class="info-value fw-semibold">{{ $planningType->name }}</div>
      </div>
      <div class="col-md-6">
        <div class="info-label">Jumlah Perencanaan</div>
        <div class="info-value fw-semibold">{{ $planningType->plannings_count ?? 0 }}</div>
      </div>
      <div class="col-12">
        <div class="info-label">Deskripsi</div>
        <div class="info-value">{{ $planningType->description ?: '—' }}</div>
      </div>
      <div class="col-md-6">
        <div class="info-label">Dibuat</div>
        <div class="info-value">{{ $planningType->created_at?->format('d M Y, H:i') ?? '—' }}</div>
      </div>
      <div class="col-md-6">
        <div class="info-label">Diperbarui</div>
        <div class="info-value">{{ $planningType->updated_at?->format('d M Y, H:i') ?? '—' }}</div>
      </div>
    </div>
  </div>

</div>
@endsection