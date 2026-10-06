@extends('layouts.app')

@section('title', 'Detail Persetujuan')
@section('page-title', 'Detail Persetujuan')

@section('content')
<style>
  :root { --primary: #003fb1; --primary-hover: #00349a; }

  .page-header-card {
    position: relative; overflow: hidden; padding: 1.75rem 2rem;
    border-radius: 1rem; margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 40%, #3b82f6 75%, #60a5fa 100%);
    color: #fff; box-shadow: 0 12px 30px rgba(37, 99, 235, 0.25);
  }
  .page-header-card h1 { color: #fff; font-weight: 700; }
  .page-header-card p { color: rgba(255,255,255,.88) !important; }
  .page-header-card .accent {
    position: absolute; top: -90px; right: -70px; width: 260px; height: 260px;
    border-radius: 50%; background: rgba(255,255,255,.12); filter: blur(10px);
  }
  .page-header-card .accent { pointer-events: none; }

  .info-card {
    background: #fff; border-radius: 0.75rem; padding: 1.5rem 1.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.25rem;
  }
  .info-card .card-title {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 1.05rem; font-weight: 600; margin-bottom: 1.25rem;
    padding-bottom: 0.9rem; border-bottom: 1px solid #eef0f2; color: #1e293b;
  }
  .info-card .card-title .material-symbols-outlined { color: var(--primary); font-size: 1.3rem; }

  .label-sm {
    font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em;
    text-transform: uppercase; color: #94a3b8; margin-bottom: 0.3rem;
  }

  .status-badge {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.35rem 0.9rem; border-radius: 9999px;
    font-size: 0.78rem; font-weight: 600;
  }
  .status-draft     { background: #f1f5f9; color: #475569; }
  .status-submitted { background: #fef3c7; color: #b45309; }
  .status-revision  { background: #ffedd5; color: #c2410c; }
  .status-approved  { background: #d1fae5; color: #047857; }

  .meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.1rem;
  }
  @media (max-width: 576px) {
    .meta-grid { grid-template-columns: 1fr; }
  }

  .approval-card {
    background: #fff; border-radius: 0.75rem; padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 2px solid #e0e7ff;
  }

  .approval-option {
    border: 1.5px solid #e2e8f0; border-radius: 0.6rem;
    padding: 1rem 1.15rem; cursor: pointer; transition: all 0.15s;
    display: flex; align-items: center; gap: 0.75rem;
  }
  .approval-option:hover { border-color: #c7d2fe; background: #f8fafc; }
  .approval-option.active-approve { border-color: #059669; background: #ecfdf5; }
  .approval-option.active-correct { border-color: #ea580c; background: #fff7ed; }
  .approval-option input { display: none; }

  .form-control {
    background-color: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; padding: 0.7rem 1rem; font-size: 0.9rem;
  }
  .form-control:focus {
    background-color: #fff; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 63, 177, 0.12);
  }

  .btn-primary { background-color: var(--primary) !important; border-color: var(--primary) !important; }
  .btn-success { background-color: #059669 !important; border-color: #059669 !important; }
  .btn-warning { background-color: #ea580c !important; border-color: #ea580c !important; color: #fff !important; }

  #correctionNotes { display: none; }

  .timeline-item {
    position: relative; padding-left: 1.5rem; padding-bottom: 1rem;
    border-left: 2px solid #e2e8f0;
  }
  .timeline-item:last-child { border-left-color: transparent; padding-bottom: 0; }
  .timeline-dot {
    position: absolute; left: -7px; top: 4px;
    width: 12px; height: 12px; border-radius: 50%; background: #cbd5e1;
  }
  .timeline-dot.done     { background: #059669; }
  .timeline-dot.pending  { background: #f59e0b; }
  .timeline-dot.revision { background: #ea580c; }

  .custom-confirm-overlay {
    position: fixed; inset: 0; z-index: 99999;
    display: none; align-items: center; justify-content: center;
    padding: 1rem; background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(5px);
  }
  .custom-confirm-overlay.show { display: flex; }
  .custom-confirm-modal {
    width: 100%; max-width: 430px; background: #fff;
    border-radius: 1rem; padding: 1.5rem;
    box-shadow: 0 20px 40px rgba(0,0,0,.15);
    animation: confirmModalIn .2s ease;
  }
  @keyframes confirmModalIn {
    from { opacity: 0; transform: scale(.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
  }
  .confirm-icon {
    width: 52px; height: 52px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1rem; background: #eff6ff; color: #003fb1;
  }
  .confirm-icon .material-symbols-outlined { font-size: 1.7rem; }
  .confirm-content h5 { font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: .4rem; }
  .confirm-content p { font-size: .9rem; line-height: 1.6; color: #64748b; margin-bottom: 1.4rem; }
  .confirm-actions { display: flex; justify-content: flex-end; gap: .6rem; }
  .confirm-actions .btn { border-radius: .5rem; padding: .6rem 1rem; font-size: .85rem; font-weight: 600; }
</style>

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
    'submitted' => 'Menunggu Review',
    'revision'  => 'Revisi',
    'approved'  => 'Disetujui',
    default     => $planning->status,
  };

  $roleLabel = match($role ?? '') {
    'kajur'    => 'Kajur',
    'wadir'    => 'Wadir',
    'direktur' => 'Direktur',
    'keuangan' => 'Keuangan',
    default    => ucfirst($role ?? ''),
  };

  $nextLabel = match($role ?? '') {
    'kajur'    => 'Wadir',
    'wadir'    => 'Direktur',
    'direktur' => 'Keuangan',
    default    => 'level berikutnya',
  };

  $isFinance = ($role ?? '') === 'keuangan';
@endphp

<div class="container-fluid px-4 py-3">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 position-relative">
      <div>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
          <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
          <span class="status-badge" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.35)">
            {{ $roleLabel }}
          </span>
        </div>
        <h1 class="fs-4 fw-bold mb-1">{{ $planning->name }}</h1>
        <p class="mb-0 small">
          {{ $planning->department->name ?? '—' }}
          · {{ $planning->date?->format('d M Y') ?? '—' }}
          · {{ $planning->period === 'ganjil' ? 'Semester Ganjil' : ($planning->period === 'genap' ? 'Semester Genap' : ($planning->period ?? '')) }}
        </p>
      </div>
      <a href="{{ route('planning-approvals.index') }}" class="btn btn-light d-inline-flex align-items-center gap-1">
        <span class="material-symbols-outlined" style="font-size:1.15rem">arrow_back</span>
        Kembali
      </a>
    </div>
  </div>

  <div class="row g-3">
    {{-- Kiri --}}
    <div class="col-12 col-xl-7">

      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">description</span>
          Detail Perencanaan
        </div>

        <div class="mb-4">
          <p class="label-sm">Nomor Surat</p>
          <p class="mb-0 fw-medium">{{ $planning->no_letter ?? '—' }}</p>
        </div>

        <div class="mb-4">
          <p class="label-sm">Nama Perencanaan</p>
          <p class="mb-0 fw-medium">{{ $planning->name }}</p>
        </div>

        <div class="mb-4">
          <p class="label-sm">Tujuan</p>
          <p class="mb-0">{{ $planning->objective ?: '—' }}</p>
        </div>

        @if($planning->target)
          <div class="mb-4">
            <p class="label-sm">Sasaran</p>
            <p class="mb-0">{{ $planning->target }}</p>
          </div>
        @endif

        @if($planning->success_indicator)
          <div class="mb-4">
            <p class="label-sm">Indikator Keberhasilan</p>
            <p class="mb-0">{{ $planning->success_indicator }}</p>
          </div>
        @endif

        <div class="meta-grid">
          <div>
            <p class="label-sm">Jenis</p>
            <p class="fw-medium mb-0">{{ $planning->planningType->name ?? '—' }}</p>
          </div>
          <div>
            <p class="label-sm">Periode</p>
            <p class="fw-medium mb-0">
              {{ $planning->period === 'ganjil' ? 'Semester Ganjil' : ($planning->period === 'genap' ? 'Semester Genap' : '—') }}
            </p>
          </div>
          <div>
            <p class="label-sm">Tanggal</p>
            <p class="fw-medium mb-0">{{ $planning->date?->format('d M Y') ?? '—' }}</p>
          </div>
          <div>
            <p class="label-sm">Anggaran</p>
            <p class="fw-medium mb-0 text-primary">
              Rp {{ number_format($planning->budget ?? 0, 0, ',', '.') }}
            </p>
          </div>
          <div>
            <p class="label-sm">Sumber Dana</p>
            <p class="fw-medium mb-0">{{ $planning->funding_source ?? '—' }}</p>
          </div>
          <div>
            <p class="label-sm">Unit Kerja</p>
            <p class="fw-medium mb-0">{{ $planning->department->name ?? '—' }}</p>
          </div>
        </div>

        @if($planning->budget_note)
          <div class="mt-4">
            <p class="label-sm">Keterangan Anggaran</p>
            <p class="mb-0">{{ $planning->budget_note }}</p>
          </div>
        @endif
      </div>

      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">person</span>
          Informasi Pengaju
        </div>
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
               style="width:48px;height:48px">
            {{ strtoupper(substr($planning->creator->name ?? '??', 0, 2)) }}
          </div>
          <div>
            <p class="fw-semibold mb-0">{{ $planning->creator->name ?? '—' }}</p>
            <p class="small text-secondary mb-0">
              {{ $planning->creator->role->name ?? '' }}
              · Diajukan {{ $planning->created_at?->format('d M Y, H:i') ?? '—' }}
            </p>
          </div>
        </div>
      </div>

      {{-- Timeline --}}
      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">timeline</span>
          Riwayat Approval
        </div>
        @forelse($planning->approvals->sortBy('sequence') as $ap)
          @php
            $dotClass = match($ap->status) {
              'approved' => 'done',
              'pending'  => 'pending',
              'revision' => 'revision',
              default    => '',
            };
            $apLabel = match($ap->status) {
              'approved' => 'Disetujui',
              'pending'  => 'Menunggu',
              'revision' => 'Revisi',
              default    => $ap->status,
            };
            $apRole = $ap->approver->role->name ?? '';
          @endphp
          <div class="timeline-item">
            <div class="timeline-dot {{ $dotClass }}"></div>
            <p class="fw-medium mb-0 small">
              {{ $ap->approver->name ?? 'Approver' }}
              @if($apRole)
                <span class="text-secondary">({{ ucfirst($apRole) }})</span>
              @endif
            </p>
            <p class="small text-secondary mb-0">
              {{ $apLabel }}
              @if($ap->approved_at)
                · {{ $ap->approved_at->format('d M Y H:i') }}
              @endif
            </p>
            @if($ap->note)
              <p class="small mb-0 mt-1">Catatan: {{ $ap->note }}</p>
            @endif
          </div>
        @empty
          <p class="text-secondary small mb-0">Belum ada riwayat approval.</p>
        @endforelse
      </div>
    </div>

    {{-- Kanan --}}
    <div class="col-12 col-xl-5">

      {{-- Dokumen --}}
      <div class="info-card">
        <div class="card-title">
          <span class="material-symbols-outlined">folder_open</span>
          Dokumen Pendukung
        </div>

        @if($planning->document_path)
          @php
            $ext = strtolower(pathinfo($planning->document_path, PATHINFO_EXTENSION));
            $fileName = basename($planning->document_path);
            $fileUrl = asset('storage/' . ltrim($planning->document_path, '/'));
            $icon = match(true) {
              in_array($ext, ['doc','docx']) => 'description',
              in_array($ext, ['xls','xlsx']) => 'table_chart',
              default => 'picture_as_pdf',
            };
          @endphp
          <div class="d-flex align-items-center justify-content-between gap-2 p-3 rounded-3"
               style="background:#f8fafc;border:1px solid #e2e8f0;min-width:0">
            <div class="d-flex align-items-center gap-3" style="min-width:0;flex:1">
              <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                   style="width:42px;height:42px;background:#fee2e2;color:#dc2626">
                <span class="material-symbols-outlined">{{ $icon }}</span>
              </div>
              <div style="min-width:0;overflow:hidden">
                <p class="fw-medium mb-0 small text-truncate" title="{{ $fileName }}">{{ $fileName }}</p>
                <p class="text-secondary mb-0" style="font-size:0.75rem">{{ strtoupper($ext) }}</p>
              </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
              <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-light" title="Lihat">
                <span class="material-symbols-outlined" style="font-size:1.1rem">visibility</span>
              </a>
              <a href="{{ $fileUrl }}" download class="btn btn-sm btn-light" title="Unduh">
                <span class="material-symbols-outlined" style="font-size:1.1rem">download</span>
              </a>
            </div>
          </div>
        @else
          <p class="text-secondary small mb-0">Tidak ada dokumen terlampir.</p>
        @endif
      </div>

      {{-- Form aksi --}}
      @if($canAct ?? false)
        <div class="approval-card">
          <div class="d-flex align-items-center gap-2 mb-3">
            <span class="material-symbols-outlined text-primary">
              {{ $isFinance ? 'payments' : 'fact_check' }}
            </span>
            <h2 class="fs-5 fw-bold mb-0">
              {{ $isFinance ? 'Proses Anggaran' : 'Keputusan Approval' }}
            </h2>
          </div>

          <form action="{{ route('planning-approvals.update', $myApproval->id) }}"
                method="POST" id="approvalForm">
            @csrf
            @method('PUT')

            @if($isFinance)
              {{-- ===== KEUANGAN ===== --}}
              <input type="hidden" name="action" value="process">
              <p class="small text-secondary mb-3">
                Sebagai <strong>Keuangan</strong>, Anda memproses anggaran perencanaan ini.
                Setelah diproses, status perencanaan menjadi <strong>Disetujui</strong>.
              </p>
              <div class="mb-3">
                <label class="label-sm mb-2">Catatan (Opsional)</label>
                <textarea name="note" rows="3" class="form-control"
                          placeholder="Catatan proses anggaran..."></textarea>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-success" id="btnSubmit">
                  <span class="material-symbols-outlined align-middle me-1" style="font-size:1.15rem">payments</span>
                  Proses Anggaran
                </button>
              </div>
            @else
              {{-- ===== KAJUR / WADIR / DIREKTUR ===== --}}
              <p class="label-sm mb-2">Pilih Keputusan <span class="text-danger">*</span></p>

              <div class="d-flex flex-column gap-2 mb-3">
                <label class="approval-option" id="optApprove">
                  <input type="radio" name="status" value="approved" id="statusApproved">
                  <span class="material-symbols-outlined text-success">check_circle</span>
                  <div>
                    <p class="fw-semibold mb-0">Disetujui</p>
                    <p class="small text-secondary mb-0">
                      Diteruskan ke <strong>{{ $nextLabel }}</strong>.
                    </p>
                  </div>
                </label>

                <label class="approval-option" id="optCorrect">
                  <input type="radio" name="status" value="revision" id="statusCorrection">
                  <span class="material-symbols-outlined" style="color:#ea580c">edit_note</span>
                  <div>
                    <p class="fw-semibold mb-0">Revisi</p>
                    <p class="small text-secondary mb-0">Dikembalikan ke pengaju untuk diperbaiki.</p>
                  </div>
                </label>
              </div>

              <div id="correctionNotes">
                <label class="label-sm mb-2">Catatan Revisi <span class="text-danger">*</span></label>
                <textarea name="note" id="noteCorrection" rows="4" class="form-control mb-3"
                          placeholder="Tuliskan poin yang perlu diperbaiki..."></textarea>
              </div>

              <div class="mb-3" id="optionalNotes">
                <label class="label-sm mb-2">Catatan Tambahan (Opsional)</label>
                <textarea id="noteOptional" rows="2" class="form-control"
                          placeholder="Catatan umum..."></textarea>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                  <span class="material-symbols-outlined align-middle me-1" style="font-size:1.15rem">send</span>
                  Kirim Keputusan
                </button>
              </div>
            @endif
          </form>
        </div>
      @else
        <div class="approval-card">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-secondary">info</span>
            <h2 class="fs-5 fw-bold mb-0">Status Approval Anda</h2>
          </div>
          @if($myApproval ?? null)
            <p class="mb-0 text-secondary">
              Anda sudah memproses:
              <strong>{{ match($myApproval->status) {
                'approved' => 'Disetujui',
                'revision' => 'Revisi',
                'pending'  => 'Menunggu',
                default    => $myApproval->status,
              } }}</strong>
              @if($myApproval->note)
                <br>Catatan: {{ $myApproval->note }}
              @endif
            </p>
          @else
            <p class="mb-0 text-secondary">Tidak ada aksi yang menunggu dari Anda.</p>
          @endif
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Modal konfirmasi --}}
<div class="custom-confirm-overlay" id="confirmModal">
  <div class="custom-confirm-modal">
    <div class="confirm-icon" id="confirmIcon">
      <span class="material-symbols-outlined" id="confirmIconSymbol">help</span>
    </div>
    <div class="confirm-content">
      <h5 id="confirmTitle">Konfirmasi</h5>
      <p id="confirmMessage">Apakah Anda yakin ingin melanjutkan?</p>
    </div>
    <div class="confirm-actions">
      <button type="button" class="btn btn-light" id="btnCancelConfirm">Batal</button>
      <button type="button" class="btn btn-primary" id="btnConfirmAction">Ya, Lanjutkan</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('approvalForm');
  if (!form) return;

  const isFinance = {{ $isFinance ? 'true' : 'false' }};

  const modal = document.getElementById('confirmModal');
  const btnCancel = document.getElementById('btnCancelConfirm');
  const btnConfirm = document.getElementById('btnConfirmAction');
  const confirmTitle = document.getElementById('confirmTitle');
  const confirmMessage = document.getElementById('confirmMessage');
  const confirmIcon = document.getElementById('confirmIcon');
  const confirmIconSymbol = document.getElementById('confirmIconSymbol');

  if (isFinance) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      confirmTitle.textContent = 'Proses Anggaran?';
      confirmMessage.textContent = 'Perencanaan akan ditandai sebagai disetujui setelah anggaran diproses.';
      confirmIcon.style.background = '#ecfdf5';
      confirmIcon.style.color = '#059669';
      confirmIconSymbol.textContent = 'payments';
      btnConfirm.textContent = 'Ya, Proses';
      btnConfirm.className = 'btn btn-success';
      modal.classList.add('show');
    });

    btnConfirm.addEventListener('click', function () {
      modal.classList.remove('show');
      form.submit();
    });
  } else {
    const optApprove = document.getElementById('optApprove');
    const optCorrect = document.getElementById('optCorrect');
    const radioApprove = document.getElementById('statusApproved');
    const radioCorrect = document.getElementById('statusCorrection');
    const correctionBox = document.getElementById('correctionNotes');
    const optionalNotes = document.getElementById('optionalNotes');
    const btnSubmit = document.getElementById('btnSubmit');

    function updateUI() {
      optApprove.classList.remove('active-approve');
      optCorrect.classList.remove('active-correct');
      correctionBox.style.display = 'none';
      if (optionalNotes) optionalNotes.style.display = 'block';

      btnSubmit.disabled = true;
      btnSubmit.classList.remove('btn-success', 'btn-warning', 'btn-primary');
      btnSubmit.classList.add('btn-primary');

      if (radioApprove.checked) {
        optApprove.classList.add('active-approve');
        btnSubmit.disabled = false;
        btnSubmit.classList.remove('btn-primary');
        btnSubmit.classList.add('btn-success');
        btnSubmit.innerHTML = `
          <span class="material-symbols-outlined align-middle me-1" style="font-size:1.15rem">check_circle</span>
          Setujui Perencanaan
        `;
      }

      if (radioCorrect.checked) {
        optCorrect.classList.add('active-correct');
        correctionBox.style.display = 'block';
        if (optionalNotes) optionalNotes.style.display = 'none';
        btnSubmit.disabled = false;
        btnSubmit.classList.remove('btn-primary');
        btnSubmit.classList.add('btn-warning');
        btnSubmit.innerHTML = `
          <span class="material-symbols-outlined align-middle me-1" style="font-size:1.15rem">edit_note</span>
          Kirim Revisi
        `;
      }
    }

    optApprove.addEventListener('click', function () {
      radioApprove.checked = true;
      updateUI();
    });
    optCorrect.addEventListener('click', function () {
      radioCorrect.checked = true;
      updateUI();
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      if (!radioApprove.checked && !radioCorrect.checked) {
        alert('Silakan pilih keputusan terlebih dahulu.');
        return;
      }

      if (radioCorrect.checked) {
        const note = document.getElementById('noteCorrection').value.trim();
        if (!note) {
          alert('Catatan revisi wajib diisi.');
          return;
        }
      }

      if (radioApprove.checked) {
        confirmTitle.textContent = 'Setujui Perencanaan?';
        confirmMessage.textContent = 'Perencanaan akan disetujui dan diteruskan ke tahap berikutnya.';
        confirmIcon.style.background = '#ecfdf5';
        confirmIcon.style.color = '#059669';
        confirmIconSymbol.textContent = 'check_circle';
        btnConfirm.textContent = 'Ya, Setujui';
        btnConfirm.className = 'btn btn-success';
      } else {
        confirmTitle.textContent = 'Kirim Revisi?';
        confirmMessage.textContent = 'Perencanaan akan dikembalikan kepada pengaju untuk diperbaiki.';
        confirmIcon.style.background = '#fff7ed';
        confirmIcon.style.color = '#ea580c';
        confirmIconSymbol.textContent = 'edit_note';
        btnConfirm.textContent = 'Ya, Revisi';
        btnConfirm.className = 'btn btn-warning';
      }

      modal.classList.add('show');
    });

    btnConfirm.addEventListener('click', function () {
      // Pastikan note terkirim
      let noteField = form.querySelector('input[name="note"][type="hidden"]');
      if (!noteField) {
        noteField = document.createElement('input');
        noteField.type = 'hidden';
        noteField.name = 'note';
        form.appendChild(noteField);
      }

      if (radioCorrect.checked) {
        noteField.value = document.getElementById('noteCorrection').value.trim();
        document.getElementById('noteCorrection').removeAttribute('name');
      } else {
        const optional = document.getElementById('noteOptional');
        noteField.value = optional ? optional.value.trim() : '';
        const noteCorrection = document.getElementById('noteCorrection');
        if (noteCorrection) noteCorrection.removeAttribute('name');
      }

      modal.classList.remove('show');
      form.submit();
    });

    updateUI();
  }

  btnCancel.addEventListener('click', function () {
    modal.classList.remove('show');
  });

  modal.addEventListener('click', function (e) {
    if (e.target === modal) modal.classList.remove('show');
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') modal.classList.remove('show');
  });
});
</script>
@endpush