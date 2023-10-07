<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Mapping;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\ProjectDetailComponent;
use App\Models\LoanComponent;
use App\Models\AnnualMaintenanceComponent;
use App\Models\InsuranceComponent;
use App\Models\RecoveryComponent;
use App\Models\TransmissionChargesComponent;
use App\Models\WheelingChargesComponent;
use App\Models\BankingComponent;
use App\Models\PeakBankingComponent;
use App\Models\ElectricityComponent;

class ProjectController extends Controller
{
    /**
     *
     * display projects list
     */
    public function index()
    {
        $sessionId = Session::get('client_detail');
        $project_list = Project::with('mapping')->where('client_id',$sessionId->id)->orderBy('created_at', 'desc')->get();
        $client_list = Client::get();
        return view('project.index',  ['client_list' => $client_list, 'current_client' => $sessionId,'project_list' => $project_list]);
    }
    /***
     *
     * create project
     */
    public function create()
    {
        $current_client = Session::get('client_detail');
        $client_list = Client::get();
        $mapping_list = Mapping::all()->where('client_id',$current_client->id);
        return view('project.create',compact('client_list','current_client','mapping_list'));
    }

    /***
     *
     * store project
     */
    public function store(Request $request)
    {
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
        // dd($request);
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'mapping_id' => ['required', 'not_in:0'],
            'project_location' => ['required', 'string', 'max:255'],
            'clipping'=>['required'],
            'clipping_value' => ['required'],
            'total_capacity' =>['required'],
            'percentage_satisfied_value' => ['required'],
            'grid_power' => ['required'],
            'lapsed_unit' => ['required'],
            'green_energy_power' => ['required'],
            'connected_voltage' => ['required'],
            'evacuation_capacity' =>['required'],
            'wind_capex' => ['required'],
            'solar_capex' => ['required'],
            'total_capex' => ['required'],
        ]);
        $project = Project::create([
            'client_id' => $client_id,
            'site_name' => $request['site_name'],
            'project_location' => $request['project_location'],
            'mapping_id' => $request['mapping_id'],
            'clipping' => $request['clipping'],
            'clipping_value' => $request['clipping_value'],
            'total_capacity' => $request['total_capacity'],
            'percentage_satisfied_value' => $request['percentage_satisfied_value'],
            'grid_power' => $request['grid_power'],
            'lapsed_unit' => $request['lapsed_unit'],
            'green_energy_power' => $request['green_energy_power'],
            'connected_voltage' => $request['connected_voltage'],
            'evacuation_capacity' => $request['evacuation_capacity'],
            'wind_capex' => $request['wind_capex'],
            'solar_capex' => $request['solar_capex'],
            'total_capex' => $request['total_capex'],
        ]);
        return redirect()->route('project.edit', ['project' => $project->id,'from_store' => true])->with('showToast', true)->with('success', __('Project added successfully.'));
    }
    /***
     *
     * edit project
     */
    public function edit($id)
    {
        $fromStore = request()->query('from_store', false);
        $client_list = Client::get();
        $current_client = Session::get('client_detail');
        $mapping_list = Mapping::all()->where('client_id',$current_client->id);
        $project = Project::find($id);
        $project_detail = ProjectDetailComponent::where('project_id',$id)->first();
        $loan = LoanComponent::where('project_id',$id)->first();
        $annual_maintenance = AnnualMaintenanceComponent::where('project_id',$id)->first();
        $insurance = InsuranceComponent::where('project_id',$id)->first();
        $recovery = RecoveryComponent::where('project_id',$id)->first();
        $transmission = TransmissionChargesComponent::where('project_id',$id)->first();
        $wheeling_charge = WheelingChargesComponent::where('project_id',$id)->first();
        $banking = BankingComponent::where('project_id',$id)->first();
        $peak_banking = PeakBankingComponent::where('project_id',$id)->first();
        // $electricity = ElectricityComponent::where('project_id',$id)->first();
        // dd($project_detail);
        return view('project.edit',compact('client_list','current_client','fromStore','project','mapping_list','project_detail','loan', 'annual_maintenance','insurance','recovery','transmission', 'wheeling_charge', 'banking', 'peak_banking'));
    }

    /***
     *
     * update project
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'mapping_id' => ['required', 'not_in:0'],
            'project_location' => ['required', 'string', 'max:255'],
            'clipping'=>['required'],
            'clipping_value' => ['required'],
            'total_capacity' =>['required'],
            'percentage_satisfied_value' => ['required'],
            'grid_power' => ['required'],
            'lapsed_unit' => ['required'],
            'green_energy_power' => ['required'],
            'connected_voltage' => ['required'],
            'evacuation_capacity' =>['required'],
            'wind_capex' => ['required'],
            'solar_capex' => ['required'],
            'total_capex' => ['required'],
        ]);
        $project = Project::find($id);

        $project->site_name = $request['site_name'];
        $project->project_location = $request['project_location'];
        $project->mapping_id = $request['mapping_id'];
        $project->clipping = $request['clipping'];
        $project->clipping_value = $request['clipping_value'];
        $project->total_capacity = $request['total_capacity'];
        $project->percentage_satisfied_value = $request['percentage_satisfied_value'];
        $project->grid_power = $request['grid_power'];
        $project->lapsed_unit = $request['lapsed_unit'];
        $project->green_energy_power = $request['green_energy_power'];
        $project->connected_voltage = $request['connected_voltage'];
        $project->evacuation_capacity = $request['evacuation_capacity'];
        $project->wind_capex = $request['wind_capex'];
        $project->solar_capex = $request['solar_capex'];
        $project->total_capex = $request['total_capex'];
        $project->save();
        return redirect()->back()->with('success',__('Project updated successfuly.'))->with('showToast', true);
    }
    /**
     * delete project
     */
    public function destroy($id)
    {
        Project::find($id)->delete();
        return redirect()->back();
    }

    public function ProjectDetailComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
       
        $projectDetail = ProjectDetailComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'no_turbines' => $request['no_turbines'],
            'solar_mwp' => $request['solar_mwp'],
            'dc_ac_ratio' => $request['dc_ac_ratio'],
            'solar_unit_mwp' => $request['solar_unit_mwp'],
            'solar_deration' => $request['solar_deration'],
            'wind_capacity_mws' => $request['wind_capacity_mws'],
            'wind_gen_unit_turbine' => $request['wind_gen_unit_turbine'],
            'total_gen' => $request['total_gen'],
            'wind_capex' => $request['wind_capex'],
            'solar_capex' => $request['solar_capex'],
            'total_capex' => $request['total_capex'],
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Project Detail add successfuly.'))->with('showToast', true);
    }

    public function LoanComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
      
        $projectDetail = LoanComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'gst' => $request['gst'],
            'income_tax' => $request['income_tax'],
            'cash_equity' => $request['cash_equity'],
            'debt' => $request['debt'],
            'total_fund' => $request['total_fund'],
            'rate_of_interest' => $request['rate_of_interest'],
            'repayment_period' => $request['repayment_period'],
            'moratorium' => $request['moratorium'],
            'tax_rate' => $request['tax_rate'],
            'depreciation_rate' => $request['depreciation_rate'],
            'addl_depreciation_rate' => $request['addl_depreciation_rate'],
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Loan component add successfuly.'))->with('showToast', true);
    }

    public function AnnualMaintenanceComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
      
        $maintenance = AnnualMaintenanceComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'solar_maintenance' => $request['solar_maintenance'],
            'soalr_free' => $request['soalr_free'],
            'solar_escalation' => $request['solar_escalation'],
            'wind_maintenance' => $request['wind_maintenance'],
            'wind_free' => $request['wind_free'],
            'wind_escalation' => $request['wind_escalation'],
            'bop_maintenance' => $request['bop_maintenance'],
            'bop_free' => $request['bop_free'],
            'bop_escalation' => $request['bop_escalation']
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Annual maintenance component add successfuly.'))->with('showToast', true);
    }

    public function InsuranceComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
      
        $maintenance = InsuranceComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'insurance' => $request['insurance'],
            'total_capex' => $request['total_capex'],
            
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Insurance component add successfuly.'))->with('showToast', true);
    }

    public function RecoveryComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
      
        $recovery = RecoveryComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'lapsed_unit' => $request['lapsed_unit'],
            'recovery_unit' => $request['recovery_unit'],
            
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Recovery component add successfuly.'))->with('showToast', true);
    }

    public function TransmissionChargesComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
      
        $transmission = TransmissionChargesComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'ctu_charge' => $request['ctu_charge'],
            'ctu_losses' => $request['ctu_losses'],
            'stu_charge' => $request['stu_charge'],
            'stu_losses' => $request['stu_losses']
            
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Transmission Charges component add successfuly.'))->with('showToast', true);
    }

    public function WheelingChargesComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
      
        $wheeling_charge = WheelingChargesComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'wheeling_charge_unit' => $request['wheeling_charge_unit'],
            'discom_charge' => $request['discom_charge'],
            'discom_losses' => $request['discom_losses']
            
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Wheeling Charges component add successfuly.'))->with('showToast', true);
    }

    public function BankingComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
      
        $bank = BankingComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'banking_basis' => $request['banking_basis'],
            'banking_charges' => $request['banking_charges'],
            
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Banking component add successfuly.'))->with('showToast', true);
    }

    public function PeakBankingComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
      
        $bank = PeakBankingComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'peak_banking_basis' => $request['peak_banking_basis'],
            'peak_banking_charges' => $request['peak_banking_charges'],
            
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Peak Banking component add successfuly.'))->with('showToast', true);
    }

    public function ElectricityComponent(Request $request){
        $sessionId = Session::get('client_detail');
        $client_id = $sessionId->id;
      
        $electricity = ElectricityComponent::updateOrCreate(['project_id' => $request['project_id']],[
            'client_id' => $client_id,
            'project_id' => $request['project_id'],
            'net_unit' => $request['net_unit'],
            'electricity_unit' => $request['electricity_unit'],
            
        ]);

        return redirect()->route('project.edit',['project' => $request['project_id']])->with('success',__('Electricity component add successfuly.'))->with('showToast', true);
    }
}
