@extends('admin.layouts.pc.theme')
@section('title', 'List Candidate')
@section('content') 



  <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col pl-0">
              <!-- Income-->
              <div class="card income">
			  <div class="card-body">        
                <div class="text-success"><b>Police Station</b> &nbsp; <div class="btn-group float-right">
                  <a type="button" href="{{url('/ropc/permission/viewps')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/ropc/permission/addps')}}" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div></div>
              </div>
            </div>
			<div class="col">
              <!-- Income-->
              <div class="card income "> 
			   <div class="card-body">
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/ropc/permission/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/ropc/permission/addauthority')}}" class="btn btn-sm btn-primary">Add</a>-->
				  </div>				</div></div>
              </div>
            </div>
          <div class="col pr-0">
              <!-- Income-->
              <div class="card income">
			  <div class="card-body">              
                <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right">
                 <a type="button" href="{{url('/ropc/permission/viewaddlocation')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/ropc/permission/addlocation')}}" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div></div>   
              </div>
            </div>
          </div>
        </div>
      </section>




@endsection
