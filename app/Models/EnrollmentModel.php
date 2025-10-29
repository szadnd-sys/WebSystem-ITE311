<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'course_id',
        'enrollment_date',
    ];

    protected $useTimestamps = false;

    /**
     * Enroll a user in a course.
     */
    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    /**
     * Get all courses a user is enrolled in.
     */
    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title, courses.description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course.
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->first() !== null;
    }

    /**
     * Drop a user's enrollment from a specific course.
     */
    public function dropEnrollment(int $userId, int $courseId): bool
    {
        return (bool) $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->delete();
    }
}
