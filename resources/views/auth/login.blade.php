<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - DIGI-Campus</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-pnm.png') }}">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    {{-- Bootstrap --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />

    {{-- Public Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
* { box-sizing: border-box; }

html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;              /* tidak bisa scroll */
    overscroll-behavior: none;     /* cegah bounce di mobile */
    font-family: 'Public Sans', system-ui, -apple-system, sans-serif;
    background: #0b1220;
}

/* ===== Full Layout ===== */
.login-page {
    height: 100%;                  /* gunakan 100% bukan min-height */
    min-height: 100%;
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    overflow: hidden;              /* pastikan grid juga tidak overflow */
}

/* ===== Left Panel (Brand) ===== */
.login-brand-panel {
    position: relative;
    background-image: url('{{ asset('images/background-login.jpeg') }}');
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    overflow: hidden;
    height: 100%;
}

    .login-brand-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(
            160deg,
            rgba(15, 23, 42, 0.88) 0%,
            rgba(29, 78, 216, 0.78) 45%,
            rgba(15, 23, 42, 0.92) 100%
        );
        z-index: 1;
    }

    /* Decorative circles */
    .login-brand-panel::after {
        content: '';
        position: absolute;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(96,165,250,0.25) 0%, transparent 70%);
        top: -80px;
        right: -100px;
        z-index: 1;
    }

    .brand-content {
        position: relative;
        z-index: 2;
        color: #fff;
        max-width: 420px;
    }

    .brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.18);
        backdrop-filter: blur(8px);
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }

    .brand-content h1 {
        font-size: 2.35rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.15;
        margin-bottom: 1rem;
    }

    .brand-content p {
        font-size: 1.05rem;
        color: rgba(255,255,255,0.82);
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .brand-features {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .brand-feature {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.92rem;
        color: rgba(255,255,255,0.9);
    }

    .brand-feature .icon {
        width: 36px;
        height: 36px;
        border-radius: 0.65rem;
        background: rgba(255,255,255,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ===== Right Panel (Form) ===== */
.login-form-panel {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
    background: #f8fafc;
    position: relative;
    height: 100%;
    overflow: hidden;              /* cegah scroll di panel kanan */
}
    .login-form-panel::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(37,99,235,0.06) 0%, transparent 70%);
        bottom: -60px;
        right: -60px;
    }

    .login-card {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 400px;
        background: #ffffff;
        border-radius: 1.5rem;
        padding: 2.5rem 2rem;
        box-shadow:
            0 4px 6px -1px rgba(0,0,0,0.04),
            0 20px 40px -12px rgba(15,23,42,0.08);
        border: 1px solid #eef2f7;
    }

    .login-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 1.75rem;
    }

    .login-logo img {
        height: 44px;
        object-fit: contain;
    }

    .login-logo .logo-text {
        line-height: 1.2;
        text-align: left;
    }

    .login-logo .logo-text strong {
        display: block;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    .login-logo .logo-text span {
        font-size: 0.75rem;
        color: #64748b;
    }

    .login-title {
        margin-bottom: 1.75rem;
        text-align: center;
    }

    .login-title h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
        margin-bottom: 0.35rem;
    }

    .login-title p {
        font-size: 0.9rem;
        color: #64748b;
        margin: 0;
    }

    /* ===== Form Elements ===== */
    .form-label {
        font-weight: 600;
        font-size: 0.82rem;
        color: #334155;
        margin-bottom: 0.45rem;
        letter-spacing: 0.01em;
    }

    .input-wrap {
        position: relative;
        margin-bottom: 1.15rem;
    }

    .input-wrap .form-control {
        height: 50px;
        border-radius: 0.75rem;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        padding-left: 2.85rem;
        padding-right: 1rem;
        font-size: 0.95rem;
        color: #0f172a;
        transition: all 0.2s ease;
        box-shadow: none !important;
    }

    .input-wrap .form-control::placeholder {
        color: #94a3b8;
    }

    .input-wrap .form-control:focus {
        border-color: #3b82f6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12) !important;
    }

    .input-wrap .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.95rem;
        pointer-events: none;
        transition: color 0.2s;
    }

    .input-wrap:focus-within .input-icon {
        color: #3b82f6;
    }

    .input-wrap.is-invalid .form-control {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .input-wrap.is-invalid:focus-within .form-control {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12) !important;
    }

    .btn-toggle-password {
        position: absolute;
        right: 0.65rem;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border: none;
        background: transparent;
        color: #94a3b8;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-toggle-password:hover {
        background: #f1f5f9;
        color: #475569;
    }

    /* Password field needs extra right padding */
    .input-wrap.has-toggle .form-control {
        padding-right: 3rem;
    }

    /* ===== Button ===== */
    .btn-login {
        height: 50px;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
        border: none;
        color: #fff;
        width: 100%;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.28);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
        box-shadow: 0 10px 28px rgba(37, 99, 235, 0.38);
        transform: translateY(-1px);
        color: #fff;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    /* ===== Alert ===== */
    .alert {
        border-radius: 0.75rem;
        font-size: 0.875rem;
        border: none;
        padding: 0.85rem 1rem;
        margin-bottom: 1.25rem;
    }

    .alert-success {
        background: #ecfdf5;
        color: #065f46;
    }

    .alert-danger {
        background: #fef2f2;
        color: #991b1b;
    }

    .alert .btn-close {
        font-size: 0.7rem;
        padding: 0.85rem;
    }

    /* ===== Footer ===== */
    .login-footer {
        text-align: center;
        margin-top: 1.75rem;
        font-size: 0.78rem;
        color: #94a3b8;
    }

    /* ===== Responsive ===== */
    @media (max-width: 991.98px) {
        .login-page {
            grid-template-columns: 1fr;
        }

        .login-brand-panel {
            display: none;
        }

        .login-form-panel {
            min-height: 100vh;
            background:
                linear-gradient(160deg, rgba(15,23,42,0.88), rgba(29,78,216,0.75)),
                url('{{ asset('images/background-login.jpeg') }}') center/cover;
        }

        .login-card {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(16px);
        }
    }

@media (max-width: 991.98px) {
    .login-page {
        grid-template-columns: 1fr;
    }

    .login-brand-panel {
        display: none;
    }

    .login-form-panel {
        height: 100%;
        min-height: 100%;
        background:
            linear-gradient(160deg, rgba(15,23,42,0.88), rgba(29,78,216,0.75)),
            url('{{ asset('images/background-login.jpeg') }}') center/cover;
        overflow: hidden;
    }

    .login-card {
        background: rgba(255,255,255,0.97);
        backdrop-filter: blur(16px);
        max-height: calc(100% - 2rem);   /* supaya kartu tidak overflow */
        overflow-y: auto;                /* kalau konten form terlalu panjang di HP, hanya kartu yang scroll */
    }
}
</style>
</head>

<body>
    <div class="login-page">

        {{-- ===== LEFT: Brand Panel ===== --}}
        <div class="login-brand-panel">
            <div class="brand-content">
                <div class="brand-badge">
                    <i class="fas fa-university"></i>
                    Politeknik Negeri Madiun
                </div>

                <h1>Transformasi Digital<br>untuk Smart Campus</h1>
                <p>
                    Platform terintegrasi untuk perencanaan, pelaksanaan,
                    dan assessment COBIT 2019 di lingkungan kampus.
                </p>

                <div class="brand-features">
                    <div class="brand-feature">
                        <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                        <span>Perencanaan & Pelaksanaan Kegiatan</span>
                    </div>
                    <div class="brand-feature">
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                        <span>Assessment & Gap Analysis COBIT 2019</span>
                    </div>
                    <div class="brand-feature">
                        <div class="icon"><i class="fas fa-shield-halved"></i></div>
                        <span>Akses aman berdasarkan peran pengguna</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT: Form Panel ===== --}}
        <div class="login-form-panel">
            <div class="login-card">

                {{-- Logo --}}
                <div class="login-logo">
                    <img src="{{ asset('images/logo-pnm.png') }}" alt="DIGI-Campus">
                    <div class="logo-text">
                        <strong>DIGI-Campus</strong>
                        <span>Smart Campus Platform</span>
                    </div>
                </div>

                {{-- Title --}}
                <div class="login-title">
                    <h2>Selamat datang</h2>
                    <p>Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                {{-- Alert --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error') || $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') ?: ($errors->first() ?: 'Email atau password salah.') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <label for="email" class="form-label">Email</label>
                    <div class="input-wrap @error('email') is-invalid @enderror">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            placeholder="nama@email.com"
                            value="{{ old('email') }}"
                            required
                            autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger small mb-2" style="margin-top:-0.65rem">{{ $message }}</div>
                    @enderror

                    {{-- Password --}}
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrap has-toggle @error('password') is-invalid @enderror">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            required>
                        <button class="btn-toggle-password"
                                type="button"
                                id="togglePassword"
                                title="Tampilkan / Sembunyikan password">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger small mb-2" style="margin-top:-0.65rem">{{ $message }}</div>
                    @enderror

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-arrow-right-to-bracket"></i>
                            Masuk
                        </button>
                    </div>
                </form>

                <div class="login-footer">
                    © {{ date('Y') }} DIGI-Campus · Politeknik Negeri Madiun
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>