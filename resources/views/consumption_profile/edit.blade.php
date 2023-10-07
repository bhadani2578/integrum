@extends('layouts.app')

@section('content')

<div class="content-body content content-components tracking-page-setup index">
    <div class="pd-x-0">
        <div class="wrap_topbar">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span
                    class="heading-main"><b>{{ $fromStore ? 'Add Consumption Profile' : 'Edit Consumption Profile' }}</b></span>
            </div>
        @if($fromStore == true)
        <div class="d-sm-flex align-items-center justify-content-end mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div class="top-right-buttons">
                <a href="{{route('consumption_profile.create')}}" class="btn btn-success">
                    <img src="{{asset('assets/img/plus.png')}}">
                    <span>Add Consumption Profile</span>
                </a>
            </div>
        </div>
        @endif
        </div>
        <div class="new-site-add-setup consumption-edit-main-part" style="margin-top:20px;">
            <div class="row">
                <div class="col-md-12 consumption-edit-pro">
                    <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('consumption_profile.update', $profile_details->id) }}" method="POST" enctype='multipart/form-data'>
                    @csrf
                    @method('PUT')
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="point_name" class="control-label">Consumption Point Name</label>
                                <input type="text" class="form-control @error('point_name') is-invalid @enderror" id="form-1-1" name="point_name" value="{{ old('point_name', $profile_details->point_name) }}" required autocomplete="point_name" autofocus {{ $fromStore ? 'disabled' : '' }}>
                                <!-- @error('point_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="state_id" class="control-label">State of Consumption Point</label>
                                <select name="state_id" id="state" class="form-control @error('state_id') is-invalid @enderror" {{ $fromStore ? 'disabled' : '' }}>
                                    <option value="0">States</option>
                                    @if(isset($state_list) && count($state_list) > 0)
                                        @foreach($state_list as $item)
                                        <option {{ old('state_id', $profile_details->state_id) == $item->id ? 'selected' : '' }} value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                               <!-- @error('state_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="voltage_id" class="control-label">Voltage connectivity</label>
                                <select name="voltage_id" id="status" class="form-control @error('voltage_id') is-invalid @enderror" {{ $fromStore ? 'disabled' : '' }}>
                                    <option value="0">Voltage(KV)</option>
                                    @if(isset($voltage_list) && count($voltage_list) > 0)
                                    @foreach($voltage_list as $item)
                                    <option {{ old('voltage_id', $profile_details->voltage_id) == $item->id ? 'selected' : '' }} value="{{$item->id}}">{{$item->kg}}(KV)</option>
                                    @endforeach
                                    @endif
                                </select>
                               <!-- @error('voltage_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="discom_id" class="control-label">DISCOM of consumption point</label>
                                <select name="discom_id" id="discom_id" class="form-control @error('discom_id') is-invalid @enderror" {{ $fromStore ? 'disabled' : '' }}>
                                    <option value="0">Select Discom</option>
                                    @if(isset($discom_list) && count($discom_list) > 0)
                                    @foreach($discom_list as $item)
                                    <option {{ old('discom_id', $profile_details->discom_id) == $item->id ? 'selected' : '' }} value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                              <!-- @error('discom_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                            <div class="col-sm-6">
                                <label for="wheeling_charge" class="control-label">Wheeling Charges</label>
                                <input type="text" class="form-control @error('wheeling_charge') is-invalid @enderror" id="wheeling_charge" name="wheeling_charge" value="{{ old('wheeling_charge' , $profile_details->wheeling_charge) }}" {{ $fromStore ? 'disabled' : '' }}>
                              <!-- @error('wheeling_charge')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="discom_category_id" class="control-label">DISCOM Tariff category</label>
                                <select name="discom_category_id" class="form-control @error('discom_category_id') is-invalid @enderror" {{ $fromStore ? 'disabled' : '' }}>
                                <option value="0">Tariff Category</option>
                                    @if(isset($tariff_category) && count($tariff_category) > 0)
                                        @foreach($tariff_category as $item)
                                        <option {{ old('discom_category_id', $profile_details->discom_category_id) == $item->id ? 'selected' : '' }} value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                 <!-- @error('discom_category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="contract_demand" class="control-label">Contract Demand</label>
                                <input type="number" class="form-control @error('contract_demand') is-invalid @enderror" id="form-1-1" name="contract_demand" value="{{ old('contract_demand', $profile_details->contract_demand) }}" {{ $fromStore ? 'disabled' : '' }}>
                                 <!-- @error('contract_demand')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                            <div class="col-sm-2">
                                <select name="contract_unit" class="form-control @error('contract_unit') is-invalid @enderror" {{ $fromStore ? 'disabled' : '' }}>
                                    @if(isset($units) && count($units) > 0)
                                        @foreach($units as $key => $item)
                                        <option {{ old('contract_unit', $profile_details->contract_unit) == $key ? 'selected' : '' }} value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                 <!-- @error('contract_unit')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="contract_demand_limitation" class="control-label">Contract Demand Limitation</label>
                                <input type="number" class="form-control @error('contract_demand_limitation') is-invalid @enderror" id="form-1-1" name="contract_demand_limitation" value="{{ old('contract_demand_limitation',$profile_details->contract_demand_limitation) }}" {{ $fromStore ? 'disabled' : '' }}>
                                <!-- @error('contract_demand_limitation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row radio-btn-part">
                            <div class="col-sm-12">
                                <label for="form1-1" >ED Waiver/rebate</label>
                                <label for="form-11" style="width: auto;display: inline-block;margin-right: 10px;" ><input {{ $fromStore ? 'disabled' : '' }}  type="radio" class=" @error('ed_type') is-invalid @enderror" id="form-1-1" name="ed_type" value="1" id="waived_field" @if(isset($profile_details->ed_detail) && ($profile_details->ed_detail->ed_type == 1)) checked @endif >
                                Waived</label>
                                <label for="form1-1-1" style=" width: auto;display: inline-block;"><input {{ $fromStore ? 'disabled' : '' }} type="radio" class="@error('ed_type') is-invalid @enderror" id="form-1-1" name="ed_type" id="rebate_field" value="2" @if(isset($profile_details->ed_detail) && ($profile_details->ed_detail->ed_type == 2)) checked @endif >
                                Rebate</label>
                                <label for="form22-1-1" style=" width: auto;display: inline-block;"><input {{ $fromStore ? 'disabled' : '' }} type="radio" class="@error('ed_type') is-invalid @enderror" id="form-1-1" name="ed_type" id="no_rebate_field" value="3" @if(isset($profile_details->ed_detail) && ($profile_details->ed_detail->ed_type == 3)) checked @endif >
                                No rebate</label>
                            </div>
                        </div>
                        <div class="form-group" id="waived_field">
                            <div class="form-group row">
                                <div class="col-sm-12 waiver_format">
                                    <div class="col-sm-6 label-part-wave"><label for="form-1-1" >ED waiver time frame</label></div>
                                    <div class="col-sm-6 time_waiver_wrap">
                                        <div class="time-radio">
                                            <label for="availale" style="width: auto;display: inline-block;margin-right: 10px;" ><input {{ $fromStore ? 'disabled' : '' }} type="radio" class=" @error('waiver_time') is-invalid @enderror"  name="waiver_time" value="1" id="available_upto" @if(isset($profile_details->ed_detail) && ($profile_details->ed_detail->waiver_time == 1)) checked @endif >
                                            Available upto</label>
                                            <label for="month" style=" width: auto;display: inline-block;"><input {{ $fromStore ? 'disabled' : '' }} type="radio" class="@error('waiver_time') is-invalid @enderror"  name="waiver_time" id="month_waiver" value="2" @if(isset($profile_details->ed_detail) && ($profile_details->ed_detail->waiver_time == 2)) checked @endif >
                                            Month</label>
                                            <label for="year" style=" width: auto;display: inline-block;"><input {{ $fromStore ? 'disabled' : '' }} type="radio" class="@error('waiver_time') is-invalid @enderror"  name="waiver_time" id="year_waiver" value="3" @if(isset($profile_details->ed_detail) && ($profile_details->ed_detail->waiver_time == 3)) checked @endif >
                                            Year</label>
                                        </div>
                                        <div class="available-section">
                                            <div class="form-group row" id="available_upto_value">
                                                <div class="col-sm-12">
                                                    <label for="date" class="control-label">Available Upto</label>
                                                    <input type="date" {{ $fromStore ? 'disabled' : '' }} class="form-control @error('available_upto') is-invalid @enderror" id="form-1-1" name="available_upto" value="{{ $profile_details->ed_detail ? $profile_details->ed_detail->available_upto  : ''}}">
                                                    <!-- @error('available_upto')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror -->
                                                </div>
                                            </div>
                                            <div class="form-group row" id="month_waiver_value">
                                                <div class="col-sm-12">
                                                    <label for="waiver_month" class="control-label">Months</label>
                                                    <input {{ $fromStore ? 'disabled' : '' }} type="number" class="form-control @error('waiver_month') is-invalid @enderror" id="form-1-1" name="waiver_month" value="{{ isset($profile_details->ed_detail) ? $profile_details->ed_detail->waiver_month : '' }}">
                                                    @error('waiver_month')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row" id="year_waiver_value">
                                                <div class="col-sm-12">
                                                    <label for="waiver_month" class="control-label">Years</label>
                                                    <input {{ $fromStore ? 'disabled' : '' }} type="number" class="form-control @error('waiver_year') is-invalid @enderror" id="form-1-1" name="waiver_year" value="{{ isset($profile_details->ed_detail) ? $profile_details->ed_detail->waiver_year : '' }}">
                                                    @error('waiver_month')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id="rebate_field">
                            <div class="col-sm-12 row rebate_section">
                                <div class="rebate_lable">
                                    <label for="form-1-1" class="control-label">ED rebate Type</label>
                                </div>
                                <div class="rebate_wrap">
                                    <div class="col-sm-6">
                                        <label for="rs" style="width: auto;display: inline-block;margin-right: 10px;" ><input {{ $fromStore ? 'disabled' : '' }} type="radio" class=" @error('rebate_type') is-invalid @enderror" id="form-1-1" name="rebate_type" value="1" @if(isset($profile_details->ed_detail) && ($profile_details->ed_detail->rebate_type == 1)) checked @endif >
                                        Rs Per Unit</label>
                                        <label for="percentage" style=" width: auto;display: inline-block;"><input {{ $fromStore ? 'disabled' : '' }} type="radio" class="@error('rebate_type') is-invalid @enderror" id="form-1-1" name="rebate_type"  value="2" @if(isset($profile_details->ed_detail) && ($profile_details->ed_detail->rebate_type == 2)) checked @endif >
                                        Percentage</label>
                                    </div>
                                    <div class="rebate_value">
                                        <div class="col-sm-3 ">
                                            <label for="rebate_value" class="control-label">ED rebate Value</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" {{ $fromStore ? 'disabled' : '' }} class="form-control @error('rebate_value') is-invalid @enderror" id="form-1-1" name="rebate_value" value="{{ isset($profile_details->ed_detail) ? $profile_details->ed_detail->rebate_value : '' }}">
                                            @error('rebate_value')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="Categories" class="control-label">Category of consumption</label>
                                <select {{ $fromStore ? 'disabled' : '' }} name="category_consumption_id" id="status" class="form-control @error('category_consumption_id') is-invalid @enderror">
                                    <option value="0">Categories</option>
                                    @if(isset($industry) && count($industry) > 0)
                                    @foreach($industry as $industries)
                                      <option {{ old('category_consumption_id', $profile_details->category_consumption_id) == $industries->id ? 'selected' : '' }} value="{{$industries->id}}">{{$industries->label}}</option>
                                    @endforeach
                                    @endif
                                </select>
                              <!-- @error('category_consumption_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="type_of_industry" class="control-label">Uploaded Granularity Level</label>
                                <select {{ $fromStore ? 'disabled' : '' }} name="granularity_level_id" id="granularity_level_id" class="@error('granularity_level_id') is-invalid @enderror">


                                    <option value="1" {{ old('granularity_level_id', $profile_details->granularity_level_id) == 1 ? 'selected' : '' }}>Annual</option>
                                <option value="2" {{ old('granularity_level_id', $profile_details->granularity_level_id) == 2 ? 'selected' : '' }}>Monthly</option>
                                <option value="3" {{ old('granularity_level_id', $profile_details->granularity_level_id) == 3 ? 'selected' : '' }}>TOD</option>
                                <option value="4" {{ old('granularity_level_id', $profile_details->granularity_level_id) == 4 ? 'selected' : '' }}>Hourly</option>
                                <option value="5" {{ old('granularity_level_id', $profile_details->granularity_level_id) == 5 ? 'selected' : '' }}>Weekly</option>

                                </select>
                              <!-- @error('granularity_level_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 upload-file">
                                <label for="custom" class="control-label">Upload consumption data at available granularity</label>
                                <div class="custom-file">
                                    <input {{ $fromStore ? 'disabled' : '' }} type="file" class="custom-file-input  @error('consumption_file_path') is-invalid @enderror" id="customFile" name="consumption_file_path" value="{{ old('consumption_file_path')  }}">
                                    @if(isset($profile_details->consumption_file_path))
                                    <a href="{{url($profile_details->consumption_file_path)}}" target="_blank">Download uploaded data sheet</a>
                                    @endif
                                    @error('consumption_file_path')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div id="tod-hide-show-working">
                        <div class="col-md-12 working-part">
                            <div class="form-group row">
                                <div class="col-sm-9">
                                    <div class="col-sm-6">
                                        <label for="day_list" class="control-label">Working Days</label>
                                        <select {{ $fromStore ? 'disabled' : '' }} name="day_start" id="day-mon" class="">
                                            <option value="">Days</option>
                                            @if(isset($day_list) && count($day_list) > 0)
                                                @foreach($day_list as $item)
                                                    <option @if(isset($profile_details->day_shift) && ($profile_details->day_shift->day_start == $item->id)) selected @endif value="{{$item->id}}">{{$item->day}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span>To</span>
                                        <select {{ $fromStore ? 'disabled' : '' }} name="day_end" id="day-fri" class="">
                                            <option value="">Days</option>
                                            @if(isset($day_list) && count($day_list) > 0)
                                                @foreach($day_list as $item)
                                                    <option @if(isset($profile_details->day_shift) && ($profile_details->day_shift->day_end == $item->id)) selected @endif value="{{$item->id}}">{{$item->day}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="hours" class="control-label">Shift Duration</label>
                                        <select {{ $fromStore ? 'disabled' : '' }} name="shift_start" id="time" class="">
                                            @if(isset($hours) && count($hours) > 0)
                                                @foreach($hours as $key => $item)
                                                    <option @if(isset($profile_details->day_shift) && ($profile_details->day_shift->shift_start == $key)) selected @endif value="{{$key}}">{{$item}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span>To</span>
                                        <select {{ $fromStore ? 'disabled' : '' }} name="shift_end" id="time" class="">
                                            @if(isset($hours) && count($hours) > 0)
                                                @foreach($hours as $key => $item)
                                                    <option @if(isset($profile_details->day_shift) && ($profile_details->day_shift->shift_end == $key)) selected @endif value="{{$key}}">{{$item}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <input type="submit" {{ $fromStore ? 'disabled' : '' }} class="form-control {{ $fromStore ? 'save-btn' : 'save-btn-pro' }}" id="form-1-1" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
             </div>
        </div>
        <div id="tod-hide-show">
        <div class="col-md-12 working-part">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main" style="padding-top:16px;"><b> TOD </b></span>
            </div>
            {{-- <form action="{{ route('consumption_profile.tod', $profile_details->id) }}" method="POST">
            @csrf --}}
                <div class="form-group row">
                    <div class="col-sm-9">
                        <div class="col-sm-12 time-slot-col">
                        <!-- @if(isset($profile_details->tod_value) && count($profile_details->tod_value) > 0)
                            @foreach($profile_details->tod_value as $i => $value)
                                <div class="time-slot">
                                    <div class="time-label">
                                        <label for="form-1-1" class="control-label">Time Slot {{$value->tod_slot_id}}</label>
                                        <input type="hidden" name="tod[{{$i+1}}][slot_id]" value="{{$value->tod_slot_id}}" />
                                    </div>
                                    <div class="timining-part">
                                        <select name="tod[{{$i+1}}][tod_start]" id="time" class="">
                                        @if(isset($hours) && count($hours) > 0)
                                            @foreach($hours as $key => $item)
                                                <option @if(isset($value->tod_start) && $value->tod_start == $key) selected @endif value="{{$key}}">{{$item}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                        <span>To</span>
                                        <select name="tod[{{$i+1}}][tod_end]" id="time" class="">
                                        @if(isset($hours) && count($hours) > 0)
                                            @foreach($hours as $key => $item)
                                                <option @if(isset($value->tod_end) && $value->tod_end == $key) selected @endif value="{{$key}}">{{$item}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                    <input class="slider" type="range" min="0" max="100" value="{{(isset($value->tod_start))? $value->tod_value : 0 }}" step="1">
                                    <div class="time_value">
                                        <input type="number" class="form-field slider-value" name="tod[{{$i+1}}][tod_percentage]" value="{{(isset($value->tod_start))? $value->tod_value : 0 }}" /><p>%</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif -->
                        @if(isset($slot_data) && count($slot_data) > 0)
                            @foreach($slot_data as $key => $item)
                            <div class="time-slot">
                                <div class="time-label">
                                    <label for="form-1-1" class="control-label">Time Slot {{$key + 1}}</label>
                                    <input type="hidden" name="tod[{{$key + 1}}][slot_id]" value="{{$key + 1}}" />
                                </div>
                                <div class="timining-part">
                                    <select name="tod[{{$key + 1}}][tod_start]" id="time" class="">
                                    @if(isset($hours) && count($hours) > 0)
                                        @foreach($hours as $keys => $value)
                                            <option @if($item['start_slot'] == $value) selected @endif value="{{$keys}}">{{$value}}</option>
                                        @endforeach
                                    @endif
                                    </select>
                                    <span>To</span>
                                    <select name="tod[{{$key + 1}}][tod_end]" id="time" class="">
                                    @if(isset($hours) && count($hours) > 0)
                                        @foreach($hours as $keys => $value)
                                            <option @if($item['end_slot'] == $value) selected @endif value="{{$keys}}">{{$value}}</option>
                                        @endforeach
                                    @endif
                                    </select>
                                </div>
                                <input class="slider" type="range" id="slider-{{$key}}" min="0" max="100" value="{{$item['percentage']}}" step="1">
                                <div class="time_value">
                                    <input type="number" class="form-field slider-value" id="number-{{$key}}" name="tod[{{$key + 1}}][tod_percentage]" value="{{$item['percentage']}}" /><p>%</p>
                                </div>
                            </div>
                            @endforeach
                            @else
                                @for($i = 1; $i<= 6; $i++)
                                <div class="time-slot">
                                    <div class="time-label">
                                        <label for="form-1-1" class="control-label">Time Slot {{$i}}</label>
                                        <input type="hidden" name="tod[{{$i}}][slot_id]" value="{{$i}}" />
                                    </div>
                                    <div class="timining-part">
                                        <select name="tod[{{$i}}][tod_start]" id="time" class="">
                                        @if(isset($hours) && count($hours) > 0)
                                            @foreach($hours as $keys => $value)
                                                <option  value="{{$keys}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                        <span>To</span>
                                        <select name="tod[{{$i}}][tod_end]" id="time" class="">
                                        @if(isset($hours) && count($hours) > 0)
                                            @foreach($hours as $keys => $value)
                                                <option  value="{{$keys}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                    <input class="slider" type="range" id="slider-{{$i}}" min="0" max="100" value="0" step="1">
                                    <div class="time_value">
                                        <input type="number" class="form-field slider-value" id="number-{{$i}}" name="tod[{{$key}}][tod_percentage]" value="0" />
                                    </div>
                                </div>
                                @endfor
                            @endif
                    </div>
                    <div class="col-sm-6">
                    </div>
                </div>
                <div class="col-md-12">
                @error('tod_not_valid')
                    <p class="invalid-feedback" style="display:block" role="alert">
                        <strong>{{ $message }}</strong>
                    </p>
                    @enderror
                    <p class="gray-text-part">Note: Percentage sum should be 100% and time slot distribution should be 24 hour</p>
                </div>
                <div class="col-md-12" style="margin-top:20px">
                    <div class="form-group row">
                        <div class="row">
                        <div class="col-md-6">
                            <input type="submit" class="form-control save-btn-pro" id="save_btn_tod" value="Save">
                        </div>
                        <div class="col-md-6" style="mar">
                            <button id="clear-button" class="btn btn-primary">Clear</button>
                        </div>
                        </div>
                    </div>
                </div>
            {{-- </form> --}}
        </div>
    </div>
</div>

                        <div class="col-md-12 convert-part" id="convert-part">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <p class="gray-text-part">Note:1st day is 1st sunday of January.</p>
                                </div>
                                <div class="col-md-5 convert-text">
                                    <p>Convert consumption data to desired granularity</p>
                                </div>
                                <div class="col-md-3 min-part">
                                    <select name="chunk_time" id="granularity_id" class="chunk_time">
                                        <option @if($profile_details->granularity_id == 1) selected @endif id="1" value="15">15 Mins</option>
                                        <option @if($profile_details->granularity_id == 2) selected @endif id="2" value="30">30 Mins</option>
                                        <option @if($profile_details->granularity_id == 3) selected @endif id="3" value="60">60 Mins</option>
                                        <!-- <option value="daily">Daily</option> -->
                                    </select>
                                </div>
                                 <div class="col-md-3 convert-btn">
                                    <a href="#" class="click-to-convert">Click here to convert</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 submit-export-btn">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                                        @if($profile_details->granularity_id == 1)
                                        <span class="heading-main" id="granularity_heading" style="padding-top:16px;"><b> 15 Minutes Granularity table</b></span>
                                        @elseif($profile_details->granularity_id == 2)
                                        <span class="heading-main" id="granularity_heading" style="padding-top:16px;"><b> 30 Minutes Granularity table</b></span>
                                        @elseif($profile_details->granularity_id == 3)
                                        <span class="heading-main" id="granularity_heading" style="padding-top:16px;"><b> 60 Minutes Granularity table</b></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button class="export-btn"><img src="{{asset('assets/img/download-file.png')}}"> Export File</button>
                                </div>
                                <div>
                                </div>
                            </div>
                        </div>
                        <div id="preloader">
                            <div class="loader_wrap">
                                <div id="loader"><img src="{{asset('assets/img/773.gif')}}" /></div>
                                <div id="loader_text">Please wait. It can take up to 2 minutes base on number of data to process.</div>
                            </div>
                        </div>
                        <div class="table-month-time" id="table-month-time">

                           <table class="table-edit custom-header">
                                <thead>
                                    <tr>
                                        <th>Months</th>
                                        <th>Day</th>
                                        <th>Hours</th>
                                        <th>Slot</th>
                                        <th>Consumption Unit(KWH)</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                      // Hide the preloader element
                      var preloader = document.getElementById("preloader");
                      preloader.style.display = "none";
                    });
                  </script>
                @if ($fromStore)
                <script>

                    var currentUrl = window.location.href;

                    // Append a parameter to the URL
                    var parameterName = '#tod-hide-show';
                    // var parameterValue = 'example';
                    var updatedUrl = currentUrl + parameterName;

                    // Redirect to the updated URL
                    if (currentUrl.includes('#tod-hide-show')) {
                            window.location.href = currentUrl;
                        }else{
                            window.location.href = updatedUrl;
                        }
                </script>
                @endif

                <script>
                    $(document).ready(function() {
                        var tod = @json($profile_details ?? null);
                        var id =@json($profile_details->id ?? null);
                        var state_id=@json($profile_details->state_id ?? null);
                        var profileDetails = @json($profile_details->ed_detail ?? null);



                        if(profileDetails && (profileDetails.ed_type == 2)){
                            $('#waived_field').hide();

                        }else if(profileDetails && (profileDetails.ed_type == 1)){
                            $('#rebate_field').hide();
                        }else if(profileDetails && (profileDetails.ed_type == 3)){
                            $('#waived_field').hide();
                            $('#rebate_field').hide();
                        }
                        else{
                            $('#rebate_field').hide();
                        }
                        var discom_value = $('#discom_id').val();

                        if(state_id != 0 && discom_value == 0)
                        {
                            var stateId = $('#state').val();
                            console.log(stateId);
                            if (stateId) {
                                $.ajax({
                                    url: '{{ route("getDiscom", ":stateId") }}'.replace(':stateId', stateId),
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function(data) {
                                        $('#discom_id').empty();
                                        $('#discom_id').append('<option value="0">Select Discom</option>');
                                        $.each(data, function(key, value) {
                                            $('#discom_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                                        });
                                    }
                                });

                                //  ajax for get state wise tod slots
                            $.ajax({
                                url: '{{ route("getStateSlot", ":stateId") }}'.replace(':stateId', stateId),
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    if(data.length > 0){
                                        $('.time-slot-col').empty();
                                        data.forEach(function(item, i) {
                                            var startSlot = item.start_slot;
                                            var endSlot = item.end_slot;
                                            console.log(startSlot);
                                            var html = `
                                                <div class="time-slot time-slot-wrap">
                                                    <div class="time-label">
                                                    <label for="timining-part" class="control-label">Time Slot ${i + 1}</label>
                                                    <input type="hidden" name="tod[${i + 1}][slot_id]" value="${i + 1}" />
                                                    </div>
                                                    <div class="timining-part">
                                                    <select name="tod[${i + 1}][tod_start]" id="time" class="">
                                                        @if(isset($hours) && count($hours) > 0)
                                                        @foreach($hours as $key => $item)
                                                            <option  ${'{{$key}}' == startSlot ? 'selected' : ''} value="{{$key}}">{{$item}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <span>To</span>
                                                    <select name="tod[${i + 1}][tod_end]" id="time" class="">
                                                        @if(isset($hours) && count($hours) > 0)
                                                        @foreach($hours as $key => $item)
                                                            <option  ${'{{$key}}' == endSlot ? 'selected' : ''} value="{{$key}}">{{$item}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    </div>
                                                    <input class="slider" type="range" id="slider-${i + 1}" min="0" value="0" max="100" step="1">
                                                    <div class="time_value">
                                                    <input type="number" class="form-field slider-value" id="number-${i + 1}" name="tod[${i + 1}][tod_percentage]" value="0" /><p>%</p>
                                                    </div>
                                                </div>
                                                `;
                                                $('.time-slot-col').append(html);
                                        });
                                        bindSliderEvents();

                                    }
                                    // $('#discom_id').empty();
                                    // $('#discom_id').append('<option value="0">Select Discom</option>');
                                    // $.each(data, function(key, value) {
                                    //     $('#discom_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                                    // });
                                }
                            });
                            } else {
                                $('#discom_id').empty();
                                $('#discom_id').append('<option value="0">Select Discom</option>');
                            }
                        }
                        $('#state').on('change', function() {
                            var stateId = $(this).val();
                            if (stateId) {
                                $.ajax({
                                    url: '{{ route("getDiscom", ":stateId") }}'.replace(':stateId', stateId),
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function(data) {
                                        $('#discom_id').empty();
                                        $('#wheeling_charge').val(0);
                                        $('#discom_id').append('<option value="0">Select Discom</option>');
                                        $.each(data, function(key, value) {
                                            $('#discom_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                                        });
                                    }
                                });

                                //  ajax for get state wise tod slots
                            $.ajax({
                                url: '{{ route("getStateSlot", ":stateId") }}'.replace(':stateId', stateId),
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    if(data.length > 0){
                                        $('.time-slot-col').empty();
                                        data.forEach(function(item, i) {
                                            var startSlot = item.start_slot;
                                            var endSlot = item.end_slot;
                                            console.log(startSlot);
                                            var html = `
                                                <div class="time-slot time-slot-wrap">
                                                    <div class="time-label">
                                                    <label for="timining-part" class="control-label">Time Slot ${i + 1}</label>
                                                    <input type="hidden" name="tod[${i + 1}][slot_id]" value="${i + 1}" />
                                                    </div>
                                                    <div class="timining-part">
                                                    <select name="tod[${i + 1}][tod_start]" id="time" class="">
                                                        @if(isset($hours) && count($hours) > 0)
                                                        @foreach($hours as $key => $item)
                                                            <option  ${'{{$key}}' == startSlot ? 'selected' : ''} value="{{$key}}">{{$item}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <span>To</span>
                                                    <select name="tod[${i + 1}][tod_end]" id="time" class="">
                                                        @if(isset($hours) && count($hours) > 0)
                                                        @foreach($hours as $key => $item)
                                                            <option  ${'{{$key}}' == endSlot ? 'selected' : ''} value="{{$key}}">{{$item}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    </div>
                                                    <input class="slider" type="range" id="slider-${i + 1}" min="0" value="0" max="100" step="1">
                                                    <div class="time_value">
                                                    <input type="number" class="form-field slider-value" id="number-${i + 1}" name="tod[${i + 1}][tod_percentage]" value="0" /><p>%</p>
                                                    </div>
                                                </div>
                                                `;
                                                $('.time-slot-col').append(html);
                                        });
                                        bindSliderEvents();

                                    }
                                    // $('#discom_id').empty();
                                    // $('#discom_id').append('<option value="0">Select Discom</option>');
                                    // $.each(data, function(key, value) {
                                    //     $('#discom_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                                    // });
                                }
                            });
                            } else {
                                $('#discom_id').empty();
                                $('#discom_id').append('<option value="0">Select Discom</option>');
                                $('#wheeling_charge').val(0);
                            }
                        });
                        const dropdown = document.getElementById('granularity_id');
                        const heading = document.getElementById('granularity_heading');

                        // Add event listener to the dropdown
                        dropdown.addEventListener('change', function() {
                        // Get the selected value from the dropdown
                            const selectedValue = dropdown.value;
                            if (selectedValue === '15') {
                                heading.innerHTML = '<b> Minutes Granularity Table (Option 1) </b>';
                            } else if (selectedValue === '30') {
                                heading.innerHTML = '<b>Hours Granularity Table (Option 2)</b>';
                            } else if (selectedValue === '60') {
                                heading.innerHTML = '<b>Days Granularity Table (Option 3)</b>';
                            }
                        // Append the selected value to the existing heading text
                        heading.style.fontWeight = 'bold';
                        heading.textContent =  selectedValue + ' '+ 'Minutes Granularity table';
                        });
                        $('#granularity_id').on('change', function()
                        {
                            var dropdown = document.getElementById('granularity_id');
                            var granularity_id = dropdown.options[dropdown.selectedIndex].id;
                             $.ajax({
                                        url: '{{route("granularity.update",":id")}}'.replace(':id', id),
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data: {
                                            granularity_id: granularity_id,
                                        },
                                        success: function (response) {
                                            // Handle the response from the controller
                                            console.log(response);
                                        },
                                        error: function (error) {
                                            // Handle any errors
                                            console.error(error);
                                        }
                                    });
                        });


                        $('#granularity_level_id').on('change', function()
                        {

                            var granularity_level_id = $(this).val();
                            if (granularity_level_id == 4)
                            {

                                $('#tod-hide-show').hide();
                                $('#tod-hide-show-working').hide();
                            }
                            else
                            {
                                $('#tod-hide-show').show();
                                $('#tod-hide-show-working').show();
                            }

                        });
                        if(tod.granularity_level_id == 4 && tod)
                            {
                                $('#tod-hide-show').hide();
                                $('#tod-hide-show-working').hide()
                            }
                        $('input[type="radio"][name="ed_type"]').change(function() {
                            var selectedOption = $(this).val();
                            if (selectedOption === "1") {
                                $('#waived_field').show();
                                $('#rebate_field').hide();
                            } else if (selectedOption === "2") {
                                $('#waived_field').hide();
                                $('#rebate_field').show();
                            } else {
                                $('#waived_field').hide();
                                $('#rebate_field').hide();
                            }
                        });
                        if(profileDetails && (profileDetails.waiver_time == 2)){
                            $('#available_upto_value').hide();
                            $('#year_waiver_value').hide();
                        }else if(profileDetails && (profileDetails.waiver_time == 3)){

                            $('#available_upto_value').hide();
                            $('#month_waiver_value').hide();
                        }else{

                            $('#month_waiver_value').hide();
                            $('#year_waiver_value').hide();
                        }


                        $('input[type="radio"][name="waiver_time"]').change(function() {
                            var selectedOption = $(this).val();
                            if (selectedOption == 1) {
                                $('#available_upto_value').show();
                                $('#month_waiver_value').hide();
                                $('#year_waiver_value').hide();
                            } else if (selectedOption == 2) {
                                $('#available_upto_value').hide();
                                $('#month_waiver_value').show();
                                $('#year_waiver_value').hide();
                            } else {
                                $('#available_upto_value').hide();
                                $('#month_waiver_value').hide();
                                $('#year_waiver_value').show();
                            }
                        });

                        // Loop through each slider element
                        $('.slider').each(function() {
                            var $slider = $(this);
                            console.log($slider);
                            // Event handler for slider input
                            $slider.on('input', function() {
                                var value = $(this).val();
                                // console.log();
                                // $(this).closest('div.time-value').find('.slider-value').val(value);
                                $(this).closest('div.time-slot').find('.time_value').children().val(value);
                            });
                        });

                        // $(document).ready(function() {
                        //   // Add event listeners to each input type number element
                        //   console.log("123");
                          $('.slider-value').on('change', function() {

                            var inputId = $(this).attr('id').split('-')[1]; // Extract the index from the input id
                            console.log(inputId);
                            var slider = $('#slider-' + inputId);
                            slider.val($(this).val());
                          });
                        // });


                        var consumption = @json($profile_details ?? null);
                        $('.table-month-time').hide();
                        $('.click-to-convert').on('click', function(e) {
                            $('.table-month-time').show();
                            $('#preloader').show();
                            e.preventDefault();
                            var profile_id = consumption.id;
                            var granularity_level = consumption.granularity_level_id;
                            var state_id = consumption.state_id;
                            var chunk_time = $('.chunk_time').val();
                            var csrfToken = $('meta[name="csrf-token"]').attr('content');
                            var data = {
                                profile_id: profile_id,
                                granularity_level: granularity_level,
                                chunk_time: chunk_time,
                                state_id: state_id
                            };
                            $.ajax({
                                   url: '{{ route("convert_consumption") }}',
                                   type: 'POST',
                                   headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                   data: data,
                                   success: function(response) {

                                        var tableBody = $('.table-month-time tbody');
                                        tableBody.empty();
                                        var currentUrl = window.location.href;
                                        var parameterName = '#table-month-time';
                                        if (currentUrl.includes('#convert-part')) {
                                            var newUrl = currentUrl.replace('#convert-part', '');
                                            window.location.href = newUrl + parameterName;
                                        }
                                        if(response.status == true && response.data.length > 0){
                                            console.log(response.data);
                                            $.each(response.data, function (index, interval) {
                                                var roundedUnit = Math.round(interval.unit);
                                                var row = '<tr>' +
                                                    '<td>' + interval.month + '</td>' +
                                                    '<td>' + interval.day + '</td>' +
                                                    '<td>' + interval.hours + '</td>' +
                                                    '<td>' + interval.slots + '</td>' +
                                                    '<td>' + roundedUnit + '</td></tr>';
                                                    // '<td style="padding:0">';

                                                // $.each(interval.slot, function(index2, value2) {
                                                //     row += '<span>' + value2.start + ' - ' +  value2.end+'</span>';
                                                // });

                                                // row += '</td><td style="padding:0">';
                                                // $.each(interval.slot, function(index2, value2) {
                                                //     row += '<span>' + value2.consumption +'</span>';
                                                // });
                                                // '</td></tr>';

                                                tableBody.append(row);

                                            });
                                        }else{
                                            var row = '<tr>' +
                                                    '<td colspan="3">No records found.</td>' +
                                                    '</tr>';

                                                tableBody.append(row);
                                        }
                                        $('#preloader').hide();
                                        $('.table-month-time').show();
                                   },
                                   complete: function() {
                                        $('#preloader').hide(); // Hide the preloader in case of an error or completion
                                    }
                                });
                                e.preventDefault();

                        });
                        //console.log($('#preloader'));
                        $('.export-btn').on('click', function(e) {
                        $('#preloader').show();
                        e.preventDefault();
                        var profile_id = consumption.id;
                        var granularity_level = consumption.granularity_level_id;
                        var state_id = consumption.state_id;
                        var chunk_time = $('.chunk_time').val();
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        var data = {
                            profile_id: profile_id,
                            granularity_level: granularity_level,
                            chunk_time: chunk_time,
                            state_id: state_id
                        };
                        $.ajax({
                           url: '{{ route("export_convert_consumption") }}',
                           type: 'POST',
                           headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                           data: data,
                           success: function(response) {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(new Blob([response]));
                            link.download = 'consumption.csv';
                            link.style.display = 'none';

                            // Trigger the download
                            document.body.appendChild(link);
                            link.click();

                            // Clean up the temporary link element
                            document.body.removeChild(link);
                            $('#preloader').hide();


                           },
                        error: function() {
                            // Handle the error and hide the preloader
                            $('#preloader').hide();
                        }

                        });

                    });


        // $('#customFile').change(function() {
        //     var selectedFile = $(this).prop('files')[0];

        //     // Perform AJAX request if a file is selected and the granularity level is TOD
        //     if (selectedFile && $('#granularity_level_id').val() === '3') {
        //         var formData = new FormData();
        //         formData.append('file', selectedFile);

        //         $.ajax({
        //             url: '{{ route("getTodData") }}',
        //             type: 'POST',
        //             data: formData,
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             processData: false,
        //             contentType: false,
        //             success: function(response) {
        //                 // Handle the AJAX response
        //                 console.log(response);
        //             },
        //             error: function(xhr, status, error) {
        //                 // Handle any errors that occur during the AJAX request
        //                 console.error(error);
        //             }
        //         });
        //     }
        // });
    });

</script>
<script>
    function bindSliderEvents() {
    // Loop through each slider element
    $('.slider').each(function() {
        var $slider = $(this);
        console.log($slider);

        // Event handler for slider input
        $slider.on('input', function() {
            var value = $(this).val();
            console.log(value);
            $(this).closest('.time-slot').find('.slider-value').val(value);
        });
    });

    $('.slider-value').on('change', function() {

        var inputId = $(this).attr('id').split('-')[1]; // Extract the index from the input id
        console.log(inputId);
        console.log($(this).val());
        var slider = $('#slider-' + inputId);
        slider.val($(this).val());
      });

}
</script>
<script>
    $(document).ready(function() {
       $("#clear-button").click(function() {
           clearTODValues();
       });

       function clearTODValues() {
           $("input[name^='tod']").val("0");
           $("select[name^='tod']").val("0");
           $(".slider").val(0);
       }
   });
</script>
<script>
    $('#save_btn_tod').on('click', function() {
        var id = @json($profile_details->id ?? null);
        var todData = []; // Create an array to store TOD data

        // Iterate through each time slot
        $('.time-slot').each(function(index) {
            var slotId = $(this).find('input[name^="tod["]').val();
            var todStart = $(this).find('select[name^="tod["][name$="[tod_start]"]').val();
            var todEnd = $(this).find('select[name^="tod["][name$="[tod_end]"]').val();
            var todPercentage = $(this).find('input[name^="tod["][name$="[tod_percentage]"]').val();

            // Create an object to represent the TOD data for the current slot
            var todSlotData = {
                slot_id: slotId,
                tod_start: todStart,
                tod_end: todEnd,
                tod_percentage: todPercentage
            };

            // Push the TOD slot data object to the array
            todData.push(todSlotData);
        });

        // Create the AJAX request
        $.ajax({
            url: '{{ route("consumption_profile.tod", ":id") }}'.replace(':id', id),
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                tod: todData // Pass the TOD data array
            },
        success: function(response) {
            console.log(response);
            var currentUrl = window.location.href;
            var parameterName = '#convert-part';
            if (currentUrl.includes('#tod-hide-show')) {
                var newUrl = currentUrl.replace('#tod-hide-show', '');
                window.location.href = newUrl + parameterName;
            }
            toastr.success(response.message);
        },
        error: function(response) {
            var errorMessage = response.responseJSON.error;
            toastr.error(errorMessage);
        }
        });
    });
    $('#clear-button').on('click', function() {
        var id = @json($profile_details->id ?? null);

        // Create the AJAX request
        $.ajax({
            url: '{{ route("tod-delete", ":id") }}'.replace(':id', id),
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        success: function(response) {
            window.location.href = response.redirect;
            toastr.success(response.message);
        },
        error: function(response) {
            var errorMessage = response.responseJSON.error;
            toastr.error(errorMessage);
        }
        });
    });
</script>
<script>
    $(document).ready(function() {
  $('form').submit(function() {
    $('#preloader').css('display', 'block'); // Show the loader element
  });
});
</script>
<script>
    $(document).ready(function() {

        $('#discom_id').on('change', function() {
            var discom_id = $(this).val();
            console.log(discom_id);
            if (discom_id) {
                //  ajax for get state wise discom
                $.ajax({
                    url: '{{ route("getWheelingCharge", ":discomId") }}'.replace(':discomId', discom_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // console.log(data[0].wheeling_charge);
                        $("#wheeling_charge").val(data[0].wheeling_charge);
                       
                    }
                });
            }

        });
    });
</script>

@endsection
