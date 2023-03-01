@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'PC Wise Rounds Schedule')
@section('content') 
  <?php   $st=getstatebystatecode($ele_details->ST_CODE);  
          $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO); 
          $url = URL::to("/"); $j=0;
    ?>
 <style type="text/css">
      
        
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
 <main role="main" class="inner cover mb-3">
  
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4>AC Wise Rounds Schedules</h4> </div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
          @if(Session::has('success_mes'))
          <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_mes')) }}</strong> </div>
       @endif   
      @if(Session::has('error_mes'))
        <div class="alert alert-danger"><strong> {{ nl2br(Session::get('error_mes')) }}</strong></div>
      @endif   
       @foreach ($errors->all() as $error)
            <span class="text-danger">{{ $error }} </span>
        @endforeach
      <div class="form-group float-right ml-4"> 
        
      </div> 
         
    </div>
    </div>
   
       
    <div class="card-body"> 
    @if($roundaclist->isEmpty())  <h6>Counting Data can not be add.</h6>  @endif 

   <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ropc/counting/verify-round-schedule-create') }}" >
                {{ csrf_field() }} 
      
    <table class="table  table-bordered preview_table" style="width:100%">
        <thead>
            <tr><th>Sr. No</th><th width="200">AC No-Name</th><th>No Of Rounds</th> </tr>
        </thead>
        <tbody><?php $j=0; $i=0;  ?>
              @if(!empty($roundaclist))
            @foreach($roundaclist as $md)  
              <?php $j++;  
                  $ac=getacbyacno($md->st_code,$md->ac_no);   
              ?>    
             <input type="hidden" name="rid{{$j}}" value="{{$md->id}}">
             <input type="hidden" name="ac_no{{$j}}" value="{{$md->ac_no}}">
            
              <tr>
                <td>{{$j}}</td>  
                <td><span>{{$ac->AC_NO}}-{{$ac->AC_NAME}}</span></td>   
                 
              <td>
               
                <input type="text" name="scheduled_round{{$j}}" maxlength="2" class="evm_input" id="scheduled_round{{$j}}" value="@if($md->scheduled_round!=0){{isset($md->scheduled_round) ?$md->scheduled_round:old('scheduled_round'.$j)}}@endif "> 
                 <span id="errmsg{{$j}}" class="text-danger"></span> 
              </td>  
               
                </tr>
                <?php $i++; ?>
            @endforeach 
            @endif 
                <input type="hidden" name="val" id="va" value="{{$j}}">
            
        </tbody>
     
    </table>
          <?php  $url = URL::to("/");  ?>
        
             <div class="form-group float-right">  
                 <input  id="submit_form" value="Update" type="button" class="btn btn-primary">
                 
             </div>
            
         
        </form> 

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
        <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Close</button>
        <button type="button" id="preview_submit" class="btn btn-primary">Ok</button>
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
      $(object).on('keyup change',function (e) {
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

     

    if(is_error){
      return false;
    }else{
      $('#preview_evem_votes .modal-body').html('');
      $('#preview_evem_votes .modal-body').html($('.preview_table').clone());
      $('#preview_evem_votes').modal("show");
      $('#preview_evem_votes input').prop('disabled',true);
    }

  });

  $('#preview_submit').click(function(e){
    if(confirm("Are you sure you want to continue.")){
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