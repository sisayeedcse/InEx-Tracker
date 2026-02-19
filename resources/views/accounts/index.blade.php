@extends('layouts.app')

@section('title', 'Accounts - InEx Tracker')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Manage Accounts</h1>
            <a href="{{ route('accounts.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                + Add Account
            </a>
        </div>

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($accounts as $account)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $account->name }}</h3>
                            <span class="text-xs text-gray-500">{{ $account->transactions_count }} transactions</span>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-1">Current Balance</p>
                            <p class="text-3xl font-bold text-gray-900">৳{{ number_format($account->balance, 2) }}</p>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('accounts.show', $account) }}"
                                class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded font-medium text-sm">
                                View
                            </a>
                            <a href="{{ route('accounts.edit', $account) }}"
                                class="flex-1 text-center bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded font-medium text-sm">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('accounts.destroy', $account) }}" class="flex-1"
                                onsubmit="return confirm('Are you sure you want to delete this account?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded font-medium text-sm">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-gray-500">
                    <div class="text-4xl mb-2">🏦</div>
                    No accounts found. <a href="{{ route('accounts.create') }}" class="text-blue-500 hover:underline">Create
                        one</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection