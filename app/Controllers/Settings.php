<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Settings extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Check if user is logged in
     */
    protected function checkAuth()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access this page.');
            return redirect()->to('login');
        }
        
        return null;
    }

    /**
     * Show settings page
     */
    public function index()
    {
        $check = $this->checkAuth();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();
        $userId = (int) $session->get('user_id');
        
        $user = $this->userModel->find($userId);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('dashboard');
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => strtolower((string) $session->get('role')),
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];

        return view('settings/index', $data);
    }

    /**
     * Update profile (name and email)
     */
    public function updateProfile()
    {
        $check = $this->checkAuth();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();
        $userId = (int) $session->get('user_id');

        $user = $this->userModel->find($userId);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('settings');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-Z\s\-\'\.]+$/]',
                'email' => 'required|valid_email|is_unique[users.email,id,' . $userId . ']'
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

                $data = [
                    'name' => $name,
                    'email' => $email
                ];

                try {
                    $updateResult = $this->userModel->update($userId, $data);
                    
                    if ($updateResult) {
                        // Update session with new name and email
                        $session->set('user_name', $name);
                        $session->set('user_email', $email);
                        
                        $session->setFlashdata('profile_success', 'Profile updated successfully.');
                        return redirect()->to('settings');
                    } else {
                        $errors = $this->userModel->errors();
                        $session->setFlashdata('error', 'Failed to update profile: ' . implode(', ', $errors));
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Profile update exception: ' . $e->getMessage());
                    $session->setFlashdata('error', 'Failed to update profile. Please try again.');
                }
            } else {
                $validationErrors = $this->validator->getErrors();
                $session->setFlashdata('error', 'Validation failed: ' . implode(', ', $validationErrors));
            }
        }

        return redirect()->to('settings')->withInput();
    }

    /**
     * Update password
     */
    public function updatePassword()
    {
        $check = $this->checkAuth();
        if ($check !== null) {
            return $check;
        }

        helper(['form']);
        $session = session();
        $userId = (int) $session->get('user_id');

        $user = $this->userModel->find($userId);
        if (!$user) {
            $session->setFlashdata('error', 'User not found.');
            return redirect()->to('settings');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min_length[6]',
                'confirm_password' => 'required|matches[new_password]'
            ];

            if ($this->validate($rules)) {
                $currentPassword = $this->request->getPost('current_password');
                $newPassword = $this->request->getPost('new_password');

                // Verify current password
                if (!password_verify($currentPassword, $user['password'])) {
                    $session->setFlashdata('error', 'Current password is incorrect.');
                    return redirect()->to('settings');
                }

                // Update password
                try {
                    $updateResult = $this->userModel->update($userId, [
                        'password' => $newPassword // Model will hash it
                    ]);
                    
                    if ($updateResult) {
                        $session->setFlashdata('password_success', 'Password changed successfully!');
                        return redirect()->to('settings');
                    } else {
                        $errors = $this->userModel->errors();
                        $session->setFlashdata('error', 'Failed to update password: ' . implode(', ', $errors));
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Password update exception: ' . $e->getMessage());
                    $session->setFlashdata('error', 'Failed to update password. Please try again.');
                }
            } else {
                $validationErrors = $this->validator->getErrors();
                $session->setFlashdata('error', 'Validation failed: ' . implode(', ', $validationErrors));
            }
        }

        return redirect()->to('settings');
    }
}

