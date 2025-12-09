<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Admin extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
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
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'matches[password]',
                'role' => 'required|in_list[student,instructor]' // Remove admin from allowed roles
            ];

            if ($this->validate($rules)) {
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
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email,id,' . $id . ']',
                'role' => 'required|in_list[student,instructor]' // Remove admin from allowed roles
            ];

            // Password is optional on update
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $rules['password'] = 'min_length[6]';
                $rules['password_confirm'] = 'matches[password]';
            }

            if ($this->validate($rules)) {
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
}

