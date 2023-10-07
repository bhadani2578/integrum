@if(!isset($showLeftSidebar) && session('client_detail'))
<aside class="aside aside-fixed">

    <div class="aside-header">

      <a href="{{ url('/client')}}" class="aside-logo"><img src="{{url('assets/img/logo.png')}}" style="height: 45px;"></a>

      <a href="" class="aside-menu-link">
        <i data-feather="menu"></i>
        <i data-feather="x"></i>
      </a>
    </div>

    <div class="aside-body">
    @if(!isset($showLeftSidebar) && session('client_detail'))
      <ul class="nav nav-aside">
        <li class="nav-item {{ ( request()->is('dashboard*')) ? 'active' : '' }}"><a href="{{ url('dashboard/' . base64_encode(session('client_detail')->id)) }}" class="nav-link"><i data-feather="home"></i> <span>Client Dashboard</span></a></li>
        <li class="nav-item  {{ ( request()->is('consumption_profile*')) ? 'active' : '' }}"><a href="{{route('consumption_profile.index')}}" class="nav-link"><i data-feather="sun"></i> <span>Consumption Profile</span></a></li>
        <li class="nav-item  {{ ( request()->is('source_profile*')) ? 'active' : '' }}"><a href="{{route('source_profile.index')}}" class="nav-link"><i data-feather="cpu"></i> <span>Generation Profile</span></a></li>
        <li class="nav-item {{ ( request()->is('mapping*')) ? 'active' : '' }}"><a href="{{route('mapping.index')}}" class="nav-link"><i data-feather="zap"></i> <span>Mapping</span></a></li>
        <li class="nav-item {{ ( request()->is('project*')) ? 'active' : '' }}"><a href="{{route('project.index')}}" class="nav-link"><i data-feather="plus-circle"></i> <span>Project</span></a></li>
        {{-- <li class="nav-item  {{ ( request()->is('client/*/edit')) ? 'active' : '' }}"><a href="{{route('client.edit', session('client_detail')->id)}}" class="nav-link"><i data-feather="user-check"></i> <span>Edit Client</span></a></li>
        <li class="nav-item  {{ ( request()->is('client/create')) ? 'active' : '' }}"><a href="{{route('client.create')}}" class="nav-link"><i data-feather="user-plus"></i> <span>Add Client</span></a></li> --}}
        <li class="nav-item"><a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i data-feather="log-in"></i> <span>Logout</span></a></li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
      </ul>
      @endif
    </div>

  </aside>

  @endif
