<?php

namespace App\Imports;

use App\Models\TodConsumptionData;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AnnualConsumptionData;
use App\Models\HourlyConsumptionData;
use App\Models\MonthlyConsumptionData;
use App\Models\WeeklyConsumptionData;
use App\Models\TodStateConsumptionData;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;


class ImportConsumptionProfile implements  ToModel, WithHeadingRow, WithChunkReading
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
            
            $annual_year = $rows['annual'];
            $proportion_unit = $rows['proportion_units_lower_on_sundays_and_holidays'];
            return AnnualConsumptionData::create([
                'profile_id' => $profile_id,
                'client_id' => $client_id,
                'year_unit' => $annual_year,
                'lower_consumption_unit' => $proportion_unit
            ]);

        }else if($this->granularity_level_id == 2){
            $name = $rows['months'];
            $proportion_unit = $rows['consumed_units'];
            return MonthlyConsumptionData::create([
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
            return TodStateConsumptionData::create([
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
        }else if($this->granularity_level_id == 4) {
            return HourlyConsumptionData::create([
                'profile_id' => $profile_id,
                'client_id' => $client_id,
                'hours' => $rows['hour'],
                'consumed_unit' => $rows['consumed_units']
            ]);
        }
        else if($this->granularity_level_id == 5)
        {
            return WeeklyConsumptionData::create([
                'profile_id' => $profile_id,
                'client_id' => $client_id,
                'weeks' => $rows['week'],
                'consumed_unit' => $rows['consumed_units']
            ]);
        }

    }

    public function chunkSize(): int
    {
        return 500; // Set the desired chunk size here
    }

}
