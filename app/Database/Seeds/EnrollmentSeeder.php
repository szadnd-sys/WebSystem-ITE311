<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get student user (ID 2)
        $student = $db->table('users')->where('email', 'student@example.com')->get()->getRowArray();
        if (!$student) {
            return;
        }
        $studentId = (int) $student['id'];
        
        // Get all courses
        $courses = $db->table('courses')->get()->getResultArray();
        
        if (empty($courses)) {
            return;
        }
        
        $enrollments = [];
        foreach ($courses as $course) {
            // Enroll student in all courses
            $enrollments[] = [
                'user_id' => $studentId,
                'course_id' => (int) $course['id'],
                'enrollment_date' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($enrollments)) {
            $db->table('enrollments')->insertBatch($enrollments);
        }
    }
}

