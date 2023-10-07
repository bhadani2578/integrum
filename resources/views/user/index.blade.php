@extends('layouts.app')
@section('title') {{'User Management'}} @endsection
@section('content')
<div class="content-body content content-components tracking-page-setup">
    <div class="container">

        @if(auth()->user()->permission == 1)        
            <div class="d-sm-flex align-items-center justify-content-end mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <div class="top-right-buttons">
                    <a href="{{route('user.create')}}" class="btn btn-success">
                        <img src="{{asset('assets/img/plus.png')}}">
                        <span>Create New</span>
                    </a>
                </div>
            </div>      
        @endif
        <div data-label="boxon" class="boxon-site-table mg-b-25 mg-t-40">
            <table id="boxontable" class="table user-management-setup">
                <thead>
                    <tr>
                        <th class="wd-15p">Names</th>
                        <th class="wd-20p">Email</th>
                        <th class="wd-20p">Phone</th>
                        <th class="wd-10p">Position</th>
                        <th class="wd-20p">Login details</th>
                        <th class="wd-15p">Permissions</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($user) && count($user) > 0)
                    @foreach($user as $key => $item)
                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{{$item->email}}</td>
                        <td>{{$item->phone}}</td>
                        <td>{{$item->position}}</td>
                        <td>{{$item->email}}</td>
                        <td> 
                            <select name="Permissions" id="Permissions">
                                <option @if($item->permission == 0) selected @endif value="2">Read</option>
                                <option @if($item->permission == 1) selected @endif value="1">Write</option>
                            </select>
                        </td>
                        <td><a href="javascript:void(0)" class="open-more-options">...</a>
                            <div class="access-more-option">
                                <a href="{{route('user.show', $item->id)}}">View</a>
                                <a href="{{route('user.edit', $item->id)}}">Edit</a>
                                <form method="POST" action="{{ route('user.destroy', $item->id) }}">
                                        @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                <!-- <a href="{{ route('user.destroy', $item->id) }}">Delete</a> -->
                                <button type="submit" title='Delete'>Delete</button>
                                </form>
                            </div>
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

<script src="lib/jquery/jquery.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="lib/feather-icons/feather.min.js"></script>
    <script src="lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="lib/prismjs/prism.js"></script>
    <script src="lib/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="lib/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
    <script src="lib/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>

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
    <script>
      $(function(){
        'use strict'

        $('#boxontable').DataTable({
          language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ items/page',
          }
        });
      
        /*open more option*/
       /* $('.access-more-option').hide();
        $('a.open-more-options').click(function(){
          $(this).next('.access-more-option').toggle();
        })*/
    //      $('a.open-more-options').click(function(){
    //       $(this).next('.access-more-option').toggleClass('active');
    //    });
         
      });
      $('.open-more-options').click(function(event) {
      event.stopPropagation(); // Prevent the event from bubbling up to the document
      $(this).next('.access-more-option').toggleClass('active');
    });

    $(document).click(function(event) {
      var dropdown = $('.access-more-option');
      if (dropdown.is(':visible') && !dropdown.is(event.target) && dropdown.has(event.target).length === 0) {
        dropdown.removeClass('active');
      }
    });
    </script>
@endsection
