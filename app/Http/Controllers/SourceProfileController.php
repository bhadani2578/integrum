<?php

namespace App\Http\Controllers;

use App\Exports\ExportSourceProfile;
use App\Imports\ImportSourceProfile;
use App\Models\AnnualSourceData;
use App\Models\ApplicablePeriods;
use App\Models\BankingArrangement;
use App\Models\Client;
use App\Models\ConsumptionDayShift;
use App\Models\Discom;
use App\Models\HourlySourceData;
use App\Models\LockingPeriod;
use App\Models\MonthlySourceData;
use App\Models\Settlement;
use App\Models\SourceProfile;
use App\Models\SourceTod;
use App\Models\State;
use App\Models\TariffCategory;
use App\Models\TodStateSlot;
use App\Models\TODStateSourceData;
use App\Models\TypeArrangement;
use App\Models\TypeContract;
use App\Models\TypeSource;
use App\Models\Voltage;
use App\Models\WeekDay;
use App\Models\WeeklySourceData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Validation\Rule;

class SourceProfileController extends Controller
{
    /**
     * Source Profile Listing
     */
    public function index()
    {
        $sessionId = Session::get('client_detail');
        $source_list = SourceProfile::with('voltage')->where('client_id',$sessionId->id)->orderBy('created_at', 'desc')->get();
        $client_list = Client::get();

        return view('source_profile.index',  ['client_list' => $client_list, 'current_client' => $sessionId,'source_list' => $source_list]);
    }

    /**
     * create source profile
     */
    public function create()
    {
        $type_source = TypeSource::all();
        $type_arrangement = TypeArrangement::all();
        $type_contract = TypeContract::all();
        $bank_arrangement = BankingArrangement::all();
        $state = State::all();
        $settlement = Settlement::all();
        $week_day = WeekDay::all();
        $client_list = Client::get();
        $tariff_category = TariffCategory::all();
        $applicable_period = ApplicablePeriods::all();
        $lockin_period = LockingPeriod::all();
        $voltage_id = Voltage::all();
        $sessionId = Session::get('client_detail');

        return view('source_profile.create', [
            'type_source' => $type_source, 'type_arrangement' => $type_arrangement,
            'type_contract' => $type_contract, 'bank_arrangement' => $bank_arrangement,
            'state' => $state, 'settlement' => $settlement,'day_list' => $week_day,
            'client_list'=>$client_list,'current_client' => $sessionId,'tariff_category'=>$tariff_category,
            'applicable_period'=>$applicable_period,'lockin_period'=>$lockin_period,'voltage_id'=>$voltage_id
        ]);
    }

    /**
     * store source profile
     */
    public function store(Request $request)
    {
        try
        {

                $rules = [
                    'source_name' => ['required', 'string', 'max:255'],
                    'type_source_id' => ['required', 'not_in:0'],
                    'type_contract_id' => ['required', 'not_in:0'],
                    'type_arrangement_id' => ['required', 'not_in:0'],
                    'banking_arragement_id' => ['required', 'not_in:0'],
                    'settlement_id' => ['required', 'not_in:0'],
                    'voltage_id' => ['required', 'not_in:0'],
                    'annual_traffic_type' => ['required'],
                    'granularity_level_id' => ['required', 'not_in:0'],
                    'source_profile_path' => ['required'],
                    'state_id' => ['required', 'not_in:0'],
                    'discoms_id' => ['required', 'not_in:0'],
                    //annual_traffic_value' => ['required'],
                    // 'locking_period_id' => ['required', 'not_in:0'],
                    // 'locking_period_month_id' => ['required', 'not_in:0'],

            ];
            if($request->type_contract_id != 1){
                $rules['quantum'] = ['required', 'numeric'];
                $rules['minimum_off_take'] = ['required'];
                $rules['applicable_period_id'] = ['required', 'not_in:0'];
                $rules['end_date'] = ['required'];
                $rules['start_date'] = ['required'];
            }
            $request->validate($rules);


            if(isset($request['source_profile_path']) && !empty($request['source_profile_path'])){
                $profile_doc = 'source_profile_' .time().'.'.$request['source_profile_path']->extension();
                $path = public_path().'/documents/source_profile';
                File::makeDirectory($path, $mode = 0777, true, true);
                $request['source_profile_path']->move($path, $profile_doc);

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
                        return back()->withInput()->withErrors(['source_profile_path' => 'Please Select Proper hourly file.']);
                    }
                }else if($request['granularity_level_id'] == 2){
                    $month_header = ["Months", "Generated units"];

                    if($actualHeaders != $month_header){
                        unlink($filePath);
                        return back()->withInput()->withErrors(['source_profile_path' => 'Please Select Proper monthly file.']);
                    }
                }else if($request['granularity_level_id'] == 3){

                    $get_slot = TodStateSlot::where('state_id', $request['state_id'])->pluck('slot')->toArray();

                    if($firstColumnValues != $get_slot){
                        unlink($filePath);
                        return back()->withInput()->withErrors(['source_profile_path' => 'Please Select Proper slot wise file.']);
                    }
                }
                else if($request['granularity_level_id'] == 4){
                    $week_header = ["Week", "Generated units"];

                    if($actualHeaders != $week_header){
                        unlink($filePath);
                        return back()->withInput()->withErrors(['source_profile_path' => 'Please Select Proper weekly file.']);
                    }
                }
                else if($request['granularity_level_id'] == 5){
                    $hour_header = ["Hour", "Generated units"];

                    if($actualHeaders != $hour_header){
                        unlink($filePath);
                        return back()->withInput()->withErrors(['source_profile_path' => 'Please Select Proper hourly file.']);
                    }
                }


            }

            $profile = SourceProfile::create([
                'client_id' => Session::get('client_detail')->id,
                'source_name' => $request['source_name'],
                'type_source_id' => $request['type_source_id'],
                'type_contract_id' => $request['type_contract_id'],
                'type_arrangement_id' => $request['type_arrangement_id'],
                'banking_arragement_id' => $request['banking_arragement_id'],
                'state_id' => $request['state_id'],
                'settlement_id' => $request['settlement_id'],
                'voltage_id' => $request['voltage_id'],
                'annual_traffic_type' => $request['annual_traffic_type'],
                'annual_traffic_value' => $request['annual_traffic_value'],
                'start_date' => $request['start_date'],
                'end_date'=> $request['end_date'],
                'quantum' => $request['quantum'],
                'loan' => $request['loan'],
                'annual_maintain' => $request['annual_maintain'],
                'insurance' => $request['insurance'],
                'revenue_unit' => $request['revenue_unit'],
                'depreciation_benefit' => $request['depreciation_benefit'],
                'transmission_charges' => $request['transmission_charges'],
                'wheeling_charges' => $request['wheeling_charges'],
                'electricity_duty' => $request['electricity_duty'],
                'asset_fees' => $request['asset_fees'],
                'energy_landed_cost' => $request['energy_landed_cost'],
                'statutory_charge' => $request['statutory_charge'],
                'minimum_off_take' => $request['minimum_off_take'],
                'supply_commitment'=> $request['supply_commitment'],
                'minimum_supply'=> $request['minimum_supply'],
                'applicable_period_id' => $request['applicable_period_id'],
                'locking_period_id' => $request['locking_period_id'],
                'locking_period_month_id' => $request['locking_period_month_id'],
                'granularity_level_id' => $request['granularity_level_id'],
                'granularity_id' => $request['granularity_id'],
                'discoms_id'=>$request['discoms_id'],
                'source_profile_path' => isset($request['source_profile_path']) && !empty($request['source_profile_path']) ? "documents/source_profile/" .$profile_doc : NULL,

            ]);

            if(isset($request['source_profile_path']) && !empty($request['source_profile_path'])){

                $path = public_path().'/documents/source_profile';
                try{
                    if($request['granularity_level_id'] == 1){
                        $existingProfile = AnnualSourceData::where('profile_id', $profile->id)->first();
                        if ($existingProfile) {
                            // Delete the existing record
                            $existingProfile->forceDelete();
                        }
                    }else if($request['granularity_level_id'] == 2){
                        $existingMonthProfile = MonthlySourceData::where('profile_id', $profile->id)->get();
                        if ($existingMonthProfile) {
                            // Delete the existing record
                            MonthlySourceData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }else if($request['granularity_level_id'] == 3){
                        $get_slot = TodStateSlot::where('state_id', $request['state_id'])->pluck('slot')->toArray();
                        if($firstColumnValues != $get_slot){
                            unlink($filePath);
                            return back()->withErrors(['consumption_file_path' => 'Please Select Proper slot wise file.']);
                        }
                    }else if($request['granularity_level_id'] == 5) {
                        $check_hours_data = HourlySourceData::where('profile_id', $profile->id)->get();
                        if(count($check_hours_data) > 0){
                            HourlySourceData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }
                    else if($request['granularity_level_id'] == 4) {
                        $check_hours_data = WeeklySourceData::where('profile_id', $profile->id)->get();
                        if(count($check_hours_data) > 0){
                            WeeklySourceData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }
                    Excel::import(new ImportSourceProfile($profile->id, $request['granularity_level_id']), $path."/". $profile_doc);
                }
                catch (\Exception $e) {
                    // dd($e);
                }
            }
            return redirect()->route('source_profile.edit', ['source_profile' => $profile->id,'from_store' => true])->with('showToast', true)->with('success', __('Source Profile added successfully.'));

        } catch (\Illuminate\Validation\ValidationException $exception) {
            // Validation failed
            $errors = $exception->validator->errors();
            
            // Redirect back with errors or handle them as needed
            return redirect()->back()->withErrors($errors)->withInput();
        }catch (\Exception $e) {
           dd($e);
            DB::rollback();
            return redirect()->route('source_profile.index')->with('error',__("Something went wrong while creating profile."));
        }
    }

    /**
     * edit source profile
     */
    public function edit($id)
    {
        $fromStoreSource = request()->query('from_store', false);
        $type_source = TypeSource::all();
        $type_arrangement = TypeArrangement::all();
        $type_contract = TypeContract::all();
        $bank_arrangement = BankingArrangement::all();
        $state = State::all();
        $settlement = Settlement::all();
        $week_day = WeekDay::all();
        $client_list = Client::get();
        $tariff_category = TariffCategory::all();
        $sessionId = Session::get('client_detail');
        $applicable_period = ApplicablePeriods::all();
        $lockin_period = LockingPeriod::all();
        $voltage_id = Voltage::all();
        $profile_details = SourceProfile::with(['day_shift','state', 'type_source', 'contract','arrangement','banking_arrangement'])->find($id);
        if(isset($profile_details) && !empty($profile_details)){
            $discom_list = Discom::where('state_id', $profile_details->state_id)->get();
        }else{
            $discom_list = [];
        }
        return view('source_profile.edit', [
            'type_source' => $type_source, 'type_arrangement' => $type_arrangement,
            'type_contract' => $type_contract, 'bank_arrangement' => $bank_arrangement,
            'state' => $state, 'settlement' => $settlement,'day_list' => $week_day,
            'client_list'=>$client_list,'current_client' => $sessionId,'tariff_category'=>$tariff_category,
            'discom_list' => $discom_list,'profile_details' => $profile_details,
            'applicable_period'=>$applicable_period,'lockin_period'=>$lockin_period,'voltage_id'=>$voltage_id,
            'fromStoreSource' => $fromStoreSource,
        ]);
    }
    /**
     * update profile
     */
    public function update(Request $request, $id)
    {
        try{
            $rules = [
                'source_name' => ['required', 'string', 'max:255'],
                'type_source_id' => ['required', 'not_in:0'],
                'type_contract_id' => ['required', 'not_in:0'],
                'type_arrangement_id' => ['required', 'not_in:0'],
                'banking_arragement_id' => ['required', 'not_in:0'],
                'state_id' => ['required', 'not_in:0'],
                'settlement_id' => ['required', 'not_in:0'],
                'discoms_id' => ['required', 'not_in:0'],
                'voltage_id' => ['required', 'not_in:0'],
                'annual_traffic_type' => ['required'],
                'annual_traffic_value' => ['required'],
                
                // 'locking_period_id' => ['required', 'not_in:0'],
                // 'locking_period_month_id' => ['required', 'not_in:0'],
                'granularity_level_id' => ['required', 'not_in:0'],
                'source_profile_path' => [
                    Rule::requiredIf(function () use ($id) {
                        // Check if file path exists in the database for the ID
                        return DB::table('source_profiles')->where('id', $id)->whereNull('source_profile_path')->exists();
                    }),
                ],
            ];

            if($request->type_contract_id != 1){
                
                $rules['quantum'] = ['required', 'numeric'];
                $rules['minimum_off_take'] = ['required'];
                $rules['applicable_period_id'] = ['required', 'not_in:0'];
                $rules['end_date'] = ['required'];
                $rules['start_date'] = ['required'];
            }
            $request->validate($rules);

            $profile = SourceProfile::find($id);
            $profile->source_name = $request['source_name'];
            $profile->type_source_id = $request['type_source_id'];
            $profile->type_contract_id = $request['type_contract_id'];
            $profile->type_arrangement_id = $request['type_arrangement_id'];
            $profile->banking_arragement_id = $request['banking_arragement_id'];
            $profile->state_id = $request['state_id'];
            $profile->settlement_id = $request['settlement_id'];
            $profile->voltage_id = $request['voltage_id'];
            $profile->annual_traffic_type = $request['annual_traffic_type'];
            $profile->annual_traffic_value = $request['annual_traffic_value'];
            $profile->start_date = $request['start_date'];
            $profile->end_date = $request['end_date'];
            $profile->quantum = $request['quantum'];
            $profile->loan = $request['loan'];
            $profile->annual_maintain = $request['annual_maintain'];
            $profile->insurance = $request['insurance'];
            $profile->revenue_unit = $request['revenue_unit'];
            $profile->depreciation_benefit = $request['depreciation_benefit'];
            $profile->transmission_charges = $request['transmission_charges'];
            $profile->wheeling_charges = $request['wheeling_charges'];
            $profile->electricity_duty = $request['electricity_duty'];
            $profile->asset_fees = $request['asset_fees'];
            $profile->energy_landed_cost = $request['energy_landed_cost'];
            $profile->statutory_charge = $request['statutory_charge'];
            $profile->discoms_id = $request['discoms_id'];
            $profile->minimum_off_take = $request['minimum_off_take'];
            $profile->applicable_period_id = $request['applicable_period_id'];
            $profile->supply_commitment = $request['supply_commitment'];
            $profile->minimum_supply = $request['minimum_supply'];
            $profile->locking_period_id = $request['locking_period_id'];
            $profile->locking_period_month_id = $request['locking_period_month_id'];
            $profile->granularity_level_id = $request['granularity_level_id'];
            $profile->granularity_id = $request['granularity_id'];
            if(isset($request['source_profile_path']) && !empty($request['source_profile_path']))
            {
                $profile_doc = 'source_profile_path' .time().'.'.$request['source_profile_path']->extension();
                $path = public_path().'/documents/source_profile';
                File::makeDirectory($path, $mode = 0777, true, true);
                $request['source_profile_path']->move($path, $profile_doc);
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
                        return back()->withErrors(['source_profile_path' => 'Please Select Proper hourly file.']);
                    }
                }else if($request['granularity_level_id'] == 2){
                    $month_header = ["Months", "Generated units"];

                    if($actualHeaders != $month_header){
                        unlink($filePath);
                        return back()->withErrors(['source_profile_path' => 'Please Select Proper monthly file.']);
                    }
                }else if($request['granularity_level_id'] == 3){
                    $get_slot = TodStateSlot::where('state_id', $request['state_id'])->pluck('slot')->toArray();


                    if($firstColumnValues != $get_slot){
                        unlink($filePath);
                        return back()->withErrors(['source_profile_path' => 'Please Select Proper slot wise file.']);
                    }
                }
                else if($request['granularity_level_id'] == 4){
                    $week_header = ["Week", "Generated units"];

                    if($actualHeaders != $week_header){
                        unlink($filePath);
                        return back()->withErrors(['source_profile_path' => 'Please Select Proper weekly file.']);
                    }
                }
                else if($request['granularity_level_id'] == 5){
                    $hour_header = ["Hour", "Generated units"];

                    if($actualHeaders != $hour_header){
                        unlink($filePath);
                        return back()->withErrors(['source_profile_path' => 'Please Select Proper hourly file.']);
                    }
                }
                $profile->source_profile_path = "documents/source_profile/" .$profile_doc;

            }
            $profile->save();
            if(isset($request['source_profile_path']) && !empty($request['source_profile_path'])){

                $path = public_path().'/documents/source_profile';

                try{
                    if($request['granularity_level_id'] == 1){
                        $existingProfile = AnnualSourceData::where('profile_id', $profile->id)->first();
                        if ($existingProfile) {
                            // Delete the existing record
                            $existingProfile->forceDelete();
                        }
                    }else if($request['granularity_level_id'] == 2){
                        $existingMonthProfile = MonthlySourceData::where('profile_id', $profile->id)->get();
                        if ($existingMonthProfile) {
                            // Delete the existing record
                            MonthlySourceData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }else if($request['granularity_level_id'] == 3){
                        $existingMonthProfile = TODStateSourceData::where('profile_id', $profile->id)->get();
                        if ($existingMonthProfile) {
                            // Delete the existing record
                            TODStateSourceData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }
                    else if($request['granularity_level_id'] == 4) {
                        $check_hours_data = WeeklySourceData::where('profile_id', $profile->id)->get();
                        if(count($check_hours_data) > 0){
                            WeeklySourceData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }
                    else if($request['granularity_level_id'] == 5) {
                        $check_hours_data = HourlySourceData::where('profile_id', $profile->id)->get();
                        if(count($check_hours_data) > 0){
                            HourlySourceData::where('profile_id', $profile->id)->forceDelete();
                        }
                    }

                    Excel::import(new ImportSourceProfile($profile->id, $request['granularity_level_id']), $path."/". $profile_doc);
                }
                catch (\Exception $e) {
                    // dd($e);
                }
            }
            return redirect()->back()->with('success',__('Source Profile updated successfuly.'))->with('showToast', true);
        } catch (\Illuminate\Validation\ValidationException $exception) {

            // Validation failed
            $errors = $exception->validator->errors();
            // dd($errors);
            // Redirect back with errors or handle them as needed
            return redirect()->back()->withErrors($errors)->withInput();
        }catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return redirect()->back()->with('error',__("Something went wrong while creating profile."));
        }

    }

    /**
     * delete source profile
     */
    public function destroy($id)
    {
        SourceProfile::find($id)->delete();
        return redirect()->back();
    }

    /**
     * convert consumption data into granularity
     */
    public function ConvertSource(Request $request)
    {
        try{
            $intervals = [];
            if($request['granularity_level'] == 1)
            {
                $data = AnnualSourceData::where('profile_id', $request['profile_id'])->get();
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
                $intervals = array_slice($intervals, 0, 50);
                return response()->json(['status' => true, 'data' => $intervals]);

            }
            else if($request['granularity_level'] == 2){

                $data = MonthlySourceData::where('profile_id', $request['profile_id'])->where('name', '!=', 'Proportion units lower on sundays and holidays')->select('name', 'consumed_unit')->get();
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


                    // dd($intervals);
                    // exit;
                }
                $intervals = array_slice($intervals, 0, 50);
                // dd($intervals);
                return response()->json(['status' => true, 'data' => $intervals]);

            }
            else if($request['granularity_level'] == 3)
            {
                $data = TODStateSourceData::where('profile_id', $request['profile_id'])->select('slot', 'jan', 'feb', 'mar','apr','may','jun','jul','aug','sep','oct','nov','dec','consumed_unit')->get();
                $checking_working_day = ConsumptionDayShift::where('profile_id', $request['profile_id'])->where('day_start', '!=', 'null')->where('day_end', '!=', 'null')->get();
                $consumption_tod = SourceTod::where('profile_id', $request['profile_id'])->get();

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
                                    'start' =>$start->format('H:i'),
                                    'end' => $chunkEnd->format('H:i'),
                                    'consumption' => round($unitsPerSlot, 3),
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
            else if($request['granularity_level'] == 5)
            {
                $data = HourlySourceData::where('profile_id', $request['profile_id'])->select('hours', 'consumed_unit')->get();
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
                $intervals = array_slice($intervals, 0, 50);
                return response()->json(['status' => true, 'data' => $intervals]);
            }
            else if ($request['granularity_level'] == 4)
            {
                $data = WeeklySourceData::where('profile_id', $request['profile_id'])->select('weeks', 'consumed_unit')->get();
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

                $intervals = array_slice($intervals, 0, 50);
                return response()->json(['status' => true, 'data' => $intervals]);
            }


        }
         catch (\Exception $e) {
            // dd($e);
            return response()->json(['status' => false]);
        }
    }

    /**
     *
     * export file
     */
    public function ExportConvertSource(Request $request)
    {
       
        $data = [
            'granularity_level' => $request['granularity_level'],
            'chunk_time' => $request['chunk_time'],
            'profile_id' => $request['profile_id']
            // Your data to export
        ];
        // dd($request);
        return Excel::download(new ExportSourceProfile($data), 'generation.csv');

    }
    /**
     *
     * update granularity
     */
    public function updateSourceGranularity(Request $request, $id)
    {
        $model = SourceProfile::find($id);
        if ($model) {
            $model->granularity_id = $request->input('granularity_id');
            $model->save();
            return response()->json(['message' => 'Update successful']);
        }
        return response()->json(['message' => 'Model not found'], 404);
    }
}
