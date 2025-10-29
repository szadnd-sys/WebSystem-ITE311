<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'course_id',
        'instructor_id',
        'title',
        'message',
        'material_id',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get announcements for students enrolled in specific courses
     */
    public function getAnnouncementsForStudent($userId, $limit = 10)
    {
        $db = \Config\Database::connect();
        
        return $db->table('announcements a')
            ->select('a.*, c.title as course_title, u.name as instructor_name, m.file_name as material_name')
            ->join('enrollments e', 'e.course_id = a.course_id')
            ->join('courses c', 'c.id = a.course_id')
            ->join('users u', 'u.id = a.instructor_id')
            ->join('materials m', 'm.id = a.material_id', 'left')
            ->where('e.user_id', $userId)
            ->orderBy('a.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get announcements in a specific course
     */
    public function getAnnouncementsByCourse($courseId, $limit = 20)
    {
        return $this->select('announcements.*, users.name as instructor_name, materials.file_name as material_name')
            ->join('users', 'users.id = announcements.instructor_id')
            ->join('materials', 'materials.id = announcements.material_id', 'left')
            ->where('announcements.course_id', $courseId)
            ->orderBy('announcements.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Create an announcement when material is uploaded
     */
    public function createMaterialAnnouncement($courseId, $instructorId, $materialId, $fileName)
    {
        $courseTitle = 'a course';
        
        // Get course title
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
        if ($course) {
            $courseTitle = $course['title'];
        }

        $data = [
            'course_id' => $courseId,
            'instructor_id' => $instructorId,
            'title' => 'New Material Uploaded',
            'message' => "A new material '{$fileName}' has been uploaded for {$courseTitle}. Please check your course materials.",
            'material_id' => $materialId,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->insert($data);
    }
}

