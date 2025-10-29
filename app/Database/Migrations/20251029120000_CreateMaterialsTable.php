<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaterialsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('course_id');

        // Add foreign key to courses table if it exists
        try {
            $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        } catch (\Throwable $e) {
            // In case the courses table or FK cannot be created now, proceed without halting migration
        }

        $this->forge->createTable('materials', true);
    }

    public function down()
    {
        // Drop the table and foreign key if present
        if ($this->db->tableExists('materials')) {
            try {
                $this->forge->dropForeignKey('materials', 'materials_course_id_foreign');
            } catch (\Throwable $e) {
                // ignore if FK name differs or not present
            }
            $this->forge->dropTable('materials', true);
        }
    }
}


