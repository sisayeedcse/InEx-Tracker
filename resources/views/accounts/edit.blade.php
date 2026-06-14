@extends('layouts.app')

@section('title', 'Edit {{ $account->name }} — InEx Tracker')
@section('page-title', 'Edit Account')

@section('content')
<div style="max-width:600px; margin:0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Edit — {{ $account->name }}</div>
                    <div class="card-subtitle">Modify account details</div>
                </div>
            </div>
            <a href="{{ route('accounts.index') }}" class="btn btn-ghost btn-sm">← Back</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom:20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div class="alert-text">
                        <div class="alert-title">Please fix the errors below</div>
                        <ul style="margin-top:6px; padding-left:16px;">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('accounts.update', $account) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="name">Account Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="{{ old('name', $account->name) }}"
                        placeholder="Account name" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="balance">Balance</label>
                        <div class="input-group">
                            <span class="input-prefix">৳</span>
                            <input type="number" step="0.01" min="0" name="balance" id="balance" class="form-control"
                                value="{{ old('balance', $account->balance) }}"
                                placeholder="0.00" required>
                        </div>
                        <div class="form-hint">Editing balance directly will desync the Main account — prefer using transactions.</div>
                    </div>

                    @if($account->isUsdAccount())
                        <div class="form-group">
                            <label class="form-label" for="currency">Currency</label>
                            <select name="currency" id="currency" class="form-control">
                                <option value="bdt">BDT (as stored)</option>
                                <option value="usd">USD — convert at ৳{{ number_format($usdToBdtRate, 2) }}/USD</option>
                            </select>
                        </div>
                    @endif
                </div>

                <div class="flex gap-3 justify-end" style="margin-top:8px;">
                    <a href="{{ route('accounts.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection