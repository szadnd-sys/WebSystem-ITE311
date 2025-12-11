<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Course extends Controller
{
    public function enroll()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in.']);
        }

        $user_id = $session->get('user_id');
        $course_id = $this->request->getPost('course_id');

        if (!$course_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID is required.']);
        }

        $enrollmentModel = new EnrollmentModel();

        // Check if already enrolled (approved)
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Already enrolled in this course.']);
        }

        // Check if already has pending enrollment
        if ($enrollmentModel->hasPendingEnrollment($user_id, $course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'You already have a pending enrollment request for this course.']);
        }

        // Enroll the user with pending status
        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s'),
            'status' => 'pending', // Always set to pending - requires teacher approval
        ];

        try {
            $result = $enrollmentModel->enrollUser($data);
            
            if ($result) {
            // Get course title and instructor info
            $db = \Config\Database::connect();
            $course = $db->table('courses')
                ->select('courses.title, courses.instructor_id, users.email as instructor_email')
                ->join('users', 'users.id = courses.instructor_id', 'left')
                ->where('courses.id', (int) $course_id)
                ->get()
                ->getRowArray();
            $courseTitle = $course && isset($course['title']) ? (string) $course['title'] : 'a course';

            // Create notification for student (pending enrollment)
            try {
                $notifications = new NotificationModel();
                $notifications->insert([
                    'user_id' => (int) $user_id,
                    'title' => 'Enrollment Request Submitted',
                    'message' => "Your enrollment request for {$courseTitle} has been submitted and is pending teacher approval.",
                    'link_url' => base_url('student/dashboard'),
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Failed to create enrollment notification: ' . $e->getMessage());
            }

            // Create notification for teacher if instructor_id exists
            if ($course && isset($course['instructor_id']) && $course['instructor_id']) {
                try {
                    $student = $db->table('users')->select('name')->where('id', $user_id)->get()->getRowArray();
                    $studentName = $student && isset($student['name']) ? $student['name'] : 'A student';
                    
                    $notifications = new NotificationModel();
                    $notifications->insert([
                        'user_id' => (int) $course['instructor_id'],
                        'title' => 'New Enrollment Request',
                        'message' => "{$studentName} has requested to enroll in {$courseTitle}.",
                        'link_url' => base_url('teacher/dashboard'),
                        'is_read' => 0,
                    ]);
                } catch (\Throwable $e) {
                    log_message('error', 'Failed to create teacher notification: ' . $e->getMessage());
                }
            }
            
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => "Enrollment request submitted for {$courseTitle}. Waiting for teacher approval.", 
                    'course_title' => $courseTitle,
                    'status' => 'pending'
                ]);
            } else {
                $errors = $enrollmentModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Failed to submit enrollment request.';
                return $this->response->setJSON(['success' => false, 'message' => $errorMsg]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Enrollment error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Check if error is due to missing status column
            if (strpos($e->getMessage(), 'status') !== false || strpos($e->getMessage(), 'Unknown column') !== false) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Database migration required. Please run: php spark migrate'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'An error occurred while processing your enrollment request. Please contact the administrator.'
            ]);
        }
    }

    public function drop()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in.']);
        }
        $userId = (int) $session->get('user_id');
        $courseId = (int) $this->request->getPost('course_id');
        if (!$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID is required.']);
        }

        $enrollmentModel = new EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'You are not enrolled in this course.']);
        }

        $ok = $enrollmentModel->dropEnrollment($userId, $courseId);
        if ($ok) {
            return $this->response->setJSON(['success' => true, 'message' => 'You have dropped the course.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to drop the course.']);
    }

    public function manage($course_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        $role = strtolower((string) $session->get('role'));
        if (!in_array($role, ['admin', 'teacher'], true)) {
            $session->setFlashdata('access_error', 'Access Denied: Only instructors/admins can manage enrollment.');
            return redirect()->to('/dashboard');
        }

        $courseId = (int) $course_id;
        $enrollmentModel = new EnrollmentModel();
        $userModel = new UserModel();

        if ($this->request->getMethod() === 'POST') {
            $email = trim((string) $this->request->getPost('student_email'));
            if ($email === '') {
                $session->setFlashdata('error', 'Student email is required.');
                return redirect()->to('/admin/course/' . $courseId . '/students');
            }
            $student = $userModel->where('email', $email)->where('role', 'student')->first();
            if (!$student) {
                $session->setFlashdata('error', 'Student account not found or not a student.');
                return redirect()->to('/admin/course/' . $courseId . '/students');
            }
            if ($enrollmentModel->isAlreadyEnrolled((int) $student['id'], $courseId)) {
                $session->setFlashdata('error', 'Student already enrolled.');
                return redirect()->to('/admin/course/' . $courseId . '/students');
            }
            
            // Check if has pending enrollment
            if ($enrollmentModel->hasPendingEnrollment((int) $student['id'], $courseId)) {
                // Update pending enrollment to approved
                $pendingEnrollment = $enrollmentModel->where('user_id', (int) $student['id'])
                    ->where('course_id', $courseId)
                    ->where('status', 'pending')
                    ->first();
                
                if ($pendingEnrollment) {
                    $enrollmentModel->update($pendingEnrollment['id'], [
                        'status' => 'approved',
                        'approved_at' => date('Y-m-d H:i:s')
                    ]);
                    $session->setFlashdata('success', 'Pending enrollment approved and student enrolled successfully.');
                } else {
                    $session->setFlashdata('error', 'Failed to approve pending enrollment.');
                }
            } else {
                // Create new approved enrollment
                $enrollmentModel->insert([
                    'user_id' => (int) $student['id'],
                    'course_id' => $courseId,
                    'enrollment_date' => date('Y-m-d H:i:s'),
                    'status' => 'approved',
                    'approved_at' => date('Y-m-d H:i:s'),
                ]);
                $session->setFlashdata('success', 'Student enrolled successfully.');
            }
            
            return redirect()->to('/admin/course/' . $courseId . '/students');
        }

        // GET: list enrolled students (approved only)
        $db = \Config\Database::connect();
        $enrolled = $db->table('enrollments e')
            ->select('u.id as user_id, u.name, u.email, e.enrollment_date, e.id as enrollment_id, e.status')
            ->join('users u', 'u.id = e.user_id', 'left')
            ->where('e.course_id', $courseId)
            ->where('e.status', 'approved') // Only show approved enrollments
            ->orderBy('u.name', 'ASC')
            ->get()
            ->getResultArray();

        return view('course/manage_students', [
            'course_id' => $courseId,
            'enrolled' => $enrolled,
        ]);
    }

    /**
     * Unenroll a student from a course (teacher/admin action)
     */
    public function unenrollStudent()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in.']);
        }

        $role = strtolower((string) $session->get('role'));
        if (!in_array($role, ['admin', 'teacher'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied. Only teachers and admins can unenroll students.']);
        }

        $enrollmentId = (int) $this->request->getPost('enrollment_id');
        $courseId = (int) $this->request->getPost('course_id');
        
        if (!$enrollmentId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment ID is required.']);
        }

        if (!$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID is required.']);
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found.']);
        }

        // Verify the enrollment belongs to the specified course
        if ($enrollment['course_id'] != $courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment does not match the course.']);
        }

        // Verify teacher has access to this course
        if ($role === 'teacher') {
            $db = \Config\Database::connect();
            $course = $db->table('courses')
                ->where('id', $courseId)
                ->where('instructor_id', $session->get('user_id'))
                ->get()
                ->getRowArray();
            
            if (!$course) {
                return $this->response->setJSON(['success' => false, 'message' => 'You do not have permission to unenroll students from this course.']);
            }
        }

        // Get student and course info for notification
        $db = \Config\Database::connect();
        $student = $db->table('users')->select('name, email')->where('id', $enrollment['user_id'])->get()->getRowArray();
        $course = $db->table('courses')->select('title')->where('id', $courseId)->get()->getRowArray();
        
        $studentName = $student && isset($student['name']) ? $student['name'] : 'Student';
        $courseTitle = $course && isset($course['title']) ? $course['title'] : 'the course';

        // Delete the enrollment
        if ($enrollmentModel->delete($enrollmentId)) {
            // Notify student
            try {
                $notifications = new NotificationModel();
                $notifications->insert([
                    'user_id' => (int) $enrollment['user_id'],
                    'title' => 'Enrollment Removed',
                    'message' => "You have been unenrolled from {$courseTitle}.",
                    'link_url' => base_url('student/dashboard'),
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Failed to create unenrollment notification: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "{$studentName} has been unenrolled from {$courseTitle}."
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to unenroll student.']);
    }

    /**
     * Approve enrollment request
     */
    public function approveEnrollment()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in.']);
        }

        $role = strtolower((string) $session->get('role'));
        if (!in_array($role, ['admin', 'teacher'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied. Only teachers can approve enrollments.']);
        }

        $enrollmentId = (int) $this->request->getPost('enrollment_id');
        if (!$enrollmentId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment ID is required.']);
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found.']);
        }

        // Verify teacher has access to this course
        if ($role === 'teacher') {
            $db = \Config\Database::connect();
            $course = $db->table('courses')
                ->where('id', $enrollment['course_id'])
                ->where('instructor_id', $session->get('user_id'))
                ->get()
                ->getRowArray();
            
            if (!$course) {
                return $this->response->setJSON(['success' => false, 'message' => 'You do not have permission to approve enrollments for this course.']);
            }
        }

        // Update enrollment status
        $updateData = [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => null,
            'rejected_at' => null
        ];

        if ($enrollmentModel->update($enrollmentId, $updateData)) {
            // Get course and student info for notifications
            $db = \Config\Database::connect();
            $course = $db->table('courses')->select('title')->where('id', $enrollment['course_id'])->get()->getRowArray();
            $student = $db->table('users')->select('name')->where('id', $enrollment['user_id'])->get()->getRowArray();
            
            $courseTitle = $course && isset($course['title']) ? $course['title'] : 'the course';
            $studentName = $student && isset($student['name']) ? $student['name'] : 'Student';

            // Notify student
            try {
                $notifications = new NotificationModel();
                $notifications->insert([
                    'user_id' => (int) $enrollment['user_id'],
                    'title' => 'Enrollment Approved',
                    'message' => "Your enrollment request for {$courseTitle} has been approved!",
                    'link_url' => base_url('student/dashboard'),
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Failed to create approval notification: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Enrollment approved for {$studentName}."
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to approve enrollment.']);
    }

    /**
     * Reject enrollment request - Teachers only
     */
    public function rejectEnrollment()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not logged in.']);
        }

        $role = strtolower((string) $session->get('role'));
        if (!in_array($role, ['admin', 'teacher'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied. Only teachers can reject enrollments.']);
        }

        $enrollmentId = (int) $this->request->getPost('enrollment_id');
        $rejectionReason = trim((string) $this->request->getPost('rejection_reason'));

        if (!$enrollmentId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment ID is required.']);
        }

        if (empty($rejectionReason)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Rejection reason is required.']);
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found.']);
        }

        // Verify teacher has access to this course
        if ($role === 'teacher') {
            $db = \Config\Database::connect();
            $course = $db->table('courses')
                ->where('id', $enrollment['course_id'])
                ->where('instructor_id', $session->get('user_id'))
                ->get()
                ->getRowArray();
            
            if (!$course) {
                return $this->response->setJSON(['success' => false, 'message' => 'You do not have permission to reject enrollments for this course.']);
            }
        }

        // Update enrollment status
        $updateData = [
            'status' => 'rejected',
            'rejected_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $rejectionReason,
            'approved_at' => null
        ];

        if ($enrollmentModel->update($enrollmentId, $updateData)) {
            // Get course and student info for notifications
            $db = \Config\Database::connect();
            $course = $db->table('courses')->select('title')->where('id', $enrollment['course_id'])->get()->getRowArray();
            $student = $db->table('users')->select('name')->where('id', $enrollment['user_id'])->get()->getRowArray();
            
            $courseTitle = $course && isset($course['title']) ? $course['title'] : 'the course';
            $studentName = $student && isset($student['name']) ? $student['name'] : 'Student';

            // Notify student
            try {
                $notifications = new NotificationModel();
                $notifications->insert([
                    'user_id' => (int) $enrollment['user_id'],
                    'title' => 'Enrollment Rejected',
                    'message' => "Your enrollment request for {$courseTitle} has been rejected. Reason: {$rejectionReason}",
                    'link_url' => base_url('student/dashboard'),
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Failed to create rejection notification: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Enrollment rejected for {$studentName}."
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to reject enrollment.']);
    }

    /**
     * Enrollment Management Page - Shows all pending enrollments
     */
    public function enrollmentManagement()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access this page.');
            return redirect()->to('login');
        }

        $role = strtolower((string) $session->get('role'));
        if (!in_array($role, ['admin', 'teacher'], true)) {
            $session->setFlashdata('access_error', 'Access denied. Only teachers and admins can manage enrollments.');
            return redirect()->to('dashboard');
        }

        $enrollmentModel = new EnrollmentModel();
        $db = \Config\Database::connect();
        
        // Check if status column exists
        $hasStatusField = false;
        try {
            $fields = $db->getFieldData('enrollments');
            foreach ($fields as $field) {
                if ($field->name === 'status') {
                    $hasStatusField = true;
                    break;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error checking status field: ' . $e->getMessage());
        }

        if (!$hasStatusField) {
            $session->setFlashdata('error', 'Database migration required. Please run: php spark migrate OR execute the SQL script: add_enrollment_approval_fields.sql');
        }
        
        // Get pending enrollments
        $pendingEnrollments = [];
        $allEnrollments = [];
        
        try {
            if ($role === 'teacher') {
                // For teachers, only show enrollments for their courses
                $userId = (int) $session->get('user_id');
                
                if ($hasStatusField) {
                    $pendingEnrollments = $enrollmentModel->getPendingEnrollmentsForTeacher($userId);
                    
                    // Get all enrollments (pending, approved, rejected) for teacher's courses
                    $allEnrollments = $db->table('enrollments e')
                        ->select('e.*, u.name as student_name, u.email as student_email, c.title as course_title, c.id as course_id')
                        ->join('users u', 'u.id = e.user_id')
                        ->join('courses c', 'c.id = e.course_id')
                        ->where('c.instructor_id', $userId)
                        ->orderBy('e.enrollment_date', 'DESC')
                        ->get()
                        ->getResultArray();
                } else {
                    // Fallback: show all enrollments as pending if status field doesn't exist
                    $allEnrollments = $db->table('enrollments e')
                        ->select('e.*, u.name as student_name, u.email as student_email, c.title as course_title, c.id as course_id')
                        ->join('users u', 'u.id = e.user_id')
                        ->join('courses c', 'c.id = e.course_id')
                        ->where('c.instructor_id', $userId)
                        ->orderBy('e.enrollment_date', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    // Mark all as pending if status field doesn't exist
                    foreach ($allEnrollments as &$enrollment) {
                        $enrollment['status'] = 'pending';
                    }
                    $pendingEnrollments = $allEnrollments;
                }
            } else {
                // For admins, show all enrollments
                if ($hasStatusField) {
                    $pendingEnrollments = $enrollmentModel->getPendingEnrollmentsForTeacher(null);
                    
                    $allEnrollments = $db->table('enrollments e')
                        ->select('e.*, u.name as student_name, u.email as student_email, c.title as course_title, c.id as course_id')
                        ->join('users u', 'u.id = e.user_id')
                        ->join('courses c', 'c.id = e.course_id')
                        ->orderBy('e.enrollment_date', 'DESC')
                        ->get()
                        ->getResultArray();
                } else {
                    // Fallback: show all enrollments as pending if status field doesn't exist
                    $allEnrollments = $db->table('enrollments e')
                        ->select('e.*, u.name as student_name, u.email as student_email, c.title as course_title, c.id as course_id')
                        ->join('users u', 'u.id = e.user_id')
                        ->join('courses c', 'c.id = e.course_id')
                        ->orderBy('e.enrollment_date', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    // Mark all as pending if status field doesn't exist
                    foreach ($allEnrollments as &$enrollment) {
                        $enrollment['status'] = 'pending';
                    }
                    $pendingEnrollments = $allEnrollments;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to fetch enrollments: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $session->setFlashdata('error', 'Failed to load enrollments: ' . $e->getMessage());
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role,
            'pendingEnrollments' => $pendingEnrollments,
            'allEnrollments' => $allEnrollments,
        ];

        return view('course/enrollment_management', $data);
    }

    /**
     * Display courses listing page with search functionality
     * Accessible to all authenticated users
     */
    public function index()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = strtolower((string) $session->get('role'));
        $userId = (int) $session->get('user_id');
        $search = trim((string) ($this->request->getGet('search') ?? ''));

        $db = \Config\Database::connect();
        
        // Get all courses with instructor names
        $builder = $db->table('courses c')
            ->select('c.id, c.title, c.description, c.created_at, c.instructor_id, u.name as instructor_name, u.email as instructor_email')
            ->join('users u', 'u.id = c.instructor_id', 'left');

        // Apply search filter if provided
        if (!empty($search)) {
            $builder->groupStart()
                ->like('c.title', $search)
                ->orLike('c.description', $search)
                ->orLike('u.name', $search)
                ->groupEnd();
        }

        $courses = $builder->orderBy('c.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get enrolled course IDs for students (approved only)
        $enrolledCourseIds = [];
        if ($role === 'student') {
            $enrollments = $db->table('enrollments')
                ->select('course_id')
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->get()
                ->getResultArray();
            $enrolledCourseIds = array_column($enrollments, 'course_id');
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role,
            'courses' => $courses,
            'enrolledCourseIds' => $enrolledCourseIds,
            'search' => $search,
        ];

        return view('course/index', $data);
    }

    /**
     * Server-side search endpoint for AJAX requests
     * Searches courses using SQL LIKE queries
     */
    public function search()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to search courses.'
            ]);
        }

        // Check if it's an AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        $query = trim((string) ($this->request->getGet('q') ?? ''));
        
        // If query is empty, return empty results
        if (empty($query)) {
            return $this->response->setJSON([
                'success' => true,
                'courses' => [],
                'count' => 0,
                'message' => 'Please enter a search term.'
            ]);
        }

        $role = strtolower((string) $session->get('role'));
        $userId = (int) $session->get('user_id');

        $db = \Config\Database::connect();
        $results = [];

        try {
            $builder = $db->table('courses c')
                ->select('c.id, c.title, c.description, c.created_at, c.instructor_id, u.name as instructor_name, u.email as instructor_email')
                ->join('users u', 'u.id = c.instructor_id', 'left');

            // Build search query using LIKE for title, description, and instructor name
            $builder->groupStart()
                ->like('c.title', $query, 'both')
                ->orLike('c.description', $query, 'both')
                ->orLike('u.name', $query, 'both')
                ->groupEnd();

            $builder->orderBy('c.created_at', 'DESC');

            $results = $builder->get()->getResultArray();

            // For students, mark which courses they're enrolled in (approved only)
            if ($role === 'student' && !empty($results)) {
                $courseIds = array_column($results, 'id');
                $enrollments = $db->table('enrollments')
                    ->select('course_id')
                    ->where('user_id', $userId)
                    ->where('status', 'approved')
                    ->whereIn('course_id', $courseIds)
                    ->get()
                    ->getResultArray();
                $enrolledIds = array_column($enrollments, 'course_id');

                foreach ($results as &$course) {
                    $course['is_enrolled'] = in_array($course['id'], $enrolledIds);
                }
                unset($course);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Course search error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred during search. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'courses' => $results,
            'count' => count($results)
        ]);
    }
}
