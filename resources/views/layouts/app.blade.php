<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DIGI-Campus')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-pnm.png') }}">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

    <style>
    :root {
        --sidebar-width: 280px;
        --sidebar-width-collapsed: 0px;
        --primary: #1d4ed8;
        --primary-dark: #1e40af;
        --content-padding-x: 1.75rem;
        --content-padding-y: 1.5rem;
    }

    * { box-sizing: border-box; }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(160deg, 
            #f8fafc 0%, 
            #f1f5f9 30%, 
            #eff6ff 65%, 
            #eef2ff 100%
        );
        background-attachment: fixed; /* biar gradasi tetap saat scroll */
        margin: 0;
        overflow-x: hidden;
        min-height: 100vh;
    }

    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }

    /* ===== Layout Wrapper ===== */
    .app-wrapper {
        display: flex;
        min-height: 100vh;
        width: 100%;
        position: relative;
    }

    /* ===== Sidebar ===== */
    .app-sidebar {
        width: var(--sidebar-width);
        min-width: var(--sidebar-width);
        height: 100vh;
        height: 100dvh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        /* biarkan style sidebar dari file sidebar (gradasi + orb) */
        display: flex;
        flex-direction: column;
        transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
    }

    .app-sidebar.collapsed {
        transform: translateX(-100%);
    }

    /* ===== Main Area (Navbar + Content) ===== */
    .app-main {
        flex: 1;
        margin-left: var(--sidebar-width);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        min-height: 100dvh;
        width: calc(100% - var(--sidebar-width));
        transition: margin-left 0.28s cubic-bezier(0.4, 0, 0.2, 1),
                    width 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent; /* biar ikut background body */
    }

    .app-main.expanded {
        margin-left: 0;
        width: 100%;
    }

    /* Content area */
    .app-content {
        flex: 1;
        padding: var(--content-padding-y) var(--content-padding-x);
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        position: relative;
    }

    /* Soft blue orbs di content (opsional, biar lebih hidup) */
    .app-content::before,
    .app-content::after {
        content: '';
        position: fixed; /* fixed biar tidak ikut scroll konten */
        border-radius: 50%;
        pointer-events: none;
        z-index: 0;
    }

    .app-content::before {
        width: 320px;
        height: 320px;
        top: 80px;
        right: -80px;
        background: radial-gradient(circle, 
            rgba(59, 130, 246, 0.10) 0%, 
            rgba(37, 99, 235, 0.04) 50%, 
            transparent 70%
        );
    }

    .app-content::after {
        width: 260px;
        height: 260px;
        bottom: 60px;
        left: 20%;
        background: radial-gradient(circle, 
            rgba(99, 102, 241, 0.09) 0%, 
            rgba(59, 130, 246, 0.03) 50%, 
            transparent 70%
        );
    }

    /* Pastikan isi content di atas orb */
    .app-content > * {
        position: relative;
        z-index: 1;
    }

    /* Footer */
    .app-footer {
        padding: 1rem var(--content-padding-x);
        border-top: 1px solid rgba(191, 219, 254, 0.5);
        text-align: center;
        font-size: 0.8rem;
        color: #64748b;
        background: linear-gradient(90deg, 
            rgba(239, 246, 255, 0.7) 0%, 
            rgba(255, 255, 255, 0.85) 50%, 
            rgba(238, 242, 255, 0.7) 100%
        );
        backdrop-filter: blur(8px);
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }

    /* Overlay mobile */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(3px);
        z-index: 1035;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .sidebar-overlay.show {
        display: block;
        opacity: 1;
    }

    /* ========== RESPONSIVE ========== */

    @media (min-width: 1400px) {
        :root {
            --content-padding-x: 2rem;
            --content-padding-y: 1.75rem;
        }
    }

    @media (max-width: 991.98px) {
        :root {
            --sidebar-width: 280px;
            --content-padding-x: 1.25rem;
            --content-padding-y: 1.25rem;
        }

        .app-sidebar {
            transform: translateX(-100%);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.08);
        }

        .app-sidebar.mobile-open {
            transform: translateX(0);
        }

        .app-main {
            margin-left: 0;
            width: 100%;
        }

        .app-main.expanded {
            margin-left: 0;
            width: 100%;
        }
    }

    @media (max-width: 767.98px) {
        :root {
            --sidebar-width: 260px;
            --content-padding-x: 1rem;
            --content-padding-y: 1rem;
        }

        .app-content {
            padding: var(--content-padding-y) var(--content-padding-x);
        }

        .app-footer {
            font-size: 0.75rem;
            padding: 0.85rem var(--content-padding-x);
        }
    }

    @media (max-width: 575.98px) {
        :root {
            --sidebar-width: 100%;
            --content-padding-x: 0.85rem;
            --content-padding-y: 0.85rem;
        }

        .app-sidebar {
            width: min(280px, 85vw);
            min-width: min(280px, 85vw);
        }

        .app-footer {
            font-size: 0.72rem;
            line-height: 1.4;
        }
    }

    @media (max-width: 400px) {
        :root {
            --content-padding-x: 0.75rem;
            --content-padding-y: 0.75rem;
        }
    }
    </style>

    @stack('styles')
</head>
<body>

<div class="app-wrapper">

    {{-- Overlay untuk mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Main (Navbar + Content + Footer) --}}
    <div class="app-main" id="appMain">
        @include('layouts.navbar')

        <main class="app-content">
            @yield('content')
        </main>

        <footer class="app-footer">
            © {{ date('Y') }} DIGI-Campus · Politeknik Negeri Madiun
        </footer>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('appSidebar');
    const appMain = document.getElementById('appMain');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    function isMobile() {
        return window.innerWidth < 992;
    }

    function toggleSidebar() {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
            // Cegah scroll body saat sidebar terbuka
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        } else {
            sidebar.classList.toggle('collapsed');
            appMain.classList.toggle('expanded');
        }
    }

    function closeMobileSidebar() {
        if (sidebar) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    // Tutup sidebar otomatis saat resize ke desktop
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (!isMobile()) {
                closeMobileSidebar();
                // Reset state collapsed jika perlu (opsional)
            }
        }, 150);
    });

    // Tutup sidebar saat tekan Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isMobile()) {
            closeMobileSidebar();
        }
    });
</script>
@stack('scripts')

{{-- ===== Modal Sukses / Error (tengah layar) ===== --}}
@if(session('success') || session('error'))
@php
  $isSuccess = session()->has('success');
  $message   = session('success') ?? session('error');
@endphp

<div class="success-overlay" id="successOverlay">
  <div class="success-modal {{ $isSuccess ? 'is-success' : 'is-error' }}">
    <div class="success-icon-wrap">
      <div class="success-icon-circle">
        @if($isSuccess)
          <span class="material-symbols-outlined">check</span>
        @else
          <span class="material-symbols-outlined">close</span>
        @endif
      </div>
    </div>

    <h3 class="success-title">
      {{ $isSuccess ? 'Berhasil!' : 'Gagal' }}
    </h3>
    <p class="success-message">{{ $message }}</p>

    <button type="button" class="btn success-btn" id="successCloseBtn">
      OK
    </button>
  </div>
</div>

<style>
  .success-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    height: 100dvh !important;
    z-index: 999999 !important;          /* sangat tinggi */
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    padding: 1rem;
    margin: 0;
    animation: overlayIn 0.25s ease forwards;
  }

  .success-modal {
    position: relative;
    z-index: 1;
    background: #fff;
    border-radius: 1.25rem;
    padding: 2rem 1.5rem 1.5rem;
    max-width: 380px;
    width: 100%;
    text-align: center;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
    animation: modalPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }

  .success-icon-wrap {
    margin-bottom: 1.25rem;
  }

  .success-icon-circle {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: iconPop 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both;
  }

  .is-success .success-icon-circle {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.35);
  }
  .is-error .success-icon-circle {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
    box-shadow: 0 8px 24px rgba(239, 68, 68, 0.35);
  }

  .success-icon-circle .material-symbols-outlined {
    font-size: 2.25rem;
    color: #fff;
    font-variation-settings: 'FILL' 1, 'wght' 600;
  }

  .success-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.5rem;
  }
  .is-success .success-title { color: #065f46; }
  .is-error .success-title { color: #991b1b; }

  .success-message {
    font-size: 0.9rem;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 1.5rem;
  }

  .success-btn {
    min-width: 120px;
    padding: 0.65rem 1.5rem;
    border-radius: 0.65rem;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    color: #fff;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
  }
  .is-success .success-btn {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
  }
  .is-error .success-btn {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
  }
  .success-btn:hover {
    transform: translateY(-1px);
  }

  .success-overlay.hiding {
    animation: overlayOut 0.25s ease forwards;
  }
  .success-overlay.hiding .success-modal {
    animation: modalOut 0.25s ease forwards;
  }

  @keyframes overlayIn {
    from { opacity: 0; }
    to   { opacity: 1; }
  }
  @keyframes overlayOut {
    from { opacity: 1; }
    to   { opacity: 0; }
  }
  @keyframes modalPop {
    from { opacity: 0; transform: scale(0.85) translateY(12px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
  }
  @keyframes modalOut {
    from { opacity: 1; transform: scale(1); }
    to   { opacity: 0; transform: scale(0.9); }
  }
  @keyframes iconPop {
    from { opacity: 0; transform: scale(0.5); }
    to   { opacity: 1; transform: scale(1); }
  }

  @media (max-width: 400px) {
    .success-modal {
      padding: 1.75rem 1.25rem 1.35rem;
      border-radius: 1rem;
    }
    .success-icon-circle {
      width: 64px;
      height: 64px;
    }
    .success-icon-circle .material-symbols-outlined {
      font-size: 2rem;
    }
  }
</style>

<script>
  (function () {
    const overlay = document.getElementById('successOverlay');
    if (!overlay) return;

    // Pindahkan overlay ke langsung di bawah <body> (penting!)
    document.body.appendChild(overlay);

    const closeBtn = document.getElementById('successCloseBtn');

    function closeModal() {
      overlay.classList.add('hiding');
      setTimeout(() => overlay.remove(), 250);
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeModal();
    });

    // Auto close
    setTimeout(closeModal, 3500);
  })();
</script>
@endif

</body>
</html>