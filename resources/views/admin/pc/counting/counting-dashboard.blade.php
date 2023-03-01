@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Counting')
@section('content')
 <?php  $st=getstatebystatecode($ele_details->ST_CODE);  
          if($ele_details->CONST_TYPE=="PC")
           $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO); 
    ?>
 @if(Session::has('success_admin'))
          <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
       @endif  
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
     @if(isset($winn_data))
   <section class="mt-3">
  <div class="container-fluid">
  <div class="row">
      @if($winn_data->status==1) <label for="">Results Declared</label>@endif
      
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 
          <div class="col form-inline"><h6 class="mr-auto">Candidate  Wise Votes Details</h6><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
            <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;   </p></div>
                </div>
                </div>
                <div class="card-body">  
 <div class="sticky-table sticky-ltr-cells">
    @if(!$result->isEmpty())
  <table class="table table-bordered table-hover table-dot" style="width:100%">
        <thead>
		<tr class="sticky-header">
			<th>Sr. No</th>
			<th data-breakpoints="xs sm" class="cand_name">Candidate Name </th>
			<th class="cand_name">Party</th><th>EVM Votes</th><th>Postal Votes</th> @if($user_data->st_code=="S09")<th>Migrant Votes</th>@endif<th>Total Votes</th> 
		</tr>
        </thead>
        <tbody>
            <?php $j=0; $rej_votes=0; $total_postal_vote=0; ?>
           
            @foreach($result as $md)  
              <?php $j++;     $mes='';
                                
                                 
                  
              ?>
            
              <tr><td>{{$j}}</td> <td class="cand_name">{{$md->candidate_name}} <br>{{$md->candidate_hname}} <b>{{$mes}}</b>
                                  @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> (Leading) </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>(Won)</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>(Trailing) </b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                        
                                @endif 

                                </td>   
                                  <td class="cand_name">{{$md->party_name}} <br>{{$md->party_hname}} </td>   
			                            <td>{{$md->evm_vote}}  </td>
                                  <td>{{$md->postal_vote}}  </td>
                                   @if($user_data->st_code=="S09")
                                   <td>{{$md->migrate_votes}}  </td>
                                   @endif
                                  <td>{{$md->total_vote}}  
                                   @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> (Leading) </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>(Won)</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>(Trailing) </b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                        
                                @endif 
                              </td></tr>
                          <?php  $rej_votes=$md->rejectedvote; 
                                $total_postal_vote=$md->postaltotalvote; ?>
            @endforeach 
                            <tr><td colspan="2">&nbsp;</td> 
                            <td colspan="2"><b> Rejected Votes</b> </td>   
                            <td>{{$rej_votes}}  </td><td>&nbsp;</td></tr>
                            <tr><td colspan="2">&nbsp;</td> 
                            <td colspan="2"><b> Postal Total Votes</b> </td>   
                            <td>{{$total_postal_vote}}  </td><td>&nbsp;</td></tr>
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
  </div>
  </section>
 @endif


@endsection
 