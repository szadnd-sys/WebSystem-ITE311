<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentApprovalFields extends Migration
{
    public function up()
    {
        $fields = [];
        $db = \Config\Database::connect();

        // Get the fields that already exist in the table
        $result = $db->getFieldData('enrollments');
        $existingFields = [];
        foreach ($result as $field) {
            $existingFields[$field->name] = true;
        }

        // Only add status if it doesn't already exist
        if (!isset($existingFields['status'])) {
            $fields['status'] = [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
                'null' => false,
                'after' => 'enrollment_date'
            ];
        }

        // Only add rejection_reason if it doesn't already exist
        if (!isset($existingFields['rejection_reason'])) {
            $fields['rejection_reason'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status'
            ];
        }

        // Only add approved_at if it doesn't already exist
        if (!isset($existingFields['approved_at'])) {
            $fields['approved_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'rejection_reason'
            ];
        }

        // Only add rejected_at if it doesn't already exist
        if (!isset($existingFields['rejected_at'])) {
            $fields['rejected_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_at'
            ];
        }

        // Add columns if any need to be added
        if (!empty($fields)) {
            $this->forge->addColumn('enrollments', $fields);
        }

        // Update existing enrollments to 'approved' status (if any exist without status)
        if (isset($existingFields['status'])) {
            $this->db->query("UPDATE enrollments SET status = 'approved' WHERE status IS NULL OR status = ''");
        }
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', ['status', 'rejection_reason', 'approved_at', 'rejected_at']);
    }
}

