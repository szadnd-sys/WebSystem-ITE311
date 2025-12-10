<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'title',
        'description',
        'instructor_id',
        'schedule_day',
        'schedule_time',
        'schedule_room',
        'schedule_start_date',
        'schedule_end_date',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[200]',
        'description' => 'permit_empty|max_length[5000]',
        'instructor_id' => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Course title is required.',
            'min_length' => 'Course title must be at least 3 characters long.',
            'max_length' => 'Course title cannot exceed 200 characters.',
        ],
        'instructor_id' => [
            'required' => 'Instructor is required.',
            'integer' => 'Invalid instructor selection.',
        ],
    ];

    /**
     * Get all courses with instructor information
     */
    public function getCoursesWithInstructor()
    {
        return $this->select('courses.*, users.name as instructor_name, users.email as instructor_email')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->orderBy('courses.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get course with instructor information
     */
    public function getCourseWithInstructor($id)
    {
        return $this->select('courses.*, users.name as instructor_name, users.email as instructor_email')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->where('courses.id', $id)
                    ->first();
    }

    /**
     * Get courses by instructor
     */
    public function getCoursesByInstructor($instructorId)
    {
        return $this->where('instructor_id', $instructorId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}

