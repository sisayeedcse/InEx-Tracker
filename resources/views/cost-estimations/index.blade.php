@extends('layouts.app')

@section('title', 'Cost Estimation — InEx Tracker')
@section('page-title', 'Cost Estimation')

@section('content')
<div class="space-y">

    {{-- KPI Cards --}}
    <div class="grid-3">
        <div class="stat-card">
            <div class="stat-card-accent" style="background:var(--indigo);"></div>
            <div class="stat-card-label">Current Balance</div>
            <div class="stat-card-value indigo">৳{{ number_format($totalCurrentBalance, 2) }}</div>
            <div class="stat-card-meta">Actual available funds</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-accent" style="background:var(--danger);"></div>
            <div class="stat-card-label">Planned Costs</div>
            <div class="stat-card-value danger">৳{{ number_format($totalEstimatedCosts, 2) }}</div>
            <div class="stat-card-meta">{{ $costEstimations->count() }} planned expense(s)</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-accent" style="background:{{ $projectedBalance >= 0 ? 'var(--success)' : 'var(--danger)' }};"></div>
            <div class="stat-card-label">Projected Balance</div>
            <div class="stat-card-value {{ $projectedBalance >= 0 ? 'success' : 'danger' }}">
                ৳{{ number_format($projectedBalance, 2) }}
            </div>
            <div class="stat-card-meta">After all planned costs</div>
        </div>
    </div>

    {{-- Visual Balance Bar --}}
    @if($totalCurrentBalance > 0)
        <div class="card">
            <div class="card-body" style="padding:20px 24px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <span style="font-size:12px; font-weight:600; color:var(--text-secondary);">Balance consumed by planned costs</span>
                    <span style="font-size:12px; font-weight:600; color:{{ $projectedBalance >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                        {{ number_format(min(100, ($totalEstimatedCosts / $totalCurrentBalance) * 100), 1) }}%
                    </span>
                </div>
                <div class="progress" style="height:10px;">
                    <div class="progress-bar {{ $projectedBalance >= 0 ? 'success' : 'danger' }}"
                         style="width:{{ number_format(min(100, ($totalEstimatedCosts / $totalCurrentBalance) * 100), 1) }}%;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:8px; font-size:11px; color:var(--text-muted);">
                    <span>৳0</span>
                    <span>৳{{ number_format($totalCurrentBalance, 2) }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Add Cost Form --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Add Planned Expense</div>
                    <div class="card-subtitle">e.g. "I will buy a trimmer for 2000 taka from main"</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('cost-estimations.parse') }}" method="POST">
                @csrf
                <div class="parser-input-row">
                    <input type="text" name="message" required
                        class="parser-input"
                        placeholder="Describe your future expense in plain text..."
                        autocomplete="off">
                    <button type="submit" class="btn btn-primary" style="padding:14px 24px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add
                    </button>
                </div>
            </form>
            <div class="alert alert-info" style="margin-top:16px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div class="alert-text">
                    <strong>Estimation only</strong> — No actual transactions are created. This helps you plan ahead and visualize your future balance.
                </div>
            </div>
        </div>
    </div>

    {{-- Planned Expenses List --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Planned Expenses</div>
                    <div class="card-subtitle">{{ $costEstimations->count() }} item(s) — these are estimates only</div>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Account</th>
                        <th>Estimated Cost</th>
                        <th>Description</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($costEstimations as $estimation)
                        <tr>
                            <td>
                                <div style="font-weight:500;">{{ $estimation->created_at->format('M d, Y') }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $estimation->created_at->format('H:i') }}</div>
                            </td>
                            <td style="font-weight:600;">{{ $estimation->account->name }}</td>
                            <td>
                                <span style="font-weight:700; color:var(--danger);">
                                    −৳{{ number_format($estimation->amount, 2) }}
                                </span>
                            </td>
                            <td class="td-muted" style="max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $estimation->description }}">
                                {{ $estimation->description }}
                            </td>
                            <td style="text-align:center;">
                                <form method="POST"
                                    action="{{ route('cost-estimations.destroy', $estimation) }}"
                                    class="inline-form"
                                    data-confirm-delete
                                    data-confirm-title="Remove this estimation?"
                                    data-confirm-desc="The estimation of ৳{{ number_format($estimation->amount, 2) }} will be removed.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon" title="Remove">
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
                                            <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                                            <line x1="6" y1="20" x2="6" y2="14"/>
                                        </svg>
                                    </div>
                                    <div class="empty-state-title">No planned expenses yet</div>
                                    <div class="empty-state-desc">Use the form above to plan your future costs.</div>
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