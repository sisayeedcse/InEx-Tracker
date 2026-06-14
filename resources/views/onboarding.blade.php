<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup — InEx Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; padding:24px; background:var(--bg-base); }
        .onboard-card { width:100%; max-width:680px; }
        .step-indicator { display:flex; align-items:center; gap:0; margin-bottom:36px; }
        .step-dot {
            width:32px; height:32px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:12px; font-weight:700;
            background:var(--bg-hover); color:var(--text-muted);
            border:2px solid var(--border);
            transition:all var(--transition); flex-shrink:0;
        }
        .step-dot.active { background:var(--indigo); color:#fff; border-color:var(--indigo); }
        .step-dot.done   { background:var(--success); color:#fff; border-color:var(--success); }
        .step-line { flex:1; height:2px; background:var(--border); margin:0 8px; }
        .step-line.done { background:var(--success); }
        .account-row {
            background:var(--bg-tertiary); border:1px solid var(--border-light);
            border-radius:var(--radius); padding:16px; margin-bottom:12px;
            display:grid; grid-template-columns:1fr 1fr auto; gap:12px; align-items:end;
        }
        .suggestions { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
        .suggestion-btn {
            padding:6px 14px; border-radius:99px; font-size:12px; font-weight:600;
            background:var(--bg-tertiary); border:1px solid var(--border-light);
            color:var(--text-secondary); cursor:pointer; transition:all var(--transition);
        }
        .suggestion-btn:hover { border-color:var(--indigo); color:var(--indigo); background:var(--indigo-soft); }
    </style>
</head>
<body>
    <div class="onboard-card">
        <div class="card">
            {{-- Header --}}
            <div class="card-header" style="flex-direction:column; align-items:flex-start; gap:20px; padding:32px 32px 24px;">
                <div style="display:flex; align-items:center; gap:14px; width:100%;">
                    <div style="width:48px; height:48px; border-radius:var(--radius); background:var(--grad-indigo); display:flex; align-items:center; justify-content:center; font-size:24px; box-shadow:var(--indigo-glow);">💰</div>
                    <div>
                        <div style="font-size:22px; font-weight:800; color:var(--text-primary); letter-spacing:-0.5px;">Welcome to InEx Tracker</div>
                        <div style="font-size:13px; color:var(--text-secondary); margin-top:2px;">Set up your accounts to get started</div>
                    </div>
                </div>

                {{-- Step Indicator --}}
                <div class="step-indicator">
                    <div class="step-dot active" id="step1-dot">1</div>
                    <div class="step-line" id="line-12"></div>
                    <div class="step-dot" id="step2-dot">2</div>
                    <div class="step-line" id="line-23"></div>
                    <div class="step-dot" id="step3-dot">✓</div>
                </div>
            </div>

            <div class="card-body" style="padding:24px 32px 32px;">
                @if(session('error'))
                    <div class="alert alert-danger" style="margin-bottom:20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div class="alert-text">{{ session('error') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('onboarding.store') }}" id="onboardingForm">
                    @csrf

                    {{-- Step 1: How many accounts --}}
                    <div id="step1">
                        <div style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Step 1 — How many accounts?</div>
                        <div style="font-size:13px; color:var(--text-secondary); margin-bottom:20px;">
                            Add each account you use (e.g., bKash, Payoneer, bank, cash). A <strong>Main</strong> aggregate account is added automatically.
                        </div>

                        <div class="form-group">
                            <label class="form-label">Number of Accounts (1–10)</label>
                            <input type="number" id="accountCount" min="1" max="10" value="3" class="form-control" style="max-width:200px;">
                        </div>

                        <div class="flex gap-3" style="margin-top:24px;">
                            <button type="button" id="nextToStep2" class="btn btn-primary btn-lg">
                                Next — Configure Accounts
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Account details --}}
                    <div id="step2" style="display:none;">
                        <div style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Step 2 — Configure Accounts</div>
                        <div style="font-size:13px; color:var(--text-secondary); margin-bottom:16px;">
                            Enter the name and opening balance for each account. Click a suggestion to auto-fill.
                        </div>

                        <div class="suggestions" id="suggestions">
                            <button type="button" class="suggestion-btn" data-name="bKash">bKash</button>
                            <button type="button" class="suggestion-btn" data-name="Upay">Upay</button>
                            <button type="button" class="suggestion-btn" data-name="Payoneer">Payoneer</button>
                            <button type="button" class="suggestion-btn" data-name="Nagad">Nagad</button>
                            <button type="button" class="suggestion-btn" data-name="Dutch Bangla">Dutch Bangla</button>
                            <button type="button" class="suggestion-btn" data-name="Cash">Cash</button>
                            <button type="button" class="suggestion-btn" data-name="DBBL">DBBL</button>
                        </div>

                        <div id="accountsContainer"></div>

                        <div class="flex gap-3" style="margin-top:24px;">
                            <button type="button" id="backToStep1" class="btn btn-ghost">← Back</button>
                            <button type="submit" class="btn btn-success btn-lg flex-1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Complete Setup
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

<script>
const step1El   = document.getElementById('step1');
const step2El   = document.getElementById('step2');
const step1Dot  = document.getElementById('step1-dot');
const step2Dot  = document.getElementById('step2-dot');
const line12    = document.getElementById('line-12');
const accountCountInput = document.getElementById('accountCount');
const container = document.getElementById('accountsContainer');

const suggestions = ['bKash', 'Upay', 'Payoneer', 'Nagad', 'Cash'];

function buildAccountFields(count) {
    container.innerHTML = '';
    for (let i = 0; i < count; i++) {
        const row = document.createElement('div');
        row.className = 'account-row';
        row.innerHTML = `
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Account ${i + 1} Name</label>
                <input type="text" name="accounts[${i}][name]" value="${suggestions[i] || ''}"
                    class="form-control" placeholder="e.g. bKash" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Opening Balance (৳)</label>
                <div class="input-group">
                    <span class="input-prefix">৳</span>
                    <input type="number" name="accounts[${i}][balance]" step="0.01" min="0" value="0"
                        class="form-control" placeholder="0.00" required>
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-row" style="margin-bottom:0; align-self:flex-end;">✕</button>
        `;
        row.querySelector('.remove-row').addEventListener('click', () => row.remove());
        container.appendChild(row);
    }
}

document.getElementById('nextToStep2').addEventListener('click', () => {
    const count = Math.min(10, Math.max(1, parseInt(accountCountInput.value) || 1));
    buildAccountFields(count);

    step1El.style.display = 'none';
    step2El.style.display = 'block';
    step1Dot.classList.add('done'); step1Dot.textContent = '✓';
    step2Dot.classList.add('active');
    line12.classList.add('done');
});

document.getElementById('backToStep1').addEventListener('click', () => {
    step2El.style.display = 'none';
    step1El.style.display = 'block';
    step1Dot.classList.remove('done'); step1Dot.textContent = '1';
    step2Dot.classList.remove('active');
    line12.classList.remove('done');
});

// Suggestions: find first empty name field and fill it
document.querySelectorAll('.suggestion-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const rows = container.querySelectorAll('input[type=text]');
        for (const input of rows) {
            if (!input.value.trim()) { input.value = btn.dataset.name; return; }
        }
        // All filled: append a new row
        const inputs = container.querySelectorAll('.account-row');
        const idx = inputs.length;
        const row = document.createElement('div');
        row.className = 'account-row';
        row.innerHTML = `
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Account ${idx + 1} Name</label>
                <input type="text" name="accounts[${idx}][name]" value="${btn.dataset.name}"
                    class="form-control" placeholder="e.g. bKash" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Opening Balance (৳)</label>
                <div class="input-group">
                    <span class="input-prefix">৳</span>
                    <input type="number" name="accounts[${idx}][balance]" step="0.01" min="0" value="0"
                        class="form-control" placeholder="0.00" required>
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-row" style="align-self:flex-end;">✕</button>
        `;
        row.querySelector('.remove-row').addEventListener('click', () => row.remove());
        container.appendChild(row);
    });
});
</script>
</body>
</html>