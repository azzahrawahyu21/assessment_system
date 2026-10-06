@extends('layouts.app')

@section('title', 'Tambah Perencanaan')
@section('page-title', 'Tambah Perencanaan')

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

  .badge-id {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.4rem 1rem;
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 9999px;
    font-size: 0.75rem; font-weight: 600;
    letter-spacing: 0.04em; text-transform: uppercase;
    color: #fff;
  }

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

  .upload-zone {
    border: 2px dashed #e2e8f0; border-radius: 0.75rem;
    padding: 2rem 1.5rem; text-align: center; cursor: pointer;
    transition: all 0.2s; background: #f8fafc;
  }
  .upload-zone:hover {
    border-color: var(--primary); background: #f0f5ff;
  }
  .upload-zone .material-symbols-outlined {
    font-size: 2.75rem; color: #94a3b8;
  }
  .upload-zone:hover .material-symbols-outlined { color: var(--primary); }

  .file-preview {
    margin-top: 1rem; padding: 0.75rem 1rem; background: #f8fafc;
    border-radius: 0.5rem; display: none; align-items: center;
    justify-content: space-between; border: 1px solid #e2e8f0;
  }

  .btn-primary {
    background-color: var(--primary) !important;
    border-color: var(--primary) !important;
  }
  .btn-primary:hover {
    background-color: var(--primary-hover) !important;
    border-color: var(--primary-hover) !important;
  }
  .btn-outline-primary {
    color: var(--primary) !important;
    border-color: var(--primary) !important;
  }
  .btn-outline-primary:hover {
    background-color: rgba(0, 63, 177, 0.06) !important;
  }

  .input-group-text {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-right: 0; font-weight: 600; color: #64748b;
  }
  .input-group .form-control { border-left: 0; }
</style>

<div class="container-fluid px-0">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Formulir Perencanaan</h1>
        <p class="mb-0 small">Buat perencanaan kegiatan unit kerja.</p>
      </div>
      <div class="badge-id">
        <span class="material-symbols-outlined" style="font-size: 1rem;">info</span>
        Form Baru
      </div>
    </div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <ul class="mb-0 small">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <form action="{{ route('plannings.store') }}" method="POST" enctype="multipart/form-data" id="planningForm">
    @csrf

    <div class="row g-3">
      {{-- Kolom Kiri --}}
      <div class="col-12 col-xl-8">

        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">assignment_turned_in</span>
            Informasi Perencanaan
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
              <input type="text" name="no_letter"
                     class="form-control @error('no_letter') is-invalid @enderror"
                     value="{{ old('no_letter') }}"
                     placeholder="Contoh: PKA01-S-2026-01" required>
              @error('no_letter') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Tanggal <span class="text-danger">*</span></label>
              <input type="date" name="date"
                     class="form-control @error('date') is-invalid @enderror"
                     value="{{ old('date', date('Y-m-d')) }}" required>
              @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
              <label class="form-label">Nama Perencanaan <span class="text-danger">*</span></label>
              <input type="text" name="name"
                     class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name') }}"
                     placeholder="Contoh: Digitalisasi Arsip Akademik 2026" required>
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Periode <span class="text-danger">*</span></label>
              <select name="period" class="form-select @error('period') is-invalid @enderror" required>
                <option value="">-- Pilih Periode --</option>
                <option value="ganjil" {{ old('period') === 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                <option value="genap"  {{ old('period') === 'genap'  ? 'selected' : '' }}>Semester Genap</option>
              </select>
              @error('period') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Jenis Perencanaan <span class="text-danger">*</span></label>
              <select name="planning_type_id" class="form-select @error('planning_type_id') is-invalid @enderror" required>
                <option value="">-- Pilih Jenis --</option>
                @foreach($planningTypes as $pt)
                  <option value="{{ $pt->id_type }}" @selected(old('planning_type_id') == $pt->id_type)>
                    {{ $pt->name }}
                  </option>
                @endforeach
              </select>
              @error('planning_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>

        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">description</span>
            Detail Perencanaan
          </div>

          <div class="mb-3">
            <label class="form-label">Tujuan</label>
            <textarea name="objective" rows="3"
                      class="form-control @error('objective') is-invalid @enderror"
                      placeholder="Tujuan strategis dari perencanaan ini...">{{ old('objective') }}</textarea>
            @error('objective') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Sasaran</label>
            <input type="text" name="target"
                   class="form-control @error('target') is-invalid @enderror"
                   value="{{ old('target') }}"
                   placeholder="Target subjek/objek...">
            @error('target') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="form-label">Indikator Keberhasilan</label>
            <textarea name="success_indicator" rows="2"
                      class="form-control @error('success_indicator') is-invalid @enderror"
                      placeholder="Apa yang menandakan kegiatan ini berhasil?">{{ old('success_indicator') }}</textarea>
            @error('success_indicator') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>

      {{-- Kolom Kanan --}}
      <div class="col-12 col-xl-4">

        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">payments</span>
            Anggaran
          </div>

          <div class="mb-3">
            <label class="form-label">Estimasi Biaya (Rp)</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="text" name="budget" id="moneyInput"
                     class="form-control @error('budget') is-invalid @enderror"
                     value="{{ old('budget') }}" placeholder="0">
            </div>
            @error('budget') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Sumber Dana</label>
            <select name="funding_source" class="form-select @error('funding_source') is-invalid @enderror">
              <option value="">-- Pilih Sumber Dana --</option>
              <option value="Anggaran Tahunan Institusi" {{ old('funding_source') == 'Anggaran Tahunan Institusi' ? 'selected' : '' }}>Anggaran Tahunan Institusi</option>
              <option value="Hibah Internal" {{ old('funding_source') == 'Hibah Internal' ? 'selected' : '' }}>Hibah Internal</option>
              <option value="Sponsorship Luar" {{ old('funding_source') == 'Sponsorship Luar' ? 'selected' : '' }}>Sponsorship Luar</option>
              <option value="Lainnya" {{ old('funding_source') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('funding_source') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="form-label">Keterangan Anggaran</label>
            <textarea name="budget_note" rows="3"
                      class="form-control @error('budget_note') is-invalid @enderror"
                      placeholder="Catatan terkait anggaran...">{{ old('budget_note') }}</textarea>
            @error('budget_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">cloud_upload</span>
            Dokumen Proposal
          </div>

          <div class="upload-zone" id="uploadZone">
            <span class="material-symbols-outlined d-block mb-2">upload_file</span>
            <p class="fw-medium mb-1 small">Klik untuk unggah atau seret file</p>
            <p class="text-secondary mb-0" style="font-size: 0.75rem;">PDF, DOC, DOCX, XLS, XLSX (Maks. 10MB)</p>
            <input type="file" name="document" id="fileInput" class="d-none"
                   accept=".pdf,.doc,.docx,.xls,.xlsx">
          </div>

          <div id="filePreview" class="file-preview">
            <div class="d-flex align-items-center gap-2">
              <span class="material-symbols-outlined text-danger" id="fileIcon">picture_as_pdf</span>
              <div>
                <p class="small mb-0 fw-medium" id="fileName">-</p>
                <p class="text-secondary mb-0" style="font-size: 0.7rem;" id="fileSize">-</p>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-link text-secondary p-0" id="clearFileBtn">
              <span class="material-symbols-outlined" style="font-size: 1.1rem;">close</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="d-flex justify-content-end gap-2 mt-3 mb-2">
      <a href="{{ route('plannings.index') }}" class="btn btn-light px-4">Batal</a>
      <button type="submit" name="status" value="draft" class="btn btn-outline-primary px-4">
        Simpan Draf
      </button>
      <button type="submit" name="status" value="submitted" class="btn btn-primary px-4">
        <span class="material-symbols-outlined align-middle me-1" style="font-size:1.1rem">send</span>
        Kirim
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('planningForm');
  const moneyInput = document.getElementById('moneyInput');

  if (moneyInput) {
    moneyInput.addEventListener('input', function (e) {
      let value = e.target.value.replace(/\D/g, '');
      e.target.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
    });
  }

  if (form) {
    form.addEventListener('submit', function () {
      if (moneyInput) {
        moneyInput.value = moneyInput.value.replace(/\D/g, '');
      }
    });
  }

  const uploadZone  = document.getElementById('uploadZone');
  const fileInput   = document.getElementById('fileInput');
  const filePreview = document.getElementById('filePreview');
  const fileName    = document.getElementById('fileName');
  const fileSize    = document.getElementById('fileSize');
  const fileIcon    = document.getElementById('fileIcon');
  const clearBtn    = document.getElementById('clearFileBtn');

  if (uploadZone && fileInput) {
    uploadZone.addEventListener('click', () => fileInput.click());
  }

  if (fileInput) {
    fileInput.addEventListener('change', function () {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        const ext  = file.name.split('.').pop().toLowerCase();

        if (['doc', 'docx'].includes(ext)) {
          fileIcon.textContent = 'description';
          fileIcon.className = 'material-symbols-outlined text-primary';
        } else if (['xls', 'xlsx'].includes(ext)) {
          fileIcon.textContent = 'table_chart';
          fileIcon.className = 'material-symbols-outlined text-success';
        } else {
          fileIcon.textContent = 'picture_as_pdf';
          fileIcon.className = 'material-symbols-outlined text-danger';
        }

        fileName.textContent = file.name;
        fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        filePreview.style.display = 'flex';
      }
    });
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      fileInput.value = '';
      filePreview.style.display = 'none';
    });
  }
});
</script>
@endpush