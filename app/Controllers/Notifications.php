<?php
namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Notifications extends Controller
{
    private function requireAuth(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        return null;
    }

    public function unreadCount()
    {
        if ($redirect = $this->requireAuth()) {
            return $this->response->setJSON(['success' => false, 'count' => 0]);
        }
        $userId = (int) session('user_id');
        $model = new NotificationModel();
        $count = 0;
        try {
            $count = $model->getUnreadCountForUser($userId);
        } catch (\Throwable $e) {
            $count = 0;
        }
        return $this->response->setJSON(['success' => true, 'count' => $count]);
    }

    public function list()
    {
        if ($redirect = $this->requireAuth()) {
            return $this->response->setJSON(['success' => false, 'notifications' => []]);
        }
        $userId = (int) session('user_id');
        $limit = max(1, (int) ($this->request->getGet('limit') ?? 10));
        $model = new NotificationModel();
        $items = [];
        try {
            $items = $model->getRecentForUser($userId, $limit);
        } catch (\Throwable $e) {
            $items = [];
        }
        return $this->response->setJSON(['success' => true, 'notifications' => $items]);
    }

    public function markAllRead()
    {
        if ($redirect = $this->requireAuth()) {
            return $this->response->setJSON(['success' => false]);
        }
        $userId = (int) session('user_id');
        $model = new NotificationModel();
        $updated = 0;
        try {
            $updated = $model->markAllAsRead($userId);
        } catch (\Throwable $e) {
            $updated = 0;
        }
        return $this->response->setJSON(['success' => true, 'updated' => (int) $updated]);
    }

    public function markRead($id)
    {
        if ($redirect = $this->requireAuth()) {
            return $this->response->setJSON(['success' => false]);
        }
        $notificationId = (int) $id;
        $userId = (int) session('user_id');
        $model = new NotificationModel();
        $ok = false;
        try {
            $ok = $model->markAsReadByIdForUser($notificationId, $userId);
        } catch (\Throwable $e) {
            $ok = false;
        }
        return $this->response->setJSON(['success' => (bool) $ok]);
    }
}


