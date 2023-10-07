<?php

namespace App\Exports;

use App\Models\AnnualConsumptionData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use App\Models\AnnualSourceData;
use App\Models\ConsumptionDayShift;
use App\Models\ConsumptionProfile;
use App\Models\ConsumptionTod;
use App\Models\HourlyConsumptionData;
use App\Models\HourlySourceData;
use App\Models\Mapping;
use App\Models\MonthlyConsumptionData;
use App\Models\MonthlySourceData;
use App\Models\SourceProfile;
use App\Models\SourceTod;
use App\Models\TodStateConsumptionData;
use App\Models\TodStateSlot;
use App\Models\TODStateSourceData;
use App\Models\WeeklyConsumptionData;
use App\Models\WeeklySourceData;
use Carbon\Carbon;

class ExportMapping implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        try {
            // $intervals = [];
            $leftoverDemandAndLaps = new Collection();
            $id = $this->data['profile_id'];
            $mapping = Mapping::find($id);
            $consumption_id = $mapping->consumption_point_id;
            $source_id = $mapping->source_point_id;
            $ConsumptionProfile = ConsumptionProfile::where('id',$consumption_id)->select('granularity_level_id')->first();
            $SourceProfile = SourceProfile::where('id',$source_id)->select('granularity_level_id')->first();
            if($ConsumptionProfile->granularity_level_id == 1)
            {
                $data = AnnualConsumptionData::where('profile_id', $consumption_id)->get();
                $yearlyConsumption = $data[0]->lower_consumption_unit; // Replace with your yearly consumption value
                $year = $data[0]->annual_year; // Replace with the year
                $hoursPerDay = 24;
                $daysPerYear = Carbon::createFromDate($year, 12, 31)->dayOfYear;
                $totalHours = $daysPerYear * $hoursPerDay;
                $conume_unit = $yearlyConsumption / $totalHours;
                for ($j = 0; $j < $totalHours; $j++)
                {
                    $slotsPerHour = 60 / $this->data['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    $unitsPerSlot = $conume_unit / $slotsPerHour;
                    $hour = str_pad($j, 2, '0', STR_PAD_LEFT);
                    $slot = [];
                    for ($k = 0; $k < $slotsPerHour; $k++) {

                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($j)->addMinutes($k * $this->data['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);


                        $slot = [
                            'month' => $startSlot->format('m'),
                            'day' => $startSlot->format('d'),
                            'hours' => $hour,
                            'start' => $startSlot->format('H:i'),
                            'end' => $endSlot->format('H:i'),
                            'consumption' => round($unitsPerSlot, 3)
                        ];

                        $Consumption[] = $slot;
                    }

                }


        }
        else if($ConsumptionProfile->granularity_level_id == 2)
        {
            $data = MonthlyConsumptionData::where('profile_id', $consumption_id)->where('name', '!=', 'Proportion units lower on sundays and holidays')->select('name', 'consumed_unit')->get();
            foreach($data as $key => $value){
                $currentYear = 2023;

                $startOfMonth = Carbon::create($currentYear, $value['name'], 1, 0, 0, 0);
                $endOfMonth = Carbon::create($currentYear, $value['name'], 1, 23, 59, 59)->endOfMonth();

                $totalHours = $startOfMonth->diffInHours($endOfMonth);

                $slot = [];
                for ($a = 0; $a < $totalHours; $a++)
                {

                    $slotsPerHour = 60 / $this->data['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    $unitsPerSlot = ($value['consumed_unit'] / $totalHours) / $slotsPerHour;

                    for ($k = 0; $k < $slotsPerHour; $k++) {
                        // echo $k;
                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($a)->addMinutes($k * $this->data['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);

                    }
                    $slot = [
                        'month' => $startOfMonth->format('m'),
                        'day' => $startSlot->format('d'),
                        'start' => $startSlot->format('H:i'),
                        'end' => $endSlot->format('H:i'),
                        'hours' => $startSlot->format('H'),
                        'consumption' => round($unitsPerSlot, 3)

                    ];
                    $Consumption[] = $slot;
                }
            }

        }
        else if($ConsumptionProfile->granularity_level_id == 3)
        {
            $data = TodStateConsumptionData::where('profile_id', $consumption_id)->select('slot', 'jan', 'feb', 'mar','apr','may','jun','jul','aug','sep','oct','nov','dec','consumed_unit')->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $consumption_id)->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $consumption_tod = ConsumptionTod::where('profile_id', $consumption_id)->get();

                $chunks = [];
                foreach ($data as $entry) {
                    $slot = $entry['slot'];
                    $holidayUnit = $entry['consumed_unit'] / 100;
                    // $consumedUnit = $entry['jan'];   // need to create loop for all month
                    for ($month = 1; $month <= 12; $month++) {

                        // Get the month name based on the current iteration
                        $monthName = Carbon::create(null, $month)->format('M');

                        $consumedUnit = $entry[strtolower($monthName)];

                        // Extract the start and end time from the time slot
                        preg_match('/(\d+:\d+)\sto\s(\d+:\d+)/', $slot, $matches);


                        $targetYear = 2023;
                        $targetMonth = $month;  // add month number for all reocrds
                        // Get the total number of days in the target month
                        $numDays = Carbon::parse($targetYear . '-' . $targetMonth)->daysInMonth;

                        // Iterate over each day within the target month
                        for ($day = 1; $day <= $numDays; $day++) {

                            $currentDate = Carbon::create($targetYear, $targetMonth, $day);

                            // Check if the current day is within the desired range (6 to 8 hours)
                            $start = $currentDate->copy()->setTime($matches[1], 0, 0);
                            $end = $currentDate->copy()->setTime($matches[2], 0, 0);
                            if ($end < $start) {
                                $end->modify('+1 day');
                            }
                            $diffInHours = $start->diffInHours($end);

                            $slotsPerHour = 60 / $this->data['chunk_time'];
                            // Generate chunks within the specified hour range for the current day
                            while ($start < $end) {
                                $chunkEnd = $start->copy()->addMinutes($this->data['chunk_time']);
                                $slot_time = $start->format('H:i') . '-' . $chunkEnd->format('H:i');

                                // $unit =  / $slotsPerHour;

                                $dayOfWeek = $currentDate->dayOfWeek;
                                $hoursOfDay = $start->hour;

                                if(count($checking_working_day) > 0  && count($consumption_tod) > 0){
                                    //  for both tod and working day
                                    foreach ($consumption_tod as $element) {
                                        $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                        $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                        $startSlotHour = $start->copy()->setTime($element->tod_start, 0, 0);
                                        $endSlotHour = $start->copy()->setTime($element->tod_end, 0, 0);
                                        if ($endSlotHour < $startSlotHour) {
                                            $endSlotHour->modify('+1 day');
                                        }
                                        $diffInHours = $startSlotHour->diffInHours($endSlotHour);


                                        if ($startHour <= $endHour) {
                                            if ($hoursOfDay >= $startHour && $hoursOfDay <= $endHour) {
                                                $matchingElement = $element;
                                                break;
                                            }
                                        } else {
                                            if ($hoursOfDay >= $startHour || $hoursOfDay <= $endHour) {
                                                $matchingElement = $element;
                                                break;
                                            }
                                        }
                                    }

                                    $startDay = $checking_working_day[0]->day_start;
                                    $endDay = $checking_working_day[0]->day_end;

                                    if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                        $unitsPerSlot = ((($consumedUnit * ($matchingElement->tod_value/ 100)) / $numDays ) / $diffInHours ) / $slotsPerHour;
                                        // $crossCheck[] = $unitsPerSlot;
                                    }else{

                                        $unitsPerSlot = (((($consumedUnit * ($matchingElement->tod_value/ 100)) / $numDays ) / $diffInHours ) * (1 - $holidayUnit)) / $slotsPerHour;
                                        // dd($unitsPerSlot);
                                        // $crossCheck[] = $unitsPerSlot;
                                    }
                                }else if(count($checking_working_day) == 0 && count($consumption_tod) > 0 ){
                                    //  for tod calculation
                                    foreach ($consumption_tod as $element) {
                                        $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                        $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                        $startSlotHour = $start->copy()->setTime($element->tod_start, 0, 0);
                                        $endSlotHour = $start->copy()->setTime($element->tod_end, 0, 0);
                                        if ($endSlotHour < $startSlotHour) {
                                            $endSlotHour->modify('+1 day');
                                        }
                                        $diffInHours = $startSlotHour->diffInHours($endSlotHour);

                                        if ($startHour <= $endHour) {
                                            if ($hoursOfDay >= $startHour && $hoursOfDay <= $endHour) {
                                                $matchingElement = $element;
                                                break;
                                            }
                                        } else {
                                            if ($hoursOfDay >= $startHour || $hoursOfDay <= $endHour) {
                                                $matchingElement = $element;
                                                break;
                                            }
                                        }
                                    }


                                    $unitsPerSlot = ((($consumedUnit * ($matchingElement->tod_value/ 100)) / $numDays ) / $diffInHours ) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }else if(count($checking_working_day) > 0 && count($consumption_tod) == 0){

                                    $startDay = $checking_working_day[0]->day_start;
                                    $endDay = $checking_working_day[0]->day_end;

                                    if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                        $unitsPerSlot = (($consumedUnit / $numDays) / $diffInHours) / $slotsPerHour;
                                        // $crossCheck[] = $unitsPerSlot;
                                    }else{
                                        $unitsPerSlot = ((($consumedUnit / $numDays) / $diffInHours) * (1 - $holidayUnit)) / $slotsPerHour;
                                        // $crossCheck[] = $unitsPerSlot;
                                    }
                                }else{
                                    $unitsPerSlot = (($consumedUnit / $numDays) / $diffInHours) / $slotsPerHour;
                                }

                                $Consumption = [
                                    'month' => $month < 10 ? '0'.$month : $month ,
                                    'day' => $currentDate->format('d'),
                                    'hours' => $start->format('H'),
                                    'slots' => $slot_time,
                                    'consumption' => round($unitsPerSlot, 3),
                                ];
                                $Consumption[] = $Consumption;
                                $start->addMinutes($this->data['chunk_time']);
                            }
                        }
                    }

                }

                usort($chunks, function ($a, $b) {
                    // Sort by month first
                    $monthComparison = strcmp($a['month'], $b['month']);
                    if ($monthComparison !== 0) {
                        return $monthComparison;
                    }

                    // Sort by day
                    $dayComparison = strcmp($a['day'], $b['day']);
                    if ($dayComparison !== 0) {
                        return $dayComparison;
                    }

                    // Sort by hours
                    return strcmp($a['hours'], $b['hours']);
                });
        }
        elseif ($ConsumptionProfile->granularity_level_id == 4) {
            $data = HourlyConsumptionData::where('profile_id', $consumption_id)
                ->select('hours', 'consumed_unit')
                ->get();

            foreach ($data as $key => $value) {
                $slotsPerHour = 60 / $this->data['chunk_time']; // 4 slots per hour (15 minutes per slot)
                $unitsPerSlot = $value['consumed_unit'] / $slotsPerHour;

                for ($i = 0; $i < $slotsPerHour; $i++) {
                    $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)
                        ->addHours($value['hours'] - 1)
                        ->addMinutes($i * $this->data['chunk_time']);
                    $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);

                    $Consumption[] = [
                        'month' => $startSlot->format('n'),
                        'day' => $startSlot->format('d'),
                        'hours' => $startSlot->format('H'),
                        'start' => $startSlot->format('H:i'),
                        'end' => $endSlot->format('H:i'),
                        'consumption' => round($unitsPerSlot, 3),
                    ];
                }
            }
        }
        else if ($ConsumptionProfile->granularity_level_id == 5)
        {
                $data = WeeklyConsumptionData::where('profile_id', $consumption_id)->where('weeks', '!=', 'Proportion units lower on sundays and holidays')->select('weeks', 'consumed_unit')->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $consumption_id)->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $lower_unit = WeeklyConsumptionData::where('profile_id', $consumption_id)->where('weeks', '=', 'Proportion units lower on sundays and holidays')->select('consumed_unit')->first()->consumed_unit;
                $consumption_tod = ConsumptionTod::where('profile_id', $consumption_id)->get();

                $holidayUnit = $lower_unit / 100;

                foreach ($data as $key => $value) {

                    $week = $value['weeks'];
                    $consumedUnit = ($value['consumed_unit'] / 7) / 24;
                    $slotPerHour = 60 / $this->data['chunk_time'];


                    // $consumedUnitPerInterval = $consumedUnit / $slotPerHour;
                    for ($day = 0; $day < 7; $day++){
                        for ($hour = 0; $hour < 24; $hour++) {
                            $startTime = Carbon::create(2023, 1, 1)->addDays($day)->setHour($hour)->setMinute(0)->setSecond(0);
                            $startDate = Carbon::now()->setISODate(2023, $week)->startOfWeek();

                            $slot = [];

                            $minute = 0;
                            for ($slot = 0; $slot < $slotPerHour; $slot++) {
                                $chunkStart = $startTime->copy()->addMinutes($minute)->format('H:i'); // Format: Hour:Minute
                                $chunkEnd = $startTime->copy()->addMinutes($minute + $this->data['chunk_time'])->format('H:i'); // Format: Hour:Minute

                                $dayOfWeek = $startTime->dayOfWeek;
                                $hoursOfDay = $startTime->hour;

                                if(count($checking_working_day) > 0  && count($consumption_tod) > 0){
                                    //  for both tod and working day
                                    foreach ($consumption_tod as $element) {
                                        $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                        $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                        $startSlotHour = $startTime->copy()->setTime($element->tod_start, 0, 0);
                                        $endSlotHour = $startTime->copy()->setTime($element->tod_end, 0, 0);
                                        if ($endSlotHour < $startSlotHour) {
                                            $endSlotHour->modify('+1 day');
                                        }
                                        $diffInHours = $startSlotHour->diffInHours($endSlotHour);

                                        if ($startHour <= $endHour) {
                                            if ($hoursOfDay >= $startHour && $hoursOfDay <= $endHour) {
                                                $matchingElement = $element;
                                                break;
                                            }
                                        } else {
                                            if ($hoursOfDay >= $startHour || $hoursOfDay <= $endHour) {
                                                $matchingElement = $element;
                                                break;
                                            }
                                        }
                                    }
                                    $startDay = $checking_working_day[0]->day_start;
                                    $endDay = $checking_working_day[0]->day_end;

                                    if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                        $unitsPerSlot = ((($consumedUnit) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotPerHour;
                                        // $crossCheck[] = $unitsPerSlot;
                                    }else{

                                        $unitsPerSlot = (((($consumedUnit ) * (1 - $holidayUnit)) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotPerHour;
                                        // $crossCheck[] = $unitsPerSlot;
                                    }
                                }else if(count($checking_working_day) == 0 && count($consumption_tod) > 0 ){
                                    //  for tod calculation
                                    foreach ($consumption_tod as $element) {
                                        $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                        $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                        $startSlotHour = $startTime->copy()->setTime($element->tod_start, 0, 0);
                                        $endSlotHour = $startTime->copy()->setTime($element->tod_end, 0, 0);
                                        if ($endSlotHour < $startSlotHour) {
                                            $endSlotHour->modify('+1 day');
                                        }
                                        $diffInHours = $startSlotHour->diffInHours($endSlotHour);

                                        if ($startHour <= $endHour) {
                                            if ($hoursOfDay >= $startHour && $hoursOfDay <= $endHour) {
                                                $matchingElement = $element;
                                                break;
                                            }
                                        } else {
                                            if ($hoursOfDay >= $startHour || $hoursOfDay <= $endHour) {
                                                $matchingElement = $element;
                                                break;
                                            }
                                        }
                                    }

                                    $unitsPerSlot = ((($consumedUnit) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }else if(count($checking_working_day) > 0 && count($consumption_tod) == 0){

                                    $startDay = $checking_working_day[0]->day_start;
                                    $endDay = $checking_working_day[0]->day_end;

                                    if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                        $unitsPerSlot = $consumedUnit / $slotPerHour;

                                        // $crossCheck[] = $unitsPerSlot;
                                    }else{
                                        $unitsPerSlot = ($consumedUnit * (1 - $holidayUnit)) / $slotPerHour;

                                        // $crossCheck[] = $unitsPerSlot;
                                    }
                                }else{
                                    $unitsPerSlot = $consumedUnit / $slotPerHour;
                                }

                                // $slots[$slot] = [
                                //     'start' => $chunkStart,
                                //     'end' => $chunkEnd,
                                //     'consumption' => round($consumedUnitPerInterval, 3)
                                // ];

                                $Consumption[] = [
                                    'month' => $startDate->month,
                                    'day' => $startTime->format('d'),
                                    'hours' => $startTime->format('H'),
                                    'start' => $chunkStart ,
                                    'end'=> $chunkEnd,
                                    'consumption' => round($unitsPerSlot, 3)
                                ];
                                $minute += $this->data['chunk_time'];

                            }

                        }
                    }
                }


        }

        if($SourceProfile->granularity_level_id == 1)
        {
            $data = AnnualSourceData::where('profile_id', $source_id)->get();
            $yearlyConsumption = $data[0]->year_unit; // Replace with your yearly consumption value
            $year = 2023; // Replace with the year
            $hoursPerDay = 24;
            $daysPerYear = Carbon::createFromDate($year, 12, 31)->dayOfYear;
            $totalHours = $daysPerYear * $hoursPerDay;
            $conume_unit = $yearlyConsumption / $totalHours;
            for ($j = 0; $j < $totalHours; $j++)
            {
                $slotsPerHour = 60 / $this->data['chunk_time'];// 4 slots per hour (15 minutes per slot)
                $unitsPerSlot = $conume_unit / $slotsPerHour;
                $hour = str_pad($j, 2, '0', STR_PAD_LEFT);
                $slot = [];
                for ($k = 0; $k < $slotsPerHour; $k++) {

                    $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($j)->addMinutes($k * $this->data['chunk_time']);
                    $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);


                    $slot = [
                        'month' => $startSlot->format('m'),
                        'day' => $startSlot->format('d'),
                        'hours' => $hour,
                        'start' => $startSlot->format('H:i'),
                        'end' => $endSlot->format('H:i'),
                        'consumption' => round($unitsPerSlot, 3)
                    ];

                    $intervals[] = $slot;
                }

            }


        }
        else if($SourceProfile->granularity_level_id == 2)
        {
            $data = MonthlySourceData::where('profile_id', $source_id)
            ->where('name', '!=', 'Proportion units lower on sundays and holidays')
            ->select('name', 'consumed_unit')
            ->get();

        foreach ($data as $key => $value) {
            $currentYear = 2023;
            $startOfMonth = Carbon::create($currentYear, $value['name'], 1, 0, 0, 0);
            $endOfMonth = Carbon::create($currentYear, $value['name'], 1, 23, 59, 59)->endOfMonth();

            $totalHours = $startOfMonth->diffInHours($endOfMonth);

            for ($a = 0; $a < $totalHours; $a++) {
                $slotsPerHour = 60 / $this->data['chunk_time']; // 4 slots per hour (15 minutes per slot)
                $unitsPerSlot = ($value['consumed_unit'] / $totalHours) / $slotsPerHour;

                for ($k = 0; $k < $slotsPerHour; $k++) {
                    $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)
                        ->addHours($a)
                        ->addMinutes($k * $this->data['chunk_time']);
                    $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);

                    $slot = [
                        'month' => $startOfMonth->format('m'),
                        'day' => $startSlot->format('d'),
                        'start' => $startSlot->format('H:i'),
                        'end' => $endSlot->format('H:i'),
                        'hours' => $startSlot->format('H'),
                        'consumption' => round($unitsPerSlot, 3),
                    ];
                    $intervals[] = $slot;
                }
            }
        }
    } elseif ($ConsumptionProfile->granularity_level_id == 4) {
        $data = HourlyConsumptionData::where('profile_id', $consumption_id)
            ->select('hours', 'consumed_unit')
            ->get();

        foreach ($data as $key => $value) {
            $slotsPerHour = 60 / $this->data['chunk_time']; // 4 slots per hour (15 minutes per slot)
            $unitsPerSlot = $value['consumed_unit'] / $slotsPerHour;

            for ($i = 0; $i < $slotsPerHour; $i++) {
                $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)
                    ->addHours($value['hours'] - 1)
                    ->addMinutes($i * $this->data['chunk_time']);
                $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);

                $Consumption[] = [
                    'month' => $startSlot->format('n'),
                    'day' => $startSlot->format('d'),
                    'hours' => $startSlot->format('H'),
                    'start' => $startSlot->format('H:i'),
                    'end' => $endSlot->format('H:i'),
                    'consumption' => round($unitsPerSlot, 3),
                ];
            }
        }
        }
        else if($SourceProfile->granularity_level_id == 3)
        {
            $data = TODStateSourceData::where('profile_id', $source_id)->select('slot', 'jan', 'feb', 'mar','apr','may','jun','jul','aug','sep','oct','nov','dec','consumed_unit')->get();
            $checking_working_day = ConsumptionDayShift::where('profile_id', $source_id)->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
            $consumption_tod = SourceTod::where('profile_id', $source_id)->get();

            $chunks = [];
            foreach ($data as $entry) {
                $slot = $entry['slot'];
                $holidayUnit = $entry['consumed_unit'] / 100;
                // $consumedUnit = $entry['jan'];   // need to create loop for all month
                for ($month = 1; $month <= 12; $month++) {

                    // Get the month name based on the current iteration
                    $monthName = Carbon::create(null, $month)->format('M');

                    $consumedUnit = $entry[strtolower($monthName)];

                    // Extract the start and end time from the time slot
                    preg_match('/(\d+:\d+)\sto\s(\d+:\d+)/', $slot, $matches);


                    $targetYear = 2023;
                    $targetMonth = $month;  // add month number for all reocrds
                    // Get the total number of days in the target month
                    $numDays = Carbon::parse($targetYear . '-' . $targetMonth)->daysInMonth;

                    // Iterate over each day within the target month
                    for ($day = 1; $day <= $numDays; $day++) {

                        $currentDate = Carbon::create($targetYear, $targetMonth, $day);

                        // Check if the current day is within the desired range (6 to 8 hours)
                        $start = $currentDate->copy()->setTime($matches[1], 0, 0);
                        $end = $currentDate->copy()->setTime($matches[2], 0, 0);
                        if ($end < $start) {
                            $end->modify('+1 day');
                        }
                        $diffInHours = $start->diffInHours($end);

                        $slotsPerHour = 60 / $this->data['chunk_time'];
                        // Generate chunks within the specified hour range for the current day
                        while ($start < $end) {
                            $chunkEnd = $start->copy()->addMinutes($this->data['chunk_time']);
                            $slot_time = $start->format('H:i') . '-' . $chunkEnd->format('H:i');

                            // $unit =  / $slotsPerHour;

                            $dayOfWeek = $currentDate->dayOfWeek;
                            $hoursOfDay = $start->hour;

                            if(count($checking_working_day) > 0  && count($consumption_tod) > 0){
                                //  for both tod and working day
                                foreach ($consumption_tod as $element) {
                                    $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                    $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                    $startSlotHour = $start->copy()->setTime($element->tod_start, 0, 0);
                                    $endSlotHour = $start->copy()->setTime($element->tod_end, 0, 0);
                                    if ($endSlotHour < $startSlotHour) {
                                        $endSlotHour->modify('+1 day');
                                    }
                                    $diffInHours = $startSlotHour->diffInHours($endSlotHour);


                                    if ($startHour <= $endHour) {
                                        if ($hoursOfDay >= $startHour && $hoursOfDay <= $endHour) {
                                            $matchingElement = $element;
                                            break;
                                        }
                                    } else {
                                        if ($hoursOfDay >= $startHour || $hoursOfDay <= $endHour) {
                                            $matchingElement = $element;
                                            break;
                                        }
                                    }
                                }

                                $startDay = $checking_working_day[0]->day_start;
                                $endDay = $checking_working_day[0]->day_end;

                                if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                    $unitsPerSlot = ((($consumedUnit * ($matchingElement->tod_value/ 100)) / $numDays ) / $diffInHours ) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }else{

                                    $unitsPerSlot = (((($consumedUnit * ($matchingElement->tod_value/ 100)) / $numDays ) / $diffInHours ) * (1 - $holidayUnit)) / $slotsPerHour;
                                    // dd($unitsPerSlot);
                                    // $crossCheck[] = $unitsPerSlot;
                                }
                            }else if(count($checking_working_day) == 0 && count($consumption_tod) > 0 ){
                                //  for tod calculation
                                foreach ($consumption_tod as $element) {
                                    $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                    $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                    $startSlotHour = $start->copy()->setTime($element->tod_start, 0, 0);
                                    $endSlotHour = $start->copy()->setTime($element->tod_end, 0, 0);
                                    if ($endSlotHour < $startSlotHour) {
                                        $endSlotHour->modify('+1 day');
                                    }
                                    $diffInHours = $startSlotHour->diffInHours($endSlotHour);

                                    if ($startHour <= $endHour) {
                                        if ($hoursOfDay >= $startHour && $hoursOfDay <= $endHour) {
                                            $matchingElement = $element;
                                            break;
                                        }
                                    } else {
                                        if ($hoursOfDay >= $startHour || $hoursOfDay <= $endHour) {
                                            $matchingElement = $element;
                                            break;
                                        }
                                    }
                                }


                                $unitsPerSlot = ((($consumedUnit * ($matchingElement->tod_value/ 100)) / $numDays ) / $diffInHours ) / $slotsPerHour;
                                // $crossCheck[] = $unitsPerSlot;
                            }else if(count($checking_working_day) > 0 && count($consumption_tod) == 0){

                                $startDay = $checking_working_day[0]->day_start;
                                $endDay = $checking_working_day[0]->day_end;

                                if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                    $unitsPerSlot = (($consumedUnit / $numDays) / $diffInHours) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }else{
                                    $unitsPerSlot = ((($consumedUnit / $numDays) / $diffInHours) * (1 - $holidayUnit)) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }
                            }else{
                                $unitsPerSlot = (($consumedUnit / $numDays) / $diffInHours) / $slotsPerHour;
                            }

                            $intervalsData = [
                                'month' => $month < 10 ? '0'.$month : $month ,
                                'day' => $currentDate->format('d'),
                                'hours' => $start->format('H'),
                                'start' =>$start->format('H:i'),
                                'end' => $chunkEnd->format('H:i'),
                                'consumption' => round($unitsPerSlot, 3),
                            ];
                            $intervals[] = $intervalsData;
                            $start->addMinutes($this->data['chunk_time']);
                        }
                    }
                }

            }

            usort($intervals, function ($a, $b) {
                // Sort by month first
                $monthComparison = strcmp($a['month'], $b['month']);
                if ($monthComparison !== 0) {
                    return $monthComparison;
                }

                // Sort by day
                $dayComparison = strcmp($a['day'], $b['day']);
                if ($dayComparison !== 0) {
                    return $dayComparison;
                }

                // Sort by hours
                return strcmp($a['hours'], $b['hours']);
            });

        }
        else if($SourceProfile->granularity_level_id == 4)
        {
            $data = WeeklySourceData::where('profile_id', $source_id)->select('weeks', 'consumed_unit')->get();
            foreach ($data as $key => $value) {
                $week = $value['week'];
                $consumedUnit = ($value['consumed_unit'] / 7) / 24;
                $slotPerHour = 60 / $this->data['chunk_time'];
                $consumedUnitPerInterval = $consumedUnit / $slotPerHour;
                for ($day = 0; $day < 7; $day++){
                    for ($hour = 0; $hour < 24; $hour++) {
                        $startTime = Carbon::create(2023, 1, 1)->addDays($day)->setHour($hour)->setMinute(0)->setSecond(0);
                        $minute = 0;
                        for ($slot = 0; $slot < $slotPerHour; $slot++) {
                            $chunkStart = $startTime->copy()->addMinutes($minute)->format('H:i'); // Format: Hour:Minute
                            $chunkEnd = $startTime->copy()->addMinutes($minute + $this->data['chunk_time'])->format('H:i'); // Format: Hour:Minute
                            $minute = $slot * $this->data['chunk_time'];
                            $interval = [
                                'month' => $startTime->format('m'),
                                'day' => $startTime->format('d'),
                                'hours' => $startTime->format('H'),
                                'start' => $chunkStart,
                                'end' => $chunkEnd,
                                'consumption' => round($consumedUnitPerInterval, 3)
                            ];
                            $intervals[] = $interval;
                        }

                    }
                }
            }


        }
        elseif($SourceProfile->granularity_level_id == 5)
        {
            $data = HourlySourceData::where('profile_id', $source_id)->select('hours', 'consumed_unit')->get();
            foreach ($data as $key => $value) {

                $slotsPerHour = 60 / $this->data['chunk_time'];// 4 slots per hour (15 minutes per slot)
                $unitsPerSlot = $value['consumed_unit'] / $slotsPerHour;
                $slot = [];
                for ($i = 0; $i < $slotsPerHour; $i++) {

                    // $startSlot = Carbon::create(2023, 1, 1)->addHours($value['hours'])->addMinutes($i * $request['chunk_time']);
                    $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($value['hours'] - 1)->addMinutes($i * $this->data['chunk_time']);
                    $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);


                    $intervals[] = [
                        'month' => $startSlot->format('m'),
                        'day' => $startSlot->format('d'),
                        'hours' => $startSlot->format('H'),
                        'start' => $startSlot->format('H:i'),
                        'end' => $endSlot->format('H:i'),
                        'consumption' => round($unitsPerSlot, 3)
                    ];


                }

            }
        }
        $iterationCount = min(count($Consumption), count($intervals));
        if($this->data['granularity_level'] == 3)
        {
            for($i = 1; $i<=12;$i++)
            {
               $consumedUnit = 0;
               $intervalsUnit = 0;
               for ($key = 0; $key < $iterationCount; $key++)
               {
                   if($i == $Consumption[$key]['month'])
                   {
                         $consumedUnit += $Consumption[$key]['consumption'];

                   }
                   if($i == $intervals[$key]['month'])
                   {
                       $intervalsUnit += $intervals[$key]['consumption'];

                   }
               }
               $leftover = $consumedUnit - $intervalsUnit;

               $slot = [
                   'month' => $i,
                   'consumption_consumption_unit' => round($consumedUnit,3),
                   'intervals_consumption_unit' => round($intervalsUnit,3),
                   'leftover_demand' => $leftover > 0 ? round($leftover, 3) : 0,
                   'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0,
               ];

               $leftoverDemandAndLaps[] = $slot;
           }

           // Return the JSON response
           return $leftoverDemandAndLaps;
        }
        if($this->data['granularity_level'] == 1 || $this->data['granularity_level'] == 4 || $this->data['granularity_level'] == 5)
        {

            for ($key = 0; $key < $iterationCount; $key++) {
                if (isset($Consumption[$key], $intervals[$key])) {
                $leftover = $Consumption[$key]['consumption'] - $intervals[$key]['consumption'];
                $slot = [
                    'month' => $Consumption[$key]['month'] ?? null,
                    'day' => $Consumption[$key]['day'] ?? null,
                    'hours' => $Consumption[$key]['hours'] ?? null,
                    'slot' => $Consumption[$key]['start'] ?? null .'-'. $Consumption[$key]['end'] ?? null,
                    'consumption_consumption_unit' => round($Consumption[$key]['consumption'] ?? null,3),
                    'intervals_consumption_unit' => round($intervals[$key]['consumption'] ?? null,3),
                    'leftover_demand' => $leftover > 0 ? round($leftover, 3) : 0,
                    'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0,
                ];

                if ($slot['laps_unit'] === null) {
                    $slot['laps_unit'] = 0;
                }
                else if ($slot['leftover_demand'] === null) {
                    $slot['leftover_demand'] = 0;
                }

                $leftoverDemandAndLaps[] = $slot;
            }
            }
            return $leftoverDemandAndLaps;
        }
        if($this->data['granularity_level'] == 2)
        {
            $consumption_state = ConsumptionProfile::where('id', $consumption_id)->select('state_id')->first();
            $state_slots = TodStateSlot::where('state_id', $consumption_state->state_id)->get();

            // Initialize slot sums
            $slotSums = [];
            $slotConsumptions = []; // Track consumption units by slot
            $slotSources = []; // Track source consumption units by slot

            foreach ($state_slots as $state_slot) {
                $slot = $state_slot['slot'];
                $slotSums[$slot] = 0;
                $slotConsumptions[$slot] = 0;
                $slotSources[$slot] = 0;
            }
            // Loop through all hours (0 to 8759)
            for ($hour = 0; $hour < 8760; $hour++) {
                $data = $Consumption[$hour] ?? null;
                $source = $intervals[$hour]['consumption'] ?? null;
                $consumption = $Consumption[$hour]['consumption'] ?? null;

                if ($data) {
                    $hoursOfDay = $data['hours'];

                    foreach ($state_slots as $state_slot) {
                        $slot = $state_slot['slot'];
                        preg_match('/(\d+):\d+\sto\s(\d+):\d+/', $slot, $matches);
                        $startHour = intval($matches[1]);
                        $endHour = intval($matches[2]);

                        if (($hoursOfDay >= $startHour && $hoursOfDay < $endHour) || ($startHour > $endHour && ($hoursOfDay >= $startHour || $hoursOfDay < $endHour))) {
                            $slotSums[$slot] += $data['consumption'];
                            $slotConsumptions[$slot] += $consumption;
                            $slotSources[$slot] += $source;
                            break;
                        }
                    }
                }
            }

            // Calculate the yearly consumption sum for each slot
            $yearlySlotSums = [];
            foreach ($slotSums as $slot => $sum) {
            $yearlySlotSums[$slot] = $sum * 8760;
                if($SourceProfile->granularity_level_id ==1)
                {
                    $slotConsumptions[$slot] = $sum * 365 ;
                }
            }
            if($SourceProfile->granularity_level_id ==1)
            {
                foreach ($slotSources as $slot => $sum) {
                    $yearlySlotSums[$slot] = $sum * 8760;
                    $slotSources[$slot] = $sum * 365 ;
                }
            }


            $final_array = [];
            // Display the slot-wise yearly consumption sums
            foreach ($yearlySlotSums as $slot => $sum) {
                $leftover = $slotConsumptions[$slot] - $slotSources[$slot];
                $slotData = [
                    'slot' => $slot,
                    'consumption_consumption_unit' => round($slotConsumptions[$slot],3),
                    'intervals_consumption_unit' => round($slotSources[$slot],3),
                    'leftover_demand' => $leftover > 0 ? round($leftover, 3) : 0,
                    'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0,
                ];

                $leftoverDemandAndLaps[] = $slotData;
            }
            return $leftoverDemandAndLaps;
        }

        } catch (\Exception $e) {
            dd($e);
            return response()->json(['status' => false]);
        }
    }

    public function headings(): array
    {
        // Define the column headings for the exported file
        $headings= [
            'Month'
           

        ];
        if ($this->data['granularity_level'] == 1 || $this->data['granularity_level'] == 4 || $this->data['granularity_level'] == 5) {
            $headings = array_merge($headings, ['Day', 'Hour','Slot',  'Consumption Unit',
            'Source Unit',
            'leftover_demand',
            'laps_unit']);
        }else if($this->data['granularity_level'] == 2 || $this->data['granularity_level'] == 3){
            $headings = array_merge($headings, [ 'Consumption Unit',
            'Source Unit',
            'leftover_demand',
            'laps_unit']);
        }
        return $headings;

    }
}
