<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\User;

class AccountsAndUsersSeeder extends Seeder
{
    public function run()
    {
        // Tạo tài khoản admin
        $admin = Account::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '0123456789',
            'password' => bcrypt('admin'),
            'role' => 'admin',
        ]);

        // Tạo 2 tài khoản với vai trò 'delivery'
        $deliveryAccounts = Account::factory()->count(2)->create(['role' => 'delivery']);

        // Tạo 2 tài khoản với vai trò 'seller'
        $sellerAccounts = Account::factory()->count(2)->create(['role' => 'seller']);

        // Tạo 5 tài khoản với vai trò 'buyer'
        $buyerAccounts = Account::factory()->count(5)->create(['role' => 'buyer']);

        // Tạo dữ liệu trong bảng `users`
        $deliveryAccounts->each(function ($account) {
            User::factory()->create([
                'account_id' => $account->id,
                'position' => 'delivery',
            ]);
        });

        $sellerAccounts->each(function ($account) {
            User::factory()->create([
                'account_id' => $account->id,
                'position' => 'seller',
            ]);
        });
    }
}
