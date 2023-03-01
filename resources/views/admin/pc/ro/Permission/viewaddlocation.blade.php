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
        @if (Session::has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h2>Location List</h2>
                        </div>
                        <div class="card-body tabular-pane">
                            <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>S.no.</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Edit/Update</th>         
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($getAllPermsDatas))
                                    @foreach($getAllPermsDatas as $data)
                                    <tr>
                                    <tr>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->location_name}}</td>
                                        <td>{{$data->location_details}}</td>
                                        <td id="edit"><a href="{{url('/aro/permission/locationeditpermsn')}}/{{$data->id}}"><span class=" btn btn-success float-right">Edit</span></a></td>
                                    </tr>
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
</main>

@endsection