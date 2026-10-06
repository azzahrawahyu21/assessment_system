@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #003fb1;
    }

    .judge-hero {
        background: linear-gradient(135deg, #0b3cc1 0%, #2563eb 45%, #60a5fa 100%);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        color: #fff;
        margin-bottom: 1.75rem;
        box-shadow: 0 18px 40px rgba(37, 99, 235, 0.22);
    }

    .judge-hero h1 {
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .domain-accordion .accordion-item {
        border: none;
        border-radius: 1rem !important;
        margin-bottom: 1rem;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .domain-accordion .accordion-button {
        font-weight: 700;
        background: #fff;
        padding: 1.15rem 1.4rem;
        box-shadow: none !important;
    }

    .domain-accordion .accordion-button:not(.collapsed) {
        background: #f0f7ff;
        color: var(--primary);
    }

    .domain-accordion .accordion-body {
        background: #fafbfc;
        padding: 1.5rem 1.6rem 1.75rem;
    }

    .obj-code {
        display: inline-flex;
        padding: .18rem .5rem;
        border-radius: .4rem;
        background: rgba(0, 63, 177, 0.1);
        color: var(--primary);
        font-size: .73rem;
        font-weight: 800;
        margin-right: .45rem;
    }

    .section-title {
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        margin-bottom: .65rem;
    }

    .recommended-box {
        border-radius: .85rem;
        padding: 1rem 1.1rem;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .recommended-level {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--primary);
    }

    .activity-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: .85rem;
        overflow: hidden;
    }

    .activity-row {
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        padding: .75rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .activity-row:last-child {
        border-bottom: none;
    }

    .activity-number {
        min-width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #eff6ff;
        color: var(--primary);
        font-size: .8rem;
        font-weight: 700;
    }

    .activity-row textarea {
        min-height: 70px;
        resize: vertical;
        border-radius: .65rem;
        border: 1px solid #e2e8f0;
    }

    .activity-row textarea:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
    }

    .remove-activity {
        flex-shrink: 0;
    }

    .evidence-box {
        border: 1.5px dashed #cbd5e1;
        border-radius: .85rem;
        padding: 1rem 1.15rem;
        background: #fff;
        transition: border-color .15s;
    }

    .evidence-box:hover {
        border-color: #93c5fd;
    }

    .existing-file {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .55rem .7rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: .6rem;
        margin-bottom: .4rem;
    }

    .notes-area textarea {
        border-radius: .75rem;
        border: 1.5px solid #e2e8f0;
        resize: vertical;
    }

    .notes-area textarea:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
    }

    .btn-primary {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
    }

    .level-group {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
    }

    .level-group label {
        display: flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .85rem;
        border: 1.5px solid #e2e8f0;
        border-radius: .65rem;
        cursor: pointer;
        font-size: .88rem;
        font-weight: 600;
        background: #fff;
        transition: all .15s ease;
    }

    .level-group label:hover {
        border-color: #93c5fd;
        background: #f0f7ff;
    }

    .level-group label:has(input:checked) {
        border-color: var(--primary);
        background: #eff6ff;
        color: var(--primary);
    }
</style>

<div class="container-fluid px-4 py-3">
    <div class="judge-hero">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
            <div>
                <p class="small text-uppercase fw-semibold mb-1" style="opacity:.85; letter-spacing:.05em;">
                    Assessor · Penilaian
                </p>
                <h1 class="fs-4 fw-bold mb-1">
                    {{ $assessment->name }}
                </h1>
                <p class="small mb-0" style="opacity:.9">
                    Validasi capability berdasarkan aktivitas dan evidence
                </p>
            </div>
            <a href="{{ route('assessor.capability.index') }}" class="btn btn-outline-light btn-sm align-self-start">
                ← Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('assessor.capability.judge.store', $assessment) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="accordion domain-accordion" id="domainAccordion">
            @foreach($objectives as $idx => $obj)
                @php
                    $cobit = $obj->cobit;
                    $existing = $judgments->get($obj->cobit_id);
                    $recommendedLevel = $obj->recommended_level;
                    $oldLevel = old("judgments.$idx.achieved_level", $existing->achieved_level ?? $recommendedLevel);
                    $activities = old("judgments.$idx.activities", $obj->assessment_activities ?? []);
                    if (!is_array($activities)) {
                        $activities = [];
                    }
                    $existingEvidence = $existing->evidence_paths ?? [];
                    if (empty($existingEvidence) && $existing && $existing->evidence_path) {
                        $existingEvidence = [$existing->evidence_path];
                    }
                    $oldNotes = old("judgments.$idx.notes", $existing->notes ?? '');
                @endphp

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#domain-{{ $obj->id }}">
                            <span class="obj-code">{{ $obj->code }}</span>
                            {{ $cobit->description ?? $obj->domain ?? '-' }}
                            <span class="ms-2 badge rounded-pill bg-light text-secondary text-uppercase" style="font-size:.68rem">
                                {{ $obj->priority }}
                            </span>
                        </button>
                    </h2>

                    <div id="domain-{{ $obj->id }}"
                         class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}"
                         data-bs-parent="#domainAccordion">
                        <div class="accordion-body">
                            <input type="hidden" name="judgments[{{ $idx }}][cobit_id]" value="{{ $obj->cobit_id }}">

                            {{-- Recommended Capability --}}
                            <div class="mb-4">
                                <div class="section-title">Recommended Capability</div>
                                <div class="recommended-box">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <div class="recommended-level">Level {{ $recommendedLevel }}</div>
                                            <div class="small text-muted mt-1">
                                                Hasil perhitungan berdasarkan jawaban seluruh responden.
                                            </div>
                                        </div>
                                        {{-- @if($obj->first_unfulfilled_level !== null)
                                            <span class="badge bg-warning text-dark">
                                                Level {{ $obj->first_unfulfilled_level }} belum terpenuhi
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                Semua level terpenuhi
                                            </span>
                                        @endif --}}
                                    </div>
                                    <input type="hidden" name="judgments[{{ $idx }}][recommended_level]" value="{{ $recommendedLevel }}">
                                </div>
                            </div>

                            {{-- Aktivitas yang Diverifikasi --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="section-title mb-0">Aktivitas yang Diverifikasi</div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addActivity({{ $idx }})">
                                        + Tambah Aktivitas
                                    </button>
                                </div>

                                <div id="activity-container-{{ $idx }}" class="activity-card">
                                    @forelse($activities as $activityIndex => $activity)
                                        <div class="activity-row">
                                            <div class="activity-number">{{ $activityIndex + 1 }}</div>
                                            <div class="flex-grow-1">
                                                <textarea name="judgments[{{ $idx }}][activities][{{ $activityIndex }}]"
                                                          class="form-control"
                                                          rows="2"
                                                          placeholder="Masukkan aktivitas yang diverifikasi...">{{ $activity }}</textarea>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger remove-activity"
                                                    onclick="removeActivity(this)">
                                                ×
                                            </button>
                                        </div>
                                    @empty
                                        <div class="p-3 text-muted small">
                                            Belum ada aktivitas. Klik <strong>+ Tambah Aktivitas</strong> untuk menambahkan.
                                        </div>
                                    @endforelse
                                </div>

                                @if($obj->first_unfulfilled_level !== null)
                                    <div class="form-text mt-2">
                                        Aktivitas awal diambil dari pernyataan pada
                                        <strong>Level {{ $obj->first_unfulfilled_level }}</strong>
                                        yang belum terpenuhi. Assessor dapat mengubah, menghapus, atau menambahkan aktivitas sesuai hasil verifikasi.
                                    </div>
                                @endif
                            </div>

                            {{-- Evidence --}}
                            <div class="mb-4">
                                <div class="section-title">Evidence</div>
                                <div class="evidence-box">
                                    <input type="file"
                                           class="form-control form-control-sm"
                                           name="judgments[{{ $idx }}][evidence][]"
                                           multiple
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                                    <div class="form-text mt-2">
                                        Unggah file maksimal 10 MB.
                                    </div>

                                    @if(count($existingEvidence) > 0)
                                        <div class="mt-3">
                                            <div class="small fw-semibold mb-2">Evidence tersimpan:</div>
                                            @foreach($existingEvidence as $evidence)
                                                <div class="existing-file">
                                                    <span class="material-symbols-outlined text-primary">description</span>
                                                    <a href="{{ asset('storage/' . $evidence) }}" target="_blank" class="fw-semibold">
                                                        Lihat Evidence {{ $loop->iteration }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Hasil Assessment Final --}}
                            <div class="mb-4">
                                <div class="section-title">Hasil Assessment Final</div>
                                <div class="level-group">
                                    @for($lv = 0; $lv <= 5; $lv++)
                                        <label>
                                            <input type="radio"
                                                   name="judgments[{{ $idx }}][achieved_level]"
                                                   value="{{ $lv }}"
                                                   {{ (string) $oldLevel === (string) $lv ? 'checked' : '' }}
                                                   required>
                                            <span>Level {{ $lv }}</span>
                                        </label>
                                    @endfor
                                </div>
                                <div class="form-text mt-2">
                                    Recommended Capability adalah hasil sistem berdasarkan jawaban responden.
                                    Hasil Assessment Final merupakan hasil validasi assessor berdasarkan aktivitas dan evidence.
                                </div>
                            </div>

                            {{-- Catatan Assessor --}}
                            <div class="notes-area">
                                <div class="section-title">Catatan Assessor</div>
                                <textarea name="judgments[{{ $idx }}][notes]"
                                          rows="4"
                                          class="form-control"
                                          placeholder="Jelaskan hasil validasi, kondisi evidence, alasan penetapan level, atau temuan assessor...">{{ $oldNotes }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end gap-2 mt-4 mb-3">
            <a href="{{ route('assessor.capability.index') }}" class="btn btn-light px-4">
                Batal
            </a>
            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1">
                <span class="material-symbols-outlined" style="font-size:1.15rem">save</span>
                Simpan Assessment
            </button>
        </div>
    </form>
</div>

<script>
    function addActivity(index) {
        const container = document.getElementById('activity-container-' + index);
        const currentRows = container.querySelectorAll('.activity-row');
        const activityIndex = currentRows.length;

        const row = document.createElement('div');
        row.className = 'activity-row';
        row.innerHTML = `
            <div class="activity-number">${activityIndex + 1}</div>
            <div class="flex-grow-1">
                <textarea name="judgments[${index}][activities][${activityIndex}]"
                          class="form-control"
                          rows="2"
                          placeholder="Masukkan aktivitas yang diverifikasi..."></textarea>
            </div>
            <button type="button"
                    class="btn btn-sm btn-outline-danger remove-activity"
                    onclick="removeActivity(this)">
                ×
            </button>
        `;

        // Hapus pesan "Belum ada aktivitas" jika masih ada
        const emptyMessage = container.querySelector('.text-muted');
        if (emptyMessage) {
            emptyMessage.remove();
        }

        container.appendChild(row);
        renumberActivities(container);
    }

    function removeActivity(button) {
        const row = button.closest('.activity-row');
        const container = row.closest('.activity-card');
        row.remove();
        renumberActivities(container);

        // Jika tidak ada aktivitas, tampilkan pesan
        const rows = container.querySelectorAll('.activity-row');
        if (rows.length === 0) {
            container.innerHTML = `
                <div class="p-3 text-muted small">
                    Belum ada aktivitas. Klik <strong>+ Tambah Aktivitas</strong> untuk menambahkan.
                </div>
            `;
        }
    }

    function renumberActivities(container) {
        const rows = container.querySelectorAll('.activity-row');
        rows.forEach((row, index) => {
            const number = row.querySelector('.activity-number');
            if (number) {
                number.textContent = index + 1;
            }

            // Update name textarea agar index tetap berurutan
            const textarea = row.querySelector('textarea');
            if (textarea) {
                const name = textarea.getAttribute('name');
                const match = name.match(/judgments\[(\d+)\]/);
                if (match) {
                    const objectiveIndex = match[1];
                    textarea.name = `judgments[${objectiveIndex}][activities][${index}]`;
                }
            }
        });
    }
</script>
@endsection