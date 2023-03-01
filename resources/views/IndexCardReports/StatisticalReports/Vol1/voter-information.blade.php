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
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(10 - VOTERS INFORMATION)</h4></div> 
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
                  <th scope="col">State/UT</th>
                  <th scope="col">Constituency Type</th>
                  <th scope="col">Seats</th>
                  <th colspan="6">Electors</th>
                  <th colspan="7">Voters</th>
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
              </thead>
<tbody>
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


</tbody>
           </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
