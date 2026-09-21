<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UpdateStaffSeeder extends Seeder
{
    public function run(): void
    {
        // Update first staff to Maam Joy
        User::where('role', 'staff')
            ->where('email', 'maria.santos@alamada-lgu.gov.ph')
            ->update([
                'name'  => 'Maam Joy',
                'email' => 'maamjoy@alamada-lgu.gov.ph',
            ]);

        // Also handle if already has new email
        User::where('role', 'staff')
            ->where('email', 'maamjoy@alamada-lgu.gov.ph')
            ->update(['name' => 'Maam Joy']);

        $this->command->info('✓ Staff name updated to Maam Joy.');
        $this->command->line('  Login: maamjoy@alamada-lgu.gov.ph / Staff@1234');
    }
}
