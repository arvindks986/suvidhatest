@extends('admin.layouts.ac.theme')
@section('title', 'Suvidha')
@section('bradcome', 'End of Poll Turnout Entry Details')
@section('content')
 <?php    $url = URL::to("/"); $j=0; ?>
  
 <main role="main" class="inner cover mb-3">
   
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
  				
  <div class="card text-left" style="width:100%; margin:10 auto;">
                <div class=" card-header">
                <div class=" row">
                <div class="col"> <h4>End of Poll Turnout Entry Details  </h4> </div> 
				<div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st_name}}</span>&nbsp;&nbsp;  <b class="bolt">AC Name:</b>  <span class="badge badge-info">{{$ac_no}}-{{$ac_name}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
        
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
       <form class="form-horizontal" id="election_form" method="post" action="{{url('roac/turnout/verify-schedule')}}" enctype="multipart/form-data" autocomplete='off'>
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
            <input type="text" name="totalvoter" id="totalvoter" class="form-control" value="{{$t}}">
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
					 <input type="text" name="malevoter" id="malevoter" class="form-control" value="{{$m}}"  >
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
                 <input type="text" name="femalevoter" id="femalevoter" class="form-control" value="{{$f }}">
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
            <input type="text" name="othervoter" id="othervoter" class="form-control" value="{{$o}}">
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
       <button type="button"  class="btn btn-primary custombtn"  onclick="location.href = '{{url('roac/turnout/schedule-entry/'.$round1)}}';">Edit</button></div>
			@endif 
			</div>
				
					</div>
					</div>
					 
			 <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
		</form>   
      @endif  
        

    </div>
   
    </div>
  
  
  </div>
  </div>
  </section>
 
   
  
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
  </main>
 
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
  if(malevoter.trim() == ''){
        $('#errmsg1').html('');
        $('#errmsg1').text('Please enter male voters');
        $( "input[name='malevoter']" ).focus();
        error = true;
      }
  if(femalevoter.trim() == ''){
       $('#errmsg2').html('');
       $('#errmsg2').html('Please enter female voters');
       $( "input[name='femalevoter']" ).focus();
       error = true;
    }
   if(othervoter.trim() == ''){
        $('#errmsg3').html('');
        $('#errmsg3').html('Please enter others voters');
        $( "input[name='othervoter']" ).focus();
         error = true;
      }
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
 

@endsection 