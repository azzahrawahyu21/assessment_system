@extends('layouts.app')

@section('content')
<style>
  :root { --primary: #003fb1; --primary-hover: #00349a; }

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
    background: #fff; border-radius: 0.75rem; padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.25rem;
  }
  .info-card .card-title {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 1.1rem; font-weight: 600; margin-bottom: 1.15rem;
    padding-bottom: 0.85rem; border-bottom: 1px solid #eef0f2;
  }
  .info-card .card-title .material-symbols-outlined { color: var(--primary); font-size: 1.35rem; }

  .label-sm {
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.04em;
    text-transform: uppercase; color: #6b7280; margin-bottom: 0.25rem;
  }

  .status-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 90px; height: 28px; padding: 0.3rem 0.75rem;
    border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
    border: 1.5px solid transparent;
  }
  .status-aktif { background: #d1fae5; color: #047857; border-color: #6ee7b7; }
  .status-nonaktif { background: #f1f5f9; color: #64748b; border-color: #cbd5e1; }

  .level-block {
    border: 1px solid #e2e8f0; border-radius: 0.75rem;
    margin-bottom: 1rem; overflow: hidden;
  }
  .level-header {
    background: #f8fafc; padding: 0.85rem 1.15rem;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid #e2e8f0;
  }
  .level-body { padding: 1rem 1.15rem; }
  .level-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 0.45rem;
    background: var(--primary); color: #fff; font-weight: 700; font-size: 0.85rem;
  }

  .statement-row {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; padding: 0.85rem 1rem; margin-bottom: 0.55rem;
  }
  .statement-row:last-child { margin-bottom: 0; }

  .btn-outline-primary { color: var(--primary) !important; border-color: var(--primary) !important; }
  .btn-outline-primary:hover { background: var(--primary) !important; color: #fff !important; }
</style>

@php
  $levelNames = [
    0 => ['name' => 'Incomplete', 'desc' => 'Tidak ada proses'],
    1 => ['name' => 'Performed', 'desc' => 'Proses dijalankan'],
    2 => ['name' => 'Managed', 'desc' => 'Direncanakan & dimonitor'],
    3 => ['name' => 'Established', 'desc' => 'Terstandar'],
    4 => ['name' => 'Predictable', 'desc' => 'Terukur & terkontrol'],
    5 => ['name' => 'Optimizing', 'desc' => 'Terus ditingkatkan'],
  ];
  $grouped = $cobitDomain->statements->groupBy('level');
@endphp

<div class="container-fluid px-4 py-3">

  {{-- Header Gradasi Biru --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">{{ $cobitDomain->name }}</h1>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <span class="small" style="color:rgba(255,255,255,.85)">Domain COBIT 2019</span>
        </div>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('administrator.cobit-domains.index') }}" class="btn btn-light d-inline-flex align-items-center gap-1">
          <span class="material-symbols-outlined" style="font-size:1.15rem">arrow_back</span>
          Kembali
        </a>
        <a href="{{ route('administrator.cobit-domains.edit', $cobitDomain) }}" class="btn btn-light d-inline-flex align-items-center gap-1">
          <span class="material-symbols-outlined" style="font-size:1.15rem">edit</span>
          Edit
        </a>
      </div>
    </div>
  </div>

  <div class="row g-3">
    {{-- Sidebar Info --}}
    <div class="col-12 col-xl-4">
      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">hub</span>
          Informasi Domain
        </div>

        <p class="label-sm">Kode</p>
        <p class="fw-semibold mb-3">
          <span class="badge bg-primary text-white px-2 py-1">{{ $cobitDomain->code }}</span>
        </p>

        <p class="label-sm">Nama</p>
        <p class="fw-semibold mb-3">{{ $cobitDomain->name }}</p>

        <p class="label-sm">Deskripsi</p>
        <p class="mb-3 text-secondary">{{ $cobitDomain->description ?? '—' }}</p>

        {{-- <div class="row g-3">
          <div class="col-6">
            <p class="label-sm">Urutan</p>
            <p class="fw-semibold mb-0">{{ $cobitDomain->sort_order }}</p>
          </div>
          <div class="col-6">
            <p class="label-sm">Status</p>
            <span class="status-badge {{ $cobitDomain->is_active ? 'status-aktif' : 'status-nonaktif' }}">
              {{ $cobitDomain->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>
        </div> --}}

        <hr class="my-3">

        <p class="label-sm">Total Pernyataan</p>
        <p class="fw-bold fs-5 mb-0 text-primary">{{ $cobitDomain->statements->count() }} <span class="fs-6 fw-normal text-secondary">pernyataan</span></p>
      </div>
    </div>

    {{-- Pernyataan per Level --}}
    <div class="col-12 col-xl-8">
      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">layers</span>
          Pernyataan per Level
        </div>

        @foreach($levelNames as $levelNum => $info)
          @php $items = $grouped->get($levelNum, collect()); @endphp
          <div class="level-block">
            <div class="level-header">
              <div class="d-flex align-items-center gap-3">
                <span class="level-badge">{{ $levelNum }}</span>
                <div>
                  <div class="fw-semibold">Level {{ $levelNum }} — {{ $info['name'] }}</div>
                  <div class="small text-secondary">{{ $info['desc'] }}</div>
                </div>
              </div>
              <span class="badge rounded-pill px-3" style="background:rgba(0,63,177,0.1);color:#003fb1;">
                {{ $items->count() }} pernyataan
              </span>
            </div>

            @if($items->count())
              <div class="level-body">
                @foreach($items as $i => $st)
                  <div class="statement-row">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-secondary border">#{{ $i + 1 }}</span>
                        @if($st->code)
                          <span class="badge" style="background:rgba(0,63,177,0.1);color:#003fb1;">{{ $st->code }}</span>
                        @endif
                      </div>
                    </div>
                    <p class="mb-0">{{ $st->statement }}</p>
                  </div>
                @endforeach
              </div>
            @else
              <div class="level-body">
                <p class="small text-secondary mb-0 fst-italic">Belum ada pernyataan di level ini.</p>
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection