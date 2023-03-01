@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Verified Candidate for Scrutiny')
@section('content') 
<?php 
  $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
  $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();
    
     ?>
	 <style type="text/css">
th, td {white-space: normal!important;}
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
   
 <?php $i=1; $url = URL::to("/"); $j=0;?>
  @if(!$lists->isEmpty())
    
  <div class="row d-flex align-items-center mb-3">
  <div class="col"> <h5>Verified Candidate for Scrutiny</h5></div>
    <div class="col-md-8">  
      @if (\Session::has('success_mes'))
          <div class="alert alert-success"> {!! \Session::get('success_mes') !!} </div>
      @endif
    <form class="form-inline">
         
          
      <div class="form-group mr-auto"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <a href="#"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"></a>
      </div>
        
      <div class="form-group float-right"> 
        <label for="noofcards" class="mr-3">Select Status</label> 
        <form name="frmstatus" id="frmstatus" method="POST"  action="" >
          
          <select name="cand_status" id="cand_status" onchange="this.form.submit();">
                <option value="" @if($status=='') selected="selected" @endif>All</option>
                @if(isset($status_list))
                @foreach($status_list as $s)  
                @if($s->id<=6) 
                <option value="{{$s->id}}" @if($status==$s->id) selected="selected" @endif >@if(isset($s)){{ucwords($s->status)}}  @endif</option>
               @endif 
                @endforeach @endif
          </select>
        </div>        
        <div class="form-group float-right ml-4">
            <div class="input-group ">
                    <input type="text" class="form-control input-lg" name="search" placeholder="Search By Candidate Name" />
          &nbsp;
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-lg"  type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
           
            </div>
        </form>
    </div>
    </div>
     <div class="row" id="myTable">
      @foreach ($lists as $key=>$list)  
    <?php  $j++;
         $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);
            $getid = Crypt::encrypt($list->nom_id); 
         $party= getpartybyid($list->party_id);
          $symb= getsymbolbyid($list->symbol_id);
          $s= getnameBystatusid($list->application_status);
    ?>   
   
    
   <!--  <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex"> -->

    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex">
    <div class="card">
      <div class="card-header d-flex align-items-center">
      <h6 class="mr-auto">@if(isset($party)){{ucwords($party->PARTYNAME)}}@endif</h6>    
      </div>
      <div class="card-body">
      <div class="table-responsive">
      <table class="table">                    
              <tbody>
                <tr class="rowOne">
                <td rowspan="3" style="width:30%" class="profileimg"><span class="btn-sno">{{$list->cand_sl_no}}</span>@if($list->cand_image!='')
                      <img src="{{$url.'/'.$list->cand_image}}" class="prfl-pic img-thumbnail" alt=""/>
                    @else 
                      <img src="{{ asset('theme/img/male_avatar.png') }}" class="prfl-pic img-thumbnail" alt=""/>
                    @endif </td>
                  <td class="td-02" style="width: 30%"><label for="name">Name: <br> Name in Hindi: <br>  Name in Vernacular : </label></td>
        <td class="td-03" style="width: 40%"><p>{{$list->cand_name}}  <br> @if(!empty($list->cand_hname)) {{$list->cand_hname}} @endif <br>  @if(!empty($list->cand_vname)){{$list->cand_vname}} @endif</p></td>                         
               </tr> 
              <tr>
          <td><label for="FName">Father's / Mother's Name / Husband's Name:</label></td>
          <td><p>{{$list->candidate_father_name}}</p></td>
        </tr> 
              <tr>
        <td><label for="DateOfsubmission">Date of Submission:</label></td>
        <td><p>{{date("d-F-Y",strtotime($list->date_of_submit))}}</p></td>
               </tr>
                <tr>
          <td rowspan="2">
           <!--  @if(isset($symb->Symbol_Img))
                    <img src="data:{{$symb->CONTENT_TYPE}};base64, {{$symb->Symbol_Img}}" alt="Red dot" class="size-50"  />
                @endif -->
            </td>
        <td><label for="Symbol">Symbol</label></td>
        <td><p>@if(isset($symb)) {{$symb->SYMBOL_DES}}@endif</p></td>
               </tr>   
         
         <tr>
                
                  <td class="td-02" style="width: 30%"><label for="name">Candidate ID: <br> Nomination ID: <br>  Party Type: </label></td>
        <td class="td-03" style="width: 40%"><p> {{ isset($list->candidate_id) ? $list->candidate_id : '' }} <br>  {{ isset($list->nom_id) ? $list->nom_id : '' }}  <br>  @if(!empty($party))  @if($party->PARTYTYPE=="N") National @endif @if($party->PARTYTYPE=="S") State  @endif @if($party->PARTYTYPE=="U") Unrecognized  @endif 
                    @if($party->PARTYTYPE=="Z") Independent  @endif @endif </p></td>                         
               </tr>


               <tr>
                
                  <td class="td-02" style="width: 30%;"><label for="name"> 
                   @if(empty($affidavit->affidavit_name) && empty($list->cand_image))<td class="td-03" style="width: 40%; color: red;"> Note: Please Upload Affidavit and Profile Image</td> 
                    @elseif(empty($affidavit->affidavit_name)) <td class="td-03" style="width: 40%; color:red;"> Note: Please Upload Affidavit</td> 
                    @elseif (empty($list->cand_image)) <td  class="td-03" style="width: 43%; color:red;"> Note: Please Upload profile image</td> @endif </label></td>
                       
               </tr> 
           
          
              </tbody>
      </table>
      </div>
      </div>
      <div class="card-footer">
      <div class="row">
	  <div class="col"> 
	  	@if($s == "accepted")
						<small class="text-data text-success"><i class="fa fa-check"></i> Accepted </small>
					@elseif($s == "rejected")
						<small class="text-data text-primary"><i class="fa fa-check"></i> Rejected </small>
					@elseif($s == "withdrawn")
						<small class="text-data text-secondary"><i class="fa fa-check"></i> Withdrwan </small>
					@else
						<small class="text-data text-warning"><i class="fa fa-check"></i>{{$s}} </small>
					@endif
	  	  </div>
      <div class="col-md-8"> 
     
      <div class="btn-group float-right" role="group" aria-label="Basic example">
       @if(!empty($affidavit->affidavit_name) && !empty($list->cand_image)) <a href="{{asset($affidavit->affidavit_path)}}" class="btn btn-primary" download>Download Affidavit</a>&nbsp;&nbsp;

<button type="button" id="{{$list->nom_id}}" class="btn btn-primary getdata" data-toggle="modal" data-target="#changestatus" data-nomid="{{$list->nom_id}}" data-canid="{{$list->candidate_id}}" data-status="{{$list->application_status}}" data-message="{{$list->rejection_message}}"> Change Status</button> 
        @else 


           @if(empty($affidavit->affidavit_name) && $list->application_status !=4)
           
              <a href="{{url('ropc/candidateaffidavit') }}" class="btn btn-primary" >Upload Affidavit</a>&nbsp;&nbsp;
              <button type="button" id="{{$list->nom_id}}" class="btn btn-primary getdatareject" data-toggle="modal" data-target="#changestatus_reject" data-nomid="{{$list->nom_id}}" data-canid="{{$list->candidate_id}}" data-status="{{$list->application_status}}" data-message="{{$list->rejection_message}}">Reject Nomination</button> &nbsp;&nbsp;
          @endif
           @if(empty($list->cand_image))

            <a href="{{'updatenomination/'.$getid}}" class="btn btn-primary">Upload Profile Img</a>&nbsp;&nbsp;
             <!--  <a href="{{url('roac/candidateaffidavit') }}" class="btn btn-primary" >Upload Profile Img</a> -->
          
          @endif

        <!-- 
           <a href="#" class="btn btn-light disabled">Affidavit Not Submitted</a>&nbsp;&nbsp;&nbsp;&nbsp;
           <a href="{{url('ropc/candidateaffidavit') }}" class="btn btn-primary" >Upload Affidavit</a> -->



         @endif       
       &nbsp;&nbsp;       
      
        
      </div>
      </div>
      </div>
      </div>
    </div>
    </div>
     
  
  <?php $i++; ?>
   @endforeach
     </div> <!-- end Row-->
  @else
      <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
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
        <h4 class="modal-title" id="exampleModalLabel">Change Candidate Status</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/statusvalidation') }}" >
                {{ csrf_field() }}   
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
     <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <div class="mb-3">
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline1" name="marks" value="4" class="custom-control-input" checked>
        <label class="custom-control-label" for="customRadioInline1">Rejected</label>
      </div>
      <!--<div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline2" name="marks" value="5" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline2">Withdrawn</label>
      </div>-->
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline3" name="marks" value="6" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline3">Accepted</label>
      </div>
      </div>
    
       <div class="mb-3">
       <p>I have examined this nomination paper in accordance with section 36 of the Representation of the People Act, 1951 (43 of 1951)  and decide as follows: <sup>*</sup></p>
    <label class="sr-only" for="validationTextarea">I have examined this nomination paper in accordance with section 36 of the Representation of the People Act, 1951 (43 of 1951)  and decide as follows: <sup>*</sup></label>
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





<!--- New Model Rejected--->

<div class="modal fade" id="changestatus_reject" tabindex="-1" role="dialog" aria-labelledby="changestatus_reject" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Reject Nomination</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/statusvalidation_reject') }}" >
                {{ csrf_field() }}   
         
    <input type="hidden" name="nom_id" id="nom_id_rej" value="" readonly="readonly">
     <input type="hidden" name="candidate_id" id="nom_id_rej" value="" readonly="readonly">
    <div class="mb-3">
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline1" name="marks" value="4" class="custom-control-input" checked>
        <label class="custom-control-label" for="customRadioInline1">Rejected</label>
      </div>
      <!--<div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline2" name="marks" value="5" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline2">Withdrawn</label>
      </div>-->
      <!-- <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline3" name="marks" value="6" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline3">Accepted</label>
      </div> -->
      </div>
    
       <div class="mb-3">
       <p>I have examined this nomination paper in accordance with section 36 of the Representation of the People Act, 1951 (43 of 1951)  and decide as follows: <sup>*</sup></p>
    <label class="sr-only" for="validationTextarea">I have examined this nomination paper in accordance with section 36 of the Representation of the People Act, 1951 (43 of 1951)  and decide as follows: <sup>*</sup></label>
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


<!---- End Model----->






@endsection
@section('script')
<script type="text/javascript">
           jQuery(document).ready(function(){
          //By Dropdown 
          jQuery("select[name='cand_status']").change(function(){
            var cand_status = jQuery(this).val();
             alert(candStatus);
            jQuery.ajax({
                    url: "{{url('/scrutiny-candidates')}}",
                    type: 'POST',
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
       
      if(s==4){
            $("#customRadioInline1").attr ( "checked" ,"checked" );
          }  
   });
    
    $(document).on("click", ".getdatareject", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       var s = $(this).attr('data-status');
       var message = $(this).attr('data-message');
      
       $("#nom_id_rej").val(nomid);
      
       $("#candidate_id_rej").val(canid);
       $("#rejection_message").val(message);
       
   });
  
    
</script>
 
@endsection