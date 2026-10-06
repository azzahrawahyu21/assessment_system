@extends('layouts.app')

@section('content')
<style>
  :root { --primary: #003fb1; }

  .cap-hero {
    position: relative; overflow: hidden;
    border-radius: 1.25rem; padding: 1.85rem 2rem; margin-bottom: 1.5rem;
    color: #fff;
    background: linear-gradient(135deg, #0b3cc1 0%, #2563eb 45%, #60a5fa 100%);
    box-shadow: 0 18px 40px rgba(37, 99, 235, 0.22);
  }
  .cap-hero::after {
    content: ""; position: absolute; right: -50px; top: -50px;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,.12);
  }
  .cap-hero h1 { color: #fff; font-weight: 700; }
  .cap-hero p { color: rgba(255,255,255,.88); margin: 0; }

  .domain-grid { display: grid; gap: .85rem; }

  .domain-card {
    display: block; text-decoration: none; color: inherit;
    background: #fff; border-radius: 1rem; padding: 1.2rem 1.35rem;
    border: 1px solid #eef2f7;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
    transition: all .18s ease;
  }
  a.domain-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 12px 28px rgba(37, 99, 235, 0.12);
    transform: translateY(-2px);
    color: inherit;
  }
  .domain-card.is-static { cursor: default; opacity: .92; }

  .obj-code {
    display: inline-flex; align-items: center;
    padding: .22rem .55rem; border-radius: .4rem;
    background: rgba(0,63,177,.08); color: var(--primary);
    font-size: .75rem; font-weight: 800; letter-spacing: .02em;
  }
  .domain-title { font-weight: 650; color: #0f172a; }
  .domain-chip {
    font-size: .7rem; font-weight: 700;
    padding: .18rem .5rem; border-radius: .35rem;
    background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;
  }

  .priority-badge {
    display: inline-flex; padding: .25rem .7rem; border-radius: 999px;
    font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em;
  }
  .badge-high { background: #fee2e2; color: #b91c1c; }
  .badge-medium { background: #fef3c7; color: #b45309; }
  .badge-low { background: #f1f5f9; color: #475569; }

  .arrow-icon {
    color: #cbd5e1; transition: color .15s ease, transform .15s ease;
  }
  a.domain-card:hover .arrow-icon {
    color: var(--primary);
    transform: translateX(3px);
  }
</style>

@php $asRespondent = $asRespondent ?? false; @endphp

<div class="container-fluid px-4 py-3">
  <div class="cap-hero">
    <div class="position-relative d-flex flex-column flex-md-row justify-content-between gap-3">
      <div>
        <p class="small text-uppercase fw-semibold mb-1" style="letter-spacing:.06em;opacity:.85">
          Capability · Domain
        </p>
        <h1 class="fs-4 fw-bold mb-1">{{ $assessment->name }}</h1>
        <p class="small">Daftar domain dari recommended objectives</p>
      </div>
      <div class="d-flex gap-2 align-self-start">
        @unless($asRespondent)
          <a href="{{ route('assessor.capability.result', $assessment) }}" class="btn btn-light btn-sm">Hasil</a>
          <a href="{{ route('assessor.capability.index') }}" class="btn btn-outline-light btn-sm">Kembali</a>
        @else
          <a href="{{ route('capability.fill.index') }}" class="btn btn-outline-light btn-sm">Kembali</a>
        @endunless
      </div>
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

  <div class="domain-grid">
    @forelse($objectives as $obj)
      @php
        $cobit = $obj->cobit;
        $href = $asRespondent && $cobit
          ? route('capability.fill.fill', [$assessment, $cobit->id_cobit])
          : null;
        $priority = $obj->priority ?? 'low';
      @endphp

      @if($href)
        <a href="{{ $href }}" class="domain-card">
          <div class="d-flex justify-content-between align-items-center gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
              <span class="obj-code">{{ $obj->code }}</span>
              <span class="domain-title">{{ $cobit->description ?? $obj->domain ?? '-' }}</span>
              {{-- @if($obj->domain)
                <span class="domain-chip">{{ $obj->domain }}</span>
              @endif --}}
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
              <span class="priority-badge badge-{{ $priority }}">{{ $priority }}</span>
              <span class="material-symbols-outlined arrow-icon">arrow_forward</span>
            </div>
          </div>
        </a>
      @else
        <div class="domain-card is-static">
          <div class="d-flex justify-content-between align-items-center gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
              <span class="obj-code">{{ $obj->code }}</span>
              <span class="domain-title">{{ $cobit->description ?? $obj->domain ?? '-' }}</span>
              @if($obj->domain)
                <span class="domain-chip">{{ $obj->domain }}</span>
              @endif
            </div>
            <span class="priority-badge badge-{{ $priority }}">{{ $priority }}</span>
          </div>
        </div>
      @endif
    @empty
      <div class="text-center text-secondary py-5">Belum ada domain/objectives.</div>
    @endforelse
  </div>
</div>

{{-- Modal Konfirmasi Kirim Jawaban --}}
{{-- @if($asRespondent ?? false)
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="confirmSubmitModalLabel">
          Konfirmasi Pengiriman
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body pt-2">
        <div class="d-flex align-items-start gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
               style="width:48px;height:48px;background:#fef3c7;">
            <span class="material-symbols-outlined" style="color:#d97706;font-size:1.6rem;">warning</span>
          </div>
          <div>
            <p class="mb-1 fw-semibold text-dark">Kirim semua jawaban sekarang?</p>
            <p class="mb-0 small text-secondary">
              Setelah dikirim, jawaban tidak dapat diubah lagi.
              Pastikan semua domain sudah diisi dengan lengkap.
            </p>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
          Batal
        </button>
        <form action="{{ route('capability.fill.complete', $assessment) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
            <span class="material-symbols-outlined" style="font-size:1.1rem">send</span>
            Ya, Kirim Jawaban
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif --}}
@endsection