<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentApprovalFields extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
                'null' => false,
                'after' => 'enrollment_date'
            ],
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status'
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'rejection_reason'
            ],
            'rejected_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_at'
            ]
        ];

        $this->forge->addColumn('enrollments', $fields);

        // Update existing enrollments to 'approved' status (if any exist without status)
        $this->db->query("UPDATE enrollments SET status = 'approved' WHERE status IS NULL OR status = ''");
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', ['status', 'rejection_reason', 'approved_at', 'rejected_at']);
    }
}

