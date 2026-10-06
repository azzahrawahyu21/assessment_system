@extends('layouts.app')

@section('content')
<style>
  :root { --primary: #003fb1; }

  .cap-hero {
    position: relative; overflow: hidden;
    border-radius: 1.25rem; padding: 1.6rem 1.85rem; margin-bottom: 1.5rem;
    color: #fff;
    background: linear-gradient(135deg, #0b3cc1 0%, #2563eb 45%, #60a5fa 100%);
    box-shadow: 0 18px 40px rgba(37, 99, 235, 0.22);
  }
  .cap-hero::after {
    content: ""; position: absolute; right: -40px; top: -40px;
    width: 180px; height: 180px; border-radius: 50%;
    background: rgba(255,255,255,.12);
  }
  .cap-hero h1 { color: #fff; font-weight: 700; }
  .cap-hero p { color: rgba(255,255,255,.88); margin: 0; }

  .level-block {
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 1rem;
    margin-bottom: 1.15rem;
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
    overflow: hidden;
  }
  .level-header {
    display: flex; align-items: center; justify-content: space-between;
    gap: .75rem;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    padding: .85rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 800;
    font-size: .82rem;
    color: #334155;
    letter-spacing: .02em;
  }
  .level-count {
    font-size: .72rem; font-weight: 700;
    color: #64748b; background: #fff;
    border: 1px solid #e2e8f0;
    padding: .18rem .55rem; border-radius: 999px;
  }

  .stmt-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 1rem;
    padding: 1.05rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background .12s ease;
  }
  .stmt-row:last-child { border-bottom: none; }
  .stmt-row:hover { background: #fafbfc; }

  .stmt-text {
    color: #0f172a;
    font-size: .92rem;
    line-height: 1.55;
  }
  .obj-code {
    display: inline-flex; align-items: center;
    padding: .14rem .42rem; border-radius: .35rem;
    background: rgba(0,63,177,.08); color: var(--primary);
    font-size: .68rem; font-weight: 800;
    margin-right: .35rem; vertical-align: middle;
  }

  .answer-group {
    flex-shrink: 0;
    display: inline-flex;
    border: 1px solid #e2e8f0;
    border-radius: .65rem;
    overflow: hidden;
    background: #fff;
  }
  .answer-group .btn {
    border: none !important;
    border-radius: 0 !important;
    min-width: 64px;
    font-weight: 700;
    font-size: .8rem;
    padding: .4rem .75rem;
  }
  .answer-group .btn-check:checked + .btn-outline-success {
    background: #059669 !important;
    color: #fff !important;
  }
  .answer-group .btn-check:checked + .btn-outline-danger {
    background: #dc2626 !important;
    color: #fff !important;
  }
  .btn-outline-success { color: #059669; }
  .btn-outline-danger { color: #dc2626; }

  .action-bar {
    position: sticky; bottom: 0;
    /* background: rgba(248, 250, 252, 0.92); */
    backdrop-filter: blur(8px);
    border-top: 1px solid #e2e8f0;
    padding: .9rem 0;
    margin-top: 1rem;
  }
  .btn-primary {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
  }
</style>

<div class="container-fluid px-4 py-3">
  <div class="cap-hero">
    <div class="position-relative d-flex flex-column flex-md-row justify-content-between gap-3">
      <div>
        <p class="small text-uppercase fw-semibold mb-1" style="letter-spacing:.06em;opacity:.85">
          Isi Capability
        </p>
        <h1 class="fs-4 fw-bold mb-1">
          {{ $cobit->code }} · {{ $cobit->description ?? $objective->domain ?? '-' }}
        </h1>
        <p class="small">{{ $assessment->name }} · Jawab Ya / Tidak per pernyataan</p>
      </div>
      <a href="{{ route('capability.fill.show', $assessment) }}"
         class="btn btn-light btn-sm align-self-start">← Domain</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
  @endif

  <form action="{{ route('capability.fill.store', [$assessment, $cobit->id_cobit]) }}" method="POST">
    @csrf

    @forelse($statements as $level => $stmts)
      <div class="level-block">
        <div class="level-header">
          <span>Level {{ $level }}</span>
          <span class="level-count">{{ $stmts->count() }} pernyataan</span>
        </div>

        @foreach($stmts as $stmt)
          @php
            $existing = $answers->get($stmt->id_statement);
            $val = old(
              "answers.{$stmt->id_statement}",
              $existing ? ($existing->answer ? '1' : '0') : null
            );

            $stmtCode = $stmt->code ?? $stmt->kode ?? null;
            $stmtText = $stmt->text
              ?? $stmt->statement
              ?? $stmt->description
              ?? $stmt->name
              ?? $stmt->pernyataan
              ?? $stmt->content
              ?? null;

            $disabled = ($respondent->status ?? '') === 'completed';
          @endphp

          <div class="stmt-row">
            <div class="stmt-text flex-grow-1">
              @if($stmtCode)
                <span class="obj-code">{{ $stmtCode }}</span>
              @endif
              @if($stmtText)
                {{ $stmtText }}
              @else
                <span class="text-danger small">
                  (Teks pernyataan kosong — cek nama kolom di cobit_statements)
                </span>
              @endif
            </div>

            <div class="answer-group" role="group">
              <input type="radio" class="btn-check"
                     name="answers[{{ $stmt->id_statement }}]"
                     id="yes-{{ $stmt->id_statement }}" value="1"
                     {{ $val === '1' || $val === 1 ? 'checked' : '' }}
                     {{ $disabled ? 'disabled' : '' }}>
              <label class="btn btn-outline-success" for="yes-{{ $stmt->id_statement }}">Ya</label>

              <input type="radio" class="btn-check"
                     name="answers[{{ $stmt->id_statement }}]"
                     id="no-{{ $stmt->id_statement }}" value="0"
                     {{ $val === '0' || $val === 0 ? 'checked' : '' }}
                     {{ $disabled ? 'disabled' : '' }}>
              <label class="btn btn-outline-danger" for="no-{{ $stmt->id_statement }}">Tidak</label>
            </div>
          </div>
        @endforeach
      </div>
    @empty
      <div class="alert alert-warning border-0 shadow-sm">
        Belum ada pernyataan di <code>cobit_statements</code> untuk domain ini.
      </div>
    @endforelse

    @if(($respondent->status ?? '') !== 'completed')
      <div class="action-bar">
        <div class="d-flex justify-content-end gap-2">
          <button type="submit" name="submit" value="0" class="btn btn-primary px-4">
            Simpan Draft
          </button>
          <button type="submit" name="submit" value="1" class="btn btn-outline-secondary px-4">
            Simpan Domain
          </button>
        </div>
      </div>
    @endif
  </form>
</div>
@endsection