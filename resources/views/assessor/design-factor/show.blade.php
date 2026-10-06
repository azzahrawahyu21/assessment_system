@extends('layouts.app')

@section('content')
<style>
  :root { --primary: #003fb1; --primary-hover: #00349a; }

  .page-header-card {
    position: relative; overflow: hidden; padding: 1.75rem 2rem;
    border-radius: 1rem; margin-bottom: 1.5rem;
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
  .page-header-card .accent,
  .page-header-card::before { pointer-events: none; }

  .form-card {
    background: #fff; border-radius: 0.75rem; padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 1.25rem;
  }
  .form-card .card-title {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;
    padding-bottom: 0.9rem; border-bottom: 1px solid #eef0f2;
  }

  .df-code {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 0.25rem 0.6rem; border-radius: 0.4rem;
    background: rgba(0,63,177,0.08); color: var(--primary);
    font-size: 0.75rem; font-weight: 700; letter-spacing: 0.03em;
  }

  .option-list { list-style: none; padding: 0; margin: 0; }
  .option-list li { margin-bottom: 0.65rem; }
  .option-list label {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.75rem 1rem; border-radius: 0.55rem;
    border: 1.5px solid #e2e8f0; cursor: pointer;
    transition: all 0.15s; background: #fff;
  }
  .option-list label:hover {
    border-color: var(--primary);
    background: rgba(0,63,177,0.03);
  }
  .option-list input { display: none; }
  .option-list input:checked + label {
    border-color: var(--primary);
    background: rgba(0,63,177,0.04);
  }
  .option-list .radio-dot,
  .option-list .check-box {
    width: 18px; height: 18px; border-radius: 50%;
    border: 2px solid #cbd5e1; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    transition: border-color 0.15s;
  }
  .option-list .check-box { border-radius: 0.3rem; }
  .option-list label:hover .radio-dot,
  .option-list label:hover .check-box { border-color: var(--primary); }
  .option-list input:checked + label .radio-dot,
  .option-list input:checked + label .check-box { border-color: var(--primary); }
  .option-list input:checked + label .radio-dot::after {
    content: ''; width: 8px; height: 8px; border-radius: 50%;
    background: var(--primary);
  }
  .option-list input:checked + label .check-box::after {
    content: ''; width: 10px; height: 6px;
    border-left: 2px solid var(--primary);
    border-bottom: 2px solid var(--primary);
    transform: rotate(-45deg); margin-top: -2px;
  }

  .goal-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; padding: 0.85rem 0;
    border-bottom: 1px solid #f1f5f9;
  }
  .goal-row:last-child { border-bottom: none; }

  .score-select {
    display: flex; gap: 0.35rem; flex-wrap: wrap;
  }
  .score-select input { display: none; }
  .score-select label {
    width: 36px; height: 36px; border-radius: 0.4rem;
    border: 1.5px solid #e2e8f0; display: flex; align-items: center;
    justify-content: center; cursor: pointer; font-weight: 700;
    font-size: 0.85rem; background: #fff; color: #64748b;
    transition: all 0.15s;
  }
  .score-select label:hover {
    border-color: var(--primary);
    color: var(--primary);
  }
  .score-select input:checked + label {
    background: var(--primary); color: #fff; border-color: var(--primary);
  }

  .risk-row {
    display: flex; align-items: center; gap: 1rem;
    padding: 0.85rem 0; border-bottom: 1px solid #f1f5f9;
  }
  .risk-row:last-child { border-bottom: none; }
  .risk-label { min-width: 160px; font-weight: 500; font-size: 0.9rem; }
  .risk-bar-wrap {
    flex: 1; height: 12px; background: #e2e8f0; border-radius: 99px;
    overflow: hidden; max-width: 220px;
  }
  .risk-bar {
    height: 100%; background: var(--primary); border-radius: 99px;
    transition: width 0.2s; width: 0%;
  }
  .risk-value {
    min-width: 28px; font-weight: 700; color: #94a3b8; font-size: 0.95rem;
  }

  .option-desc {
    font-size: 0.8rem; color: #64748b; margin-top: 0.15rem;
  }

  /* ===== Action Bar Modern (tidak mengambang) ===== */
  .form-actions {
    background: #fff;
    border-radius: 0.85rem;
    padding: 1.15rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.75rem;
    margin-top: 0.5rem;
    margin-bottom: 1.5rem;
  }

  .btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.65rem;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
    cursor: pointer;
  }

  .btn-cancel {
    background: #f1f5f9;
    color: #475569;
  }
  .btn-cancel:hover {
    background: #e2e8f0;
    color: #1e293b;
  }

  .btn-save {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);
    color: #fff !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
  }
  .btn-save:hover {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #2563eb 100%);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
    transform: translateY(-2px);
    color: #fff !important;
  }
  .btn-save:active {
    transform: translateY(0);
  }
  .btn-save .material-symbols-outlined {
    font-size: 1.2rem;
  }
</style>

<div class="container-fluid px-4 py-3">

  {{-- Header --}}
  <div class="page-header-card">
    <div class="accent"></div>
    <div class="position-relative">
      <p class="small text-uppercase fw-semibold mb-1" style="letter-spacing:0.04em">
        Assessor
      </p>
      <h1 class="fs-4 fw-bold mb-1">COBIT 2019 Design Factors</h1>
      <p class="small mb-0" style="opacity:0.85">
        {{ $assessment->name ?? 'Tentukan faktor desain organisasi sebelum penilaian kapabilitas proses.' }}
      </p>
    </div>
  </div>

  {{-- Error validasi --}}
  @if ($errors->any())
    <div class="alert alert-danger mb-3">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if (session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  <form action="{{ route('assessor.design-factors.store') }}" method="POST" id="designFactorForm">
    @csrf
    <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">

    @forelse($designFactors as $df)
      <div class="form-card">
        <div class="card-title">
          <span class="df-code">{{ $df->code }}</span>
          {{ $df->name }}
        </div>

        @if($df->description)
          <p class="small text-secondary mb-3">{{ $df->description }}</p>
        @endif

        {{-- ===================== SINGLE CHOICE ===================== --}}
        @if($df->input_type === 'single_choice')
          <ul class="option-list">
            @foreach($df->options as $opt)
              @php
                $isChecked = isset($answers[$opt->id]);
              @endphp
              <li>
                <input type="radio"
                       name="single_choice[{{ $df->id }}]"
                       id="opt_{{ $opt->id }}"
                       value="{{ $opt->id }}"
                       {{ $isChecked ? 'checked' : '' }}
                       {{ $loop->first ? 'required' : '' }}>
                <label for="opt_{{ $opt->id }}">
                  <span class="radio-dot"></span>
                  <div>
                    <span>{{ $opt->name }}</span>
                    @if($opt->description)
                      <div class="option-desc">{{ $opt->description }}</div>
                    @endif
                  </div>
                </label>
              </li>
            @endforeach
          </ul>

        {{-- ===================== MULTIPLE CHOICE ===================== --}}
        @elseif($df->input_type === 'multiple_choice')
          <ul class="option-list">
            @foreach($df->options as $opt)
              @php
                $isChecked = isset($answers[$opt->id]);
              @endphp
              <li>
                <input type="checkbox"
                       name="answers[{{ $opt->id }}]"
                       id="opt_{{ $opt->id }}"
                       value="1"
                       {{ $isChecked ? 'checked' : '' }}>
                <label for="opt_{{ $opt->id }}">
                  <span class="check-box"></span>
                  <div>
                    <span>{{ $opt->name }}</span>
                    @if($opt->description)
                      <div class="option-desc">{{ $opt->description }}</div>
                    @endif
                  </div>
                </label>
              </li>
            @endforeach
          </ul>

        {{-- ===================== RATING (1–5) ===================== --}}
        @elseif($df->input_type === 'rating')
          @foreach($df->options as $opt)
            @php
              $currentValue = isset($answers[$opt->id]) ? (int) $answers[$opt->id]->value : null;
            @endphp

            @if($df->code === 'DF03')
              <div class="risk-row">
                <div class="risk-label">
                  @if($opt->code)
                    <span class="df-code me-1">{{ $opt->code }}</span>
                  @endif
                  {{ $opt->name }}
                </div>
                <div class="score-select risk-score" data-opt="{{ $opt->id }}">
                  @for($s = 1; $s <= 5; $s++)
                    <div>
                      <input type="radio"
                             name="answers[{{ $opt->id }}]"
                             id="opt_{{ $opt->id }}_{{ $s }}"
                             value="{{ $s }}"
                             {{ $currentValue === $s ? 'checked' : '' }}
                             required>
                      <label for="opt_{{ $opt->id }}_{{ $s }}">{{ $s }}</label>
                    </div>
                  @endfor
                </div>
                <div class="risk-bar-wrap d-none d-md-block">
                  <div class="risk-bar" id="bar_{{ $opt->id }}"
                       style="width: {{ $currentValue ? ($currentValue / 5 * 100) : 0 }}%"></div>
                </div>
                <span class="risk-value" id="val_{{ $opt->id }}"
                      style="{{ $currentValue ? 'color:var(--primary)' : '' }}">
                  {{ $currentValue ?? '–' }}
                </span>
              </div>
            @else
              <div class="goal-row">
                <div>
                  @if($opt->code)
                    <span class="df-code me-1">{{ $opt->code }}</span>
                  @endif
                  <span class="fw-medium">{{ $opt->name }}</span>
                </div>
                <div class="score-select">
                  @for($s = 1; $s <= 5; $s++)
                    <div>
                      <input type="radio"
                             name="answers[{{ $opt->id }}]"
                             id="opt_{{ $opt->id }}_{{ $s }}"
                             value="{{ $s }}"
                             {{ $currentValue === $s ? 'checked' : '' }}
                             required>
                      <label for="opt_{{ $opt->id }}_{{ $s }}">{{ $s }}</label>
                    </div>
                  @endfor
                </div>
              </div>
            @endif
          @endforeach

        @else
          <p class="text-muted small mb-0">Tipe input tidak dikenali: {{ $df->input_type }}</p>
        @endif
      </div>
    @empty
      <div class="form-card text-center py-5">
        <p class="text-secondary mb-0">Belum ada Design Factor yang aktif. Jalankan seeder terlebih dahulu.</p>
      </div>
    @endforelse

    {{-- Actions (tidak sticky / tidak mengambang) --}}
    @if($designFactors->isNotEmpty())
      <div class="form-actions">
        <a href="{{ route('assessor.design-factors.index') }}" class="btn-action btn-cancel">
          <span class="material-symbols-outlined" style="font-size:1.15rem;">arrow_back</span>
          Batal
        </a>

        <button type="submit" class="btn-action btn-save">
          <span class="material-symbols-outlined">save</span>
          Simpan &amp; Lanjut
        </button>
      </div>
    @endif
  </form>
</div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.risk-score input').forEach(input => {
    input.addEventListener('change', function () {
      const optId = this.closest('.risk-score').dataset.opt;
      const val = parseInt(this.value, 10);
      const bar = document.getElementById('bar_' + optId);
      const label = document.getElementById('val_' + optId);
      if (bar) bar.style.width = (val / 5 * 100) + '%';
      if (label) {
        label.textContent = val;
        label.style.color = 'var(--primary)';
      }
    });
  });
</script>
@endpush