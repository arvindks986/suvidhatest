@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Poll Day Schedule')
@section('content')
 <?php    $st=getstatebystatecode($ele_details->ST_CODE);  
          $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO); 
          $url = URL::to("/"); $j=0;
    ?>
 
 <main role="main" class="inner cover mb-3">
  
  <div class="container-fluid mt-3">
  <div class="row text-center mb-3">
   <div class="col">
   <span class="">
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$totalturnout_per}}%</span>
   <br />
				 <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">Voter Turn Out</span></span>
  </div></div>
   
  <div class="row">
  					
						
						
					
					
  <div class="card text-left mt-5" style="width:100%;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4>Estimated Poll Day Turnout Details-  </h4> </div> 
          <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b>PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
         @if (\Session::has('success'))
			<div class="alert alert-success">
				<ul>
					<li>{!! \Session::get('success') !!}</li>
				</ul>
			</div>
		@endif
      
         
    </div>
    </div>
   		  
    <div class="card-body">
   <div class="table-responsive">
  <table class="table Toggletable table-bordered table-hover" style="width:100%">
        <thead> <tr> <th rowspan="2">Sl. No.</th><th rowspan="2">AC No & Name</th> <th align="center">Electors Total</th>
          
          <th align="center">Latest Updated %</th><th align="center">Round1 % (Poll Start to 9:00 AM)</th> <th  align="center">Round2 %
(Poll Start to 11:00 AM) </th><th align="center">Round3 % (Poll Start to 1:00 PM)</th><th align="center">Round4 %
(Poll Start to 3:00 PM)</th><th  align="center">Round5 %
(Poll Start to 5:00 PM)</th><th  align="center">Close of Poll %</th>
<!--<th  align="center">Action</th>-->
</tr>
          
        </thead>  <?php $net_total=0; $net_elec_total=0;
              $net_round1 =0;
               $net_round2=0;
              $net_round3 =0;
               $net_round4=0;
               $net_round5 =0;
                $net_close=0;  ?>
        <tbody>@if(isset($lists))

            @foreach($lists as $list)   
            <?php $j++; 
                    $ac=getacbyacno($ele_details->ST_CODE,$list->ac_no);
                    $ele=getcdacelectorsdetails($ele_details->ST_CODE,$list->ac_no);
					
					//echo "<pre>"; print_r($ele); die;
					
//electors total

if(!empty($ele->electors_total))
{
$et= $ele->electors_total;
}	
else
{
$et=0;
}
                 
              ?> 
              
        <tr><td>{{$j}}</td><td>{{$ac->AC_NO}}- {{$ac->AC_NAME}}</td>
            <td>@if(isset($ele)){{$et}} @endif </td>
            
            <td>{{$list->est_turnout_total}}</td>
            <td>{{$list->est_turnout_round1}}</td> 
            <td>{{$list->est_turnout_round2}}</td>
            <td>{{$list->est_turnout_round3}}</td>
            <td>{{$list->est_turnout_round4}}</td>
            <td>{{$list->est_turnout_round5}}</td> 
            <td>{{$list->close_of_poll}}</td> 
            
	   <!--<td>@if($list->end_of_poll_finalize==0) <button type="button" id="{{$list->id}}" class="btn btn-primary  btn-sm getdata" data-toggle="modal" data-target="#changestatus" data-id="{{$list->id}}" data-acno="{{$list->ac_no}}" data-pcno="{{$list->pc_no}}" data-acname="{{$ac->AC_NAME}}"> Turnout Change</button> @else Finalize by CEO @endif</td>-->      
          </tr>
              @php($net_elec_total +=$et)
              @php($net_total +=$list->est_turnout_total)
              @php($net_round1 +=$list->est_turnout_round1)
              @php($net_round2 +=$list->est_turnout_round2)
              @php($net_round3 +=$list->est_turnout_round3)
              @php($net_round4 +=$list->est_turnout_round4)
              @php($net_round5 +=$list->est_turnout_round5)
              @php($net_close +=$list->close_of_poll)
              
            @endforeach 
            
            @endif 
        </tbody>
     
    </table>
      </div> 

    </div>
    </div>
  
  
  </div>
  </div>
  </section>
  </main>
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Estimated Poll Turnout %</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mb-0 text-left"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <br>
              <b>PC Name:</b> <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  
              <br><b>AC Name:</b>  <span class="badge badge-info" name="acname" id="acname"> </span>
            </p><br>
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/voting/estimated-turnout-change') }}" >
                {{ csrf_field() }}   
         
    <input type="hidden" name="id" id="id" value="" readonly="readonly">
     <input type="hidden" name="acno" id="acno" value="" readonly="readonly">
    <div class="mb-3">
      
        
      </div>
        <div class="mb-3">
      
            <label for="PercenTage" class="mt-2"><b>Select Rounds</b></label>
            <select name="rounds" id="rounds" required="required">
              <option value=""> Select One</option>
              <option value="1"> Round1 (9:00 AM) </option>
              <option value="2"> Round1 (11:00 AM) </option>
              <option value="3"> Round1 (1:00 PM) </option>
              <option value="4"> Round1 (3:00 PM) </option>
              <option value="5"> Round1 (5:00 PM) </option>
              <option value="6"> close of poll</option>
               
            </select> <span id="errmsg1" class="text-danger"></span> 
      
        </div>
       <div class="mb-3">
      
            <label for="PercenTage" class="mt-2"><b>Enter Total Percentage here</b></label>
            <input type="text" name="est_turnout" id="est_turnout" class="PoLLInput" placeholder="Estimated Poll Turnout % " value="" maxlength="5"  />
                           <span id="errmsg" class="text-danger"></span> 
                            @if ($errors->has('est_turnout'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round1') }}</span>
                           @endif  
               <span id="err" class="text-danger"></span>
                  <div class="invalid-feedback">  Please enter a turnout value.
                  </div>
      
        </div>
  
   
 
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="saverec">Save changes</button>
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
   $(document).ready(function () {  
     $("#est_turnout").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });
     

    $('#saverec').click(function(){
      var est = $('select[name="rounds"]').val();
      error = false;
     if(est.trim() == ''){
      $('#errmsg1').html('');
      $('#errmsg1').html('Please select Rounds');
      $( "select[name='rounds']" ).focus();
       error = true;
      }

   

    var est = $('input[name="est_turnout"]').val();
      error = false;
     if(est.trim() == ''){
      $('#errmsg').html('');
      $('#errmsg').html('Please enter voters turnout');
      $( "input[name='est_turnout']" ).focus();
       error = true;
      }

    if(error){
      return false;
    }
    
       }) // 
   
   
});
 
      $(document).on("click", ".getdata", function () {
       id = $(this).attr('data-id');
       acno = $(this).attr('data-acno'); 
       var acname = $(this).attr('data-acname');
       
       $("#id").val(id);
       $("#acno").val(acno);
       $("#acname").text(acname);
        
   });
 </script>
 @endsection
