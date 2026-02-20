<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class MainAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Main account already exists
        $mainAccount = Account::where('name', 'Main')->first();
        
        if (!$mainAccount) {
            // Calculate total balance from all existing accounts (excluding Main)
            $totalBalance = Account::where('name', '!=', 'Main')->sum('balance');
            
            // Create Main account
            Account::create([
                'name' => 'Main',
                'balance' => $totalBalance,
            ]);
            
            $this->command->info('Main account created successfully with balance: ৳' . number_format($totalBalance, 2));
        } else {
            $this->command->warn('Main account already exists.');
        }
    }
}
