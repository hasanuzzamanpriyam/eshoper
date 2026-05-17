<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrderManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Clean up existing test employee user
        DB::table('admins')->where('email', 'order_manager@admin.com')->delete();

        // 2. Check if 'Order Manager' custom role already exists, otherwise create it
        $role = DB::table('admin_roles')->where('name', 'Order Manager')->first();
        
        if (!$role) {
            $roleId = DB::table('admin_roles')->insertGetId([
                'name' => 'Order Manager',
                'module_access' => json_encode(['order_management', 'product_management']),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Created new Order Manager role.');
        } else {
            $roleId = $role->id;
            // Update permissions to ensure it has the correct standard ones
            DB::table('admin_roles')->where('id', $roleId)->update([
                'module_access' => json_encode(['order_management', 'product_management']),
                'updated_at' => now(),
            ]);
            $this->command->info('Found existing Order Manager role and verified permissions.');
        }

        // 3. Insert the test employee user
        DB::table('admins')->insert([
            'name' => 'Order Manager Employee',
            'phone' => '01711111111',
            'email' => 'order_manager@admin.com',
            'admin_role_id' => $roleId,
            'image' => 'def.png',
            'password' => Hash::make('12345678'),
            'status' => 1,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Order Manager role and user seeded successfully!');
        $this->command->info('Email: order_manager@admin.com');
        $this->command->info('Password: 12345678');
    }
}
