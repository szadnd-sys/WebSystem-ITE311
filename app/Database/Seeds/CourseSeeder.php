<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title'         => 'Introduction to Web Development',
                'description'   => 'Learn the basics of HTML, CSS, and JavaScript to build your first website.',
                'instructor_id' => 3, // Assuming Jane Instructor has ID 3
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Advanced PHP Programming',
                'description'   => 'Dive deep into PHP with object-oriented programming, frameworks, and best practices.',
                'instructor_id' => 3,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Database Design and Management',
                'description'   => 'Master SQL, database normalization, and efficient data management techniques.',
                'instructor_id' => 3,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'JavaScript Frameworks: React',
                'description'   => 'Build dynamic user interfaces with React, including components, state, and hooks.',
                'instructor_id' => 3,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Cybersecurity Fundamentals',
                'description'   => 'Understand the principles of information security, threats, and protective measures.',
                'instructor_id' => 3,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert multiple records into 'courses' table
        $this->db->table('courses')->insertBatch($data);
    }
}
