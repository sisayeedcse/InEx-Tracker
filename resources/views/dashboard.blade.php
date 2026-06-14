@extends('layouts.app')

@section('title', 'Dashboard — InEx Tracker')
@section('page-title', 'Dashboard')

@section('topbar-actions')
    <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Account
    </a>
@endsection

@section('content')
<div class="space-y">

    {{-- ===== KPI CARDS ===== --}}
    <div class="grid-4">
        <div class="stat-card">
            <div class="stat-card-accent" style="background:var(--indigo);"></div>
            <div class="stat-card-label">Total Balance</div>
            <div class="stat-card-value indigo"
                 data-count="{{ $totalBalance }}" data-prefix="৳" data-decimals="2">৳0.00</div>
            <div class="stat-card-meta">All accounts combined</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-accent" style="background:var(--success);"></div>
            <div class="stat-card-label">All-Time Income</div>
            <div class="stat-card-value success"
                 data-count="{{ $totalIncome }}" data-prefix="৳" data-decimals="2">৳0.00</div>
            <div class="stat-card-meta">Total money in</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-accent" style="background:var(--danger);"></div>
            <div class="stat-card-label">All-Time Expense</div>
            <div class="stat-card-value danger"
                 data-count="{{ $totalExpense }}" data-prefix="৳" data-decimals="2">৳0.00</div>
            <div class="stat-card-meta">Total money out</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-accent" style="background:var(--warning);"></div>
            <div class="stat-card-label">This Month Net</div>
            @php $monthNet = $monthIncome - $monthExpense; @endphp
            <div class="stat-card-value {{ $monthNet >= 0 ? 'success' : 'danger' }}"
                 data-count="{{ abs($monthNet) }}" data-prefix="{{ $monthNet >= 0 ? '+' : '-' }}৳" data-decimals="2">৳0</div>
            <div class="stat-card-meta">Income vs. Expense this month</div>
        </div>
    </div>

    {{-- ===== CHARTS + ACCOUNTS ===== --}}
    <div class="grid-2" style="grid-template-columns: 2fr 1fr;">

        {{-- 6-Month Bar Chart --}}
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-icon indigo">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                    <div>
                        <div class="card-title">6-Month Overview</div>
                        <div class="card-subtitle">Income vs. Expense trends</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:220px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Donut Chart --}}
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-icon success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div>
                        <div class="card-title">This Month</div>
                        <div class="card-subtitle">Income vs. Expense split</div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="display:flex; flex-direction:column; align-items:center; gap:16px;">
                <div class="chart-container" style="height:160px; width:160px;">
                    <canvas id="donutChart"></canvas>
                </div>
                <div style="display:flex; gap:20px; justify-content:center;">
                    <div style="text-align:center;">
                        <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.8px; margin-bottom:4px;">Income</div>
                        <div style="font-size:15px; font-weight:700; color:var(--success);">৳{{ number_format($monthIncome, 2) }}</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.8px; margin-bottom:4px;">Expense</div>
                        <div style="font-size:15px; font-weight:700; color:var(--danger);">৳{{ number_format($monthExpense, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ACCOUNT BALANCES ===== --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Account Balances</div>
                    <div class="card-subtitle">Overview of all your accounts</div>
                </div>
            </div>
            <a href="{{ route('accounts.index') }}" class="btn btn-ghost btn-sm">Manage</a>
        </div>
        <div class="card-body">
            <div class="grid-3">
                @foreach($accounts as $account)
                    @php
                        $isMain = $account->isMainAccount();
                        $isUsd  = $account->isUsdAccount();
                        $pct    = $totalBalance > 0 ? min(100, ($account->balance / $totalBalance) * 100) : 0;
                    @endphp
                    <div class="account-card" style="{{ $isMain ? 'border-color:var(--border-accent);' : '' }}">
                        <div class="account-card-header">
                            <div class="account-card-name">{{ $account->name }}</div>
                            <span class="badge {{ $isMain ? 'badge-indigo' : 'badge-neutral' }}">
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

                        @if(!$isMain)
                            <div class="progress" style="margin-top:8px;">
                                <div class="progress-bar indigo" style="width:{{ $pct }}%;"></div>
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">{{ number_format($pct, 1) }}% of total</div>
                        @endif

                        <div class="account-card-actions">
                            <a href="{{ route('accounts.show', $account) }}" class="btn btn-ghost btn-sm flex-1">View</a>
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-indigo btn-sm flex-1">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== AI TRANSACTION PARSER ===== --}}
    <div class="parser-panel">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">AI Transaction Parser</div>
                    <div class="card-subtitle">Type naturally — e.g. "I bought a shirt for 500 taka from bKash"</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="chatForm">
                @csrf
                <div class="parser-input-row">
                    <input type="text" id="chatInput" name="message"
                        class="parser-input"
                        placeholder="Describe your transaction in plain text..."
                        autocomplete="off">
                    <button type="submit" id="parseBtn" class="btn btn-primary" style="padding:14px 24px;">
                        <span id="parseBtnText" class="flex items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            Parse
                        </span>
                        <span id="parseBtnLoading" class="flex items-center gap-2 hidden">
                            <span class="spinner"></span> Parsing...
                        </span>
                    </button>
                </div>
                <div style="margin-top:8px; font-size:12px; color:var(--text-muted);">
                    Tip: Use ↑/↓ arrow keys to recall previous messages. Press Escape to clear.
                </div>
            </form>

            {{-- Parse Result --}}
            <div id="parseResult" class="hidden">
                <div class="parser-result">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                        <span class="badge badge-indigo">Detected Transaction</span>
                        <span id="resultBadgeTransfer" class="badge badge-info hidden">Transfer</span>
                    </div>

                    {{-- Normal Transaction --}}
                    <div id="normalTransactionDetails">
                        <div class="parser-result-row">
                            <span class="parser-result-key">Type</span>
                            <span id="resultType" class="badge"></span>
                        </div>
                        <div class="parser-result-row">
                            <span class="parser-result-key">Amount</span>
                            <span class="parser-amount-display">৳<span id="resultAmount"></span></span>
                        </div>
                        <div class="parser-result-row">
                            <span class="parser-result-key">Account</span>
                            <span id="resultAccount" class="parser-result-val"></span>
                        </div>
                        <div class="parser-result-row">
                            <span class="parser-result-key">Note</span>
                            <span id="resultNote" class="parser-result-val" style="color:var(--text-secondary); font-style:italic;"></span>
                        </div>
                    </div>

                    {{-- Transfer --}}
                    <div id="transferTransactionDetails" class="hidden">
                        <div class="parser-result-row">
                            <span class="parser-result-key">Amount</span>
                            <span class="parser-amount-display">৳<span id="transferAmount"></span></span>
                        </div>
                        <div class="parser-result-row">
                            <span class="parser-result-key">From</span>
                            <span id="transferFromAccount" class="parser-result-val"></span>
                        </div>
                        <div class="parser-result-row">
                            <span class="parser-result-key">To</span>
                            <span id="transferToAccount" class="parser-result-val"></span>
                        </div>
                        <div class="parser-result-row">
                            <span class="parser-result-key">Note</span>
                            <span id="transferNote" class="parser-result-val" style="color:var(--text-secondary); font-style:italic;"></span>
                        </div>
                    </div>

                    <form id="confirmForm" method="POST" action="{{ route('transactions.store') }}" style="margin-top:20px;">
                        @csrf
                        <input type="hidden" name="account_id" id="confirmAccountId">
                        <input type="hidden" name="type"       id="confirmType">
                        <input type="hidden" name="amount"     id="confirmAmount">
                        <input type="hidden" name="note"       id="confirmNote">
                        <input type="hidden" name="from_account_id" id="confirmFromAccountId">
                        <input type="hidden" name="to_account_id"   id="confirmToAccountId">

                        <div class="flex gap-3">
                            <button type="submit" class="btn btn-success flex-1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Confirm &amp; Save
                            </button>
                            <button type="button" id="cancelBtn" class="btn btn-ghost">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Error Display --}}
            <div id="parseError" class="hidden" style="margin-top:16px;">
                <div class="alert alert-danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div class="alert-text">
                        <div class="alert-title">Could not parse transaction</div>
                        <ul id="errorList" style="margin-top:6px; padding-left:16px; font-size:13px;"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== RECENT TRANSACTIONS ===== --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                        <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
                        <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Recent Transactions</div>
                    <div class="card-subtitle">Your latest 8 financial activities</div>
                </div>
            </div>
            <a href="{{ route('transactions.index') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Note</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $transaction)
                        <tr>
                            <td class="td-muted">
                                <div>{{ $transaction->created_at->format('M d, Y') }}</div>
                                <div style="font-size:11px;">{{ $transaction->created_at->format('H:i') }}</div>
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
                            <td><span style="font-weight:600;">{{ $transaction->account->name }}</span></td>
                            <td>
                                <span style="font-weight:700; color:{{ $transaction->type === 'income' ? 'var(--success)' : 'var(--danger)' }};">
                                    {{ $transaction->type === 'income' ? '+' : '−' }}৳{{ number_format($transaction->amount, 2) }}
                                </span>
                            </td>
                            <td class="td-muted" style="max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $transaction->note ?? '—' }}
                            </td>
                            <td style="text-align:center;">
                                <form method="POST"
                                    action="{{ route('transactions.destroy', $transaction) }}"
                                    class="inline-form"
                                    data-confirm-delete
                                    data-confirm-title="Delete this transaction?"
                                    data-confirm-desc="The account balance of {{ $transaction->account->name }} will be restored by ৳{{ number_format($transaction->amount, 2) }}.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon" title="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                    </div>
                                    <div class="empty-state-title">No transactions yet</div>
                                    <div class="empty-state-desc">Use the AI parser above to record your first transaction.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- end .space-y --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
/* ---- Monthly Chart ---- */
const monthlyData = @json($monthlyData);
const labels   = monthlyData.map(d => d.month);
const incomes  = monthlyData.map(d => d.income);
const expenses = monthlyData.map(d => d.expense);

const chartDefaults = {
    borderWidth: 0,
    borderRadius: 6,
    borderSkipped: false,
};

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Income',  data: incomes,  backgroundColor: 'rgba(16,185,129,0.75)', ...chartDefaults },
            { label: 'Expense', data: expenses, backgroundColor: 'rgba(244,63,94,0.7)',   ...chartDefaults },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: '#475569', font: { size: 12, family: 'Inter' } } },
            tooltip: {
                callbacks: {
                    label: ctx => ' ৳' + ctx.raw.toLocaleString('en-US', { minimumFractionDigits: 2 })
                }
            }
        },
        scales: {
            x: { grid: { color: '#e2e8f0' }, ticks: { color: '#64748b', font: { size: 11 } } },
            y: {
                grid: { color: '#e2e8f0' },
                ticks: { color: '#64748b', font: { size: 11 }, callback: v => '৳' + v.toLocaleString() }
            }
        }
    }
});

/* ---- Donut Chart ---- */
const mIncome  = {{ $monthIncome }};
const mExpense = {{ $monthExpense }};

new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: ['Income', 'Expense'],
        datasets: [{
            data: [mIncome || 0.01, mExpense || 0.01],
            backgroundColor: ['rgba(16,185,129,0.8)', 'rgba(244,63,94,0.8)'],
            borderColor:     ['#10b981', '#f43f5e'],
            borderWidth: 1,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ' ৳' + ctx.raw.toLocaleString('en-US', { minimumFractionDigits: 2 }) } }
        }
    }
});

/* ---- AI Parser ---- */
const chatForm    = document.getElementById('chatForm');
const chatInput   = document.getElementById('chatInput');
const parseResult = document.getElementById('parseResult');
const parseError  = document.getElementById('parseError');
const cancelBtn   = document.getElementById('cancelBtn');
const confirmForm = document.getElementById('confirmForm');
const parseBtn    = document.getElementById('parseBtn');
const parseBtnText    = document.getElementById('parseBtnText');
const parseBtnLoading = document.getElementById('parseBtnLoading');

// Input history
let history = JSON.parse(localStorage.getItem('inex_history') || '[]');
let historyIdx = -1;

chatInput.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (historyIdx < history.length - 1) {
            historyIdx++;
            chatInput.value = history[historyIdx];
        }
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (historyIdx > 0) { historyIdx--; chatInput.value = history[historyIdx]; }
        else { historyIdx = -1; chatInput.value = ''; }
    } else if (e.key === 'Escape') {
        chatInput.value = '';
        historyIdx = -1;
        parseResult.classList.add('hidden');
        parseError.classList.add('hidden');
    }
});

function setLoading(loading) {
    parseBtn.disabled = loading;
    parseBtnText.classList.toggle('hidden', loading);
    parseBtnLoading.classList.toggle('hidden', !loading);
}

chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const message = chatInput.value.trim();
    if (!message) return;

    setLoading(true);
    parseResult.classList.add('hidden');
    parseError.classList.add('hidden');

    try {
        const res  = await fetch('{{ route('chat.parse') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message })
        });
        const data = await res.json();

        if (data.success) {
            // Save to history
            history.unshift(message);
            history = history.slice(0, 10);
            localStorage.setItem('inex_history', JSON.stringify(history));
            historyIdx = -1;

            if (data.data.type === 'transfer') {
                document.getElementById('transferAmount').textContent      = Number(data.data.amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
                document.getElementById('transferFromAccount').textContent = data.data.from_account;
                document.getElementById('transferToAccount').textContent   = data.data.to_account;
                document.getElementById('transferNote').textContent        = data.data.note;
                document.getElementById('confirmFromAccountId').value      = data.data.from_account_id;
                document.getElementById('confirmToAccountId').value        = data.data.to_account_id;
                document.getElementById('confirmAmount').value             = data.data.amount;
                document.getElementById('confirmNote').value               = data.data.note;
                confirmForm.action = '{{ route('chat.transfer') }}';
                document.getElementById('transferTransactionDetails').classList.remove('hidden');
                document.getElementById('normalTransactionDetails').classList.add('hidden');
                document.getElementById('resultBadgeTransfer').classList.remove('hidden');
            } else {
                const typeEl = document.getElementById('resultType');
                typeEl.textContent = data.data.type;
                typeEl.className   = 'badge ' + (data.data.type === 'income' ? 'badge-success' : 'badge-danger');
                document.getElementById('resultAmount').textContent  = Number(data.data.amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
                document.getElementById('resultAccount').textContent = data.data.account;
                document.getElementById('resultNote').textContent    = data.data.note;
                document.getElementById('confirmAccountId').value    = data.data.account_id;
                document.getElementById('confirmType').value         = data.data.type;
                document.getElementById('confirmAmount').value       = data.data.amount;
                document.getElementById('confirmNote').value         = data.data.note;
                confirmForm.action = '{{ route('transactions.store') }}';
                document.getElementById('normalTransactionDetails').classList.remove('hidden');
                document.getElementById('transferTransactionDetails').classList.add('hidden');
                document.getElementById('resultBadgeTransfer').classList.add('hidden');
            }
            parseResult.classList.remove('hidden');
        } else {
            const errorList = document.getElementById('errorList');
            errorList.innerHTML = '';
            data.errors.forEach(err => {
                const li = document.createElement('li');
                li.textContent = err;
                errorList.appendChild(li);
            });
            parseError.classList.remove('hidden');
        }
    } catch (err) {
        Toast.show('error', 'Network error. Please try again.', 'Request Failed');
        console.error(err);
    } finally {
        setLoading(false);
    }
});

cancelBtn.addEventListener('click', () => {
    parseResult.classList.add('hidden');
    parseError.classList.add('hidden');
    chatInput.value = '';
    historyIdx = -1;
});
</script>
@endpush