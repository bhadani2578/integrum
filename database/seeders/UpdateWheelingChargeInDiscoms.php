<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Discom;

class UpdateWheelingChargeInDiscoms extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $values = [10, 11, 12, 13, 14, 15, 16, 17, 18, 19,20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32]; // Add more values as needed

        // Get the existing records and loop through them
        $records = Discom::all();

        foreach ($records as $key => $record) {
            // Use key to get a value from the $values array
            $newValue = $values[$key];

            // Update the new column with the new value
            $record->update(['wheeling_charge' => $newValue]);
        }
    }
}
