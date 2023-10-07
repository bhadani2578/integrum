<?php

namespace App\Imports;

use App\Models\AnnualSourceData;
use App\Models\HourlySourceData;
use App\Models\MonthlySourceData;
use App\Models\TODStateSourceData;
use App\Models\WeeklySourceData;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ImportSourceProfile implements  ToModel, WithHeadingRow, WithChunkReading
{
    use Importable;
    protected $profile_id;
    protected $granularity_level_id;
    protected $i;
    public function __construct($profile_id, $granularity_level_id)
    {
        $this->profile_id = $profile_id;
        $this->granularity_level_id = $granularity_level_id;
        $this->i = 1;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $rows)
    {
        $client_id = Session::get('client_detail')->id;
        $profile_id = $this->profile_id;
        $rows = array_filter($rows);
        
        if($this->granularity_level_id == 1){
            $year_unit = $rows['annual'];
            $proportion_unit = $rows['proportion_units_lower_on_sundays_and_holidays'];

            return AnnualSourceData::create([
                'profile_id' => $profile_id,
                'client_id' => $client_id,
                'year_unit' => $year_unit,
                'lower_consumption_unit' => $proportion_unit
            ]);

        }else if($this->granularity_level_id == 2){
            $name = $rows['months'];
            $proportion_unit = $rows['generated_units'];
            return MonthlySourceData::create([
                'profile_id' => $profile_id,
                'client_id' => $client_id,
                'name' => ($this->i <= 12) ? $this->i++ : $name,
                'consumed_unit' => $proportion_unit
            ]);
        } else if($this->granularity_level_id == 3){
            $slot = $rows[0];
            $jan = $rows['jan'];
            $feb = $rows['feb'];
            $mar = $rows['mar'];
            $apr = $rows['apr'];
            $may = $rows['may'];
            $jun = $rows['jun'];
            $jul = $rows['jul'];
            $aug = $rows['aug'];
            $sep = $rows['sep'];
            $oct = $rows['oct'];
            $nov = $rows['nov'];
            $dec = $rows['dec'];
            $unit = $rows['proportion_units_lower_on_sundays_and_holidays'];
            return TODStateSourceData::create([
                'profile_id' => $profile_id,
                'client_id' => $client_id,
                'slot' => $slot,
                'jan' => $jan,
                'feb' => $feb,
                'mar' => $mar,
                'apr' => $apr,
                'may' => $may,
                'jun' => $jun,
                'jul' => $jul,
                'aug' => $aug,
                'sep' => $sep,
                'oct' => $oct,
                'nov' => $nov,
                'dec' => $dec,
                'consumed_unit' => $unit,
            ]);
        }
        else if($this->granularity_level_id == 4)
        {
            try {
                if (isset($rows['generated_units'])) {
                    $consumedUnit = strval($rows['generated_units']);
                } else {
                    // Handle the case where 'generated_units' key is not present
                    $consumedUnit = null; // or any default value you prefer
                }
                return WeeklySourceData::create([
                    'profile_id' => $profile_id,
                    'client_id' => $client_id,
                    'weeks' => $rows['week'],
                    'consumed_unit' => $consumedUnit
                ]);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }
        else if($this->granularity_level_id == 5) {
            try {
                if (isset($rows['generated_units'])) {
                    $consumedUnit = strval($rows['generated_units']);
                } else {
                    // Handle the case where 'generated_units' key is not present
                    $consumedUnit = null; // or any default value you prefer
                }
              
            
                return HourlySourceData::create([
                    'profile_id' => $profile_id,
                    'client_id' => $client_id,
                    'hours' => $rows['hour'],
                    'consumed_unit' => $consumedUnit
                ]);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
            // return HourlySourceData::create([
            //     'profile_id' => $profile_id,
            //     'client_id' => $client_id,
            //     'hours' => $rows['hour'],
            //     'consumed_unit' => $rows['generated_units']
            // ]);
        }


    }
    public function chunkSize(): int
    {
            return 500; // Set the desired chunk size here
    }

}
