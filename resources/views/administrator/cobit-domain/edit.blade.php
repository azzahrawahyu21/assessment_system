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
  .form-card .card-title .material-symbols-outlined { color: var(--primary); font-size: 1.35rem; }

  .form-label {
    font-size: 0.72rem; font-weight: 600; letter-spacing: 0.05em;
    text-transform: uppercase; color: #6b7280; margin-bottom: 0.4rem;
  }
  .form-control, .form-select {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; padding: 0.7rem 1rem; font-size: 0.9rem;
  }
  .form-control:focus, .form-select:focus {
    background: #fff; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0,63,177,0.12);
  }

  .level-block {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    margin-bottom: 1rem;
    overflow: hidden;
  }
  .level-header {
    background: #f8fafc;
    padding: 0.9rem 1.15rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    border-bottom: 1px solid #e2e8f0;
  }
  .level-header:hover { background: #f1f5f9; }
  .level-body { padding: 1rem 1.15rem; }
  .level-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 0.45rem;
    background: var(--primary); color: #fff; font-weight: 700; font-size: 0.85rem;
  }

  .statement-item {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 0.5rem; padding: 1rem; margin-bottom: 0.75rem;
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
</style>

@php
  $levels = [
    0 => ['name' => 'Incomplete', 'desc' => 'Tidak ada proses / belum dijalankan'],
    1 => ['name' => 'Performed', 'desc' => 'Proses dijalankan mencapai tujuan'],
    2 => ['name' => 'Managed', 'desc' => 'Direncanakan, dimonitor, dan disesuaikan'],
    3 => ['name' => 'Established', 'desc' => 'Terstandar di seluruh organisasi'],
    4 => ['name' => 'Predictable', 'desc' => 'Terukur dan beroperasi dalam batas terdefinisi'],
    5 => ['name' => 'Optimizing', 'desc' => 'Terus ditingkatkan untuk memenuhi tujuan bisnis'],
  ];
  $grouped = $cobitDomain->statements->groupBy('level');
@endphp

<div class="container-fluid px-4 py-3">

  {{-- Header Gradasi Biru --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative">
      <div>
        <h1 class="fs-4 fw-bold mb-1">Edit Domain COBIT</h1>
        <p class="mb-0 small">{{ $cobitDomain->code }} — {{ $cobitDomain->name }}</p>
      </div>
      <a href="{{ route('administrator.cobit-domains.show', $cobitDomain) }}" class="btn btn-light d-inline-flex align-items-center gap-2">
        <span class="material-symbols-outlined" style="font-size:1.15rem">arrow_back</span>
        Kembali
      </a>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger mb-3">
      <strong>Gagal menyimpan:</strong>
      <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('administrator.cobit-domains.update', $cobitDomain) }}" method="POST" id="domainForm">
    @csrf
    @method('PUT')

    <div class="row g-3">
      {{-- Kiri --}}
      <div class="col-12 col-xl-8">

        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">hub</span>
            Informasi Domain
          </div>

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Kode Domain <span class="text-danger">*</span></label>
              <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                     value="{{ old('code', $cobitDomain->code) }}" required maxlength="10">
              @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-8">
              <label class="form-label">Nama Domain <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name', $cobitDomain->name) }}" required>
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
              <label class="form-label">Deskripsi</label>
              <textarea name="description" rows="2" class="form-control">{{ old('description', $cobitDomain->description) }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Urutan Tampil</label>
              <input type="number" name="sort_order" class="form-control" min="1"
                     value="{{ old('sort_order', $cobitDomain->sort_order) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="is_active" class="form-select">
                <option value="1" {{ old('is_active', $cobitDomain->is_active) == '1' || old('is_active', $cobitDomain->is_active) === true ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('is_active', $cobitDomain->is_active) == '0' || old('is_active', $cobitDomain->is_active) === false ? 'selected' : '' }}>Nonaktif</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">layers</span>
            Level Kapabilitas &amp; Pernyataan
          </div>

          <p class="small text-secondary mb-3">
            Setiap domain memiliki 6 level (0–5). Tambahkan atau ubah pernyataan penilaian mandiri pada level yang relevan.
          </p>

          @foreach($levels as $levelNum => $levelInfo)
            @php $items = $grouped->get($levelNum, collect()); @endphp
            <div class="level-block" data-level="{{ $levelNum }}">
              <div class="level-header" onclick="this.parentElement.querySelector('.level-body').classList.toggle('d-none')">
                <div class="d-flex align-items-center gap-3">
                  <span class="level-badge">{{ $levelNum }}</span>
                  <div>
                    <div class="fw-semibold">Level {{ $levelNum }} — {{ $levelInfo['name'] }}</div>
                    <div class="small text-secondary">{{ $levelInfo['desc'] }}</div>
                  </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge rounded-pill px-3" style="background:rgba(0,63,177,0.1);color:#003fb1;">
                    {{ $items->count() }} pernyataan
                  </span>
                  <span class="material-symbols-outlined text-secondary">expand_more</span>
                </div>
              </div>

              <div class="level-body {{ $items->isEmpty() && $levelNum > 0 ? 'd-none' : '' }}">
                <div class="statements-wrapper" id="statements-level-{{ $levelNum }}">
                @foreach($items as $i => $st)
                  <div class="statement-item">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                      <span class="small fw-semibold text-secondary">Pernyataan Level {{ $levelNum }} #{{ $i + 1 }}</span>
                      <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-statement">
                        <span class="material-symbols-outlined" style="font-size:1.1rem">close</span>
                      </button>
                    </div>

                    {{-- GANTI BAGIAN INI --}}
                    <input type="hidden" name="levels[{{ $levelNum }}][statements][{{ $i }}][id]" value="{{ $st->id_statement }}">
                    <input type="hidden" name="levels[{{ $levelNum }}][statements][{{ $i }}][level]" value="{{ $levelNum }}">
                    <textarea name="levels[{{ $levelNum }}][statements][{{ $i }}][text]"
                              rows="2"
                              class="form-control form-control-sm"
                              placeholder="Tuliskan pernyataan untuk level {{ $levelNum }}..."
                              required>{{ $st->statement }}</textarea>
                  </div>
                @endforeach
                </div>

                <button type="button"
                        class="btn btn-outline-primary btn-sm add-statement-btn"
                        data-level="{{ $levelNum }}">
                  <span class="material-symbols-outlined align-middle" style="font-size:1.05rem">add</span>
                  Tambah Pernyataan Level {{ $levelNum }}
                </button>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Sidebar --}}
      <div class="col-12 col-xl-4">
        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">info</span>
            Petunjuk COBIT 2019
          </div>
          <ul class="small text-secondary mb-0 ps-3">
            <li class="mb-2"><strong>EDM</strong> — Evaluate, Direct and Monitor</li>
            <li class="mb-2"><strong>APO</strong> — Align, Plan and Organise</li>
            <li class="mb-2"><strong>BAI</strong> — Build, Acquire and Implement</li>
            <li class="mb-2"><strong>DSS</strong> — Deliver, Service and Support</li>
            <li class="mb-2"><strong>MEA</strong> — Monitor, Evaluate and Assess</li>
          </ul>
        </div>

        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">tune</span>
            Skala Level 0–5
          </div>
          <div class="small">
            @foreach($levels as $num => $info)
              <div class="d-flex gap-2 mb-2">
                <span class="level-badge" style="width:26px;height:26px;font-size:0.75rem;flex-shrink:0">{{ $num }}</span>
                <div>
                  <strong>{{ $info['name'] }}</strong>
                  <div class="text-secondary">{{ $info['desc'] }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div class="form-card">
          <div class="card-title">
            <span class="material-symbols-outlined">warning</span>
            Catatan Edit
          </div>
          <ul class="small text-secondary mb-0 ps-3">
            <li class="mb-2">Pernyataan yang dihapus dari form akan dihapus dari database.</li>
            <li class="mb-2">Pernyataan baru (tanpa ID) akan ditambahkan.</li>
            <li>Pernyataan lama (dengan ID) akan diperbarui.</li>
          </ul>
        </div>
      </div>
    </div>

    {{-- Actions tanpa card --}}
    <div class="d-flex justify-content-end gap-2 mt-3 mb-2">
      <a href="{{ route('administrator.cobit-domains.show', $cobitDomain) }}" class="btn btn-light px-4">Batal</a>
      <button type="submit" class="btn btn-primary px-4">
        <span class="material-symbols-outlined align-middle me-1" style="font-size:1.15rem">save</span>
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  const counters = {};
  @foreach(range(0, 5) as $lv)
    counters[{{ $lv }}] = {{ $grouped->get($lv, collect())->count() }};
  @endforeach

  document.querySelectorAll('.add-statement-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const level = this.dataset.level;
      const idx = counters[level] || 0;
      const wrapper = document.getElementById('statements-level-' + level);

      const html = `
        <div class="statement-item">
          <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
            <span class="small fw-semibold text-secondary">Pernyataan Level ${level} #${idx + 1}</span>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-statement">
              <span class="material-symbols-outlined" style="font-size:1.1rem">close</span>
            </button>
          </div>
          <input type="hidden" name="levels[${level}][statements][${idx}][level]" value="${level}">
          <textarea name="levels[${level}][statements][${idx}][text]"
                    rows="2"
                    class="form-control form-control-sm"
                    placeholder="Tuliskan pernyataan untuk level ${level}..."
                    required></textarea>
        </div>
      `;
      wrapper.insertAdjacentHTML('beforeend', html);
      counters[level] = idx + 1;
    });
  });

  document.addEventListener('click', function (e) {
    if (e.target.closest('.remove-statement')) {
      e.target.closest('.statement-item').remove();
    }
  });
</script>
@endpush