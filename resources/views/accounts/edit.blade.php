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

                <div class="mb-6">
                    <label for="balance" class="block text-sm font-medium text-gray-700 mb-2">Current Balance (৳)</label>
                    <input type="number" id="balance" name="balance" step="0.01" min="0"
                        value="{{ old('balance', $account->balance) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('balance') border-red-500 @enderror"
                        required>
                    @error('balance')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

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
@endsection