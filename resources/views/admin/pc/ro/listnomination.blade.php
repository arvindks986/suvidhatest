@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'List of All Applications')
@section('content') 

<?php  
    
    $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();
     
     ?>
<main>
<style type="text/css">
th, td {white-space: normal!important;}
.data_table td label {
    font-size: 12px!important;
}
.table tr td p {
    margin: 0px;
    font-size: 13px;
}
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
<section>
	
		<div class="row">
			<div class="col">
				  @if($cand_finalize_ro==0)
     <div class="alert alert-danger"> Candidate Nominations details has not been finalized</div>
     @elseif($checkval==1)
                    <div class="alert alert-success">  Candidate Nominations details has been finalized </div>
            @endif</div>
			</div>
		
	
</section>
<section class="data_table mt-5 form">
  <div class="container-fluid">
 
	<div class="row">
	    @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         @if (session('finalize_mes'))
          <div class="alert alert-success"> {{session('finalize_mes') }}</div>
        @endif
	</div>
	<div class="row d-flex align-items-center mb-3">
	<div class="col">
		<h5>List of All Applications</h5>
	</div>
		<div class="col-md-8">
		<form class="form-inline pull-right">
         
          
			<div class="form-group float-right"> 
				<label for="noofcards" class="mr-3">Select Status</label> 
				<form name="frmstatus" id="frmstatus" method="POST"  action="" >
				<select name="cand_status" id="cand_status" onchange="this.form.submit();">
              <option value="" @if($status=='') selected="selected" @endif>All</option>
              @if(isset($status_list))
              @foreach($status_list as $s)
              @if($s->id==1 || $s->id==4 ||$s->id==5|| $s->id==6)
              <option value="{{$s->id}}" @if($status==$s->id) selected="selected" @endif >@if(isset($s)){{ucwords($s->status)}}  @endif</option>
              @endif
              @endforeach @endif
        </select>
		    </div>				
		    <div class="form-group float-right ml-4">
                <div class="input-group ">
                    <input type="text" class="form-control input-lg" name="search" placeholder="Search By Candidate Name"  />
					&nbsp;
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
        </form>
		</div>
		</div>
	 
	<div class="row" id="myTable">
	<?php $url = URL::to("/");    $i=1;   ?>
	@if(!$lists->isEmpty())
	@foreach ($lists as $key=>$list)
	<?php   $getid = Crypt::encrypt($list->nom_id);
	      
		 $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);// \app( 
		 $party= getpartybyid($list->party_id);
		 $symb= getsymbolbyid($list->symbol_id);
		 $s= getnameBystatusid($list->application_status);
	?>   
	
		<div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex" data-id="key{{$s}}">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<h6 class="mr-auto">
					@if(!empty($party))
						{{$party->PARTYNAME}}/{{ !empty($party->PARTYHNAME) ? trim($party->PARTYHNAME) : ''}} 
					@endif</h6>
	<!-- @if($cand_finalize_ro==0)				 
	<button type="button" id="{{$list->nom_id}}" class="btn btn-link btn-sm getdata" data-toggle="modal" data-target="#changestatus" data-nomid="{{$list->nom_id}}" data-canid="{{$list->candidate_id}}"> Drop <i class="fa fa-times" aria-hidden="true"></i></button> 
	@endif  -->
				 
				</div>


			 
			<div class="table-responsive card-body">
		
			<table class="table " border="0">                    
			  <tbody>
				<tr class="space">
				<td rowspan="6" class="profileimg td-01" style="width: 30%">
				<span class="btn-sno">{{$i}}</span>	@if($list->cand_image!='')
                      <img src="{{$url.'/'.$list->cand_image}}" class="prfl-pic img-thumbnail" alt="no images">
                    @else 
                      <img src="{{ asset('admintheme/images/User-Icon.png') }}" class="prfl-pic img-thumbnail" alt="">
                    @endif
				</td>
				<td class="td-02" style="width: 30%"><label for="name">Name: <br> Name in Hindi <br>  Name in Vernacular</label></td>
				<td class="td-03" style="width: 40%"><p>{{$list->cand_name}}  <br> @if(!empty($list->cand_hname)) {{$list->cand_hname}} @endif <br>  @if(!empty($list->cand_vname)){{$list->cand_vname}} @endif</p></td>
				</tr>
				<tr class="space">
				<td><label for="FName">Candidate ID:</label></td>
				<td><p>{{$list->candidate_id}} </p></td>
				
				</tr>   
				<tr class="space">
				<td><label for="FName">Father's / Mother's Name / Husband's Name:</label></td>
				<td><p>{{$list->candidate_father_name}}</p></td>
				
				</tr> 
				<tr class="space">
				<td><label for="DateOfsubmission">Date of Submission:</label></td>
				<td><p>{{date("d M Y",strtotime($list->date_of_submit))}}</p></td>
				
				</tr> 
				<tr class="space">
 
				<td>
				<label for="Symbol">Symbol</label></td><td><p>@if(!empty($symb)) {{$symb->SYMBOL_DES}} @endif</p>				
				</td>
				</tr>
				<tr class="space">
				<td><label for="Ptype">Party Type</label></td>
				<td>
						<p>
							@if($party->PARTYTYPE=="N") 
								National  
							@endif 
							@if($party->PARTYTYPE=="S") 
								State  
							@endif 
							@if($party->PARTYTYPE=="U") 
								Unrecognized  
							@endif 
							@if($party->PARTYTYPE=="Z") 
								Independent  
							@endif
						</p></td>
				</tr> 
		
	
		  
		
				</tbody>
			</table>
			</div>
				<div class="card-footer">
      <div class="row d-flex align-items-center">
	  <div class="col md-3">
	  @if($s == "accepted")
						<small class="text-data text-success"><i class="fa fa-check"></i> Accepted </small>
					@elseif($s == "rejected")
						<small class="text-data text-danger"><i class="fa fa-check"></i> Rejected </small>
					@elseif($s == "withdrawn")
						<small class="text-data text-secondary"><i class="fa fa-check"></i> Withdrwan </small>
					@else
						<small class="text-data text-warning"><i class="fa fa-check"></i>{{$s}} </small>
					@endif
					</div>
      <div class="col"> 
     
      <div class="btn-group float-right" role="group" aria-label="Basic example">
     		@if(!empty($affidavit->affidavit_name))
				<a href="{{asset($affidavit->affidavit_path)}}" class="btn btn-primary btn-sm" download>Download Affidavit</a>&nbsp;&nbsp;
			@endif
			@if($cand_finalize_ro==0)
           		<a href="{{'updatenomination/'.$getid}}" class="btn btn-primary btn-sm">Update Profile</a>&nbsp;&nbsp;
           @endif
           @if($list->cand_name!="NOTA")
				<a href="{{'viewnomination/'.$getid}}" class="btn btn-primary btn-sm">View Profile</a>
			@endif
		
		
      </div>
      </div>
      </div>
      </div>
			
			</div>
			
		</div>
	<?php $i++; ?>	 
	@endforeach
	@else
	  <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
	@endif
	</div>
</div>
</section>
 <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <small class="modal-title" id="exampleModalLabel">Remove Duplicate Candidate Entry.</small>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/duplicate-drop') }}" >
                {{ csrf_field() }}   
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
     <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <div class="mb-3">
    	
		 <p style="font-size:14px;" class="">Are you sure. You want to drop this duplicate record<sup>*</sup>
		 <br /> </p>
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline1" name="marks" value="11" class="custom-control-input" required="required">
        <label class="custom-control-label" for="customRadioInline1" >Duplicate Drop</label>
      </div>
	  <br />
	 
     </div>
     <div class="mb-3">
      <small class="text-secondary">Incase if the entry has been made wrongly, can be removed by this option</small>
      </div> 
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Remove</button>
      </div>
    </form>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->
</main>  
@endsection
@section('script')
<script type = "text/javascript">  
window.onload = function () {  
	document.onkeydown = function (e) {  
		return (e.which || e.keyCode) != 116;  
	};  
}  
jQuery(document).ready(function(){
	//By Dropdown 
	jQuery("select[name='cand_status']").change(function(){
		var cand_status = jQuery(this).val();
		//alert(candStatus);
		jQuery.ajax({
            url: "{{url('/listnomination')}}",
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
});

$(document).on("click", ".getdata", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
        
   });
</script>  
@endsection
