<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Search extends Controller
{
    /**
     * Display the search page with all courses
     */
    public function index()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to use the search feature.');
            return redirect()->to('login');
        }
        
        $role = strtolower((string) $session->get('role'));
        $userId = (int) $session->get('user_id');
        
        $db = \Config\Database::connect();
        
        // Get all courses with instructor names for initial load
        $courses = [];
        $enrolledCourseIds = [];
        
        try {
            // Get user's enrolled course IDs if student
            if ($role === 'student') {
                $enrollments = $db->table('enrollments')
                    ->select('course_id')
                    ->where('user_id', $userId)
                    ->get()
                    ->getResultArray();
                $enrolledCourseIds = array_column($enrollments, 'course_id');
            }
            
            // Get all courses with instructor names
            $courses = $db->table('courses c')
                ->select('c.id, c.title, c.description, c.created_at, u.name as instructor_name')
                ->join('users u', 'u.id = c.instructor_id', 'left')
                ->orderBy('c.created_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Failed to fetch courses for search: ' . $e->getMessage());
        }
        
        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role,
            'courses' => $courses,
            'enrolledCourseIds' => $enrolledCourseIds,
        ];
        
        return view('search/index', $data);
    }
    
    /**
     * Server-side search endpoint (AJAX) - Searches courses using SQL LIKE queries
     */
    public function courses()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to search courses.'
            ]);
        }
        
        $query = $this->request->getGet('q');
        $query = trim((string) ($query ?? ''));
        
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
                ->select('c.id, c.title, c.description, c.created_at, u.name as instructor_name')
                ->join('users u', 'u.id = c.instructor_id', 'left');
            
            // Build search query using LIKE for title, description, and instructor name
            $builder->groupStart();
            $builder->like('c.title', $query, 'both');
            $builder->orLike('c.description', $query, 'both');
            $builder->orLike('u.name', $query, 'both');
            $builder->groupEnd();
            
            $builder->orderBy('c.created_at', 'DESC');
            
            $results = $builder->get()->getResultArray();
            
            // For students, mark which courses they're enrolled in
            if ($role === 'student' && !empty($results)) {
                $courseIds = array_column($results, 'id');
                $enrollments = $db->table('enrollments')
                    ->select('course_id')
                    ->where('user_id', $userId)
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
            log_message('error', 'Search error: ' . $e->getMessage());
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
    
    /**
     * Server-side search for materials (AJAX)
     */
    public function materials()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to search materials.'
            ]);
        }
        
        $query = trim((string) $this->request->getGet('q') ?? '');
        
        if (empty($query)) {
            return $this->response->setJSON([
                'success' => true,
                'materials' => [],
                'count' => 0
            ]);
        }
        
        $role = strtolower((string) $session->get('role'));
        $userId = (int) $session->get('user_id');
        
        $db = \Config\Database::connect();
        $results = [];
        
        try {
            $builder = $db->table('materials m')
                ->select('m.id, m.file_name, m.original_name, m.created_at, c.id as course_id, c.title as course_title')
                ->join('courses c', 'c.id = m.course_id', 'left');
            
            // Search in file name and course title using LIKE
            $builder->groupStart();
            $builder->like('m.file_name', $query, 'both');
            $builder->orLike('m.original_name', $query, 'both');
            $builder->orLike('c.title', $query, 'both');
            $builder->groupEnd();
            
            // Students can only see materials from courses they're enrolled in
            if ($role === 'student') {
                $builder->whereIn('c.id', function($subBuilder) use ($userId) {
                    return $subBuilder->select('course_id')
                        ->from('enrollments')
                        ->where('user_id', $userId);
                });
            }
            
            $builder->orderBy('m.created_at', 'DESC');
            
            $results = $builder->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Material search error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred during search. Please try again.'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'materials' => $results,
            'count' => count($results)
        ]);
    }
}

