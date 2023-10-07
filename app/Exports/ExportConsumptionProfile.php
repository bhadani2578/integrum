<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use App\Models\HourlyConsumptionData;
use App\Models\AnnualConsumptionData;
use App\Models\MonthlyConsumptionData;
use App\Models\TodConsumptionData;
use App\Models\WeeklyConsumptionData;
use App\Models\TodStateConsumptionData;
use App\Models\ConsumptionDayShift;
use App\Models\ConsumptionTod;
use Carbon\Carbon;

class ExportConsumptionProfile implements FromCollection, WithHeadings
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
        try{
            // $intervals = [];
            $intervals = new Collection();
            if($this->data['granularity_level'] == 1){
                $checking_working_day = ConsumptionDayShift::where('profile_id', $this->data['profile_id'])->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $data = AnnualConsumptionData::where('profile_id', $this->data['profile_id'])->get();
                $consumption_tod = ConsumptionTod::where('profile_id', $this->data['profile_id'])->get();

                $yearlyConsumption = $data[0]->year_unit; // Replace with your yearly consumption value
                $lower_unit = $data[0]->lower_consumption_unit;

                $holidayUnit = $lower_unit / 100;

                $year = 2023; // Replace with the year
                $hoursPerDay = 24;
                $daysPerYear = Carbon::createFromDate($year, 12, 31)->dayOfYear;
                $totalHours = $daysPerYear * $hoursPerDay;
                $conume_unit = $yearlyConsumption / $totalHours;
                for ($j = 0; $j < $totalHours; $j++)
                {
                    $slotsPerHour = 60 / $this->data['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    // $unitsPerSlot = $conume_unit / $slotsPerHour;
                    $slot = [];
                    for ($k = 0; $k < $slotsPerHour; $k++) {

                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($j)->addMinutes($k * $this->data['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);

                        $dayOfWeek = $startSlot->dayOfWeek;
                        // $dayOfWeek = Carbon::create($year, 1, 1, 0, 0, 0)->addHours($j)->dayOfWeek;
                        // $hoursOfDay = Carbon::create($year, 1, 1, 0, 0, 0)->addHours($j)->hour;
                        $hoursOfDay = $startSlot->hour;

                        if(count($checking_working_day) > 0  && count($consumption_tod) > 0){
                            //  for both tod and working day
                            foreach ($consumption_tod as $element) {
                                $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                $startSlotHour = $startSlot->copy()->setTime($element->tod_start, 0, 0);
                                $endSlotHour = $startSlot->copy()->setTime($element->tod_end, 0, 0);
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
                                $unitsPerSlot = ((($yearlyConsumption /  $daysPerYear ) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotsPerHour;
                                // $crossCheck[] = $unitsPerSlot;
                            }else{

                                $unitsPerSlot = (((($yearlyConsumption /  $daysPerYear ) * (1 - $holidayUnit)) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotsPerHour;
                                // $crossCheck[] = $unitsPerSlot;
                            }
                        }else if(count($checking_working_day) == 0 && count($consumption_tod) > 0 ){
                            //  for tod calculation
                            foreach ($consumption_tod as $element) {
                                $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                $startSlotHour = $startSlot->copy()->setTime($element->tod_start, 0, 0);
                                $endSlotHour = $startSlot->copy()->setTime($element->tod_end, 0, 0);
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

                            $unitsPerSlot = ((($yearlyConsumption /  $daysPerYear ) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotsPerHour;
                            // $crossCheck[] = $unitsPerSlot;
                        }else if(count($checking_working_day) > 0 && count($consumption_tod) == 0){
                             //  for working day calculation
                            $startDay = $checking_working_day[0]->day_start;
                            $endDay = $checking_working_day[0]->day_end;

                            if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                $unitsPerSlot = $conume_unit / $slotsPerHour;
                                // $crossCheck[] = $unitsPerSlot;
                            }else{
                                $unitsPerSlot = ($conume_unit * (1 - $holidayUnit)) / $slotsPerHour;
                                // $crossCheck[] = $unitsPerSlot;
                            }
                        }else{
                            //  for without tod and working day calculation
                            $unitsPerSlot = $conume_unit / $slotsPerHour;
                        }

                        $interval = [
                            'Month' => $startSlot->format('m'),
                            'Day' => $startSlot->format('d'),
                            'Hour' => $startSlot->format('H'),
                            'Slot' => $startSlot->format('H:i') . ' - ' . $endSlot->format('H:i'),
                            'Consumption Unit' => round($unitsPerSlot)
                        ];

                        $intervals[] = $interval;
                    }
                }
                return $intervals;
            }elseif ($this->data['granularity_level'] == 2) {
                // Code for monthly consumption
                $data = MonthlyConsumptionData::where('profile_id', $this->data['profile_id'])
                    ->where('name', '!=', 'Proportion units lower on sundays and holidays')
                    ->select('name', 'consumed_unit')
                    ->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $this->data['profile_id'])->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $lower_unit = MonthlyConsumptionData::where('profile_id', $this->data['profile_id'])->where('name', '=', 'Proportion units lower on sundays and holidays')->select('consumed_unit')->first()->consumed_unit;
                $consumption_tod = ConsumptionTod::where('profile_id', $this->data['profile_id'])->get();

                $holidayUnit = $lower_unit / 100;

                foreach ($data as $value) {
                    $currentYear = 2023;
                    $startOfMonth = Carbon::create($currentYear, $value['name'], 1, 0, 0, 0);
                    $endOfMonth = Carbon::create($currentYear, $value['name'], 1, 23, 59, 59)->endOfMonth();
                    $totalHours = $startOfMonth->diffInHours($endOfMonth);
                    $totalDays = $startOfMonth->daysInMonth;
                    $slot = [];

                    for ($a = 0; $a < $totalHours; $a++) {
                        $slotsPerHour = 60 / $this->data['chunk_time']; // 4 slots per hour (15 minutes per slot)
                        // $unitsPerSlot = ($value['consumed_unit'] / $totalHours) / $slotsPerHour;

                        for ($k = 0; $k < $slotsPerHour; $k++) {
                            $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($a)->addMinutes($k * $this->data['chunk_time']);
                            $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);

                            $dayOfWeek = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($a)->dayOfWeek;
                            $hoursOfDay = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($a)->hour;

                            if(count($checking_working_day) > 0  && count($consumption_tod) > 0){
                                //  for both tod and working day
                                foreach ($consumption_tod as $element) {
                                    $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                    $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                    $startSlotHour = $startSlot->copy()->setTime($element->tod_start, 0, 0);
                                    $endSlotHour = $startSlot->copy()->setTime($element->tod_end, 0, 0);
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
                                    $unitsPerSlot = ((($value['consumed_unit'] /  $totalDays) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }else{

                                    $unitsPerSlot = (((($value['consumed_unit'] /  $totalDays ) * (1 - $holidayUnit)) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }
                            }else if(count($checking_working_day) == 0 && count($consumption_tod) > 0 ){
                                //  for tod calculation
                                foreach ($consumption_tod as $element) {
                                    $startHour = Carbon::createFromTimeString($element->tod_start)->hour;
                                    $endHour = Carbon::createFromTimeString($element->tod_end)->hour;

                                    $startSlotHour = $startSlot->copy()->setTime($element->tod_start, 0, 0);
                                    $endSlotHour = $startSlot->copy()->setTime($element->tod_end, 0, 0);
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

                                $unitsPerSlot = ((($value['consumed_unit'] /  $totalDays ) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotsPerHour;
                                // $crossCheck[] = $unitsPerSlot;
                            }else if(count($checking_working_day) > 0 && count($consumption_tod) == 0){

                                $startDay = $checking_working_day[0]->day_start;
                                $endDay = $checking_working_day[0]->day_end;

                                if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                    $unitsPerSlot = ($value['consumed_unit'] / $totalHours) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }else{
                                    $unitsPerSlot = (($value['consumed_unit'] / $totalHours) * (1 - $holidayUnit)) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }
                            }else{
                                $unitsPerSlot = ($value['consumed_unit'] / $totalHours) / $slotsPerHour;
                            }

                            $intervals[] = [
                                'Month' => $startOfMonth->format('m'),
                                'Day' => $startSlot->format('d'),
                                'Hour' => $startSlot->format('H'),
                                'Slot' => $startSlot->format('H:i') . ' - ' . $endSlot->format('H:i'),
                                'Consumption Unit' => round($unitsPerSlot)
                            ];
                        }
                    }
                    $intervals[] = [
                        'Month' => $startOfMonth->format('m'),
                        'Day' => $startSlot->format('d'),
                        'Hour' => $startSlot->format('H'),
                        'Slot' => $startSlot->format('H:i') . ' - ' . $endSlot->format('H:i'),
                        'Consumption Unit' => round($unitsPerSlot)
                    ];
                }

                return $intervals;
            }elseif ($this->data['granularity_level'] == 3) {
                $data = TodStateConsumptionData::where('profile_id', $this->data['profile_id'])->select('slot', 'jan', 'feb', 'mar','apr','may','jun','jul','aug','sep','oct','nov','dec','consumed_unit')->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $this->data['profile_id'])->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $consumption_tod = ConsumptionTod::where('profile_id', $this->data['profile_id'])->get();

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

                                // $unit = (($consumedUnit / $numDays) / $diffInHours) / $slotsPerHour;
                                $chunkData = [
                                    'month' => $month < 10 ? '0'.$month : $month ,
                                    'day' => $currentDate->format('d'),
                                    'hours' => $start->format('H'),
                                    'slots' => $slot_time,
                                    'unit' => round($unitsPerSlot),
                                ];
                                $chunks[] = $chunkData;
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
                $intervals[] = $chunks;
                return $intervals;
            }else if($this->data['granularity_level'] == 4){
                $data = HourlyConsumptionData::where('profile_id', $this->data['profile_id'])->select('hours', 'consumed_unit')->get();

                foreach ($data as $key => $value) {

                    $slotsPerHour = 60 / $this->data['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    $unitsPerSlot = $value['consumed_unit'] / $slotsPerHour;
                    $slot = [];
                    for ($i = 0; $i < $slotsPerHour; $i++) {

                        // $startSlot = Carbon::create(2023, 1, 1)->addHours($value['hours'])->addMinutes($i * $this->data['chunk_time']);
                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($value['hours'] - 1)->addMinutes($i * $this->data['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($this->data['chunk_time']);

                        $interval = [
                            'Month' => $startSlot->format('m'),
                            'Day' => $startSlot->format('d'),
                            'Hour' => $startSlot->format('H'),
                            'Slot' => $startSlot->format('H:i') . ' - ' . $endSlot->format('H:i'),
                            'Consumption Unit' => round($unitsPerSlot)
                        ];

                        $intervals[] = $interval;
                    }
                }
                return $intervals;
            }else if ($this->data['granularity_level'] == 5) {
                $data = WeeklyConsumptionData::where('profile_id', $this->data['profile_id'])->where('weeks', '!=', 'Proportion units lower on sundays and holidays')
                    ->select('weeks', 'consumed_unit')
                    ->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $this->data['profile_id'])->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $lower_unit = WeeklyConsumptionData::where('profile_id', $this->data['profile_id'])->where('weeks', '=', 'Proportion units lower on sundays and holidays')->select('consumed_unit')->first()->consumed_unit;
                $consumption_tod = ConsumptionTod::where('profile_id', $this->data['profile_id'])->get();

                $holidayUnit = $lower_unit / 100;

                foreach ($data as $key => $value) {
                    $week = $value['weeks'];
                    $consumedUnit = ($value['consumed_unit'] / 7) / 24;
                    $slotPerHour = 60 / $this->data['chunk_time'];

                    for ($day = 0; $day < 7; $day++) {
                        for ($hour = 0; $hour < 24; $hour++) {
                            $startTime = Carbon::create(2023, 1, 1)->addDays($day)->setHour($hour)->setMinute(0)->setSecond(0);
                            $startDate = Carbon::now()->setISODate(2023, $week)->startOfWeek();

                            $minute = 0;

                            for ($slot = 0; $slot < $slotPerHour; $slot++) {
                                // $minute = $slot * $this->data['chunk_time'];
                                $chunkStart = $startTime->copy()->addMinutes($minute)->format('H:i');
                                $chunkEnd = $startTime->copy()->addMinutes($minute + $this->data['chunk_time'])->format('H:i');

                                $dayOfWeek = $startTime->dayOfWeek;
                                $hoursOfDay = $startTime->hour;;

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

                                $interval = [
                                    'Month' => $startDate->month,
                                    'Day' => $startTime->format('d'),
                                    'Hour' => $startTime->format('H'),
                                    'Slot' => $chunkStart . ' - ' . $chunkEnd,
                                    'Consumption Unit' => round($unitsPerSlot)
                                ];
                                $minute += $this->data['chunk_time'];
                                $intervals[] = $interval;
                            }
                        }

                    }

                }

                return $intervals;
            }


        } catch (\Exception $e) {
            dd($e);
            return response()->json(['status' => false]);
        }

    }

    public function headings(): array
    {
        // Define the column headings for the exported file
        return [
            'Month',
            'Day',
            'Hour',
            'Slot',
            'Consumption Unit(KWH)',

        ];
    }
}
