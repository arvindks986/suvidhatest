@extends('admin.layouts.pc.dashboard-theme')
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
  .state_row{
    background: #000 !important;
    color: #FFF !important;
  }
  .state_row td a, .state_row td{
    color: #FFF !important;
  }
  </style>
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;position: relative;top: 70px;">
      <div class=" card-header">
      <div class=" row">
           <div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
    <h4>Election Commission Of India, General Elections, 2019
</h4>
   <h4>{!! $heading_title !!}</h4>
  </div>

<div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
      
    </div> 

  </div>


</div>  


</div>

  <div class="row">
   <div class="col-md-5 statistics pt-4 pb-2">  
          
    </div>
    
    @if(isset($filter_buttons) && count($filter_buttons)>0)
      <div class="col-md-5 statistics pt-4 pb-2"> 
              @foreach($filter_buttons as $button)
                  <?php $but = explode(':',$button); ?>
                  <span class="pull-right" style="margin-right: 10px;">
                  <span><b>{!! $but[0] !!}:</b></span>
                  <span class="badge badge-info">{!! $but[1] !!}</span>

                  </span>
                  
              @endforeach
      </div>
      @endif
</div>
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>

              <tr><th colspan="11" class="text-center">{!! $heading_title_with_all !!}</th></tr>
       <tr>
          <th rowspan="2">State /UT </th>
          <th rowspan="2">Seats</th>
          <th rowspan="2">Catagory</th>
          <th colspan="3" class="text-center">No. Of Women</th>
          <th colspan="3" class="text-center">% of Elected Women</th>
        </tr>
        <tr>
         <th colspan="1">Contestants</th>
         <th colspan="1">Elected</th>
         <th colspan="1">Deposits Forfeited</th>

         <th colspan="1">Over Total Women Candidates in the State</th>
         <th colspan="1">Over total seats in State/UT</th>


       </tr>


    </thead>
        <tbody>
        @php  

        $count = 1;

        $TotalPcs             = 0;
        $TotalContested       = 0;
        $TotalElected         = 0;
        $TotalFd              = 0;
        $OvertTotalWomenState = 0;  
        $OvertTotalSeatsState = 0;   
        
        @endphp

        @forelse($results as $result)
       
        @php

        $TotalPcs               +=$result['seats'];

         if($result['is_state']==1){
     
         $TotalContested         +=$result['cont_female'];
         $TotalElected           +=$result['elected_women'];
         $TotalFd                +=$result['fdfemale'];
         $OvertTotalWomenState   +=$result['over_total_women'];
         $OvertTotalSeatsState   +=$result['over_total_seats'];
       

        }

       @endphp

 
         <tr class="<?php if($result['is_state']==1){ ?> state_row <?php } ?>">
          
            <td>{{ $result['st_name'] }} </td>
            <td> @php if($result['seats'] > 0){ @endphp {{ $result['seats'] }} @php } @endphp</td>
            <td>{{ $result['category'] }}</td>
            <td>{{ $result['cont_female'] }}</td>
            <td>{{ $result['elected_women'] }}</td>
            <td>{{ $result['fdfemale'] }}</td>
            <td> {{ $result['over_total_women'] }}</td>
            <td> {{ $result['over_total_seats'] }}</td>
            


          </tr>

     
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Participation of Women Candidates in Poll </td>                 
              </tr>

          @endforelse

          <tr><td><b>Total</b></td><td><b>{{$TotalPcs}}</b></td><td></td><td><b>{{$TotalContested}}</b></td><td><b>{{$TotalElected}}</b></td><td><b>{{$TotalFd}}</b></td><td><b>{{ ROUND($TotalElected/$TotalContested*100,2)}}</b></td><td><b>
		  @if($TotalPcs)
		  {{ ROUND($TotalElected/$TotalPcs*100,2)}}
	      @endif
		  </b></td></tr>

        
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>



<script type="text/javascript">

function filter(){
  var url = "<?php echo $action ?>";
  var query = '';
  if(jQuery("#state").val() != ''){
      query += '&state='+jQuery("#state").val();
    }
  window.location.href = url+'?'+query.substring(1);
}

setTimeout(function(e){
    referesh_page();
},300000);

function referesh_page(){
    location.reload();
}
</script>

@endsection


