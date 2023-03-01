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
                  <a type="button" href="{{url('/aro/permission/viewps')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/aro/permission/addps')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div></div>
              </div>
            </div>
			<div class="col">
              <!-- Income-->
              <div class="card income "> 
			   <div class="card-body">
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/aro/permission/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/aro/permission/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div></div>
              </div>
            </div>
          <div class="col pr-0">
              <!-- Income-->
              <div class="card income">
			  <div class="card-body">              
                <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right">
                 <a type="button" href="{{url('/aro/permission/viewaddlocation')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/aro/permission/addlocation')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div></div>   
              </div>
            </div>
          </div>
        </div>
      </section>
<section>
		 @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
   
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 p-0">
              <div class="card">
                <div class="card-header d-flex align-items-center">
                  <h5>Police Station Details</h5>
                </div>
                <div class="card-body tabular-pane">
                 <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
            <tr>
                <th>S.no.</th>
                <th>Police Station Name</th>
                <th>Police Station Address</th>
                 <th>Police Station Mobile No</th>
                <th>Incharge Name</th>
                <th>Police Station Incharge Mobile No</th>
                <th>Edit/Update</th>               
            </tr>
        </thead>
        <tbody>
            @if(!empty($getAllPSData))
            @foreach($getAllPSData as $data)
              <tr>
                <td>{{$data->id}}</td>
                <td>{{$data->police_st_name}}</td>
                <td>{{$data->police_station_address}}</td>
                <td>{{$data->police_station_no}}</td>
                <td>{{$data->incharge_name}}</td>
                <td>{{$data->police_st_incharge_no}}</td>
                <td><a href="{{url('/aro/permission/editps')}}/{{$data->id}}" class="btn btn-primary btn-block">Edit</a></td>
            </tr>
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




@endsection
