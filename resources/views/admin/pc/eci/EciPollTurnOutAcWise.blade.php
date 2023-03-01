@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container">
  <div class="row">
  <div class="card text-left mt-3" style="width:100%;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4>ECI Poll Turn Out AC Wise</h4></div> 

              

              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;
              <a href="{{url('/eci/EciPollTurnOutAcWiseExcel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body"> 
<div class="table-responsive">
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>State</th> 
          <th>PC No</th> 
          <th>PC Name</th> 
          <th>AC No</th> 
          <th>AC Name</th> 
          <th>Electors Total</th>
          <th>Latest Total</th> 
          <th>Percent</th>  
        </tr>
        </thead>
        <tbody>
        @php  
        $count = 1; 
       

        @endphp
         @forelse ($EciPollTurnOutAcWise as $key=>$listdata)

       
          <tr>
             <td>{{ $count }}</td>
            <td>{{ $listdata->ST_NAME }}</td>
            <td>{{ $listdata->pc_no }}</td>
            <td>{{ $listdata->PC_NAME }}</td>
            <td>{{ $listdata->ac_no }}</td>
            <td>{{ $listdata->ac_name }}</td>
            <td>{{ $listdata->electors_total }}</td>
            <td>{{ $listdata->Latest_total }}</td>
            <td>{{ $listdata->Percent }}</td>
          
            
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Poll Turn Out</td>                 
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


