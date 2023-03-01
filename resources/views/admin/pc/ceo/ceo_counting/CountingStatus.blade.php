@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container">
  <div class="row">
  <div class="card text-left mt-3" style="width:100%;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4> List Of Counting Status</h4></div> 

              

              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;<a href="{{url('/pcceo/CountingStatusPdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
             
              <a href="{{url('/pcceo/CountingStatusExcel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
            
              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body"> 
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>PC No</th>
          <th>PC Name</th> 
          <th>Counting Status</th> 
          <th>Result Status</th> 
        
        </tr>
        </thead>
        <tbody>
        @php  
        $count = 1;

        @endphp
         @forelse ($CountingStatus as $key=>$CountingData)

          <tr>
         
             <td>{{ $CountingData->pno }}</td>
             <td>{{ $CountingData->npc }}</td>
             <td>{{ $CountingData->counting }}</td>
             <td>{{ $CountingData->res_declare }}</td>
            
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Counting Status</td>                 
              </tr>
          @endforelse
         
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


