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
  .page-header-card::after {
    pointer-events: none;
  }

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
    background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;
    padding: 0.7rem 1rem; font-size: 0.9rem;
  }
  .form-control:focus, .form-select:focus {
    background-color: #fff; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 63, 177, 0.12);
  }
  .form-actions {
    border-radius: 0.75rem; padding: 1.25rem 1.5rem;
    display: flex; justify-content: flex-end; gap: 0.75rem;
  }
  .btn-primary { background-color: var(--primary) !important; border-color: var(--primary) !important; }
  .btn-primary:hover { background-color: var(--primary-hover) !important; border-color: var(--primary-hover) !important; }
</style>

<div class="container-fluid px-4 py-3">
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="position-relative">
      <h1 class="fs-4 fw-bold mb-1">Tambah Pengguna</h1>
      <p class="mb-0 small">Tambahkan akun pengguna baru ke dalam sistem.</p>
    </div>
  </div>

  <form action="{{ route('administrator.users.store') }}" method="POST">
    @csrf
    <div class="form-card">
      <div class="card-title">
        <span class="material-symbols-outlined">person</span>
        Data Pengguna
      </div>
      @include('administrator.user.partials.form', ['user' => null])
    </div>
    <div class="form-actions">
      <a href="{{ route('administrator.users.index') }}" class="btn btn-light px-4">Batal</a>
      <button type="submit" class="btn btn-primary px-4">Simpan Pengguna</button>
    </div>
  </form>
</div>
@endsection