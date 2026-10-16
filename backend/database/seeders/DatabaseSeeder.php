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
        // Use firstOrCreate so re-running never causes duplicate errors
        $admin = User::firstOrCreate(
            ['email' => 'admin@alamada-lgu.gov.ph'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@1234'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'maamjoy@alamada-lgu.gov.ph'],
            [
                'name' => 'Maam Joy',
                'password' => Hash::make('Staff@1234'),
                'role' => 'staff',
                'phone' => '09171234567',
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'jose.reyes@alamada-lgu.gov.ph'],
            [
                'name' => 'Jose Reyes',
                'password' => Hash::make('Staff@1234'),
                'role' => 'staff',
                'phone' => '09189876543',
                'status' => 'active',
            ]
        );

        if (Notification::count() === 0) {
            Notification::create([
                'title' => 'Welcome to Alamada LGU Learning Center Monitoring System',
                'message' => 'The system is now active. Staff can add children, manage enrollment, and record attendance.',
                'recipient_group' => 'all',
                'sent_by' => $admin->id,
                'sent_at' => Carbon::now(),
            ]);
        }
    }
}
