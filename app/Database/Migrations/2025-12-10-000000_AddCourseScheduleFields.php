<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseScheduleFields extends Migration
{
    public function up()
    {
        $fields = [
            'schedule_day' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'instructor_id',
                'comment' => 'Day of the week (e.g., Monday, Tuesday)'
            ],
            'schedule_time' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'schedule_day',
                'comment' => 'Time slot (e.g., 9:00 AM - 11:00 AM)'
            ],
            'schedule_room' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'schedule_time',
                'comment' => 'Room number or location'
            ],
            'schedule_start_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'schedule_room',
                'comment' => 'Course start date'
            ],
            'schedule_end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'schedule_start_date',
                'comment' => 'Course end date'
            ]
        ];

        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['schedule_day', 'schedule_time', 'schedule_room', 'schedule_start_date', 'schedule_end_date']);
    }
}

