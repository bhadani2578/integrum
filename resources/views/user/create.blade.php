@extends('layouts.app')
@section('title') {{'Add User'}} @endsection
@section('content')
<div class="content-body content content-components tracking-page-setup">
    <div class="container">

             
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <a href="{{url('user')}}" class="btn btn-success">
                <img src="{{url('assets/img/back-right.png')}}">
                <span>Back</span>
            </a>
        </div>      
        
        <div class="new-site-add-setup">
            <div class="row mg-t-40">
                <div class="col-md-9">
                    <form class="form-horizontal mrg-top-40 pdd-right-30 ng-pristine ng-valid add-user-form-setup" action="{{ route('user.store') }}" method="POST" enctype='multipart/form-data'>
                    @csrf
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="form-1-1" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="form-1-1" class="control-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="form-1-1" name="email" value="{{ old('email') }}">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <label for="form-1-1" class="control-label">Phone Number</label>
                                <input type="phone" class="form-control @error('phone') is-invalid @enderror" id="form-1-1" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Address</label>
                                <input type="textarea" class="form-control @error('address') is-invalid @enderror" id="form-1-1" name="address" value="{{ old('address') }}">
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
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
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Position</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" id="form-1-1" name="position" value="{{ old('position') }}">
                                @error('position')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Permissions</label>
                                <select name="permissions" id="Permissions" class="@error('permissions') is-invalid @enderror">
                                <option value="">Select Permissions</option>
                                    <option value="0">Read</option>
                                    <option value="1">Write</option>
                                </select>
                                @error('permissions')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="form-1-1" class="control-label">Photo attachment</label>                       
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input  @error('photo') is-invalid @enderror" id="customFile" name="photo" value="{{ old('photo') }}">
                                    <label class="custom-file-label" for="customFile">Choosesss file</label>
                                    @error('photo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <input type="submit" class="form-control" id="form-1-1" value="Submit">
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
