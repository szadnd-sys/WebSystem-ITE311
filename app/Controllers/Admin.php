<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;
use CodeIgniter\Controller;

class Admin extends Controller
{
    protected $userModel;
    protected $courseModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
    }

    /**
     * Check if user is admin
     */
    protected function checkAdmin()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access this page.');
            return redirect()->to('login');
        }
        
        $role = strtolower((string) $session->get('role'));
        if ($role !== 'admin') {
            $session->setFlashdata('access_error', 'Access denied. Admin privileges required.');
            return redirect()->to('dashboard');
        }
        
        return null;
    }

    /**
     * List all users
     */
    public function users()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        $session = session();
        $search = $this->request->getGet('search');
        $roleFilter = $this->request->getGet('role');

        $builder = $this->userModel->builder();
        
        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }
        
        // Apply role filter
        if (!empty($roleFilter)) {
            $roleFilterLower = strtolower($roleFilter);
            if ($roleFilterLower === 'teacher') {
                $builder->groupStart()
                    ->where('LOWER(role)', 'instructor')
                    ->orWhere('LOWER(role)', 'teacher')
                    ->groupEnd();
            } elseif ($roleFilterLower === 'admin') {
                $builder->groupStart()
                    ->where('LOWER(role)', 'admin')
                    ->orWhere('LOWER(role)', 'administrator')
                    ->groupEnd();
            } else {
                $builder->where('LOWER(role)', $roleFilterLower);
            }
        }

        $users = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();

        // Normalize role display
        foreach ($users as &$user) {
            $roleRaw = strtolower(trim((string)($user['role'] ?? 'student')));
            if ($roleRaw === 'instructor') {
                $user['role_display'] = 'TEACHER';
            } elseif ($roleRaw === 'admin' || $roleRaw === 'administrator') {
                $user['role_display'] = 'ADMIN';
            } else {
                $user['role_display'] = strtoupper($roleRaw);
            }
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => 'admin',
            'users' => $users,
            'search' => $search,
            'roleFilter' => $roleFilter
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Show add user form
     */
    public function addUser()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();
        
        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => 'admin',
            'validation' => \Config\Services::validation()
        ];

        return view('admin/users/add', $data);
    }

    /**
     * Create new user
     */
    public function createUser()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();

        if ($this->request->getMethod() === 'POST') {
            $roleInput = strtolower((string) $this->request->getPost('role'));
            
            // Prevent creating new admin users through the UI
            if ($roleInput === 'admin') {
                $session->setFlashdata('error', 'Admin users cannot be created through this interface. Admin role is protected.');
                return redirect()->to('admin/users/add')->withInput();
            }

            $rules = [
                'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-Z\s\-\'\.]+$/]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'matches[password]',
                'role' => 'required|in_list[student,instructor]' // Remove admin from allowed roles
            ];

            $messages = [
                'name' => [
                    'required' => 'Full name is required.',
                    'min_length' => 'Full name must be at least 3 characters long.',
                    'max_length' => 'Full name cannot exceed 100 characters.',
                    'regex_match' => 'Full name cannot contain special characters. Only letters, spaces, hyphens, apostrophes, and periods are allowed.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                $name = trim($this->request->getPost('name'));
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                // Normalize role: convert 'teacher' to 'instructor' if needed
                if ($roleInput === 'teacher') {
                    $role = 'instructor';
                } else {
                    $role = $roleInput;
                }

                $data = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password, // Model will hash it
                    'role' => $role,
                    'is_active' => 1 // New users are active by default
                ];

                try {
                    $insertResult = $this->userModel->insert($data);
                    
                    if ($insertResult) {
                        $session->setFlashdata('success', 'User created successfully.');
                        return redirect()->to('admin/users');
                    } else {
                        $errors = $this->userModel->errors();
                        $session->setFlashdata('error', 'Failed to create user: ' . implode(', ', $errors));
                    }
                } catch (\Exception $e) {
                    log_message('error', 'User creation exception: ' . $e->getMessage());
                    $session->setFlashdata('error', 'Failed to create user. Please try again.');
                }
            } else {
                $validationErrors = $this->validator->getErrors();
                $session->setFlashdata('error', 'Validation failed: ' . implode(', ', $validationErrors));
            }
        }

        return redirect()->to('admin/users/add')->withInput();
    }

    /**
     * Check if user is admin (protected role)
     */
    protected function isAdminRole($user)
    {
        if (!$user) {
            return false;
        }
        $roleRaw = strtolower(trim((string)($user['role'] ?? 'student')));
        return ($roleRaw === 'admin' || $roleRaw === 'administrator');
    }

    /**
     * Show edit user form
     */
    public function editUser($id)
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();
        $user = $this->userModel->find($id);

        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('admin/users');
        }

        // Prevent editing admin users
        if ($this->isAdminRole($user)) {
            $session->setFlashdata('error', 'Admin users cannot be edited. Admin role is protected.');
            return redirect()->to('admin/users');
        }

        // Normalize role for display
        $roleRaw = strtolower(trim((string)($user['role'] ?? 'student')));
        if ($roleRaw === 'instructor') {
            $user['role_display'] = 'teacher';
        } else {
            $user['role_display'] = $roleRaw;
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => 'admin',
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/users/edit', $data);
    }

    /**
     * Update user
     */
    public function updateUser($id)
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();

        $user = $this->userModel->find($id);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('admin/users');
        }

        // Prevent updating admin users
        if ($this->isAdminRole($user)) {
            $session->setFlashdata('error', 'Admin users cannot be updated. Admin role is protected.');
            return redirect()->to('admin/users');
        }

        if ($this->request->getMethod() === 'POST') {
            // Build validation rules
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-Z\s\-\'\.]+$/]',
                'email' => 'required|valid_email|is_unique[users.email,id,' . $id . ']',
                'role' => 'required|in_list[student,instructor]' // Remove admin from allowed roles
            ];

            // Password is optional on update
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $rules['password'] = 'min_length[6]';
                $rules['password_confirm'] = 'matches[password]';
            }

            $messages = [
                'name' => [
                    'required' => 'Full name is required.',
                    'min_length' => 'Full name must be at least 3 characters long.',
                    'max_length' => 'Full name cannot exceed 100 characters.',
                    'regex_match' => 'Full name cannot contain special characters. Only letters, spaces, hyphens, apostrophes, and periods are allowed.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                $name = trim($this->request->getPost('name'));
                $email = $this->request->getPost('email');
                $roleInput = strtolower((string) $this->request->getPost('role'));

                // Normalize role
                if ($roleInput === 'teacher') {
                    $role = 'instructor';
                } else {
                    $role = $roleInput;
                }

                $data = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ];

                // Only update password if provided
                if (!empty($password)) {
                    $data['password'] = $password; // Model will hash it
                }

                try {
                    $updateResult = $this->userModel->update($id, $data);
                    
                    if ($updateResult) {
                        $session->setFlashdata('success', 'User updated successfully.');
                        return redirect()->to('admin/users');
                    } else {
                        $errors = $this->userModel->errors();
                        $session->setFlashdata('error', 'Failed to update user: ' . implode(', ', $errors));
                    }
                } catch (\Exception $e) {
                    log_message('error', 'User update exception: ' . $e->getMessage());
                    $session->setFlashdata('error', 'Failed to update user. Please try again.');
                }
            } else {
                $validationErrors = $this->validator->getErrors();
                $session->setFlashdata('error', 'Validation failed: ' . implode(', ', $validationErrors));
            }
        }

        return redirect()->to('admin/users/edit/' . $id)->withInput();
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        $session = session();

        // Prevent admin from deleting themselves
        if ($id == $session->get('user_id')) {
            $session->setFlashdata('error', 'You cannot delete your own account.');
            return redirect()->to('admin/users');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('admin/users');
        }

        // Prevent deleting admin users
        if ($this->isAdminRole($user)) {
            $session->setFlashdata('error', 'Admin users cannot be deleted. Admin role is protected.');
            return redirect()->to('admin/users');
        }

        try {
            $deleteResult = $this->userModel->delete($id);
            
            if ($deleteResult) {
                $session->setFlashdata('success', 'User deleted successfully.');
            } else {
                $session->setFlashdata('error', 'Failed to delete user.');
            }
        } catch (\Exception $e) {
            log_message('error', 'User deletion exception: ' . $e->getMessage());
            $session->setFlashdata('error', 'Failed to delete user. Please try again.');
        }

        return redirect()->to('admin/users');
    }

    /**
     * Activate user
     */
    public function activateUser($id)
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        $session = session();

        // Check if is_active column exists
        if (!$this->checkIsActiveColumnExists()) {
            $session->setFlashdata('error', 'The is_active column does not exist. Please run the migration: php spark migrate OR run the SQL script: add_is_active_column.sql');
            return redirect()->to('admin/users');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('admin/users');
        }

        // Prevent activating/deactivating admin users
        if ($this->isAdminRole($user)) {
            $session->setFlashdata('error', 'Admin users cannot be activated or deactivated. Admin role is protected.');
            return redirect()->to('admin/users');
        }

        try {
            $updateResult = $this->userModel->update($id, ['is_active' => 1]);
            
            if ($updateResult) {
                $session->setFlashdata('success', 'User activated successfully.');
            } else {
                $errors = $this->userModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Failed to activate user.';
                log_message('error', 'User activation failed: ' . $errorMsg);
                $session->setFlashdata('error', $errorMsg);
            }
        } catch (\Exception $e) {
            log_message('error', 'User activation exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $session->setFlashdata('error', 'Failed to activate user: ' . $e->getMessage());
        }

        return redirect()->to('admin/users');
    }

    /**
     * Check if is_active column exists in users table
     */
    protected function checkIsActiveColumnExists()
    {
        try {
            $db = \Config\Database::connect();
            $fields = $db->getFieldData('users');
            foreach ($fields as $field) {
                if ($field->name === 'is_active') {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            log_message('error', 'Error checking is_active column: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Deactivate user
     */
    public function deactivateUser($id)
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        $session = session();

        // Check if is_active column exists
        if (!$this->checkIsActiveColumnExists()) {
            $session->setFlashdata('error', 'The is_active column does not exist. Please run the migration: php spark migrate OR run the SQL script: add_is_active_column.sql');
            return redirect()->to('admin/users');
        }

        // Prevent admin from deactivating themselves
        if ($id == $session->get('user_id')) {
            $session->setFlashdata('error', 'You cannot deactivate your own account.');
            return redirect()->to('admin/users');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('admin/users');
        }

        // Prevent deactivating admin users
        if ($this->isAdminRole($user)) {
            $session->setFlashdata('error', 'Admin users cannot be activated or deactivated. Admin role is protected.');
            return redirect()->to('admin/users');
        }

        try {
            $updateResult = $this->userModel->update($id, ['is_active' => 0]);
            
            if ($updateResult) {
                $session->setFlashdata('success', 'User deactivated successfully.');
            } else {
                $errors = $this->userModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Failed to deactivate user.';
                log_message('error', 'User deactivation failed: ' . $errorMsg);
                $session->setFlashdata('error', $errorMsg);
            }
        } catch (\Exception $e) {
            log_message('error', 'User deactivation exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $session->setFlashdata('error', 'Failed to deactivate user: ' . $e->getMessage());
        }

        return redirect()->to('admin/users');
    }

    /**
     * List all courses
     */
    public function courses()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        $session = session();
        $search = $this->request->getGet('search');
        
        $db = \Config\Database::connect();
        $builder = $db->table('courses c')
            ->select('c.*, u.name as instructor_name, u.email as instructor_email')
            ->join('users u', 'u.id = c.instructor_id', 'left');
        
        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('c.title', $search)
                ->orLike('c.description', $search)
                ->orLike('u.name', $search)
                ->groupEnd();
        }
        
        $courses = $builder->orderBy('c.created_at', 'DESC')->get()->getResultArray();

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => 'admin',
            'courses' => $courses,
            'search' => $search
        ];

        return view('admin/courses/index', $data);
    }

    /**
     * Show add course form
     */
    public function addCourse()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();
        
        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => 'admin',
            'validation' => \Config\Services::validation()
        ];

        return view('admin/courses/add', $data);
    }

    /**
     * Check for schedule conflicts
     */
    protected function checkScheduleConflict($instructorId, $scheduleDay, $scheduleTime, $excludeCourseId = null)
    {
        if (empty($instructorId) || empty($scheduleDay) || empty($scheduleTime)) {
            return null; // No conflict if schedule is incomplete
        }

        $db = \Config\Database::connect();
        $builder = $db->table('courses')
            ->where('instructor_id', (int) $instructorId)
            ->where('schedule_day', $scheduleDay)
            ->where('schedule_time', $scheduleTime);

        // Exclude current course when updating
        if ($excludeCourseId !== null) {
            $builder->where('id !=', (int) $excludeCourseId);
        }

        return $builder->get()->getRowArray();
    }

    /**
     * Create new course
     */
    public function createCourse()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'title' => 'required|min_length[3]|max_length[200]',
                'description' => 'permit_empty|max_length[5000]',
                'instructor_id' => 'permit_empty|integer',
                'schedule_day' => 'permit_empty|max_length[50]',
                'schedule_time' => 'permit_empty|max_length[50]',
                'schedule_room' => 'permit_empty|max_length[100]',
                'schedule_start_date' => 'permit_empty|valid_date',
                'schedule_end_date' => 'permit_empty|valid_date'
            ];

            $messages = [
                'title' => [
                    'required' => 'Course title is required.',
                    'min_length' => 'Course title must be at least 3 characters long.',
                    'max_length' => 'Course title cannot exceed 200 characters.',
                ],
                'instructor_id' => [
                    'integer' => 'Invalid instructor selection.',
                ],
            ];

            if ($this->validate($rules, $messages)) {
                $scheduleDay = trim($this->request->getPost('schedule_day'));
                $scheduleTime = trim($this->request->getPost('schedule_time'));

                $data = [
                    'title' => trim($this->request->getPost('title')),
                    'description' => trim($this->request->getPost('description')),
                    'instructor_id' => null, // Teacher assignment is done through Assign Courses page
                    'schedule_day' => $scheduleDay ?: null,
                    'schedule_time' => $scheduleTime ?: null,
                    'schedule_room' => trim($this->request->getPost('schedule_room')) ?: null,
                    'schedule_start_date' => $this->request->getPost('schedule_start_date') ?: null,
                    'schedule_end_date' => $this->request->getPost('schedule_end_date') ?: null,
                ];

                try {
                    $insertResult = $this->courseModel->insert($data);
                    
                    if ($insertResult) {
                        $session->setFlashdata('success', 'Course created successfully.');
                        return redirect()->to('admin/courses');
                    } else {
                        $errors = $this->courseModel->errors();
                        $session->setFlashdata('error', 'Failed to create course: ' . implode(', ', $errors));
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Course creation exception: ' . $e->getMessage());
                    $session->setFlashdata('error', 'Failed to create course. Please try again.');
                }
            } else {
                $validationErrors = $this->validator->getErrors();
                $session->setFlashdata('error', 'Validation failed: ' . implode(', ', $validationErrors));
            }
        }

        return redirect()->to('admin/courses/add')->withInput();
    }

    /**
     * Show edit course form
     */
    public function editCourse($id)
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();
        $course = $this->courseModel->getCourseWithInstructor($id);

        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('admin/courses');
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => 'admin',
            'course' => $course,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/courses/edit', $data);
    }

    /**
     * Update course
     */
    public function updateCourse($id)
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();

        $course = $this->courseModel->find($id);
        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('admin/courses');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'title' => 'required|min_length[3]|max_length[200]',
                'description' => 'permit_empty|max_length[5000]',
                'instructor_id' => 'permit_empty|integer',
                'schedule_day' => 'permit_empty|max_length[50]',
                'schedule_time' => 'permit_empty|max_length[50]',
                'schedule_room' => 'permit_empty|max_length[100]',
                'schedule_start_date' => 'permit_empty|valid_date',
                'schedule_end_date' => 'permit_empty|valid_date'
            ];

            $messages = [
                'title' => [
                    'required' => 'Course title is required.',
                    'min_length' => 'Course title must be at least 3 characters long.',
                    'max_length' => 'Course title cannot exceed 200 characters.',
                ],
                'instructor_id' => [
                    'integer' => 'Invalid instructor selection.',
                ],
            ];

            if ($this->validate($rules, $messages)) {
                $scheduleDay = trim($this->request->getPost('schedule_day'));
                $scheduleTime = trim($this->request->getPost('schedule_time'));
                $currentInstructorId = $course['instructor_id'] ?? null;

                // Check for schedule conflict if course has an assigned instructor and schedule is being updated
                if (!empty($currentInstructorId) && !empty($scheduleDay) && !empty($scheduleTime)) {
                    $conflict = $this->checkScheduleConflict($currentInstructorId, $scheduleDay, $scheduleTime, $id);
                    if ($conflict) {
                        $db = \Config\Database::connect();
                        $teacher = $db->table('users')->select('name')->where('id', $currentInstructorId)->get()->getRowArray();
                        $teacherName = $teacher && isset($teacher['name']) ? $teacher['name'] : 'The teacher';
                        $session->setFlashdata('error', "Schedule conflict! {$teacherName} already has another course scheduled on {$scheduleDay} at {$scheduleTime}. Please choose a different day/time or change the teacher assignment in the Assign Courses page.");
                        return redirect()->to('admin/courses/edit/' . $id)->withInput();
                    }
                }

                $data = [
                    'title' => trim($this->request->getPost('title')),
                    'description' => trim($this->request->getPost('description')),
                    // instructor_id is not updated here - use Assign Courses page instead
                    'schedule_day' => $scheduleDay ?: null,
                    'schedule_time' => $scheduleTime ?: null,
                    'schedule_room' => trim($this->request->getPost('schedule_room')) ?: null,
                    'schedule_start_date' => $this->request->getPost('schedule_start_date') ?: null,
                    'schedule_end_date' => $this->request->getPost('schedule_end_date') ?: null,
                ];

                try {
                    $updateResult = $this->courseModel->update($id, $data);
                    
                    if ($updateResult) {
                        $session->setFlashdata('success', 'Course updated successfully.');
                        return redirect()->to('admin/courses');
                    } else {
                        $errors = $this->courseModel->errors();
                        $session->setFlashdata('error', 'Failed to update course: ' . implode(', ', $errors));
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Course update exception: ' . $e->getMessage());
                    $session->setFlashdata('error', 'Failed to update course. Please try again.');
                }
            } else {
                $validationErrors = $this->validator->getErrors();
                $session->setFlashdata('error', 'Validation failed: ' . implode(', ', $validationErrors));
            }
        }

        return redirect()->to('admin/courses/edit/' . $id)->withInput();
    }

    /**
     * Delete course
     */
    public function deleteCourse($id)
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        $session = session();
        $course = $this->courseModel->find($id);

        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('admin/courses');
        }

        try {
            $deleteResult = $this->courseModel->delete($id);
            
            if ($deleteResult) {
                $session->setFlashdata('success', 'Course deleted successfully.');
            } else {
                $session->setFlashdata('error', 'Failed to delete course.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Course deletion exception: ' . $e->getMessage());
            $session->setFlashdata('error', 'Failed to delete course. Please try again.');
        }

        return redirect()->to('admin/courses');
    }

    /**
     * Assign courses to teachers page
     */
    public function assignCourses()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        $session = session();
        $db = \Config\Database::connect();

        // Get all teachers
        $teachers = $this->userModel->where('role', 'instructor')
            ->orWhere('role', 'teacher')
            ->orderBy('name', 'ASC')
            ->findAll();

        // Get all courses with their current assignments
        $courses = $this->courseModel->getCoursesWithInstructor();

        // Get courses without assigned teachers
        $unassignedCourses = $db->table('courses')
            ->where('instructor_id IS NULL', null, false)
            ->orWhere('instructor_id', 0)
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        // Get teacher schedules for display
        $teacherSchedules = [];
        foreach ($teachers as $teacher) {
            $teacherCourses = $db->table('courses')
                ->select('id, title, schedule_day, schedule_time, schedule_room')
                ->where('instructor_id', $teacher['id'])
                ->where('schedule_day IS NOT NULL', null, false)
                ->where('schedule_time IS NOT NULL', null, false)
                ->orderBy('schedule_day', 'ASC')
                ->orderBy('schedule_time', 'ASC')
                ->get()
                ->getResultArray();
            
            $teacherSchedules[$teacher['id']] = $teacherCourses;
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => 'admin',
            'teachers' => $teachers,
            'courses' => $courses,
            'unassignedCourses' => $unassignedCourses,
            'teacherSchedules' => $teacherSchedules,
        ];

        return view('admin/courses/assign', $data);
    }

    /**
     * Assign course to teacher (AJAX)
     */
    public function assignCourseToTeacher()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $session = session();
        $courseId = (int) $this->request->getPost('course_id');
        $teacherId = $this->request->getPost('teacher_id');
        $scheduleDay = trim((string) $this->request->getPost('schedule_day'));
        $scheduleTime = trim((string) $this->request->getPost('schedule_time'));

        if (!$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID is required.']);
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found.']);
        }

        // If teacher is being assigned and schedule is provided, check for conflicts
        if (!empty($teacherId) && !empty($scheduleDay) && !empty($scheduleTime)) {
            $conflict = $this->checkScheduleConflict((int) $teacherId, $scheduleDay, $scheduleTime, $courseId);
            if ($conflict) {
                $db = \Config\Database::connect();
                $teacher = $db->table('users')->select('name')->where('id', $teacherId)->get()->getRowArray();
                $conflictCourse = $db->table('courses')->select('title')->where('id', $conflict['id'])->get()->getRowArray();
                $teacherName = $teacher && isset($teacher['name']) ? $teacher['name'] : 'The teacher';
                $conflictTitle = $conflictCourse && isset($conflictCourse['title']) ? $conflictCourse['title'] : 'another course';
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Schedule conflict! {$teacherName} already teaches '{$conflictTitle}' on {$scheduleDay} at {$scheduleTime}. Please choose a different day/time or assign a different teacher."
                ]);
            }
        }

        // Update course assignment
        $updateData = [];
        if (!empty($teacherId)) {
            $updateData['instructor_id'] = (int) $teacherId;
        } else {
            $updateData['instructor_id'] = null;
        }

        // Update schedule if provided
        if (!empty($scheduleDay)) {
            $updateData['schedule_day'] = $scheduleDay;
        }
        if (!empty($scheduleTime)) {
            $updateData['schedule_time'] = $scheduleTime;
        }

        try {
            $result = $this->courseModel->update($courseId, $updateData);
            
            if ($result) {
                $db = \Config\Database::connect();
                $course = $this->courseModel->getCourseWithInstructor($courseId);
                $teacherName = $course && isset($course['instructor_name']) ? $course['instructor_name'] : 'Unassigned';
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Course assigned successfully to {$teacherName}.",
                    'course' => $course
                ]);
            } else {
                $errors = $this->courseModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to assign course: ' . implode(', ', $errors)
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Course assignment error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while assigning the course.'
            ]);
        }
    }

    /**
     * Manage course schedules page
     */
    public function scheduleCourses()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $check;
        }

        $session = session();
        $db = \Config\Database::connect();

        // Get all courses with their current assignments and schedules
        $courses = $this->courseModel->getCoursesWithInstructor();

        // Get all teachers for reference
        $teachers = $this->userModel->where('role', 'instructor')
            ->orWhere('role', 'teacher')
            ->orderBy('name', 'ASC')
            ->findAll();

        // Get teacher schedules for display
        $teacherSchedules = [];
        foreach ($teachers as $teacher) {
            $teacherCourses = $db->table('courses')
                ->select('id, title, schedule_day, schedule_time, schedule_room')
                ->where('instructor_id', $teacher['id'])
                ->where('schedule_day IS NOT NULL', null, false)
                ->where('schedule_time IS NOT NULL', null, false)
                ->orderBy('schedule_day', 'ASC')
                ->orderBy('schedule_time', 'ASC')
                ->get()
                ->getResultArray();
            
            $teacherSchedules[$teacher['id']] = $teacherCourses;
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => 'admin',
            'courses' => $courses,
            'teachers' => $teachers,
            'teacherSchedules' => $teacherSchedules,
        ];

        return view('admin/courses/schedule', $data);
    }

    /**
     * Update course schedule (AJAX)
     */
    public function updateCourseSchedule()
    {
        $check = $this->checkAdmin();
        if ($check !== null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $courseId = (int) $this->request->getPost('course_id');
        $scheduleDay = trim((string) $this->request->getPost('schedule_day'));
        $scheduleTime = trim((string) $this->request->getPost('schedule_time'));
        $scheduleRoom = trim((string) $this->request->getPost('schedule_room'));

        if (!$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID is required.']);
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found.']);
        }

        $currentInstructorId = $course['instructor_id'] ?? null;

        // Check for schedule conflict if course has an assigned instructor and schedule is being set
        if (!empty($currentInstructorId) && !empty($scheduleDay) && !empty($scheduleTime)) {
            $conflict = $this->checkScheduleConflict($currentInstructorId, $scheduleDay, $scheduleTime, $courseId);
            if ($conflict) {
                $db = \Config\Database::connect();
                $teacher = $db->table('users')->select('name')->where('id', $currentInstructorId)->get()->getRowArray();
                $conflictCourse = $db->table('courses')->select('title')->where('id', $conflict['id'])->get()->getRowArray();
                $teacherName = $teacher && isset($teacher['name']) ? $teacher['name'] : 'The teacher';
                $conflictTitle = $conflictCourse && isset($conflictCourse['title']) ? $conflictCourse['title'] : 'another course';
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Schedule conflict! {$teacherName} already teaches '{$conflictTitle}' on {$scheduleDay} at {$scheduleTime}. Please choose a different day/time or change the teacher assignment."
                ]);
            }
        }

        // Update schedule (day, time, room only - dates are managed separately based on term)
        $updateData = [
            'schedule_day' => $scheduleDay ?: null,
            'schedule_time' => $scheduleTime ?: null,
            'schedule_room' => $scheduleRoom ?: null,
        ];

        try {
            $result = $this->courseModel->update($courseId, $updateData);
            
            if ($result) {
                $updatedCourse = $this->courseModel->getCourseWithInstructor($courseId);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Course schedule updated successfully.',
                    'course' => $updatedCourse
                ]);
            } else {
                $errors = $this->courseModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update schedule: ' . implode(', ', $errors)
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Course schedule update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating the schedule.'
            ]);
        }
    }
}

