<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

class Announcements extends Controller
{
    public function create($course_id = null)
    {
        $session = session();
        helper(['form']);

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        $role = strtolower((string) $session->get('role'));
        if (!in_array($role, ['admin', 'teacher'], true)) {
            $session->setFlashdata('access_error', 'Access Denied: Only instructors/admins can create announcements.');
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();

        // Fetch courses list for selector (teachers: show all for simplicity)
        $courses = [];
        try {
            $courses = $db->table('courses')
                ->orderBy('title', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            $courses = [];
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'course_id' => 'required|integer',
                'title' => 'required|min_length[3]|max_length[255]',
                'message' => 'permit_empty|max_length[5000]',
            ];

            if (!$this->validate($rules)) {
                return view('announcements/create', [
                    'courses' => $courses,
                    'course_id' => (int) ($course_id ?? (int) $this->request->getPost('course_id')),
                    'validation' => $this->validator,
                ]);
            }

            $announcementModel = new AnnouncementModel();
            $data = [
                'course_id' => (int) $this->request->getPost('course_id'),
                'instructor_id' => (int) $session->get('user_id'),
                'title' => trim((string) $this->request->getPost('title')),
                'message' => (string) $this->request->getPost('message'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            try {
                $announcementModel->insert($data);
                $session->setFlashdata('success', 'Announcement sent to enrolled students.');
                // Redirect back to teacher dashboard
                return redirect()->to('/teacher/dashboard');
            } catch (\Throwable $e) {
                log_message('error', 'Failed creating announcement: ' . $e->getMessage());
                $session->setFlashdata('error', 'Failed to create announcement. Please try again.');
                return redirect()->back();
            }
        }

        return view('announcements/create', [
            'courses' => $courses,
            'course_id' => (int) ($course_id ?? 0),
            'validation' => null,
        ]);
    }
}



