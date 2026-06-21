<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminLoginSeeder extends Seeder
{
    /**
     * Seed the super admin login credentials.
     *
     * Credentials:
     *   Email    : admin@admin.com
     *   Password : 12345678
     */
    public function run()
    {
        // ── 1. Ensure the Master Admin role exists ────────────────────────────
        $roleExists = DB::table('admin_roles')->where('id', 1)->exists();

        if (! $roleExists) {
            DB::table('admin_roles')->insert([
                'id'            => 1,
                'name'          => 'Master Admin',
                'module_access' => null,   // null means full access
                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $this->command->info('Master Admin role created.');
        } else {
            $this->command->line('Master Admin role already exists — skipping.');
        }

        // ── 2. Upsert the super admin account ────────────────────────────────
        $email = 'admin@admin.com';

        $existing = DB::table('admins')->where('email', $email)->first();

        if ($existing) {
            DB::table('admins')->where('email', $email)->update([
                'name'          => 'Super Admin',
                'password'      => Hash::make('12345678'),
                'admin_role_id' => 1,
                'status'        => 1,
                'updated_at'    => now(),
            ]);
            $this->command->info("Existing admin ({$email}) credentials reset.");
        } else {
            DB::table('admins')->insert([
                'name'           => 'Super Admin',
                'phone'          => '01700000000',
                'email'          => $email,
                'admin_role_id'  => 1,
                'image'          => 'def.png',
                'password'       => Hash::make('12345678'),
                'status'         => 1,
                'remember_token' => Str::random(10),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            $this->command->info("New super admin created ({$email}).");
        }

        // ── 3. Print credentials summary ──────────────────────────────────────
        $this->command->newLine();
        $this->command->info('╔═════════════════════════════════════╗');
        $this->command->info('║       Admin Login Credentials       ║');
        $this->command->info('╠═════════════════════════════════════╣');
        $this->command->info("║  Email    : {$email}        ║");
        $this->command->info('║  Password : 12345678                ║');
        $this->command->info('╚═════════════════════════════════════╝');
    }
}
