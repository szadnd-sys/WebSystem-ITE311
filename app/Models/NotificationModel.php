<?php
namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'title',
        'message',
        'link_url',
        'is_read',
    ];

    public function getUnreadCountForUser(int $userId): int
    {
        return (int) $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    public function getRecentForUser(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    public function markAllAsRead(int $userId): int
    {
        $this->where('user_id', $userId)->where('is_read', 0);
        return $this->set(['is_read' => 1])->update();
    }

    public function markAsReadByIdForUser(int $notificationId, int $userId): bool
    {
        $this->where('id', $notificationId)->where('user_id', $userId);
        return (bool) $this->set(['is_read' => 1])->update();
    }

    public function deleteByIdForUser(int $notificationId, int $userId): bool
    {
        $this->where('id', $notificationId)->where('user_id', $userId);
        return (bool) $this->delete();
    }

    public function deleteAllForUser(int $userId): int
    {
        $this->where('user_id', $userId);
        return (int) $this->delete();
    }
}


