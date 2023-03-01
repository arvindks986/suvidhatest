@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Rounds Wise Entry')
@section('content')
 <?php  $st=getstatebystatecode($round_details->st_code);  
          if($ele_details->CONST_TYPE=="PC")
           $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO); 
    ?>


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
  <table class="table table-bordered table-hover datatable"  style="width:100%">
        <thead>
		<tr class="sticky-header">
			<th>Sr. No</th>
			<th class="sticky-cell cand_name">Candidate Name</th>
			<th class="sticky-cell cand_name" >Party</th>			
				@for($k=1; $k<=$round_details->scheduled_round; $k++)
			<th data-breakpoints="xs sm md lg">  Round&nbsp;&nbsp; {{$k}}</th>
				@endfor
			<th class="sticky-cell-opposite sorting">Total Votes</th> 
		</tr>
        </thead>
        <tbody>
            <?php $j=0;  ?>
           
            @foreach($master_data as $md)  
              <?php $j++;   
                    
                  
              ?>
            
              <tr><td>{{$j}}</td> <td class="sticky-cell cand_name">{{$md->candidate_name}} <br>{{$md->candidate_hname}}  </td>   
                                  <td class="sticky-cell cand_name">{{$md->party_name}} <br>{{$md->party_hname}} </td>   
			 
                 @for($k=1; $k<=$round_details->scheduled_round; $k++) 
                  <?php $field="round".$k ?>
                  <td>{{$md->$field}}</td>
                @endfor 
                
                <td class="sticky-cell-opposite sorting">{{$md->total_vote}}  </td></tr>

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