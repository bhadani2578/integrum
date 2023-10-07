@extends('layouts.app')
@section('title') {{'View Client'}} @endsection
@section('content')
<div class="content-body content content-components tracking-page-setup">
    <div class="container">

             
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <a href="{{url('client')}}" class="btn btn-success">
                <img src="{{url('assets/img/back-right.png')}}">
                <span>Back</span>
            </a>
        </div>      
        
        <div class="new-site-add-setup">
            <div class="row mg-t-40">
                <div class="col-md-9">
                    <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" >
                    @csrf
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Client Name</label>
                                <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="form-1-1" name="name" value="{{ $client_details->client_name }}" required autocomplete="name" autofocus>
                              
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Person Name</label>
                                <input type="text" class="form-control @error('person_name') is-invalid @enderror" id="form-1-1" name="person_name" value="{{ $client_details->person_name }}" required autocomplete="name" autofocus>
                              
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="form-1-1" class="control-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="form-1-1" name="email" value="{{ $client_details->email }}">
                               
                            </div>
                            <div class="col-sm-6">
                                <label for="form-1-1" class="control-label">Phone Number</label>
                                <input type="phone" class="form-control @error('phone') is-invalid @enderror" id="form-1-1" name="phone" value="{{ $client_details->phone }}">
                               
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Address</label>
                                <input type="textarea" class="form-control @error('address') is-invalid @enderror" id="form-1-1" name="address" value="{{ $client_details->address }}">
                                
                            </div>
                        </div>
                        <!-- <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="form-1-1" class="control-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="form-1-1" name="password"  >
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <label for="form-1-1" class="control-label">Confirm password</label>
                                <input type="password" class="form-control" id="form-1-1" name="password_confirmation" >
                            </div>
                        </div> -->
                        <!-- <div class="form-group row"> -->
                            <!-- <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Position</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" id="form-1-1" name="position" value="{{ $client_details->position }}">
                               
                            </div>
                        </div> -->
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Status</label>
                                <select name="status" id="status" class="@error('status') is-invalid @enderror">
                                <option value="">Select Status</option>
                                    <option @if($client_details->status == 0) selected @endif value="0">Inactive</option>
                                    <option @if($client_details->status == 1) selected @endif value="1">Active</option>
                                </select>
                                
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <a href="{{url('client')}}" class="btn btn-success">
                                    <img src="{{url('assets/img/back-right.png')}}">
                                    <span>Cancel</span>
                                </a>
                                <!-- <input type="submit" class="form-control" id="form-1-1" value="Cancel"> -->
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
    
    
@endsection
