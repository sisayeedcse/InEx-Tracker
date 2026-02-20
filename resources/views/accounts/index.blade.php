@extends('layouts.app')

@section('title', 'Accounts - InEx Tracker')

@section('content')
    <div class="space-y-8 animate-fade-in">
        <!-- Exchange Rate Setting -->
        <div class="glass-effect rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h2
                    class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>💱</span>
                    Currency Exchange Rate
                </h2>
                <p class="text-sm text-gray-600 mt-1">Set conversion rate for USD to BDT (Used for Payoneer transactions)
                </p>
            </div>
            <div class="p-8">
                <form method="POST" action="{{ route('settings.exchange-rate') }}" class="flex items-end gap-4">
                    @csrf
                    <div class="flex-1">
                        <label for="usd_to_bdt_rate" class="block text-sm font-semibold text-gray-700 mb-2">
                            1 USD = ? BDT
                        </label>
                        <input type="number" step="0.01" name="usd_to_bdt_rate" id="usd_to_bdt_rate"
                            value="{{ $usdToBdtRate }}"
                            class="w-full border-2 border-gray-300 rounded-xl px-6 py-4 focus:outline-none focus:ring-4 focus:ring-green-200 focus:border-green-400 transition-all duration-300 text-gray-700 text-lg font-bold"
                            placeholder="120.00" required>
                    </div>
                    <button type="submit"
                        class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <span class="flex items-center gap-2">
                            <span>💾</span>
                            Update Rate
                        </span>
                    </button>
                </form>
                <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <span class="font-bold">💡 Tip:</span> When you say "I transferred 20 dollar from Payoneer to Upay",
                        the system will automatically convert $20 to BDT using this rate
                        ({{ number_format($usdToBdtRate, 2) }} BDT/USD).
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Manage
                Accounts</h1>
            <a href="{{ route('accounts.create') }}"
                class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                <span class="flex items-center gap-2">
                    <span>+</span>
                    Add Account
                </span>
            </a>
        </div>

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($accounts as $account)
                <div class="glass-effect rounded-2xl shadow-xl card-hover overflow-hidden">
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-4">
                            <h3
                                class="text-xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                {{ $account->name }}
                            </h3>
                            <span
                                class="text-xs bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-semibold">{{ $account->transactions_count }}
                                transactions</span>
                        </div>

                        <div class="mb-6">
                            <p class="text-sm text-gray-600 mb-2 font-medium uppercase tracking-wide">Current Balance</p>
                            <p
                                class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                ৳{{ number_format($account->balance, 2) }}</p>
                            @if(strtolower($account->name) === 'payoneer')
                                <p class="text-sm text-gray-500 mt-2 font-medium">
                                    💵 ${{ number_format($account->balance / $usdToBdtRate, 2) }} USD
                                </p>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('accounts.show', $account) }}"
                                class="flex-1 text-center bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-300 transform hover:scale-105">
                                👁️ View
                            </a>
                            <a href="{{ route('accounts.edit', $account) }}"
                                class="flex-1 text-center bg-gradient-to-r from-blue-100 to-blue-200 hover:from-blue-200 hover:to-blue-300 text-blue-700 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-300 transform hover:scale-105">
                                ✏️ Edit
                            </a>
                            <form method="POST" action="{{ route('accounts.destroy', $account) }}" class="flex-1"
                                onsubmit="return confirm('Are you sure you want to delete this account?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-red-100 to-red-200 hover:from-red-200 hover:to-red-300 text-red-700 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-300 transform hover:scale-105">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16">
                    <div class="text-8xl mb-4 opacity-20">🏦</div>
                    <p class="text-gray-500 font-medium text-lg">
                        No accounts found. <a href="{{ route('accounts.create') }}"
                            class="text-purple-600 hover:text-purple-700 font-bold underline">Create one</a>
                    </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection