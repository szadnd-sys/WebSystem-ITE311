<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    /**
     * Count users whose role matches any of the provided aliases (case-insensitive).
     */
    protected function countUsersByRoles(array $roles): int
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('COUNT(*) as cnt');
        $builder->groupStart();
        foreach ($roles as $i => $r) {
            $builder->orWhere('LOWER(role)', strtolower((string) $r));
        }
        $builder->groupEnd();
        $row = $builder->get()->getRowArray();
        return (int) ($row['cnt'] ?? 0);
    }
    /**
     * Registration is disabled - only admins can add users
     */
    public function register()
    {
        $session = session();
        $session->setFlashdata('login_error', 'Registration is disabled. Please contact an administrator to create an account.');
        return redirect()->to('login');
    }

    public function login()
    {
        helper(['form']);
        $session = session();
        
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required'
            ];
            
            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');
                
                try {
                    $model = new UserModel();
                    
                    // Find user by email only
                    $user = $model->where('email', $email)->first();
                    
                    // Debug logging
                    if (!$user) {
                        log_message('info', "Login attempt failed: User not found for email: {$email}");
                    } elseif (isset($user['password'])) {
                        $passwordMatch = password_verify($password, $user['password']);
                        log_message('info', "Login attempt - Email: {$email}, Password match: " . ($passwordMatch ? 'YES' : 'NO'));
                        if (!$passwordMatch) {
                            log_message('info', "Stored hash: " . substr($user['password'], 0, 20) . "...");
                        }
                    }
                    
                    if ($user && password_verify($password, $user['password'])) {
                        // Check if account is active
                        $isActive = isset($user['is_active']) ? (int)$user['is_active'] : 1; // Default to active if field doesn't exist
                        
                        if ($isActive === 0) {
                            $session->setFlashdata('login_error', 'Your account has been deactivated. Please contact the administrator.');
                            return redirect()->to('login');
                        }
                        
                        // Use the name field directly from database
                        $userName = $user['name'] ?? $user['email'];
                        // Normalize role: trim, lowercase, map common aliases
                        $roleRaw = strtolower(trim((string)($user['role'] ?? 'student')));
                        $roleMap = [
                            'admin' => 'admin',
                            'administrator' => 'admin',
                            'teacher' => 'teacher',
                            'instructor' => 'teacher',
                            'professor' => 'teacher',
                            'student' => 'student',
                        ];
                        $role = $roleMap[$roleRaw] ?? 'student';
                        // Set session data
                        $sessionData = [
                            'user_id' => $user['id'],
                            'user_name' => $userName,
                            'user_email' => $user['email'],
                            'role' => $role,
                            'isLoggedIn' => true
                        ];
                        
                        // Prevent session fixation
                        $session->regenerate();
                        $session->set($sessionData);
                        $session->setFlashdata('success', 'Welcome, ' . $userName . '!');

                        // Role-based redirection
                        if ($role === 'admin') {
                            return redirect()->to(base_url('admin/dashboard'));
                        } elseif ($role === 'teacher') {
                            return redirect()->to(base_url('teacher/dashboard'));
                        } else {
                            // Default for students
                            return redirect()->to(base_url('student/dashboard'));
                        }
                    } else {
                        $session->setFlashdata('login_error', 'Invalid email or password.');
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Login exception: ' . $e->getMessage());
                    $session->setFlashdata('login_error', 'Login failed. Please try again.');
                }
            } else {
                $session->setFlashdata('login_error', 'Please check your input and try again.');
            }
        }
        
        return view('auth/login', [
            'validation' => $this->validator
        ]);
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('login');
    }

    public function dashboard()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access the dashboard.');
            return redirect()->to('login');
        }
        
        $role = strtolower((string) $session->get('role'));
        if ($role === 'instructor') {
            $role = 'teacher';
        }
        $userId = (int) $session->get('user_id');

        // Prepare role-specific data
        $db = \Config\Database::connect();
        $roleData = [];
        try {
            if ($role === 'admin') {
                $userModel = new UserModel();
                $roleData['totalUsers'] = $userModel->countAllResults();
                // Case-insensitive counts with common aliases
                $roleData['totalAdmins'] = $this->countUsersByRoles(['admin','administrator']);
                $roleData['totalTeachers'] = $this->countUsersByRoles(['teacher','instructor','professor']);
                $roleData['totalStudents'] = $this->countUsersByRoles(['student']);
                try {
                    $roleData['totalCourses'] = $db->table('courses')->countAllResults();
                } catch (\Throwable $e) {
                    $roleData['totalCourses'] = 0;
                }
                $roleData['recentUsers'] = $userModel->orderBy('created_at', 'DESC')->limit(5)->find();

                // Admin: list all courses for management (including upload materials)
                try {
                    $roleData['allCourses'] = $db->table('courses')
                        ->orderBy('created_at', 'DESC')
                        ->get(20)
                        ->getResultArray();
                } catch (\Throwable $e) {
                    $roleData['allCourses'] = [];
                }
            } elseif ($role === 'teacher') {
                $courses = [];
                try {
                    // Show all courses to instructors for easier material uploads
                    $courses = $db->table('courses')
                        ->orderBy('created_at', 'DESC')
                        ->get(50)
                        ->getResultArray();
                } catch (\Throwable $e) {
                    $courses = [];
                }
                $notifications = [];
                try {
                    $notifications = $db->table('submissions')
                        ->select('student_name, course_id, created_at')
                        ->orderBy('created_at', 'DESC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();
                } catch (\Throwable $e) {
                    $notifications = [];
                }
                // Get pending enrollments for teacher's courses
                $pendingEnrollments = [];
                try {
                    $enrollmentModel = new \App\Models\EnrollmentModel();
                    $pendingEnrollments = $enrollmentModel->getPendingEnrollmentsForTeacher($userId);
                } catch (\Throwable $e) {
                    $pendingEnrollments = [];
                }
                $roleData['courses'] = $courses;
                $roleData['notifications'] = $notifications;
                $roleData['pendingEnrollments'] = $pendingEnrollments;
            } elseif ($role === 'student') {
                $enrolledCourses = [];
                $pendingEnrollments = [];
                $rejectedEnrollments = [];
                $upcomingDeadlines = [];
                $recentGrades = [];
                try {
                    // Get all enrollments with status
                    $allEnrollments = $db->table('enrollments e')
                        ->select('c.id, c.title, c.description, e.enrollment_date as created_at, e.status, e.rejection_reason')
                        ->join('courses c', 'c.id = e.course_id', 'left')
                        ->where('e.user_id', $userId)
                        ->orderBy('e.enrollment_date', 'DESC')
                        ->orderBy('e.id', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    // Separate by status
                    foreach ($allEnrollments as $enrollment) {
                        $status = $enrollment['status'] ?? 'pending';
                        if ($status === 'approved') {
                            $enrolledCourses[] = $enrollment;
                        } elseif ($status === 'pending') {
                            $pendingEnrollments[] = $enrollment;
                        } elseif ($status === 'rejected') {
                            $rejectedEnrollments[] = $enrollment;
                        }
                    }
                } catch (\Throwable $e) {
                    $enrolledCourses = [];
                    $pendingEnrollments = [];
                    $rejectedEnrollments = [];
                }
                try {
                    $upcomingDeadlines = $db->table('assignments a')
                        ->select('a.id, a.title, a.due_date, c.title as course_title')
                        ->join('courses c', 'c.id = a.course_id', 'left')
                        ->where('a.due_date >=', date('Y-m-d'))
                        ->orderBy('a.due_date', 'ASC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();
                } catch (\Throwable $e) {
                    $upcomingDeadlines = [];
                }
                try {
                    $recentGrades = $db->table('grades g')
                        ->select('g.score, g.created_at, a.title as assignment_title, c.title as course_title')
                        ->join('assignments a', 'a.id = g.assignment_id', 'left')
                        ->join('courses c', 'c.id = a.course_id', 'left')
                        ->where('g.student_id', $userId)
                        ->orderBy('g.created_at', 'DESC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();
                } catch (\Throwable $e) {
                    $recentGrades = [];
                }
                $roleData['enrolledCourses'] = $enrolledCourses;
                $roleData['pendingEnrollments'] = $pendingEnrollments;
                $roleData['rejectedEnrollments'] = $rejectedEnrollments;
                $roleData['upcomingDeadlines'] = $upcomingDeadlines;
                $roleData['recentGrades'] = $recentGrades;

                // Student: materials per enrolled course
                try {
                    $materialsByCourse = [];
                    if (!empty($enrolledCourses)) {
                        $courseIds = array_map(function ($c) { return (int)($c['id'] ?? 0); }, $enrolledCourses);
                        $courseIds = array_values(array_filter($courseIds));
                        if (!empty($courseIds)) {
                            $materials = $db->table('materials')
                                ->whereIn('course_id', $courseIds)
                                ->orderBy('created_at', 'DESC')
                                ->get()
                                ->getResultArray();
                            foreach ($materials as $m) {
                                $cid = (int)($m['course_id'] ?? 0);
                                if (!isset($materialsByCourse[$cid])) {
                                    $materialsByCourse[$cid] = [];
                                }
                                $materialsByCourse[$cid][] = $m;
                            }
                        }
                    }
                    $roleData['materialsByCourse'] = $materialsByCourse;
                } catch (\Throwable $e) {
                    $roleData['materialsByCourse'] = [];
                }

                // Student: Get announcements for enrolled courses
                try {
                    $announcementModel = new AnnouncementModel();
                    $roleData['announcements'] = $announcementModel->getAnnouncementsForStudent($userId, 10);
                } catch (\Throwable $e) {
                    $roleData['announcements'] = [];
                }
            }
        } catch (\Throwable $e) {
            $roleData = [];
        }

        $data = array_merge([
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role
        ], $roleData);

        return view('auth/dashboard', $data);
    }

    public function announcements()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to view announcements.');
            return redirect()->to('login');
        }
        
        $role = strtolower((string) $session->get('role'));
        
        // Only students can access announcements page
        if ($role !== 'student') {
            $session->setFlashdata('access_error', 'Access denied. This page is only for students.');
            return redirect()->to('dashboard');
        }
        
        $userId = (int) $session->get('user_id');
        $announcements = [];
        
        try {
            $announcementModel = new AnnouncementModel();
            $announcements = $announcementModel->getAnnouncementsForStudent($userId, 50); // Get more announcements for dedicated page
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch announcements: ' . $e->getMessage());
            $session->setFlashdata('error', 'Failed to load announcements. Please try again.');
        }
        
        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role,
            'announcements' => $announcements,
        ];
        
        return view('auth/announcements', $data);
    }
}