@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Assembly Segment Wise Information of Electors')
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
           <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(15 - Assembly Segment Wise Information of Electors)</h4></div>
             <div class="col">



              </p>
               <p class="mb-0 text-right">
                      <a href="AssemblySegmentWiseInformationElectorsPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
       <a href="AssemblySegmentWiseInformationElectorsXLS" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px;     width: 61px !important; display: table-row;"></a>
               </p>
             </div>


           </div>
     </div>

<div class="card-body">

    <div class="table-responsive">
      <table class="table table-bordered table-striped" style="width: 100%;">
         <thead>

            <tr class="table-primary">

               <th rowspan="4">AC NAME </th>
               <th colspan="12">Electors  </th>
               <th rowspan="4">Votes Polled On EVM </th>
            </tr>

            <tr>
              <th colspan="8">General</th>
              <th colspan="3">Service</th>
             <th rowspan="3"><b>Total</b></th>


            </tr>

              <tr>
              <th colspan="4">Other Than NRI</th>
              <th colspan="4">NRI</th>
               <th rowspan="2">Male</th>
              <th rowspan="2">Female</th>
              <th rowspan="2">Total</th>

            </tr>

              <tr>
              <th>Male</th>
              <th>Female</th>
              <th>Third Gender</th>
              <th>Total</th>

               <th>Male</th>
              <th>Female</th>
              <th>Third Gender</th>
              <th>Total</th>




            </tr>


            </thead>
            <?php

              $grandtotalMalegen = $grandtotalfemalegen = $grandtotalothergen = $grandtotalgen = $grandtotalmalenri=
              $grandtotalfemalenri = $grandtotalnriother = $grandtotalnri = $grandtotalsermale = $grandtotalserfemale
              = $grandtotalser = $grandtotalelector = $grandtotalvoteevm =  0;
            ?>

            
             @foreach ($datanew as $key => $value1)

               

                <?php
                   $statetotalvoteevm = $statetotalelector = $statetotalser =
                   $statetotalserfemale = $statetotalsermale = $statetotalnri = $statetotalnriother =
                   $statetotalfemalenri = $statetotalmalenri = $statetotalgen= $statetotalothergen =
                   $statetotalfemalegen = $statetotalMalegen  = 0;
                 ?>
             <tr><th style="text-align: left;" colspan="1"><b>State: {{$key}}</b><br/> <b></b></th></tr>
              @foreach ($value1 as $key2 => $value2)

            <tbody>

              <tr style="height: 10px;"></tr>



              <tr><th style="text-align: left;" colspan="1"><b></b><br/> <b>PC:- {{$key2}}</b></th></tr>
             <?php
                $totalvoteevm = $totalelector = $totalser =
                $totalserfemale = $totalsermale = $totalnri = $totalnriother =
                $totalfemalenri = $totalmalenri = $totalgen= $totalothergen =
                $totalfemalegen = $totalMalegen  = 0;
              ?>
             @foreach($value2->ac_name as $kkey => $vvalue)
              <tr>
               <td>{{$vvalue->name}}</td>
               <td>{{$vvalue->gen_electors_male}}</td>
               <td>{{$vvalue->gen_electors_female}}</td>
               <td>{{$vvalue->gen_electors_other}}</td>
               <td>{{$vvalue->gen_total}}</td>
               <td>{{$vvalue->nri_male_electors}}</td>
               <td>{{$vvalue->nri_female_electors}}</td>
               <td>{{$vvalue->nri_third_electors}}</td>
               <td>{{$vvalue->nri_total}}</td>
               <td>{{$vvalue->service_male_electors}}</td>
               <td>{{$vvalue->service_female_electors}}</td>
               <td>{{$vvalue->ser_total}}</td>
               <td>{{$vvalue->total_elector}}</td>
               <td>{{$vvalue->votes_total_evm_all}}</td>
               </tr>

               <?php


                    $totalMalegen += $vvalue->gen_electors_male;
                    $totalfemalegen += $vvalue->gen_electors_female;
                    $totalothergen += $vvalue->gen_electors_other;
                    $totalgen += $vvalue->gen_total;
                    $totalmalenri += $vvalue->nri_male_electors;
                    $totalfemalenri += $vvalue->nri_female_electors;
                    $totalnriother += $vvalue->nri_third_electors;
                    $totalnri += $vvalue->nri_total;
                    $totalsermale += $vvalue->service_male_electors;
                    $totalserfemale += $vvalue->service_female_electors;
                    $totalser += $vvalue->ser_total;
                    $totalelector += $vvalue->total_elector;
                    $totalvoteevm += $vvalue->votes_total_evm_all;


               ?>


               @endforeach
	<?php $notretrive = \App\models\Admin\VoterModel::get_notretrive($value2->st_code,$value2->pc_no); ?>

				<tr>
				   <td>Votes not retrieved from EVM</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>{{$notretrive[0]->votes_not_retreived}}</td>
				 </tr>
			  <?php $totalvoteevm += $notretrive[0]->votes_not_retreived; ?>


			<?php $rejecteddue = \App\models\Admin\VoterModel::get_rejecteddue($value2->st_code,$value2->pc_no); ?>
				<tr>
				   <td>Rejected due to other reason</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>{{$rejecteddue[0]->rejected_votes_due}}</td>
				 </tr>
			  <?php $totalvoteevm += $rejecteddue[0]->rejected_votes_due; ?>



              <?php if($value2->st_code == 'S09' && in_array($value2->pc_no, [1,2,3]) ){ 
			  
			  $migratedata = \App\models\Admin\VoterModel::get_migrante($value2->st_code,$value2->pc_no);
			  
			  ?>

				<tr>
               <td>DelhiUdhampurJammu</td>
				<td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
				   <td>0</td>
               <td>{{$migratedata[0]->migrate_votes}}</td>
             </tr>
			  <?php 			  
			  $totalvoteevm += $migratedata[0]->migrate_votes;
			  
			  } ?>

              <?php

              $statetotalMalegen += $totalMalegen;
              $statetotalfemalegen += $totalfemalegen;
              $statetotalothergen += $totalothergen;
              $statetotalgen += $totalgen;
              $statetotalmalenri += $totalmalenri;
              $statetotalfemalenri += $totalfemalenri;
              $statetotalnriother += $totalnriother;
              $statetotalnri += $totalnri;
              $statetotalsermale += $totalsermale;
              $statetotalserfemale += $totalserfemale;
              $statetotalser += $totalser;
              $statetotalelector += $totalelector;
              $statetotalvoteevm += $totalvoteevm

               ?>


              <tr>
               <td><b>PC:- {{$key2}}</b></td>
               <td><b>{{$totalMalegen}}</b></td>
               <td><b>{{$totalfemalegen}}</b></td>
               <td><b>{{$totalothergen}}</b></td>
               <td><b>{{$totalgen}}</b></td>
               <td><b>{{$totalmalenri}}</b></td>
               <td><b>{{$totalfemalenri}}</b></td>
               <td><b>{{$totalnriother}}</b></td>
               <td><b>{{$totalnri}}</b></td>
               <td><b>{{$totalsermale}}</b></td>
               <td><b>{{$totalserfemale}}</b></td>
               <td><b>{{$totalser}}</b></td>
               <td><b>{{$totalelector}}</b></td>
               <td><b>{{$totalvoteevm}}</b></td>



             </tr>
              @endforeach

              <?php

                $grandtotalMalegen += $statetotalMalegen;
                $grandtotalfemalegen += $statetotalfemalegen;
                $grandtotalothergen += $statetotalothergen;
                $grandtotalgen += $statetotalgen;
                $grandtotalmalenri += $statetotalmalenri;
                $grandtotalfemalenri += $statetotalfemalenri;
                $grandtotalnriother += $statetotalnriother;
                $grandtotalnri += $statetotalnri;
                $grandtotalsermale += $statetotalsermale;
                $grandtotalserfemale += $statetotalserfemale;
                $grandtotalser += $statetotalser;
                $grandtotalelector += $statetotalelector;
                $grandtotalvoteevm += $statetotalvoteevm;


               ?>



             <tr>
              <th><b>{{$key}}</b></th>

              <td><b>{{$statetotalMalegen}}</b></td>
              <td><b>{{$statetotalfemalegen}}</b></td>
              <td><b>{{$statetotalothergen}}</b></td>
              <td><b>{{$statetotalgen}}</b></td>

              <td><b>{{$statetotalmalenri}}</b></td>
              <td><b>{{$statetotalfemalenri}}</b></td>
              <td><b>{{$statetotalnriother}}</b></td>
              <td><b>{{$statetotalnri}}</b></td>

              <td><b>{{$statetotalsermale}}</b></td>
              <td><b>{{$statetotalserfemale}}</b></td>
              <td><b>{{$statetotalser}}</b></td>
              <td><b>{{$statetotalelector}}</b></td>
              <td><b>{{$statetotalvoteevm}}</b></td>

            </tr>
             @endforeach

             <tr>
              <th>
                <b>Grand Total</b>

              </th>

              <td><b>{{$grandtotalMalegen}}</b></td>
              <td><b>{{$grandtotalfemalegen}}</b></td>
              <td><b>{{$grandtotalothergen}}</b></td>
              <td><b>{{$grandtotalgen}}</b></td>

              <td><b>{{$grandtotalmalenri}}</b></td>
              <td><b>{{$grandtotalfemalenri}}</b></td>
              <td><b>{{$grandtotalnriother}}</b></td>
              <td><b>{{$grandtotalnri}}</b></td>

              <td><b>{{$grandtotalsermale}}</b></td>
              <td><b>{{$grandtotalserfemale}}</b></td>
              <td><b>{{$grandtotalser}}</b></td>
              <td><b>{{$grandtotalelector}}</b></td>
              <td><b>{{$grandtotalvoteevm}}</b></td>

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
