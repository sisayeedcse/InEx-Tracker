@extends('layouts.app')

@section('title', 'Dashboard - InEx Tracker')

@section('content')
    <div class="space-y-6">
        <!-- Balance Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-500 text-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium opacity-90">Total Balance</h3>
                <p class="text-3xl font-bold mt-2">৳{{ number_format($totalBalance, 2) }}</p>
            </div>
            <div class="bg-green-500 text-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium opacity-90">Total Income</h3>
                <p class="text-3xl font-bold mt-2">৳{{ number_format($totalIncome, 2) }}</p>
            </div>
            <div class="bg-red-500 text-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium opacity-90">Total Expense</h3>
                <p class="text-3xl font-bold mt-2">৳{{ number_format($totalExpense, 2) }}</p>
            </div>
        </div>

        <!-- Account Balances -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Account Balances</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($accounts as $account)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-700">{{ $account->name }}</h4>
                            <p class="text-2xl font-bold text-gray-900 mt-1">৳{{ number_format($account->balance, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chat Input Section -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Add Transaction</h2>
                <p class="text-sm text-gray-500 mt-1">Type naturally, e.g., "I bought a shirt for 500 taka from bKash"</p>
            </div>
            <div class="p-6">
                <form id="chatForm">
                    @csrf
                    <div class="flex gap-2">
                        <input type="text" id="chatInput" name="message"
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter your transaction..." autocomplete="off">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                            Send
                        </button>
                    </div>
                </form>

                <!-- Parse Result Display -->
                <div id="parseResult" class="mt-4 hidden">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <h3 class="font-medium text-gray-700 mb-3">Detected Transaction:</h3>
                        <div class="space-y-2 text-sm">
                            <div><span class="font-medium">Type:</span> <span id="resultType" class="capitalize"></span>
                            </div>
                            <div><span class="font-medium">Amount:</span> ৳<span id="resultAmount"></span></div>
                            <div><span class="font-medium">Account:</span> <span id="resultAccount"></span></div>
                            <div><span class="font-medium">Note:</span> <span id="resultNote"></span></div>
                        </div>
                        <form id="confirmForm" method="POST" action="{{ route('transactions.store') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="account_id" id="confirmAccountId">
                            <input type="hidden" name="type" id="confirmType">
                            <input type="hidden" name="amount" id="confirmAmount">
                            <input type="hidden" name="note" id="confirmNote">
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded font-medium">
                                    Confirm & Save
                                </button>
                                <button type="button" id="cancelBtn"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded font-medium">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Error Display -->
                <div id="parseError" class="mt-4 hidden">
                    <div class="border border-red-300 bg-red-50 rounded-lg p-4">
                        <h4 class="font-medium text-red-800 mb-2">Could not process:</h4>
                        <ul id="errorList" class="list-disc list-inside text-sm text-red-700"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Recent Transactions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentTransactions as $transaction)
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
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->account->name }}</td>
                                <td
                                    class="px-6 py-4 text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}৳{{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->note }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No transactions yet. Start adding some!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const chatForm = document.getElementById('chatForm');
            const chatInput = document.getElementById('chatInput');
            const parseResult = document.getElementById('parseResult');
            const parseError = document.getElementById('parseError');
            const cancelBtn = document.getElementById('cancelBtn');

            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const message = chatInput.value.trim();
                if (!message) return;

                try {
                    const response = await fetch('{{ route('chat.parse') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ message })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Show parsed result
                        document.getElementById('resultType').textContent = data.data.type;
                        document.getElementById('resultAmount').textContent = data.data.amount;
                        document.getElementById('resultAccount').textContent = data.data.account;
                        document.getElementById('resultNote').textContent = data.data.note;

                        // Set hidden form values
                        document.getElementById('confirmAccountId').value = data.data.account_id;
                        document.getElementById('confirmType').value = data.data.type;
                        document.getElementById('confirmAmount').value = data.data.amount;
                        document.getElementById('confirmNote').value = data.data.note;

                        parseResult.classList.remove('hidden');
                        parseError.classList.add('hidden');
                    } else {
                        // Show errors
                        const errorList = document.getElementById('errorList');
                        errorList.innerHTML = '';
                        data.errors.forEach(error => {
                            const li = document.createElement('li');
                            li.textContent = error;
                            errorList.appendChild(li);
                        });

                        parseError.classList.remove('hidden');
                        parseResult.classList.add('hidden');
                    }
                } catch (error) {
                    alert('An error occurred while processing your request.');
                    console.error(error);
                }
            });

            cancelBtn.addEventListener('click', () => {
                parseResult.classList.add('hidden');
                parseError.classList.add('hidden');
                chatInput.value = '';
            });
        </script>
    @endpush
@endsection