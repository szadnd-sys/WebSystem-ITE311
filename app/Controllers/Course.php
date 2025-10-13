<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
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
            return $this->response->setJSON(['success' => true, 'message' => 'Successfully enrolled in the course.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to enroll in the course.']);
        }
    }
}
