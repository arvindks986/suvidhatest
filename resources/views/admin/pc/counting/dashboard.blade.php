@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Counting Dashboard')
@section('content')
 <?php  $st=getstatebystatecode($ele_details->ST_CODE);  
         if($ele_details->CONST_TYPE=="PC")
           $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO); 
         
    ?>
	
	<?php /*
 <section class="statistics">
        <div class="container mt-5 mb-5">
          <div class="row d-flex">
            <div class="col-lg-4 pl-0">
              <!-- Income-->
              <div class="card income">
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div><b class="mr-auto"><a class="text-info" href="{{url('/aro/counting/round-schedule')}}">Rounds Schedule <i class="fa fa-arrow-right float-right" aria-hidden="true"></i></a></b> </div>
              </div>
            </div>
          <div class="col-lg-4 ">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b class="mr-auto"><a class="text-warning" rel="" href="{{url('/aro/counting/counting-data-entry')}}">EVM Votes Data Entry <i class="fa fa-arrow-right float-right" aria-hidden="true"></i></a></b>
                   </div>
              </div>
            </div>
          <div class="col-lg-4">
              <!-- Income-->
              <div class="card income">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class=""> <b class="mr-auto"> <a class="text-success" rel="" href="{{url('/aro/counting/round-wise-entry')}}" >Rounds Wise Entry Details  <i class="fa fa-arrow-right float-right" aria-hidden="true"></i></a></b></div>   
              </div>
            </div>
       
          </div>
        </div>
      </section>*/?>

    <section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 
          <div class="col form-inline"><h6 class="mr-auto">Rounds Wise Entry Reports</h6><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
            <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac_details->AC_NAME}}</span></p></div>
                </div>
                </div>
                <div class="card-body">  
   

    @if(!$master_data->isEmpty())
  <table class="table table-bordered table-hover datatable" style="width:100%">
        <thead>
		<tr class="sticky-header">
			<th class="">Sr. No</th>
			<th class="sticky-cell cand_name" data-breakpoints="xs sm">Candidate Name</th>
			<th class="sticky-cell cand_name" >Party</th>
      @if(isset($round_details))		
				@for($k=1; $k<=$round_details->scheduled_round; $k++)
			<th style="text-align:center;" data-breakpoints="xs sm md lg">Round&nbsp; {{$k}}</th>
				@endfor
        @endif
			<th style="text-align:center; color:#fff;" class="sticky-cell-opposite last_tab">Total Votes</th> 
		</tr>
        </thead>
        <tbody>
            <?php $j=0;  ?>
              
            @foreach($master_data as $md)  
              <?php $j++;   
                    
                  
              ?>
            
              <tr><td class="">{{$j}}</td><td class="sticky-cell cand_name">{{$md->candidate_name}} <br>{{$md->candidate_hname}}   </td>   
                                  <td class="sticky-cell cand_name">{{$md->party_name}} <br>{{$md->party_hname}} </td> 
			           @if(isset($round_details))  
                 @for($k=1; $k<=$round_details->scheduled_round; $k++) 
                  <?php $field="round".$k ?>
                  <td style="text-align:center;">{{$md->$field}}</td>
                @endfor 
                @endif
                <td class="sticky-cell-opposite last_tab">{{$md->total_vote}}  </td></tr>

            @endforeach 
            
             </tbody>
     
    </table>
    @else
               <p> Counting Data Not exit! Ro is not activate  for counting</p>
            @endif 
     <!-- end reponcive-->
  
                </div>
              </div>
  
  
  </div>
  </div>
  </section>
 


@endsection