@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Rounds Wise Entry')
@section('content')
<style type="text/css">
 
hr {
    margin-top: 0;
    margin-bottom: 0;   
}
</style>
 <?php  $st=getstatebystatecode($ele_details->ST_CODE);  
          if($ele_details->CONST_TYPE=="PC")
           $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);
          
           if($dis_ac!="All" and  $dis_ac!=""){
              $ac=getacbyacno($ele_details->ST_CODE,$dis_ac);
              $ac_name=$ac->AC_NAME; 
           }
           else{
              $ac_name="ALL"; 
           }

    ?>
 <section>

  <div class="row">

		  @if (session('success_mes'))
                  <div class="alert alert-success"> {{session('success_mes') }}</div>
              @endif
              @if (session('error_mes'))
                  <div class="alert alert-danger"> {{session('error_mes') }}</div>
              @endif
           
   
  </div>
    
  @if(isset($winn_data))

<div class="form-style">
<div class="container-fluid">
<div class="row">
	
			<table class="table table-bordered table-result" style="width:100%">              
                @if(isset($winn_data))
               <tbody>
		   <tr>
			<td colspan="3" class="td-green"><h5>@if(isset($winn_data))@if($winn_data->status==0) Leading @else Winning @endif @endif Candidates</h5></td>
			<td colspan="3" class="td-ornage"><h5> Trailing  Candidate</h5></td>
			<td>Margin</td>
			
		   </tr>
		   
		   <tr>
				<td class="td-green-light"><b>Candidate</b>@if(isset($winn_data)){{$winn_data->lead_cand_name}}@endif</td>  
                <td class="td-green-light"><b>Party</b>@if(isset($winn_data)){{$winn_data->lead_cand_party}}@endif</td>  
				<td class="td-green-light"><b>Candidate Votes</b>@if(isset($winn_data)){{$winn_data->lead_total_vote}}@endif</td>
                <td class="td-ornage-light"><b>Candidate</b>@if(isset($winn_data)){{$winn_data->trail_cand_name}}@endif</td>  
                <td class="td-ornage-light"><b>Party</b>@if(isset($winn_data)){{$winn_data->trail_cand_party}}@endif</td>                   
                <td class="td-ornage-light"><b>Candidate Votes</b>@if(isset($winn_data)){{$winn_data->trail_total_vote}}@endif</td>  
                <td><h3>@if(isset($winn_data)){{$winn_data->margin}}@endif</h3> 
                    @if($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                          <b> (Tie)  </b>
                                @endif
                    @if($winn_data->status==1) <b>Won </b>  @endif

                </td></tr>   
               </tbody>
               @endif
          </table>
   
	
  </div>
  </div>
 </div> 

  <div class="container-fluid mt-3">
  <div class="row">   
  <div class="card text-left" style="width:100%; margin:0 auto;">
 
		@if(isset($winn_data))	@if($winn_data->status==1) <p>Results Decleared</p>@endif @endif
			
 <hr />
                <div class=" card-header">
                <div class=" row">
				<div class="col-sm-4">
                 <form name="frmstatus" id="frmstatus" method="get"  action="return false;" >
          <label for="">Select Particular AC to edit Round Vote Entry</label>
          <select name="dis_ac" id="dis_ac" onchange="filter();" class="form-control" autofocus  style="font-size: 16px;  height: 40px;   font-weight: 500;">
                <option value="" selected >All</option>
                @if(isset($list_allac))
                @foreach($list_allac as $ac)   
                <option value="{{$ac->AC_NO}}" @if($dis_ac == $ac->AC_NO) selected="selected" @endif >{{$ac->AC_NO}} - {{$ac->AC_NAME}}</option>
                
                @endforeach @endif
          </select>
		  </form>
		  </div>
          <div class="col-sm-8 form-inline"><!-- <h6 class="mr-auto">Rounds Wise Entry Reports</h6> --><p class="mb-0 ml-auto"><b class="bolt">State Name:</b> 
            <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  <b class="bolt">AC Name:</b> 
            <span class="badge badge-info"> {{$ac_name}}</span></p></div>
                </div>
                </div>
               




				
 <div class="sticky-table sticky-ltr-cells">
    @if(!$result->isEmpty())
  <table class="table table-bordered table-hover table-dot" style="width:100%; border:0px;">
        <thead> 
		<tr class="sticky-header">
			<th class="sticky-cell">Sr. No</th>
			<th class="sticky-cell cand_name" data-breakpoints="xs sm">Candidate Name</th>
			<th class="sticky-cell cand_name">Party</th>	

      @if(isset($rounds))		
				@for($k=1; $k<=$rounds['max_round']; $k++)
			<th data-breakpoints="xs sm md lg">  Round-{{$k}}
		 @if($winn_data->status==0)
	<?php if((int)$dis_ac > 0) {        $field="round".$k;   ?>
     
		<a href="{!! url('/ropc/counting-details/edit/'.base64_encode($dis_ac).'/'.base64_encode($k)) !!}" class="badge badge-warning float-right" style="color: #FFF;">Edit <i style="" class="fa fa-angle-right"></i></a>
      
		 <?php } ?>
      @endif 
		
		</th>
				@endfor
        @endif
			<th class="sticky-cell-opposite">Total Votes</th> 
		</tr>
        </thead>
        <tbody>
            <?php $j=0;    ?>
           
            @foreach($result as $md)   
              <?php $j++;    
                    
                  
              ?>
            
              <tr><td class="sticky-cell">{{$j}}</td> 
			  <td class="sticky-cell cand_name">{{$md->candidate_name}} <br>{{$md->candidate_hname}} 
           @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> (Leading) </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>(Won)</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>(Trailing) </b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                        
                                @endif 
           </td>   
                                  <td class="sticky-cell cand_name">{{$md->party_name}} <br>{{$md->party_hname}} </td>   
			           @if(isset($rounds))   
                 @for($k=1; $k<=$rounds['max_round']; $k++) 
                  <?php $field="round".$k ?>
                  <td>{{$md->$field}}</td>
                @endfor 
                @endif
                <td class="sticky-cell-opposite">{{$md->total_evm_vote}}
                   @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> (Leading) </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>(Won)</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>(Trailing) </b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                        
                                @endif 

                </td></tr>

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
 
@endif


@endsection
@section('script')
<script type="text/javascript">
           jQuery(document).ready(function(){
          //By Dropdown 
          jQuery("select[name='dis_ac']").change(function(){
            var dis_ac = jQuery(this).val();
             
            jQuery.ajax({
                    url: "{{url('/ac-wise-counting')}}",
                    type: 'POST',
                    data: {dis_ac:dis_ac},
                    success: function(result){
              }
            });
          });
          
           
          
        });
  
  
    
</script>


<script type="text/javascript">

function filter(){
    var url = "<?php echo url('ropc/counting-details') ?>";
    var query = '';

    if(jQuery("#dis_ac").val() != ''){
      query += '&dis_ac='+jQuery("#dis_ac").val();
    }

    window.location.href = url+'?'+query.substring(1);
}
</script>

@if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif


 
@endsection