<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin account ─────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'System Administrator',
            'email'    => 'admin@alamada-lgu.gov.ph',
            'password' => Hash::make('Admin@1234'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        // ── Staff accounts ────────────────────────────────────────────
        User::create([
            'name'     => 'Maam Joy',
            'email'    => 'maamjoy@alamada-lgu.gov.ph',
            'password' => Hash::make('Staff@1234'),
            'role'     => 'staff',
            'phone'    => '09171234567',
            'status'   => 'active',
        ]);

        User::create([
            'name'     => 'Jose Reyes',
            'email'    => 'jose.reyes@alamada-lgu.gov.ph',
            'password' => Hash::make('Staff@1234'),
            'role'     => 'staff',
            'phone'    => '09189876543',
            'status'   => 'active',
        ]);

        // ── Welcome notification ──────────────────────────────────────
        Notification::create([
            'title'           => 'Welcome to Alamada LGU Learning Center Monitoring System',
            'message'         => 'The new digital monitoring system is now active. Staff can now add children, manage enrollment, and record attendance.',
            'recipient_group' => 'all',
            'sent_by'         => $admin->id,
            'sent_at'         => Carbon::now(),
        ]);

        $this->command->info('✓ Seeding complete. No sample students added.');
        $this->command->line('  Admin:  admin@alamada-lgu.gov.ph  / Admin@1234');
        $this->command->line('  Staff:  maamjoy@alamada-lgu.gov.ph / Staff@1234');
        $this->command->line('  Staff:  jose.reyes@alamada-lgu.gov.ph / Staff@1234');
        $this->command->line('');
        $this->command->line('  Students must be added manually by Admin or Staff.');
    }
}
