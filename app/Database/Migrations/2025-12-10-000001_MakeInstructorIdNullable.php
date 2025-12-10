<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeInstructorIdNullable extends Migration
{
    public function up()
    {
        // Make instructor_id nullable to allow courses without assigned teachers initially
        $fields = [
            'instructor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ];

        $this->forge->modifyColumn('courses', $fields);
    }

    public function down()
    {
        // Revert to not null (but this might fail if there are null values)
        $fields = [
            'instructor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
        ];

        $this->forge->modifyColumn('courses', $fields);
    }
}

