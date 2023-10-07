<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TodStateSlot;

class TodStateSlots extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $todstateslot = [
            ['id' => '1','state_id' => '8', 'slot' => '06:00 to 10:00'],
            ['id' => '2','state_id' => '8', 'slot' => '10:00 to 18:00'],
            ['id' => '3','state_id' => '8', 'slot' => '18:00 to 22:00'],
            ['id' => '4','state_id' => '8', 'slot' => '22:00 to 06:00'],
            ['id' => '5','state_id' => '1', 'slot' => '06:00 to 10:00'],
            ['id' => '6','state_id' => '1', 'slot' => '10:00 to 18:00'],
            ['id' => '7','state_id' => '1', 'slot' => '18:00 to 22:00'],
            ['id' => '8','state_id' => '1', 'slot' => '22:00 to 06:00'],
            ['id' => '9','state_id' => '3', 'slot' => '06:00 to 10:00'],
            ['id' => '10','state_id' => '3', 'slot' => '10:00 to 15:00'],
            ['id' => '11','state_id' => '3', 'slot' => '15:00 to 18:00'],
            ['id' => '12','state_id' => '3', 'slot' => '18:00 to 22:00'],
            ['id' => '13','state_id' => '3', 'slot' => '22:00 to 24:00'],
            ['id' => '14','state_id' => '3', 'slot' => '00:00 to 06:00'],
            ['id' => '15','state_id' => '7', 'slot' => '09:00 to 12:00'],
            ['id' => '16','state_id' => '7', 'slot' => '12:00 to 18:00'],
            ['id' => '17','state_id' => '7', 'slot' => '18:00 to 22:00'],
            ['id' => '18','state_id' => '7', 'slot' => '22:00 to 09:00']
        ];

        TodStateSlot::insert($todstateslot);
    }
}
