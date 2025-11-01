<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MaterialsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get instructor user (ID 3)
        $instructor = $db->table('users')->where('email', 'instructor@example.com')->get()->getRowArray();
        if (!$instructor) {
            return;
        }
        
        // Get all courses
        $courses = $db->table('courses')->get()->getResultArray();
        
        if (empty($courses)) {
            return;
        }
        
        $materials = [];
        $sampleFiles = [
            'Course_Introduction.pdf',
            'Week_1_Lecture_Notes.docx',
            'Assignment_Guidelines.pdf',
            'Study_Guide.pdf',
        ];
        
        foreach ($courses as $index => $course) {
            // Add 2-3 materials per course
            for ($i = 0; $i < min(3, count($sampleFiles)); $i++) {
                $fileName = $sampleFiles[$i] ?? 'Material_' . ($i + 1) . '.pdf';
                $filePath = 'uploads/materials/' . $fileName;
                
                $materials[] = [
                    'course_id' => (int) $course['id'],
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'created_at' => date('Y-m-d H:i:s', time() - (86400 * ($i + 1))), // Different dates
                ];
            }
        }
        
        if (!empty($materials)) {
            $db->table('materials')->insertBatch($materials);
        }
    }
}

