@extends('layouts.app')
@section('content')
<div class="content-body content content-components tracking-page-setup index">
    <div class="pd-x-0">
<div class="wrap_topbar">
            <span><b>Project list</b></span>

            <div class="d-sm-flex align-items-center justify-content-end mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <div class="top-right-buttons">
                    <a href="{{route('project.create')}}" class="btn btn-success">
                        <img src="{{asset('assets/img/plus.png')}}">
                        <span>Add Project</span>
                    </a>
                </div>
            </div>
      </div>
        <div data-label="boxon" class="boxon-site-table mg-b-25 mg-t-40">
            <table id="boxontable" class="table user-management-setup table-row-withoutbg">
                <thead>
                    <tr>
                        <th class="wd-15p">Project Name</th>
                        <th class="wd-15p">Mapping</th>
                        <th class="wd-15p">Project Site</th>
                        <th class="wd-15p">Total Capacity</th>
                        <th class="wd-15p">Total CAPEX</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($project_list) && count($project_list) > 0)
                    @foreach($project_list as $key => $item)
                    <tr>
                        <td>{{isset($item->site_name) ? $item->site_name : '11'}}</td>
                        <td>{{isset($item->mapping->mapping_name) ? $item->mapping->mapping_name : ''}}</td>
                        <td>{{isset($item->project_location) ? $item->project_location : ''}}</td>
                        <td>{{isset($item->total_capacity) ? $item->total_capacity : ''}}</td>
                        <td>{{isset($item->total_capex) ? $item->total_capex : ''}}</td>
                        <td>
                            <a href="{{ route('project.edit', $item->id) }}"  title="View"><i class="fa fa-eye"></i></a>
                            {{-- <a href="{{ route('project.destroy', $item->id) }}" title="Delete" onclick="event.preventDefault(); if (confirm('Are you sure you want to delete this item?')) document.getElementById('delete-form-{{ $item->id }}').submit();"><i class="fa fa-trash"></i></a>
                            <form id="delete-form-{{ $item->id }}" action="{{ route('project.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form> --}}
                            <a href="#" title="Delete" onclick="openDeletePopup('{{ route('project.destroy', $item->id) }}')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>
@include('delete_popup')
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
      $(function(){
        'use strict'

        $('#boxontable').DataTable({
          language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ items/page',
          },
          columnDefs: [
            { type: 'date', targets: 0 } // Set the first column to be treated as a date type
        ],
            order: [[0, 'asc']]
        });
        });
    </script>


@endsection
