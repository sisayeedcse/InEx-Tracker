@extends('layouts.app')

@section('title', $account->name . ' - InEx Tracker')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">{{ $account->name }}</h1>
            <a href="{{ route('accounts.index') }}" class="text-blue-500 hover:text-blue-600">
                ← Back to Accounts
            </a>
        </div>

        <!-- Account Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Current Balance</p>
                    @if(strtolower($account->name) === 'payoneer')
                        <p class="text-4xl font-bold text-gray-900">💵
                            ${{ number_format($account->balance / $usdToBdtRate, 2) }}</p>
                        <p class="text-sm text-gray-500 mt-2 font-medium">
                            ≈ ৳{{ number_format($account->balance, 2) }} BDT
                        </p>
                    @else
                        <p class="text-4xl font-bold text-gray-900">৳{{ number_format($account->balance, 2) }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Transactions</p>
                    <p class="text-4xl font-bold text-gray-900">{{ $account->transactions->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Transactions for this Account -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Transaction History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($account->transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($transaction->type === 'income')
                                        <span
                                            class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Income</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Expense</span>
                                    @endif
                                </td>
                                <td
                                    class="px-6 py-4 text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}৳{{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->note }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('transactions.destroy', $transaction) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this transaction? The account balance will be restored.');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors duration-200"
                                            title="Delete Transaction">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No transactions yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection