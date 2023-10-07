<?php

namespace Database\Seeders;

use App\Models\ApplicablePeriods;
use App\Models\ApplicationPeriod;
use App\Models\LockingPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationDataInsertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ApplicablePeriods::insert([
            ['id' => '1','name' => 'TOD'],
            ['id'=>'2','name'=>'Daily'],
            ['id'=>'3','name' => 'Monthly'],
            ['id'=>'4','name'=>'Annually']
        ]);
        for ($i = 1; $i <= 20; $i++) {
            LockingPeriod::create([
                'locking_number' => $i,
            ]);
        }

    }
}
