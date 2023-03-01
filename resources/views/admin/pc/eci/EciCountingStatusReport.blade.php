@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> List Of Counting Status</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; <a href="{{url('/eci/EciCountingStatusReportPdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci/EciCountingExcelStatus')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>State</th> 
          <th>Total PC</th> 
          <th>Counting Started</th> 
          <th>Result Declared</th> 
          <th>Percentage</th> 
        </tr>
        </thead>
        <tbody>
        @php  

        $count = 1;
        $TotalPc= 0;
        $TotalCountingStarted = 0;
        $TotalDeclated = 0;

         @endphp

         @forelse ($EciCountingStatusReport as $key=>$listdata)

         @php 

         $TotalPc              += $listdata->TOTAL_PC;
         $TotalCountingStarted += $listdata->COUNTING_STARTED;
         $TotalDeclated        += $listdata->RESULT_DECLARED;


         @endphp

          <tr>
             <td>{{ $count }}</td>
            <td>{{ $listdata->ST_NAME }}</td>
            <td> @if($listdata->TOTAL_PC =='' )     0  @else  {{ $listdata->TOTAL_PC }} @endif</td>
            <td> @if($listdata->COUNTING_STARTED =='' )   0  @else {{ $listdata->COUNTING_STARTED }} @endif</td>
            <td> @if($listdata->RESULT_DECLARED =='' )   0  @else {{ $listdata->RESULT_DECLARED }} @endif</td>
            <td> @if($listdata->PERCENTAGE =='' )   0  @else {{ $listdata->PERCENTAGE }} @endif</td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Counting Status</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td><td><b></b></td><td><b>{{$TotalPc}}</b></td><td><b>{{$TotalCountingStarted}}</b></td><td><b>{{$TotalDeclated}}</b></td><td><b></b></td></tr>
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

@endsection


