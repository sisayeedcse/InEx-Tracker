@extends('layouts.app')

@section('title', 'Dashboard - InEx Tracker')

@section('content')
    <div class="space-y-8 animate-fade-in">
        <!-- Balance Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="gradient-blue text-white rounded-2xl shadow-xl p-8 card-hover relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 text-white opacity-10 text-9xl">💰</div>
                <div class="relative z-10">
                    <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Total Balance</h3>
                    <p class="text-4xl font-bold mt-3">৳{{ number_format($totalBalance, 2) }}</p>
                    <div class="mt-4 pt-4 border-t border-white border-opacity-30">
                        <p class="text-xs opacity-75">Your current net worth</p>
                    </div>
                </div>
            </div>
            <div class="gradient-green text-white rounded-2xl shadow-xl p-8 card-hover relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 text-white opacity-10 text-9xl">📈</div>
                <div class="relative z-10">
                    <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Total Income</h3>
                    <p class="text-4xl font-bold mt-3">৳{{ number_format($totalIncome, 2) }}</p>
                    <div class="mt-4 pt-4 border-t border-white border-opacity-30">
                        <p class="text-xs opacity-75">Money you've earned</p>
                    </div>
                </div>
            </div>
            <div class="gradient-red text-white rounded-2xl shadow-xl p-8 card-hover relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 text-white opacity-10 text-9xl">📉</div>
                <div class="relative z-10">
                    <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Total Expense</h3>
                    <p class="text-4xl font-bold mt-3">৳{{ number_format($totalExpense, 2) }}</p>
                    <div class="mt-4 pt-4 border-t border-white border-opacity-30">
                        <p class="text-xs opacity-75">Money you've spent</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Balances -->
        <div class="glass-effect rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>💳</span>
                    Account Balances
                </h2>
                <p class="text-sm text-gray-600 mt-1">Track all your accounts in one place</p>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($accounts as $account)
                        <div class="group bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 rounded-xl p-6 card-hover hover:border-purple-300 transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-gray-800 text-lg">{{ $account->name }}</h4>
                                <span class="text-2xl group-hover:scale-110 transition-transform duration-300">💰</span>
                            </div>
                            <p class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                ৳{{ number_format($account->balance, 2) }}
                            </p>
                            @if(strtolower($account->name) === 'payoneer')
                                <p class="text-sm text-gray-500 mt-2 font-medium">
                                    💵 ${{ number_format($account->balance / $usdToBdtRate, 2) }} USD
                                </p>
                            @endif
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Available Balance</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chat Input Section -->
        <div class="glass-effect rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>🤖</span>
                    AI Transaction Parser
                </h2>
                <p class="text-sm text-gray-600 mt-1">Type naturally, e.g., "I bought a shirt for 500 taka from bKash"</p>
            </div>
            <div class="p-8">
                <form id="chatForm">
                    @csrf
                    <div class="flex gap-3">
                        <div class="flex-1 relative group">
                            <input type="text" id="chatInput" name="message"
                                class="w-full border-2 border-gray-300 rounded-xl px-6 py-4 focus:outline-none focus:ring-4 focus:ring-purple-200 focus:border-purple-400 transition-all duration-300 text-gray-700 placeholder-gray-400"
                                placeholder="💬 Enter your transaction..." autocomplete="off">
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-400 to-pink-400 opacity-0 group-focus-within:opacity-20 transition-opacity duration-300 pointer-events-none"></div>
                        </div>
                        <button type="submit"
                            class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            <span class="flex items-center gap-2">
                                <span>✨</span>
                                Send
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Parse Result Display -->
                <div id="parseResult" class="mt-6 hidden animate-slide-up">
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl p-6 shadow-lg">
                        <h3 class="font-bold text-purple-900 mb-4 flex items-center gap-2">
                            <span class="text-2xl">✓</span>
                            <span>Detected Transaction</span>
                        </h3>
                        <div id="normalTransactionDetails" class="space-y-3 text-sm">
                            <div class="flex items-center gap-3 bg-white bg-opacity-60 rounded-lg p-3">
                                <span class="font-semibold text-gray-700 w-20">Type:</span> 
                                <span id="resultType" class="capitalize px-3 py-1 bg-purple-100 text-purple-800 rounded-full font-medium"></span>
                            </div>
                            <div class="flex items-center gap-3 bg-white bg-opacity-60 rounded-lg p-3">
                                <span class="font-semibold text-gray-700 w-20">Amount:</span> 
                                <span class="font-bold text-2xl text-purple-600">৳<span id="resultAmount"></span></span>
                            </div>
                            <div class="flex items-center gap-3 bg-white bg-opacity-60 rounded-lg p-3">
                                <span class="font-semibold text-gray-700 w-20">Account:</span> 
                                <span id="resultAccount" class="font-medium text-gray-800"></span>
                            </div>
                            <div class="bg-white bg-opacity-60 rounded-lg p-3">
                                <span class="font-semibold text-gray-700 block mb-2">Note:</span> 
                                <span id="resultNote" class="text-gray-600 italic"></span>
                            </div>
                        </div>
                        <div id="transferTransactionDetails" class="space-y-3 text-sm hidden">
                            <div class="flex items-center gap-3 bg-white bg-opacity-60 rounded-lg p-3">
                                <span class="font-semibold text-gray-700 w-20">Type:</span> 
                                <span class="capitalize px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-medium">Transfer</span>
                            </div>
                            <div class="flex items-center gap-3 bg-white bg-opacity-60 rounded-lg p-3">
                                <span class="font-semibold text-gray-700 w-20">Amount:</span> 
                                <span class="font-bold text-2xl text-blue-600">৳<span id="transferAmount"></span></span>
                            </div>
                            <div class="flex items-center gap-3 bg-white bg-opacity-60 rounded-lg p-3">
                                <span class="font-semibold text-gray-700 w-20">From:</span> 
                                <span id="transferFromAccount" class="font-medium text-gray-800"></span>
                            </div>
                            <div class="flex items-center gap-3 bg-white bg-opacity-60 rounded-lg p-3">
                                <span class="font-semibold text-gray-700 w-20">To:</span> 
                                <span id="transferToAccount" class="font-medium text-gray-800"></span>
                            </div>
                            <div class="bg-white bg-opacity-60 rounded-lg p-3">
                                <span class="font-semibold text-gray-700 block mb-2">Note:</span> 
                                <span id="transferNote" class="text-gray-600 italic"></span>
                            </div>
                        </div>
                        <form id="confirmForm" method="POST" action="{{ route('transactions.store') }}" class="mt-6">
                            @csrf
                            <input type="hidden" name="account_id" id="confirmAccountId">
                            <input type="hidden" name="type" id="confirmType">
                            <input type="hidden" name="amount" id="confirmAmount">
                            <input type="hidden" name="note" id="confirmNote">
                            <!-- Transfer specific fields -->
                            <input type="hidden" name="from_account_id" id="confirmFromAccountId">
                            <input type="hidden" name="to_account_id" id="confirmToAccountId">
                            <div class="flex gap-3">
                                <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                    ✓ Confirm & Save
                                </button>
                                <button type="button" id="cancelBtn"
                                    class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-bold transition-all duration-300">
                                    ✕ Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Error Display -->
                <div id="parseError" class="mt-6 hidden animate-slide-up">
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 border-2 border-red-300 rounded-xl p-6 shadow-lg">
                        <h4 class="font-bold text-red-800 mb-3 flex items-center gap-2">
                            <span class="text-2xl">⚠</span>
                            <span>Could not process</span>
                        </h4>
                        <ul id="errorList" class="list-disc list-inside text-sm text-red-700 space-y-1 bg-white bg-opacity-60 rounded-lg p-4"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="glass-effect rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>📊</span>
                    Recent Transactions
                </h2>
                <p class="text-sm text-gray-600 mt-1">Your latest financial activities</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Account</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTransactions as $transaction)
                            <tr class="hover:bg-purple-50 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($transaction->type === 'income')
                                        <span class="px-3 py-1.5 text-xs font-bold bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 rounded-full shadow-sm border border-green-200">
                                            📈 Income
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 text-xs font-bold bg-gradient-to-r from-red-100 to-pink-100 text-red-800 rounded-full shadow-sm border border-red-200">
                                            📉 Expense
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $transaction->account->name }}</td>
                                <td class="px-6 py-4 text-sm font-bold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}৳{{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->note }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="text-6xl mb-4 opacity-20">📝</span>
                                        <p class="text-gray-500 font-medium">No transactions yet. Start adding some!</p>
                                    </div>
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
            const confirmForm = document.getElementById('confirmForm');

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
                        if (data.data.type === 'transfer') {
                            // Show transfer result
                            document.getElementById('transferAmount').textContent = data.data.amount;
                            document.getElementById('transferFromAccount').textContent = data.data.from_account;
                            document.getElementById('transferToAccount').textContent = data.data.to_account;
                            document.getElementById('transferNote').textContent = data.data.note;

                            // Set hidden form values
                            document.getElementById('confirmFromAccountId').value = data.data.from_account_id;
                            document.getElementById('confirmToAccountId').value = data.data.to_account_id;
                            document.getElementById('confirmAmount').value = data.data.amount;
                            document.getElementById('confirmNote').value = data.data.note;

                            // Update form action for transfer
                            confirmForm.action = '{{ route('chat.transfer') }}';

                            // Show transfer details, hide normal details
                            document.getElementById('transferTransactionDetails').classList.remove('hidden');
                            document.getElementById('normalTransactionDetails').classList.add('hidden');
                        } else {
                            // Show normal transaction result
                            document.getElementById('resultType').textContent = data.data.type;
                            document.getElementById('resultAmount').textContent = data.data.amount;
                            document.getElementById('resultAccount').textContent = data.data.account;
                            document.getElementById('resultNote').textContent = data.data.note;

                            // Set hidden form values
                            document.getElementById('confirmAccountId').value = data.data.account_id;
                            document.getElementById('confirmType').value = data.data.type;
                            document.getElementById('confirmAmount').value = data.data.amount;
                            document.getElementById('confirmNote').value = data.data.note;

                            // Update form action for normal transaction
                            confirmForm.action = '{{ route('transactions.store') }}';

                            // Show normal details, hide transfer details
                            document.getElementById('normalTransactionDetails').classList.remove('hidden');
                            document.getElementById('transferTransactionDetails').classList.add('hidden');
                        }

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