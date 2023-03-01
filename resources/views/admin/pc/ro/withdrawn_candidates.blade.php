@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Withdrawn of Candidate')
@section('content') 
<?php 
  $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
  $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();
   ?>

   <style type="text/css">
     
.col-xl-4 {
  -ms-flex: 0 0 33.333333%;
  flex: 0 0 50%;
  max-width: 50%;
}
.text-warning{color: #4CAF50 !important;}


   </style>
	 
 <section class="statistics color-grey pt-5 pb-5" style="border-bottom:1px solid #eee;">
        <div class="container-fluid">
          <div class="row d-flex">
            <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
                <div class="number yellow">{{$total}}</div><p>Applications<strong class="text-primary">Applied</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                  <div class="icon"><img src="{{ asset('admintheme/img/icon/verified.png') }}" alt="" /></div>
                <div class="number green">{{$totaccepted}}</div><p>Applications<strong class="text-primary">Accepted </strong></p>
               
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('admintheme/img/icon/generate.png') }}" alt="" /></div>
                <div class="number orange">{{$totrej}}</div><p>Total Receipt<strong class="text-primary">Rejected</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('admintheme/img/icon/notverified.png') }}" alt="" /></div>
                <div class="number red">{{$totalwith}}</div><p>Applications<strong class="text-primary">Withdrawn</strong></p>
               
              </div>
            </div>
         
          
          </div>
        </div>
</section>
<section class="data_table mt-5 form">
  <div class="container-fluid">
         @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
<?php $i=0; $url = URL::to("/");  ?>
   @if(!$lists->isEmpty())
  <div class="row d-flex align-items-center">
<div class="col"> <h5>Withdrawl of Candidate</h5></div>
    <div class="col">
    <form class="form-inline float-right">
         
          
   
        
      <div class="form-group "> 
        <label for="noofcards" class="mr-3">Select Status</label> 
        <form name="frmstatus" id="frmstatus" method="GET"  action="" >
        <select name="cand_status" id="cand_status" onchange="this.form.submit();">
              <option value="" @if($status=='') selected="selected" @endif>All</option>
              @foreach($status_list as $s)   
               @if($s->id==5|| $s->id==6) 
              <option value="{{$s->id}}" @if($status==$s->id) selected="selected" @endif >@if(isset($s)) {{ucwords($s->status)}} @endif</option>
              @endif
              @endforeach
        </select>
  &nbsp;  &nbsp;
                <div class="input-group ">
                    <input type="text" class="form-control input-lg"  name="search" placeholder="Search By Candidate Name"  />
          &nbsp;
                    <span class="input-group-btn">
                        <button class="btn btn-primary  btn-lg" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>



        </div>        
        <div class="form-group float-right ml-4">
               <!--  <div class="input-group ">
                    <input type="text" class="form-control input-lg"  name="search" placeholder="Search By Candidate Name"  />
          &nbsp;
                    <span class="input-group-btn">
                        <button class="btn btn-primary  btn-lg" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div> -->
            </div>
        </form>
    </div>
    </div>
    <div class="row" id="myTable">
  
      @foreach ($lists as $key=>$list)  
  <?php  $i++;
    $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);// \app(App\adminmodel\Candidateaffidavit::class)->where(['nom_id' =>$list->nom_id])->first(); 
     $party= getpartybyid($list->party_id);
     $symb= getsymbolbyid($list->symbol_id);
     $s= getnameBystatusid($list->application_status);
  ?>   
  
  
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex">
    <div class="card">
      <div class="card-header d-flex align-items-center">
       <h6 class="mr-auto">@if(isset($party)){{ucwords($party->PARTYNAME)}}@endif</h6>    
      </div>
      <div class="card-body">
      <div class="table-responsive">
      <table class="table">                    
              <tbody>
              <tr class="space">
                <td rowspan="5" class="profileimg" class="td-01">
				  <span class="btn-sno">{{$i}}</span>				@if($list->cand_image!='')
                       <img src="{{$url.'/'.$list->cand_image}}" class="prfl-pic img-thumbnail" alt="">
                    @else 
                      <img src="{{ asset('admintheme/img/male_avatar.png') }}" class="prfl-pic img-thumbnail" alt="">
				
                    @endif </td>
                 <td class="td-02" style="width: 30%"><label for="name">Name: <br> Name in Hindi <br>  Name in Vernacular</label></td>
        <td class="td-03" style="width: 40%"><p>{{$list->cand_name}}  <br> @if(!empty($list->cand_hname)) {{$list->cand_hname}} @endif <br>  @if(!empty($list->cand_vname)){{$list->cand_vname}} @endif</p></td>
               </tr> 
              <tr>	<td><label for="FName">Father's Name:</label></td>
					<td><p>{{$list->candidate_father_name}}</p></td>
			  </tr> 
			  
              <tr><td><label for="DateOfsubmission">Date of Submission:</label></td>
			  <td><p>{{date("d-F-Y",strtotime($list->date_of_submit))}}</p></td>
               </tr> 
            
			  <tr>
				<td><label for="Symbol">Symbol</label></td>
				<td><p>@if(isset($symb)) {{$symb->SYMBOL_DES}}@endif</p></td>
			  </tr>
         <tr>
                
                  <td class="td-02" style="width: 30%"><label for="name">Candidate ID: <br> Nomination ID: <br>  Party Type: </label></td>
        <td class="td-03" style="width: 40%"><p> {{ isset($list->candidate_id) ? $list->candidate_id : '' }} <br>  {{ isset($list->nom_id) ? $list->nom_id : '' }}  <br>  @if(!empty($party))  @if($party->PARTYTYPE=="N") National @endif @if($party->PARTYTYPE=="S") State  @endif @if($party->PARTYTYPE=="U") Unrecognized  @endif 
                    @if($party->PARTYTYPE=="Z") Independent  @endif @endif </p></td>                         
               </tr>
			  <!-- <tr>
				<td><label for="Ptype">Party Type</label></td>
				<td><p>@if(!empty($party))  @if($party->PARTYTYPE=="N") National   @endif @if($party->PARTYTYPE=="S") State  @endif @if($party->PARTYTYPE=="U") Unrecognized  @endif 
                    @if($party->PARTYTYPE=="Z") Independent  @endif @endif</p></td>
			  </tr> -->
             
             </tbody>
      </table>
      </div>
      </div>
      <div class="card-footer">
      <div class="row d-flex align-items-left">
	  <div class="col"> <small class="text-data text-success"><i class="fa fa-check"></i> @if(isset($s)){{ucwords($s)}} @endif</small> </div>
      <div class="col"> 
     
      <div class="btn-group float-right" role="group" aria-label="">
      @if(!empty($affidavit->affidavit_name)) <a href="{{asset($affidavit->affidavit_path)}}" class="btn btn-primary" target="_blank" >Download Affidavit</a>  @else <a href="#" class="btn btn-light">No Affidavit</a> @endif     
       &nbsp;&nbsp;     
      
        <button type="button" id="{{$list->nom_id}}" class="btn btn-primary  btn-sm getdata" data-toggle="modal" data-target="#changestatus" data-nomid="{{$list->nom_id}}" data-canid="{{$list->candidate_id}}" data-status="{{$list->application_status}}" data-message="{{$list->rejection_message}}"> Withdraw Candidate</button> 
      </div>
      </div>
      </div>
      </div>
    </div>
    </div>
  @endforeach
  @else
    <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
 
  </div>
   @endif
<!-- ==========================-->
    
    
  
</div>
</section>
  <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Withdrawn Candidate Status</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/withstatusvalidation') }}" >
                {{ csrf_field() }}   
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
     <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <div class="mb-3">
      <!--<div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline1" name="marks" value="4" class="custom-control-input" checked>
        <label class="custom-control-label" for="customRadioInline1">Rejected</label>
      </div>-->
       <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline2" name="marks" value="5" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline2">Withdrawn</label>
      </div> 
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline3" name="marks" value="6" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline3">Not Withdrawn</label>
      </div>
      </div>
    
       <div class="mb-3">
      
    <label class="sr-only" for="validationTextarea">I have examined the withdrawn papers <sup>*</sup></label>
    <textarea class="form-control " id="rejection_message" name="rejection_message" placeholder="Required example textarea" required="required"></textarea>
    <span id="err" class="text-danger"></span>
    <div class="invalid-feedback">
      Please enter a message in the textarea.
    </div>
    
    

  </div>
  
   
 
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </form>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->


@endsection
@section('script')
<script type="text/javascript">
           jQuery(document).ready(function(){
          //By Dropdown 
          jQuery("select[name='cand_status']").change(function(){
            var cand_status = jQuery(this).val();
            //alert(candStatus);
            jQuery.ajax({
                    url: "{{url('/listnomination')}}",
                    type: 'GET',
                    data: {cand_status:cand_status},
                    success: function(result){
              }
            });
          });
          
          //By Searh Text
          jQuery("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            jQuery("#myTable div").filter(function() {
              jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
            });
          });

          $("#election_form").submit(function(){
             if($("#rejection_message").val()=='')
                    {  
                    $("#err").text("");
                    $("#err").text("Please enter message");
                    $("#rejection_message").focus();
                    return false;
                    }
               });
        });
  
  $(document).on("click", ".getdata", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       var s = $(this).attr('data-status');
       var message = $(this).attr('data-message');
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
       $("#rejection_message").val(message);
       if(s==6){
            $("#customRadioInline3").attr ( "checked" ,"checked" );
          }
       
      if(s==5){
            $("#customRadioInline2").attr ( "checked" ,"checked" );
          }  
   });
    
    
</script>
 
@endsection