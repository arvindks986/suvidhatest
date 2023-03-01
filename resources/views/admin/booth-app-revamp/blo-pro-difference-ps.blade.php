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
 @keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

<div class="loader" style="display:none;"></div>

@include('admin/common/form-filter')

<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-9 pull-left">
       <h4>{!! $heading_title !!}</h4>
     </div>

     <div class="col-md-3 pull-right text-right">

      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach

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
<div class="alert <?php echo $class; ?> fade in">
  <a href="#" class="close" data-dismiss="alert">&times;</a>
  {{ Session::get('flash-message') }}
</div>
@endif


<div class="container-fluid">
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
       <div class="random-area">


         <div class="table-responsive">
          <table class="table table-bordered " id="my-list-table" data-page-length='50'>
           <thead>
            <tr>
               <th>PS No. & Name</th>
              <th>State Name</th>
              <th>AC No. & Name</th>
              
              <!--<th>BLO Name & Mobile</th>
              <th>PO Name & Mobile</th>
              <th>PRO Name & Mobile</th>-->
              <th>Total BLO Scan</th>
              <th>Total PO Scan</th>
              <th>Total Difference</th>
            </tr>
          </thead>
          <tbody>  
            @php
            $bloscan = '0';
            $poscan = '0';
            $difference = '0';
            @endphp

            @if(count($voter_turnouts)>0)
            @foreach($voter_turnouts as $result)
            <tr>
			<td>{{$result['ps_no']}}-{{$result['ps_name']}}</td>
             <td>{{$result['st_name']}}</td>
             <td>{{$result['ac_no']}}-{{$result['ac_name']}}</td>
             
             <!--<td>{{$result['blo_name']}} @if($result['blo_mobile']) - {{$result['blo_mobile']}} @endif</td>
             <td>{{$result['po_name']}} @if($result['po_mobile']) - {{$result['po_mobile']}} @endif</td>
             <td>{{$result['pro_name']}} @if($result['pro_mobile']) - {{$result['pro_mobile']}} @endif</td>-->
             <td>{{$result['blo_turnout']}}</td>
             <td>{{$result['pro_turnout']}}</td>
             <td>{{$result['difference']}}</td>
           </tr>

           @php
           $bloscan +=   $result['blo_turnout'];
           $poscan +=   $result['pro_turnout'];
           $difference  +=   $result['difference'];

           @endphp

           @endforeach
           @else
           <tr>
            <td colspan="6">
              No Record Found.
            </td>
          </tr>
          @endif
        </tbody>

        <tr>
          <td style="text-align: center;"><b>Grand Total</b></td>
          <td><b></b></td>
          <td><b></b></td>
          <!--<td><b></b></td>
          <td><b></b></td>
          <td><b></b></td>-->
          <td><b>{{$bloscan}}</b></td>
          <td><b>{{$poscan}}</b></td>
          <td><b>{{$difference}}</b></td>

        </tr>

      </table>
    </div><!-- End Of  table responsive -->  
  </div><!-- End Of intra-table Div -->   


</div><!-- End Of random-area Div -->

</div><!-- End OF page-contant Div -->
</div>      
</div><!-- End Of parent-wrap Div -->
</div> 


@endsection

@section('script')
@if(isset($add_download_log))
<script type="text/javascript">
  function download_booth_slip(id){
    $.ajax({
      url: "{!! $add_download_log !!}",
      type: 'POST',
      data: '_token=<?php echo csrf_token() ?>&ps_no='+id,
      dataType: 'json', 
      beforeSend: function() {

      },  
      complete: function() {
      },        
      success: function(json) {         
      },
      error: function(data) {
        var errors = data.responseJSON;
      }
    });
  }
</script>
@endif

<script type="text/javascript">
	if($('#my-list-table').length>0){
      $('#my-list-table').DataTable({
        "pageLength": 50,
        "aaSorting": []
      });
    }

</script>
@endsection