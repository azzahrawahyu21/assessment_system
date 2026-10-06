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
  .page-header-card .accent, .page-header-card::before, .page-header-card::after {
    pointer-events: none;
  }

  .form-card {
    background: #fff; border-radius: 0.75rem; padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.25rem;
  }

  .table th {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #6b7280;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
  }
  .table td { vertical-align: middle; }

  .badge-type {
    display: inline-block;
    min-width: 110px;
    text-align: center;
    padding: 0.4rem 0.85rem;
    font-size: 0.78rem;
    font-weight: 600;
    border-radius: 999px;
  }

  .badge-prodi {
    background: rgba(0, 63, 177, 0.1);
    color: #003fb1;
  }
  .badge-kajur {
    background: rgba(139, 92, 246, 0.12);
    color: #7c3aed;
  }
  .badge-wadir {
    background: rgba(245, 158, 11, 0.12);
    color: #d97706;
  }
  .badge-upa {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
  }

  .btn-action {
    width: 34px;
    height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
  }
  .btn-action .material-symbols-outlined {
    font-size: 1.15rem;
  }

  .btn-primary { background-color: var(--primary) !important; border-color: var(--primary) !important; }
  .btn-primary:hover { background-color: var(--primary-hover) !important; border-color: var(--primary-hover) !important; }

  /* Modal Delete */
  .modal-delete .modal-content {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
  }
  .modal-delete .modal-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #fef2f2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
  }
  .modal-delete .modal-icon .material-symbols-outlined {
    font-size: 2rem;
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
</style>

<div class="container-fluid px-4 py-3">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Manajemen Unit</h1>
        <p class="mb-0 small">Kelola Program Studi, Kepala Jurusan, Wakil Direktur, dan UPA.</p>
      </div>
      <a href="{{ route('administrator.departments.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3">
        <span class="material-symbols-outlined" style="font-size: 1.2rem;">add</span>
        Tambah Unit
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="form-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th width="60" class="text-center">No</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Tipe</th>
            <th class="text-center">Dibuat</th>
            <th width="130" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($departments as $dept)
            <tr>
              <td class="text-center text-muted">{{ $departments->firstItem() + $loop->index }}</td>
              <td class="fw-medium">{{ $dept->name }}</td>
              <td class="text-center">
                @if($dept->type === 'prodi')
                  <span class="badge badge-type badge-prodi">Program Studi</span>
                @elseif($dept->type === 'kajur')
                  <span class="badge badge-type badge-kajur">Kepala Jurusan</span>
                @elseif($dept->type === 'wadir')
                  <span class="badge badge-type badge-wadir">Wakil Direktur</span>
                @elseif($dept->type === 'upa')
                  <span class="badge badge-type badge-upa">UPA</span>
                @else
                  <span class="badge badge-type" style="background: #f1f5f9; color: #64748b;">
                    {{ strtoupper($dept->type) }}
                  </span>
                @endif
              </td>
              <td class="text-secondary small text-center">{{ $dept->created_at?->format('d M Y') }}</td>
              <td class="text-center">
                <div class="d-inline-flex gap-1">
                  <a href="{{ route('administrator.departments.edit', $dept) }}"
                     class="btn btn-sm btn-outline-primary btn-action"
                     title="Edit">
                    <span class="material-symbols-outlined">edit</span>
                  </a>

                  <button type="button"
                          class="btn btn-sm btn-outline-danger btn-action btn-delete"
                          title="Hapus"
                          data-bs-toggle="modal"
                          data-bs-target="#deleteModal"
                          data-name="{{ $dept->name }}"
                          data-url="{{ route('administrator.departments.destroy', $dept) }}">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-secondary py-5">
                <span class="material-symbols-outlined d-block mb-2" style="font-size:2.5rem;opacity:.4">apartment</span>
                Belum ada data unit
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($departments->hasPages())
      <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <small class="text-muted">
          Menampilkan {{ $departments->firstItem() }}–{{ $departments->lastItem() }} dari {{ $departments->total() }} data
        </small>
        {{ $departments->links() }}
      </div>
    @endif
  </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade modal-delete" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-4 p-md-5">
        <div class="modal-icon">
          <span class="material-symbols-outlined">warning</span>
        </div>
        <h5 class="fw-bold mb-2">Hapus Unit?</h5>
        <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus unit</p>
        <p class="fw-semibold text-dark mb-4" id="deleteDeptName">—</p>

        <form id="deleteForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger px-4">
              <span class="material-symbols-outlined me-1" style="font-size:18px;vertical-align:middle;">delete</span>
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

  // Pindahkan modal ke body supaya muncul di tengah layar
  if (deleteModal) {
    document.body.appendChild(deleteModal);
  }

  document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function () {
      const name = this.dataset.name;
      const url  = this.dataset.url;

      document.getElementById('deleteDeptName').textContent = `"${name}"`;
      document.getElementById('deleteForm').action = url;
    });
  });
});
</script>
@endpush