@extends('layouts.app')

@section('title', '{{ $account->name }} — InEx Tracker')
@section('page-title', $account->name)

@section('topbar-actions')
    <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-indigo btn-sm">Edit</a>
@endsection

@section('content')
<div class="space-y">

    {{-- Account Summary --}}
    <div class="grid-3">
        <div class="stat-card">
            <div class="stat-card-label">Current Balance</div>
            @if($account->isUsdAccount())
                <div class="stat-card-value success">${{ number_format($account->balance / $usdToBdtRate, 2) }}</div>
                <div class="stat-card-meta">≈ ৳{{ number_format($account->balance, 2) }} BDT</div>
            @else
                <div class="stat-card-value success">৳{{ number_format($account->balance, 2) }}</div>
                <div class="stat-card-meta">Available balance</div>
            @endif
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Total Income</div>
            <div class="stat-card-value success">৳{{ number_format($account->transactions->where('type','income')->sum('amount'), 2) }}</div>
            <div class="stat-card-meta">Money received</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Total Expense</div>
            <div class="stat-card-value danger">৳{{ number_format($account->transactions->where('type','expense')->sum('amount'), 2) }}</div>
            <div class="stat-card-meta">Money spent</div>
        </div>
    </div>

    {{-- Transaction History --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                        <line x1="8" y1="18" x2="21" y2="18"/>
                        <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/>
                        <line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">All Transactions</div>
                    <div class="card-subtitle">{{ $account->transactions->count() }} transaction(s) for this account</div>
                </div>
            </div>
            <a href="{{ route('accounts.index') }}" class="btn btn-ghost btn-sm">← Accounts</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Note</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($account->transactions->sortByDesc('created_at') as $transaction)
                        <tr>
                            <td>
                                <div style="font-weight:500;">{{ $transaction->created_at->format('M d, Y') }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $transaction->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                @if($transaction->type === 'income')
                                    <span class="badge badge-success">
                                        <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                                        Income
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                        Expense
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight:700; color:{{ $transaction->type === 'income' ? 'var(--success)' : 'var(--danger)' }};">
                                    {{ $transaction->type === 'income' ? '+' : '−' }}৳{{ number_format($transaction->amount, 2) }}
                                </span>
                            </td>
                            <td class="td-muted" style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $transaction->note }}">
                                {{ $transaction->note ?? '—' }}
                            </td>
                            <td style="text-align:center;">
                                <form method="POST"
                                    action="{{ route('transactions.destroy', $transaction) }}"
                                    class="inline-form"
                                    data-confirm-delete
                                    data-confirm-title="Delete this transaction?"
                                    data-confirm-desc="The balance will be restored by ৳{{ number_format($transaction->amount, 2) }}.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon" title="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6"/><path d="M14 11v6"/>
                                            <path d="M9 6V4h6v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <line x1="12" y1="1" x2="12" y2="23"/>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                    </div>
                                    <div class="empty-state-title">No transactions for this account</div>
                                    <div class="empty-state-desc">Use the AI Parser on the Dashboard to add transactions.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection