@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Counting Finalize for PC')
@section('content') 
 <?php  $st=getstatebystatecode($st_code);  
          if($ele_details->CONST_TYPE=="PC")
          $pc=getpcbypcno($st_code,$pc_no);
  ?>

<style type="text/css">
      
        <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
        
        html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
  <!-- sachchidanand css -->
<style type="text/css">
    .text-danger{
      width: 100%;
      float: left;
      font-size: 10px;
    }
    .input-error{
      border-color: red;
    }
    .evm_input{
      width: 150px;
    }
    .table td:last-child {
      width: 150px;
    }
    .table td:nth-child(1) span, .table td:nth-child(2) span{
      width: 300px;
      word-break: break-all;
      white-space: initial;
      float: left;
    }
  </style>
<main role="main" class="inner cover mb-3">
 <section>
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h6 class="mr-auto">Counting Finalize Summary</h6></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
                        <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
                        <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  </p></div>
         
                </div>
                </div>
    
   
       
    <div class="card-body">  
  <table class="table  table-bordered" style="width:100%">
        <thead><tr><th>Sr. No</th><th>Candidate Name</th><th>Party</th> <!-- <th>Evm Votes</th> -->
                 <th>Postal Votes</th><!-- <th>Total Votes</th> --> </tr>
        </thead>
      <tbody>
           <?php $j=0;   ?>
              @if(!empty($master_data))
            @foreach($master_data as $md)  
            <?php $j++;  ?>
          <tr><td>{{$j}}</td> <td><span>{{$md->candidate_name}} <br>{{$md->candidate_hname}}  @if($md->nom_id==$winn_data->nomination_id) <b>(leading) </b>@endif @if($md->nom_id==$winn_data->trail_nomination_id) <b>(Trailing)</b>  @endif</span></td>   
                <td><span>{{$md->party_name}} <br>{{$md->party_hname}} </span></td>  <!--<td>{{$md->evm_vote}}</td>--><td>{{ $md->postal_vote}}</td>
                 <!-- <td>{{$md->total_vote}}  </td> --> </tr>

            @endforeach 
            @endif 
             </tbody> 
            </table> 
             <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/counting/counting-finalized-verify') }}" >
            {{ csrf_field() }}    <input type="hidden" name="state_code" id="state_code" value="{{$st_code}}">  
                                  <input type="hidden" name="pcval" id="pcval" value="{{$pc_no}}">  
       
     <!-- <div class="form-group">
          <label>Verify OTP Number :-<sup>*</sup></label>
              <input type='text'  name="verifyotp" id="verifyotp" lass="form-control" value="{{old('verifyotp') }}"/>
                <span id="err1"  style="color:red;"></span>
                   @if ($errors->has('verifyotp'))  <span style="color:red;"><strong>{{ $errors->first('verifyotp') }}</strong></span>  @endif
                 
                 <div id="clockdiv"></div>
      </div>-->
                 <?php  $url = URL::to("/");  ?>
              <div class="form-group float-right"> 

                <input type="button" value="Cancel" class="btn btn-primary" onclick="location.href = '{{$url}}/ropc/counting/postal-data-entry';">

                 <input type="button" value="Finalize " class="btn btn-primary" id="preview_submit">
                  
                     
              </div>
             
      </form>

 </div>
</div>
</div></div>
</section>
</main>
     

 <script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script>  
$(document).ready(function(){
 
  $("#election_form").submit(function(){
    
     if($("#verifyotp").val()=="")
    {
      $("#err").text("");
      $("#err1").text("Please enter verifyotp");
      $("#verifyotp").focus();
      return false;
    } 
      
    });
  });
</script> 
<script type="text/javascript">
  var time_in_minutes = 10;
var current_time = Date.parse(new Date());
var deadline = new Date(current_time + time_in_minutes*60*1000);


function time_remaining(endtime){
  var t = Date.parse(endtime) - Date.parse(new Date());
  var seconds = Math.floor( (t/1000) % 60 );
  var minutes = Math.floor( (t/1000/60) % 60 );
  var hours = Math.floor( (t/(1000*60*60)) % 24 );
  var days = Math.floor( t/(1000*60*60*24) );
  return {'total':t, 'days':days, 'hours':hours, 'minutes':minutes, 'seconds':seconds};
}
function run_clock(id,endtime){
  var clock = document.getElementById(id);
  function update_clock(){
    var t = time_remaining(endtime);
    clock.innerHTML = 'Left Time For OTP : '+t.minutes+' : '+t.seconds;
    if(t.total<=0){ clearInterval(timeinterval); }
  }
  update_clock(); // run function once at first to avoid delay
  var timeinterval = setInterval(update_clock,1000);
}
run_clock('clockdiv',deadline);
</script>
<script type="text/javascript">
 
    var prefix = 'ropc';
   function DownloadPdf(){
        var state_code = $("#state").val();
        var pcno = $("#pcval").val();
        
        var acurl = '<?php echo url('/'); ?>/'+prefix+'/form-21-report-pdf';
        $('#exportFrm').attr('action', acurl);
        $("#statevalue").val(state_code);
        $("#pcvalue").val(pcno);
        //$("#partyvalue").val(party_id);
        $("#exportFrm").submit();
    }

    
</script>
  

@endsection


@section('script')
<script type="text/javascript">
$(document).ready(function(e){
    $('#preview_submit').click(function(e){
    if(confirm("Are you sure you want to Finalize the Vote Count. Upon Finalization Changes can't be done and the same data will be reflected in trends and result Website.")){
      $(this).text('Processing...');
      $(this).prop('disabled',true);
      $("#election_form").submit();
    }else{

    }
  });

});
</script>
@endsection
 