@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Poll Day Create Schedule')
@section('content')
 <?php    $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$user_data->ac_no); 
          $pc=getpcbypcno($ele_details->ST_CODE,$user_data->pc_no);
          $url = URL::to("/"); $j=0; 
           $r="end";
          $round1=base64_encode($r);  
    ?>
   <?php /*$f1=1; $f2=1; $f3=1;$f4=1;$f5=1; $f6=1; if($lists->round5_voter_total>0) $f5=0;  if($lists->round4_voter_total>0) $f4=0;if($lists->round3_voter_total>0) $f3=0;if($lists->round2_voter_total>0) $f2=0;if($lists->round1_voter_total>0) $f1=0; if($lists->end_voter_total>0) $f6=0; */?>
 <main role="main" class="inner cover mb-3">
    <?php /* @if($ele_details->ScheduleID!=0)  
 @if($lists->close_of_poll!=0)  */ ?>
 <div class="section mt-4">
	<div class="container">
		<div class="row text-center mb-3">
   <div class="col">
   <span class="">
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$totalturnout_per}}%</span>
   <br>
				 <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">Voter Turn Out</span></span>
  </div></div>
  <div class="row text-center">
								<div class="col">
				
				 
				 <span type="text" class="btn btn-outline-dark outlinDark">Female 
				 <span class="badge badge-light">{{$femaleturnout_per}}%</span>
				 </span>  
				 
				 <span type="text" class="btn btn-outline-dark outlinDark">Male 
				 <span class="badge badge-light">{{$maleturnout_per}}%</span>
				 </span>  <span type="text" class="btn btn-outline-dark outlinDark">Others 
				 <span class="badge badge-light">{{$othersturnout_per}}%</span>
				 </span>  
				
				 
				 </div>
							</div> 
	</div>
  </div>
  <section class="mt-3">
  <div class="container">
<div class="row">
  				
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                <div class="col"> <h4>End of Poll Turnout Entry Details  </h4> </div> 
				<div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span>&nbsp;&nbsp; <b>PC Name:</b>  <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  <b class="bolt">AC Name:</b>  <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col-sm-12">
        
        @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         @if (\Session::has('success'))
			<div class="alert alert-success">
				<ul>
					<li>{!! \Session::get('success') !!}</li>
				</ul>
			</div>
		@endif
      
     @if($lists->end_of_poll_finalize==1)  <b>Finalized </b>@endif    
    </div>
   
    <div class="card-border">    

       @if($lists->end_of_poll_finalize==0)
       <form class="form-horizontal" id="election_form" method="post" action="{{url('aro/voting/verify-schedule')}}" enctype="multipart/form-data" autocomplete='off'>
  {{csrf_field()}}  
		  <div class="row">
				<div class="col-md-12">  
				@if(isset($lists))
					<input type="hidden" name="id" value="{{$lists->id}}">
        @else
          <input type="hidden" name="id" value="">
        @endif
        <input type="hidden" name="round" value="end">
					<div class="row d-flex align-items-center ">
            
						 <?php  if(old('totalvoter')){
              $t = old('totalvoter');
                } ?>   
         <div class="col">
          <label for="affidavit" class="col-form-label">Total Votes Polled<span class="errorred">*</span> </label>
            <input type="text" name="totalvoter" id="totalvoter" class="form-control" value="{{$lists->end_voter_total}}">
             @if ($errors->has('totalvoter'))
                                <span style="color:red;">{{ $errors->first('totalvoter') }}</span>
                           @endif 
          <span id="errmsg4" class="text-danger"></span>  
          </div> 		
											
		         <?php  if(old('malevoter')){
              $m = old('malevoter');
                } ?>
					<div class="col">
					<label for="affidavit" class="col-form-label">Male Voters </label>
					 <input type="text" name="malevoter" id="malevoter" class="form-control" value="{{$lists->total_male}}"  >
            @if ($errors->has('malevoter'))
                                <span style="color:red;">{{ $errors->first('malevoter') }}</span>
                           @endif 
					<span id="errmsg1" class="text-danger"></span>	
					</div>
           <?php  if(old('femalevoter')){
              $f = old('femalevoter');
                } ?>
          <div class="col">
                <label for="candidate_id" class="col-form-label">Female Voters  </label> &nbsp; &nbsp;
                 <input type="text" name="femalevoter" id="femalevoter" class="form-control" value="{{$lists->total_female }}">
                  @if ($errors->has('femalevoter'))
                                <span style="color:red;">{{ $errors->first('femalevoter') }}</span>
                           @endif 
                 <span id="errmsg2" class="text-danger"></span>  
                </div>  
                
           <?php  if(old('othervoter')){
              $o = old('othervoter');
                } ?>           
    
          <div class="col">
          <label for="affidavit" class="col-form-label">Other Voters  </label>
            <input type="text" name="othervoter" id="othervoter" class="form-control" value="{{$lists->total_other}}">
             @if ($errors->has('othervoter'))
                                <span style="color:red;">{{ $errors->first('othervoter') }}</span>
                           @endif 
          <span id="errmsg3" class="text-danger"></span>  
          </div>  
          <input type="hidden" name="newround" value="{{$round}}">

<div class="col-md-1 p-0 m-0">
        @if( $round!='')
      <button type="submit" id="saverec" class="btn btn-primary custombtn">Save</button>
      @else
       <button type="button"  class="btn btn-primary custombtn"  onclick="location.href = '{{url('aro/voting/schedule-entry/'.$round1)}}';">Edit</button></div>
			@endif 
			</div>
				
					</div>
					</div>
					 
			 <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
		</form>   
      @endif  
      @if($lists->end_of_poll_finalize==0)
      <div class="row"><div class="col-md-2 p-0 m-0" style="width: 100px;"></div>
     <div class="col-md-12 " style="margin-left:20px;">
        <label for="candidate_id" class="col-form-label">Editing of Voter Details will not be availaible after clicking on Finalize Turnout Button</label>
              <!--<button type="button"  class="btn btn-primary custombtn"  onclick="location.href = '{{url('roac/turnout/finalize-turnout')}}';">Finalized Turnout</button>-->
              <button type="button"  class="btn btn-primary custombtn"  onclick="return finalize();">Finalize Turnout</button>
     </div>
     </div>
   @endif

    </div>
   
    </div>
  
  
  </div>
  </div>
  </section>
<?php /*  @endif */ ?>
   
  
   <section class="mt-3">
  <div class="container">
<div class="row">
          
  <div class="card text-left" style="width:100%; margin:0 auto;">
                
    <table   class="table table-striped table-bordered" style="width:100%">
        <thead> <tr> <th rowspan="2">Sl. No.</th> <th colspan="4" align="center">Electors</th> <th colspan="4" align="center">End of Poll Turnout</th>
        <th colspan="4" align="center">Turnout % </th>  </tr>
          <tr>  <th>Male</th> <th>female</th><th>Other</th><th>total</th><th>Male</th> <th>female</th><th>Other</th><th>total</th>  
            <th>Male</th> <th>female</th><th>Other</th><th>total</th>
             </tr>
        </thead>

        <tr><td>1</td><td>@if(isset($ele)) {{$ele->electors_male}} @endif</td><td>@if(isset($ele)) {{$ele->electors_female}} @endif</td> <td>@if(isset($ele)) {{$ele->electors_other}} @endif</td><td>@if(isset($ele)) {{$ele->electors_total}} @endif</td>
         <td>{{$lists->end_voter_male}}</td><td>{{$lists->end_voter_female}}</td> <td>{{$lists->end_voter_other}}</td><td>{{$lists->end_voter_total}}</td> 
         <td>{{$maleturnout_per}}%</td><td>{{$femaleturnout_per}}%</td> <td>{{$othersturnout_per}}%</td><td>{{$totalturnout_per}}%</td>
            </tr>
    </table>

     
    </div>
  
  
  </div>
  </div>
  </section>
 
    <?php /* @else 
         <br><br>
         <p>You are not entitled for this election phase!</p>
         <br><br>
   @endif */ ?>
  </main>

  <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">               					
				<h4 class="modal-title w-100">Are you sure you want to finalize End of Poll Voter turnout data?</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p><span style="color:red">After finalization, the Voter turnout percentage will be updated for public through Voter turnout app and ECI website.<span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok confirm_button" onclick="submitForm();">Confirm</a>
            </div>
        </div>
    </div>
</div>
@endsection
 @section('script')
<script type="text/javascript">
   $(document).ready(function(){  
    $("#malevoter").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
       if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg1").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });
     $("#femalevoter").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
       if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg2").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });

     $("#othervoter").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
       if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg3").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });
     $("#totalvoter").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
       if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg4").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });    


    

    $('#saverec').click(function(){
    var round = $('select[name="round"]').val();
    var malevoter = $('input[name="malevoter"]').val();
    var femalevoter = $('input[name="femalevoter"]').val();
    var othervoter = $('input[name="othervoter"]').val(); 
    var totalvoter = $('input[name="totalvoter"]').val(); 

    error = false;
     
    if(round.trim() == ''){
      $('#errmsg').html('');
      $('#errmsg').html('Please select round').show()
      $( "input[name='round']" ).focus();
      error = true;
     
    }
   // if(malevoter.trim() == ''){
   //    $('#errmsg1').html('');
   //    $('#errmsg1').text('Please enter male voters');
   //    $( "input[name='malevoter']" ).focus();
   //     error = true;
      
   //  }
   //  if(femalevoter.trim() == ''){
   //    $('#errmsg2').html('');
   //    $('#errmsg2').html('Please enter female voters');
   //    $( "input[name='femalevoter']" ).focus();
   //     error = true;
    
   //  }
   //  if(othervoter.trim() == ''){
   //    $('#errmsg3').html('');
   //    $('#errmsg3').html('Please enter others voters');
   //    $( "input[name='othervoter']" ).focus();
   //     error = true;
      
   //  }
    if(totalvoter.trim() == ''){
      $('#errmsg4').html('');
      $('#errmsg4').html('Please enter total voters');
      $( "input[name='totalvoter']" ).focus();
       error = true;
      
    }

    if(error){
      return false;
    }

       }) // 
    }) // end function

  function jump_to_href(round){
      window.location.href = "<?php echo url('aro/voting/schedule-entry'); ?>/"+round;
    }
         
</script>
 <script type="text/javascript">	
	function finalize(){
	 $('#confirm').modal('show');
	}
	
	function submitForm(){
		window.location.href = "<?php echo url('aro/voting/finalize-turnout'); ?>";
		//document.preview.submit();
	}
</script>

@endsection 