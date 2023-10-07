<?php

namespace App\Http\Controllers;

use App\Exports\ExportMapping;
use App\Models\AnnualConsumptionData;
use App\Models\AnnualSourceData;
use App\Models\Client;
use App\Models\ConsumptionDayShift;
use App\Models\ConsumptionProfile;
use App\Models\ConsumptionTod;
use App\Models\HourlyConsumptionData;
use App\Models\HourlySourceData;
use App\Models\Mapping;
use App\Models\MappingPriority;
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
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class MappingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sessionId = Session::get('client_detail');
        $mapping = Mapping::with('consumption_profile','source_profile')->where('client_id',$sessionId->id)->orderBy('consumption_point_id', 'asc')->get();
        $consumption_list = ConsumptionProfile::all()->sortByDesc('created_at')->where('client_id',$sessionId->id);
        $client_list = Client::get();
        return view('mapping.index',  ['client_list' => $client_list, 'current_client' => $sessionId,'mapping' => $mapping, 'consumption_list' => $consumption_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $current_client = Session::get('client_detail');
        $client_list = Client::get();
        $consumption_list = ConsumptionProfile::all()->sortByDesc('created_at')->where('client_id',$current_client->id);
        $source_list = SourceProfile::all()->where('client_id',$current_client->id);
        $mapping_digit = MappingPriority::select('digit')->get();
        $mapping_latter = MappingPriority::select('latters')->whereNotNull('latters')->get();
       return view('mapping.create',compact('client_list','current_client','consumption_list','source_list','mapping_latter', 'mapping_digit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
        $request->validate([
            'mapping_name' => ['required', 'string', 'max:255'],
            'consumption_point_id' => ['required', 'not_in:0'],
            'source_point_id' => ['required', 'not_in:0'],
            'quantum_min' => ['required'],
            // 'consumption_priority' => ['required_if:consumption_priority,0'],
            // 'generation_priority' => ['required_if:generation_priority,0'],
            'granularity_level_id' => ['required', 'not_in:0'],
        ]);
        // $c_to_s_exists = DB::table('mapping')
        //     ->where('client_id', $client_id)
        //     ->where('c_to_s_priority', $request->input('consumption_priority'))   
        //     ->where('consumption_point_id', $request['consumption_point_id'])         
        //     ->exists();
            
        // if ($c_to_s_exists) {
        //     // If the combination already exists, add a single error message for both fields
        //     $validator = $this->getValidationFactory()->make([], []);
        //     $validator->errors()->add('consumption_priority', 'The combination of consumption point already exists for this client.');
            
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        // $s_to_c_exists = DB::table('mapping')
        //     ->where('client_id', $client_id)
        //     ->where('s_to_c_priority', $request->input('generation_priority')) 
        //     ->where('source_point_id', $request['source_point_id'])           
        //     ->exists();
        
        // if ($s_to_c_exists) {
        //     // If the combination already exists, add a single error message for both fields
        //     $validator = $this->getValidationFactory()->make([], []);
        //     $validator->errors()->add('generation_priority', 'The combination of generation point already exists for this client.');
            
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        
        $mapping = Mapping::create([
            'client_id' => $client_id,
            'mapping_name' => $request['mapping_name'],
            'source_point_id' => $request['source_point_id'],
            'consumption_point_id' => $request['consumption_point_id'],
            'quantum_min' => $request['quantum_min'],
            'quantum_max' => $request['quantum_max'],
            'duration' => $request['duration'],
            // 'c_to_s_priority' => $request['consumption_priority'],
            // 's_to_c_priority' => $request['generation_priority'],
            'granularity_level_id' => $request['granularity_level_id'],
        ]);
        return redirect()->route('mapping.edit',['mapping' => $mapping->id,'from_store' => true])->with('success',__('Mapping updated successfuly.'));
    }
    public function edit($id)
    {
        $fromStore = request()->query('from_store', false);
        $client_list = Client::get();
        $current_client = Session::get('client_detail');
        $consumption_list = ConsumptionProfile::all()->sortByDesc('created_at')->where('client_id',$current_client->id);
        $source_list = SourceProfile::all()->where('client_id',$current_client->id);
        $mapping_digit = MappingPriority::select('digit')->get();
        $mapping_latter = MappingPriority::select('latters')->whereNotNull('latters')->get();
        $mapping = Mapping::find($id);
        return view('mapping.edit',compact('client_list','current_client','fromStore','mapping','consumption_list','source_list','mapping_digit', 'mapping_latter'));
    }


    public function update(Request $request, $id)
    {

        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
        
        $request->validate([
            'mapping_name' => ['required', 'string', 'max:255'],
            'consumption_point_id' => ['required', 'string', 'max:255'],
            'source_point_id' => ['required', 'string'],
            'quantum_min' => ['required'],
            // 'consumption_priority' => ['required_if:consumption_priority,0'],
            // 'generation_priority' => ['required_if:generation_priority,0'],
            'granularity_level_id' => ['required'],
        ]);

        $mapping = Mapping::find($id);


        // $c_to_s_exists = DB::table('mapping')
        //     ->where('client_id', $client_id)
        //     ->where('c_to_s_priority', $request->input('consumption_priority'))
        //     ->where('consumption_point_id', $request['consumption_point_id'])                     
        //     ->exists();
            
        // if ($c_to_s_exists && $mapping->c_to_s_priority != $request->input('consumption_priority')) {
        //     // If the combination already exists, add a single error message for both fields
        //     $validator = $this->getValidationFactory()->make([], []);
        //     $validator->errors()->add('consumption_priority', 'The combination of consumption point already exists for this client.');
            
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        // $s_to_c_exists = DB::table('mapping')
        //     ->where('client_id', $client_id)
        //     ->where('s_to_c_priority', $request->input('generation_priority')) 
        //     ->where('source_point_id', $request['source_point_id'])           
        //     ->exists();
            
        // if ($s_to_c_exists && $mapping->s_to_c_priority != $request->input('generation_priority') . $request->input('s_to_c_digit')) {
        //     // If the combination already exists, add a single error message for both fields
        //     $validator = $this->getValidationFactory()->make([], []);
        //     $validator->errors()->add('generation_priority', 'The combination of generation point already exists for this client.');
            
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        
        $mapping->mapping_name = $request['mapping_name'];
        $mapping->consumption_point_id = $request['consumption_point_id'];
        $mapping->source_point_id = $request['source_point_id'];
        $mapping->quantum_min = $request['quantum_min'];
        $mapping->quantum_max = $request['quantum_max'];
        $mapping->duration = $request['duration'];
        // $mapping->c_to_s_priority = $request->input('consumption_priority');
        // $mapping->s_to_c_priority = $request->input('generation_priority');
        $mapping->granularity_level_id = $request['granularity_level_id'];
        $mapping->save();
        return redirect()->back()->with('success',__('Mapping updated successfuly.'))->with('showToast', true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Mapping::find($id)->delete();
        return redirect()->back();
    }

    public function GenerateMappingTable(Request $request)
    {
        $id = $request->id;
        $mapping = Mapping::find($id);
        $consumption_id = $mapping->consumption_point_id;
        $source_id = $mapping->source_point_id;
        $ConsumptionProfile = ConsumptionProfile::where('id',$consumption_id)->select('granularity_level_id')->first();
        $SourceProfile = SourceProfile::where('id',$source_id)->select('granularity_level_id')->first();
        $Consumption = [];
        $leftoverDemandAndLaps = [];
        if($ConsumptionProfile->granularity_level_id == 1)
        {
            $data = AnnualConsumptionData::where('profile_id', $consumption_id)->get();
            $yearlyConsumption = $data[0]->year_unit; // Replace with your yearly consumption value
            $year = 2023; // Replace with the year
            $hoursPerDay = 24;
            $daysPerYear = Carbon::createFromDate($year, 12, 31)->dayOfYear;
            $totalHours = $daysPerYear * $hoursPerDay;
            $conume_unit = $yearlyConsumption / $totalHours;
            for ($j = 0; $j < $totalHours; $j++)
            {
                $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                $unitsPerSlot = $conume_unit / $slotsPerHour;
                $hour = str_pad($j, 2, '0', STR_PAD_LEFT);
                $slot = [];
                for ($k = 0; $k < $slotsPerHour; $k++) {

                    $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($j)->addMinutes($k * $request['chunk_time']);
                    $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);


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

                    $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    $unitsPerSlot = ($value['consumed_unit'] / $totalHours) / $slotsPerHour;

                    for ($k = 0; $k < $slotsPerHour; $k++) {
                        // echo $k;
                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($a)->addMinutes($k * $request['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);

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
        else if($ConsumptionProfile->granularity_level_id == 3) {
            $data = TodStateConsumptionData::where('profile_id', $consumption_id)->select('slot', 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'consumed_unit')->get();
            $checking_working_day = ConsumptionDayShift::where('profile_id', $consumption_id)->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
            $consumption_tod = ConsumptionTod::where('profile_id', $consumption_id)->get();

            $Consumption = [];
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

                        $slotsPerHour = 60 / $request['chunk_time'];
                        // Generate chunks within the specified hour range for the current day
                        while ($start < $end) {
                            $chunkEnd = $start->copy()->addMinutes($request['chunk_time']);
                            $slot_time = $start->format('H:i') . '-' . $chunkEnd->format('H:i');

                            // $unit =  / $slotsPerHour;

                            $dayOfWeek = $currentDate->dayOfWeek;
                            $hoursOfDay = $start->hour;

                            if (count($checking_working_day) > 0  && count($consumption_tod) > 0) {
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

                                if ($dayOfWeek >= $startDay && $dayOfWeek <= $endDay) {
                                    $unitsPerSlot = ((($consumedUnit * ($matchingElement->tod_value / 100)) / $numDays) / $diffInHours) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                } else {

                                    $unitsPerSlot = (((($consumedUnit * ($matchingElement->tod_value / 100)) / $numDays) / $diffInHours) * (1 - $holidayUnit)) / $slotsPerHour;
                                    // dd($unitsPerSlot);
                                    // $crossCheck[] = $unitsPerSlot;
                                }
                            } else if (count($checking_working_day) == 0 && count($consumption_tod) > 0) {
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


                                $unitsPerSlot = ((($consumedUnit * ($matchingElement->tod_value / 100)) / $numDays) / $diffInHours) / $slotsPerHour;
                                // $crossCheck[] = $unitsPerSlot;
                            } else if (count($checking_working_day) > 0 && count($consumption_tod) == 0) {

                                $startDay = $checking_working_day[0]->day_start;
                                $endDay = $checking_working_day[0]->day_end;

                                if ($dayOfWeek >= $startDay && $dayOfWeek <= $endDay) {
                                    $unitsPerSlot = (($consumedUnit / $numDays) / $diffInHours) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                } else {
                                    $unitsPerSlot = ((($consumedUnit / $numDays) / $diffInHours) * (1 - $holidayUnit)) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                }
                            } else {
                                $unitsPerSlot = (($consumedUnit / $numDays) / $diffInHours) / $slotsPerHour;
                            }

                            $chunkData = [
                                'month' => $month < 10 ? '0' . $month : $month,
                                'day' => $currentDate->format('d'),
                                'hours' => $start->format('H'),
                                'start' => $start,
                                'end' => $chunkEnd,
                                'consumption' => round($unitsPerSlot, 3),
                            ];
                            $Consumption[] = $chunkData;

                            $start->addMinutes($request['chunk_time']);
                        }
                    }
                }
            }

            usort($Consumption, function ($a, $b) {
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
        else if($ConsumptionProfile->granularity_level_id == 4)
        {
            $data = HourlyConsumptionData::where('profile_id', $consumption_id)->select('hours', 'consumed_unit')->get();

            foreach ($data as $key => $value) {

                $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                $unitsPerSlot = $value['consumed_unit'] / $slotsPerHour;
                $slot = [];
                for ($i = 0; $i < $slotsPerHour; $i++) {

                    $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($value['hours'] - 1)->addMinutes($i * $request['chunk_time']);
                    $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);

                    $Consumption[] = [
                        'month' => $startSlot->format('n'),
                        'day' => $startSlot->format('d'),
                        'hours' => $startSlot->format('H'),
                        'start' => $startSlot->format('H:i') ,
                        'end'=>$endSlot->format('H:i'),
                        'consumption' => round($unitsPerSlot, 3)
                    ];
                }


            }

        }
        else if($ConsumptionProfile->granularity_level_id == 5)
        {
            $data = WeeklyConsumptionData::where('profile_id', $consumption_id)->where('weeks', '!=', 'Proportion units lower on sundays and holidays')->select('weeks', 'consumed_unit')->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $consumption_id)->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $lower_unit = WeeklyConsumptionData::where('profile_id', $consumption_id)->where('weeks', '=', 'Proportion units lower on sundays and holidays')->select('consumed_unit')->first();
                $consumption_tod = ConsumptionTod::where('profile_id', $consumption_id)->get();

                $holidayUnit = $lower_unit->consumed_unit / 100;

                foreach ($data as $key => $value) {

                    $week = $value['weeks'];
                    $consumedUnit = ($value['consumed_unit'] / 7) / 24;
                    $slotPerHour = 60 / $request['chunk_time'];

                    for ($day = 0; $day < 7; $day++){
                        for ($hour = 0; $hour < 24; $hour++) {
                            $startTime = Carbon::create(2023, 1, 1)->addDays($day)->setHour($hour)->setMinute(0)->setSecond(0);
                            $startDate = Carbon::now()->setISODate(2023, $week)->startOfWeek();

                            $slot = [];

                            $minute = 0;
                            for ($slot = 0; $slot < $slotPerHour; $slot++) {
                                $chunkStart = $startTime->copy()->addMinutes($minute)->format('H:i'); // Format: Hour:Minute
                                $chunkEnd = $startTime->copy()->addMinutes($minute + $request['chunk_time'])->format('H:i'); // Format: Hour:Minute

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
                                    }else{

                                        $unitsPerSlot = (((($consumedUnit ) * (1 - $holidayUnit)) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotPerHour;
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
                                }else if(count($checking_working_day) > 0 && count($consumption_tod) == 0){

                                    $startDay = $checking_working_day[0]->day_start;
                                    $endDay = $checking_working_day[0]->day_end;

                                    if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                        $unitsPerSlot = $consumedUnit / $slotPerHour;
                                    }else{
                                        $unitsPerSlot = ($consumedUnit * (1 - $holidayUnit)) / $slotPerHour;
                                    }
                                }else{
                                    $unitsPerSlot = $consumedUnit / $slotPerHour;
                                }

                                $Consumption[] = [
                                    'month' => $startDate->month,
                                    'day' => $startTime->format('d'),
                                    'hours' => $startTime->format('H'),
                                    'start' => $chunkStart ,
                                    "end" => $chunkEnd,
                                    'consumption' => round($unitsPerSlot, 3)
                                ];
                                $minute += $request['chunk_time'];
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
                $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                $unitsPerSlot = $conume_unit / $slotsPerHour;
                $hour = str_pad($j, 2, '0', STR_PAD_LEFT);
                $slot = [];
                for ($k = 0; $k < $slotsPerHour; $k++) {

                    $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($j)->addMinutes($k * $request['chunk_time']);
                    $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);


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
            $data = MonthlySourceData::where('profile_id', $source_id)->where('name', '!=', 'Proportion units lower on sundays and holidays')->select('name', 'consumed_unit')->get();
            foreach($data as $key => $value){
                $currentYear = 2023;

                $startOfMonth = Carbon::create($currentYear, $value['name'], 1, 0, 0, 0);
                $endOfMonth = Carbon::create($currentYear, $value['name'], 1, 23, 59, 59)->endOfMonth();

                $totalHours = $startOfMonth->diffInHours($endOfMonth);

                $slot = [];
                for ($a = 0; $a < $totalHours; $a++)
                {

                    $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    $unitsPerSlot = ($value['consumed_unit'] / $totalHours) / $slotsPerHour;

                    for ($k = 0; $k < $slotsPerHour; $k++) {
                        // echo $k;
                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($a)->addMinutes($k * $request['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);

                    }
                    $slot = [
                        'month' => $startOfMonth->format('m'),
                        'day' => $startSlot->format('d'),
                        'start' => $startSlot->format('H:i'),
                        'end' => $endSlot->format('H:i'),
                        'hours' => $startSlot->format('H'),
                        'consumption' => round($unitsPerSlot, 3)

                    ];
                    $intervals[] = $slot;
                }

            }

        }
        else if($SourceProfile->granularity_level_id == 3)
        {
            $data = TODStateSourceData::where('profile_id', $source_id)->select('slot', 'jan', 'feb', 'mar','apr','may','jun','jul','aug','sep','oct','nov','dec','consumed_unit')->get();
            $checking_working_day = ConsumptionDayShift::where('profile_id', $source_id)->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
            $consumption_tod = SourceTod::where('profile_id', $source_id)->get();

            $intervals = [];
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

                        $slotsPerHour = 60 / $request['chunk_time'];
                        // Generate chunks within the specified hour range for the current day
                        while ($start < $end) {
                            $chunkEnd = $start->copy()->addMinutes($request['chunk_time']);
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
                            $start->addMinutes($request['chunk_time']);
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
                $slotPerHour = 60 / $request['chunk_time'];
                $consumedUnitPerInterval = $consumedUnit / $slotPerHour;
                for ($day = 0; $day < 7; $day++){
                    for ($hour = 0; $hour < 24; $hour++) {
                        $startTime = Carbon::create(2023, 1, 1)->addDays($day)->setHour($hour)->setMinute(0)->setSecond(0);
                        $minute = 0;
                        for ($slot = 0; $slot < $slotPerHour; $slot++) {
                            $chunkStart = $startTime->copy()->addMinutes($minute)->format('H:i'); // Format: Hour:Minute
                            $chunkEnd = $startTime->copy()->addMinutes($minute + $request['chunk_time'])->format('H:i'); // Format: Hour:Minute
                            $minute = $slot * $request['chunk_time'];
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

                $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                $unitsPerSlot = $value['consumed_unit'] / $slotsPerHour;
                $slot = [];
                for ($i = 0; $i < $slotsPerHour; $i++) {

                    // $startSlot = Carbon::create(2023, 1, 1)->addHours($value['hours'])->addMinutes($i * $request['chunk_time']);
                    $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($value['hours'] - 1)->addMinutes($i * $request['chunk_time']);
                    $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);


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
        

        if($request->granularity_level_id == 1)
        {
            for ($key = 0; $key < $iterationCount; $key++) {
                if (isset($Consumption[$key], $intervals[$key])) {
                
                if($intervals[$key]['consumption'] < 0){
                    $leftover = $Consumption[$key]['consumption'];
                }else{
                    $leftover = $Consumption[$key]['consumption'] - $intervals[$key]['consumption'];
                }
                $slot = [
                    'month' => $Consumption[$key]['month'] ?? null,
                    'day' => $Consumption[$key]['day'] ?? null,
                    'hours' => $Consumption[$key]['hours'] ?? null,
                    'slot' => $Consumption[$key]['start'] ?? null .'-'. $Consumption[$key]['end'] ?? null,
                    'leftover_demand' => $leftover > 0 ? round($leftover, 3) : 0,
                    'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0,
                    'intervals_consumption_unit' => round($intervals[$key]['consumption'] ?? null, 3),
                    'consumption_consumption_unit' => round($Consumption[$key]['consumption'] ?? null, 3),
                ];

                $leftoverDemandAndLaps[] = $slot;
            }
            }
        }
        if ($request->granularity_level_id == 2) {
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
                if($ConsumptionProfile->granularity_level_id ==1)
                {
                    $slotConsumptions[$slot] = $sum * 365 ;
                }
            }
            if($SourceProfile->granularity_level_id ==1)
            {
                foreach ($slotSources as $slot => $sum) {
                    if($SourceProfile->granularity_level_id == 4)
                    {
                        $slotSources[$slot] = $sum;
                    }
                    else
                    {
                        $yearlySlotSums[$slot] = $sum * 8760;
                        $slotSources[$slot] = $sum * 365 ;
                    }
                }
            }
            // Display the slot-wise yearly consumption sums
            foreach ($yearlySlotSums as $slot => $sum) {
                
                if($slotSources[$slot] < 0){
                    $leftover = $slotConsumptions[$slot];
                }else{
                    $leftover = $slotConsumptions[$slot] - $slotSources[$slot];
                }
                $slotData = [
                    'slot' => $slot,
                    'consumption_consumption_unit' => round($slotConsumptions[$slot],3),
                    'intervals_consumption_unit' => round($slotSources[$slot],3),
                    'leftover_demand' => $leftover > 0 ? round($leftover, 3) : 0,
                    'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0,
                ];

                $leftoverDemandAndLaps[] = $slotData;
            }
        }
        if ($request->granularity_level_id == 3) {
            for ($i = 1; $i <= 12; $i++) {
                $consumedUnit = 0;
                $intervalsUnit = 0;

                for ($key = 0; $key < $iterationCount; $key++) {
                    if ($i == $Consumption[$key]['month']) {
                        if (isset($Consumption[$key + 1])) {
                            $consumedUnit += $Consumption[$key]['consumption'];
                        } else {
                            $consumedUnit += $Consumption[$key]['consumption'];
                        }
                    }

                    if ($SourceProfile->granularity_level_id == 4) {
                        if ($i == $intervals[$key]['month']) {
                            $intervalsUnit += $intervals[$key]['consumption'];

                        }
                        else
                        {
                            $intervalsUnit += $intervals[$key]['consumption'];
                        }
                    } else {
                        if (isset($intervals[$key]) && $i == $intervals[$key]['month']) {
                            if (isset($intervals[$key + 1])) {
                                $intervalsUnit += $intervals[$key]['consumption'];
                            } else {
                                $intervalsUnit += $intervals[$key]['consumption'];
                            }
                        }
                    }
                }

                // $leftover = $consumedUnit - $intervalsUnit;
                if($intervalsUnit < 0){
                    $leftover = $consumedUnit;
                }else{
                    $leftover = $consumedUnit - $intervalsUnit;
                }
                $slot = [
                    'month' => $i,
                    'consumption_consumption_unit' => round($consumedUnit, 3),
                    'intervals_consumption_unit' => round($intervalsUnit, 3),
                    'leftover_demand' => $leftover > 0 ? round($leftover, 3) : 0,
                    'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0,
                ];

                $leftoverDemandAndLaps[] = $slot;
            }
        }
        if($request->granularity_level_id == 4 || $request->granularity_level_id == 5){
            for ($key = 0; $key < $iterationCount; $key++) {
                if (isset($Consumption[$key], $intervals[$key])) {
                    if($intervals[$key]['consumption'] < 0){
                        $leftover = $Consumption[$key]['consumption'];
                    }else{
                        $leftover = $Consumption[$key]['consumption'] - $intervals[$key]['consumption'];
                    }
                
              
                $slot = [
                    'month' => $Consumption[$key]['month'] ?? null,
                    'day' => $Consumption[$key]['day'] ?? null,
                    'hours' => $Consumption[$key]['hours'] ?? null,
                    'slot' => $Consumption[$key]['start'] .' - '. $Consumption[$key]['end'],
                    'leftover_demand' => $leftover > 0 ? round($leftover, 3) : 0,
                    'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0,
                    'intervals_consumption_unit' => round($intervals[$key]['consumption'] ?? null, 3),
                    'consumption_consumption_unit' => round($Consumption[$key]['consumption'] ?? null, 3),
                ];

                $leftoverDemandAndLaps[] = $slot;
            }
            }
            
        }
        return response()->json(['status' => true, 'data' => $leftoverDemandAndLaps]);



    }
    public function ExportConvertMapping(Request $request)
    {


        $data = [
            'granularity_level' => $request['granularity_level'],
            'chunk_time' => $request['chunk_time'],
            'profile_id' => $request['profile_id']
        ];


        return Excel::download(new ExportMapping($data), 'mapping.csv');
    }

    public function ManyToManyMapping(Request $request)
    {
        try {
            // dd($request);
            
            $consumption = $request->consumption_point_id;
            $chunk_time = $request->chunk_time;
            $check_mapping = $request->source_point_id;
            $graph_type = $request->data_level;
            $sessionId = Session::get('client_detail');
            $client_list = Client::get();
            $consumption_list = ConsumptionProfile::all()->sortByDesc('created_at')->where('client_id',$sessionId->id);
            $source_list = Mapping::with(['source_profile'])->where('consumption_point_id', $request->consumption_point_id)->get();
            $uniqueResults = $source_list->unique(function ($item) {
                return $item->source_profile->id . '-' . $item->source_profile->source_name;
            })->values()->all();
            $list = [];
            foreach($uniqueResults as $key => $item){
                if(isset($item->source_profile)){
                    $list[] = [
                        'id' => $item->source_profile->id,
                        'name' => $item->source_profile->source_name
                    ];
                }
            }
            // dd($request);
            // $check_mapping = Mapping::where('consumption_point_id', $consumption)->get();
            if(isset($check_mapping) && !empty($check_mapping) && !is_array($check_mapping) && count($check_mapping) <= 0){
                return redirect()->back()->withErrors(['not_mapping' => 'Selected Consumption have not mapping'])->withInput();
            }
            // dd($check_mapping);
            
            $consumption_id = $consumption;
            // $source_id = $check_mapping->source_point_id;
            $ConsumptionProfile = ConsumptionProfile::where('id',$consumption_id)->select('granularity_level_id')->first();
            // $SourceProfile = SourceProfile::where('id',$source_id)->select('granularity_level_id')->first();
            $Consumption = [];
            $leftoverDemandAndLaps = [];
            if($ConsumptionProfile->granularity_level_id == 1)
            {
                $data = AnnualConsumptionData::where('profile_id', $consumption_id)->get();
                $yearlyConsumption = $data[0]->year_unit; // Replace with your yearly consumption value
                $year = 2023; // Replace with the year
                $hoursPerDay = 24;
                $daysPerYear = Carbon::createFromDate($year, 12, 31)->dayOfYear;
                $totalHours = $daysPerYear * $hoursPerDay;
                $conume_unit = $yearlyConsumption / $totalHours;
                for ($j = 0; $j < $totalHours; $j++)
                {
                    $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    $unitsPerSlot = $conume_unit / $slotsPerHour;
                    $hour = str_pad($j, 2, '0', STR_PAD_LEFT);
                    $slot = [];
                    for ($k = 0; $k < $slotsPerHour; $k++) {

                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($j)->addMinutes($k * $request['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);


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

                        $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                        $unitsPerSlot = ($value['consumed_unit'] / $totalHours) / $slotsPerHour;

                        for ($k = 0; $k < $slotsPerHour; $k++) {
                            // echo $k;
                            $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($a)->addMinutes($k * $request['chunk_time']);
                            $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);

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
            else if($ConsumptionProfile->granularity_level_id == 3) {
                $data = TodStateConsumptionData::where('profile_id', $consumption_id)->select('slot', 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'consumed_unit')->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $consumption_id)->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $consumption_tod = ConsumptionTod::where('profile_id', $consumption_id)->get();

                $Consumption = [];
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

                            $slotsPerHour = 60 / $request['chunk_time'];
                            // Generate chunks within the specified hour range for the current day
                            while ($start < $end) {
                                $chunkEnd = $start->copy()->addMinutes($request['chunk_time']);
                                $slot_time = $start->format('H:i') . '-' . $chunkEnd->format('H:i');

                                // $unit =  / $slotsPerHour;

                                $dayOfWeek = $currentDate->dayOfWeek;
                                $hoursOfDay = $start->hour;

                                if (count($checking_working_day) > 0  && count($consumption_tod) > 0) {
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

                                    if ($dayOfWeek >= $startDay && $dayOfWeek <= $endDay) {
                                        $unitsPerSlot = ((($consumedUnit * ($matchingElement->tod_value / 100)) / $numDays) / $diffInHours) / $slotsPerHour;
                                        // $crossCheck[] = $unitsPerSlot;
                                    } else {

                                        $unitsPerSlot = (((($consumedUnit * ($matchingElement->tod_value / 100)) / $numDays) / $diffInHours) * (1 - $holidayUnit)) / $slotsPerHour;
                                        // dd($unitsPerSlot);
                                        // $crossCheck[] = $unitsPerSlot;
                                    }
                                } else if (count($checking_working_day) == 0 && count($consumption_tod) > 0) {
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


                                    $unitsPerSlot = ((($consumedUnit * ($matchingElement->tod_value / 100)) / $numDays) / $diffInHours) / $slotsPerHour;
                                    // $crossCheck[] = $unitsPerSlot;
                                } else if (count($checking_working_day) > 0 && count($consumption_tod) == 0) {

                                    $startDay = $checking_working_day[0]->day_start;
                                    $endDay = $checking_working_day[0]->day_end;

                                    if ($dayOfWeek >= $startDay && $dayOfWeek <= $endDay) {
                                        $unitsPerSlot = (($consumedUnit / $numDays) / $diffInHours) / $slotsPerHour;
                                        // $crossCheck[] = $unitsPerSlot;
                                    } else {
                                        $unitsPerSlot = ((($consumedUnit / $numDays) / $diffInHours) * (1 - $holidayUnit)) / $slotsPerHour;
                                        // $crossCheck[] = $unitsPerSlot;
                                    }
                                } else {
                                    $unitsPerSlot = (($consumedUnit / $numDays) / $diffInHours) / $slotsPerHour;
                                }

                                $chunkData = [
                                    'month' => $month < 10 ? '0' . $month : $month,
                                    'day' => $currentDate->format('d'),
                                    'hours' => $start->format('H'),
                                    'start' => $start,
                                    'end' => $chunkEnd,
                                    'consumption' => round($unitsPerSlot, 3),
                                ];
                                $Consumption[] = $chunkData;

                                $start->addMinutes($request['chunk_time']);
                            }
                        }
                    }
                }

                usort($Consumption, function ($a, $b) {
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
            else if($ConsumptionProfile->granularity_level_id == 4)
            {
                $data = HourlyConsumptionData::where('profile_id', $consumption_id)->select('hours', 'consumed_unit')->get();

                foreach ($data as $key => $value) {

                    $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    $unitsPerSlot = $value['consumed_unit'] / $slotsPerHour;
                    $slot = [];
                    for ($i = 0; $i < $slotsPerHour; $i++) {

                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($value['hours'] - 1)->addMinutes($i * $request['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);

                        $Consumption[] = [
                            'month' => $startSlot->format('n'),
                            'day' => $startSlot->format('d'),
                            'hours' => $startSlot->format('H'),
                            'start' => $startSlot->format('H:i') ,
                            'end'=>$endSlot->format('H:i'),
                            'consumption' => round($unitsPerSlot, 3)
                        ];
                    }


                }

            }
            else if($ConsumptionProfile->granularity_level_id == 5)
            {
               
                $data = WeeklyConsumptionData::where('profile_id', $consumption_id)->where('weeks', '!=', 'Proportion units lower on sundays and holidays')->select('weeks', 'consumed_unit')->get();
                    $checking_working_day = ConsumptionDayShift::where('profile_id', $consumption_id)->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                    $lower_unit = WeeklyConsumptionData::where('profile_id', $consumption_id)->where('weeks', '=', 'Proportion units lower on sundays and holidays')->select('consumed_unit')->first();
                    $consumption_tod = ConsumptionTod::where('profile_id', $consumption_id)->get();
                    
                    $holidayUnit = $lower_unit->consumed_unit / 100;

                    foreach ($data as $key => $value) {

                        $week = $value['weeks'];
                        $consumedUnit = ($value['consumed_unit'] / 7) / 24;
                        $slotPerHour = 60 / $request['chunk_time'];

                        for ($day = 0; $day < 7; $day++){
                            for ($hour = 0; $hour < 24; $hour++) {
                                $startTime = Carbon::create(2023, 1, 1)->addDays($day)->setHour($hour)->setMinute(0)->setSecond(0);
                                $startDate = Carbon::now()->setISODate(2023, $week)->startOfWeek();

                                $slot = [];

                                $minute = 0;
                                for ($slot = 0; $slot < $slotPerHour; $slot++) {
                                    $chunkStart = $startTime->copy()->addMinutes($minute)->format('H:i'); // Format: Hour:Minute
                                    $chunkEnd = $startTime->copy()->addMinutes($minute + $request['chunk_time'])->format('H:i'); // Format: Hour:Minute

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
                                        }else{

                                            $unitsPerSlot = (((($consumedUnit ) * (1 - $holidayUnit)) * ($matchingElement->tod_value/ 100)) / $diffInHours) / $slotPerHour;
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
                                    }else if(count($checking_working_day) > 0 && count($consumption_tod) == 0){

                                        $startDay = $checking_working_day[0]->day_start;
                                        $endDay = $checking_working_day[0]->day_end;

                                        if($dayOfWeek >= $startDay && $dayOfWeek <= $endDay){
                                            $unitsPerSlot = $consumedUnit / $slotPerHour;
                                        }else{
                                            $unitsPerSlot = ($consumedUnit * (1 - $holidayUnit)) / $slotPerHour;
                                        }
                                    }else{
                                        $unitsPerSlot = $consumedUnit / $slotPerHour;
                                    }

                                    $Consumption[] = [
                                        'month' => $startDate->month,
                                        'day' => $startTime->format('d'),
                                        'hours' => $startTime->format('H'),
                                        'start' => $chunkStart ,
                                        "end" => $chunkEnd,
                                        'consumption' => round($unitsPerSlot, 3)
                                    ];
                                    $minute += $request['chunk_time'];
                                }
                            }
                        }
                    }


            }
            

            $sum_source = [];
            $individualSourceResults = [];
            // dd($check_mapping);
            foreach($check_mapping as $keys => $val){
                // dd($val);
                $intervals1 = $intervals2 = $intervals3 = $intervals4 = $intervals5 = [];
                $source_id = $val;
                $SourceProfile = SourceProfile::where('id',$source_id)->select('granularity_level_id')->first();
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
                        $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                        $unitsPerSlot = $conume_unit / $slotsPerHour;
                        $hour = str_pad($j, 2, '0', STR_PAD_LEFT);
                        $slot = [];
                        
                        for ($k = 0; $k < $slotsPerHour; $k++) {
                        
                            $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($j)->addMinutes($k * $request['chunk_time']);
                            $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);
                        
                        
                            $slot = [
                                'month' => $startSlot->format('m'),
                                'day' => $startSlot->format('d'),
                                'hours' => $hour,
                                'start' => $startSlot->format('H:i'),
                                'end' => $endSlot->format('H:i'),
                                'source' => round($unitsPerSlot, 3)
                            ];
                        
                            $intervals1[] = $slot;
                        } 
                    }
                }
                else if($SourceProfile->granularity_level_id == 2)
                {
                    $data = MonthlySourceData::where('profile_id', $source_id)->where('name', '!=', 'Proportion units lower on sundays and holidays')->select('name', 'consumed_unit')->get();
                    // echo "<pre>";
                    // print_r($intervals);
                    // $intervals2 = [];
                    
                    foreach($data as $key => $value){
                        $currentYear = 2023;

                        $startOfMonth = Carbon::create($currentYear, $value['name'], 1, 0, 0, 0);
                        $endOfMonth = Carbon::create($currentYear, $value['name'], 1, 23, 59, 59)->endOfMonth();

                        $totalHours = $startOfMonth->diffInHours($endOfMonth);

                        $slot = [];
                        
                        for ($a = 0; $a < $totalHours; $a++)
                        {
                            
                            $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                            $unitsPerSlot = ($value['consumed_unit'] / $totalHours) / $slotsPerHour;
                        
                            for ($k = 0; $k < $slotsPerHour; $k++) {
                                // echo $k;
                                $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($a)->addMinutes($k * $request['chunk_time']);
                                $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);

                            }
                            $slot = [
                                'month' => $startOfMonth->format('m'),
                                'day' => $startSlot->format('d'),
                                'start' => $startSlot->format('H:i'),
                                'end' => $endSlot->format('H:i'),
                                'hours' => $startSlot->format('H'),
                                'source' => round($unitsPerSlot, 3)
                            
                            ];
                            $intervals2[] = $slot;
                        }

                    }

                }
                else if($SourceProfile->granularity_level_id == 3)
                {
                    $data = TODStateSourceData::where('profile_id', $source_id)->select('slot', 'jan', 'feb', 'mar','apr','may','jun','jul','aug','sep','oct','nov','dec','consumed_unit')->get();
                    $checking_working_day = ConsumptionDayShift::where('profile_id', $source_id)->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                    $consumption_tod = SourceTod::where('profile_id', $source_id)->get();

                    $intervals3 = [];
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
                            
                                $slotsPerHour = 60 / $request['chunk_time'];
                                // Generate chunks within the specified hour range for the current day
                            while ($start < $end) {
                                $chunkEnd = $start->copy()->addMinutes($request['chunk_time']);
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
                                    'source' => round($unitsPerSlot, 3),
                                ];
                                $intervals3[] = $intervalsData;
                                $start->addMinutes($request['chunk_time']);
                            }
                        }
                    }

                    }

                    usort($intervals3, function ($a, $b) {
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
                    $intervals4 = [];
                    foreach ($data as $key => $value) {
                        $week = $value['week'];
                        $consumedUnit = ($value['consumed_unit'] / 7) / 24;
                        $slotPerHour = 60 / $request['chunk_time'];
                        $consumedUnitPerInterval = $consumedUnit / $slotPerHour;
                        for ($day = 0; $day < 7; $day++){
                            for ($hour = 0; $hour < 24; $hour++) {
                                $startTime = Carbon::create(2023, 1, 1)->addDays($day)->setHour($hour)->setMinute(0)->setSecond(0);
                                $minute = 0;
                                for ($slot = 0; $slot < $slotPerHour; $slot++) {
                                    $chunkStart = $startTime->copy()->addMinutes($minute)->format('H:i'); // Format: Hour:Minute
                                    $chunkEnd = $startTime->copy()->addMinutes($minute + $request['chunk_time'])->format('H:i'); // Format: Hour:Minute
                                    $minute = $slot * $request['chunk_time'];
                                    $interval = [
                                        'month' => $startTime->format('m'),
                                        'day' => $startTime->format('d'),
                                        'hours' => $startTime->format('H'),
                                        'start' => $chunkStart,
                                        'end' => $chunkEnd,
                                        'source' => round($consumedUnitPerInterval, 3)
                                    ];
                                    $intervals4[] = $interval;
                                }

                            }
                        }
                    }
                }
                elseif($SourceProfile->granularity_level_id == 5)
                {
                    $data = HourlySourceData::where('profile_id', $source_id)->select('hours', 'consumed_unit')->get();
                    foreach ($data as $key => $value) {
                    
                        $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                        $unitsPerSlot = $value['consumed_unit'] / $slotsPerHour;
                        $slot = [];
                        for ($i = 0; $i < $slotsPerHour; $i++) {
                        
                            // $startSlot = Carbon::create(2023, 1, 1)->addHours($value['hours'])->addMinutes($i * $request['chunk_time']);
                            $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($value['hours'] - 1)->addMinutes($i * $request['chunk_time']);
                            $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);
                        
                        
                            $intervals5[] = [
                                'month' => $startSlot->format('m'),
                                'day' => $startSlot->format('d'),
                                'hours' => $startSlot->format('H'),
                                'start' => $startSlot->format('H:i'),
                                'end' => $endSlot->format('H:i'),
                                'source' => round($unitsPerSlot, 3)
                            ];
                        
                        
                        }
                    
                    }
                }
            
                $sum_source[] = array_merge($intervals1, $intervals2, $intervals3, $intervals4, $intervals5);
                // if (!isset($individualSourceResults[])) {
                    $individualSourceResults[] = $sum_source;
                // }
                // echo "<pre>";
                // print_r(count(array_merge($intervals1, $intervals2, $intervals3, $intervals4, $intervals5)));
                // exit;
            
            }
           
            // echo "<pre>";
            // print_r($individualSourceResults);
            // exit;
            $summedArray = [];
            foreach ($sum_source as $outerIndex => $innerArray) {
                foreach ($innerArray as $innerIndex => $data) {
                    if (!isset($summedArray[$innerIndex])) {
                        $summedArray[$innerIndex] = $data;
                    } else {
                        $summedArray[$innerIndex]['source'] += $data['source'];
                    }
                }
            }
            $finalResult = array_values($summedArray);

            $individualmonthlySum = [];
            $monthlySum = [];
            // foreach($individualSourceResults as $count => $individual){
            //     $iterationCount = min(count($Consumption), count($individual[0]));
                
            //     if($graph_type == 2){
            //         for($p = 0; $p < $iterationCount; $p++){
            //             $month = $Consumption[$p]['month'];
            //             $consumption = $Consumption[$p]['consumption'];
            //             $source = $individual[0][$p]['source'];
            //             $quarter = ceil($month / 3);
            //             if (!isset($individualmonthlySum[$count][$quarter])) {

            //                 $individualmonthlySum[$count][$quarter]['source'] = 0;
            //                 $individualmonthlySum[$count][$quarter]['consumption'] = 0;
            //             }
            //             $individualmonthlySum[$count][$quarter]['month'] = $quarter;
            //             $individualmonthlySum[$count][$quarter]['source'] = round($source, 3);
            //             $individualmonthlySum[$count][$quarter]['consumption'] = round($consumption,3);
            //             $leftover = $individualmonthlySum[$count][$quarter]['consumption'] - $individualmonthlySum[$count][$quarter]['source'];
            //             $individualmonthlySum[$count][$quarter]['leftover'] = $leftover > 0 ? round($leftover, 3) : 0;
            //             $individualmonthlySum[$count][$quarter]['laps'] = $leftover < 0 ? round(abs($leftover), 3) : 0;
            //             $individualmonthlySum[$count][$quarter]['type'] = 2;
                    
            //             // 'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0
            //         }
            //     }else{
            //         for($p = 0; $p < $iterationCount; $p++){
            //             $month = $Consumption[$p]['month'];
            //             $consumption = $Consumption[$p]['consumption'];
            //             $source = $individual[0][$p]['source'];
                        
            //             if (!isset($individualmonthlySum[$count][$month])) {

            //                 $individualmonthlySum[$count][$month]['source'] = 0;
            //                 $individualmonthlySum[$count][$month]['consumption'] = 0;
            //             }
            //             $individualmonthlySum[$count][$month]['month'] = $month;
            //             $individualmonthlySum[$count][$month]['source'] += round($source, 3);
            //             $individualmonthlySum[$count][$month]['consumption'] += round($consumption,3);
            //             $leftover = $individualmonthlySum[$count][$month]['consumption'] - $individualmonthlySum[$count][$month]['source'];
            //             $individualmonthlySum[$count][$month]['leftover'] = $leftover > 0 ? round($leftover, 3) : 0;
            //             $individualmonthlySum[$count][$month]['laps'] = $leftover < 0 ? round(abs($leftover), 3) : 0;
            //             $individualmonthlySum[$count][$month]['type'] = 1;
                    
            //             // 'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0
            //         }
            //     }
            // }
            // echo "<pre>";
            // print_r($individualmonthlySum);
            // exit;
            // dd($request->type);
            if($request->type == 1){
                foreach($individualSourceResults as $count => $individual){
                    $iterationCount = min(count($Consumption), count($individual[0]));
                    
                    if($graph_type == 2){
                        for($p = 0; $p < $iterationCount; $p++){
                            $month = $Consumption[$p]['month'];
                            $consumption = $Consumption[$p]['consumption'];
                            $source = $individual[0][$p]['source'];
                            $quarter = ceil($month / 3);
                            if (!isset($individualmonthlySum[$count][$quarter])) {
    
                                $individualmonthlySum[$count][$quarter]['source'] = 0;
                                $individualmonthlySum[$count][$quarter]['consumption'] = 0;
                            }
                            $individualmonthlySum[$count][$quarter]['month'] = $quarter;
                            $individualmonthlySum[$count][$quarter]['source'] = round($source, 3);
                            $individualmonthlySum[$count][$quarter]['consumption'] = round($consumption,3);
                            if($individualmonthlySum[$count][$quarter]['source'] < 0){
                                $leftover = $individualmonthlySum[$count][$quarter]['consumption'];
                            }else{
                                $leftover = $individualmonthlySum[$count][$quarter]['consumption'] - $individualmonthlySum[$count][$quarter]['source'];
                            }
                            
                            $individualmonthlySum[$count][$quarter]['leftover'] = $leftover > 0 ? round($leftover, 3) : 0;
                            $individualmonthlySum[$count][$quarter]['laps'] = $leftover < 0 ? round(abs($leftover), 3) : 0;
                            $individualmonthlySum[$count][$quarter]['type'] = 2;
                        
                            // 'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0
                        }
                    }else{
                        for($p = 0; $p < $iterationCount; $p++){
                            $month = $Consumption[$p]['month'];
                            $consumption = $Consumption[$p]['consumption'];
                            $source = $individual[0][$p]['source'];
                            
                            if (!isset($individualmonthlySum[$count][$month])) {
    
                                $individualmonthlySum[$count][$month]['source'] = 0;
                                $individualmonthlySum[$count][$month]['consumption'] = 0;
                            }
                            $individualmonthlySum[$count][$month]['month'] = $month;
                            $individualmonthlySum[$count][$month]['source'] += round($source, 3);
                            $individualmonthlySum[$count][$month]['consumption'] += round($consumption,3);
                            if($individualmonthlySum[$count][$month]['source'] < 0){
                                $leftover = $individualmonthlySum[$count][$month]['consumption'];
                            }else{
                                $leftover = $individualmonthlySum[$count][$month]['consumption'] - $individualmonthlySum[$count][$month]['source'];
                            }
                            $individualmonthlySum[$count][$month]['leftover'] = $leftover > 0 ? round($leftover, 3) : 0;
                            $individualmonthlySum[$count][$month]['laps'] = $leftover < 0 ? round(abs($leftover), 3) : 0;
                            $individualmonthlySum[$count][$month]['type'] = 1;
                        
                            // 'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0
                        }
                    }
                }
            }else{
                $iterationCount = min(count($Consumption), count($finalResult));
                
                if($graph_type == 2){
                    for($p = 0; $p < $iterationCount; $p++){
                        $month = $Consumption[$p]['month'];
                        $consumption = $Consumption[$p]['consumption'];
                        $source = $finalResult[$p]['source'];
                        $quarter = ceil($month / 3);
                        if (!isset($monthlySum[$quarter])) {

                            $monthlySum[$quarter]['source'] = 0;
                            $monthlySum[$quarter]['consumption'] = 0;
                        }
                        $monthlySum[$quarter]['month'] = $quarter;
                        $monthlySum[$quarter]['source'] += round($source, 3);
                        $monthlySum[$quarter]['consumption'] += round($consumption,3);
                        if($monthlySum[$quarter]['source'] < 0){
                            $leftover = $monthlySum[$quarter]['consumption'];
                        }else{
                            $leftover = $monthlySum[$quarter]['consumption'] - $monthlySum[$quarter]['source'];
                        }
                        $monthlySum[$quarter]['leftover'] = $leftover > 0 ? round($leftover, 3) : 0;
                        $monthlySum[$quarter]['laps'] = $leftover < 0 ? round(abs($leftover), 3) : 0;
                        $monthlySum[$quarter]['type'] = 2;
                    
                        // 'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0
                    }
                }else{
                    for($p = 0; $p < $iterationCount; $p++){
                        $month = $Consumption[$p]['month'];
                        $consumption = $Consumption[$p]['consumption'];
                        $source = $finalResult[$p]['source'];
                        if (!isset($monthlySum[$month])) {

                            $monthlySum[$month]['source'] = 0;
                            $monthlySum[$month]['consumption'] = 0;
                        }
                        $monthlySum[$month]['month'] = $month;
                        $monthlySum[$month]['source'] += round($source, 3);
                        $monthlySum[$month]['consumption'] += round($consumption,3);
                        if($monthlySum[$month]['source'] < 0){
                            $leftover = $monthlySum[$month]['consumption'];
                        }else{
                            $leftover = $monthlySum[$month]['consumption'] - $monthlySum[$month]['source'];
                        }
                        $monthlySum[$month]['leftover'] = $leftover > 0 ? round($leftover, 3) : 0;
                        $monthlySum[$month]['laps'] = $leftover < 0 ? round(abs($leftover), 3) : 0;
                        $monthlySum[$month]['type'] = 1;
                    
                        // 'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0
                    }
                }
            }
            
            

            // $resutArray = [];
            // $graphArray = [];
            
            
            // for($p = 0; $p < $iterationCount; $p++){
                
            //     $leftover = $Consumption[$p]['consumption'] - $finalResult[$p]['source'];
            //     $resutArray[$p] = [
            //         'month' => $Consumption[$p]['month'],
            //         'day' => $Consumption[$p]['day'],
            //         'hours' => $Consumption[$p]['hours'],
            //         'start' => $Consumption[$p]['start'] ,
            //         "end" => $Consumption[$p]['end'],
            //         "consumption" => $Consumption[$p]['consumption'],
            //         "source" => $finalResult[$p]['source'],
            //         'leftover_demand' => $leftover > 0 ? round($leftover, 3) : 0,
            //         'laps_unit' => $leftover < 0 ? round(abs($leftover), 3) : 0
            //     ];
            //     $graphArray[$p] = [
            //         'month' => $Consumption[$p]['month'],
            //         'day' => $Consumption[$p]['day'],
            //         'hours' => $Consumption[$p]['hours'],
            //         'start' => $Consumption[$p]['start'] ,
            //         "end" => $Consumption[$p]['end'],
            //         "consumption" => $Consumption[$p]['consumption'],
            //         "source" => $finalResult[$p]['source'],
            //         'leftover_demand' => $leftover,
            //         'date' => Carbon::create(2023, $Consumption[$p]['month'], $Consumption[$p]['day'])
                    
            //     ];
            // }
            
           
            
            // dd($list);
            return view('mapping.many-to-many', [ 'type' => $request->type, 'current_client' => $sessionId, 'client_list' => $client_list, 'monthlySum' => $monthlySum, 'individualmonthlySum' => $individualmonthlySum, 'consumption_list' => $consumption_list, 'selected_consumption' => $request->consumption_point_id, 'selected_source' => $check_mapping, 'source_list' => $list, 'graph_type' => $graph_type]);
        }catch (\Illuminate\Validation\ValidationException $exception) {
            // Validation failed
            $errors = $exception->validator->errors();

            // Redirect back with errors or handle them as needed
            return redirect()->back()->withErrors($errors)->withInput();
        }
        catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->route('mapping.index')->with('error',__('Someting went wrong while creating profile.')) ->withInput();
        }
        
    }

    public function GetSourceProfile($id)
    {
        $consumption = $id;
        $check_mapping = Mapping::with(['source_profile'])->where('consumption_point_id', $consumption)->get();

        $uniqueResults = $check_mapping->unique(function ($item) {
            return $item->source_profile->id . '-' . $item->source_profile->source_name;
        })->values()->all();

        $result = [];
        foreach($uniqueResults as $key => $item){
            if(isset($item->source_profile)){
                $result[] = [
                    'id' => $item->source_profile->id,
                    'name' => $item->source_profile->source_name
                ];
            }
        }
        
        return response()->json($result);
        // dd($check_mapping);
    }
}
