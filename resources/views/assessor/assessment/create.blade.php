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
  .page-header-card .btn-primary {
    background: #fff !important; color: #2563eb !important; border: none !important;
    font-weight: 600; transition: .25s;
  }
  .page-header-card .btn-primary:hover {
    background: #eff6ff !important; color: #1d4ed8 !important; transform: translateY(-2px);
  }
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
    background: #fff; border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 1.75rem;
  }

  .btn-primary { background-color: var(--primary) !important; border-color: var(--primary) !important; }
  .btn-primary:hover { background-color: var(--primary-hover) !important; border-color: var(--primary-hover) !important; }
  .btn-outline-primary { color: var(--primary) !important; border-color: var(--primary) !important; }
  .btn-outline-primary:hover { background-color: rgba(0, 63, 177, 0.06) !important; }

  .form-control, .form-select {
    background-color: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; font-size: 0.875rem;
  }
  .form-control:focus, .form-select:focus {
    background-color: #fff; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 63, 177, 0.12);
  }

  .form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
  }
</style>

<div class="container-fluid px-4 py-3">

  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Tambah Assessment</h1>
        <p class="mb-0 small">Buat assessment dari perencanaan yang sudah dilaksanakan.</p>
      </div>
      <a href="{{ route('assessor.assessments.index') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3">
        <span class="material-symbols-outlined" style="font-size: 1.2rem;">arrow_back</span>
        Kembali
      </a>
    </div>
  </div>

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="form-card">
    <form action="{{ route('assessor.assessments.store') }}" method="POST">
      @csrf

      <div class="mb-4">
        <label for="name" class="form-label fw-medium">Nama Assessment <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name') }}"
               placeholder="Contoh: Assessment COBIT 2019 - 2026" required>
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label fw-medium">Jenis Assessment <span class="text-danger">*</span></label>
        <div class="d-flex flex-column gap-2 mt-2">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="assessment_type" id="type_single"
                   value="single" {{ old('assessment_type', 'single') == 'single' ? 'checked' : '' }}>
            <label class="form-check-label" for="type_single">
              Tingkat Program Studi / Unit Penunjang Akademik (UPA)
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="assessment_type" id="type_all"
                   value="all" {{ old('assessment_type') == 'all' ? 'checked' : '' }}>
            <label class="form-check-label" for="type_all">
              Tingkat Politeknik Negeri Madiun (Semua Department)
            </label>
          </div>
        </div>
      </div>

      <div class="mb-4" id="department-wrapper">
        <label for="department_id" class="form-label fw-medium">Pilih Department <span class="text-danger">*</span></label>
        <select name="department_id" id="department_id"
                class="form-select @error('department_id') is-invalid @enderror">
          <option value="">-- Pilih Department --</option>
          @foreach($departments as $dept)
            @php $cnt = $implementedByDept[$dept->id_department] ?? 0; @endphp
            <option value="{{ $dept->id_department }}"
                    data-count="{{ $cnt }}"
                    {{ old('department_id') == $dept->id_department ? 'selected' : '' }}> {{ $dept->name }} 
                    {{-- ({{ $cnt }} perencanaan terimplementasi) --}}
            </option>
          @endforeach
        </select>
        @error('department_id')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <p class="small text-secondary mt-1 mb-0" id="dept-hint"></p>
      </div>

      <div class="alert alert-info small mb-4" id="scope-info">
        Sistem akan mengambil semua <strong>perencanaan berstatus approved yang sudah memiliki data pelaksanaan</strong>
        sesuai scope di atas.
        <span id="scope-total"></span>
      </div>

      <div class="d-flex gap-2 pt-2 justify-content-end">
        <a href="{{ route('assessor.assessments.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4">
          <span class="material-symbols-outlined" style="font-size: 1.15rem;">save</span>
          Simpan Assessment
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const typeSingle = document.getElementById('type_single');
    const typeAll = document.getElementById('type_all');
    const deptWrapper = document.getElementById('department-wrapper');
    const deptSelect = document.getElementById('department_id');
    const scopeTotal = document.getElementById('scope-total');
    const implementedTotal = {{ (int) $implementedTotal }};

    function toggleDepartment() {
      if (typeAll.checked) {
        deptWrapper.style.display = 'none';
        deptSelect.removeAttribute('required');
        scopeTotal.textContent = ' Total institusi: ' + implementedTotal + ' perencanaan.';
      } else {
        deptWrapper.style.display = 'block';
        deptSelect.setAttribute('required', 'required');
        updateDeptHint();
      }
    }

    function updateDeptHint() {
      const opt = deptSelect.options[deptSelect.selectedIndex];
      const count = opt ? (opt.dataset.count || 0) : 0;
      scopeTotal.textContent = opt && opt.value
        ? (' Department terpilih: ' + count + ' perencanaan.')
        : '';
    }

    typeSingle.addEventListener('change', toggleDepartment);
    typeAll.addEventListener('change', toggleDepartment);
    deptSelect.addEventListener('change', updateDeptHint);
    toggleDepartment();
  });
</script>
@endpush