@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'List All Accepted Candidates')
@section('content') 
<?php $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();


   $appliedtotal=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->whereNotIn('application_status', [4,5,6])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();

?>

<style type="text/css">
th, td {white-space: normal!important;}


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
                  <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
                <div class="number green">{{$totaccepted}}</div><p>Applications<strong class="text-primary">Accepted</strong></p>
               
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
                <div class="number orange">{{$totrej}}</div><p>Total Receipt<strong class="text-primary">Rejected</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
                <div class="number red">{{$totalwith}}</div><p>Applications<strong class="text-primary">Withdrawn</strong></p>
               
              </div>
            </div>
         
          
          </div>

            @if($appliedtotal > 0)
          <div class="alert alert-danger text-center" role="alert">Please Update Candidate Status for Scrutiny</div>

          @else
           @if($checkval==0)
      <!-- <h4 class="form-group float-right ml-4">Candidate Nominations details has not been finalized</h4> -->
     @elseif($checkval==1)
                      <h4 class="form-group float-right ml-4">Candidate Nominations details has been finalized</h4> 
            @endif
        </div>
</section>
<section class="data_table mt-5 form">
  <div class="container-fluid">
   
  @if(!$lists->isEmpty())
     <?php  $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no'=>$ele_details->CONST_NO])->where(['application_status' =>'6'])->get()->count();
     ?>
  <div class="row">
    <div class="col">
       <form class="form-inline d-flex align-items-center mb-5">
        <h4>Mark validly nominated candidates</h4>
       <div class="form-group mr-8 ml-auto ">
         
       
         
          @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
        @if (session('error_mes1'))
          <div class="alert alert-danger"> {{session('error_mes1') }}</div>
        @endif
        @if(!empty($errors->first()))
          <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
        @endif  
        </div>
            
        <div class="form-group float-right ml-4">
          <div class="input-group ">
                  <input type="text" class="form-control input-lg" name="search" placeholder="Search By Candidate Name" id="myInput"/>
              &nbsp;
              <span class="input-group-btn">
                <button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-search"></i></button>
              </span>
            </div>
            </div>
        </form>
    </div>
    </div>
  <div class="container">
    <p>List showing below is all accepted nominations. Please mark the candidates as validly nominated. In case of multiple accepted nominations of the same candidate mark only one as validly nominated. (Only the nominations marked as validly nominated will be available for final list of candidates)</p><br>
    <form class="form-horizontal" id="form7" method="POST"  action="{{url('ropc/change-sequence') }}" >
                {{ csrf_field() }}  
  <div class="row" id="myTable">
    <div class="col">
    <ul id="sortable1" class="connectedSortable list-group">
    <?php $i=1; $url = URL::to("/");   $val=0; ?>
   
      @foreach ($lists as $key=>$list)
  <?php 
     $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);
     $party= getpartybyid($list->party_id);
     $symb= getsymbolbyid($list->symbol_id);
     $s= getnameBystatusid($list->application_status);
     
    if(!empty($party)){
          if($party->PARTYTYPE=="N") $p="National";  
          if($party->PARTYTYPE=="S") $p="State";    
          if($party->PARTYTYPE=="U") $p="Unrecognized";    
          if($party->PARTYTYPE=="Z") $p="Independent";    
    }
   ?> 
      <li class="ui-state-default ">
      <div class="card">
  <div class="">
  <table class="table" cellspacing="0" class="table datalist-move">
	<tr>
		<td rowspan="4" class="profileimg" style="width: 16%;" >@if($list->cand_image!='')
                       <img src="{{$url.'/'.$list->cand_image}}" class="prfl-pic img-thumbnail" alt="">
                    @else 
                      <img src="{{ asset('admintheme/img/male_avatar.png') }}" class="prfl-pic img-thumbnail" alt="">
                    @endif 
      <span class="btn btn-danger btn-number">{{$i}}</span></td>
		<td colspan="4"><h5 class=" border-bottom m-0 p-2 mb-2">@if(isset($party)){{ucwords($party->PARTYNAME)}}@endif</h5></td>
		
	</tr>
	<tr>
		 
		<td colspan="2">Name in English :- <b> {{$list->cand_name}} </b> <br> Name in Hindi :- <b>@if(!empty($list->cand_hname)) {{$list->cand_hname}} @endif </b>  
    <br> Name in Vernacular :- <b> @if(!empty($list->cand_vname)){{$list->cand_vname}} @endif </b></td>
		<td colspan="2">Party Type <b>@if(isset($p)){{ucwords($p)}} @endif</b></td>
		
	</tr>
	<tr>
		
		<td colspan="2">Gender <b>{{ucwords($list->cand_gender)}}</b></td>
		<td colspan="2">Symbol <b>@if(isset($symb)) {{$symb->SYMBOL_DES}}@endif</b></td>
		
	</tr>
	<tr>
		
		<!-- <td>Current Status <b class="text-success">@if(isset($s)){{ucwords($s)}} @endif</b></td> -->
		
		<td colspan="3" >

      <label class="p-3 row align-items-center d-flex card-footer box-shadowb" for="">@if($checkval==0) Mark validly Nominated Candidates:- @if($list->finalaccepted==0)<span class="text-red"> No</span> @else <span class="text-green">Yes</span> @endif 



      <button type="button" id="{{$list->nom_id}}" class="btn btn-primary getdata ml-auto" data-toggle="modal" data-target="#changestatus" data-nomid="{{$list->nom_id}}" data-canid="{{$list->candidate_id}}">Mark validly Nominated Candidates</button> 
         @endif</label>  </td>  </tr>
  </table>
 
  </div>
      </div>
    </li>
    <?php $i++; ?>
    @endforeach
  </ul>
       
  </div>
</div>
    
</form>
</div>
@else
     <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
  @endif
  @endif
</div>
</section>
 <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Mark validly nominated candidates</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/finalaccepted') }}" >
                {{ csrf_field() }}   
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
     <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <div class="mb-3">
      
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline1" name="marks" value="1" class="custom-control-input" checked>
        <label class="custom-control-label" for="customRadioInline1">yes</label>
      </div>
       <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline2" name="marks" value="0" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline2">No</label>
      </div> 
      
      </div>
    
       <div class="mb-3">
       <p></p>
    
  </div>
  
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
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
        var v = $("#noval").val();
         //By Searh Text
          jQuery("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            jQuery("#myTable div").filter(function() {
              jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
            });
          });
        for (i = 1; i <=v; i++) { 
            jQuery("#newsrno"+i).keypress(function (e) {
               //if the letter is not digit then display error and don't type anything
               if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                  //display error message
                  jQuery("#errmsg"+i).html("Digits Only").show().fadeOut("slow");
                  return false;
              }
             });
            } // end for
        });
  
   $(document).on("click", ".getdata", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       var s = $(this).attr('data-status');
       var message = $(this).attr('data-message');
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
        
   });
    
    
</script>
  @endsection