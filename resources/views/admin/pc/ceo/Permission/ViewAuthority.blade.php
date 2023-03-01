@extends('admin.layouts.pc.theme')
@section('title', 'List Candidate')
@section('content') 
<style>
 .statistics div[class*=col-] .card {
    padding: 20px 7px;
 
    }
    .card.income b {
    font-size: 16px;
}
</style>
<main role="main" class="inner cover mb-3 mb-auto">
    <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
	<div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority Type</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/pcceo/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcceo/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
          <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission</b> &nbsp; <div class="btn-group float-right">
<!--					<button type="button" class="btn btn-sm btn-outline-primary">View</button>-->
                  <a type="button" href="{{url('/pcceo/viewpermsn')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcceo/addpermission')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div> 
              <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/pcceo/viewnodals')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcceo/addnodals')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
              
               <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission Date Restriction</b> &nbsp; <div class="btn-group float-right">
<!--					<button type="button" class="btn btn-sm btn-outline-primary">View</button>-->
                  <a type="button" href="{{url('/pcceo/EditRestriction')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/pcceo/addpermission')}}" class="btn btn-sm btn-primary">Update</a>-->
				  </div></div>
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
<div class="container-fluid">
<div class="row">
<div class="col-lg-12 p-0">
              <div class="card">
                <div class="card-header d-flex align-items-center">
                  <h2>Authority List</h2>
                </div>
                <div class="card-body tabular-pane">
                 <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
            <tr>
                <th>S.no.</th>
                <th>Authority Type Name</th>
              <th>Edit/Update</th>          
            </tr>
        </thead>
        <tbody>
            @if(!empty($getAllAuthorityData))
            @foreach($getAllAuthorityData as $data)
            <tr>
                <td>{{$data->id}}</td>
                <td>{{$data->name}}</td>
                <td id="edit"><a href="{{url('/pcceo/editauthority')}}/{{$data->id}}"><span class=" btn btn-success">Edit</span></a></td>
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