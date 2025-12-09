<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Delete existing seed users first to avoid duplicate key errors and update with correct password hashes
        $seedEmails = ['admin@example.com', 'student@example.com', 'instructor@example.com'];
        $this->db->table('users')->whereIn('email', $seedEmails)->delete();

        $data = [
            [
                'name'     => 'Admin User',
                'email'    => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role'     => 'admin',
                'is_active' => 1,
            ],
            [
                'name'     => 'John Student',
                'email'    => 'student@example.com',
                'password' => password_hash('student123', PASSWORD_BCRYPT),
                'role'     => 'student',
                'is_active' => 1,
            ],
            [
                'name'     => 'Jane Instructor',
                'email'    => 'instructor@example.com',
                'password' => password_hash('instructor123', PASSWORD_BCRYPT),
                'role'     => 'instructor',
                'is_active' => 1,
            ],
        ];

        // Insert multiple records into 'users' table
        $this->db->table('users')->insertBatch($data);
    }
}
