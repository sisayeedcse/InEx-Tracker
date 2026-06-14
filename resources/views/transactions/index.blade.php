@extends('layouts.app')

@section('title', 'Transactions — InEx Tracker')
@section('page-title', 'Transactions')

@section('topbar-actions')
    <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-ghost btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Export CSV
    </a>
@endsection

@section('content')
<div class="space-y">

    {{-- Summary Stat Cards --}}
    <div class="grid-3">
        <div class="stat-card">
            <div class="stat-card-label">Filtered Income</div>
            <div class="stat-card-value success">৳{{ number_format($sumIncome, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Filtered Expense</div>
            <div class="stat-card-value danger">৳{{ number_format($sumExpense, 2) }}</div>
        </div>
        <div class="stat-card">
            @php $net = $sumIncome - $sumExpense; @endphp
            <div class="stat-card-label">Net (filtered)</div>
            <div class="stat-card-value {{ $net >= 0 ? 'success' : 'danger' }}">
                {{ $net >= 0 ? '+' : '' }}৳{{ number_format($net, 2) }}
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                </div>
                <div class="card-title">Filter Transactions</div>
            </div>
            @if(request()->hasAny(['account_id', 'type', 'start_date', 'end_date', 'keyword']))
                <a href="{{ route('transactions.index') }}" class="btn btn-ghost btn-sm">Clear Filters</a>
            @endif
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" id="filterForm">
                <div class="filter-bar" style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr auto; gap:12px; align-items:end;">
                    <div class="form-group">
                        <label class="form-label">Account</label>
                        <select name="account_id" class="form-control">
                            <option value="">All Accounts</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            <option value="income"  {{ request('type') == 'income'  ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">From Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">To Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keyword</label>
                        <input type="text" name="keyword" class="form-control" placeholder="Search notes..." value="{{ request('keyword') }}">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Transactions Table --}}
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
                    <div class="card-title">Transaction History</div>
                    <div class="card-subtitle">{{ $transactions->total() }} transaction(s) found</div>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Note</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="td-muted text-xs">{{ $transaction->id }}</td>
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
                            <td style="font-weight:600;">{{ $transaction->account->name }}</td>
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
                                    data-confirm-desc="The balance of {{ $transaction->account->name }} will be restored by ৳{{ number_format($transaction->amount, 2) }}.">
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
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <line x1="12" y1="1" x2="12" y2="23"/>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                    </div>
                                    <div class="empty-state-title">No transactions found</div>
                                    <div class="empty-state-desc">Try adjusting your filters or go to the Dashboard to add transactions.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination">
                    {{-- Previous --}}
                    @if($transactions->onFirstPage())
                        <span class="page-link disabled">&laquo;</span>
                    @else
                        <a href="{{ $transactions->previousPageUrl() }}" class="page-link">&laquo;</a>
                    @endif

                    {{-- Pages --}}
                    @foreach($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                        @if($page == $transactions->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}" class="page-link">&raquo;</a>
                    @else
                        <span class="page-link disabled">&raquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection