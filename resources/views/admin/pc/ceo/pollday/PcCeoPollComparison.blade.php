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


  </style>

  <div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">

<div class="row text-center">
<div class="col-lg-12">
<button type="button" onclick="referesh_page()" class="btn btn-primary pull-right" style="font-size: 30px; padding: 15px 10px;">Refresh Page</button>
</div>
</div>

<div class="row text-center mb-3">
   <div class="col">
   <span class="">
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$number_of_voting}}%</span>
   <br />
                 <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">Voter Turn Out</span></span>
  </div></div>




<div class="container-fluid">
  <div class="row">
  <div class="col-md-6 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-6  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
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

<section class="dashboard-header section-padding">
  <div class="container-fluid">
  
        
    <form id="generate_report_id"  class="row" method="get" onsubmit="return false;">
 

    
        
          <?php if(isset($phases) && count($phases)>0){ ?>
         <div class="form-group col-md-3"> <label>Phases </label>

           <select name="phase" id="phase" class="form-control" onchange ="filter()">
           @foreach($phases as $result)
             @if($phase==$result->SCHEDULEID)
               <option value="{{$result->SCHEDULEID}}" selected="selected" >Phase-{{$result->SCHEDULEID}}</option>
             @else
               <option value="{{$result->SCHEDULEID}}" >Phase-{{$result->SCHEDULEID}}</option>
             @endif
           @endforeach

           </select>
         </div>
       <?php }else{ ?>
        <input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
       <?php } ?>
          

          <?php /*
          <div class="form-group col-md-3"> <label>Constituency </label> 
          
            <select name="pc_no" id="pc_no" class="form-control" onchange ="filter()">
              <option value="">Select Constituency</option>
            @if(count($consituencies)>0)
            @foreach($consituencies as $result)
              @if($pc_no == $result['pc_no'])
                <option value="{{ $result['pc_no'] }}" selected="selected" >{{ $result['pc_name'] }}</option> 
              @else 
                <option value="{{ $result['pc_no'] }}" >{{ $result['pc_name'] }}</option> 
              @endif   
            @endforeach
            @endif  
            </select>
          </div>
          */?>



        </form>   
  
    
  </div>
</section>



<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>

    

           <div class="table-responsive">
      
            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%"><thead>
       <tr><th colspan="12" class="text-center">{!! $heading_title !!}</th></tr>
       <tr>
        <th colspan="3"> State </th>
        <th> PC No </th>
        <th> PC Name </th>
        <th colspan="1">Round1 %<br>(Poll Start to 09:00 AM)</th>
        <th colspan="1">Round2 %<br>(Poll Start to 11:00 AM)</th>
        <th colspan="1">Round3 %<br>(Poll Start to 1:00 PM)</th>
        <th colspan="1">Round4 %<br>(Poll Start to 3:00 PM)</th>
        <th colspan="1">Round5 %<br>(Poll Start to 5:00 PM)</th>
         <th colspan="1">Close of Poll %</th>
        <th colspan="1">Latest Updated %</th>
        
       </tr>


    </thead>
        <tbody>
      @foreach($results as $result)
        <tr>
        <td colspan="3">
          <a href="<?php echo $result['href'] ?>">
          <span>{!! $result['label'] !!}</span>
        </a>
        </td> 

        <td>
        <a href="<?php echo $result['href'] ?>">
        {{$result['pc_no'] }}</a>
         </td>

         <td>
        <a href="<?php echo $result['href'] ?>">
        {{$result['pc_name'] }}
    </a>
         </td>

        <td>
        @if($result['est_total_round1']>0)
        <a href="<?php echo $result['href'] ?>">
        {{ $result['est_total_round1'] }}
        </a>
        @else
        {{$result['est_total_round1'] }}
        @endif
         </td>
         <td>
        @if($result['est_total_round2']>0)
        <a href="<?php echo $result['href'] ?>">
        {{ $result['est_total_round2'] }}
        </a>
        @else
        {{$result['est_total_round2'] }}
        @endif
         </td>
         <td>
        @if($result['est_total_round3']>0)
        <a href="<?php echo $result['href'] ?>">
        {{ $result['est_total_round3'] }}
        </a>
        @else
        {{$result['est_total_round3'] }}
        @endif
         </td>
         <td>
        @if($result['est_total_round4']>0)
        <a href="<?php echo $result['href'] ?>">
        {{ $result['est_total_round4'] }}
        </a>
        @else
        {{$result['est_total_round4'] }}
        @endif
         </td>

         <td>
        @if($result['est_total_round5']>0)
        <a href="<?php echo $result['href'] ?>">
        {{ $result['est_total_round5'] }}
        </a>
        @else
        {{$result['est_total_round5'] }}
        @endif
         </td>


         <td>
        @if($result['close_of_poll']>0)
        <a href="<?php echo $result['href'] ?>">
        {{ $result['close_of_poll'] }}
        </a>
        @else
        {{$result['close_of_poll'] }}
        @endif
         </td>

         <td>
        @if($result['total_percentage']>0)
        <a href="<?php echo $result['href'] ?>">
        {{ $result['total_percentage'] }}
        </a>
        @else
        {{$result['total_percentage'] }}
        @endif
         </td>
 
         </tr>
        @endforeach
        <?php if(isset($totals)){ ?>
        <tfoot>
         <tr>
        <td colspan="3"><span>{!! $totals['label'] !!}</span></td> 
        <td></td>  
        <td></td>  
        <td>{!! $totals['est_total_round1'] !!} </td>         
        <td>{!! $totals['est_total_round2'] !!} </td>       
        <td>{!! $totals['est_total_round3'] !!} </td> 
        <td>{!! $totals['est_total_round4'] !!} </td> 
        <td>{!! $totals['est_total_round5'] !!} </td>    
        <td>{!! $totals['close_of_poll'] !!} </td>        
        <td>{!! $totals['total_percentage'] !!} </td> 
         </tr>
        </tfoot>
      <?php } ?>
       </tbody></table>

         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 


<script type="text/javascript">

function filter(){
    var url = "<?php echo $action ?>";
    var query = '';
    if(jQuery("#phase").val() != '' && jQuery("#phase").val() != 'undefined'){
      query += '&phase='+jQuery("#phase").val();
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