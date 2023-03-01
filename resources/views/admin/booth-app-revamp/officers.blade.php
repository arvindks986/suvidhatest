@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
 }
 .list-inline li{
  display: inline;
}
td {
    height: 125px !important;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
table td{position:relative;}
p.btn.btn-table {
    position: absolute;
    bottom: 0;
    width: 100%;
    text-align: center;
    left: 0;
    background: #17a2b8;
    border-radius: 0;
}
p.btn.btn-add { position: absolute;
    bottom: 0;
    width: 100%;
    /* text-align: center; */
    left: 0;
    background: #c0c0c0;
    border-radius: 0;
    /* height: 100%; */
    display: flex;
    height: 125px; padding:0;}
p.btn.btn-add a {
 padding: 50px 0 0 0;
    display: block;
    width: 100%;
}
p.btn.btn-table a, p.btn.btn-add a { color: #fff; font-size:14px;}
p.btn.btn-table:hover{background:#49a8a4;}
p.btn.btn-add:hover{background:#49a8a4;}
.second-td{
  max-width: 150px;
}
</style>

@include('admin/common/form-filter')

<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-9 pull-left">
       <h4>{!! $heading_title !!}</h4>
     </div>

     <div class="col-md-3  pull-right  text-right">
      @if(count($results)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
      @endif    
    </div>
  </div>
</div>  
</section>


@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
        <?php $but = explode(':',$button); ?>
        <span class="pull-right" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach
      </div>
    </div>
  </div>
</section>
@endif





<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
      @if(Session::has('flash-message'))
@if(Session::has('status'))
<?php
$status = Session::get('status');
if($status==1){
 $class = 'alert-success';
}
else{
  $class = 'alert-danger';
}
?>
@endif
<div class="alert <?php echo $class; ?> in">
  <a href="#" class="close" data-dismiss="alert">&times;</a>
  {{ Session::get('flash-message') }}
</div>
@endif
     <div class="page-contant">
       <div class="random-area">
        <br>

        <div class="table-responsive">
          <table class="table table-bordered list-table-remove" id="my-list-table"> 
           <thead>
            <tr>
            <th>AC No & Name</th> 
              <th>PS No & Name</th>

              <th>Name</th>
              <th>Mobile</th>
              <th>Designation</th>
              <th>Activated</th>
              <th>Role</th>
            </tr>

          </thead>
          @if(count($results)>0)
          <tbody id="oneTimetab">   
            @foreach($results as $result)
            <tr>
              <td>{{$result['ac_no']}} - {{$result['ac_name']}}</td>
              <td>{{$result['ps_no']}} @if($result['ps_name']) - {{$result['ps_name']}} @endif</td>
              <td>{{$result['name']}}</td>
              <td>{{$result['mobile']}}</td>
              <td>{{$result['designation']}}</td>
             <td> @if($result['is_login'])
                Yes
                @else
                No
                @endif
              </td>
              <td>{{$result['designation']}}</td>
            </tr>
            @endforeach
          </tbody>

          @else
          <tbody>
            <tr>
              <td colspan="7">
                @if(isset($no_record))
                {{$no_record}}
                @else
                No Record Found
                @endif
              </td>
            </tr>
          </tbody>
          @endif
        </table>
      </div><!-- End Of  table responsive -->  
    </div><!-- End Of intra-table Div -->   


  </div><!-- End Of random-area Div -->

</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
</div> 


@endsection
@section("script")
<script type="text/javascript">
  $(document).ready(function () {
    if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 50,
        "aaSorting": []
      });
    }
  });
</script>
@endsection