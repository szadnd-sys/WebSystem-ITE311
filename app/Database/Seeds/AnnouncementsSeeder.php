<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get instructor user (ID 3)
        $instructor = $db->table('users')->where('email', 'instructor@example.com')->get()->getRowArray();
        if (!$instructor) {
            return;
        }
        $instructorId = (int) $instructor['id'];
        
        // Get all courses
        $courses = $db->table('courses')->get()->getResultArray();
        
        if (empty($courses)) {
            return;
        }
        
        $announcements = [];
        $announcementTemplates = [
            [
                'title' => 'Welcome to the Course!',
                'message' => 'Welcome everyone! This course will cover fundamental concepts. Please review the course materials and syllabus.',
            ],
            [
                'title' => 'Important Reminder',
                'message' => 'Remember to submit your assignments before the deadline. Late submissions will not be accepted.',
            ],
            [
                'title' => 'New Materials Available',
                'message' => 'I have uploaded new course materials. Please check the materials section for the latest files.',
            ],
        ];
        
        foreach ($courses as $courseIndex => $course) {
            // Add 2-3 announcements per course
            for ($i = 0; $i < min(3, count($announcementTemplates)); $i++) {
                $template = $announcementTemplates[$i];
                
                $announcements[] = [
                    'course_id' => (int) $course['id'],
                    'instructor_id' => $instructorId,
                    'title' => $template['title'],
                    'message' => $template['message'],
                    'material_id' => null,
                    'created_at' => date('Y-m-d H:i:s', time() - (86400 * ($i + 1))), // Different dates
                ];
            }
        }
        
        if (!empty($announcements)) {
            $db->table('announcements')->insertBatch($announcements);
        }
    }
}

