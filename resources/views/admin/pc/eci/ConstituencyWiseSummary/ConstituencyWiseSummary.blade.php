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
    <h4>Election Commission Of India, General Elections, 2019</h4>
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
         <tr>
         
          <th>PC No</th>
          <th>PC Name</th> 
          <th>No Of AC Segments</th> 
          <th>No Of Polling Station</th> 
          <th>Electors</th> 
          <th>Avg. No. of Electors Per PS</th> 
          <th>Nominations</th> 
          <th>Contestants</th> 
          <th>Forefeited Deposits</th> 
          <th>Voters</th> 
          <th>Voters Turn Out (%)</th>          
        </tr>
        </thead>
        <tbody>
        @php  

        $count = 1;

        $TotalAc          = 0;
        $TotalPs          = 0;
        $TotalElector     = 0;
        $TotalAvgElector  = 0;
        $TotalNominated   = 0;
        $TotalContested   = 0;
        $TotalForefeited  = 0;
        $TotalVoter       = 0;
       

         @endphp

        @forelse($results as $result)

         @php
         if($result['is_state']==1){

         $TotalAc          +=$result['total_const'];
         $TotalPs          +=$result['total_ps'];
         $TotalElector     +=$result['total_electors'];
         $TotalAvgElector  +=$result['avg_elector_in_ps'];
         $TotalNominated   +=$result['nominated'];
         $TotalContested   +=$result['contested'];
         $TotalForefeited  +=$result['forefeited'];
         $TotalVoter       +=$result['total_voter'];

        }

         @endphp

     
         @if($result['is_state'] == 0 && empty($result['constno']))
          <tr class="">
          
            <td colspan="11"><b>{{ $result['st_name'] }}</b></td> 
          </tr>
          @else
          <tr class="<?php if($result['is_state']==1){ ?> state_row <?php } ?>">
             

          @if($result['is_state'] == 1)
            <td style="border-right:none;border-left:none;"><b>{{ $result['constno'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['const_name'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['total_const'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['total_ps'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['total_electors'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['avg_elector_in_ps'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['nominated'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['contested']  }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['forefeited'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['total_voter'] }}  </b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['voterturnout'] }}</b></td>
           @else
           <td>{{ $result['constno'] }}</td>
            <td>{{ $result['const_name'] }}  </td>
            <td>{{ $result['total_const'] }}  </td>
            <td>{{ $result['total_ps'] }}  </td>
            <td>{{ $result['total_electors'] }}  </td>
            <td>{{ $result['avg_elector_in_ps'] }}</td>
            <td>{{ $result['nominated'] }}</td>
            <td>{{ $result['contested']  }}</td>
            <td>{{ $result['forefeited'] }}</td>
            <td>{{ $result['total_voter'] }}  </td>
            <td>{{ $result['voterturnout'] }}</td>
           @endif
           
          
          </tr>
          @endif
     
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Constituency (PC) Wise Summary</td>                 
              </tr>
          @endforelse

          @php if($user_data->role_id == '7' || $user_data->role_id =='27'){  @endphp
           <tr><td><b>Grand Total</b></td><td></td><td><b>{{$TotalAc}}</b></td><td><b>{{$TotalPs}}</b></td><td><b>{{$TotalElector}}</b></td><td><b>{{round($TotalElector/$TotalPs,0)}}</b></td><td><b>{{$TotalNominated}}</b></td><td><b>{{$TotalContested}}</b></td><td><b>{{$TotalContested}}</b></td><td><b>{{$TotalVoter}}</b></td><td><b>{{ ROUND($TotalVoter/$TotalElector*100,2)}}</b></td></tr>

           @php } @endphp
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


