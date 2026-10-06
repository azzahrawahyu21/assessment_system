@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<style>
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
  .page-header-card h4 {
    color: #fff;
    font-weight: 700;
  }
  .page-header-card p {
    color: rgba(255, 255, 255, 0.88) !important;
  }
  .page-header-card .accent {
    position: absolute;
    top: -90px;
    right: -70px;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    filter: blur(10px);
  }

  .profile-card {
    background: #fff;
    border-radius: 1rem;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
  }

  .profile-avatar-wrap {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    border: 3px solid #dbeafe;
    overflow: hidden;
    flex-shrink: 0;
  }

  .profile-avatar-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #334155;
    margin-bottom: 0.4rem;
  }

  .form-control-modern {
    height: 46px;
    border-radius: 0.65rem;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    font-size: 0.95rem;
    color: #0f172a;
    padding: 0.6rem 1rem;
    transition: all 0.15s ease;
  }

  .form-control-modern:focus {
    background: #fff;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
  }

  .form-control-modern:read-only,
  .form-control-modern[readonly] {
    background: #f1f5f9;
    color: #64748b;
    cursor: not-allowed;
  }

  .form-control-modern.is-invalid {
    border-color: #ef4444;
  }

  .form-control-modern.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
  }

  .section-divider {
    border: none;
    border-top: 1px solid #f1f5f9;
    margin: 1.75rem 0 1.25rem;
  }

  .btn-cancel {
    height: 44px;
    padding: 0 1.25rem;
    border-radius: 0.65rem;
    font-weight: 500;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #475569;
  }

  .btn-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
  }

  .btn-save {
    height: 44px;
    padding: 0 1.35rem;
    border-radius: 0.65rem;
    font-weight: 600;
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
    border: none;
    color: #fff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
  }

  .btn-save:hover {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    color: #fff;
  }

  .info-hint {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-top: 0.3rem;
  }
</style>

{{-- Header Card --}}
<div class="page-header-card">
  <div class="accent"></div>
  <div class="position-relative d-flex align-items-center gap-3">
    <div class="profile-avatar-wrap">
      <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ffffff&color=1d4ed8&bold=true&size=128"
           alt="{{ $user->name }}">
    </div>
    <div>
      <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
      <p class="mb-0 small opacity-90">
        {{ $user->role->name ?? '-' }}
        @if($user->department)
          · {{ $user->department->name }}
        @endif
      </p>
    </div>
  </div>
</div>

{{-- Alert --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
       style="border-radius: 0.75rem;">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- Form Card --}}
<div class="profile-card">
  <div class="p-4 p-md-4">

    <form action="{{ route('profile.update') }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row g-3">

        {{-- Nama --}}
        <div class="col-md-6">
          <label class="form-label">Nama Lengkap</label>
          <input type="text"
                 name="name"
                 class="form-control form-control-modern @error('name') is-invalid @enderror"
                 value="{{ old('name', $user->name) }}"
                 placeholder="Masukkan nama lengkap"
                 required>
          @error('name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        {{-- Email --}}
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email"
                 name="email"
                 class="form-control form-control-modern @error('email') is-invalid @enderror"
                 value="{{ old('email', $user->email) }}"
                 placeholder="email@example.com"
                 required>
          @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        {{-- Role --}}
        <div class="col-md-6">
          <label class="form-label">Role</label>
          <input type="text"
                 class="form-control form-control-modern"
                 value="{{ $user->role->name ?? '-' }}"
                 readonly>
          <p class="info-hint mb-0">Role tidak dapat diubah sendiri</p>
        </div>

        {{-- Department --}}
        <div class="col-md-6">
          <label class="form-label">Department / Unit</label>
          <input type="text"
                 class="form-control form-control-modern"
                 value="{{ $user->department->name ?? 'Tidak Ada' }}"
                 readonly>
          <p class="info-hint mb-0">Unit tidak dapat diubah sendiri</p>
        </div>
      </div>

      {{-- Password Section --}}
      <hr class="section-divider">

      <h6 class="fw-semibold mb-1" style="color:#0f172a;">Ubah Password</h6>
      <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengubah password.</p>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Password Baru</label>
          <input type="password"
                 name="password"
                 class="form-control form-control-modern @error('password') is-invalid @enderror"
                 placeholder="Minimal 8 karakter"
                 autocomplete="new-password">
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label">Konfirmasi Password Baru</label>
          <input type="password"
                 name="password_confirmation"
                 class="form-control form-control-modern"
                 placeholder="Ulangi password baru"
                 autocomplete="new-password">
        </div>
      </div>

      {{-- Actions --}}
      <div class="d-flex justify-content-end gap-2 mt-4 pt-2">
        <a href="{{ route('dashboard') }}" class="btn btn-cancel d-inline-flex align-items-center">
          Batal
        </a>
        <button type="submit" class="btn btn-save d-inline-flex align-items-center gap-1">
          <span class="material-symbols-outlined" style="font-size:1.15rem;">save</span>
          Simpan Perubahan
        </button>
      </div>
    </form>

  </div>
</div>
@endsection