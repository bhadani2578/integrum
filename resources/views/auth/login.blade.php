@extends('layouts.login')

@section('content')
<div class="content ht-100v pd-0 login-page-setup">
    <div class="content-body">
        <div class="login-body pd-x-0">
            <div class="sign-wrapper">
            <h2 class="tx-color-01 mg-b-5"><a href="{{ route('login') }}" class="aside-logo"><img src="{{asset('assets/img/login_logo.png')}}" ></a></h2>
            <p class="tx-color-03 tx-16 mg-b-40" style="color: #fff;">Please enter your user name and password to login</p>

                <div class="login-form-group">
                    <div class="form-group">
                        <div class="mg-b-5">
                            <div class="login-lock"><img src="{{asset('assets/img/lock.png')}}"> &nbsp;Login</div>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
        
                                <!-- <div class="row mb-3"> -->
                                   
        
                                    <!-- <div class="col-md-6"> -->
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    <!-- </div> -->
                                <!-- </div> -->
        
                                <!-- <div class="row mb-3"> -->
                                    <!-- <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label> -->
        
                                    <!-- <div class="col-md-6"> -->
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
        
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    <!-- </div>
                                </div> -->
        
                                <!-- <div class="row mb-3">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        
                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
         -->
                                <!-- <div class="row mb-0">
                                    <div class="col-md-8 offset-md-4"> -->
                                        <input type="submit" name="submit" value="{{ __('Login') }}">
                                            
                                        <!-- </button> -->
        
                                        <!-- @if (Route::has('password.request'))
                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        @endif -->
                                    <!-- </div>
                                </div> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="lib/jquery/jquery.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="lib/feather-icons/feather.min.js"></script>
    <script src="lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="lib/jquery.flot/jquery.flot.js"></script>
    <script src="lib/jquery.flot/jquery.flot.stack.js"></script>
    <script src="lib/jquery.flot/jquery.flot.resize.js"></script>
    <script src="lib/chart.js/Chart.bundle.min.js"></script>
    <script src="lib/jqvmap/jquery.vmap.min.js"></script>
    <script src="lib/jqvmap/maps/jquery.vmap.usa.js"></script>

    <script src="assets/js/dashforge.js"></script>
    <script src="assets/js/dashforge.aside.js"></script>
    <script src="assets/js/dashforge.sampledata.js"></script>
    <script src="assets/js/dashboard-one.js"></script>

    <!-- append theme customizer -->
    <script src="lib/js-cookie/js.cookie.js"></script>
    <script src="assets/js/dashforge.settings.js"></script>
@endsection
