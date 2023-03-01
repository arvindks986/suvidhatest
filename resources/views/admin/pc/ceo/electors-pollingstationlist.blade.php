@extends('admin.layouts.pc.report-theme')
@section('title', 'Electors Polling Stations')
@section('content') 
  <?php  $st=getstatebystatecode($user_data->st_code);
          $pc=getpcbypcno($user_data->st_code,$user_data->pc_no);
          $pcheade='all';
          $j=0;
  ?> 
<style>
 input[type="number"] {
   
    max-width: 81px;
}
 td {
    padding: 3px!important; vertical-align:middle; font-size:11px;
}
th {
    text-align: left; padding:2px; font-size:14px;
}
.table td span {
    padding: 2px;
}
input{    border: none;
    height: 23px;
    margin-top: 5px;
    padding: 2px;
    font-size:12px;
    }
td:first-child {
    text-align: center;
    vertical-align: middle;
}
 .table-responsive::-webkit-scrollbar-track
{
	background-color: #f2f2f2;
}

.table-responsive::-webkit-scrollbar{
	height: 10px;
}
input + span {
    font-size: 14px;
}

input.btn.btn-primary {
    height: auto;
}
 table, .table{margin-bottom:0px!important;}
 .table-responsive{}
 </style>
<section>
  <div class="container-fluid">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:20px auto 0;">
            <div class=" card-header">
                <div class=" row">
                  <div class="col"><h4>Electors Polling Station Info</h4></div> 
                   <div class="col"><p class="mb-0 text-right">
                   <b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
                   <b></b> 
                   <span class="badge badge-info"></span>&nbsp;&nbsp; </p>
                   </div>
                </div>
            </div>
   <div class="row">
    <div class="col">
      @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
      @endif
    </div>
    </div>
       
    <div class="card-body">  
     <form class="form-horizontal table-responsive" id="electorsPollingStation_form" method="POST"  action="{{url('pcceo/electors-pollingstation') }}" >
        {{ csrf_field() }} 
       <table  id="acViewBody" class="table table-striped table-bordered" style="width:100%">
       <div class="mb-3 row m-0" style="width:100%;"><div class="col-sm-2">PC Name</div> 
	   <div class="col p-0">
			  <select name="pc_no"  id="pc_no" class="form-control party_id">
        <option value=" ">---Select PC---</option>
					@foreach($all_pc as $pcList)
					<option value="{{ $pcList->PC_NO }}" >{{ $pcList->PC_NAME }}</option>
        	@endforeach
          <option value="90">All PC</option>
        </select></div>
       
		 		@if ($errors->has('pc_no'))
        <span style="color:red;">{{ $errors->first('pc_no') }}</span>
      	@endif
		  </div>
      <tbody>
       
       
        </tbody>
    </table>
        <?php  $url = URL::to("/");  ?>
             <!-- <div class="form-group float-right">  
                <input type="submit" value="Save" class="btn btn-primary">
                <input type="button" value="Delete" class="btn btn-primary" onclick="location.href = '{{$url}}/ropc/counting-finalized';">
             </div> -->
        </form> 
    </div>
    </div>
  </div>
  </div>
  </section>

@endsection

<script src="{{ asset('js/jquery.js')}}" type="text/JavaScript"></script> 
<script type="text/javascript">
   $(document).ready(function () {  
  //called when change the pc name
  jQuery("select[name='pc_no']").change(function(){
    var pc_no = jQuery(this).val();  
        jQuery.ajax({
            url: "{{url('/pcceo/getaclistbypc')}}",
            type: 'GET',
            data: {pc_no:pc_no},
            success: function(result){
            //  alert(result);
              $('#acViewBody').html(result);;
                }
            });
        });
});
 </script>