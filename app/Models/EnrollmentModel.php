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
        'status',
        'rejection_reason',
        'approved_at',
        'rejected_at',
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
     * Check if a user is already enrolled in a specific course (approved only).
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->where('status', 'approved')
                    ->first() !== null;
    }

    /**
     * Check if a user has a pending enrollment request.
     */
    public function hasPendingEnrollment($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->where('status', 'pending')
                    ->first() !== null;
    }

    /**
     * Get pending enrollments for a course.
     */
    public function getPendingEnrollments($course_id)
    {
        return $this->select('enrollments.*, users.name as student_name, users.email as student_email, courses.title as course_title')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.course_id', $course_id)
                    ->where('enrollments.status', 'pending')
                    ->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get all pending enrollments for teacher's courses.
     */
    public function getPendingEnrollmentsForTeacher($teacher_id = null)
    {
        $builder = $this->select('enrollments.*, users.name as student_name, users.email as student_email, courses.title as course_title, courses.id as course_id')
                        ->join('users', 'users.id = enrollments.user_id')
                        ->join('courses', 'courses.id = enrollments.course_id')
                        ->where('enrollments.status', 'pending')
                        ->orderBy('enrollments.enrollment_date', 'DESC');
        
        // If teacher_id is provided, filter by teacher's courses
        if ($teacher_id !== null) {
            $builder->where('courses.instructor_id', $teacher_id);
        }
        
        return $builder->findAll();
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
