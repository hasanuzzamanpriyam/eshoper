<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BlogRbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Clean up existing test data to ensure clean rerun
        DB::table('admins')->where('email', 'blog_manager@admin.com')->delete();
        DB::table('admin_roles')->where('name', 'Blog Manager')->delete();

        // 2. Insert the 'Blog Manager' custom role
        $roleId = DB::table('admin_roles')->insertGetId([
            'name' => 'Blog Manager',
            'module_access' => json_encode(['dashboard', 'blog_section']),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Insert the test employee user
        DB::table('admins')->insert([
            'name' => 'Blog Manager Employee',
            'phone' => '01700000000',
            'email' => 'blog_manager@admin.com',
            'admin_role_id' => $roleId,
            'image' => 'def.png',
            'password' => Hash::make('12345678'),
            'status' => 1,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Blog RBAC role and user seeded successfully!');
        $this->command->info('Email: blog_manager@admin.com');
        $this->command->info('Password: 12345678');
    }
}
