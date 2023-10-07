@extends('layouts.app')
@section('title') {{'Client Dashboard'}} @endsection
@section('content')
<div class="content-body" style="background-color: #f5f5f5">
    <div class="pd-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30 dashboard">
            <div class="wrap_topbar">
                <span><b>Client Dashboard</b></span>
          </div>
        </div>
        <div class="row mg-t-25 table-row-first2"><!-- second row start -->
            <div class="col-md-12"><!-- 3-table -->
                <div class="dashboard-table-setup">
                    <h5 class="tx-spacing-1 tx-color-02 tx-semibold mg-b-15">
                        <span class="consumption-profile-text">Client details</span>
                        <a href="{{ route('client.edit', session('client_detail')->id) }}" id="addRowBtn" style="color: gray; margin-left: -10px; display: flex; align-items: center;">
                            <i class="fa fa-edit"></i>
                        </a>
                    </h5>
                    <div class="dashbord-table-one">
                        <table id="example3" class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Client Name</th>
                                    <th scope="col">Key Person</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> {{$sessionId->client_name}} </td>
                                    <td> {{$sessionId->person_name}} </td>
                                    <td> {{$sessionId->email}} </td>
                                    <td> {{$sessionId->phone}} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mg-t-25 table-row-first2">

            <div class="col-md-6">
                <div class="dashboard-table-setup">
                     <h5 class="tx-spacing-1 tx-color-02 tx-semibold mg-b-15">
                        <span class="consumption-profile-text">Consumption Profile</span>
                        <a href="{{ route('consumption_profile.create') }}" id="addRowBtn">+ Add Consumption</a>
                    </h5>
                    <div class="dashbord-table-one">
                        <table id="example3" class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Point Name</th>
                                    <th scope="col">State</th>
                                    <th scope="col">Connectivity</th>
                                    <th scope="col"> </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($consumption_list) && count($consumption_list) > 0)
                                @foreach($consumption_list as $key => $item)
                                <tr>
                                    <td>{{isset($item->point_name) ? $item->point_name : ''}}</td>
                                    <td>{{isset($item->state->name) ? $item->state->name : ''}}</td>
                                    <td>{{isset($item->voltage->kg) ? $item->voltage->kg . 'KV' : ''}}</td>
                                    <td> <a href="{{ route('consumption_profile.edit', $item->id) }}"  title="View"><i class="fa fa-eye"></i></a> </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="records"> No Consumption Found click here to <a href="{{ route('consumption_profile.create') }}" >Create Consumption</a> </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-table-setup">
                    <h5 class="tx-spacing-1 tx-color-02 tx-semibold mg-b-15">
                        <span class="consumption-profile-text">Generation Profile</span>
                        <a href="{{ route('source_profile.create') }}" id="addRowBtn">+ Add Generation</a>
                    </h5>
                    <div class="dashbord-table-one">
                        <table id="example4" class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Generation Name</th>
                                    <th scope="col">Generation</th>
                                    <th scope="col">Contract</th>
                                    <th scope="col"> </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($source_list) && count($source_list) > 0)
                                @foreach($source_list as $key => $item)
                                <tr>
                                    <td>{{isset($item->source_name) ? $item->source_name : ''}}</td>
                                    <td>{{isset($item->voltage->kg) ? $item->voltage->kg . 'KV' : ''}}</td>
                                    <td>{{isset($item->contract->name) ? $item->contract->name : ''}}</td>
                                    <td> <a href="{{ route('source_profile.edit', $item->id) }}"  title="View"><i class="fa fa-eye"></i></a> </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="records"> No Generation Found click here to <a href="{{ route('source_profile.create') }}" >Create Generation</a> </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
        <div class="row mg-t-25 table-row-first2">

            <div class="col-md-6">
                <div class="dashboard-table-setup">
                     <h5 class="tx-spacing-1 tx-color-02 tx-semibold mg-b-15">
                        <span class="consumption-profile-text">Mapping Profile</span>
                        <a href="{{ route('mapping.create') }}" id="addRowBtn">+ Add Mapping</a>
                    </h5>
                    <div class="dashbord-table-one">
                        <table id="example3" class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Mapping Name</th>
                                    <th scope="col">Consumption Point</th>
                                    <th scope="col">Generation Point</th>
                                    <th scope="col"> </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($mapping_list) && count($mapping_list) > 0)
                                @foreach($mapping_list as $key => $item)
                                <tr>
                                    <td>{{isset($item->mapping_name) ? $item->mapping_name : ''}}</td>
                                    <td>{{isset($item->consumption_profile->point_name) ? $item->consumption_profile->point_name : ''}}</td>
                                    <td>{{isset($item->source_profile->source_name) ? $item->source_profile->source_name : ''}}</td>
                                    <td> <a href="{{ route('mapping.edit', $item->id) }}"  title="View"><i class="fa fa-eye"></i></a> </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="records"> No Mapping Found click here to <a href="{{ route('mapping.create') }}" >Create Mapping</a> </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-table-setup">
                    <h5 class="tx-spacing-1 tx-color-02 tx-semibold mg-b-15">
                        <span class="consumption-profile-text">Project</span>
                        <a href="{{ route('project.create') }}" id="addRowBtn">+ Add Project</a>
                    </h5>
                    <div class="dashbord-table-one">
                        <table id="example4" class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Project site</th>
                                    <th scope="col">Total Capacity</th>
                                    <th scope="col">Total CAPEX</th>
                                    <th scope="col"> </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($project_list) && count($project_list) > 0)
                                @foreach($project_list as $key => $item)
                                <tr>
                                    <td>{{isset($item->site_name) ? $item->site_name : ''}}</td>
                                    <td>{{isset($item->total_capacity) ? $item->total_capacity : ''}}</td>
                                    <td>{{isset($item->total_capex) ? $item->total_capex : ''}}</td>
                                    <td> <a href="{{ route('project.edit', $item->id) }}"  title="View"><i class="fa fa-eye"></i></a> </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="records"> No Project Found click here to <a href="{{ route('project.create') }}" >Create Project</a> </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
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
    <script src="{{url('lib/select2/js/select2.min.js')}}"></script>
    <!-- append theme customizer -->
       <script src="{{url('assets/js/dashforge.settings.js')}}"></script>


      <script src="{{url('assets/js/chart.chartjs.js')}}"></script>

    <script src="{{url('lib/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{url('lib/datatables.net-dt/js/dataTables.dataTables.min.js')}}"></script>
    <script src="{{url('lib/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{url('lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js')}}"></script>
    <script src="{{url('lib/select2/js/select2.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
      <script>
      $(function(){
        'use strict'

        $('#boxontable').DataTable({
          language: {
            lengthMenu: '_MENU_ items/page',
          }
        });

    });
    </script>
    <script>
        $(document).ready(function() {
            // Hide success message after 3 seconds
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 3000);
        });
    </script>
@endsection
