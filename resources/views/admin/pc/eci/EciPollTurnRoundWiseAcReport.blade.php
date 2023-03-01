@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
<section>
  <div class="container">
  <div class="row">
  <div class="card text-left mt-3" style="width:100%;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4>Eci Complete Poll Round Report</h4></div> 

              

              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;
              <a href="{{url('/eci/EciCompPollRoundReportExcel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
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
          <th>9 AM Male</th>
          <th>9 AM Female</th> 
          <th>9 AM Other</th> 
          <th>9 AM Total</th> 
          <th>11 AM Male</th>
          <th>11 AM Female</th> 
          <th>11 AM Other</th>
          <th>11 AM Total</th> 
          <th>1 PM Male</th>
          <th>1 PM Female</th> 
          <th>1 PM Other</th>
          <th>1 PM Total</th> 
          <th>3 PM Male</th>
          <th>3 PM Female</th> 
          <th>3 PM Other</th>
          <th>3 PM Total</th> 
          <th>5 PM Male</th>
          <th>5 PM Female</th> 
          <th>5 PM Other</th>
          <th>5 PM Total</th>
          <th>End Of Poll Male</th>
          <th>End Of Poll Female</th> 
          <th>End Of Poll Other</th>
          <th>End Of Poll Total</th>
          <th>Latest Male</th>
          <th>Latest Female</th>
          <th>Latest Other</th>
          <th>Latest Votes</th>
          <th>Electors Male</th>
          <th>Electors Female</th>
          <th>Electors Other</th>
          <th>Electors Total</th>
          <th>Voting Percent</th>
        </tr>
        </thead>
        <tbody>
        @php  
        $count = 1; 
       

        @endphp
         @forelse ($EciPollTurnRoundWiseAcReport as $key=>$listdata)

       
          <tr>
             <td>{{ $count }}</td>
            <td>{{ $listdata->ST_NAME }}</td>
            <td>{{ $listdata->pc_no }}</td>
            <td>{{ $listdata->PC_NAME }}</td>
            <td>{{ $listdata->ac_no }}</td>
            <td>{{ $listdata->ac_name }}</td> 
            <td>{{ $listdata->round1_voter_male }}</td>
            <td>{{ $listdata->round1_voter_female }}</td>
            <td>{{ $listdata->round1_voter_other }}</td>
            <td>{{ $listdata->round1_voter_total }}</td>
            <td>{{ $listdata->round2_voter_male }}</td>
            <td>{{ $listdata->round2_voter_female }}</td>
            <td>{{ $listdata->round2_voter_other }}</td>
            <td>{{ $listdata->round2_voter_total }}</td>
            <td>{{ $listdata->round3_voter_male }}</td>
            <td>{{ $listdata->round3_voter_female }}</td>
            <td>{{ $listdata->round3_voter_other }}</td>
            <td>{{ $listdata->round3_voter_total }}</td>
            <td>{{ $listdata->round4_voter_male }}</td>
            <td>{{ $listdata->round4_voter_female }}</td>
            <td>{{ $listdata->round4_voter_other }}</td>
            <td>{{ $listdata->round4_voter_total }}</td>
            <td>{{ $listdata->round5_voter_male }}</td>
            <td>{{ $listdata->round5_voter_female }}</td>
            <td>{{ $listdata->round5_voter_other }}</td>
            <td>{{ $listdata->round5_voter_total }}</td>
            <td>{{ $listdata->end_voter_male }}</td>
            <td>{{ $listdata->end_voter_female }}</td>
            <td>{{ $listdata->end_voter_other }}</td>
            <td>{{ $listdata->end_voter_total }}</td>
            <td>{{ $listdata->total_male }}</td>
            <td>{{ $listdata->total_female }}</td>
            <td>{{ $listdata->total_other }}</td>
            <td>{{ $listdata->Latest_total }}</td>
            <td>{{ $listdata->electors_male }}</td>
            <td>{{ $listdata->electors_female }}</td>
            <td>{{ $listdata->electors_other }}</td>
            <td>{{ $listdata->electors_total }}</td>
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


