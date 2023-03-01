@extends('admin.layouts.pc.theme')
@section('title', 'List Candidate')
@section('content') 
<main role="main" class="inner cover mb-3 mb-auto">
   <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col-lg-3 pl-0">
              <!-- Income-->
              <div class="card income text-center">
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-success"><b>Police Station</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/ropc/permission/viewps')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/ropc/permission/addps')}}" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div>
              </div>
            </div>
<!--          <div class="col-lg-3 ">
               Income
              <div class="card income text-center">
                 <div class="icon"><i class="icon-line-chart"></i></div> 
                <div class="text-info"><b>Permission</b> &nbsp; <div class="btn-group float-right mt-2">
		  <a type="button" href="{{url('/ropc/permission/viewpermsn')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/ropc/permission/addpermission')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div> -->
			<div class="col-lg-3 ">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right mt-2">
	          <a type="button" href="{{url('/ropc/permission/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/ropc/permission/addauthority')}}" class="btn btn-sm btn-primary">Add</a>-->
				  </div>				</div>
              </div>
            </div>
          <div class="col-lg-3 pr-0">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right mt-2">
		<a type="button" href="{{url('/ropc/permission/viewaddlocation')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/ropc/permission/addlocation')}}" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div>   
              </div>
            </div>
          </div>
        </div>
      </section>
    <section class="mt-5" id="wrapper">
        @if (Session::has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
   @endif
              @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="sidebar__inner">
                        <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                            <div class="card-header d-flex align-items-center">
                                <h2>Update Location</h2>
                            </div>
                            <div class="card-body getpermission">

                                @if(!empty($getAllPermsDatas))
                                @foreach($getAllPermsDatas as $data)
                                <form class="form-horizontal" method="POST" action="{{url('/ropc/permission/updateLocationval')}}">

                                    {{csrf_field()}}
                                    <input type="hidden" class="form-control" name="updateid"  value="{{$data->id}}">
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Name <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{$data->location_name}}">
                                            <span class="text-danger">{{ $errors->error->first('name') }}</span>
                                        </div>
                                    </div>



                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Address <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <textarea name="addr" class="form-control" placeholder="Add Address Here" id="" cols="3" rows="4">{{$data->location_details}}</textarea>
                                            <span class="text-danger">{{ $errors->error->first('addr') }}</span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                <div class="form-group row">

                                    <div class="col">
                                        <button class="btn btn-success float-right" name="submit" value="Update">UPDATE</button>
                                    </div>
                                </div>
                            </div>
                                </form>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>





            </div>
        </div>

    </section>

</main>
@endsection