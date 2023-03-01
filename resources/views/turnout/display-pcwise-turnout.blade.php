@extends('turnout.layouts.outertheme')
 @section('content')
 <?php 
    //dd($totals);
 ?>
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

 

 



<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   

  </div>
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

              <tr><th colspan="20" class="text-center">{!! $heading_title_with_all !!}</th></tr>
      <tr>
          <th colspan="3" rowspan="2" class="text-center">State</th>
          <th colspan="3" rowspan="2" class="text-center">PC Name</th>
          <th colspan="6" class="text-center">Electors</th>
          <th colspan="4" class="text-center">Total Voter turnout </th>
          <th colspan="4" class="text-center">Total Votes Casted</th>
       </tr>

       <tr>
         <th>General Male</th>
         <th>General Female</th>
         <th>General Other</th>
         <th>General  Electors</th>
         <th>Service Electors</th>
         <th>Total Electors</th>

         <th>Male</th>
         <th>Female</th>
         <th>Other</th>
         <th>Total Voters</th>

         <th>EVM Votes</th>
         <th>Postal Votes</th>
         <th>migrant Votes</th>
         <th>Total  Votes</th>
       </tr>
 
    </thead>
        <tbody>
      @foreach($results as $result)
        <tr>
        <td colspan="3">
          <!-- <a href="<?php echo $result['href'] ?>"> -->
          <span>{!! $result['label'] !!}</span>
       <!--  </a> -->
        </td> 
        <td colspan="3">
          <!-- <a href="<?php echo $result['href'] ?>"> -->
          <span>{!!$result['pc_no']."-".$result['pc_name'] !!}</span>
       <!--  </a> -->
        </td> 

        <td>
        @if($result['electors_male']>0)

        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['electors_male'] }}
       <!--  </a> -->
        @else
        {{$result['electors_male'] }}
        @endif
         </td>
         <td>
        @if($result['electors_female']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['electors_female'] }}
       <!--  </a> -->
        @else
        {{$result['electors_female'] }}
        @endif
         </td>
         <td>
        @if($result['electors_other']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['electors_other'] }}
       <!--  </a> -->
        @else
        {{$result['electors_other'] }}
        @endif
         </td>
         <td>
        @if($result['electors_total']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['electors_total'] }}
       <!--  </a> -->
        @else
        {{$result['electors_total'] }}
        @endif
         </td>
        <td>
        @if($result['electors_service']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['electors_service'] }}
       <!--  </a> -->
        @else
        {{$result['electors_service'] }}
        @endif
         </td>
           <td>
        @if($result['grand_total']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['grand_total'] }}
       <!--  </a> -->
        @else
        {{$result['grand_total'] }}
        @endif
         </td>
         <td>
        @if($result['voter_male']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['voter_male'] }}
       <!--  </a> -->
        @else
        {{$result['voter_male'] }}
        @endif
         </td>

         <td>
        @if($result['voter_female']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['voter_female'] }}
       <!--  </a> -->
        @else
        {{$result['voter_female'] }}
        @endif
         </td>

         <td>
        @if($result['voter_other']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['voter_other'] }}
       <!--  </a> -->
        @else
        {{$result['voter_other'] }}
        @endif
         </td>

         <td>
        @if($result['voter_total']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['voter_total'] }}
       <!--  </a> -->
        @else
        {{$result['voter_total'] }}
        @endif
         </td>

        <td>
        @if($result['evm_votes']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['evm_votes'] }}
       <!--  </a> -->
        @else
        {{$result['evm_votes'] }}
        @endif
         </td>

          <td>
        @if($result['postal_vote']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['postal_vote'] }}
       <!--  </a> -->
        @else
        {{$result['postal_vote'] }}
        @endif
         </td>

          <td>
        @if($result['migrate_votes']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['migrate_votes'] }}
       <!--  </a> -->
        @else
        {{$result['migrate_votes'] }}
        @endif
         </td>

          <td>
        @if($result['total_votes']>0)
        <!-- <a href="<?php echo $result['href'] ?>"> -->
        {{ $result['total_votes'] }}
       <!--  </a> -->
        @else
        {{$result['total_votes'] }}
        @endif
         </td>
 
         
         </tr>
        @endforeach
        <tfoot>
          <?php if(isset($totals)){ ?>
          <tr>
          <td colspan="6">{!! $totals['label'] !!}</td> 
           <td>{{$totals['total_electors_male'] }}</td>
           <td>{{$totals['total_electors_female'] }}</td>
           <td>{{$totals['total_electors_other'] }}</td>
           <td>{{$totals['total_electors_total'] }}</td>
           <td>{{$totals['total_electors_service'] }}</td>
           <td>{{$totals['total_grand_total'] }}</td>


           <td>{{$totals['total_voter_male'] }}</td>
           <td>{{$totals['total_voter_female'] }}</td>
           <td>{{$totals['total_voter_other'] }}</td>
           <td>{{$totals['total_voter_total'] }}</td>

            <td>{{$totals['total_evm_votes'] }}</td>
            <td>{{$totals['total_postal_vote'] }}</td>
            <td>{{$totals['total_migrate_votes'] }}</td>
            <td>{{$totals['total_total_votes'] }}</td>

         </tr>
       <?php } ?>
        </tfoot>
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
  if(jQuery("#phase").val() != ''){
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