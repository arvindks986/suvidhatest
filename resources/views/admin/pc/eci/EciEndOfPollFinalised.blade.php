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

  <div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">

<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-5  pull-right text-right">

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
  
        
    <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
 

    
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
      
            <table id="acViewBody" class="table table-striped table-bordered" style="width:100%"><thead>

              <tr><th colspan="11" class="text-center">{!! $heading_title_with_all !!}</th></tr>
       <tr>
          <th>State</th>
          <th> Total PCs </th>
          <th>Pc Finalised </th>
       </tr>
    </thead>
        <tbody>

      <?php 

      $index = 0;
      $TotalPc = 0;
      $TotalFinalisePc = 0; 

      ?>
      @foreach($results as $result)

       @php 

         $TotalPc += $result['total_pc'];

         $TotalFinalisePc += $result['pc_finalised'];

        @endphp
    
          <tr>
          <td><span>{!! $result['label'] !!}</span></td> 
          <td>{{$result['total_pc']  }}</td>
          <td>{{$result['pc_finalised'] }}</td>
          </tr>

      <?php $index++; ?>

      @endforeach
      
       <tr style="background: #f0587e;color: #fff;"><td><b>Total</b></td><td><b>{{ $TotalPc }}</b></td><td><b>{{ $TotalFinalisePc }}</b></td></tr>
        
       </tbody></table>

       <script type="text/javascript"> 
        console.log(<?php echo $index ?>);
       </script>

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
  if(jQuery("#phase").val() != ''){
      query += '&phase='+jQuery("#phase").val();
    }
  window.location.href = url+'?'+query.substring(1);
}

setTimeout(function(e){
    referesh_page();
},300000);

</script>
@endsection