<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds for accounts aligned with RoleSeeder roles.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Andi Pratama',
                'email' => 'andi.pratama@travelsys.internal',
                'password' => Hash::make('password'),
                'employee_code' => 'EMP-4091',
                'job_title' => 'Senior Cloud Consultant',
                'department' => 'Engineering & Technology',
                'band' => 'Band 2',
                'role' => 'employee',
                'approval_limit' => 0.00,
                'phone' => null,
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Siti Rahma',
                'email' => 'siti.rahma@travelsys.internal',
                'password' => Hash::make('password'),
                'employee_code' => 'EMP-1022',
                'job_title' => 'VP of Product Management',
                'department' => 'Product & Design',
                'band' => 'Band 4',
                'role' => 'manager',
                'approval_limit' => 5000000.00, // Limit Tier 1: ≤ Rp 5.000.000
                'phone' => null,
                'avatar_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@travelsys.internal',
                'password' => Hash::make('password'),
                'employee_code' => 'EMP-0504',
                'job_title' => 'Corporate Finance Director',
                'department' => 'Corporate Finance',
                'band' => 'Band 5',
                'role' => 'finance',
                'approval_limit' => 15000000.00, // Limit Tier 2: ≤ Rp 15.000.000
                'phone' => null,
                'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Raion',
                'email' => 'farhan260908@gmail.com',
                'password' => Hash::make('password'),
                'employee_code' => 'EMP-5913',
                'job_title' => 'Lead Software Engineer',
                'department' => 'Engineering & Tech',
                'band' => 'Band 2',
                'role' => 'employee',
                'approval_limit' => 0.00,
                'phone' => null,
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra.gunawan@travelsys.internal',
                'password' => Hash::make('password'),
                'employee_code' => 'EMP-0010',
                'job_title' => 'Managing Director & CEO',
                'department' => 'Executive Management',
                'band' => 'Band 5',
                'role' => 'direktur',
                'approval_limit' => 100000000.00, // Limit Tier 3: > Rp 15.000.000
                'phone' => null,
                'avatar_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'admin@travelsys.internal',
                'password' => Hash::make('password'),
                'employee_code' => 'ADM-0001',
                'job_title' => 'Corporate Travel Administrator',
                'department' => 'General Affairs & Operations',
                'band' => 'Band 3',
                'role' => 'travel_admin',
                'approval_limit' => 50000000.00,
                'phone' => null,
                'avatar_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&auto=format&fit=crop&q=80',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
