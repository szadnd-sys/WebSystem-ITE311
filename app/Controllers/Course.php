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

        // Check if already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Already enrolled in this course.']);
        }

        // Enroll the user
        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s'),
        ];

        if ($enrollmentModel->enrollUser($data)) {
            // Create a notification for the enrolled student
            try {
                $db = \Config\Database::connect();
                $course = $db->table('courses')->select('title')->where('id', (int) $course_id)->get()->getRowArray();
                $courseTitle = $course && isset($course['title']) ? (string) $course['title'] : 'a course';

                $notifications = new NotificationModel();
                $notifications->insert([
                    'user_id' => (int) $user_id,
                    'title' => 'Enrollment Confirmed',
                    'message' => "You have been enrolled in {$courseTitle}.",
                    'link_url' => base_url('student/dashboard'),
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                // Silently ignore notification failures
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Successfully enrolled in the course.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to enroll in the course.']);
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
            $enrollmentModel->insert([
                'user_id' => (int) $student['id'],
                'course_id' => $courseId,
                'enrollment_date' => date('Y-m-d H:i:s'),
            ]);
            $session->setFlashdata('success', 'Student enrolled successfully.');
            return redirect()->to('/admin/course/' . $courseId . '/students');
        }

        // GET: list enrolled students
        $db = \Config\Database::connect();
        $enrolled = $db->table('enrollments e')
            ->select('u.id as user_id, u.name, u.email, e.enrollment_date')
            ->join('users u', 'u.id = e.user_id', 'left')
            ->where('e.course_id', $courseId)
            ->orderBy('u.name', 'ASC')
            ->get()
            ->getResultArray();

        return view('course/manage_students', [
            'course_id' => $courseId,
            'enrolled' => $enrolled,
        ]);
    }
}
