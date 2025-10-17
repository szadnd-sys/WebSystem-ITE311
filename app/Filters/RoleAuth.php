<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access this page.');
            return redirect()->to('login');
        }
        
        $userRole = strtolower((string) $session->get('role'));
        $currentPath = $request->getUri()->getPath();
        // Normalize path to start with a single leading slash for prefix checks
        if ($currentPath === null) {
            $currentPath = '/';
        }
        if ($currentPath === '') {
            $currentPath = '/';
        }
        if ($currentPath[0] !== '/') {
            $currentPath = '/' . $currentPath;
        }
        // Support environments where index.php is in the URL
        if (strpos($currentPath, '/index.php/') === 0) {
            $currentPath = substr($currentPath, strlen('/index.php'));
        } elseif ($currentPath === '/index.php') {
            $currentPath = '/';
        }

        // Remove base subfolder (e.g., /ITE311-EMBEN) so checks are app-root-relative
        $basePath = parse_url(base_url(), PHP_URL_PATH) ?? '';
        if ($basePath !== '') {
            $basePath = rtrim($basePath, '/');
            if ($basePath !== '' && strpos($currentPath, $basePath . '/') === 0) {
                $currentPath = substr($currentPath, strlen($basePath));
            } elseif ($currentPath === $basePath) {
                $currentPath = '/';
            }
        }
        
        // Define role-based access rules
        $accessRules = [
            'admin' => ['/admin', '/announcements'],
            'teacher' => ['/teacher', '/announcements'],
            'student' => ['/student', '/announcements']
        ];
        
        // Check if user's role has access to current path
        $hasAccess = false;
        
        if (isset($accessRules[$userRole])) {
            foreach ($accessRules[$userRole] as $allowedPath) {
                // ensure allowedPath also has leading slash
                if ($allowedPath === '') {
                    continue;
                }
                if ($allowedPath[0] !== '/') {
                    $allowedPath = '/' . $allowedPath;
                }
                if (strpos($currentPath, $allowedPath) === 0) {
                    $hasAccess = true;
                    break;
                }
            }
        }
        
        // If no access, redirect to announcements with error message
        if (!$hasAccess) {
            $session->setFlashdata('access_error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/announcements');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
