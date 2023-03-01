<html>
  <head>
      <style>

        @page { sheet-size: A4-L; }
        @page bigger { sheet-size: 420mm 370mm; }
        @page toc { sheet-size: A4; }
        @page {
            header: page-header;
            footer: page-footer;
        }

        td {
        font-size: 12px !important;
        font-weight: 500 !important;
        text-align: center;
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
    font-size: 12px;
    font-weight: bold !important;
    }
    
    table{
    width: 100%;
    }
    .border th{
border-collapse: collapse;
      border-bottom: 1px solid #000; 
    } 
    .border td{
border-collapse: collapse;
      border-bottom: 1px solid #000; 
    }
    th {
  text-decoration: underline;
    font-size: 12px;
    font-weight: bold !important;
    }

    table{
    width: 100%;
    }
      </style>
  </head>
  <div class="bordertestreport">
      <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, Elections,2019 ( 17 LOK SABHA )</strong></p></td>
            </tr>
             
  </table>

<table class="border">
  <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 20px !important; text-transform: uppercase;"><strong>15 - Assembly Segment Wise Information of Electors </strong></p>
                  </td>
              </tr>

</table>
<br>

  <table class="">
      <?php  if (verifyreport(15) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>


  </table>

  <table class="table border" style="width: 100%;">
     <thead>

     
            <tr>

               <th rowspan="4" style="border-right: 1px solid #000; text-align: center;">AC NAME </th>
               <th colspan="12" style="text-align: center;">Electors  </th>
               <th rowspan="4" style="border-left: 1px solid #000; text-align: center;">Votes <br>Polled On <br>EVM </th>
            </tr>

            <tr>
              <th colspan="8" style="text-align: center;">General</th>
              <th colspan="3" style="text-align: center;">Service</th>
             <th rowspan="3" style="text-align: center;"><b>Total</b></th>


            </tr>

              <tr>
              <th colspan="4" style="border-right: 1px solid #000; text-align: center;">Other Than NRI</th>
              <th colspan="4" style="border-right: 1px solid #000; text-align: center;">NRI</th>
               <th rowspan="2" style="text-align: center;">Male</th>
              <th rowspan="2" style="text-align: center;">Female</th>
              <th rowspan="2" style="border-right: 1px solid #000; text-align: center;">Total</th>

            </tr>

              <tr>
              <th style="text-align: center;">Male</th>
              <th style="text-align: center;">Female</th>
              <th style="text-align: center;">Third Gender</th>
              <th style="border-right: 1px solid #000; text-align: center;">Total</th>

               <th style="text-align: center;">Male</th>
              <th style="text-align: center;">Female</th>
              <th style="text-align: center;">Third Gender</th>
              <th style="border-right: 1px solid #000; text-align: center;">Total</th>




            </tr>

        </thead>
        <?php

          $grandtotalMalegen = $grandtotalfemalegen = $grandtotalothergen = $grandtotalgen = $grandtotalmalenri=
          $grandtotalfemalenri = $grandtotalnriother = $grandtotalnri = $grandtotalsermale = $grandtotalserfemale
          = $grandtotalser = $grandtotalelector = $grandtotalvoteevm =  0;
        ?>

        <?php //echo "<pre>"; print_r($datanew); die; ?>
         @foreach ($datanew as $key => $value1)

           <?php //echo "<pre>"; print_r($value1); die; ?>

            <?php
               $statetotalvoteevm = $statetotalelector = $statetotalser =
               $statetotalserfemale = $statetotalsermale = $statetotalnri = $statetotalnriother =
               $statetotalfemalenri = $statetotalmalenri = $statetotalgen= $statetotalothergen =
               $statetotalfemalegen = $statetotalMalegen  = 0;
             ?>
         <tr><th style="text-align: left;border-right: 1px solid #000;" colspan="1"><b>State: {{$key}}</b><br/> <b></b></th>
		 <th colspan="13"></th>
		 </tr>
          @foreach ($value1 as $key2 => $value2)

        <tbody>

          <tr style="height: 10px;"><th colspan="14"></th></tr>



          <tr><th style="text-align: left;border-right: 1px solid #000;" colspan="1"><br/> <b>PC:- {{$key2}}</b></th><th colspan="13"></th></tr>
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
           <th>PC:- {{$key2}}</th>
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
            <b>Grand Total:</b>

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


 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>



<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 <?php if (verifyreport(15) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>



</html>
