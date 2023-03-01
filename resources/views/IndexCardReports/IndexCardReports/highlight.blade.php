@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')
@section('bradcome', 'Index Card Ac Wise')
@section('content')

@php
  if(Auth::user()->designation == 'ROAC'){
    $prefix   = 'roac';
  }else if(Auth::user()->designation == 'CEO'){ 
    $prefix   = 'acceo';
  }else if(Auth::user()->role_id == '27'){
    $prefix   = 'eci-index';
  }else if(Auth::user()->role_id == '7'){
    $prefix   = 'eci';
  }
@endphp

<?php  $st=getstatebystatecode($st_code);   ?>

<style>
th, td{
text-transform: uppercase;
}
.dev2{
text-transform: capitalize;
}

.table th {
    background: #f0587e;
    color: #fff;
}


</style>
<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(4 - Highlights )</h4></div>
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b>
            </p>
            <p class="mb-0 text-right">
              <a href="{!! url('/'.$prefix.'/highlights-pdf/'.$st_code) !!}" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="{!! url('/'.$prefix.'/highlights-excel/'.$st_code) !!}" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 63px !important; display: table-row;"></a>
            </p>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" style="width: 100%;table-layout: fixed;">
            <tr>
              <th>1.</th>
              <th colspan="9">No. of Constituencies</th>
            </tr>
            <tr>
              <td colspan="6">Type Of Constituency</td>
              <td>GEN</td>
              <td>SC</td>
              <td>ST</td>
              <td colspan="">Total</td>
            </tr>
            <tr>
              <td colspan="6">No Of Constituencies</td>
              <td>{{(isset($candidates->genac) ? $candidates->genac : 0)}}</td>
              <td>{{(isset($candidates->scac) ? $candidates->scac : 0) }}</td>
              <td>{{ (isset($candidates->stac) ? $candidates->stac : 0)}}</td>
              <td colspan="">{{(isset($candidates->genac) ? $candidates->genac : 0) +(isset($candidates->scac) ? $candidates->scac: 0) + (isset($candidates->stac) ? $candidates->stac : 0)}}</td>
            </tr>
            <tr>
              <th>2.</th>
              <th colspan="9">NO. of Contestants</th>
            </tr>
            <tr>
              <td colspan="2">NO. of Contestants in a Constituency</td>
              <td>1</td>
              <td>2</td>
              <td>3</td>
              <td>4</td>
              <td>5</td>
              <td>6-10</td>
              <td>11-15</td>
              <td>Above 15</td>
            </tr>
            <tr>
              <td colspan="2">NO Of Such CONSTITUENCIES
              </td>
              <td>{{$candidates->one}}</td>
              <td>{{$candidates->two}}</td>
              <td>{{$candidates->three}}</td>
              <td>{{$candidates->four}}</td>
              <td>{{$candidates->five}}</td>
              <td>{{$candidates->fiveten}}</td>
              <td>{{$candidates->tenfifteen}}</td>
              <td>{{$candidates->fifteen}}</td>
            </tr>
            <tr>
              <td colspan="8">Total Contestants in a Fray</td>
              <td colspan="2">{{$candidates->Total_Candidates}}</td>
            </tr>
            <tr>
              <td colspan="8">Average Contestants Per Constituency</td>
              <td colspan="2">{{$candidates->Avg}}</td>
            </tr>
            <tr>
              <td colspan="8">Minimum Contestants in a Constituency</td>
              <td colspan="2">{{$candidates->maxcnd}}</td>
            </tr>
            <tr>
              <td colspan="8">Maximum Contestants in a Constituency</td>
              <td colspan="2">{{$candidates->mincnd}}</td>
            </tr>
            <tr>
              <th>3.</th>
              <th colspan="9">Electors</th>
            </tr>
            <tr>
              <td colspan="6"></td>
              <td colspan="">Male</td>
              <td colspan="">Female</td>
              <td colspan="">Third Gender </td>
              <td colspan="">Total</td>
            </tr>
            <tr>
              <td>i.</td>
              <td  class="dev2" colspan="5">NO. OF ELECTORS(including service electors)</td>
              <td colspan="">{{$candidates->maleElectors}}</td>
              <td colspan="">{{$candidates->femaleElectors}}</td>
              <td colspan="">{{$candidates->thirdElectors}}</td>
              <td colspan="">{{$candidates->totalElectors}}</td>
            </tr>
            <tr>
              <td>ii.</td>
              <td colspan="5"> No. of Electors Who
              Voted</td>
              <td colspan="">{{$candidates->totalMaleVoters}}</td>
              <td colspan="">{{$candidates->totalFemaleVoters}}</td>
              <td colspan="">{{$candidates->totalOtherVoters}}</td>
              <td colspan="">{{$candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters}}</td>
            </tr>
            <tr>
              <td>iii. </td>
              <td colspan="5">Polling Percentage</td>
              <td colspan="">{{round($candidates->totalMaleVoters/$candidates->maleElectors * 100,2)}}</td>
              <td colspan="">{{round($candidates->totalFemaleVoters/$candidates->femaleElectors * 100,2)}}</td>
              <?php if($candidates->thirdElectors != 0)  { ?>
                <td colspan="">{{round($candidates->totalOtherVoters/$candidates->thirdElectors * 100,2)}}</td>
              <?php } else { ?>
                <td>0</td>
              <?php } ?>
              <td colspan="">{{round(($candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters)/$candidates->totalElectors * 100,2)}}</td>
            </tr>
            <th>4.</th>
            <td colspan="7"> No. Of valid VOTES (EVM Valid+postat Valid)</td>
            <td colspan="2">{{$candidates->totalEvmPostalvote}}</td>
          </tr>
          <tr>
            <th>5.</th>
            <td colspan="7"> NO. OF VOTES REJECTED (postal)</td>
            <td colspan="2">{{$candidates->rejectedpostalvote}}</td>
          </tr>
          <tr>
            <th>6.</th>
            <td colspan="7">NO. OF VOTES NOT RETRIEVED FROM <br> EVM,TEST VOTES, REJECTED VOTES DUE TO: <br>OTHER REASON AND NOTA VOTES </td>
            <?php $evmnota = ($candidates->notatotal - $candidates->notapostaltotal) ?>
            <td colspan="2">{{$candidates->votes_not_retreived_from_evm+$candidates->test_votes_49_ma
              +$candidates->rejected_votes_due_2_other_reason+ $evmnota}}</td>
          </tr>
          <tr>
            <th>7.</th>
            <td colspan="7"> NO. OF POLLING STATIONS
            </td>
            <td colspan="2">{{$candidates->totalpollingstation}}</td>
          </tr>
          <tr>
            <th>8.</th>
            <td colspan="7">AVERAGE NO. OF ELECTORS <br> PER POLLING STATION
            </td>
            <td colspan="2">{{round($candidates->totalElectors/$candidates->totalpollingstation,0)}}</td>
          </tr>
          <tr>
            <th>9.</th>
            <th colspan="9">Performance of Contesting Candidates</th>
          </tr>
          <tr>
            <td colspan="6"></td>
            <td>Male</td>
            <td>Female</td>
            <td>Third Gender</td>
            <td colspan="">Total</td>
          </tr>
          <tr>
            <td colspan="">i. </td>
            <td colspan="5">No. Of Contestants</td>
            <td>{{$candidates->totalnominatedmale}}</td>
            <td>{{$candidates->totalnominatedfemale}}</td>
            <td>{{$candidates->totalnominatedthird}}</td>
            <td>{{$candidates->totalnominatedmale+$candidates->totalnominatedfemale+$candidates->totalnominatedthird}}</td>
          </tr>
          <tr>
            <td>ii. </td>
            <td colspan="5">Elected</td>
            <td>{{$candidates->totalwinnermale}}</td>
            <td>{{$candidates->totalwinnerfemale}}</td>
            <td>{{$candidates->totalwinnerthird}}</td>
            <td colspan="">{{$candidates->totalwinnermale+$candidates->totalwinnerfemale+$candidates->totalwinnerthird}}</td>
          </tr>
          <tr>
            <td>iii.</td>
            <td colspan="5"> Forfeited Deposits</td>
            <td>{{$candidates->fdmale}}</td>
            <td>{{$candidates->fdfemale}}</td>
            <td>{{$candidates->fdthird}}</td>
            <td colspan="">{{$candidates->fdmale+$candidates->fdfemale+$candidates->fdthird}}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
</section>
@endsection