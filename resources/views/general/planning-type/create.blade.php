@extends('layouts.app')

@section('title', 'Tambah Jenis Perencanaan')
@section('page-title', 'Tambah Jenis Perencanaan')

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

  .form-label {
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.05em;
    text-transform: uppercase; color: #6b7280; margin-bottom: 0.4rem;
  }
  .form-control, .form-select {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;
    padding: 0.7rem 1rem; font-size: 0.9rem;
  }
  .form-control:focus, .form-select:focus {
    background: #fff; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0,63,177,0.12);
  }

  .form-actions {
    background: #fff; padding: 2.5rem 1.5rem 1.25rem;
    display: flex; justify-content: flex-end; gap: 0.75rem;
  }
  .btn-primary { background-color: var(--primary) !important; border-color: var(--primary) !important; }
  .btn-primary:hover { background-color: var(--primary-hover) !important; border-color: var(--primary-hover) !important; }
</style>

<div class="container-fluid px-0">

  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Tambah Jenis Perencanaan</h1>
        <p class="mb-0 small">Isi nama dan deskripsi jenis perencanaan.</p>
      </div>
      <a href="{{ route('planning-types.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2">
        <span class="material-symbols-outlined" style="font-size:1.15rem">arrow_back</span>
        Kembali
      </a>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger mb-3">
      <ul class="mb-0 small">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('planning-types.store') }}" method="POST">
    @csrf

    <div class="form-card">
      <div class="card-title">
        <span class="material-symbols-outlined">category</span>
        Informasi Jenis
      </div>

      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Nama Jenis <span class="text-danger">*</span></label>
          <input type="text" name="name"
                 class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name') }}"
                 placeholder="Contoh: Kurikulum, Infrastruktur, SDM" required>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" rows="4"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="Ringkasan jenis perencanaan...">{{ old('description') }}</textarea>
          @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="form-actions">
        <a href="{{ route('planning-types.index') }}" class="btn btn-light px-4">Batal</a>
        <button type="submit" class="btn btn-primary px-4">
          <span class="material-symbols-outlined align-middle me-1" style="font-size:1.15rem">save</span>
          Simpan
        </button>
      </div>

    </div>
  </form>
</div>
@endsection