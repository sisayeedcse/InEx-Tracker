<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Setting;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Show the onboarding form.
     */
    public function onboarding()
    {
        // If accounts already exist, redirect to dashboard
        if (Account::count() > 0) {
            return redirect()->route('dashboard');
        }

        return view('onboarding');
    }

    /**
     * Store onboarding data (multiple accounts).
     */
    public function storeOnboarding(Request $request)
    {
        $request->validate([
            'accounts' => 'required|array|min:1',
            'accounts.*.name' => 'required|string|max:255',
            'accounts.*.balance' => 'required|numeric|min:0',
        ]);

        foreach ($request->accounts as $accountData) {
            Account::create([
                'name' => $accountData['name'],
                'balance' => $accountData['balance'],
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Accounts created successfully!');
    }

    /**
     * Display a listing of accounts.
     */
    public function index()
    {
        $accounts = Account::withCount('transactions')->get();
        $usdToBdtRate = Setting::getUsdToBdtRate();
        return view('accounts.index', compact('accounts', 'usdToBdtRate'));
    }

    /**
     * Show the form for creating a new account.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:accounts',
            'balance' => 'required|numeric|min:0',
        ]);

        Account::create($request->only(['name', 'balance']));

        return redirect()->route('accounts.index')->with('success', 'Account created successfully!');
    }

    /**
     * Display the specified account.
     */
    public function show(Account $account)
    {
        $account->load('transactions');
        $usdToBdtRate = Setting::getUsdToBdtRate();
        return view('accounts.show', compact('account', 'usdToBdtRate'));
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, Account $account)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:accounts,name,' . $account->id,
            'balance' => 'required|numeric|min:0',
        ]);

        $account->update($request->only(['name', 'balance']));

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully!');
    }

    /**
     * Remove the specified account.
     */
    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully!');
    }

    /**
     * Update USD to BDT exchange rate.
     */
    public function updateExchangeRate(Request $request)
    {
        $request->validate([
            'usd_to_bdt_rate' => 'required|numeric|min:0.01',
        ]);

        Setting::set('usd_to_bdt_rate', $request->usd_to_bdt_rate);

        return redirect()->route('accounts.index')->with('success', 'Exchange rate updated successfully!');
    }
}
