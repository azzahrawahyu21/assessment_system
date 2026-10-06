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

  .form-label {
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.05em;
    text-transform: uppercase; color: #6b7280; margin-bottom: 0.4rem;
  }
  .form-control, .form-select {
    background-color: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; padding: 0.7rem 1rem; font-size: 0.9rem;
  }
  .form-control:focus, .form-select:focus {
    background-color: #fff; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 63, 177, 0.12);
  }

  .info-box {
    background: #f0f5ff; border: 1px solid #dbe4ff;
    border-radius: 0.6rem; padding: 1rem 1.25rem;
  }

  .evidence-item {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; padding: 1rem; margin-bottom: 0.75rem;
  }
  .evidence-existing {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; padding: 0.75rem 1rem;
    display: flex; align-items: center; gap: 0.75rem;
    margin-bottom: 0.6rem;
  }

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
        <h1 class="fs-4 fw-bold mb-1">Edit Pelaksanaan</h1>
        <p class="mb-0 small">{{ $planning->name ?? '—' }}</p>
      </div>
      <a href="{{ route('implementations.show', $implementation) }}"
         class="btn btn-light d-inline-flex align-items-center gap-2">
        <span class="material-symbols-outlined" style="font-size: 1.15rem;">arrow_back</span>
        Kembali
      </a>
    </div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Form update (hanya data di kiri; file di kanan pakai form="formUpdate") --}}
  <form id="formUpdate"
        action="{{ route('implementations.update', $implementation) }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf
    @method('PUT')
  </form>

  <div class="row g-3">
    {{-- ===== KIRI ===== --}}
    <div class="col-12 col-xl-8">
      <div class="form-card">
        <div class="card-title">
          <span class="material-symbols-outlined">assignment</span>
          Informasi Perencanaan
        </div>
        <div class="info-box mb-0">
          <div class="row g-3">
            <div class="col-12">
              <p class="small text-secondary mb-1">Judul Perencanaan</p>
              <p class="fw-semibold mb-0">{{ $planning->name }}</p>
            </div>
            <div class="col-md-4">
              <p class="small text-secondary mb-1">No. Surat</p>
              <p class="fw-semibold mb-0">{{ $planning->no_letter }}</p>
            </div>
            <div class="col-md-4">
              <p class="small text-secondary mb-1">Tanggal Perencanaan</p>
              <p class="fw-semibold mb-0">{{ $planning->date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div class="col-md-4">
              <p class="small text-secondary mb-1">Unit Kerja</p>
              <p class="fw-semibold mb-0">{{ $planning->department->name ?? '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="form-card">
        <div class="card-title">
          <span class="material-symbols-outlined">play_circle</span>
          Detail Pelaksanaan
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
            <input type="date" name="date" form="formUpdate"
                   class="form-control @error('date') is-invalid @enderror"
                   value="{{ old('date', $implementation->date?->format('Y-m-d')) }}"
                   required>
            @error('date')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-12">
            <label class="form-label">Hasil / Capaian</label>
            <textarea name="result" form="formUpdate" rows="4"
                      class="form-control @error('result') is-invalid @enderror"
                      placeholder="Jelaskan hasil atau capaian...">{{ old('result', $implementation->result) }}</textarea>
            @error('result')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-12">
            <label class="form-label">Kendala / Hambatan</label>
            <textarea name="obstacle" form="formUpdate" rows="3"
                      class="form-control @error('obstacle') is-invalid @enderror"
                      placeholder="Sebutkan kendala (opsional)...">{{ old('obstacle', $implementation->obstacle) }}</textarea>
            @error('obstacle')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ===== KANAN ===== --}}
    <div class="col-12 col-xl-4">

      {{-- Bukti saat ini (form hapus terpisah) --}}
      <div class="form-card">
        <div class="card-title">
          <span class="material-symbols-outlined">attach_file</span>
          Bukti Saat Ini
          <span class="badge bg-secondary ms-auto" style="font-size: 0.7rem;">
            {{ $implementation->evidences->count() }}
          </span>
        </div>

        @forelse($implementation->evidences as $evidence)
          @php $url = asset('storage/' . ltrim($evidence->file_path, '/')); @endphp
          <div class="evidence-existing">
            <div class="flex-grow-1 overflow-hidden">
              <div class="fw-medium text-truncate" style="font-size: 0.875rem;">
                {{ $evidence->file_name ?? basename($evidence->file_path) }}
              </div>
              <div class="small text-secondary">{{ $evidence->file_type ?? '—' }}</div>
            </div>
            <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka">
              <span class="material-symbols-outlined" style="font-size: 1rem;">open_in_new</span>
            </a>
            <form action="{{ route('implementations.evidences.destroy', $evidence) }}"
                  method="POST" class="d-inline"
                  onsubmit="return confirm('Hapus bukti ini?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                <span class="material-symbols-outlined" style="font-size: 1rem;">delete</span>
              </button>
            </form>
          </div>
        @empty
          <p class="text-secondary small mb-0 text-center py-2">Belum ada bukti.</p>
        @endforelse
      </div>

      {{-- Tambah bukti baru (ikut formUpdate) --}}
      <div class="form-card">
        <div class="card-title">
          <span class="material-symbols-outlined">cloud_upload</span>
          Tambah Bukti Baru
        </div>

        <div id="evidenceContainer">
          <div class="evidence-item" data-index="0">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="small fw-semibold text-secondary">Bukti #1</span>
            </div>
            <div class="mb-2">
              <label class="form-label">Jenis Bukti</label>
              <select name="evidences[0][type]" form="formUpdate" class="form-select form-select-sm">
                <option value="Foto">Foto</option>
                <option value="PDF">PDF</option>
                <option value="Video">Video</option>
                <option value="Dokumen">Dokumen</option>
              </select>
            </div>
            <div>
              <label class="form-label">File</label>
              <input type="file" name="evidences[0][file]" form="formUpdate"
                     class="form-control form-control-sm"
                     accept=".jpg,.jpeg,.png,.pdf,.mp4,.doc,.docx">
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-2" id="addEvidenceBtn">
          <span class="material-symbols-outlined align-middle" style="font-size: 1.1rem;">add</span>
          Tambah Bukti Lain
        </button>
        <p class="small text-secondary mt-3 mb-0">
          Format: JPG, PNG, PDF, MP4, DOC, DOCX (Maks. 10MB)
        </p>
      </div>
    </div>
  </div>

  {{-- Tombol aksi --}}
  <div class="d-flex justify-content-end gap-2 mt-2 mb-2">
    <a href="{{ route('implementations.show', $implementation) }}" class="btn btn-light px-4">Batal</a>
    <button type="submit" form="formUpdate" class="btn btn-primary px-4">
      <span class="material-symbols-outlined align-middle me-1" style="font-size: 1.15rem;">save</span>
      Simpan Perubahan
    </button>
  </div>

</div>
@endsection

@push('scripts')
<script>
  let evidenceIndex = 1;
  const container = document.getElementById('evidenceContainer');
  const addBtn = document.getElementById('addEvidenceBtn');

  addBtn?.addEventListener('click', function () {
    const html = `
      <div class="evidence-item" data-index="${evidenceIndex}">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="small fw-semibold text-secondary">Bukti #${evidenceIndex + 1}</span>
          <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-evidence">
            <span class="material-symbols-outlined" style="font-size: 1.1rem;">close</span>
          </button>
        </div>
        <div class="mb-2">
          <label class="form-label">Jenis Bukti</label>
          <select name="evidences[${evidenceIndex}][type]" form="formUpdate" class="form-select form-select-sm">
            <option value="Foto">Foto</option>
            <option value="PDF">PDF</option>
            <option value="Video">Video</option>
            <option value="Dokumen">Dokumen</option>
          </select>
        </div>
        <div>
          <label class="form-label">File</label>
          <input type="file" name="evidences[${evidenceIndex}][file]" form="formUpdate"
                 class="form-control form-control-sm"
                 accept=".jpg,.jpeg,.png,.pdf,.mp4,.doc,.docx">
        </div>
      </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    evidenceIndex++;
  });

  container?.addEventListener('click', function (e) {
    if (e.target.closest('.remove-evidence')) {
      e.target.closest('.evidence-item').remove();
    }
  });
</script>
@endpush