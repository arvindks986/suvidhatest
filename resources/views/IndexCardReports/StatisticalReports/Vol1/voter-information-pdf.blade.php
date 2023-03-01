<html>
  <head>
      <style>
        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #000 !important;
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
    color: #212529;

    }

   
    .border{
    border: 1px solid #000;
    }
    th {
    background: #eff2f4;
    color: #000 !important;
    font-size: 12px;
    font-weight: bold !important;
    }
    
    table{
    width: 100%;
    }
      </style>
  </head>
  <div class="bordertestreport">
      <table class="border">
          <tr>
                <td style="text-align: left;">
                    <p> <img src="img/Cyber-Security-Logo.png" class="img-responsive" style="width:100px;" alt="">  </p>
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
             <p style="font-size: 15px;"><b>10 - VOTERS INFORMATION</b></p>

          </td>
          <td style="text-align: right;">
              <p style="float: right;width: 100%;font-size: 15px;"><strong>State :</strong> All India </p>
          </td>
      </tr>

       <tr>
    <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
    <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
  </tr>


  </table> 


                <table class="table table-bordered">
               
                <tr>
                  <th scope="col">State/UT</th>
                  <th scope="col">Constituency Type</th>
                  <th scope="col">Seats</th>
                  <th colspan="6" style="text-decoration: underline;">Electors</th>
                  <th colspan="7" style="text-decoration: underline;">Voters</th>
                  <th scope="col">Rejected Votes(Postal)</th>
                  <th scope="col">Votes Not Retrived From EVM</th>
                  <th scope="col">NOTA Votes </th>
                  <th scope="col">Valid Votes Polled</th>
                  <th scope="col">Tendered Votes</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Others</th>
                    <th>Total</th>
                    <th>NRI</th>
                    <th>Service</th>
                
                    <th>Male</th>
                    <th>Female</th>
                    <th>Others</th>
                    <th>Postal</th>
                    <th>Total</th>
                    <th>NRI</th>
                    <th>Poll %</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					 </tr>
              <?php //echo '<pre>'; print_r($voterquery); die;
	 //grandtotal
	$totalseats =  $totalemale = $totalefemale = $totaleother = $totaletotal = $totalenri = $totaleser = $totalvmale = $totalvfemale = $totalvother = $totalvpostal = $totalvtotal = $totalvnri = $totalpollpercent = $totalrejectedvote = $totalnotretrived = $totalnota= $totalvalidpollvotes= $totaltenderedvotes = $totalpoll = 0;
	$tpollpercent=0;$telectors=0;$tvoters=0;
      ?>
      @foreach ($voterquery as $row)
      <?php 
     
      $telectors=($row->etotal+$row->nri+$row->ser);
      $tvoters=($row->totalpostal+$row->voternri+$row->totalvotes);
      if($telectors!=0){
		  $tpollpercent=round((($tvoters/$telectors)*100),2);
		}


     //$totalseats+=$row->seats;
      $totalemale+=$row->emale;
      $totalefemale+=$row->efemale;
      $totaleother+=$row->eother;
      $totaletotal+=$row->etotal;
      //$totalseats+=$row->seats;
      $totalenri+=$row->nri;
      $totaleser+=$row->ser;
      $totalvmale+=$row->votermale;
      $totalvfemale+=$row->voterfemale;
      $totalvother+=$row->voterother;
      $totalvpostal+=$row->totalpostal;
      $totalvtotal+=$row->totalvotes;
      $totalvnri+=$row->voternri;
      $totalpoll+=$tpollpercent;

      $totalrejectedvote+=$row->rejectedvotes;
      $totalnotretrived+=$row->notretrivedfromevm;
      $totalnota+=$row->notavotes;
      $totalvalidpollvotes+=$row->totalvalidvote;
      $totaltenderedvotes+=$row->tenderedvotes;
      ?>

    <tr>
        <td>{{$stname}}</td>
        <td>{{$row->PC_TYPE}}</td>
        <?php $seat=0; 
        ($row->PC_TYPE=='GEN')?$seat=$row->GENSEATS:0;
        ($row->PC_TYPE=='SC')?$seat=$row->SCSEATS:0;
        ($row->PC_TYPE=='ST')?$seat=$row->STSEATS:0;
        $totalseats+=$row->GENSEATS+$row->SCSEATS+$row->STSEATS;
        ?>

         <td>{{$seat}}</td>
        
        <td>{{$row->emale}}</td>
        <td>{{$row->efemale}}</td>
        <td>{{$row->eother}}</td>
        <td>{{$row->etotal}}</td>
        <td>{{$row->nri}}</td>
        <td>{{$row->ser}}</td>
        <td>{{$row->votermale}}</td>
        <td>{{$row->voterfemale}}</td>
        <td>{{$row->voterother}}</td>
        <td>{{$row->totalpostal}}</td>
        <td>{{$row->totalvotes}}</td>
        <td>{{$row->voternri}}</td>

        <td>{{$tpollpercent}}</td>

        <td>{{$row->rejectedvotes}}</td>
        <td>{{$row->notretrivedfromevm}}</td>
        <td>{{$row->notavotes}}</td>
        <td>{{$row->totalvalidvote}}</td>
        <td>{{$row->tenderedvotes}}</td>
       

    </tr>

@endforeach

<tr>
    <td><b>Grand Total</b></td>
    <td></td>
        <td>{{$totalseats}}</td>
        <td>{{$totalemale}}</td>
        <td>{{$totalefemale}}</td>
        <td>{{$totaleother}}</td>
        <td>{{$totaletotal}}</td>

       

        <td>{{$totalenri}}</td>
        <td>{{$totaleser}}</td>
        <td>{{$totalvmale}}</td>
        <td>{{$totalvfemale}}</td>
        <td>{{$totalvother}}</td>
        <td>{{$totalvpostal}}</td>
        <td>{{$totalvtotal}}</td>
        <td>{{$totalvnri}}</td>


        <td>{{$totalpoll}}</td>
        <td>{{$totalrejectedvote}}</td>
        <td>{{$totalnotretrived}}</td>
        <td>{{$totalnota}}</td>
        <td>{{$totalvalidpollvotes}}</td>
        <td>{{$totaltenderedvotes}}</td>
        

</tr>



            </table>
          </div>
