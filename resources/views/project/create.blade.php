@extends('layouts.app')
@section('content')

<div class="content-body content content-components tracking-page-setup index">
    <div class="pd-x-0">
        <div class="wrap_topbar" style="padding-bottom: 15px;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Add Project </b></span>
            </div>

            <div class="d-sm-flex align-items-center justify-content-end mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <div class="top-right-buttons">
                    <a href="{{route('consumption_profile.create')}}" class="btn btn-success">
                        <img src="{{asset('assets/img/plus.png')}}">
                        <span>Add Project</span>
                    </a>
                </div>
            </div>
      </div>
        <div class="new-site-add-setup consumption-edit-main-part">
            <div class="row">
                <div class="col-md-12 consumption-edit-pro">
                    <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('project.store') }}" method="POST" enctype='multipart/form-data'>
                    @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="site_name" class="control-label">Site Name</label>
                                <input type="text" class="form-control @error('site_name') is-invalid @enderror" id="form-1-1" name="site_name" value="{{ old('site_name') }}" placeholder="Site Name" autocomplete="site_name" autofocus>
                               <!-- @error('site_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="mapping_id" class="control-label">Select Mapping</label>
                                <select name="mapping_id" class="form-control @error('mapping_id') is-invalid @enderror">
                                <option value="0">Mapping</option>
                                    @if(isset($mapping_list) && count($mapping_list) > 0)
                                        @foreach($mapping_list as $item)
                                            <option value="{{$item->id}}" {{ old('mapping_id') == $item->id ? 'selected' : '' }}>{{$item->mapping_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                               <!-- @error('mapping_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="project_location" class="control-label">Project Site/Location</label>
                                <input type="text" class="form-control @error('project_location') is-invalid @enderror" id="form-1-1" name="project_location" value="{{ old('project_location') }}" placeholder="Project site" required autocomplete="project_location" autofocus>
                               <!-- @error('project_location')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <div class="clipping">
                                    <label for="clipping" class="control-label">Clipping losses</label>
                                    <input type="text" class="form-control @error('clipping') is-invalid @enderror" id="form-1-1" name="clipping" value="{{ old('clipping') }}" placeholder="Captive" required autocomplete="clipping" autofocus>
                                    <!-- @error('clipping')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror -->
                                </div>
                                <div class="or">
                                    <label for="clipping_losses_percentage" class="control-label" style="padding-left:18px;">OR </label>
                                    <input type="text" class="form-control @error('clipping_losses_percentage') is-invalid @enderror" id="form-1-1" name="clipping_value" value="{{ old('clipping_losses_percentage') }}" placeholder="0%" required autocomplete="clipping_losses_percentage" autofocus>
                                    <!-- @error('clipping_losses_percentage')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror -->
                                </div>
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="total_capacity" class="control-label">Total Capacity </label>
                                <input type="text" class="form-control @error('total_capacity') is-invalid @enderror" id="form-1-1" name="total_capacity" value="{{ old('total_capacity') }}" placeholder="123456789" required autocomplete="total_capacity" autofocus>
                               <!-- @error('total_capacity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <div class="percentage_satisfied">
                                    <label for="percentage_satisfied" class="control-label">Percentage Satisfied</label>
                                    <input type="text" class="form-control @error('percentage_satisfied') is-invalid @enderror" id="form-1-1" name="percentage_satisfied_value" value="{{ old('percentage_satisfied') }}" placeholder="0%" autocomplete="percentage_satisfied" autofocus>
                                    <!-- @error('percentage_satisfied')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror -->
                                </div>
                                <div class="grid_power_replace">
                                    <label for="grid_power_replace" class="control-label" style="padding-left:18px;">Grid Power Replaced </label>
                                <input type="text" class="form-control @error('grid_power_replace') is-invalid @enderror" id="form-1-1" name="grid_power" value="{{ old('grid_power_replace') }}" placeholder="0%" autocomplete="grid_power_replace" autofocus>
                                <!-- @error('grid_power_replace')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror -->
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="green_energy_power" class="control-label">Highest Replaced by Green Energy power  </label>
                                <input type="text" class="form-control @error('green_energy_power') is-invalid @enderror" id="form-1-1" name="green_energy_power" value="{{ old('total_capacity') }}" placeholder="0%" required autocomplete="total_capacity" autofocus>
                               <!-- @error('green_energy_power')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="green_energy_power" class="control-label">Evacuation Capacity</label>
                                <input type="text" class="form-control @error('evacuation_capacity') is-invalid @enderror" id="form-1-1" name="evacuation_capacity" value="{{ old('evacuation_capacity') }}" placeholder="123456789" required autocomplete="evacuation_capacity" autofocus>
                               <!-- @error('evacuation_capacity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="lapsed_unit" class="control-label">Lapsed Units</label>
                                <input type="text" class="form-control @error('lapsed_unit') is-invalid @enderror" id="form-1-1" name="lapsed_unit" value="{{ old('lapsed_unit') }}" placeholder="0" required autocomplete="lapsed_unit" autofocus>
                               <!-- @error('lapsed_unit')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="connected_voltage" class="control-label">Connected Voltage</label>
                                <input type="text" class="form-control @error('connected_voltage') is-invalid @enderror" id="form-1-1" name="connected_voltage" value="{{ old('connected_voltage') }}" placeholder="0" autocomplete="connected_voltage" autofocus>
                                <!-- @error('connected_voltage')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            <input class="slider" type="range" min="0" value="0" max="100" step="1" style="margin-left:6px;">
                                <!-- @error('grid_power_replace')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="wind_cpaex" class="control-label">Wind CAPEX </label>
                                <input type="text" class="form-control @error('wind_cpaex') is-invalid @enderror" id="form-1-1" name="wind_capex" value="{{ old('wind_cpaex') }}" placeholder="0" required autocomplete="wind_cpaex" autofocus>
                               <!-- @error('wind_cpaex')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="solar_cpaex" class="control-label">Solar CAPEX</label>
                                <input type="text" class="form-control @error('solar_cpaex') is-invalid @enderror" id="form-1-1" name="solar_capex" value="{{ old('solar_cpaex') }}" placeholder="0" autocomplete="solar_cpaex" autofocus>
                                <!-- @error('solar_cpaex')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                                <label for="total_cpaex" class="control-label" style="padding-left:18px;">Total CAPEX </label>
                                <input type="text" class="form-control @error('total_cpaex') is-invalid @enderror" id="form-1-1" name="total_capex" value="{{ old('total_cpaex') }}" placeholder="0" autocomplete="total_cpaex" autofocus>
                                <!-- @error('total_cpaex')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                        </div>
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="toggleable-content">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Price Component </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro price-factor">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="project_details" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" id="project_details" data-target="#projectDetailsTarget" name="price_component">Project Details</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="loan" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#loanTarget" id="loan" name="price_component">Loan (EMI)</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="annual_maintenance" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#annualMaintenanceTarget" id="annual_maintenance" name="price_component">Annual Maintenance</label>                           
                            </div>
                            <!-- <div class="col-sm-3">
                                <label for="insurance" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#insuranceTarget" id="insurance" name="price_component">Insurance</label>                           
                            </div> -->
                        </div>
                        <!-- <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="recovery_lapsed_units" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#recoveryTarget" id="recovery_lapsed_units" name="price_component">Recovery from Lapsed Units</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="transmission_charges" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#transmissionTarget" id="transmission_charges" name="price_component">Transmission Charges</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="wheeling_charges" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#wheelingChargeTarget" id="wheeling_charges" name="price_component">Wheeling Charges</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="banking_charges" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#bankingChargeTarget" id="banking_charges" name="price_component">Banking Charges</label>                           
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="peak_banking_charges" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#peakBankTarget" id="peak_banking_charges" name="price_component">Peak Banking Charges</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="electricity_duty" class="control-label">
                                <input type="checkbox" class="form-control" id="electricity_duty" data-target="#electricityTarget" name="price_component">Electricity Duty</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="asset_management_fees" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#assetTarget" id="asset_management_fees" name="price_component">Asset Management Fees</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="statutory_charges" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle"  data-target="#statutoryTarget" id="statutory_charges" name="price_component">Statutory Charges</label>                           
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="transaction_structure" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#transactionTarget" id="transaction_structure" name="price_component">Transaction structure</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="cross_subsidy" class="control-label">
                                <input type="checkbox" class="form-control" data-target="#crossSubsidyTarget" id="cross_subsidy" name="price_component">Cross Subsidy (* Not Applied on Captive Project)</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="additional_subcharge" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#additionalTarget" id="additional_subcharge" name="price_component">Additional Subcharge</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="transmission_wheeling_losses" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#transmissionWheelingTarget" id="transmission_wheeling_losses" name="price_component">Transmission and Wheeling Losses</label>                           
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="other_charges_1" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#chargeOneTarget" id="other_charges_1" name="price_component">Other charges 1</label>                           
                            </div>
                            <div class="col-sm-3">
                                <label for="cross_subsidy" class="control-label">
                                <input type="checkbox" class="form-control checkbox-toggle" data-target="#chargeTarget" id="other_charges" name="price_component">Other charges 2</label> 
                            </div>
                            <div class="col-sm-3">
                                                          
                            </div>
                            <div class="col-sm-3">
                                                          
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div id="projectDetailsTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Project Details </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('project-detail-component.store') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="no_turbines" class="control-label">No of Turbines</label>
                                <input type="text" class="form-control @error('no_turbines') is-invalid @enderror" id="no_turbines" name="no_turbines" value="{{ old('no_turbines') }}" >
                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="solar_mwp" class="control-label">Solar Mwp</label>
                                <input type="text" class="form-control @error('solar_mwp') is-invalid @enderror" id="solar_mwp" name="solar_mwp" value="{{ old('solar_mwp') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="dc_ac_ratio" class="control-label">DC to AC ratio</label>
                                <input type="text" class="form-control @error('dc_ac_ratio') is-invalid @enderror" id="dc_ac_ratio" name="dc_ac_ratio" value="{{ old('dc_ac_ratio') }}" >
                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="solar_unit_mwp" class="control-label">Solar gen in units per MWp</label>
                                <input type="text" class="form-control @error('solar_unit_mwp') is-invalid @enderror" id="solar_unit_mwp" name="solar_unit_mwp" value="{{ old('solar_unit_mwp') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="solar_deration" class="control-label">Solar Deration/Degradation</label>
                                <input type="text" class="form-control @error('solar_deration') is-invalid @enderror" id="solar_deration" name="solar_deration" value="{{ old('solar_deration') }}" >
                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="wind_capacity_mws" class="control-label">Wind turbine capacity in MWs</label>
                                <input type="text" class="form-control @error('wind_capacity_mws') is-invalid @enderror" id="wind_capacity_mws" name="wind_capacity_mws" value="{{ old('wind_capacity_mws') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="wind_gen_unit_turbine" class="control-label">Wind gen in units per turbine</label>
                                <input type="text" class="form-control @error('wind_gen_unit_turbine') is-invalid @enderror" id="wind_gen_unit_turbine" name="wind_gen_unit_turbine" value="{{ old('wind_gen_unit_turbine') }}" >
                               
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="total_gen" class="control-label">Total Gen</label>
                                <input type="text" class="form-control @error('total_gen') is-invalid @enderror" id="total_gen" name="total_gen" value="{{ old('total_gen') }}" >
                               
                            </div>
                            
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="solar_capex" class="control-label">Solar Capex per MWp</label>
                                <input type="text" class="form-control @error('solar_capex') is-invalid @enderror" id="solar_capex" name="solar_capex" value="{{ old('solar_capex') }}" >
                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="wind_capex" class="control-label">Wind Capex per MW</label>
                                <input type="text" class="form-control @error('wind_capex') is-invalid @enderror" id="wind_capex" name="wind_capex" value="{{ old('wind_capex') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="total_capex" class="control-label">Total Capex</label>
                                <input type="text" class="form-control @error('total_capex') is-invalid @enderror" id="total_capex" name="total_capex" value="{{ old('total_capex') }}" >
                               
                            </div>                            
                        </div>
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="loanTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Loan (EMI) </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('loan-component.store') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="gst" class="control-label">Equity </label>
                                <input type="text" class="form-control @error('gst') is-invalid @enderror" id="gst" name="gst" value="{{ old('gst') }}" placeholder="GST" >
                               
                            </div>
                            <div class="col-sm-3 ">
                                <input type="text" class="form-control @error('income_tax') is-invalid @enderror" id="income_tax" name="income_tax" placeholder="Income Tax" value="{{ old('income_tax') }}" >
                                
                            </div>
                            <div class="col-sm-3 ">
                                <input type="text" class="form-control @error('cash_equity') is-invalid @enderror" id="cash_equity" name="cash_equity" placeholder="Cash Equity" value="{{ old('cash_equity') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="debt" class="control-label">Debt</label>
                                <input type="text" class="form-control @error('debt') is-invalid @enderror" id="debt" name="debt" value="{{ old('debt') }}" >
                               
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="total_fund" class="control-label">Total Source Fund</label>
                                <input type="text" class="form-control @error('total_fund') is-invalid @enderror" id="total_fund" name="total_fund" value="{{ old('total_fund') }}" >
                               
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="rate_of_interest" class="control-label">Rate of Interest</label>
                                <input type="text" class="form-control @error('rate_of_interest') is-invalid @enderror" id="rate_of_interest" name="rate_of_interest" value="{{ old('rate_of_interest') }}" >                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="repayment_period" class="control-label">Repayment Period</label>
                                <input type="text" class="form-control @error('repayment_period') is-invalid @enderror" id="repayment_period" name="repayment_period" value="{{ old('repayment_period') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="moratorium" class="control-label">Moratorium</label>
                                <input type="text" class="form-control @error('moratorium') is-invalid @enderror" id="moratorium" name="moratorium" value="{{ old('moratorium') }}" >                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="tax_rate" class="control-label">Tax Rate</label>
                                <input type="text" class="form-control @error('tax_rate') is-invalid @enderror" id="tax_rate" name="tax_rate" value="{{ old('tax_rate') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="depreciation_rate" class="control-label">Depreciation Rate</label>
                                <input type="text" class="form-control @error('depreciation_rate') is-invalid @enderror" id="depreciation_rate" name="depreciation_rate" value="{{ old('depreciation_rate') }}" >
                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="addl_depreciation_rate" class="control-label">Addl. Depreciation Rate</label>
                                <input type="text" class="form-control @error('addl_depreciation_rate') is-invalid @enderror" id="addl_depreciation_rate" name="addl_depreciation_rate" value="{{ old('addl_depreciation_rate') }}" >
                            </div>
                        </div>
                        
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="annualMaintenanceTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Annual Maintenance </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('annual-maintenance-component') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="solar_maintenance" class="control-label">Solar Annual Maintenance per MWp </label>
                                <input type="text" class="form-control @error('solar_maintenance') is-invalid @enderror" id="solar_maintenance" name="solar_maintenance" value="{{ old('solar_maintenance') }}" >
                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="soalr_free" class="control-label">Solar Free O&M</label>
                                <input type="text" class="form-control @error('soalr_free') is-invalid @enderror" id="soalr_free" name="soalr_free" value="{{ old('soalr_free') }}" >
                               
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="solar_escalation" class="control-label">Solar O&M Escalation</label>
                                <input type="text" class="form-control @error('solar_escalation') is-invalid @enderror" id="solar_escalation" name="solar_escalation" value="{{ old('solar_escalation') }}" >
                               
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="wind_maintenance" class="control-label">Wind Annual Maintenance per MWp</label>
                                <input type="text" class="form-control @error('wind_maintenance') is-invalid @enderror" id="wind_maintenance" name="wind_maintenance" value="{{ old('wind_maintenance') }}" >                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="wind_free" class="control-label">Wind Free O&M</label>
                                <input type="text" class="form-control @error('wind_free') is-invalid @enderror" id="wind_free" name="wind_free" value="{{ old('wind_free') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="wind_escalation" class="control-label">Wind O&M Escalation</label>
                                <input type="text" class="form-control @error('wind_escalation') is-invalid @enderror" id="wind_escalation" name="wind_escalation" value="{{ old('wind_escalation') }}" >                               
                            </div>
                            
                        </div>
                        <div class="form-group row">                            
                            <div class="col-sm-6">
                                <label for="bop_maintenance" class="control-label">BOP Annual Maintenance per MWp</label>
                                <input type="text" class="form-control @error('bop_maintenance') is-invalid @enderror" id="bop_maintenance" name="bop_maintenance" value="{{ old('bop_maintenance') }}" >
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                            <label for="bop_free" class="control-label">BOP Free O&M</label>
                                <input type="text" class="form-control @error('bop_free') is-invalid @enderror" id="bop_free" name="bop_free" value="{{ old('bop_free') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                           
                            <div class="col-sm-6">
                                <label for="bop_escalation" class="control-label">BOP O&M Escalation</label>
                                <input type="text" class="form-control @error('bop_escalation') is-invalid @enderror" id="bop_escalation" name="bop_escalation" value="{{ old('bop_escalation') }}" >
                            </div>
                        </div>
                        
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="insuranceTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Insurance </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('insurance-component') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="insurance" class="control-label">Insurance (% of Asset) </label>
                                <input type="text" class="form-control @error('insurance') is-invalid @enderror" id="insurance" name="insurance" value="{{ old('insurance') }}" >                                
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="total_capex" class="control-label">Total Capex</label>
                                <input type="text" class="form-control @error('total_capex') is-invalid @enderror" id="total_capex" name="total_capex" value="{{ old('total_capex') }}" >
                               
                            </div>

                        </div>
                       
                       
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="recoveryTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Recovery from Lapsed Units </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('recovery-component') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="lapsed_unit" class="control-label">Lapsed units in % of generation </label>
                                <input type="text" class="form-control @error('lapsed_unit') is-invalid @enderror" id="lapsed_unit" name="lapsed_unit" value="{{ old('lapsed_unit') }}" >                                
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="recovery_unit" class="control-label">Recovery in Rs / units</label>
                                <input type="text" class="form-control @error('recovery_unit') is-invalid @enderror" id="recovery_unit" name="recovery_unit" value="{{ old('recovery_unit') }}" >
                               
                            </div>

                        </div>
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit"  disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="transmissionTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Transmission Charges </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('transmission-charges-component') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="ctu_charge" class="control-label">CTU Transmission charges </label>
                                <input type="text" class="form-control @error('ctu_charge') is-invalid @enderror" id="ctu_charge" name="ctu_charge" value="{{ old('ctu_charge') }}" >
                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="ctu_losses" class="control-label">CTU Transmission losses</label>
                                <input type="text" class="form-control @error('ctu_losses') is-invalid @enderror" id="ctu_losses" name="ctu_losses" value="{{ old('ctu_losses') }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="stu_charge" class="control-label">STU Transmission charges</label>
                                <input type="text" class="form-control @error('stu_charge') is-invalid @enderror" id="stu_charge" name="stu_charge" value="{{ old('stu_charge') }}" >
                               
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="stu_losses" class="control-label">STU transmission losses</label>
                                <input type="text" class="form-control @error('stu_losses') is-invalid @enderror" id="stu_losses" name="stu_losses" value="{{ old('stu_losses') }}" >
                            </div>
                        </div>
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="wheelingChargeTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Wheeling Charges </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('wheeling-charge-component') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="wheeling_charge_unit" class="control-label">Wheeling charges applicable on per MW or per unit</label>
                                <input type="text" class="form-control @error('wheeling_charge_unit') is-invalid @enderror" id="wheeling_charge_unit" name="wheeling_charge_unit" value="{{ old('wheeling_charge_unit') }}" >                                
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="discom_charge" class="control-label">DISCOM wheeling charges</label>
                                <input type="text" class="form-control @error('discom_charge') is-invalid @enderror" id="discom_charge" name="discom_charge" value="{{ old('discom_charge') }}" >
                               
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="discom_losses" class="control-label">Discom wheeling losses</label>
                                <input type="text" class="form-control @error('discom_losses') is-invalid @enderror" id="discom_losses" name="discom_losses" value="{{ old('discom_losses') }}" >
                               
                            </div>

                        </div>
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="bankingChargeTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Banking Charges </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('banking-component') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="banking_basis" class="control-label">Banking basis</label>
                                <input type="text" class="form-control @error('banking_basis') is-invalid @enderror" id="banking_basis" name="banking_basis" value="{{ old('banking_basis') }}" >
                                
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="banking_charges" class="control-label">Banking Charges</label>
                                <input type="text" class="form-control @error('banking_charges') is-invalid @enderror" id="banking_charges" name="banking_charges" value="{{ old('banking_charges') }}" >
                               
                            </div>

                        </div>
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="peakBankTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Peak Banking Charges </b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('peak-banking-component') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="peak_banking_basis" class="control-label">Peak Banking basis</label>
                                <input type="text" class="form-control @error('peak_banking_basis') is-invalid @enderror" id="peak_banking_basis" name="peak_banking_basis" value="{{ old('peak_banking_basis') }}" >
                                
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="peak_banking_charges" class="control-label">Peak Banking Charges</label>
                                <input type="text" class="form-control @error('peak_banking_charges') is-invalid @enderror" id="peak_banking_charges" name="peak_banking_charges" value="{{ old('peak_banking_charges') }}" >
                               
                            </div>

                        </div>
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div id="electricityTarget" class="toggleable-content" style="display: none;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Electricity Duty</b></span>
            </div>
            <div class="new-site-add-setup consumption-edit-main-part">
                <div class="row">
                    <div class="col-md-12 consumption-edit-pro">
                        <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('electricity-component') }}" method="POST" enctype='multipart/form-data'>
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="net_unit" class="control-label">Net Settled Units - Metadata</label>
                                <input type="text" class="form-control @error('net_unit') is-invalid @enderror" id="net_unit" name="net_unit" value="{{ old('net_unit') }}" >
                                
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="electricity_unit" class="control-label">Electricity Duty (per unit) - User Input</label>
                                <input type="text" class="form-control @error('electricity_unit') is-invalid @enderror" id="electricity_unit" name="electricity_unit" value="{{ old('electricity_unit') }}" >
                               
                            </div>

                        </div>
                        <div class="col-md-12 component-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="submit" disabled class="form-control save-btn-project" id="form-1-1"
                                    value="Save Site Details">
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="toggleable-content">
            <div class="wrap_topbar" style="padding-bottom: 15px;">
                <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                    <span class="heading-main"><b>Type Of Energy we want to setup </b></span>
                </div>

                <div class="d-sm-flex align-items-center justify-content-end mg-b-20 mg-lg-b-25 mg-xl-b-30">
                    <div class="top-right-buttons">
                        <a href="#"  onclick="openPopup()" class="btn btn-success">
                            <img src="{{asset('assets/img/plus.png')}}">
                            <span>Create Scenario</span>
                        </a>
                        <a href="#" class="btn btn-success">
                            <img src="{{asset('assets/img/plus.png')}}">
                            <span>Compare Site Scenario</span>
                        </a>
                    </div>
                </div>
          </div>
        </div>
    </div>
</div>
<script src="{{url('lib/jquery/jquery.min.js')}}"></script>
<script src="{{url('lib/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{url('lib/feather-icons/feather.min.js')}}"></script>
<script src="{{url('lib/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{url('lib/prismjs/prism.js')}}"></script>
<script src="{{url('lib/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{url('lib/datatables.net-dt/js/dataTables.dataTables.min.js')}}"></script>
<script src="{{url('lib/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{url('lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js')}}"></script>
<script src="{{url('lib/jquery.flot/jquery.flot.js')}}"></script>
<script src="{{url('lib/jquery.flot/jquery.flot.stack.js')}}"></script>
<script src="{{url('lib/jquery.flot/jquery.flot.resize.js')}}"></script>
<script src="{{url('lib/chart.js/Chart.bundle.min.js')}}"></script>
<script src="{{url('lib/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{url('lib/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<script src="{{url('assets/js/dashforge.js')}}"></script>
<script src="{{url('assets/js/dashforge.aside.js')}}"></script>
<script src="{{url('assets/js/dashforge.sampledata.js')}}"></script>
<script src="{{url('assets/js/dashboard-one.js')}}"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script> -->
<script>
    function openPopup() {
      // Create popup container
      var popup = document.createElement('div');
      popup.classList.add('popup-container');

      // Create title
      var title = document.createElement('h1');
      title.textContent = 'Create Scenario';

      // Create close icon
      var closeIcon = document.createElement('span');
      closeIcon.classList.add('close-icon');
      closeIcon.innerHTML = '&times;';

      // Create form field for scenario name
      var nameField = document.createElement('div');
      nameField.classList.add('form-field');

      var nameLabel = document.createElement('label');
      nameLabel.classList.add('form-label');
      nameLabel.textContent = 'Please Specify a name for the new Scenario';

      var nameInput = document.createElement('input');
      nameInput.classList.add('form-input');
      nameInput.type = 'text';
      nameInput.placeholder = 'Enter scenario name';

      // Create form field for scenario type dropdown
      var typeField = document.createElement('div');
      typeField.classList.add('form-field');





      // Create buttons container
      var buttonsContainer = document.createElement('div');
      buttonsContainer.classList.add('buttons-container');

      // Create submit button
      var submitButton = document.createElement('button');
      submitButton.innerHTML = 'Submit';

      // Create close button
      var closeButton = document.createElement('button');
      closeButton.innerHTML = 'Close';

      // Append title, close icon, form fields, and buttons to popup container
      nameField.appendChild(nameLabel);
      nameField.appendChild(nameInput);



      buttonsContainer.appendChild(submitButton);
      buttonsContainer.appendChild(closeButton);

      popup.appendChild(title);
      popup.appendChild(closeIcon);
      popup.appendChild(nameField);
      popup.appendChild(typeField);
      popup.appendChild(buttonsContainer);

      // Append popup container to body
      document.body.appendChild(popup);

      // Close popup when close icon is clicked
      closeIcon.addEventListener('click', function() {
        document.body.removeChild(popup);
      });

      // Close popup when close button is clicked
      closeButton.addEventListener('click', function() {
        document.body.removeChild(popup);
      });

      // Submit button event listener
      submitButton.addEventListener('click', function() {
        // Retrieve the entered scenario name
        var scenarioName = nameInput.value;
        console.log('New scenario name:', scenarioName);

        // Retrieve the selected scenario type
        var selectedType = typeDropdown.value;
        console.log('Selected scenario type:', selectedType);

        // Do something with the scenario name and type...

        document.body.removeChild(popup);
      });
    }
  </script>
  <script>
    $(document).ready(function () {
        // When any checkbox with the class "checkbox-toggle" is clicked
        $(".checkbox-toggle").change(function () {
            const target = $($(this).data("target")); // Get the target content div

            if ($(this).is(":checked")) {
                // Checkbox is checked, show the target content
                target.show();
            } else {
                // Checkbox is unchecked, hide the target content
                target.hide();
            }
        });
    });
</script>
@endsection
