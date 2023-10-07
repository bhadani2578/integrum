@extends('layouts.app')
@section('content')
<div class="content-body content content-components tracking-page-setup">
    <div class="container">
        <div class="wrap_topbar">
            <span><b> My Client </b></span>
            <div class="d-sm-flex align-items-center justify-content-end mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <div class="top-right-buttons">
                    <a href="{{route('client.create')}}" class="btn btn-success">
                        <img src="{{asset('assets/img/plus.png')}}">
                        <span>Create New Client</span>
                    </a>
                </div>
            </div>
        </div>

        <div data-label="boxon" class="boxon-site-table mg-b-25 mg-t-40 full-width-bar">
            <table id="boxontable" class="table user-management-setup table-row-withoutbg">
                <thead>
                    <tr>
                        <th class="wd-20p">Client Id</th>
                        <th class="wd-15p">Client Name</th>
                        <th class="wd-15p">Parent Group</th>
                        <th class="wd-20p">Email</th>
                        <th class="wd-15p">Phone</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($client_list) && count($client_list) > 0)
                    @foreach($client_list as $key => $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->client_name}}</td>
                        <td>{{$item->parent_group}}</td>
                        <td>{{$item->email}}</td>
                        <td>{{$item->phone}}</td>
                        <td>
                            <a href="{{ route('dashboard', ['id' => base64_encode($item->id)]) }}"  title="View"><i class="fa fa-eye"></i></a>
                            {{-- <a href="{{ route('client.destroy', $item->id) }}" title="Delete" onclick="event.preventDefault(); if (confirm('Are you sure you want to delete this item?')) document.getElementById('delete-form-{{ $item->id }}').submit();"><i class="fa fa-trash"></i></a>
                            <form id="delete-form-{{ $item->id }}" action="{{ route('client.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form> --}}
                            <a href="#" title="Delete" onclick="openDeletePopup('{{ route('client.destroy', $item->id) }}')"><i class="fa fa-trash"></i></a>
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
      $(function(){
        'use strict'

        $('#boxontable').DataTable({
          language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ items/page',
          },
          order: [[0, 'desc']],
        });
      });
    </script>


@endsection
