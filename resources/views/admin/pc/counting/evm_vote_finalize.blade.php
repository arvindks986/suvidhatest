@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Finalize EVM Rounds')
@section('content')
 <?php  $st=getstatebystatecode($round_details->st_code);  
         if($ele_details->CONST_TYPE=="PC")
           $pc=getpcbypcno($round_details->st_code,$round_details->pc_no); 
         
    ?>


<main role="main" class="inner cover mb-3">

    <section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
               
          <div class="col form-inline"><h6 class="mr-auto">Finalize EVM Rounds Summary</h6><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
            <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp; <b class="bolt">AC Name:</b> 
            <span class="badge badge-info">{{$ac_details->AC_NAME}}</span></p></div>
                </div>
                </div>
                <div class="card-body">  
 
 
  <table class="table table-bordered table-hover datatable" style="width:100%">
        <thead>
    <tr class="sticky-header">
      <th class="">Sr. No</th>
      <th class="sticky-cell cand_name">Candidate Name</th>
      <th class="sticky-cell cand_name">Party</th>      
        @for($k=1; $k<=$round_details->scheduled_round; $k++)
      <th data-breakpoints="xs sm md lg">  Round&nbsp;&nbsp; {{$k}}</th>
        @endfor
      <th class="sticky-cell-opposite">Total Votes</th> 
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
                
                <td class="sticky-cell-opposite">{{$md->total_vote}}   </td></tr>

            @endforeach 
            
             </tbody>
     
    </table>
	</div>
   <div class="card-footer">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('aro/counting/finalize_evm_rounds') }}" >
            {{ csrf_field() }} 
       <input type="hidden" name="new_table" value="{{$new_table}}">
        
                 <?php  $url = URL::to("/");  ?>
              <div class="form-group float-right">  
                
                 <!--<input type="button" value="Resend OTP" onclick="location.href = '{{$url}}/ropc/counting-evm-finalized';" class="btn btn-primary">-->
                 <input type="button" value="Cancel" class="btn btn-primary" onclick="location.href = '{{$url}}/aro/counting/counting-data-entry';">
				  <input type="button" value="Finalize " class="btn btn-success" id="preview_submit">
              </div>
             
      </form>
                </div>
              </div>
  
  
  </div>
  </div>
  </section>
  </main>

@endsection


@section('script')
<script type="text/javascript">
$(document).ready(function(e){
    $('#preview_submit').click(function(e){
    if(confirm("Are you sure you want to Finalize the EVM Vote Count. Upon Finalization Changes can't be done from your end and the same data will be reflected in trends and result Website.")){
      $(this).text('Processing...');
      $(this).prop('disabled',true);
      $("#election_form").submit();
    }else{

    }
  });

});
</script>
@endsection