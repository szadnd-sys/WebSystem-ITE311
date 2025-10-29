<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Helper seeder to update existing user passwords to use bcrypt
 * Run this if you can't run UserSeeder directly
 */
class UpdateUserPasswords extends Seeder
{
    public function run()
    {
        // Update passwords for seed users with bcrypt hashes
        $updates = [
            [
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
            ],
            [
                'email' => 'student@example.com',
                'password' => password_hash('student123', PASSWORD_BCRYPT),
            ],
            [
                'email' => 'instructor@example.com',
                'password' => password_hash('instructor123', PASSWORD_BCRYPT),
            ],
        ];

        foreach ($updates as $update) {
            $this->db->table('users')
                ->where('email', $update['email'])
                ->update(['password' => $update['password']]);
        }
    }
}



