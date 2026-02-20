@extends('layouts.app')

@section('title', 'Create Account - InEx Tracker')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Create New Account</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('accounts.store') }}" id="createAccountForm">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="e.g., bKash, Cash, Bank, Payoneer" required oninput="toggleCurrencyInput()">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="usd_input_section" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Initial Balance</label>
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label for="balance_usd" class="block text-xs text-gray-600 mb-1">💵 Amount in USD</label>
                            <input type="number" id="balance_usd" name="balance_usd" step="0.01" min="0"
                                value="{{ old('balance', 0) }}"
                                class="w-full border-2 border-green-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 font-bold text-lg"
                                oninput="updateBdtPreviewCreate()">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-gray-600 mb-1">৳ Equivalent in BDT</label>
                            <input type="text" id="bdt_preview_create"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 text-gray-600 font-semibold text-lg"
                                value="0.00" readonly>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        Exchange Rate: 1 USD = {{ number_format($usdToBdtRate, 2) }} BDT
                    </p>
                </div>

                <div id="bdt_input_section" class="mb-6">
                    <label for="balance" class="block text-sm font-medium text-gray-700 mb-2">Initial Balance (৳)</label>
                    <input type="number" id="balance" name="balance_bdt" step="0.01" min="0" value="{{ old('balance', 0) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('balance') border-red-500 @enderror"
                        required>
                    @error('balance')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <input type="hidden" id="balance_input" name="balance" value="0">
                <input type="hidden" id="currency_input" name="currency" value="bdt">

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                        Create Account
                    </button>
                    <a href="{{ route('accounts.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const exchangeRate = {{ $usdToBdtRate }};

        function toggleCurrencyInput() {
            const accountName = document.getElementById('name').value.toLowerCase().trim();
            const usdSection = document.getElementById('usd_input_section');
            const bdtSection = document.getElementById('bdt_input_section');

            if (accountName === 'payoneer') {
                usdSection.classList.remove('hidden');
                bdtSection.classList.add('hidden');
                document.getElementById('currency_input').value = 'usd';
            } else {
                usdSection.classList.add('hidden');
                bdtSection.classList.remove('hidden');
                document.getElementById('currency_input').value = 'bdt';
            }
        }

        function updateBdtPreviewCreate() {
            const usdAmount = parseFloat(document.getElementById('balance_usd').value) || 0;
            const bdtAmount = usdAmount * exchangeRate;
            document.getElementById('bdt_preview_create').value = bdtAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        // Before form submission, set the correct balance value
        document.getElementById('createAccountForm').addEventListener('submit', function (e) {
            const accountName = document.getElementById('name').value.toLowerCase().trim();

            if (accountName === 'payoneer') {
                const usdAmount = parseFloat(document.getElementById('balance_usd').value) || 0;
                document.getElementById('balance_input').value = usdAmount;
            } else {
                const bdtAmount = parseFloat(document.getElementById('balance').value) || 0;
                document.getElementById('balance_input').value = bdtAmount;
            }
        });
    </script>
@endsection