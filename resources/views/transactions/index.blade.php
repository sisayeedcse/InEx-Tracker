@extends('layouts.app')

@section('title', 'Transactions - InEx Tracker')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Transaction History</h1>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Account</label>
                    <select name="account_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Accounts</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Type</label>
                    <select name="type"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $transaction->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $transaction->created_at->format('M d, Y') }}<br>
                                <span class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($transaction->type === 'income')
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Income</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Expense</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->account->name }}</td>
                            <td
                                class="px-6 py-4 text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}৳{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->note ?: 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-4xl mb-2">📊</div>
                                No transactions found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection