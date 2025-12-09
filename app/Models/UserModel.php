<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false; // timestamps handled by DB defaults in migration

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Ensure password is hashed before saving.
     * Only hashes when a non-empty plain password is provided in data.
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']) || !is_array($data['data'])) {
            return $data;
        }

        if (array_key_exists('password', $data['data'])) {
            $password = (string) $data['data']['password'];
            if ($password !== '' && substr($password, 0, 4) !== '$2y$') {
                // Avoid rehashing if already a bcrypt hash
                $data['data']['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
        }

        return $data;
    }
}

