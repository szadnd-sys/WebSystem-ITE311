<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $users = $db->table('users')->select('id')->limit(5)->get()->getResultArray();
        if (empty($users)) {
            return;
        }
        $rows = [];
        foreach ($users as $u) {
            $uid = (int) ($u['id'] ?? 0);
            if (!$uid) continue;
            $rows[] = [
                'user_id' => $uid,
                'title' => 'Welcome to ITE311!'
                , 'message' => 'Your account is ready. Explore your dashboard.'
                , 'link_url' => base_url('dashboard')
                , 'is_read' => 0
                , 'created_at' => date('Y-m-d H:i:s', time() - 3600)
                , 'updated_at' => null
            ];
            $rows[] = [
                'user_id' => $uid,
                'title' => 'New materials available'
                , 'message' => 'Check out the latest uploaded course materials.'
                , 'link_url' => base_url('student/dashboard')
                , 'is_read' => 0
                , 'created_at' => date('Y-m-d H:i:s', time() - 600)
                , 'updated_at' => null
            ];
        }
        if (!empty($rows)) {
            $db->table('notifications')->insertBatch($rows);
        }
    }
}



