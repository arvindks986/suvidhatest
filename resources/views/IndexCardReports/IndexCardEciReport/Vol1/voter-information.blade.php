@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Voters Information')
@section('content')



<style>
  
  th{
    text-align: center;
  }
</style>

<section class="">
  <div class="container-fluid">
    <div class="row">
      <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
        <div class=" card-header">
          <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(10 - VOTERS INFORMATION)</h4></div> 
            <div class="col">
              <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
              </p>
              <p class="mb-0 text-right">
              <a href="voterInformationPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
              <a href="voterInformationXls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
              </p>
            </div>
          </div>
        </div>
        
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped" style="width: 100%;">
               <thead>
                <tr class="table-primary">
                  <th scope="col" rowspan="2" class="blc">State/UT</th>
                  <th scope="col" rowspan="2" class="blc">Constituency Type</th>
                  <th scope="col" rowspan="2" class="blc">Seats</th>
                  <th colspan="6">Electors</th>
                  <th colspan="7">Voters</th>
                  <th scope="col">Rejected <br> Votes <br>(Postal)</th>
                  <th scope="col"> Votes Rejected <br> / Votes Not <br> Retrived <br> From EVM</th>
                  <th scope="col">NOTA <br> Votes </th>
                  <th scope="col">Valid <br> Votes <br> Polled</th>
                  <th scope="col">Tendered Votes</th>
                </tr>


                <tr>
                    <th class="blc">Male</th>
                    <th class="blc">Female</th>
                    <th class="blc">Third Gender</th>
                    <th class="blc">Total</th>
                    <th class="blc">NRIs</th>
                    <th class="blc">Service</th>
                
                    <th class="blc">Male</th>
                    <th class="blc">Female</th>
                    <th class="blc">Third Gender</th>
                    <th class="blc">Postal</th>
                    <th class="blc">Total</th>
                    <th class="blc">NRIs  </th>
                    <th class="blc">Poll %</th>
                    <th class="blc"></th>
                    <th class="blc"></th>
                    <th class="blc"></th>
                    <th class="blc"></th>
                    <th class="blc"></th>


                </tr>
              </thead>

<tbody>
  
     <?php 

        $grandtotal= $grandseattotal= $grandemaletotal= $grandefemaletotal = $grandeothertotal = $grandestatetotal
        = $grandnrielectorstotal =$grandserviceelectorstotal = $grandgenmalevotertotal = $grandgenfemalevotertotal 
        = $grandgenothervotertotal = $grandpostaltotalstate = $grandtotalvotestate =$grandtotalnristate 
        = $grandpostalrejectedtotal = $grandvotesnotretrivedtotal = $grandnotavotetotal = $grandtendedvotetotal = $grandtestvote = $grandduetoother = 0; 

     ?>
  
  
      @foreach($voterarray as $row1 => $value1)
      <tr><td><b>{{$row1}}</b></td></tr>

      <?php $seattotal = $emaletotal= $efemaletotal = $eothertotal = $estatetotal = $nrielectorstotal 
      = $serviceelectorstotal = $genmalevotertotal = $genfemalevotertotal = $genothervotertotal = $postaltotalstate 
      = $totalvotestate = $totalnristate = $postalrejectedtotal = $votesnotretrivedtotal = $notavotetotal = $tendedvotetotal =  $testtotal = $duetototal =0; ?>

      @foreach($value1 as $row2 => $value2)

      
      

       <tr>
        
        <td></td> 
       

        <td>{{$value2['pc_type']}}</td>
        
        <td>{{$value2['seats']}}</td>
        <td>{{$value2['emale']}}</td>
        <td>{{$value2['efemale']}}</td>
        <td>{{$value2['eother']}}</td>
        <td>{{$value2['etotal']}}</td>
        <td>{{$value2['nrielectors']}}</td>
        <td>{{$value2['serviceelectors']}}</td>
        <td>{{$value2['general_male_voters']}}</td>
        <td>{{$value2['general_female_voters']}}</td>
        <td>{{$value2['general_other_voters']}}</td>
        <td>{{$value2['postaltotalvote']}}</td>
        <td>{{$value2['total_vote']}}</td>

        <td>{{$value2['voternri']}}</td>

        <td>{{round($value2['total_vote']/$value2['etotal']*100,2)}}</td>
        <td>{{$value2['postal_votes_rejected']}}</td>
        <td>{{$value2['votes_not_retreived_from_evm']+$value2['rejected_votes_due_2_other_reason']}}</td>
        <td>{{$value2['nota_vote']}}</td>
        
        <td>{{$value2['total_vote']-($value2['postal_votes_rejected']+$value2['votes_not_retreived_from_evm']+$value2['nota_vote']+$value2['rejected_votes_due_2_other_reason'])}}</td>
       <td>{{$value2['tended_votes']}}</td>
      

        

    </tr>

    <?php 

    $seattotal += $value2['seats']; 
    $emaletotal += $value2['emale'];
    $efemaletotal += $value2['efemale'];
    $eothertotal += $value2['eother'];
    $estatetotal += $value2['etotal'];

    $nrielectorstotal += $value2['nrielectors'];
    $serviceelectorstotal += $value2['serviceelectors'];
    $genmalevotertotal += $value2['general_male_voters'];
    $genfemalevotertotal += $value2['general_female_voters'];
    $genothervotertotal += $value2['general_other_voters'];

    $postaltotalstate += $value2['postaltotalvote'];
    $totalvotestate += $value2['total_vote'];
    $totalnristate += $value2['voternri'];
    $postalrejectedtotal += $value2['postal_votes_rejected'];
    $votesnotretrivedtotal  += $value2['votes_not_retreived_from_evm'];

    $notavotetotal += $value2['nota_vote'];
    $tendedvotetotal += $value2['tended_votes'];

    $testtotal += $value2['test_votes_49_ma'];
    $duetototal += $value2['rejected_votes_due_2_other_reason'];





    ?>

@endforeach

<tr>
    <td colspan="2"><b>State Total</b></td>
        <td><b>{{$seattotal}}</b></td>
        <td><b>{{$emaletotal}}</b></td>
        <td><b>{{$efemaletotal}}</b></td>
        <td><b>{{$eothertotal}}</b></td>
        <td><b>{{$estatetotal}}</b></td>
        <td><b>{{$nrielectorstotal}}</b></td>
        <td><b>{{$serviceelectorstotal}}</b></td>

        <td><b>{{$genmalevotertotal}}</b></td>
        <td><b>{{$genfemalevotertotal}}</b></td>
        <td><b>{{$genothervotertotal}}</b></td>
        <td><b>{{$postaltotalstate}}</b></td>
        <td><b>{{$totalvotestate}}</b></td>
        <td><b>{{$totalnristate}}</b></td>


        <td>{{round($totalvotestate/$estatetotal*100,2)}}</td>
        <td><b>{{$postalrejectedtotal}}</b></td>
        <td>{{$votesnotretrivedtotal+$duetototal}}</td>
        <td><b>{{$notavotetotal}}</b></td>
        <td><b>{{$totalvotestate-($postalrejectedtotal+$votesnotretrivedtotal+$notavotetotal+$duetototal)}}</b></td>
        <td><b>{{$tendedvotetotal}}</b></td>
        

</tr>

<?php 

    $grandseattotal += $seattotal;
    $grandemaletotal += $emaletotal;
    $grandefemaletotal += $efemaletotal;
    $grandeothertotal += $eothertotal;
    $grandestatetotal += $estatetotal;

    $grandnrielectorstotal += $nrielectorstotal;
    $grandserviceelectorstotal += $serviceelectorstotal;

    $grandgenmalevotertotal += $genmalevotertotal;
    $grandgenfemalevotertotal += $genfemalevotertotal;
    $grandgenothervotertotal += $genothervotertotal;

    $grandpostaltotalstate += $postaltotalstate;
    $grandtotalvotestate += $totalvotestate;
    $grandtotalnristate += $totalnristate;

    $grandpostalrejectedtotal += $postalrejectedtotal;
    $grandvotesnotretrivedtotal += $votesnotretrivedtotal;

    $grandnotavotetotal += $notavotetotal;
    $grandtendedvotetotal += $tendedvotetotal;

    $grandtestvote += $testtotal;
    $grandduetoother += $duetototal;


    
    
    


    ?>


@endforeach

<tr>
    <td colspan="2"><b>Grand Total</b></td>
        <td><b>{{$grandseattotal}}</b></td>
        <td><b>{{$grandemaletotal}}</b></td>
        <td><b>{{$grandefemaletotal}}</b></td>
        <td><b>{{$grandeothertotal}}</b></td>
        <td><b>{{$grandestatetotal}}</b></td>

       

        <td><b>{{$grandnrielectorstotal}}</b></td>
        <td><b>{{$grandserviceelectorstotal}}</b></td>
        <td><b>{{$grandgenmalevotertotal}}</b></td>
        <td><b>{{$grandgenfemalevotertotal}}</b></td>
        <td><b>{{$grandgenothervotertotal}}</b></td>
        <td><b>{{$grandpostaltotalstate}}</b></td>
        <td><b>{{$grandtotalvotestate}}</b></td>
        <td><b>{{$grandtotalnristate}}</b></td>


        <td>{{round($grandtotalvotestate/$grandestatetotal*100,2)}}</td>

        <td><b>{{$grandpostalrejectedtotal}}</b></td>
        <td><b>{{$grandvotesnotretrivedtotal + $grandduetoother}}</b></td>
        <td><b>{{$grandnotavotetotal}}</b></td>

        <td>{{$grandtotalvotestate-($grandpostalrejectedtotal+$grandvotesnotretrivedtotal+$grandnotavotetotal+$grandduetoother)}}</td>



        <td><b>{{$grandtendedvotetotal}}</b></td>
        

</tr>


</tbody>
           </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
