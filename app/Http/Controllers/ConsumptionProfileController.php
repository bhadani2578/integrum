<?php

namespace App\Http\Controllers;

use App\Models\ConsumptionProfile;
use App\Models\ConsumptionEdType;
use App\Models\ConsumptionDayShift;
use App\Models\ConsumptionTod;
use App\Models\State;
use App\Models\Voltage;
use App\Models\Discom;
use App\Models\Industry;
use App\Models\Client;
use App\Models\TariffCategory;
use App\Models\TodStateConsumptionData;
use App\Models\WeekDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportConsumptionProfile;
use App\Exports\ExportConsumptionProfile;
use App\Models\HourlyConsumptionData;
use App\Models\AnnualConsumptionData;
use App\Models\MonthlyConsumptionData;
use App\Models\TodConsumptionData;
use App\Models\WeeklyConsumptionData;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\TodStateSlot;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class ConsumptionProfileController extends Controller
{
    public static $hours = [
        '00:00' => '00:00',
        '01:00' => '01:00',
        '02:00' => '02:00',
        '03:00' => '03:00',
        '04:00' => '04:00',
        '05:00' => '05:00',
        '06:00' => '06:00',
        '07:00' => '07:00',
        '08:00' => '08:00',
        '09:00' => '09:00',
        '10:00' => '10:00',
        '11:00' => '11:00',
        '12:00' => '12:00',
        '13:00' => '13:00',
        '14:00' => '14:00',
        '15:00' => '15:00',
        '16:00' => '16:00',
        '17:00' => '17:00',
        '18:00' => '18:00',
        '19:00' => '19:00',
        '20:00' => '20:00',
        '21:00' => '21:00',
        '22:00' => '22:00',
        '23:00' => '23:00',
        '24:00' => '24:00'
    ];

    public static $units = [
        '1' => 'KVA',
        '2' => 'KW',
        '3' => 'MW',
        '4' => 'GW'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sessionId = Session::get('client_detail');
        $consumption_list = ConsumptionProfile::with('ed_detail')->get()->sortByDesc('created_at')->where('client_id',$sessionId->id);
        $client_list = Client::get();

        return view('consumption_profile.index',  ['client_list' => $client_list, 'current_client' => $sessionId,'consumption_list' => $consumption_list]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


        $state_list = State::all();
        $voltage_list = Voltage::all();
        $industry = Industry::all();
        $week_day = WeekDay::all();
        $tariff_category = TariffCategory::all();
        $sessionId = Session::get('client_detail');
        $client_list = Client::get();

        return view('consumption_profile.create', ['units' => self::$units, 'hours' => self::$hours, 'day_list' => $week_day, 'tariff_category' => $tariff_category, 'client_list' => $client_list, 'current_client' => $sessionId, 'state_list' => $state_list, 'voltage_list' => $voltage_list, 'industry' => $industry]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'point_name' => ['required', 'string', 'max:255'],
                'state_id' => ['required', 'not_in:0'],
                'voltage_id' => ['required', 'not_in:0'],
                'discom_id' => ['required', 'not_in:0'],
                'discom_category_id' => ['required', 'not_in:0'],
                'contract_unit' => ['required', 'not_in:0'],
                'ed_type' => ['required', 'not_in:0'],
                'waiver_time' => ['required', 'not_in:0'],
                'voltage_id' => ['required', 'not_in:0'],
                'wheeling_charge' => ['required'],
                // 'available_upto' => ['required'],
                // 'rebate_type' => ['required', 'not_in:0'],
                'category_consumption_id' => ['required', 'not_in:0'],
                'granularity_level_id' => ['required', 'not_in:0'],
                'consumption_file_path' => ['required']
            ]);
            if(isset($request['consumption_file_path']) && !empty($request['consumption_file_path'])){
                $profile_doc = 'consumption_profile_' .time().'.'.$request['consumption_file_path']->extension();
                $path = public_path().'/documents/consumption_profile';
                File::makeDirectory($path, $mode = 0777, true, true);
                $request['consumption_file_path']->move($path, $profile_doc);

                // Perform additional header validation on the file if required
                $filePath = $path . '/' . $profile_doc;

                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                // Get the values of the first column
                $firstColumnValues = [];
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cell = $worksheet->getCellByColumnAndRow(1, $row);
                    $value = $cell->getValue();
                    if ($value !== null) {
                        $firstColumnValues[] = $value;
                    }

                }

                // Get the headers from the first row
                $actualHeaders = [];
                foreach ($worksheet->getRowIterator(1) as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $cellValue = $cell->getValue();
                        if ($cellValue !== null) {
                            $actualHeaders[] = $cellValue;
                        }
                    }
                    break; // Only read the first row
                }

                if($request['granularity_level_id'] == 1){
                    $annual_header = ["Annual", "Proportion units lower on sundays and holidays"];

                    if($actualHeaders != $annual_header){
                        unlink($filePath);
                        return back()->withInput()->withErrors(['consumption_file_path' => 'Please Select Proper hourly file.']);
                    }
                }else if($request['granularity_level_id'] == 2){
                    $month_header = ["Months", "Consumed units"];

                    if($actualHeaders != $month_header){
                        unlink($filePath);
                        return back()->withInput()->withErrors(['consumption_file_path' => 'Please Select Proper monthly file.']);
                    }
                }else if($request['granularity_level_id'] == 3){
                    $get_slot = TodStateSlot::where('state_id', $request['state_id'])->pluck('slot')->toArray();
                    if($firstColumnValues != $get_slot){
                        unlink($filePath);
                        return back()->withInput()->withErrors(['consumption_file_path' => 'Please Select Proper slot wise file.']);
                    }
                }
                else if($request['granularity_level_id'] == 4){
                    $hour_header = ["Hour", "Consumed units"];

                    if($actualHeaders != $hour_header){
                        unlink($filePath);
                        return back()->withInput()->withErrors(['consumption_file_path' => 'Please Select Proper hourly file.']);
                    }
                }
                else if($request['granularity_level_id'] == 5){
                    $week_header = ["Week", "Consumed units"];

                    if($actualHeaders != $week_header){
                        unlink($filePath);
                        return back()->withInput()->withErrors(['consumption_file_path' => 'Please Select Proper weekly file.']);
                    }
                }
            }

            $profile = ConsumptionProfile::create([
                'point_name' => $request['point_name'],
                'client_id' => Session::get('client_detail')->id,
                'state_id' => $request['state_id'],
                'voltage_id' => $request['voltage_id'],
                'discom_id' => $request['discom_id'],
                'wheeling_charge' => $request['wheeling_charge'],
                'discom_category_id' => $request['discom_category_id'],
                'contract_demand' => $request['contract_demand'],
                'contract_unit' => $request['contract_unit'],
                'contract_demand_limitation' => $request['contract_demand_limitation'],
                'category_consumption_id' => $request['category_consumption_id'],
                'granularity_level_id' => $request['granularity_level_id'],
                'consumption_file_path' => isset($request['consumption_file_path']) && !empty($request['consumption_file_path']) ? "documents/consumption_profile/" .$profile_doc : NULL,

            ]);

            ConsumptionEdType::create([
                'profile_id' => $profile->id,
                'ed_type' => $request['ed_type'],
                'waiver_time' => $request['waiver_time'],
                'available_upto' => $request['available_upto'],
                'waiver_month' => $request['waiver_month'],
                'waiver_year' => $request['waiver_year'],
                'rebate_type' => $request['rebate_type'],
                'rebate_value' => $request['rebate_value']
            ]);

            ConsumptionDayShift::create([
                'profile_id' => $profile->id,
                'day_start' => $request['day_start'],
                'day_end' => $request['day_end'],
                'shift_start' => $request['shift_start'],
                'shift_end' => $request['shift_end']
            ]);


            if(isset($request['consumption_file_path']) && !empty($request['consumption_file_path'])){

                $path = public_path().'/documents/consumption_profile';
                try{
                    if($request['granularity_level_id'] == 1){
                        $existingProfile = AnnualConsumptionData::where('profile_id', $profile->id)->first();
                        if ($existingProfile) {
                            // Delete the existing record
                            $existingProfile->forceDelete();
                        }
                    }else if($request['granularity_level_id'] == 2){
                        $existingMonthProfile = MonthlyConsumptionData::where('profile_id', $profile->id)->get();
                        if ($existingMonthProfile) {
                            // Delete the existing record
                            MonthlyConsumptionData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }else if($request['granularity_level_id'] == 3){
                        $existingMonthProfile = TodStateConsumptionData::where('profile_id', $profile->id)->get();
                        if ($existingMonthProfile) {
                            // Delete the existing record
                            TodStateConsumptionData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }else if($request['granularity_level_id'] == 4) {
                        $check_hours_data = HourlyConsumptionData::where('profile_id', $profile->id)->get();
                        if(count($check_hours_data) > 0){
                            HourlyConsumptionData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }
                    else if($request['granularity_level_id'] == 5) {
                        $check_week_data = WeeklyConsumptionData::where('profile_id', $profile->id)->get();
                        if(count($check_week_data) > 0){
                            WeeklyConsumptionData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }

                    Excel::import(new ImportConsumptionProfile($profile->id, $request['granularity_level_id'], $request['state_id']), $path."/". $profile_doc);
                }
                catch (\Exception $e) {
                    dd($e);
                }
            }
            return redirect()->route('consumption_profile.edit', ['consumption_profile' => $profile->id, 'from_store' => true])->with('success','Consumption Profile added successfully.');


        }
        catch (\Illuminate\Validation\ValidationException $exception) {
            // Validation failed
            $errors = $exception->validator->errors();

            // Redirect back with errors or handle them as needed
            return redirect()->back()->withErrors($errors)->withInput();
        }
        catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->route('consumption_profile.index')->with('error',__('Someting went wrong while creating profile.')) ->withInput();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConsumptionProfile  $consumptionProfile
     * @return \Illuminate\Http\Response
     */
    public function show(ConsumptionProfile $consumptionProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ConsumptionProfile  $consumptionProfile
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fromStore = request()->query('from_store', false);

        $sessionId = Session::get('client_detail');
        $client_list = Client::get();
        $state_list = State::all();
        $voltage_list = Voltage::all();
        $industry = Industry::all();
        $week_day = WeekDay::all();
        $tariff_category = TariffCategory::all();
        $profile_details = ConsumptionProfile::with(['ed_detail', 'day_shift', 'tod_value'])->find($id);
        $tod_detail = TodStateSlot::where('state_id', $profile_details->state_id)->get();
        $slotData = [];
        // if($profile_details->granularity_level_id == 1){
        //     $annual_unit = AnnualConsumptionData::where('profile_id', $profile_details->id)->select('year_unit')->first();

        //     $currentYear = 2023;
        //     $startOfYear = Carbon::create($currentYear, 1, 1)->startOfYear();
        //     $endOfYear = Carbon::create($currentYear, 12, 31)->endOfYear();

        //     $totalHours = $startOfYear->diffInHours($endOfYear);


        //     $unitPerHour = $annual_unit['year_unit'];
        //     $sumRangeValues = 0;

        //     foreach($tod_detail as $value){
        //         $slot = $value['slot'];

        //         preg_match('/(\d+:\d+)\sto\s(\d+:\d+)/', $slot, $matches);

        //         $percentage = ConsumptionTod::where('profile_id', $profile_details->id)->where('tod_start', $matches[1])->where('tod_end', $matches[2])->select('tod_value')->get();

        //         $slotData[] = [
        //             'start_slot' => $matches[1],
        //             'end_slot' => $matches[2],
        //             'average_consumed_unit' => $averageConsumedUnit,
        //             'percentage' => $percentage
        //         ];

        //     }
        // }else if($profile_details->granularity_level_id == 2){
        //     foreach($tod_detail as $value){
        //         $slot = $value['slot'];

        //         preg_match('/(\d+:\d+)\sto\s(\d+:\d+)/', $slot, $matches);


        //         $slotData[] = [
        //             'start_slot' => $matches[1],
        //             'end_slot' => $matches[2],
        //             'average_consumed_unit' => 0,
        //             'percentage' => 0
        //         ];

        //     }
        // }else if($profile_details->granularity_level_id == 3){
        //     foreach($tod_detail as $value){
        //         $slot = $value['slot'];

        //         preg_match('/(\d+:\d+)\sto\s(\d+:\d+)/', $slot, $matches);


        //         $slotData[] = [
        //             'start_slot' => $matches[1],
        //             'end_slot' => $matches[2],
        //             'average_consumed_unit' => 0,
        //             'percentage' => 0
        //         ];

        //     }
        // }else if($profile_details->granularity_level_id == 4){
        //     foreach($tod_detail as $value){
        //         $slot = $value['slot'];

        //         preg_match('/(\d+:\d+)\sto\s(\d+:\d+)/', $slot, $matches);


        //         $slotData[] = [
        //             'start_slot' => $matches[1],
        //             'end_slot' => $matches[2],
        //             'average_consumed_unit' => 0,
        //             'percentage' => 0
        //         ];

        //     }
        // }else if($profile_details->granularity_level_id == 5){
        //     foreach($tod_detail as $value){
        //         $slot = $value['slot'];
        //         preg_match('/(\d+:\d+)\sto\s(\d+:\d+)/', $slot, $matches);
        //         $slotData[] = [
        //             'start_slot' => $matches[1],
        //             'end_slot' => $matches[2],
        //             'average_consumed_unit' => 0,
        //             'percentage' => 0
        //         ];

        //     }
        // }
        foreach($tod_detail as $value){
            $slot = $value['slot'];

            preg_match('/(\d+:\d+)\sto\s(\d+:\d+)/', $slot, $matches);

            $percentage = ConsumptionTod::where('profile_id', $profile_details->id)->where('tod_start', $matches[1])->where('tod_end', $matches[2])->select('tod_value')->first();

            
            $startHour = Carbon::parse($matches[1])->hour;
            $endHour = Carbon::parse($matches[2])->hour;
            // dd($percentage);
            if(!isset($percentage)){
                
                if($profile_details->granularity_level_id == 1 || $profile_details->granularity_level_id == 2 || $profile_details->granularity_level_id == 5){
                   

                    if($startHour < $endHour){
                        $hoursInTimeSlot = $endHour - $startHour;
                    }else{
                        $hoursInTimeSlot = ($endHour + 24) - $startHour;
                    }

                    $slotPercentage = number_format(($hoursInTimeSlot / 24) * 100, 2);
                }
                elseif($profile_details->granularity_level_id == 3){
                    $data = TodStateConsumptionData::where('profile_id', $profile_details->id)->select('slot', 'jan', 'feb', 'mar','apr','may','jun','jul','aug','sep','oct','nov','dec','consumed_unit')->get();
                    $firstTimeSlot = null;
                    $allTimeSlotsSum = 0;
                    foreach ($data as $dataItem) {
                        
                        $abc = $dataItem->toArray();
                        
                        if ($firstTimeSlot === null) {
                            $firstTimeSlot = array_sum(array_slice($abc, 1, 12)); // Sum columns 'jan' to 'dec'
                        }
                        $allTimeSlotsSum += $firstTimeSlot;
                        
                    }
                   
                    
                    $slotPercentage = ($firstTimeSlot / $allTimeSlotsSum) * 100;
                    
                    
                }
               
            }  
            // dd($percentage); 
            $slotData[] = [
                'start_slot' => $matches[1],
                'end_slot' => $matches[2],
                'percentage' => isset($percentage) ? $percentage->tod_value : (isset($slotPercentage) ? $slotPercentage : 0)
            ];

        }
        // dd($slotData);

        if(isset($profile_details) && !empty($profile_details)){
            $discom_list = Discom::where('state_id', $profile_details->state_id)->get();
        }else{
            $discom_list = [];
        }

        return view('consumption_profile.edit',  ['fromStore' => $fromStore, 'units' => self::$units, 'hours' => self::$hours, 'day_list' => $week_day, 'tariff_category' => $tariff_category, 'discom_list' => $discom_list, 'state_list' => $state_list, 'voltage_list' => $voltage_list, 'industry' => $industry, 'profile_details' => $profile_details, 'client_list' => $client_list, 'current_client' => $sessionId, 'slot_data' => $slotData]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ConsumptionProfile  $consumptionProfile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try
        {
        $request->validate([
            'point_name' => ['required', 'string', 'max:255'],
            'state_id' => ['required', 'not_in:0'],
            'voltage_id' => ['required', 'not_in:0'],
            'discom_id' => ['required', 'not_in:0'],
            'wheeling_charge' => ['required'],
            'discom_category_id' => ['required', 'not_in:0'],
            'contract_demand'=> ['required'],
            'contract_demand_limitation'=> ['required'],
            'ed_type' => ['required', 'not_in:0'],
            'waiver_time' => ['required', 'not_in:0'],
            // 'available_upto' => ['required'],
            'contract_unit' => ['required', 'not_in:0'],
            'category_consumption_id' => ['required', 'not_in:0'],
            'granularity_level_id' => ['required', 'not_in:0'],
            'consumption_file_path' => [
                Rule::requiredIf(function () use ($id) {
                    // Check if file path exists in the database for the ID
                    return DB::table('consumption_profiles')->where('id', $id)->whereNull('consumption_file_path')->exists();
                }),
            ],

        ]);
        $profile = ConsumptionProfile::find($id);
        $profile->point_name = $request['point_name'];
        $profile->state_id = $request['state_id'];
        $profile->voltage_id = $request['voltage_id'];
        $profile->discom_id = $request['discom_id'];
        $profile->wheeling_charge = $request['wheeling_charge'];
        $profile->discom_category_id = $request['discom_category_id'];
        $profile->contract_demand = $request['contract_demand'];
        $profile->contract_unit = $request['contract_unit'];
        $profile->contract_demand_limitation = $request['contract_demand_limitation'];
        $profile->category_consumption_id = $request['category_consumption_id'];
        $profile->granularity_level_id = $request['granularity_level_id'];
        if(isset($request['consumption_file_path']) && !empty($request['consumption_file_path'])){
            $profile_doc = 'consumption_profile_' .time().'.'.$request['consumption_file_path']->extension();
            $path = public_path().'/documents/consumption_profile';
            File::makeDirectory($path, $mode = 0777, true, true);
            $request['consumption_file_path']->move($path, $profile_doc);

            // Perform additional header validation on the file if required
            $filePath = $path . '/' . $profile_doc;


            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            // Get the values of the first column
            $firstColumnValues = [];
            for ($row = 1; $row <= $highestRow; $row++) {
                $cell = $worksheet->getCellByColumnAndRow(1, $row);
                $value = $cell->getValue();
                if ($value !== null) {
                    $firstColumnValues[] = $value;
                }

            }

            // Get the headers from the first row
            $actualHeaders = [];
            foreach ($worksheet->getRowIterator(1) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $cellValue = $cell->getValue();
                    if ($cellValue !== null) {
                        $actualHeaders[] = $cellValue;
                    }
                }
                break; // Only read the first row
            }

            if($request['granularity_level_id'] == 1){
                $annual_header = ["Annual", "Proportion units lower on sundays and holidays"];

                if($actualHeaders != $annual_header){
                    unlink($filePath);
                    return back()->withErrors(['consumption_file_path' => 'Please Select Proper hourly file.']);
                }
            }else if($request['granularity_level_id'] == 2){
                $month_header = ["Months", "Consumed units"];

                if($actualHeaders != $month_header){
                    unlink($filePath);
                    return back()->withErrors(['consumption_file_path' => 'Please Select Proper monthly file.']);
                }
            }else if($request['granularity_level_id'] == 3){

                $get_slot = TodStateSlot::where('state_id', $request['state_id'])->pluck('slot')->toArray();

                if($firstColumnValues != $get_slot){
                    unlink($filePath);
                    return back()->withErrors(['consumption_file_path' => 'Please Select Proper slot wise file.']);
                }
            }
            else if($request['granularity_level_id'] == 4)
            {
                $hour_header = ["Hour", "Consumed units"];

                if($actualHeaders != $hour_header){
                    unlink($filePath);
                    return back()->withErrors(['consumption_file_path' => 'Please Select Proper hourly file.']);
                }
            }
            else if($request['granularity_level_id'] == 5){
                $week_header = ["Week", "Consumed units"];

                if($actualHeaders != $week_header){
                    unlink($filePath);
                    return back()->withErrors(['consumption_file_path' => 'Please Select Proper weekly file.']);
                }
            }
            $profile->consumption_file_path = "documents/consumption_profile/" .$profile_doc;

        }
        $profile->save();

        ConsumptionEdType::updateOrCreate(['profile_id' => $id], [
            'ed_type' => $request['ed_type'],
            'waiver_time' => ($request['ed_type'] == 1) ? $request['waiver_time'] : 1,
            'available_upto' => ($request['ed_type'] == 1) ? $request['available_upto'] : NULL,
            'waiver_month' => ($request['ed_type'] == 1) ? $request['waiver_month'] : NULL,
            'waiver_year' => ($request['ed_type'] == 1) ? $request['waiver_year'] : NULL,
            'rebate_type' => ($request['ed_type'] == 2) ? $request['rebate_type'] : NULL,
            'rebate_value' => ($request['ed_type'] == 2) ? $request['rebate_value'] : NULL
        ]);


        ConsumptionDayShift::updateOrCreate(['profile_id' => $id], [
            'day_start' => $request['day_start'],
            'day_end' => $request['day_end'],
            'shift_start' => $request['shift_start'],
            'shift_end' => $request['shift_end']
        ]);

        if(isset($request['consumption_file_path']) && !empty($request['consumption_file_path'])){

            $path = public_path().'/documents/consumption_profile';
            try{
                if($request['granularity_level_id'] == 1){
                    $existingProfile = AnnualConsumptionData::where('profile_id', $profile->id)->first();
                    if ($existingProfile) {
                        // Delete the existing record
                        $existingProfile->forceDelete();
                    }
                }else if($request['granularity_level_id'] == 2){
                    $existingMonthProfile = MonthlyConsumptionData::where('profile_id', $profile->id)->get();
                    if ($existingMonthProfile) {
                        // Delete the existing record
                        MonthlyConsumptionData::where('profile_id', $profile->id)->forceDelete();
                    }
                }else if($request['granularity_level_id'] == 3){
                    $existingMonthProfile = TodStateConsumptionData::where('profile_id', $profile->id)->get();

                    if ($existingMonthProfile) {
                        // Delete the existing record
                        TodStateConsumptionData::where('profile_id', $profile->id)->forceDelete();
                    }
                }else if($request['granularity_level_id'] == 4) {
                    $check_hours_data = HourlyConsumptionData::where('profile_id', $profile->id)->get();
                    if(count($check_hours_data) > 0){
                        HourlyConsumptionData::where('profile_id', $profile->id)->forceDelete();
                    }
                }
                else if($request['granularity_level_id'] == 5) {
                    $check_hours_data = WeeklyConsumptionData::where('profile_id', $profile->id)->get();
                    if(count($check_hours_data) > 0){
                        WeeklyConsumptionData::where('profile_id', $profile->id)->forceDelete();
                    }
                }
                Excel::import(new ImportConsumptionProfile($profile->id, $request['granularity_level_id']), $path."/". $profile_doc);
            }
            catch (\Exception $e) {
                dd($e);
            }
        }
        return redirect()->back()->with('success', 'Consumption Profile updated successfuly.')->with('showToast', true);
    }


    catch (\Illuminate\Validation\ValidationException $exception) {

        // Validation failed
        $errors = $exception->validator->errors();

        // Redirect back with errors or handle them as needed
        return redirect()->back()->withErrors($errors)->withInput();
    }
    catch (\Exception $e) {
        dd($e->getMessage());
        DB::rollback();
        return redirect()->back()->with('error',__("Something went wrong while creating profile."));
    }

}


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConsumptionProfile  $consumptionProfile
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ConsumptionProfile::find($id)->delete();
        return redirect()->back();
    }

    /**
     * restore all client
     *
     * @return response()
     */
    public function restoreAll()
    {
        // restore all user
        ConsumptionProfile::onlyTrashed()->restore();
        // restore specific user
        // Project::withTrashed()->find($id)->restore();

        return redirect()->back();
    }

    public function getDiscom($stateId)
    {
        $discom_list = Discom::where('state_id', $stateId)->get();
        return response()->json($discom_list);
    }

    public function getWheelingCharge($discomId)
    {
        $wheelingCharge = Discom::where('id', $discomId)->get();
        return response()->json($wheelingCharge);
    }

    public function getTodData(Request $request)
    {

        $data = Excel::import(new ImportAnnualConsumptionProfile, $request->file('file')->store('temp'));

    }

    public function SaveTodTime(Request $request, $id)
    {
        if(isset($request['tod']) && count($request['tod']) > 0)
        {
            $percentageSum = 0;
            foreach ($request['tod'] as $slot) {
                $percentageSum += $slot['tod_percentage'];
            }
            if ((int)$percentageSum !== 100) {
                // Display error message or take appropriate action
                return response()->json(['error' => 'The sum of TOD percentage values should be equal to 100.'], 422);

            } else {
                ConsumptionTod::where('profile_id', $id)->delete();

                foreach($request['tod'] as $key => $value){
                    ConsumptionTod::create([
                        'profile_id' => $id,
                        'tod_slot_id' => $value['slot_id'],
                        'tod_start' => $value['tod_start'],
                        'tod_end' => $value['tod_end'],
                        'tod_value' => $value['tod_percentage']
                    ]);
                }
                $message = 'TOD slots created successfully';
            }

        }
        return response()->json(['message' => $message, 'redirect' => route('consumption_profile.edit', $id)]);

    }
        public function DeleteTodTime(Request $request,$id)
        {
            $records =  ConsumptionTod::where('profile_id', $id)->delete();
            return response()->json(['message' => 'TOD slots deleted successfully', 'redirect' => route('consumption_profile.edit', $id)]);
        }

    public function ConvertConsumotion(Request $request)
    {
        try{
            $intervals = [];
            if($request['granularity_level'] == 1)
            {
                $checking_working_day = ConsumptionDayShift::where('profile_id', $request['profile_id'])->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $data = AnnualConsumptionData::where('profile_id', $request['profile_id'])->get();
                $consumption_tod = ConsumptionTod::where('profile_id', $request['profile_id'])->get();

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
                    $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    $slot = [];

                    for ($k = 0; $k < $slotsPerHour; $k++) {

                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($j)->addMinutes($k * $request['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);
                        $dayOfWeek = Carbon::create($year, 1, 1, 0, 0, 0)->addHours($j)->dayOfWeek;
                        $hoursOfDay = Carbon::create($year, 1, 1, 0, 0, 0)->addHours($j)->hour;


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

                        $intervals[] = [
                            'month' => $startSlot->format('m'),
                            'day' => $startSlot->format('d'),
                            'hours' => $startSlot->format('H'),
                            'slots' => $startSlot->format('H:i') ." - ". $endSlot->format('H:i'),
                            'unit' => round($unitsPerSlot, 3)
                        ];
                    }


                }

                // dd(array_sum($crossCheck));
                // $intervals = array_slice($intervals, 0, 50);
                return response()->json(['status' => true, 'data' => $intervals]);

            }
            else if($request['granularity_level'] == 2){

                $data = MonthlyConsumptionData::where('profile_id', $request['profile_id'])->where('name', '!=', 'Proportion units lower on sundays and holidays')->select('name', 'consumed_unit')->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $request['profile_id'])->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $consumption_tod = ConsumptionTod::where('profile_id', $request['profile_id'])->get();
                $lower_unit = MonthlyConsumptionData::where('profile_id', $request['profile_id'])->where('name', '=', 'Proportion units lower on sundays and holidays')->select('consumed_unit')->first()->consumed_unit;

                $holidayUnit = $lower_unit / 100;


                foreach($data as $key => $value){
                    $currentYear = 2023;

                    $startOfMonth = Carbon::create($currentYear, $value['name'], 1, 0, 0, 0);
                    $endOfMonth = Carbon::create($currentYear, $value['name'], 1, 23, 59, 59)->endOfMonth();

                    $totalHours = $startOfMonth->diffInHours($endOfMonth);
                    $totalDays = $startOfMonth->daysInMonth;

                    $slot = [];
                    for ($a = 0; $a < $totalHours; $a++)
                    {

                        $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)


                        for ($k = 0; $k < $slotsPerHour; $k++) {
                            // echo $k;
                            $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($a)->addMinutes($k * $request['chunk_time']);
                            $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);

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

                            // $slot[$k] = [
                            //     'start' => $startSlot->format('H:i'),
                            //     'end' => $endSlot->format('H:i'),
                            //     'consumption' => round($unitsPerSlot, 3)
                            // ];
                            $intervals[] = [
                                'month' => $startOfMonth->format('m'),
                                'day' => $startSlot->format('d'),
                                'hours' => $startSlot->format('H'),
                                'slots' => $startSlot->format('H:i') ." - ". $endSlot->format('H:i'),
                                'unit' => round($unitsPerSlot, 3)
                            ];
                        }

                    }


                    // dd(array_sum($crossCheck));
                    // exit;
                }
                $intervals = array_slice($intervals, 0, 50);
                // dd($intervals);
                return response()->json(['status' => true, 'data' => $intervals]);

            }else if($request['granularity_level'] == 3){
                $data = TodStateConsumptionData::where('profile_id', $request['profile_id'])->select('slot', 'jan', 'feb', 'mar','apr','may','jun','jul','aug','sep','oct','nov','dec','consumed_unit')->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $request['profile_id'])->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $consumption_tod = ConsumptionTod::where('profile_id', $request['profile_id'])->get();

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

                                $chunkData = [
                                    'month' => $month < 10 ? '0'.$month : $month ,
                                    'day' => $currentDate->format('d'),
                                    'hours' => $start->format('H'),
                                    'slots' => $slot_time,
                                    'unit' => round($unitsPerSlot, 3),
                                ];
                                $chunks[] = $chunkData;
                                $start->addMinutes($request['chunk_time']);
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


                return response()->json(['status' => true, 'data' => $chunks]);
            }
            else if($request['granularity_level'] == 4)
            {
                $data = HourlyConsumptionData::where('profile_id', $request['profile_id'])->select('hours', 'consumed_unit')->get();

                foreach ($data as $key => $value) {

                    $slotsPerHour = 60 / $request['chunk_time'];// 4 slots per hour (15 minutes per slot)
                    $unitsPerSlot = $value['consumed_unit'] / $slotsPerHour;
                    $slot = [];
                    for ($i = 0; $i < $slotsPerHour; $i++) {

                        // $startSlot = Carbon::create(2023, 1, 1)->addHours($value['hours'])->addMinutes($i * $request['chunk_time']);
                        $startSlot = Carbon::create(2023, 1, 1, 0, 0, 0)->addHours($value['hours'] - 1)->addMinutes($i * $request['chunk_time']);
                        $endSlot = $startSlot->copy()->addMinutes($request['chunk_time']);

                        // $slot[$i] = [
                        //     'start' => $startSlot->format('H:i'),
                        //     'end' => $endSlot->format('H:i'),
                        //     'consumption' => round($unitsPerSlot, 3)
                        // ];

                        $intervals[] = [
                            'month' => $startSlot->format('m'),
                            'day' => $startSlot->format('d'),
                            'hours' => $startSlot->format('H'),
                            'slots' => $startSlot->format('H:i') . " - " .$endSlot->format('H:i'),
                            'unit' => round($unitsPerSlot, 3)
                        ];
                    }


                }

                $intervals = array_slice($intervals, 0, 50);
                return response()->json(['status' => true, 'data' => $intervals]);
            }else if ($request['granularity_level'] == 5)
            {
                $data = WeeklyConsumptionData::where('profile_id', $request['profile_id'])->where('weeks', '!=', 'Proportion units lower on sundays and holidays')->select('weeks', 'consumed_unit')->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $request['profile_id'])->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $lower_unit = WeeklyConsumptionData::where('profile_id', $request['profile_id'])->where('weeks', '=', 'Proportion units lower on sundays and holidays')->select('consumed_unit')->first()->consumed_unit;
                $consumption_tod = ConsumptionTod::where('profile_id', $request['profile_id'])->get();

                $holidayUnit = $lower_unit / 100;

                foreach ($data as $key => $value) {

                    $week = $value['weeks'];
                    $consumedUnit = ($value['consumed_unit'] / 7) / 24;
                    $slotPerHour = 60 / $request['chunk_time'];


                    // $consumedUnitPerInterval = $consumedUnit / $slotPerHour;
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

                                $intervals[] = [
                                    'month' => $startDate->month,
                                    'day' => $startTime->format('d'),
                                    'hours' => $startTime->format('H'),
                                    'slots' => $chunkStart . " - " . $chunkEnd,
                                    'unit' => round($unitsPerSlot, 3)
                                ];
                                $minute += $request['chunk_time'];
                            }

                        }
                    }
                }

                $intervals = array_slice($intervals, 0, 50);
                return response()->json(['status' => true, 'data' => $intervals]);
            }
        }
         catch (\Exception $e) {
            dd($e);
            return response()->json(['status' => false]);
        }
    }


    public function updateGranularity(Request $request, $id)
    {
        $model = ConsumptionProfile::find($id);
        if ($model) {
            $model->granularity_id = $request->input('granularity_id');
            $model->save();
            return response()->json(['message' => 'Update successful']);
        }
        return response()->json(['message' => 'Model not found'], 404);
    }

    public function ExportConvertConsumotion(Request $request)
    {
        $data = [
            'granularity_level' => $request['granularity_level'],
            'chunk_time' => $request['chunk_time'],
            'profile_id' => $request['profile_id']
            // Your data to export
        ];

        return Excel::download(new ExportConsumptionProfile($data), 'consumption.csv');

    }

    //  Get State wise slot data
    public function getStateSlot($stateId)
    {
        $slot_list = TodStateSlot::where('state_id', $stateId)->get();
        $slotData = [];
        foreach($slot_list as $value){
            $slot = $value['slot'];
            preg_match('/(\d+:\d+)\sto\s(\d+:\d+)/', $slot, $matches);
            $slotData[] = [
                'start_slot' => $matches[1],
                'end_slot' => $matches[2],
                'average_consumed_unit' => 0,
                'percentage' => 0
            ];

        }
        return response()->json($slotData);
    }
}
