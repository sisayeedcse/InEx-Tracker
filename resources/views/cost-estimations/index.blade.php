@extends('layouts.app')

@section('title', 'Cost Estimation - InEx Tracker')

@section('content')
    <div class="space-y-6 animate-fade-in">
        <!-- Title -->
        <div class="flex justify-between items-center">
            <div>
                <h1
                    class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span class="text-4xl">📊</span>
                    Cost Estimation
                </h1>
                <p class="text-gray-600 mt-2">Plan your future expenses and see projected balance</p>
            </div>
        </div>

        <!-- Balance Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="gradient-blue text-white rounded-2xl shadow-xl p-8 card-hover relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 text-white opacity-10 text-9xl">💰</div>
                <div class="relative z-10">
                    <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Current Total Balance</h3>
                    <p class="text-4xl font-bold mt-3">৳{{ number_format($totalCurrentBalance, 2) }}</p>
                    <div class="mt-4 pt-4 border-t border-white border-opacity-30">
                        <p class="text-xs opacity-75">Your actual current balance</p>
                    </div>
                </div>
            </div>
            <div class="gradient-red text-white rounded-2xl shadow-xl p-8 card-hover relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 text-white opacity-10 text-9xl">📉</div>
                <div class="relative z-10">
                    <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Estimated Future Costs</h3>
                    <p class="text-4xl font-bold mt-3">৳{{ number_format($totalEstimatedCosts, 2) }}</p>
                    <div class="mt-4 pt-4 border-t border-white border-opacity-30">
                        <p class="text-xs opacity-75">Total planned expenses</p>
                    </div>
                </div>
            </div>
            <div
                class="gradient-{{ $projectedBalance >= 0 ? 'green' : 'red' }} text-white rounded-2xl shadow-xl p-8 card-hover relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 text-white opacity-10 text-9xl">🔮</div>
                <div class="relative z-10">
                    <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Projected Balance</h3>
                    <p class="text-4xl font-bold mt-3">৳{{ number_format($projectedBalance, 2) }}</p>
                    <div class="mt-4 pt-4 border-t border-white border-opacity-30">
                        <p class="text-xs opacity-75">Balance after planned costs</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input Section -->
        <div class="glass-effect rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <h2
                    class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>💬</span>
                    Add Future Cost
                </h2>
                <p class="text-sm text-gray-600 mt-1">Type naturally, e.g., "I will buy a trimmer for 2000 taka"</p>
            </div>
            <div class="p-8">
                <form action="{{ route('cost-estimations.parse') }}" method="POST">
                    @csrf
                    <div class="flex gap-3">
                        <div class="flex-1 relative group">
                            <input type="text" name="message" required
                                class="w-full border-2 border-gray-300 rounded-xl px-6 py-4 focus:outline-none focus:ring-4 focus:ring-purple-200 focus:border-purple-400 transition-all duration-300 text-gray-700 placeholder-gray-400"
                                placeholder="💬 Enter your future expense..." autocomplete="off">
                            <div
                                class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-400 to-pink-400 opacity-0 group-focus-within:opacity-20 transition-opacity duration-300 pointer-events-none">
                            </div>
                        </div>
                        <button type="submit"
                            class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            <span class="flex items-center gap-2">
                                <span>➕</span>
                                Add
                            </span>
                        </button>
                    </div>
                </form>

                @if(session('error'))
                    <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded">
                        <p class="font-medium">{{ session('error') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Cost Estimations Table -->
        <div class="glass-effect rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                <h2
                    class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>📋</span>
                    Planned Expenses
                </h2>
                <p class="text-sm text-gray-600 mt-1">Your future cost estimations (no actual transactions)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Added</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($costEstimations as $estimation)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $estimation->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $estimation->created_at->format('M d, Y') }}<br>
                                    <span class="text-xs text-gray-500">{{ $estimation->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $estimation->account->name }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-red-600">
                                    -৳{{ number_format($estimation->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $estimation->description }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('cost-estimations.destroy', $estimation) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this cost estimation?');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors duration-200"
                                            title="Delete Estimation">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="text-6xl mb-4">📊</div>
                                    <p class="text-lg font-medium">No cost estimations yet</p>
                                    <p class="text-sm mt-2">Start planning your future expenses above!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Information Box -->
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 px-6 py-4 rounded-lg">
            <div class="flex items-start">
                <span class="text-2xl mr-3">ℹ️</span>
                <div>
                    <p class="font-bold text-lg mb-1">This is for estimation only</p>
                    <p class="text-sm">No actual transactions will occur. This page helps you plan future expenses and see
                        how they will affect your balance.</p>
                </div>
            </div>
        </div>
    </div>
@endsection