@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container-fluid mt-3">
  <div class="row">
  <div class="card text-left" style="width:100%;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4> List of Nominations State and PC Wise Data</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; <a href="{{url('/eci/EciNominationPcWisePdf')}}/{{base64_encode($stcode)}}/{{base64_encode($pcno)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci/EciNominationPcWiseExcelReport')}}/{{base64_encode($stcode)}}/{{base64_encode($pcno)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
 <div class="table-responsive">
    <table class="list-table" class="table table-striped table-bordered table-hover " border="1"  style="width:100%; border-color:#dfdfdf;">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>Candidate Name</th> 
          <th>PC Name</th> 
          <th>Party Name</th> 
          <th>Symbol</th> 
        </tr>
        </thead>
        <tbody>
       @php  $count = 1; @endphp
         @forelse ($EciNominationPcWiseReport as $key=>$listdata)
          <tr>
            <td>{{ $count }}</td>
            <td><a href="{{url('/eci/EciViewNomination')}}/{{base64_encode($listdata->nom_id)}}/{{base64_encode($listdata->candidate_id)}}">{{ $listdata->cand_name }}</a></td>
            <td>{{ $listdata->PC_NAME }}</td>
            <td>{{ $listdata->PARTYNAME }}</td>
            <td>{{ $listdata->SYMBOL_DES }}</td>           
          </tr>
        @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="5">No Data Found For Nominations</td>                 
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


