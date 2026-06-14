@extends('layouts.app')

@section('title', 'Accounts — InEx Tracker')
@section('page-title', 'Accounts')

@section('topbar-actions')
    <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Account
    </a>
@endsection

@section('content')
<div class="space-y">

    {{-- Exchange Rate Card --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">USD → BDT Exchange Rate</div>
                    <div class="card-subtitle">Used for Payoneer account conversions</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('settings.exchange-rate') }}" style="display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap;">
                @csrf
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:200px;">
                    <label class="form-label">1 USD = ? BDT</label>
                    <div class="input-group">
                        <span class="input-prefix">৳</span>
                        <input type="number" step="0.01" name="usd_to_bdt_rate" id="usd_to_bdt_rate"
                            value="{{ $usdToBdtRate }}"
                            class="form-control"
                            placeholder="121.00" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Update Rate
                </button>
            </form>
            <div class="alert alert-info" style="margin-top:16px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div class="alert-text">
                    When you say <em>"I transferred 20 dollar from Payoneer to Upay"</em>,
                    the system converts $20 using this rate (currently <strong>{{ number_format($usdToBdtRate, 2) }} BDT/USD</strong>).
                </div>
            </div>
        </div>
    </div>

    {{-- Accounts Grid --}}
    <div class="grid-3">
        @forelse($accounts as $account)
            @php
                $isMain = $account->isMainAccount();
                $isUsd  = $account->isUsdAccount();
            @endphp
            <div class="account-card {{ $isMain ? 'style=border-color:var(--border-accent);' : '' }}">
                <div class="account-card-header">
                    <div class="account-card-name">{{ $account->name }}</div>
                    <span class="badge {{ $isMain ? 'badge-indigo' : ($isUsd ? 'badge-warning' : 'badge-neutral') }}">
                        {{ $isMain ? 'Aggregate' : ($isUsd ? 'USD' : 'BDT') }}
                    </span>
                </div>

                @if($isUsd)
                    <div class="account-card-balance">${{ number_format($account->balance / $usdToBdtRate, 2) }}</div>
                    <div class="account-card-sub">≈ ৳{{ number_format($account->balance, 2) }} BDT</div>
                @else
                    <div class="account-card-balance">৳{{ number_format($account->balance, 2) }}</div>
                    <div class="account-card-sub">Available balance</div>
                @endif

                <div style="display:flex; align-items:center; gap:8px; margin-top:4px;">
                    <span class="badge badge-neutral">{{ $account->transactions_count }} transactions</span>
                </div>

                <div class="account-card-actions">
                    <a href="{{ route('accounts.show', $account) }}" class="btn btn-ghost btn-sm flex-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        View
                    </a>
                    <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-indigo btn-sm flex-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                    </a>
                    @if(!$isMain)
                        <form method="POST" action="{{ route('accounts.destroy', $account) }}"
                            class="inline-form"
                            data-confirm-delete
                            data-confirm-title="Delete '{{ $account->name }}'?"
                            data-confirm-desc="All transactions linked to this account will also be deleted. This cannot be undone.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
                                    <path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <div class="empty-state-title">No accounts yet</div>
                    <div class="empty-state-desc">
                        <a href="{{ route('accounts.create') }}" class="text-indigo" style="font-weight:600;">Create your first account</a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection