<nav class="navbar-modern">
    <div class="navbar-inner">

        {{-- Kiri --}}
        <div class="navbar-left">
            <button type="button" id="sidebarToggle" class="btn-toggle-sidebar" title="Toggle menu">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <div class="page-heading">
                {{-- <h2 class="page-title mb-0">
                    @yield('page-title', 'Dashboard')
                </h2> --}}
                @hasSection('page-subtitle')
                    <p class="page-subtitle mb-0">@yield('page-subtitle')</p>
                @endif
            </div>
        </div>

        {{-- Kanan --}}
        <div class="navbar-right">
            <div class="dropdown">
                <a href="#"
                   class="user-trigger"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1d4ed8&color=fff&bold=true"
                         alt="Profile"
                         width="38"
                         height="38"
                         class="user-avatar">

                    <div class="user-meta d-none d-md-block">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">{{ Auth::user()->role->name ?? '-' }}</div>
                    </div>

                    <span class="material-symbols-outlined user-chevron d-none d-md-inline">expand_more</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end user-dropdown">
                    <li class="dropdown-header-user">
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1d4ed8&color=fff&bold=true"
                                 alt="Profile"
                                 width="40"
                                 height="40"
                                 class="rounded-circle">
                            <div>
                                <div class="fw-semibold" style="font-size:0.9rem;color:#0f172a">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-muted" style="font-size:0.75rem">
                                    {{ Auth::user()->email ?? (Auth::user()->role->name ?? '-') }}
                                </div>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <span class="material-symbols-outlined">person</span>
                            Profil Saya
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <span class="material-symbols-outlined">logout</span>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
.navbar-modern {
    position: sticky;
    top: 0;
    z-index: 1020;
    background: linear-gradient(90deg, 
        rgba(239, 246, 255, 0.92) 0%, 
        rgba(255, 255, 255, 0.88) 40%, 
        rgba(238, 242, 255, 0.90) 100%
    );
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(191, 219, 254, 0.6);
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.06);
    /* HAPUS overflow: hidden agar dropdown tidak terpotong */
}

/* Soft blue glow di pojok */
.navbar-modern::before {
    content: '';
    position: absolute;
    top: -40px;
    right: 10%;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
}

.navbar-modern::after {
    content: '';
    position: absolute;
    bottom: -50px;
    left: 5%;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.10) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
}

.navbar-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0.7rem 1.35rem;
    gap: 1rem;
    position: relative;
    z-index: 1;
}

/* ===== Kiri ===== */
.navbar-left {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    min-width: 0;
}

.btn-toggle-sidebar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 1px solid rgba(191, 219, 254, 0.7);
    border-radius: 0.7rem;
    background: rgba(255, 255, 255, 0.8);
    color: #334155;
    cursor: pointer;
    transition: all 0.18s ease;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.06);
}

.btn-toggle-sidebar:hover {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #1d4ed8;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12);
}

.btn-toggle-sidebar .material-symbols-outlined {
    font-size: 1.35rem;
}

.page-heading {
    min-width: 0;
}

.page-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.page-subtitle {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ===== Kanan ===== */
.navbar-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.btn-icon-nav {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 1px solid rgba(191, 219, 254, 0.7);
    border-radius: 0.7rem;
    background: rgba(255, 255, 255, 0.8);
    color: #64748b;
    cursor: pointer;
    transition: all 0.18s ease;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.06);
}

.btn-icon-nav:hover {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #93c5fd;
}

.btn-icon-nav .notif-dot {
    position: absolute;
    top: 8px;
    right: 9px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #ef4444;
    border: 1.5px solid #fff;
}

/* User trigger */
.user-trigger {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.3rem 0.55rem 0.3rem 0.3rem;
    border-radius: 999px;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.18s ease;
}

.user-trigger:hover {
    background: rgba(239, 246, 255, 0.8);
    border-color: rgba(191, 219, 254, 0.8);
}

.user-avatar {
    border-radius: 50%;
    border: 2px solid #bfdbfe;
    object-fit: cover;
    flex-shrink: 0;
}

.user-meta {
    line-height: 1.2;
    text-align: left;
}

.user-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f172a;
}

.user-role {
    font-size: 0.72rem;
    color: #64748b;
    text-transform: capitalize;
}

.user-chevron {
    font-size: 1.15rem !important;
    color: #94a3b8;
}

/* Dropdown - pastikan muncul di atas */
.user-dropdown {
    min-width: 240px;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 0.5rem;
    margin-top: 0.55rem !important;
    box-shadow:
        0 10px 15px -3px rgba(15, 23, 42, 0.08),
        0 4px 6px -4px rgba(15, 23, 42, 0.05) !important;
    background: #ffffff;
    z-index: 1050 !important; /* pastikan di atas elemen lain */
}

.dropdown-header-user {
    padding: 0.65rem 0.75rem 0.5rem;
}

.user-dropdown .dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.7rem 0.85rem;
    border-radius: 0.65rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #334155;
    transition: all 0.15s ease;
}

.user-dropdown .dropdown-item:hover {
    background: #eff6ff;
    color: #1d4ed8;
}

.user-dropdown .dropdown-item.text-danger {
    color: #dc2626;
}

.user-dropdown .dropdown-item.text-danger:hover {
    background: #fef2f2;
    color: #b91c1c;
}

.user-dropdown .material-symbols-outlined {
    font-size: 1.2rem;
    opacity: 0.9;
}

.user-dropdown .dropdown-divider {
    border-color: #f1f5f9;
    margin: 0.35rem 0;
}

/* Responsive */
@media (max-width: 575.98px) {
    .navbar-inner {
        padding: 0.65rem 1rem;
    }

    .page-title {
        font-size: 1rem;
    }
}
</style>