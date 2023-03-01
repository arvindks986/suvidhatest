@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Results Declaration Process')
@section('content')
<?php  $st=getstatebystatecode($st_code);   
          if($ele_details->CONST_TYPE=="PC")
          $pc=getpcbypcno($st_code,$pc_no);
       $url = URL::to("/");  
?>


<!-- sachchida css -->
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
     
      @if (session('unsuccess_insert'))
          <div class="alert alert-danger"> {{session('unsuccess_insert') }}</div>
      @endif
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> @if($winn_data->status==0)<h6 class="mr-auto">Results Declaration Process</h6> @else
                  <h6 class="mr-auto">Results   Declared</h6> @endif
				 <small>@if($finalize==0) Your PC Not Finalize @endif</small></div> 
                  <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> 
                        <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
                        <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  </p></div>
                 </div>
                </div>
       
    <div class="card-body"> 
    @if (session('success_mes'))
                  <div class="alert alert-success"> {{session('success_mes') }}</div>
              @endif
              @if (session('error_mes'))
                  <div class="alert alert-danger"> {{session('error_mes') }}</div>
              @endif 
   <table class="table  table-bordered" style="width:100%">
        <thead><tr><th>Sr. No</th><th >Candidate Name</th><th>Party</th> <th>Evm Votes</th>
                 <th>Postal Votes</th>@if($user_data->st_code=="S09")<th>Migrant Votes</th>@endif<th>Total Votes</th></tr>
        </thead>
      <tbody>
           <?php 

           $master_data_array = [];

           $j=0;  $tended_votes=0; ?>
              @if(!empty($master_data))
            @foreach($master_data as $md)  
             <?php $j++;      $tended_votes=$md->tended_votes;  $tended=$md->tended;?>
          <tr><td>{{$j}}</td> <td><span>{{$md->candidate_name}} <br>{{$md->candidate_hname}}  
             @if($winn_data->lead_total_vote!=$winn_data->trail_total_vote and $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0)  
                                        @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='0') <b> (Leading) </b>@endif   
                                         @if($md->nom_id==$winn_data->nomination_id and $winn_data->status=='1')  <b>(Won)</b> @endif   
                                        @if($md->nom_id==$winn_data->trail_nomination_id and $winn_data->status=='0')  <b>(Trailing) </b>@endif    
                                 @elseif($winn_data->lead_total_vote==$winn_data->trail_total_vote and  $winn_data->lead_total_vote!=0 and $winn_data->trail_total_vote!=0) 
                                        
                                @endif </span></td>   
                <td><span>{{$md->party_name}} <br>{{$md->party_hname}} </span></td>  <td>{{$md->evm_vote}}</td><td>{{ $md->postal_vote}}</td>
                                 @if($user_data->st_code=="S09")
                                   <td>{{$md->migrate_votes}}  </td>
                                   @endif
                 <td>{{$md->total_vote}}  </td> </tr>
                <?php  
                if($md->party_id != '1180'){
                  $master_data_array[] = [
                    'id'              => $md->id,
                    'nomination_id'   => $md->nom_id,
                    'candidate_name'  => $md->candidate_name,
                    'total_vote'      => $md->total_vote
                  ];
                }
                ?>
            @endforeach 
            @endif 
            
             </tbody> 
            </table> 
             <form class="form-horizontal" id="election_form" method="POST" action="{{url('/ropc/counting/results-declaration') }}" > 
              <input type="hidden" name="leading_id" readonly="readonly" value="@if(isset($winn_data)) {{$winn_data->leading_id}} @endif">
            {{ csrf_field() }}    
                 <?php  $url = URL::to("/");  ?>
                 
              <div class="form-group float-right"> 
               
                @if($finalize==1 && isset($winn_data) && $winn_data->status==0)
                 
                <button type="button" class="btn btn-primary electrolpopup" data-toggle="modal" data-target="#myModal" data-stcode="{{$st_code }}" data-pc_no="{{$pc_no}}" data-tended_votes="{{$tended_votes }}">Add Tendered Votes</button>
               @if($tended==1)
                 <button type="button" class="btn btn-primary" onclick="result_declare()">Results Declaration</button>
                @endif
                @endif 
                
             
                  <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{$url}}/ropc/form-21-report-pdf/{{$st_code}}/{{$pc_no}}"  title="Download PDF">Download Form 21 E</a></span>
				  
				  
				  
                  <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary"  href="{{url('/ropc/form-21c-report-pdf')}}" download="download" title="Download PDF">Download Form 21 C/D</a></span>
             
              </div>
             
      </form>
 </div>
</div>
</div></div>
</section>
</main>

<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Tendered Votes</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       <form class="form-horizontal" method="POST" action="{{url('ropc/counting/tenders-votes')}}" id="election_form">

         {{ csrf_field() }}
                         
         <input type="hidden" id="st_code" class="form-control" name="st_code" value="">
         <input type="hidden" id="pc_no" class="form-control" name="pc_no" value="">
         <div class="form-group row">
          <label class="col-sm-4 form-control-label">Tendered Votes <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="number" id="tended_votes" class="evm_input input-error"  maxsize="6" minsize="1" class="form-control" name="tended_votes" value="" >
            <span id="errmsg" class="text-danger"></span>
          </div>
        </div>
 

        <input type="submit" name="Update" value="Update" id="submit_form" class="btn btn-danger">
              
    </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
  <!--EDIT POP UP ENDS-->

<div class="modal fade" id="validate_result" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <?php if(isset($winn_data) && ($winn_data->margin == '0' || $winn_data->margin == 'NULL')){ ?>
          <h5 class="modal-title">Select Winner and Losser</h5>
        <?php }else{ ?>
        
        <h5 class="modal-title">Please enter the winner name<small>(in english)</small></h5>

        <?php } ?>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">    
        <form id="validate_result_form" onsubmit="return false;">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">


          <?php if(isset($winn_data) && ($winn_data->margin == '0' || $winn_data->margin == 'NULL')){ ?>

            <?php $button_id = 'submit_by_lottery'; ?>

            <?php if(isset($master_data)){ ?>


          
            <?php

              usort($master_data_array, function($a, $b){
                return $a['total_vote'] - $a['total_vote'];
              });


             
            ?>

            <div class="form-group">
            <label>Leading Candidate</label>
            <select name="draw_leading_nomination_id" class="form-control">
              <option value="">Select Winning Candidate</option>
            <?php foreach ($master_data_array as $key => $result) { ?>
                <option value="{{$result['nomination_id']}}">{{$result['candidate_name']}}- <b>Votes: {{$result['total_vote']}}</b></option>
            <?php } ?>
            </select>
            </div>

            <div class="form-group">
            <label>Trailing Candidate</label>
            <select name="draw_trailing_nomination_id" class="form-control">
              <option value="">Select Trailing Candidate</option>
            <?php foreach ($master_data_array as $key => $result) { ?>
                <option value="{{$result['nomination_id']}}">{{$result['candidate_name']}}- <b>Votes: {{$result['total_vote']}}</b></option>
            <?php } ?>
            </select>
            </div>

            <?php } ?>
          <?php }else{ ?>

            <?php $button_id = 'preview_submit'; ?>
            <div class="form-group">
              <input type="text" autocomplete="off" placeholder="Winner name(in english)" class="form-control" name="winner_name" id="winner_name">
            </div> 

          <?php } ?>





        </form> 
      </div>

      <div class="modal-footer">
        <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cancel</button>
        <button type="button" id="{{$button_id}}" class="btn btn-primary">Submit</button>
      </div>
      
      
    </div>
  </div>
</div>
 
@endsection
 

@section('script')

 <script type="text/javascript">
   function result_declare(){
    $('#validate_result').modal("show");
   }
$('#election_form .evm_input').each(function(i,object){
      $(".evm_input").removeClass("input-error");
      $(object).on('keyup change keydown',function (e) {
        if (parseInt($(object).val()) >= 0 && !isNaN($(object).val()) && $(object).val().indexOf('.') == '-1'){
          $(object).removeClass("input-error");
          $(object).parent('td').find('.text-danger').text("").hide();
          $(object).val(trim_number($(object).val()));
        }else{
          $(object).addClass("input-error");
          $(object).parent('td').find('.text-danger').text("please enter positive numeric value..").show();
          $(object).val('');
        }
         
      });
    });

     
//==========================
$("#election_form #submit_form").click(function(){
    
    var is_error = false;
    var total = 0;
    $('#election_form .evm_input').each(function(i,object){
      $(".evm_input").removeClass("input-error");
      if (parseInt($(object).val()) >= 0 && !isNaN($(object).val())  && $(object).val().indexOf('.') == '-1'){
        $(object).removeClass("input-error");
        $(object).parent('td').find('.text-danger').text("").hide();
        $(object).val(trim_number($(object).val()));
      }else{
        $(object).addClass("input-error");
        $(object).parent('td').find('.text-danger').text("please enter positive numeric value..").show();
        $(object).val('');
        is_error = true;
      }
       
    });

    

  });





  //=====================
   $(document).ready(function(e){
    $('#preview_submit').click(function(e){
      $.ajax({
        url: "{!! url('/ropc/counting/verify_winner_by_name') !!}",
        type: 'POST',
        data: $('#validate_result_form').serialize(),
        dataType: 'json', 
        beforeSend: function() {
          $('#preview_submit').prop('disabled',true);
          $('#preview_submit').text("Validating...");
          $('#preview_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {
        },        
        success: function(json) {
          if(json['status']==true){
            $('#election_form').submit();
          }else{
            $('.jq-toast-wrap').remove();
            error_messages(json['message']);
          }

          $('#preview_submit').prop('disabled',false);
          $('#preview_submit').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          console.log(errors);
          $('#preview_submit').prop('disabled',false);
          $('#preview_submit').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 

    });

    $('#submit_by_lottery').click(function(e){

      if(confirm("Are you sure you want to declare the result.")){
        $.ajax({
          url: "{!! url('/ropc/counting/result_declared_by_lottery') !!}",
          type: 'POST',
          data: $('#validate_result_form').serialize(),
          dataType: 'json', 
          beforeSend: function() {
            $('#submit_by_lottery').prop('disabled',true);
            $('#submit_by_lottery').text("Validating...");
            $('#submit_by_lottery').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          },  
          complete: function() {
          },        
          success: function(json) {
            if(json['status']==true){
              location.reload();
            }else{
              $('.jq-toast-wrap').remove();
              error_messages(json['message']);
            }

            $('#submit_by_lottery').prop('disabled',false);
            $('#submit_by_lottery').text("Submit");
            $('.loading_spinner').remove();
          },
          error: function(data) {
            var errors = data.responseJSON;
            console.log(errors);
            $('#submit_by_lottery').prop('disabled',false);
            $('#submit_by_lottery').text("Submit");
            $('.loading_spinner').remove();
          }
        }); 
      }else{

      }

    });



  });
  
   $(document).on("click", ".electrolpopup", function () {
 
       st_code = $(this).attr('data-stcode');
       pc_no = $(this).attr('data-pc_no');
       tended_votes = $(this).attr('data-tended_votes');
       
       $('#pc_no').val(pc_no);
       $('#st_code').val(st_code);
       $('#tended_votes').val(tended_votes);
    
   });
 </script>

<!-- Waseem validation -->
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
 
