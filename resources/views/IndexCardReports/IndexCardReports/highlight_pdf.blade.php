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
<html>
  <head>
    <style>
    td {
    font-size: 14px !important;
    font-weight: 500 !important;
    text-align: left;
    padding: 6px 0px;
    text-transform: uppercase;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
    .left-al tr td{
    text-align: left;
    }
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;
    color: #212529;
    }
    .blc{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-spacing: 0px 8px;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    }
    .border{
    border: 1px solid #000;
    }
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 16px;
    font-weight: bold !important;
    text-align: left;
    padding: 7px;
    text-transform: uppercase;
    }
p{
  text-transform: uppercase;
}
.dev tr td{
  width: 9.7%;
  text-align: center;
}
    table{
    width: 100%;
    border-collapse: collapse;
    }
    </style>
  </head>
  <div class="bordertestreport">
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p> <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>  </p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><b>SECRETARIAT OF THE <br>ELECTION COMMISSION OF INDIA
            </b>
          <br><b>Nirvachan Sadan, Ashoka Road, New Delhi-110001</b></p>
        </td>
      </tr>
    </table>
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p style="font-size: 15px;"><b>4 - Highlights </b></p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><strong>State :</strong>{{$st->ST_NAME}}</p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table>
    <table><tr><td><p></p></td></tr>
  </table>

              <p><b>1. No. of Constituencies</b></p>
      

   <table class="table table-bordered dev" style="width: 100%;table-layout: fixed;">
           
            <tr>
              <td colspan="6"><b>Type Of Constituency</b></td>
              <td><b>GEN</b></td>
              <td><b>SC</b></td>
              <td><b>ST</b></td>
              <td colspan=""><b>Total</b></td>
            </tr>
            <tr>
              <td colspan="6"><b>No Of Constituencies</b></td>
             <td>{{(isset($candidates->genac) ? $candidates->genac : 0)}}</td>
              <td>{{(isset($candidates->scac) ? $candidates->scac : 0) }}</td>
              <td>{{ (isset($candidates->stac) ? $candidates->stac : 0)}}</td>
              <td colspan="">{{(isset($candidates->genac) ? $candidates->genac : 0) +(isset($candidates->scac) ? $candidates->scac: 0) + (isset($candidates->stac) ? $candidates->stac : 0)}}</td>
            </tr>
         
</table>
       
 
              <p><b>2. NO. of Contestants</b></p>
        

         <table class="table table-bordered dev">  

            <tr>
              <td colspan="2"><b>NO. of Contestants in a Constituency</b></td>
              <td><b>1</b></td>
              <td><b>2</b></td>
              <td><b>3</b></td>
              <td><b>4</b></td>
              <td><b>5</b></td>
              <td><b>6-10</b></td>
              <td><b>11-15</b></td>
              <td><b>Above 15</b></td>
            </tr>
            <tr>
              <td colspan="2"><b>NO Of Such CONSTITUENCIES
              </b></td>
              <td>{{$candidates->one}}</td>
              <td>{{$candidates->two}}</td>
              <td>{{$candidates->three}}</td>
              <td>{{$candidates->four}}</td>
              <td>{{$candidates->five}}</td>
              <td>{{$candidates->fiveten}}</td>
              <td>{{$candidates->tenfifteen}}</td>
              <td>{{$candidates->fifteen}}</td>
            </tr>

                     </table>


<table> 
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

</table>
            <p><b>3.Electors </b></p>

<table class="table table-bordered dev">

            <tr>
              <td colspan="6"></td>
              <td colspan=""><b>Male<b></td>
              <td colspan=""><b>Female</b></td>
              <td colspan=""><b>Third Gender</b> </td>
              <td colspan=""><b>Total</b></td>
            </tr>
            <tr>
              <td>i.</td>
              <td  class="dev2" colspan="5"><b>NO. OF ELECTORS</b>(Including Service Electors)</td>
              <td colspan="">{{$candidates->maleElectors}}</td>
              <td colspan="">{{$candidates->femaleElectors}}</td>
              <td colspan="">{{$candidates->thirdElectors}}</td>
              <td colspan="">{{$candidates->totalElectors}}</td>
            </tr>
            <tr>
              <td>ii.</td>
              <td colspan="5"> <b>No. of Electors Who
              Voted</b></td>
              <td colspan="">{{$candidates->totalMaleVoters}}</td>
              <td colspan="">{{$candidates->totalFemaleVoters}}</td>
              <td colspan="">{{$candidates->totalOtherVoters}}</td>
              <td colspan="">{{$candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters}}</td>
            </tr>
            <tr>
              <td>iii. </td>
              <td colspan="5"><b>Polling Percentage</b></td>
              <td colspan="">{{round($candidates->totalMaleVoters/$candidates->maleElectors * 100,2)}}</td>
              <td colspan="">{{round($candidates->totalFemaleVoters/$candidates->femaleElectors * 100,2)}}</td>
              <?php if($candidates->thirdElectors != 0)  { ?>
                <td colspan="">{{round($candidates->totalOtherVoters/$candidates->thirdElectors * 100,2)}}</td>
              <?php } else { ?>
                <td>0</td>
              <?php } ?>
              <td colspan="">{{round(($candidates->totalMaleVoters+$candidates->totalFemaleVoters+$candidates->totalOtherVoters)/$candidates->totalElectors * 100,2)}}</td>
            </tr>
</table>


        <table class="table">

            <tr>
            <td colspan="7"><b>4.  No. Of valid VOTES </b>(EVM Valid+postat Valid)</td>
            <td colspan="2">{{$candidates->totalEvmPostalvote}}</td>
          </tr>
          <tr>
            <td colspan="7"><b>5.  NO. OF VOTES REJECTED (postal)</b></td>
            <td colspan="2">{{$candidates->rejectedpostalvote}}</td>
          </tr>
          <tr>
            <td colspan="7"><b>6.  NO. OF VOTES NOT RETRIEVED FROM <br> EVM,TEST VOTES, REJECTED VOTES DUE TO: <br>OTHER REASON AND NOTA VOTES </b></td>
            <?php $evmnota = ($candidates->notatotal - $candidates->notapostaltotal) ?>
            <td colspan="2">{{$candidates->votes_not_retreived_from_evm+$candidates->test_votes_49_ma
              +$candidates->rejected_votes_due_2_other_reason+ $evmnota}}</td>
          </tr>
          <tr>
            <td colspan="7"><b>7.  NO. OF POLLING STATIONS</b>
            </td>
            <td colspan="2">{{$candidates->totalpollingstation}}</td>
          </tr>
          <tr>
            <td colspan="7"><b>8.  AVERAGE NO. OF ELECTORS <br> PER POLLING STATION </b>
            </td>
            <?php if($candidates->totalpollingstation != 0){ ?>
              <td colspan="2">{{round($candidates->totalElectors/$candidates->totalpollingstation,0)}}</td>
            <?php } else { ?>
              <td colspan="2">0</td>
            <?php } ?>
          </tr>

        </table>

                <p><b>9.Performance of Contesting Candidates</b></p>

        <table class="table table-bordered dev">
          
          <tr>
            <td colspan="6"></td>
            <td><b>Male</b></td>
            <td><b>Female</b></td>
            <td><b>Third Gender</b></td>
            <td colspan=""><b>Total</b></td>
          </tr>
          <tr>
            <td colspan=""><b>i. </b></td>
            <td colspan="5"><b>No. Of Contestants</b></td>
            <td>{{$candidates->totalnominatedmale}}</td>
            <td>{{$candidates->totalnominatedfemale}}</td>
            <td>{{$candidates->totalnominatedthird}}</td>
            <td>{{$candidates->totalnominatedmale+$candidates->totalnominatedfemale+$candidates->totalnominatedthird}}</td>
          </tr>
          <tr>
            <td><b>ii. </b></td>
            <td colspan="5"><b>Elected</b></td>
            <td>{{$candidates->totalwinnermale}}</td>
            <td>{{$candidates->totalwinnerfemale}}</td>
            <td>{{$candidates->totalwinnerthird}}</td>
            <td colspan="">{{$candidates->totalwinnermale+$candidates->totalwinnerfemale+$candidates->totalwinnerthird}}</td>
          </tr>
          <tr>
            <td><b>iii. </b></td>
            <td colspan="5"><b> Forfeited Deposits</b></td>
            <td>{{$candidates->fdmale}}</td>
            <td>{{$candidates->fdfemale}}</td>
            <td>{{$candidates->fdthird}}</td>
            <td colspan="">{{$candidates->fdmale+$candidates->fdfemale+$candidates->fdthird}}</td>
          </tr>
        </table>
    
</div>
</html>