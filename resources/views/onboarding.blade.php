<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Onboarding - InEx Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">💰 Welcome to InEx Tracker</h1>
            <p class="text-gray-600">Let's set up your accounts to get started</p>
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" id="onboardingForm">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    How many accounts do you want to add?
                </label>
                <input type="number" id="accountCount" min="1" max="10" value="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div id="accountsContainer" class="space-y-4 mb-6">
                <!-- Accounts will be added here dynamically -->
            </div>

            <div class="flex justify-between">
                <button type="button" id="generateBtn"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                    Generate Fields
                </button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                    Complete Setup
                </button>
            </div>
        </form>
    </div>

    <script>
        const accountCountInput = document.getElementById('accountCount');
        const generateBtn = document.getElementById('generateBtn');
        const accountsContainer = document.getElementById('accountsContainer');

        // Common account suggestions
        const suggestions = ['bKash', 'Upay', 'Payoneer', 'Bank', 'Cash'];

        function generateAccountFields() {
            const count = parseInt(accountCountInput.value) || 1;
            accountsContainer.innerHTML = '';

            for (let i = 0; i < count; i++) {
                const accountDiv = document.createElement('div');
                accountDiv.className = 'border border-gray-200 rounded-lg p-4 bg-gray-50';
                accountDiv.innerHTML = `
                    <h4 class="font-medium text-gray-700 mb-3">Account ${i + 1}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                            <input 
                                type="text" 
                                name="accounts[${i}][name]" 
                                value="${suggestions[i] || ''}"
                                required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., bKash"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Initial Balance (৳)</label>
                            <input 
                                type="number" 
                                name="accounts[${i}][balance]" 
                                step="0.01"
                                min="0"
                                value="0"
                                required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00"
                            >
                        </div>
                    </div>
                `;
                accountsContainer.appendChild(accountDiv);
            }
        }

        generateBtn.addEventListener('click', generateAccountFields);

        // Generate default fields on page load
        generateAccountFields();
    </script>
</body>

</html>