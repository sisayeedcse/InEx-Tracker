<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InEx Tracker')</title>
    <meta name="description" content="InEx Tracker — Personal finance tracker for accounts, income, expenses and cost estimations.">

    {{-- Design System CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

{{-- Flash data for JS toasts --}}
@if(session('success'))
    <div id="flash-success" data-message="{{ session('success') }}" style="display:none;"></div>
@endif
@if(session('error'))
    <div id="flash-error" data-message="{{ session('error') }}" style="display:none;"></div>
@endif

{{-- Toast Container --}}
<div id="toast-container"></div>

{{-- Sidebar Overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="app-wrapper">

    {{-- =========== SIDEBAR =========== --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">💰</div>
            <div class="sidebar-brand-text">
                <div class="sidebar-brand-name">InEx Tracker</div>
                <div class="sidebar-brand-sub">Finance Manager</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <span class="nav-section-label">Main</span>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('transactions.index') }}"
               class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Transactions
            </a>

            <a href="{{ route('accounts.index') }}"
               class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
                Accounts
            </a>

            <a href="{{ route('cost-estimations.index') }}"
               class="nav-link {{ request()->routeIs('cost-estimations.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                Cost Estimation
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-footer-text">InEx Tracker &copy; {{ date('Y') }}</div>
            <div style="text-align:center; margin-top:6px;">
                <span class="sidebar-footer-badge">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                    Laravel 12
                </span>
            </div>
        </div>
    </aside>

    {{-- =========== MAIN CONTENT =========== --}}
    <div class="main-content">

        {{-- Topbar --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger-btn" id="hamburger-btn" aria-label="Toggle sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            </div>
            <div class="topbar-right">
                @yield('topbar-actions')
            </div>
        </header>

        {{-- Page Content --}}
        <main class="page-content animate-fade-in">
            @yield('content')
        </main>

    </div>{{-- end .main-content --}}
</div>{{-- end .app-wrapper --}}

{{-- Global JS --}}
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')

</body>
</html>