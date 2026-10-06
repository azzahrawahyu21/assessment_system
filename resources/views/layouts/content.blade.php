<div class="container-fluid" style="max-width: 1200px; justify-content: right; align-items: right; margin-left: 280px;">
    <!-- Metrics Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card h-100 border shadow-sm">
                <div class="card-body">
                    <p class="text-uppercase text-muted small mb-1">Maturity Level</p>
                    <h3 class="text-primary fw-bold display-5">78%</h3>
                    <div class="text-success small mt-3">
                        <span class="material-symbols-outlined">arrow_upward</span>
                        +2.4% vs last period
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border shadow-sm">
                <div class="card-body">
                    <p class="text-uppercase text-muted small mb-1">Alignment Index</p>
                    <h3 class="text-primary fw-bold display-5">82%</h3>
                    <div class="text-success small mt-3">
                        <span class="material-symbols-outlined">arrow_upward</span>
                        +1.8% target met
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border shadow-sm">
                <div class="card-body">
                    <p class="text-uppercase text-muted small mb-1">KPI Score</p>
                    <h3 class="text-primary fw-bold display-5">80%</h3>
                    <div class="text-muted small mt-3">
                        <span class="material-symbols-outlined">remove</span>
                        Stable progress
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border shadow-sm">
                <div class="card-body">
                    <p class="text-uppercase text-muted small mb-1">Readiness Score</p>
                    <h3 class="text-primary fw-bold display-5">75%</h3>
                    <div class="text-danger small mt-3">
                        <span class="material-symbols-outlined">priority_high</span>
                        Capacity gap detected
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-5">
        <!-- Radar Chart -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Maturity Assessment Radar</h6>
                    <small class="text-muted">Evaluation by dimension</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 420px;">
                    <svg width="340" height="340" viewBox="0 0 400 400">
                        <!-- Isi SVG sama seperti sebelumnya -->
                        <circle class="radar-grid" cx="200" cy="200" r="150"/>
                        <circle class="radar-grid" cx="200" cy="200" r="100"/>
                        <circle class="radar-grid" cx="200" cy="200" r="50"/>
                        <line class="radar-grid" x1="200" x2="200" y1="50" y2="350"/>
                        <line class="radar-grid" x1="50" x2="350" y1="200" y2="200"/>
                        <polygon class="radar-area" points="200,80 320,180 250,300 120,280 80,150"/>
                        <text x="200" y="40" text-anchor="middle" class="small">Strategy</text>
                        <text x="360" y="200" text-anchor="middle" class="small">Tech</text>
                        <text x="200" y="370" text-anchor="middle" class="small">Culture</text>
                        <text x="40" y="200" text-anchor="middle" class="small">Processes</text>
                    </svg>
                </div>
            </div>
        </div>

        <!-- KPI Trend -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">KPI Performance Trend</h6>
                        <small class="text-muted">Quarterly progression</small>
                    </div>
                    <select class="form-select w-auto">
                        <option>Year 2024</option>
                        <option>Year 2023</option>
                    </select>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 420px;">
                    <svg width="100%" height="220" viewBox="0 0 400 200">
                        <!-- Trend SVG sama seperti aslinya -->
                        <defs>
                            <linearGradient id="trend-grad" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#00668a"/>
                                <stop offset="100%" stop-color="#ffffff"/>
                            </linearGradient>
                        </defs>
                        <path class="trend-gradient" d="M0,150 Q50,140 100,100 T200,110 T300,60 T400,20 L400,200 L0,200 Z"/>
                        <path class="trend-line" d="M0,150 Q50,140 100,100 T200,110 T300,60 T400,20"/>
                        <g>
                            <line x1="0" x2="400" y1="180" y2="180" stroke="#c2c7d1" stroke-width="1"/>
                            <line x1="0" x2="400" y1="120" y2="120" stroke="#c2c7d1" stroke-width="1"/>
                            <line x1="0" x2="400" y1="60" y2="60" stroke="#c2c7d1" stroke-width="1"/>
                        </g>
                        <text x="10" y="195" class="small">Q1</text>
                        <text x="110" y="195" class="small">Q2</text>
                        <text x="210" y="195" class="small">Q3</text>
                        <text x="310" y="195" class="small">Q4</text>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekomendasi & Insight -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border border-2 border-primary shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-1">Panel Rekomendasi Strategis</h6>
                    <small class="text-muted mb-0">Langkah prioritas untuk percepatan transformasi digital universitas.</small>
                </div>
                {{-- <div class="card-body">
                    <!-- Recommendation items (sama strukturnya, gunakan Bootstrap grid) -->
                    @include('layouts.recommendations') <!-- Opsional: bisa dipisah lagi -->
                </div> --}}
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100 bg-primary text-white shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h6>Mulai Assessment Baru</h6>
                        <small class="opacity-75">Jalankan evaluasi mandiri tahunan untuk periode 2024-2025.</small>
                    </div>
                    <button class="btn btn-light w-100 py-3 fw-bold">Assessment Dimulai</button>
                </div>
            </div>
        </div>
    </div>
</div>