<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnnouncementsTable extends Migration
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
            'instructor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'material_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Reference to materials table if announcement is about a material upload',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('course_id');
        $this->forge->addKey('created_at');

        // Add foreign keys
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('instructor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('material_id', 'materials', 'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('announcements');
    }

    public function down()
    {
        $this->forge->dropTable('announcements', true);
    }
}



