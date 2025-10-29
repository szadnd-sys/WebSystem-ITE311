<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;
use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

class Materials extends Controller
{
    public function upload($course_id)
    {
        $session = session();
        helper(['form']);

        // Require login and role check (admin or teacher)
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        $role = strtolower((string) $session->get('role'));
        if (!in_array($role, ['admin', 'teacher'], true)) {
            $session->setFlashdata('access_error', 'Access Denied: Only instructors/admins can upload.');
            return redirect()->to('/dashboard');
        }

        $courseId = (int) $course_id;
        $materialModel = new MaterialModel();

        if ($this->request->getMethod() === 'POST') {
            $validationRules = [
                'userfile' => [
                    'label' => 'Material File',
                    'rules' => 'uploaded[userfile]|max_size[userfile,10240]|ext_in[userfile,pdf,doc,docx,ppt,pptx,txt,zip]',
                ],
            ];

            if (!$this->validate($validationRules)) {
                return view('materials/upload', [
                    'course_id' => $courseId,
                    'validation' => $this->validator,
                    'materials' => $materialModel->getMaterialsByCourse($courseId),
                ]);
            }

            $file = $this->request->getFile('userfile');
            if ($file && $file->isValid()) {
                // Ensure target directory exists (writable/uploads/materials)
                $targetDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'materials';
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0775, true);
                }
                // Store in writable/uploads/materials with random name
                $storedPath = $file->store('materials'); // e.g., materials/xxxxx.ext under writable/uploads
                if ($storedPath === false) {
                    $session->setFlashdata('error', 'Failed to store the uploaded file.');
                    return redirect()->back();
                }

                $data = [
                    'course_id' => $courseId,
                    'file_name' => $file->getClientName(),
                    'file_path' => $storedPath, // relative to writable/uploads
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $materialId = $materialModel->insertMaterial($data);
                if ($materialId) {
                    // Create announcement for enrolled students
                    try {
                        $announcementModel = new AnnouncementModel();
                        $instructorId = (int) $session->get('user_id');
                        $announcementModel->createMaterialAnnouncement(
                            $courseId,
                            $instructorId,
                            $materialId,
                            $file->getClientName()
                        );
                    } catch (\Exception $e) {
                        // Log error but don't fail the upload
                        log_message('error', 'Failed to create announcement: ' . $e->getMessage());
                    }
                    
                    $session->setFlashdata('success', 'Material uploaded successfully. Announcement sent to enrolled students.');
                } else {
                    // Rollback stored file if DB insert failed
                    @unlink(WRITEPATH . 'uploads/' . $storedPath);
                    $session->setFlashdata('error', 'Failed to save material record.');
                }

                // Redirect back to role-appropriate upload page
                $redirectBase = ($role === 'teacher') ? '/teacher/course/' : '/admin/course/';
                return redirect()->to($redirectBase . $courseId . '/upload');
            }

            // Surface specific upload error if available
            if (isset($file) && $file !== null) {
                $session->setFlashdata('error', 'Upload failed: ' . $file->getErrorString() . ' (' . $file->getError() . ')');
            } else {
                $session->setFlashdata('error', 'Invalid upload.');
            }
            return redirect()->back();
        }

        // GET: Show upload form and list
        return view('materials/upload', [
            'course_id' => $courseId,
            'materials' => $materialModel->getMaterialsByCourse($courseId),
            'validation' => null,
        ]);
    }

    public function delete($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        $role = strtolower((string) $session->get('role'));
        if (!in_array($role, ['admin', 'teacher'], true)) {
            $session->setFlashdata('access_error', 'Access Denied: Only instructors/admins can delete.');
            return redirect()->to('/dashboard');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find((int) $material_id);
        if (!$material) {
            $session->setFlashdata('error', 'Material not found.');
            return redirect()->back();
        }

        $courseId = (int) $material['course_id'];
        // Delete file
        $fullPath = WRITEPATH . 'uploads/' . $material['file_path'];
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
        // Delete DB record
        $materialModel->delete((int) $material_id);
        $session->setFlashdata('success', 'Material deleted.');
        $redirectBase = ($role === 'teacher') ? '/teacher/course/' : '/admin/course/';
        return redirect()->to($redirectBase . $courseId . '/upload');
    }

    public function download($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $materialModel = new MaterialModel();
        $enrollmentModel = new EnrollmentModel();

        $material = $materialModel->find((int) $material_id);
        if (!$material) {
            return $this->response->setStatusCode(404, 'Material not found');
        }

        $userId = (int) $session->get('user_id');
        $courseId = (int) $material['course_id'];

        // Only enrolled students (or teacher/admin) can download
        $role = strtolower((string) $session->get('role'));
        $allowed = in_array($role, ['admin', 'teacher'], true) || $enrollmentModel->isAlreadyEnrolled($userId, $courseId);
        if (!$allowed) {
            $session->setFlashdata('access_error', 'Access Denied: You must be enrolled to download this material.');
            return redirect()->to('/dashboard');
        }

        $fullPath = WRITEPATH . 'uploads/' . $material['file_path'];
        if (!is_file($fullPath)) {
            return $this->response->setStatusCode(404, 'File not found');
        }

        return $this->response->download($fullPath, null)->setFileName($material['file_name']);
    }
}


