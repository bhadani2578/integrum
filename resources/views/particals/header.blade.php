<div class="navbar-header" style="position:relative">
    <div class="content-header">
        <div class="navbar-left">
            <!-- <h1>@yield('title')</h1> -->

                    @if(isset($showLeftSidebar))
                        <a href="{{ url('/client')}}" class="aside-logo"><img src="{{url('assets/img/logo.png')}}" style="height: 45px;"></a>
                    @endif
                    @if(!isset($showLeftSidebar))
                    @if(isset($client_list) && count($client_list) > 0)
                    <div class="project_dropdown">
                    <label class="header-heading-part" >{{isset($current_client->client_name) ? $current_client->client_name : ''}}</label>
                    <!-- <label class="new_add program_heading" ></label> -->
                   {{--  <h5 class=" dropdown-toggle new_add program_heading"  id="dropdownProject" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="dropdownProject">{{$current_client->client_name}}</span><span class="l_rotate"></span></h5>
                    <div class="dropdown-menu header_add"  aria-labelledby="dropdownProject">
                        @foreach($client_list as $value)
                            <a href="{{ route('dashboard', ['id' => base64_encode($value->id)]) }}">{{$value->client_name}}</a></br>
                        @endforeach
                    </div> --}}
                    </div>
                    @endif
                    @endif

        </div>
        <div class="navbar-right">

            <div class="dropdown dropdown-profile">
                <a href="" class="dropdown-link" data-toggle="dropdown" data-display="static">
                    <div class="avatar avatar-sm">{{ ucfirst(substr(auth()->user()->name, 0, 1))}}<!-- <img src="https://via.placeholder.com/500" class="rounded-circle" alt=""> --></div>
                </a><!-- dropdown-link -->
                <div class="dropdown-menu dropdown-menu-right tx-13">
                    <!-- <div class="avatar avatar-lg mg-b-15"><img src="https://via.placeholder.com/500" class="rounded-circle" alt=""></div>
                    <h6 class="tx-semibold mg-b-5">Katherine Pechon</h6> -->

                    <a href="{{ route('change_password') }}" class="dropdown-item"><i data-feather="settings"></i>Change Password</a>
                    <a href="{{ route('logout') }}" class="dropdown-item"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i data-feather="log-out"></i>
                        Sign Out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div><!-- dropdown-menu -->
            </div><!-- dropdown -->
        </div><!-- navbar-right -->
        <div class="navbar-search">
            <div class="navbar-search-header">
                <input type="search" class="form-control" placeholder="Type and hit enter to search...">
                <button class="btn"><i data-feather="search"></i></button>
                <a id="navbarSearchClose" href="" class="link-03 mg-l-5 mg-lg-l-10"><i data-feather="x"></i></a>
            </div><!-- navbar-search-header -->
            <div class="navbar-search-body">
                <label class="tx-10 tx-medium tx-uppercase tx-spacing-1 tx-color-03 mg-b-10 d-flex align-items-center">Recent Searches</label>
                <ul class="list-unstyled">
                    <li><a href="dashboard-one.html">modern dashboard</a></li>
                    <li><a href="app-calendar.html">calendar app</a></li>
                    <li><a href="../../collections/modal.html">modal examples</a></li>
                    <li><a href="../../components/el-avatar.html">avatar</a></li>
                </ul>

                <hr class="mg-y-30 bd-0">

                <label class="tx-10 tx-medium tx-uppercase tx-spacing-1 tx-color-03 mg-b-10 d-flex align-items-center">Search Suggestions</label>

                <ul class="list-unstyled">
                    <li><a href="dashboard-one.html">cryptocurrency</a></li>
                    <li><a href="app-calendar.html">button groups</a></li>
                    <li><a href="../../collections/modal.html">form elements</a></li>
                    <li><a href="../../components/el-avatar.html">contact app</a></li>
                </ul>
            </div><!-- navbar-search-body -->
        </div><!-- navbar-search -->
    </div><!-- content-header -->
</div>
