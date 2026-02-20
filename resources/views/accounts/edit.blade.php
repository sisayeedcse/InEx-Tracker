@extends('layouts.app')

@section('title', 'Edit Account - InEx Tracker')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Account</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('accounts.update', $account) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $account->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if(strtolower($account->name) === 'payoneer')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Balance</label>
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label for="balance_usd" class="block text-xs text-gray-600 mb-1">💵 Amount in USD</label>
                                <input type="number" id="balance_usd" name="balance" step="0.01" min="0"
                                    value="{{ old('balance', number_format($account->balance / $usdToBdtRate, 2, '.', '')) }}"
                                    class="w-full border-2 border-green-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 @error('balance') border-red-500 @enderror font-bold text-lg"
                                    required oninput="updateBdtPreview()">
                                <input type="hidden" name="currency" value="usd">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs text-gray-600 mb-1">৳ Equivalent in BDT</label>
                                <input type="text" id="bdt_preview"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 text-gray-600 font-semibold text-lg"
                                    value="{{ number_format($account->balance, 2) }}" readonly>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            Exchange Rate: 1 USD = {{ number_format($usdToBdtRate, 2) }} BDT
                        </p>
                        @error('balance')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div class="mb-6">
                        <label for="balance" class="block text-sm font-medium text-gray-700 mb-2">Current Balance (৳)</label>
                        <input type="number" id="balance" name="balance" step="0.01" min="0"
                            value="{{ old('balance', $account->balance) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('balance') border-red-500 @enderror"
                            required>
                        <input type="hidden" name="currency" value="bdt">
                        @error('balance')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                        Update Account
                    </button>
                    <a href="{{ route('accounts.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(strtolower($account->name) === 'payoneer')
        <script>
            function updateBdtPreview() {
                const usdAmount = parseFloat(document.getElementById('balance_usd').value) || 0;
                const exchangeRate = {{ $usdToBdtRate }};
                const bdtAmount = usdAmount * exchangeRate;
                document.getElementById('bdt_preview').value = bdtAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
        </script>
    @endif
@endsection