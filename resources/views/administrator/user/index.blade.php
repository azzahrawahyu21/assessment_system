@extends('layouts.app')

@section('content')
<style>
  :root {
    --primary: #2563eb;
    --primary-hover: #1d4ed8;
    --primary-soft: rgba(37, 99, 235, 0.08);
    --danger: #ef4444;
    --danger-soft: #fef2f2;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-700: #334155;
    --gray-900: #0f172a;
    --radius: 1rem;
    --radius-sm: 0.625rem;
  }

  /* ========== Header ========== */
  .page-header {
    position: relative;
    overflow: hidden;
    padding: 1.75rem 2rem;
    border-radius: var(--radius);
    margin-bottom: 1.75rem;
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 45%, #3b82f6 100%);
    color: #fff;
    box-shadow: 0 16px 40px rgba(37, 99, 235, 0.28);
  }
  .page-header h1 {
    color: #fff;
    font-weight: 700;
    font-size: 1.5rem;
    letter-spacing: -0.02em;
  }
  .page-header p {
    color: rgba(255,255,255,.85) !important;
    font-size: 0.9rem;
  }
  .page-header .btn-add {
    background: #fff !important;
    color: var(--primary) !important;
    border: none !important;
    font-weight: 600;
    padding: 0.55rem 1.25rem;
    border-radius: 0.75rem;
    transition: all .25s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
  }
  .page-header .btn-add:hover {
    background: #eff6ff !important;
    color: var(--primary-hover) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,.12);
  }
  .page-header .decor {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
  }
  .page-header .decor-1 {
    top: -80px; right: -60px;
    width: 240px; height: 240px;
    background: rgba(255,255,255,.1);
    filter: blur(8px);
  }
  .page-header .decor-2 {
    left: -50px; bottom: -70px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.07);
  }

  /* ========== Card ========== */
  .content-card {
    background: #fff;
    border-radius: var(--radius);
    padding: 1.5rem 1.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.03);
    border: 1px solid var(--gray-100);
  }

  /* ========== Filter ========== */
  .filter-box {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    padding: 1.1rem 1.25rem;
    margin-bottom: 1.5rem;
  }
  .filter-box .form-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.35rem;
  }
  .filter-box .input-group {
    border-radius: 0.65rem;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
  }
  .filter-box .input-group-text {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-right: none;
    color: var(--gray-400);
  }
  .filter-box .form-control {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-left: none;
    padding: 0.65rem 1rem;
    font-size: 0.9rem;
  }
  .filter-box .form-control:focus {
    border-color: var(--primary);
    box-shadow: none;
  }
  .filter-box .form-control:focus + .input-group-text,
  .filter-box .input-group:focus-within .input-group-text {
    border-color: var(--primary);
  }
  .btn-search {
    background: var(--primary) !important;
    border: none !important;
    padding: 0.65rem 1.4rem;
    border-radius: 0.65rem;
    font-weight: 600;
    font-size: 0.9rem;
  }
  .btn-search:hover {
    background: var(--primary-hover) !important;
  }
  .btn-reset {
    border: 1px solid var(--gray-200);
    color: var(--gray-500);
    background: #fff;
    border-radius: 0.65rem;
    padding: 0.65rem 1.1rem;
    font-weight: 500;
  }
  .btn-reset:hover {
    background: var(--gray-50);
    color: var(--gray-700);
  }

  /* ========== Table ========== */
  .table-modern {
    margin-bottom: 0;
  }
  .table-modern thead th {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-500);
    font-weight: 600;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 0.85rem 1rem;
    white-space: nowrap;
  }
  .table-modern tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
    color: var(--gray-700);
    font-size: 0.925rem;
  }
  .table-modern tbody tr {
    transition: background .15s ease;
  }
  .table-modern tbody tr:hover {
    background: var(--primary-soft);
  }
  .table-modern tbody tr:last-child td {
    border-bottom: none;
  }

  /* Badge Role */
  .badge-role {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 100px;
    padding: 0.35rem 0.85rem;
    font-size: 0.78rem;
    font-weight: 600;
    border-radius: 999px;
    background: var(--primary-soft);
    color: var(--primary);
    letter-spacing: 0.01em;
  }

  /* Action Buttons */
  .btn-action {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.6rem;
    border: 1px solid transparent;
    transition: all .2s ease;
  }
  .btn-action .material-symbols-outlined {
    font-size: 1.2rem;
  }
  .btn-edit {
    color: var(--primary);
    background: var(--primary-soft);
  }
  .btn-edit:hover {
    background: var(--primary);
    color: #fff;
    transform: translateY(-1px);
  }
  .btn-delete {
    color: var(--danger);
    background: var(--danger-soft);
  }
  .btn-delete:hover {
    background: var(--danger);
    color: #fff;
    transform: translateY(-1px);
  }

  /* Empty State */
  .empty-state {
    padding: 3.5rem 1rem;
    text-align: center;
    color: var(--gray-400);
  }
  .empty-state .material-symbols-outlined {
    font-size: 3rem;
    opacity: 0.35;
    margin-bottom: 0.75rem;
  }

  /* ========== Pagination Modern ========== */
  .pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--gray-100);
  }
  .pagination-info {
    font-size: 0.85rem;
    color: var(--gray-500);
  }
  .pagination {
    margin: 0;
    gap: 0.35rem;
  }
  .pagination .page-item .page-link {
    border: none;
    background: var(--gray-50);
    color: var(--gray-700);
    border-radius: 0.55rem !important;
    min-width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all .2s ease;
  }
  .pagination .page-item .page-link:hover {
    background: var(--primary-soft);
    color: var(--primary);
  }
  .pagination .page-item.active .page-link {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
  }
  .pagination .page-item.disabled .page-link {
    background: transparent;
    color: var(--gray-300);
  }

  /* ========== Modal Delete ========== */
  .modal-delete .modal-content {
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
  }
  .modal-delete .modal-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: var(--danger-soft);
    color: var(--danger);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
  }
  .modal-delete .modal-icon .material-symbols-outlined {
    font-size: 2.1rem;
  }
  .modal-delete h5 {
    font-weight: 700;
    color: var(--gray-900);
  }
  .modal-delete .btn-cancel {
    background: var(--gray-100);
    border: none;
    color: var(--gray-700);
    font-weight: 500;
    border-radius: 0.65rem;
    padding: 0.6rem 1.4rem;
  }
  .modal-delete .btn-cancel:hover {
    background: var(--gray-200);
  }
  .modal-delete .btn-confirm {
    background: var(--danger);
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 0.65rem;
    padding: 0.6rem 1.4rem;
  }
  .modal-delete .btn-confirm:hover {
    background: #dc2626;
  }

  /* Alert */
  .alert-success {
    border: none;
    border-radius: var(--radius-sm);
    background: #ecfdf5;
    color: #065f46;
  }
</style>

<div class="container-fluid px-4 py-3">

  {{-- Header --}}
  <div class="page-header">
    <div class="decor decor-1"></div>
    <div class="decor decor-2"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="mb-1">Manajemen Pengguna</h1>
        <p class="mb-0">Kelola akun pengguna sistem dengan mudah.</p>
      </div>
      <a href="{{ route('administrator.users.create') }}" class="btn btn-add d-inline-flex align-items-center gap-2">
        <span class="material-symbols-outlined" style="font-size: 1.25rem;">person_add</span>
        Tambah Pengguna
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
      <span class="material-symbols-outlined">check_circle</span>
      <div>{{ session('success') }}</div>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="content-card">

    {{-- Filter --}}
    <div class="filter-box">
      <form action="{{ route('administrator.users.index') }}" method="GET">
        <div class="row g-3 align-items-end">
          <div class="col-md-6 col-lg-5">
            <label class="form-label">Pencarian</label>
            <div class="input-group">
              <span class="input-group-text">
                <span class="material-symbols-outlined" style="font-size:1.2rem">search</span>
              </span>
              <input type="text"
                     name="search"
                     value="{{ request('search') }}"
                     class="form-control"
                     placeholder="Cari nama, email, atau NIP...">
            </div>
          </div>
          <div class="col-md-auto d-flex gap-2">
            <button type="submit" class="btn btn-search text-white">
              Cari
            </button>
            @if(request()->filled('search'))
              <a href="{{ route('administrator.users.index') }}" class="btn btn-reset">
                Reset
              </a>
            @endif
          </div>
        </div>
      </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table table-modern align-middle">
        <thead>
          <tr>
            <th width="70" class="text-center">No</th>
            <th>Nama</th>
            <th>Email</th>
            <th class="text-center">Peran</th>
            <th class="text-center">Unit</th>
            <th width="120" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td class="text-center text-muted fw-medium">
                {{ $users->firstItem() + $loop->index }}
              </td>
              <td class="fw-semibold text-dark">{{ $user->name }}</td>
              <td class="text-secondary">{{ $user->email }}</td>
              <td class="text-center">
                <span class="badge-role">{{ $user->role->name ?? '-' }}</span>
              </td>
              <td class="text-center">{{ $user->department->name ?? '-' }}</td>
              <td class="text-center">
                <div class="d-inline-flex gap-2">
                  <a href="{{ route('administrator.users.edit', $user) }}"
                     class="btn btn-action btn-edit"
                     title="Edit">
                    <span class="material-symbols-outlined">edit</span>
                  </a>

                  <button type="button"
                          class="btn btn-action btn-delete"
                          title="Hapus"
                          data-bs-toggle="modal"
                          data-bs-target="#deleteModal"
                          data-id="{{ $user->id }}"
                          data-name="{{ $user->name }}"
                          data-url="{{ route('administrator.users.destroy', $user) }}">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6">
                <div class="empty-state">
                  <span class="material-symbols-outlined d-block">group</span>
                  <div class="fw-medium">Tidak ada pengguna ditemukan</div>
                  <small>Coba ubah kata kunci pencarian atau tambah pengguna baru.</small>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Menampilkan <strong>{{ $users->firstItem() }}</strong>–<strong>{{ $users->lastItem() }}</strong>
          dari <strong>{{ $users->total() }}</strong> data
        </div>
        <div>
          {{ $users->withQueryString()->links() }}
        </div>
      </div>
    @endif
  </div>
</div>

{{-- ==================== MODAL KONFIRMASI HAPUS ==================== --}}
<div class="modal fade modal-delete" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-4 p-md-5">
        <div class="modal-icon">
          <span class="material-symbols-outlined">warning</span>
        </div>
        <h5 class="mb-2">Hapus Pengguna?</h5>
        <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus pengguna</p>
        <p class="fw-semibold text-dark mb-4" id="deleteUserName">—</p>

        <form id="deleteForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-confirm d-inline-flex align-items-center gap-1">
              <span class="material-symbols-outlined" style="font-size:18px;">delete</span>
              Ya, Hapus
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const deleteModal = document.getElementById('deleteModal');

  // Pindahkan modal ke body supaya selalu muncul di tengah layar
  if (deleteModal) {
    document.body.appendChild(deleteModal);
  }

  document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function () {
      const name = this.dataset.name;
      const url  = this.dataset.url;

      document.getElementById('deleteUserName').textContent = `"${name}"`;
      document.getElementById('deleteForm').action = url;
    });
  });
});
</script>
@endpush