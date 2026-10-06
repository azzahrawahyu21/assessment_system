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

<div class="container-fluid px-4 py-3">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Tambah Pelaksanaan</h1>
        <p class="mb-0 small">Catat realisasi dan bukti pelaksanaan kegiatan.</p>
      </div>
      <a href="{{ route('implementations.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2">
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

  <form action="{{ route('implementations.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="planning_id" value="{{ $planning->id_plan }}">

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
                <p class="small text-secondary mb-1">Judul Perencanaan</p>
                <p class="fw-semibold mb-0">{{ $planning->name }}</p>
              </div>
              <div class="col-md-4">
                <p class="small text-secondary mb-1">No. Surat</p>
                <p class="fw-semibold mb-0">{{ $planning->no_letter }}</p>
              </div>
              <div class="col-md-4">
                <p class="small text-secondary mb-1">Tanggal Perencanaan</p>
                <p class="fw-semibold mb-0">
                  {{ $planning->date?->format('d M Y') ?? '—' }}
                </p>
              </div>
              <div class="col-md-4">
                <p class="small text-secondary mb-1">Periode</p>
                <p class="fw-semibold mb-0">
                  @if($planning->period === 'ganjil')
                    Semester Ganjil
                  @elseif($planning->period === 'genap')
                    Semester Genap
                  @else
                    {{ $planning->period ?? '—' }}
                  @endif
                </p>
              </div>
              <div class="col-md-4">
                <p class="small text-secondary mb-1">Jenis</p>
                <p class="fw-semibold mb-0">{{ $planning->planningType?->name ?? '—' }}</p>
              </div>
              <div class="col-md-4">
                <p class="small text-secondary mb-1">Unit Kerja</p>
                <p class="fw-semibold mb-0">{{ $planning->department->name ?? '—' }}</p>
              </div>
              <div class="col-md-4">
                <p class="small text-secondary mb-1">Anggaran</p>
                <p class="fw-semibold mb-0 text-primary">
                  Rp {{ number_format($planning->budget ?? 0, 0, ',', '.') }}
                </p>
              </div>
              @if($planning->funding_source)
              <div class="col-md-4">
                <p class="small text-secondary mb-1">Sumber Dana</p>
                <p class="fw-semibold mb-0">{{ $planning->funding_source }}</p>
              </div>
              @endif
              @if($planning->objective)
              <div class="col-12">
                <p class="small text-secondary mb-1">Tujuan</p>
                <p class="fw-semibold mb-0">{{ $planning->objective }}</p>
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
              <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
              <input type="date" name="date"
                     class="form-control @error('date') is-invalid @enderror"
                     value="{{ old('date', date('Y-m-d')) }}" required>
              @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label class="form-label">Hasil / Capaian</label>
              <textarea name="result" rows="4"
                        class="form-control @error('result') is-invalid @enderror"
                        placeholder="Jelaskan hasil atau capaian dari pelaksanaan kegiatan ini...">{{ old('result') }}</textarea>
              @error('result')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label class="form-label">Kendala / Hambatan</label>
              <textarea name="obstacle" rows="3"
                        class="form-control @error('obstacle') is-invalid @enderror"
                        placeholder="Sebutkan kendala yang dihadapi (opsional)...">{{ old('obstacle') }}</textarea>
              @error('obstacle')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Kolom Kanan --}}
      <div class="col-12 col-xl-4">
        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">cloud_upload</span>
            Bukti Pelaksanaan
          </div>

          <div id="evidenceContainer">
            <div class="evidence-item" data-index="0">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-secondary">Bukti #1</span>
              </div>
              <div class="mb-2">
                <label class="form-label">Jenis Bukti</label>
                <select name="evidences[0][type]" class="form-select form-select-sm">
                  <option value="Foto">Foto</option>
                  <option value="PDF">PDF</option>
                  <option value="Video">Video</option>
                  <option value="Dokumen">Dokumen</option>
                </select>
              </div>
              <div>
                <label class="form-label">File</label>
                <input type="file" name="evidences[0][file]"
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
            Format: JPG, PNG, PDF, MP4, DOC, DOCX (Maks. 10MB per file)
          </p>
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="d-flex justify-content-end gap-2 mt-3 mb-2">
      <a href="{{ route('implementations.index') }}" class="btn btn-light px-4">Batal</a>
      <button type="submit" class="btn btn-primary px-4">
        <span class="material-symbols-outlined align-middle me-1" style="font-size: 1.15rem;">save</span>
        Simpan Pelaksanaan
      </button>
    </div>
  </form>
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
          <select name="evidences[${evidenceIndex}][type]" class="form-select form-select-sm">
            <option value="Foto">Foto</option>
            <option value="PDF">PDF</option>
            <option value="Video">Video</option>
            <option value="Dokumen">Dokumen</option>
          </select>
        </div>
        <div>
          <label class="form-label">File</label>
          <input type="file" name="evidences[${evidenceIndex}][file]"
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