@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Migrant Vote Entry')
@section('content') 
  <?php  $st=getstatebystatecode($st_code);   
         $pc=getpcbypcno($st_code,$pc_no);
        $j=0;
  ?> 
<style type="text/css">
      
       .preview_table tr th:first-child, .preview_table tr td:first-child {
    max-width: 96px !important;
}
      div.dataTables_wrapper {margin:0 auto;} 
 
 

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
     .evm_input1{
      width: 150px;
    }
    .table td:last-child {
      width: 150px;
    }
   
    #preview_evem_votes input{
      border:0px;
      background: transparent;
    }
   
  </style>


 <main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid mt-2">
  <div class="row">

    
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>Migrant Vote Entry Form</h4></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp; </p></div>
         
                </div>
                </div>

   <div class="row">
    <div class="col">

          
      

    @if(Session::has('success_admin'))
          <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
       @endif   
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
    </div>
   
       
    <div class="card-body"> 
  
    @if(!$master_data->isEmpty())
  

        <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/counting/verify-migrate-votes') }}" >
                {{ csrf_field() }} 
                  
                 <input type="hidden" name="leading_id" value="{{$winn_data->leading_id}}">
                 <input type="hidden" name="CONST_TYPE" value="{{$ele_details->CONST_TYPE}}">
                 <input type="hidden" name="CONST_NO" value="{{$ele_details->CONST_NO}}">
                 <input type="hidden" name="ST_CODE" value="{{$ele_details->ST_CODE}}">
                 <input type="hidden" name="ELECTION_ID" value="{{$ele_details->ELECTION_ID}}">
    <table class="table  table-bordered preview_table">
        <thead>
            <tr><th width="30px">Sr. No</th><th>Candidate Name</th><th>Party</th><th>EVM Votes</th><th>Postal Votes</th><th>Migrant Votes</th>@if($finalize==1) <th>Total Votes</th> @endif</tr>
        </thead>
        <tbody><?php $j=0; $nett=0;  //dd($master_data); ?>
              @if(!empty($master_data))
            @foreach($master_data as $md)  
              <?php $j++;   ?>
             <input type="hidden" name="mid{{$j}}" value="{{$md->id}}">
             <input type="hidden" name="nom_id{{$j}}" value="{{$md->nom_id}}">
             <input type="hidden" name="candidate_id{{$j}}" value="{{$md->candidate_id}}">
              <tr  class="row_table">
                <td class="text-center text_td"><span class="english">{{$j}}</span></td>  
                <td class="text_td"><span class="english">{{$md->candidate_name}}</span> 
                  <br><span>{{$md->candidate_hname}} </span> </td>   

                <td  class="text_td"><span class="english">{{$md->party_name}} </span> <br>{{$md->party_hname}} </td> 

                <td  class="previous_vote_td"><input type="hidden" name="priviousvote{{$j}}" value="{{$md->total_vote}}" readonly="readonly"><span>{{$md->evm_vote}}</span></td> 
                <td  class="postal_vote_td"><span>{{$md->postal_vote}}</span></td>
              @if($finalize==0)  
              <td  class="current_vote_td"> <input type="text" name="currentvote{{$j}}" maxlength="6" class="evm_input" id="currentvote{{$j}}" value=" {{isset($md->migrate_votes) ?$md->migrate_votes:old('currentvote'.$j)}} "> 
                <span id="errmsg{{$j}}" class="text-danger"></span> </td> @else 
                <td><span>{{$md->migrate_votes}}</span></td> 
                <td><span>{{$md->total_vote}}</span></td> 
                @endif
                </tr>
                 <?php  $nett=$nett+$md->migrate_votes;   ?>
            @endforeach 
            @endif 
                <input type="hidden" name="val" id="va" value="{{$j}}">
           @if($finalize==0)
             
            <tr><td colspan="3">&nbsp;</td>   <td colspan="2"><b> Total Votes</b> <br><small class="text-danger">Please verify this total with manual record.</small></td>  
              <td> <input type="text" name="totalvotes" id="totalvotes" class="evm_input"  readonly="readonly" value="{{$nett}}"  >
                <span id="errtotal" class="text-danger"></span></td>  </tr>
            
                
             @endif
        </tbody>
     
    </table>
          <?php  $url = URL::to("/");  ?>
         @if($finalize==0)
             <div class="form-group float-right">  
                <input  id="submit_form" value="Print Preview " type="button" class="btn btn-primary">
                 
             </div>
            @endif
         
        </form>  
        @else
                 <p>No Records  Founds </p>  
        @endif      
            
    

    </div>
    </div>
  
  
  </div>
  </div>
  </section>
  </main>


<div class="modal fade" id="preview_evem_votes" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Preview your entry</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">    
             
    
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Edit</button>
        <button type="button" id="preview_print" class="btn btn-primary">Print</button>
        <button type="button" id="preview_submit" class="btn btn-primary display_none">Submit</button>
      </div>
      
      
    </div>
  </div>
</div>

 
@endsection
@section('script')

<script type="text/javascript">
   
  $(document).ready(function () { 
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
        calculate_total();
      });
    });

     

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
      if($(object).attr('id') != 'totalvotes'){
        total += parseInt($(object).val());
      }
    });

    if(total != $('#election_form #totalvotes').val()){
      $('#election_form #totalvotes').next('.text-danger').text("Total mismatched.").show();
      is_error = true;
    }
     
    
    if(is_error){
      return false;
    }else{
      $('#preview_evem_votes .modal-body').html('');
      $('#preview_evem_votes .modal-body').html($('.preview_table').clone());
      $('#preview_evem_votes').modal("show");
      $('#preview_evem_votes input').prop('disabled',true);
    }

  });

    $('#preview_print').click(function(e){
    
    var head = "<html><head><title></title><style>table {border-collapse: collapse;}td,th{font-size:13px;vertical-align:top;}</style></head><body style='padding:20px 20px;width:600px;'>";
  
      var foot = "</body>";
      var body = '';

      

      $('#election_form .preview_table tbody .row_table').each(function(index,object){
        body += "<tr>";
        var total = 0;
        $(object).find('td').each(function(index2, object2){

            if($(object2).hasClass('text_td')){
              var colspan = '';
              if($(object2).hasClass('has_class_total')){
                colspan =5;
              }
              body += "<td colspan='"+colspan+"'>"+$(object2).find('.english').html()+"</td>";
            }

            if($(object2).hasClass('previous_vote_td')){
                total += parseInt($(object2).text());
                body += "<td>"+$(object2).text()+"</td>";
            }
            if($(object2).hasClass('postal_vote_td')){
                total += parseInt($(object2).text());
                body += "<td>"+$(object2).text()+"</td>";
            }
            if($(object2).hasClass('current_vote_td')){
                total += parseInt($(object2).find('input').val());
                body += "<td>"+parseInt($(object2).find('input').val())+"</td>";
            }
          
        });
        body += "<td>"+total+"</td>";
        body += "</tr>";
      });
      html = '';
      
      html += "<table class='' border='1' cellpadding='15' style='verticle-align:top;'>";
      html += body;
      html += "</table>";
     

      $.ajax({
        url: "{!! url('/ropc/counting/migrant_pdf') !!}",
        type: 'POST',
        data: "pc_no={!! @$pc->PC_NO !!}&pc_name={!! @$pc->PC_NAME !!}&round='Ballot'&json=1&print_table="+encodeURIComponent(body),
        dataType: 'json', 
        beforeSend: function() {
        },  
        complete: function() {
        },        
        success: function(json) {
          window.open("{!! url('/ropc/counting/migrant_pdf') !!}","_blank");
          $('#preview_submit').removeClass("display_none");
        },
        error: function(data) {
          var errors = data.responseJSON;
          console.log(errors);
        }
      });  
      

      

    // $(this).addClass("display_none");
    



  });

  $('#preview_submit').click(function(e){
    if(confirm("Are you sure you want to submit the postal vote data. Upon submission the same data will be reflected in trends and result Website. You can edit the vote after the entry also.")){
      $(this).text('Processing...');
      $(this).prop('disabled',true);
      $("#election_form").submit();
    }else{

    }
  });

});

function trim_number(s) {
  while (s.substr(0,1) == '0' && s.length>1) { s = s.substr(1,9999); }
  return s;
}

function calculate_total(){
  var total_count = 0;
  $('#election_form .evm_input').each(function(i,object){
    if($(object).attr('id') != 'totalvotes' && parseInt($(object).val()) >= 0 && !isNaN($(object).val())){
      total_count = parseInt(total_count) + parseInt($(object).val());
    }
  });
  $('#election_form #totalvotes').val(total_count);
}

</script>

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