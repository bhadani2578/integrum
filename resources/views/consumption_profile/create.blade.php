@extends('layouts.app')

@section('content')

<div class="content-body content content-components tracking-page-setup index">
    <div class="pd-x-0">
        <div class="wrap_topbar" style="padding-bottom: 15px;">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <span class="heading-main"><b>Add Consumption Profile</b></span>
            </div>

            <div class="d-sm-flex align-items-center justify-content-end mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <div class="top-right-buttons">
                    <a href="{{route('consumption_profile.create')}}" class="btn btn-success">
                        <img src="{{asset('assets/img/plus.png')}}">
                        <span>Add Consumption Profile</span>
                    </a>
                </div>
            </div>
      </div>
        <div class="new-site-add-setup consumption-edit-main-part">
            <div class="row">
                <div class="col-md-12 consumption-edit-pro">
                    <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('consumption_profile.store') }}" method="POST" enctype='multipart/form-data'>
                    @csrf
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="point_name" class="control-label">Consumption Point Name</label>
                                <input type="text" class="form-control @error('point_name') is-invalid @enderror" id="form-1-1" name="point_name" value="{{ old('point_name') }}" placeholder="Point Name" autocomplete="point_name" autofocus>
                               <!-- @error('source_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="state_id" class="control-label">State of Consumption Point</label>
                                <select name="state_id" id="state" class="c @error('state_id') is-invalid @enderror">
                                <option value="0">States</option>
                                    @if(isset($state_list) && count($state_list) > 0)
                                        @foreach($state_list as $item)
                                            <option value="{{$item->id}}" {{ old('state_id') == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
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
                                <select name="voltage_id" id="status" class=" form-control @error('voltage_id') is-invalid @enderror">
                                <option value="0">Voltage(kV)</option>
                                    @if(isset($voltage_list) && count($voltage_list) > 0)
                                        @foreach($voltage_list as $item)
                                            <option value="{{$item->id}}" {{ old('voltage_id') == $item->id ? 'selected' : '' }}>{{$item->kg}}(KV)</option>
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
                                <select name="discom_id" id="discom_id" class=" form-control @error('discom_id') is-invalid @enderror">
                                <option value="0">Select Discom</option>

                                </select>
                              <!-- @error('discom_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                            <div class="col-sm-5 weeling_wrap">
                                <label for="wheeling_charge" class="control-label">Wheeling Charges</label>
                                <input type="text" class="form-control @error('wheeling_charge') is-invalid @enderror" id="wheeling_charge" name="wheeling_charge" placeholder="0" value="{{ old('wheeling_charge') }}" required>
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
                                <select name="discom_category_id" class="form-control @error('discom_category_id') is-invalid @enderror">
                                <option value="0">Tariff Category</option>
                                    @if(isset($tariff_category) && count($tariff_category) > 0)
                                        @foreach($tariff_category as $item)
                                            <option value="{{$item->id}}" {{ old('discom_category_id') == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
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
                                <input type="number" class="form-control @error('contract_demand') is-invalid @enderror" id="form-1-1" name="contract_demand" value="{{ old('contract_demand') }}"placeholder="0" required>
                               <!-- @error('contract_demand')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                            <div class="col-sm-2">
                                <select name="contract_unit" class="form-control @error('contract_unit') is-invalid @enderror">
                                    @if(isset($units) && count($units) > 0)
                                        @foreach($units as $key => $item)
                                            <option value="{{$key}}" {{ old('contract_unit') == $key ? 'selected' : '' }}>{{$item}}</option>
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
                                <input type="number" class="form-control @error('contract_demand_limitation') is-invalid @enderror" id="form-1-1" name="contract_demand_limitation" placeholder="0" value="{{ old('contract_demand_limitation') }}" required>
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
                                <label for="form-1" style="width: auto;display: inline-block;margin-right: 10px;" ><input type="radio" class=" @error('ed_type') is-invalid @enderror" id="form-1-1" name="ed_type" value="1" id="waived_field" checked>
                                Waived</label>
                                <label for="for111" style=" width: auto;display: inline-block;"><input type="radio" class="@error('ed_type') is-invalid @enderror" id="form-1-1" name="ed_type" id="rebate_field" value="2">
                                Rebate</label>
                                <label for="foe" style=" width: auto;display: inline-block;"><input type="radio" class="@error('ed_type') is-invalid @enderror" id="form-1-1" name="ed_type" id="no_rebate_field" value="3">
                                No rebate</label>
                            </div>
                        </div>
                        <div class="form-group" id="waived_field">
                            <div class="form-group row">
                                <div class="col-sm-12 waiver_format">
                                    <div class="col-sm-6 label-part-wave"><label for="form-1-1" >ED waiver time frame</label></div>
                                    <div class="col-sm-6 time_waiver_wrap">
                                        <div class="time-radio">
                                            <label for="available upto" style="width: auto;display: inline-block;margin-right: 10px;" ><input type="radio" class=" @error('waiver_time') is-invalid @enderror"  name="waiver_time" value="1" id="available_upto" checked>
                                            Available upto</label>
                                            <label for="month" style=" width: auto;display: inline-block;"><input type="radio" class="@error('waiver_time') is-invalid @enderror"  name="waiver_time" id="month_waiver" value="2">
                                            Month</label>
                                            <label for="year" style=" width: auto;display: inline-block;"><input type="radio" class="@error('waiver_time') is-invalid @enderror"  name="waiver_time" id="year_waiver" value="3">
                                            Year</label>
                                        </div>
                                        <div class="available-section">
                                            <div class="form-group row" id="available_upto_value">
                                                <div class="col-sm-12">
                                                    <label for="available" class="control-label">Available Upto</label>
                                                    <input type="date" class="form-control @error('available_upto') is-invalid @enderror" id="form-1-1" name="available_upto" value="{{ old('avialable_upto') }}">
                                                    @error('available_upto')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row" id="month_waiver_value">
                                                <div class="col-sm-12">
                                                    <label for="waiver_month" class="control-label">Months</label>
                                                    <input type="number" class="form-control @error('waiver_month') is-invalid @enderror" id="form-1-1" name="waiver_month" value="{{ old('waiver_month') }}">
                                                    @error('waiver_month')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row" id="year_waiver_value">
                                                <div class="col-sm-12">
                                                    <label for="year_waiver_value" class="control-label">Years</label>
                                                    <input type="number" class="form-control @error('waiver_year') is-invalid @enderror" id="form-1-1" name="waiver_year" value="{{ old('waiver_year') }}">
                                                    @error('waiver_year')
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
                                    <label for="rebate_lable" class="control-label">ED rebate Type</label>
                                </div>
                                <div class="rebate_wrap">
                                    <div class="col-sm-6">
                                        <label for="Rs_per" style="width: auto;display: inline-block;margin-right: 10px;" ><input type="radio" class=" @error('rebate_type') is-invalid @enderror" id="form-1-1" name="rebate_type" value="1"  checked>
                                        Rs Per Unit</label>
                                        <label for="Percentage" style=" width: auto;display: inline-block;"><input type="radio" class="@error('rebate_type') is-invalid @enderror" id="form-1-1" name="rebate_type"  value="2">
                                        Percentage</label>
                                    </div>
                                    <div class="rebate_value">
                                        <div class="col-sm-3">
                                            <label for="rebate_value1" class="control-label">ED rebate Value</label>
                                        </div>
                                        <div class="col-sm-3 ed_value">
                                            <input type="text" class="form-control @error('rebate_value') is-invalid @enderror" id="form-1-1" name="rebate_value" value="{{ old('rebate_value') }}">
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
                                <label for="category_consumption_id" class="control-label">Category of consumption</label>
                                <select name="category_consumption_id" id="status" class="form-control @error('category_consumption_id') is-invalid @enderror">
                                    <option value="0">Categories</option>
                                    @if(isset($industry) && count($industry) > 0)
                                        @foreach($industry as $industries)
                                            <option value="{{$industries->id}}" {{ old('category_consumption_id') == $industries->id ? 'selected' : '' }}>{{$industries->label}}</option>
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
                                <label for="granularity_level_id" class="control-label">Uploaded Granularity Level</label>
                                <select name="granularity_level_id" id="granularity_level_id" class="form-control @error('granularity_level_id') is-invalid @enderror">
                                    <option value="0">Granularity Level</option>
                                    <option value="1" {{ old('granularity_level_id') == 1 ? 'selected' : '' }}>Annual</option>
                                    <option value="2" {{ old('granularity_level_id') ==  2 ? 'selected' : '' }}>Monthly</option>
                                    <option value="3" {{ old('granularity_level_id') == 3 ? 'selected' : '' }}>TOD</option>
                                    <option value="4" {{ old('granularity_level_id') == 4 ? 'selected' : '' }}>Hourly</option>
                                    <option value="5" {{ old('granularity_level_id') == 5 ? 'selected' : '' }}>Weekly</option>

                                </select>
                                <!-- @error('granularity_level_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="custom-file-input" class="control-label">Upload consumption data at available granularity</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input  @error('consumption_file_path') is-invalid @enderror" id="customFile" name="consumption_file_path" value="{{ old('consumption_file_path')  }}">
                                    <!-- <label class="custom-file-label" for="customFile">Choosesss file</label> -->
                                    @error('consumption_file_path')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>
                        </div>
                        <div id="tod-hide-show">
                        <div class="col-md-12 working-part">
                            <div class="form-group row">
                                <div class="col-sm-9">
                                    <div class="col-sm-6">
                                        <label for="day_start" class="control-label">Working Days</label>

                                        <select name="day_start" id="day-mon" class="">
                                            <option value="">Days</option>
                                            @if(isset($day_list) && count($day_list) > 0)
                                                @foreach($day_list as $item)
                                                    <option value="{{$item->id}}">{{$item->day}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span>To</span>
                                        <select name="day_end" id="day-fri" class="">
                                            <option value="">Days</option>
                                            @if(isset($day_list) && count($day_list) > 0)
                                                @foreach($day_list as $item)
                                                    <option value="{{$item->id}}">{{$item->day}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="shift_start" class="control-label">Shift Duration</label>
                                        <select name="shift_start" id="time" class="">
                                            @if(isset($hours) && count($hours) > 0)
                                                @foreach($hours as $key => $item)
                                                    <option value="{{$key}}">{{$item}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span>To</span>
                                        <select name="shift_end" id="time" class="">
                                            @if(isset($hours) && count($hours) > 0)
                                                @foreach($hours as $key => $item)
                                                    <option value="{{$key}}">{{$item}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <input type="submit" class="form-control save-btn-pro" id="form-1-1" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="preloader" style="display:none">
            <div class="loader_wrap">
                <div id="loader"><img src="{{asset('assets/img/773.gif')}}" /></div>
                <div id="loader_text">Please wait. It can take up to 2 minutes base on number of data to process.</div>
            </div>
        </div>
        <div id="tod-hide-show-working">

            <div class="col-md-12 working-part">
                <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                    <span class="heading-main" style="padding-top:16px;"><b>TOD </b></span>
                </div>
                <div class="form-group row">
                    <div class="col-sm-9">
                        <div class="col-sm-12 time-slot-col">
                            @for($i = 1; $i <= 6; $i++)
                            <div class="time-slot time-slot-wrap">
                                    <div class="time-label">
                                        <label for="timining-part" class="control-label">Time Slot {{$i}}</label>
                                        <input type="hidden" name="tod[{{$i}}][slot_id]" value="{{$i}}" />
                                    </div>
                                    <div class="timining-part">
                                        <select name="tod[{{$i}}][tod_start]" id="time" class="">
                                        @if(isset($hours) && count($hours) > 0)
                                            @foreach($hours as $key => $item)
                                                <option value="{{$key}}">{{$item}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                        <span>To</span>
                                        <select name="tod[{{$i}}][tod_end]" id="time" class="">
                                        @if(isset($hours) && count($hours) > 0)
                                            @foreach($hours as $key => $item)
                                                <option value="{{$key}}">{{$item}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                    <input class="slider" type="range" min="0" value="0" max="100" step="1">
                                    <div class="time_value">
                                        <input type="number" class="form-field slider-value" name="tod[{{$i}}][tod_percentage]" value="0" /><p>%</p>
                                    </div>
                                </div>
                            @endfor


                        </div>
                        <div class="col-sm-6">
                        </div>
                    </div>

                    <div class="col-md-12" style="margin-top:20px">
                        <div class="col-md-12">
                            <p class="gray-text-part">Note: Percentage sum should be 100% and time slot distribution should be 24 hour</p>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <input type="submit" disabled class="form-control save-btn" id="form-1-1" value="Save">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="col-md-12 convert-part">
                <div class="form-group row">
                    <div class="col-md-12">
                        <p class="gray-text-part">Note:1st day is 1st sunday of January.</p>
                    </div>
                    <div class="col-md-5 convert-text">
                        <p>Convert consumption data to desired granularity</p>
                    </div>
                    <div class="col-md-3 min-part">
                        <select name="time" id="time" class="">
                            <option selected="" value="0">15 Mins</option>
                            <option selected="" value="1">30 Mins</option>
                            <option selected="" value="2">60 Mins</option>
                        </select>
                    </div>
                     <div class="col-md-3 convert-btn">
                        <a href="#" class="click-to-convert" style="pointer-events: none">Click here to convert</a>
                    </div>
                </div>
            </div>
            <div class="col-md-12 submit-export-btn">
                <div class="form-group row">
                    <div class="col-sm-6">
                    </div>
                    <div class="col-md-6">
                            <button class="export-btn" style="pointer-events: none"><img src="{{asset('assets/img/download-file.png')}}"> Export File</button>
                        </div>
                    </div>
                </div>
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

    <script>
    $(document).ready(function() {

        $('#rebate_field').hide();
        $('#state').on('change', function() {
            var stateId = $(this).val();
            if (stateId) {
                //  ajax for get state wise discom
                $.ajax({
                    url: '{{ route("getDiscom", ":stateId") }}'.replace(':stateId', stateId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#discom_id').empty();
                        $("#wheeling_charge").val(0);
                        $('#discom_id').append('<option value="0">Select Discom</option>');
                        $.each(data, function(key, value) {
                            $('#discom_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        var oldDiscomId = '{{ old("discom_id") }}';
                        if (oldDiscomId) {
                            discomDropdown.val(oldDiscomId);
                        }
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
                                        <input class="slider" type="range" min="0" value="0" max="100" step="1">
                                        <div class="time_value">
                                          <input type="number" class="form-field slider-value" name="tod[${i + 1}][tod_percentage]" value="0" /><p>%</p>
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
        $('#month_waiver_value').hide();
        $('#year_waiver_value').hide();
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
        $('.slider').each(function() {
        var $slider = $(this);
        // Event handler for slider input
            $slider.on('input', function() {
            var value = $(this).val();
            // $(this).closest('div').find('.slider-value').val(value);
            $(this).closest('div.time-slot').find('.time_value').children().val(value);
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
}
</script>
<!--  script for form submit event -->
<script>
    $(document).ready(function() {
  $('form').submit(function() {
    $('#preloader').css('display', 'block'); // Show the loader element
  });
});
</script>
<script>
    $(document).ready(function() {
        var oldStateid = "{{ old('state_id') }}";
        var olddiscom = "{{ old('discom_id') }}";
        if(oldStateid){
            
            $.ajax({
                url: '{{ route('getDiscom', ':stateId') }}'.replace(':stateId', oldStateid),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#discom_id').empty();
                    $('#discom_id').append('<option value="0">Select Discom</option>');
                    $.each(data, function(key, value) {
                        $('#discom_id').append('<option value="' + value.id +
                            '">' + value.name + '</option>');
                    });
                    
                    $('#discom_id').val(olddiscom);
                }
            });

        }
        
    })
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
