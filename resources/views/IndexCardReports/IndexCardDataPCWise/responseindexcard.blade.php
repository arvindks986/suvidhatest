@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Index Card Finalization Requests')
@section('content')

<style>
  .card-header:before{
    visibility: hidden;
  }

  

img#theImg {
    display: none;
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
            <div class="col"><h4> Index Card Finalization Requests</h4></div> 
             
			
            </div>
      </div>
  
 <div class="card-body">
 
	<div class="table-responsive">
		
	
<table class="table table-bordered table-striped">
              <thead>
                <tr class="table-primary">
                  <th scope="col">SI No</th>
                  <th scope="col">State Name</th>
                  <th scope="col">Constituency Name</th>
                  <th scope="col">Request Date</th>
                  <th scope="col">Approved/Rejected Date</th>
                  <th scope="col">Status</th>
                  <th scope="col">Download</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                
				@foreach($data as $key => $raw)
                <tr>
                  <td>{{$key+1}}.</td>
                  <td>{{$raw->ST_NAME}}</td>
                  <td>{{$raw->PC_NAME}}</td>
                  <td>{{date('d-m-Y',strtotime($raw->submitted_at))}}</td>
				  <td>
						@if($raw->review_status == '0')
							--
						@else
						  {{date('d-m-Y',strtotime($raw->review_at))}}
						@endif
				  </td>
                  <td>
						@if($raw->review_status == '1')
							Approved
						@elseif($raw->review_status == '2')
							Rejected
						@else
							Pending
						@endif
				  </td>
                  <td><a href="../indexcard/{{$raw->file_name}}" target="_blank"> <i class="fa fa-download" aria-hidden="true" disabled></i></a></td>
                  <td><!-- data-toggle="modal" data-target="#myModal" -->
						@if($raw->review_status == '0')
							<button type="button" class="btn btn-info btn-lg" onclick="myfunction({{$raw->id}})" data-toggle="modal" data-target="#myModal">Click Here</button>
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


              
              <form class="form-vertical" action="submitindexcard" method="POST" enctype= "multipart/form-data">
              @csrf
			  <input type="hidden" name="id" id="id">
			  
			  
              <div class="col-sm-12 form-group">
                <div class="form-check-inline">
                  <input class="form-check-input" type="radio" name="reviewstatus" id="exampleRadios1" value="1" checked>
                  <label class="form-check-label" for="exampleRadios1">
                    Accept
                  </label>
                </div>
                <div class="form-check-inline">
                  <input class="form-check-input" type="radio" name="reviewstatus" id="exampleRadios2" value="2">
                  <label class="form-check-label" for="exampleRadios2">
                    Reject
                  </label>  
                </div>
              </div>
              
              <!-- checbox hidden -->
              <div class="form-group row" id="hidrow" style="display: none;">
                <div class="col-sm-6"><p style="font-size: 15px;">  Please Mark Checked Sections :</p> </div>
                <div class="col-sm-6">
                  <div class="form-check">
                    <input class="form-check-input" name="indexCard[]" type="checkbox" id="gridCheck1" value="Index Card Issue">
                    <label class="form-check-label" for="gridCheck1">
                      Index Card Issue.
                    </label>
                  </div>
                  
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox"  name="indexCard[]" id="gridCheck3" value="Signature Issue">
                    <label class="form-check-label" for="gridCheck3">
                      Signature Issue.
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="indexCard[]" id="gridCheck4" value="Coloured Copy Issue">
                    <label class="form-check-label" for="gridCheck4">
                      Coloured Copy Issue.
                    </label>
                  </div>
				  
				  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="indexCard[]" id="gridCheck2" value="Quality Issue">
                    <label class="form-check-label" for="gridCheck2">
                      Quality Issue.
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <!-- ends -->
            <div class="form-group">
              <div class="col-sm-12">
                <textarea id="" cols="10" rows="5" name="comments" class="form-control" placeholder="Comments if any"></textarea>
              </div>
            </div>
            <div class="col-sm-8 form-group" style="text-align: right;">
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

function myfunction(id){
	$.ajax({
		type: 'GET',
		dataType: 'json',
		url: '../eci/approveddata',
		data: {'id' : id},
		success: function (data) {
			//console.log(data);
			//console.log(data.id);
			$('#id').val(data.id);		
			$('#myModal').show();
		}
	});
}



$("#exampleRadios2").click(function(){
$("#hidrow").toggle();
});
$("#exampleRadios1").click(function(){
$("#hidrow").hide();
});
</script>



@endsection