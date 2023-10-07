@extends('layouts.app')
@section('content')

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">

<div class="content-body content content-components tracking-page-setup wrap-add-client">
    <div class="container">


        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <span><b>Edit Client</b></span>
        </div>

        <div class="new-site-add-setup">
            <div class="row mg-t-40">
                <div class="col-md-12">
                    <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup add-client" action="{{ route('client.update', $client_details->id) }}" method="POST" enctype='multipart/form-data'>
                    @csrf
                    @method('PUT')
                        <div class="form-group row clinet-row-1">
                            <div class="col-sm-6">
                                <label for="form-1-1" class="control-label">Client Name</label>
                                <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="form-1-1" name="client_name" value="{{ $client_details->client_name }}" required autocomplete="client_name" autofocus>
                                <!-- @error('client_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror -->
                            </div>
                            <div class="col-sm-6">
                                <label for="form-1-1" class="control-label">Parent Group</label>
                                <input type="text" class="form-control @error('parent_group') is-invalid @enderror" id="form-1-1" name="parent_group" value="{{ $client_details->parent_group }}" required autocomplete="parent_group" autofocus>
                                @error('parent_group')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-6" style="padding-top:10px;">
                                <label for="form-1-1" class="control-label">Type of Industry</label>
                                <select name="type_of_industry" id="status" class="@error('type_of_industry') is-invalid @enderror">
                                    <!-- <option value="0">Type</option> -->
                                    @if(isset($industry) && count($industry) > 0)
                                        @foreach($industry as $industries)
                                            <option value="{{$industries->id}}">{{$industries->label}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('type_of_industry')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row clinet-row-3">
                            <div class="col-sm-12 form-subtitle-custom" style="margin-bottom:15px;">
                                <h4>Key Person Details</h4>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row clinet-row-4" style="margin:0;margin-bottom: 15px;">
                                    <label for="form-1-1" class="control-label">Name</label>
                                    <input type="text" class="form-control @error('person_name') is-invalid @enderror" id="form-1-1" name="person_name" value="{{ $client_details->person_name }}">
                                    <!-- @error('person_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror -->
                                </div>
                                <div class="form-group row clinet-row-5" style="margin:0;margin-bottom: 15px;">
                                    <label for="form-1-1" class="control-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="form-1-1" name="email" value="{{ $client_details->email }}">                                    
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row clinet-row-6" style="margin:0;margin-bottom: 15px;">
                                    <label for="form-1-1" class="control-label">Phone</label>
                                    <div class="country-tel">
                                    <input type="hidden" name="country_code" class="phone_code" />
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ $client_details->phone }}" maxlength="10">
                                    </div>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group row clinet-row-6" style="margin:0;margin-bottom: 15px;">
                                    <label for="form-1-1" class="control-label">Designation</label>
                                    <input type="text" class="form-control @error('designation') is-invalid @enderror" id="form-1-1" name="designation" value="{{ $client_details->designation }}">
                                    @error('designation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="client-row-wrap">
                            <div class="col-sm-12 form-subtitle-custom" >
                                <h4 style="margin-bottom:15px;">Type of Lead</h4>
                            </div>
                            <div class="form-group row clinet-row-8">
                                <div class="col-sm-6">
                                    <label for="form-1-1" style="width: auto;display: inline-block;margin-right: 10px;" ><input type="radio" class=" @error('lead_type') is-invalid @enderror" id="form-1-1" name="lead_type" value="0" @if($client_details->lead_type == 0) checked @endif>
                                        Direct</label>
                                    <label for="form-1-1" style=" width: auto;display: inline-block;"><input type="radio" class="@error('lead_type') is-invalid @enderror" id="form-1-1" name="lead_type" value="1" @if($client_details->lead_type == 1) checked @endif >
                                        Indirect</label>
                                </div>
                            </div>
                            <div class="form-group row clinet-row-9">
                                <div class="col-sm-6">
                                    <label for="form-1-1" class="control-label">Employee Name/Consultant Name</label>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control @error('consultant_name') is-invalid @enderror" id="form-1-1" name="consultant_name" value="{{ $client_details->consultant_name }}">
                                    @error('consultant_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row clinet-row-10">
                                <div class="col-sm-6">
                                    <label for="form-1-1" class="control-label">INR/unit or INR/MW or Both</label>
                                </div>
                                <div class="col-sm-6">
                                    <select name="comission_fee" id="status" class="@error('comission_fee') is-invalid @enderror">
                                        <!-- <option value="0">Comission Type</option> -->
                                        @if(isset($comission_unit) && count($comission_unit) > 0)
                                            @foreach($comission_unit as $item)
                                                <option value="{{$item->id}}" @if($client_details->comission_fee == $item->id) selected @endif>{{$item->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('comission_fee')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        {{-- <div class="client-row-wrap" style="margin-top: 20px"> --}}

                           {{--  <div class="form-group row clinet-row-12">
                                <div class="col-sm-6">
                                    <label for="form-1-1" class="control-label">Number of Consumption Points</label>
                                </div>
                                <div class="col-sm-6">
                                <input type="number" class="form-control @error('consumption_point_no') is-invalid @enderror" id="form-1-1" name="consumption_point_no" value="{{ $client_details->consumption_point_no }}">
                                    @error('consumption_point_no')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div> --}}
                            {{-- <div class="form-group row clinet-row-13">
                                <div class="col-sm-6">
                                    <label for="form-1-1" class="control-label">Number of Sourcing Points</label>
                                </div>
                                <div class="col-sm-6">
                                <input type="number" class="form-control @error('source_point_no') is-invalid @enderror" id="form-1-1" name="source_point_no" value="{{ $client_details->source_point_no }}">
                                    @error('source_point_no')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div> --}}
                        {{-- </div> --}}
                        <div class="form-group row clinet-row-14">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Metadata Upload</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input  @error('is_metadata') is-invalid @enderror" id="customFile" name="is_metadata" value="{{ old('is_metadata') }}">
                                    @if(isset($client_details->is_metadata))
                                    <a href="{{url('/' . $client_details->is_metadata)}}" target="_blank">Metadata Detail</a>
                                    @endif
                                    @error('is_metadata')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row clinet-row-15">
                            <div class="col-sm-12">
                                <input type="submit" class="form-control" id="form-1-1" value="Update">
                            </div>
                        </div>
                    </form>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        // Assuming you have a pre-filled phone number
        var preFilledNumber = "{{$client_details->country_code}}{{$client_details->phone}}";

        // Initialize the phone input field
        var input = document.querySelector("#phone");
        var iti = window.intlTelInput(input, {
        initialCountry: "auto",
        separateDialCode: true,
        preferredCountries: ["in", "gb"], // Customize preferred countries
        });

        var hiddenInput = document.querySelector(".phone_code");
        hiddenInput.value = "{{$client_details->country_code}}";
        input.addEventListener("change", function() {

            var selectedCountryData = iti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            hiddenInput.value = "+" +countryCode;

        });

        // Set the pre-filled number
        iti.setNumber(preFilledNumber);

    </script>



@endsection
