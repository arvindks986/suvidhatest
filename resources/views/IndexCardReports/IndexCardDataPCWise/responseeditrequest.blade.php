@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Index Card Edit Requests')
@section('content')

<style>
  .card-header:before{
    visibility: hidden;
  }

  .modal-dialog {
    max-width: 600px;
}


  th{
    text-align: center;
  }
  td{
    text-align: center;
  }
</style>

<?php  //$st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Index Card Edit Requests</h4></div> 
             
			
            </div>
      </div>
  
 <div class="card-body">
 
	<div class="table-responsive">
		
	
<table class="table table-bordered table-striped">
              <thead>
                <tr class="table-primary">
                  <th scope="col">SI No</th>
                  <th scope="col">State Name</th>
                  <th scope="col">Constituency</th>
                  <th scope="col">Request Date</th>
                  <th scope="col">Approved Date</th>
                  <th scope="col">Approved Status</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                
				
				@foreach($data as $key => $row)
				<tr>
                  <td>{{$key+1}}.</td>
                  <td>{{$row->ST_NAME}}</td>
                  <td>{{$row->ac_name}}{{$row->PC_NAME}}  @if($row->for_ac_no) (AC) @endif @if($row->for_pc_no) (PC) @endif</td>
                  <td>{{date('d/m/Y h:i A', strtotime($row->request_start_datetime))}}   --   {{date('d/m/Y  h:i A', strtotime($row->request_end_datetime))}}</td>
				  <td>
					@if($row->approve_start_date)
						{{date('d/m/Y h:i A', strtotime($row->approve_start_date))}}   --   {{date('d/m/Y  h:i A', strtotime($row->approve_end_date))}}
					@else
						--
					@endif
				  </td>
                  <td>
					@if($row->approval_status == 0)
						Pending
					@elseif($row->approval_status == 1)
						Approved
					@else
						Rejects
					@endif
				  </td>
				  
				  <td>
					@if($row->approval_status == 0)
						<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Click Here</button>
					@else
						--
					@endif
				  </td>
                  
                </tr>
				@endforeach
			

              </tbody>
            </table>





<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
                  <h6 style="width: 100%;text-align: center;text-decoration: underline;">Please Select Action Status</h6>

        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">

              <form class="form-vertical" action="#" method="POST" enctype= "multipart/form-data">
              
              <div class="col-sm-12 form-group" style="text-align: center;">
                <div class="form-check-inline">
                  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                  <label class="form-check-label" for="exampleRadios1">
                    Accept
                  </label>
                </div>
                <div class="form-check-inline">
                  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                  <label class="form-check-label" for="exampleRadios2">
                    Reject
                  </label>  
                </div>
              </div>
              
              <!-- checbox hidden -->
             <!--  <div class="form-group row">
                <div class="col-sm-6">Please Mark Checked Sections : </div>
                <div class="col-sm-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="gridCheck1">
                    <label class="form-check-label" for="gridCheck1">
                      Index Card Checked.
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="gridCheck2">
                    <label class="form-check-label" for="gridCheck2">
                      Quality Checked.
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="gridCheck3">
                    <label class="form-check-label" for="gridCheck3">
                      Signature Checked.
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="gridCheck4">
                    <label class="form-check-label" for="gridCheck4">
                      Coloured Copy Checked.
                    </label>
                  </div>
                </div>
              </div>
            </div> -->
            <!-- ends -->
            <div class="form-group" id="hidrow" style="display: inline-flex;">
              

                  <label for="rStartDate" style="padding: 2px;" class="col-sm-2 control-label">Start Date</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control" id="rStartDate" name="rStartDateAC">
                  </div>
                <label for="rEndDate" class="col-sm-2 control-label">End Date</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control" id="rEndDate" name="rEndDateAC">
                  </div>




            </div>


            <div class="form-group">  

                <div class="col-sm-12">
                <textarea name="" id="" cols="10" rows="5" class="form-control" placeholder="Comments if any"></textarea>
              </div> 


            </div>
            <div class="col-sm-7 form-group" style="text-align: right;">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>















      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- modal ends -->



	</div>
 </div>
 </div>
 </div>
 </div>
 </section>







<script type="text/javascript">
$("#exampleRadios1").click(function(){
$("#hidrow").toggle();
});
$("#exampleRadios2").click(function(){
$("#hidrow").hide();
});
</script>
@endsection