@extends('layouts.app')

@section('title', 'Create Account — InEx Tracker')
@section('page-title', 'New Account')

@section('content')
<div style="max-width:600px; margin:0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Create Account</div>
                    <div class="card-subtitle">Add a new financial account to track</div>
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
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('accounts.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Account Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="{{ old('name') }}"
                        placeholder="e.g. bKash, Payoneer, OneBank..."
                        required autofocus>
                    <div class="form-hint">Use a unique, recognizable name for this account.</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="balance">Opening Balance</label>
                        <div class="input-group">
                            <span class="input-prefix">৳</span>
                            <input type="number" step="0.01" min="0" name="balance" id="balance" class="form-control"
                                value="{{ old('balance', 0) }}"
                                placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="form-group" id="currencyGroup" style="display:none;">
                        <label class="form-label" for="currency">Currency (Payoneer only)</label>
                        <select name="currency" id="currency" class="form-control">
                            <option value="bdt">BDT (Bangladeshi Taka)</option>
                            <option value="usd">USD — auto-convert at ৳{{ number_format($usdToBdtRate, 2) }}/USD</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 justify-end" style="margin-top:8px;">
                    <a href="{{ route('accounts.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const nameInput = document.getElementById('name');
    const currencyGroup = document.getElementById('currencyGroup');
    nameInput.addEventListener('input', () => {
        currencyGroup.style.display = nameInput.value.toLowerCase() === 'payoneer' ? 'block' : 'none';
    });
</script>
@endpush