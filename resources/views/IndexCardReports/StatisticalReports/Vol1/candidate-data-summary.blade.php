@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'PC Wise Voters Turn Out')
@section('content')

<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>6 - Candidate Data Summary on Nominations , Rejections,Withdrawals and Deposits Forfeited</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="statewisecandidatedatasummary_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="statewisecandidatedatasummary_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">

            <table class="table table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th colspan="2"></th>
                        <th colspan="4">NOMINATIONS FILED</th>
                        <th colspan="4">NOMINATIONS REJECTED</th>
                        <th colspan="4">NOMINATIONS WITHDRAWN</th>
                        <th colspan="4">CONTESTING CANDIDATES</th>
                        <th colspan="4">DEPOSIT FORFEITED </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>

                        <td class="blc"  colspan="2">State/UT</td>
                        <td class="blc">Male</td>
                        <td class="blc">Women</td>
                        <td class="blc">Third Gender</td>
                        <td class="blc">Total</td>
                        <td class="blc">Male</td>
                        <td class="blc">Women</td>
						<td class="blc">Third Gender</td>
                        <td class="blc">Total</td>
                        <td class="blc">Male</td>
                        <td class="blc">Women</td>
						<td class="blc">Third Gender</td>
                        <td class="blc">Total</td>
                        <td class="blc">Male</td>
                        <td class="blc">Women</td>
						<td class="blc">Third Gender</td>
                        <td class="blc">Total</td>
                        <td class="blc">Male</td>
                        <td class="blc">Women</td>
						<td class="blc">Third Gender</td>
                        <td class="blc">Total</td>
                    </tr>
                    
                   
                    <?php $allcnomfdtotal = $allcnomfdother = $allcnomfdfemale = $allcnomfdmale = $allcnomcototal = $allcnomcother = $allcnomcofemale = $allcnomcomale = $allcnomwtotal = $allcnomwother = $allcnomwfemale = $allcnomwmale = $allcnomrall = $allcnomrother = 
                            $allcnomrfemale = $allcnomrmale = $allCandNomall = $allCandNomOther = $allCandNomFemale = $allcandNomMale = $alltotSeat = 0;  ?>
                    
					@foreach($dataArray as $key => $data)
					 <tr>
                        <th colspan="22">{{$key}}</th>
                    </tr> 

                     <tr style="white-space: nowrap;">
                        <td colspan=""><b> Constituency Type</b></td>
                        <td colspan="">No. of Seats</td>
                    </tr>

	<?php $cnomfdtotal = $cnomfdother = $cnomfdfemale = $cnomfdmale = $cnomcototal = $cnomcother = $cnomcofemale = $cnomcomale = $cnomwtotal = $cnomwother = $cnomwfemale = $cnomwmale = $cnomrall = $cnomrother = 
                                $cnomrfemale = $cnomrmale = $CandNomall = $CandNomOther = $CandNomFemale = $candNomMale = $totSeat = 0; 

?>

					@foreach($data as $key => $raw)

                    <tr>
                        <td colspan=""><b>{{$raw['category']}}</b></td>
						<td>{{$raw['total_pc']}}</td>
                        <td>{{$raw['nom_male']}}</td>
                        <td>{{$raw['nom_female']}}</td>
                        <td>{{$raw['nom_third']}}</td>
                        <td>{{$raw['nom_total']}}</td>
                        <td>{{$raw['rej_male']}}</td>
                        <td>{{$raw['rej_female']}}</td>
                        <td>{{$raw['rej_third']}}</td>
                        <td>{{$raw['rej_total']}}</td>
                        <td>{{$raw['with_male']}}</td>
                        <td>{{$raw['with_female']}}</td>
                        <td>{{$raw['with_third']}}</td>
                        <td>{{$raw['with_total']}}</td>
                        <td>{{$raw['cont_male']}}</td>
                        <td>{{$raw['cont_female']}}</td>
                        <td>{{$raw['cont_third']}}</td>
                        <td>{{$raw['cont_total']}}</td>
                        <td>{{$raw['fdmale']}}</td>
                        <td>{{$raw['fdfemale']}}</td>
                        <td>{{$raw['fdthird']}}</td>
                        <td>{{$raw['fd']}}</td>
                    </tr>
										
					<?php $totSeat 		+= $raw['total_pc'];
					$candNomMale 		+= $raw['nom_male'];
					$CandNomFemale 		+= $raw['nom_female'];
					$CandNomOther  		+= $raw['nom_third'];
					$CandNomall   		+= $raw['nom_total'];
					$cnomrmale 			+= $raw['rej_male'];
					$cnomrfemale 		+= $raw['rej_female'];
					$cnomrother  		+= $raw['rej_third'];
					$cnomrall   		+= $raw['rej_total'];
					$cnomwmale  		+= $raw['with_male'];
					$cnomwfemale 		+= $raw['with_female'];
					$cnomwother 		+= $raw['with_third'];
					$cnomwtotal 		+= $raw['with_total'];
					$cnomcomale 		+= $raw['cont_male'];
					$cnomcofemale 		+= $raw['cont_female'];
					$cnomcother 		+= $raw['cont_third'];
					$cnomcototal 		+= $raw['cont_total'];
					$cnomfdmale			+= $raw['fdmale'];
					$cnomfdfemale 		+= $raw['fdfemale'];
					$cnomfdother 		+= $raw['fdthird'];
					$cnomfdtotal 		+= $raw['fd'];
					?>

				@endforeach
				
				<tr style="font-weight:bold;">
					<td><b>Total</td>
					<td><b>{{$totSeat}}</b></td>
					<td><b>{{$candNomMale}}</b></td>
					<td><b>{{$CandNomFemale}}</b></td>
					<td><b>{{$CandNomOther}}</b></td>
					<td><b>{{$CandNomall}}</b></td>
					<td><b>{{$cnomrmale}}</b></td>
					<td><b>{{$cnomrfemale}}</b></td>
					<td><b>{{$cnomrother}}</b></td>
					<td><b>{{$cnomrall}}</b></td>
					<td><b>{{$cnomwmale}}</b></td>
					<td><b>{{$cnomwfemale}}</b></td>
					<td><b>{{$cnomwother}}</b></td>
					<td><b>{{$cnomwtotal}}</b></td>
					<td><b>{{$cnomcomale}}</b></td>
					<td><b>{{$cnomcofemale}}</b></td>
					<td><b>{{$cnomcother}}</b></td>
					<td><b>{{$cnomcototal}}</b></td>
					<td><b>{{$cnomfdmale}}</b></td>
					<td><b>{{$cnomfdfemale}}</b></td>
					<td><b>{{$cnomfdother}}</b></td>
					<td><b>{{$cnomfdtotal}}</b></td>
				</tr>
				
				
				<?php 
								$alltotSeat 			+= $totSeat;
								$allcandNomMale 		+= $candNomMale;
								$allCandNomFemale 		+= $CandNomFemale;
								$allCandNomOther  		+= $CandNomOther;
								$allCandNomall   		+= $CandNomall;
								$allcnomrmale 			+= $cnomrmale;
								$allcnomrfemale 		+= $cnomrfemale;
								$allcnomrother  		+= $cnomrother;
								$allcnomrall   			+= $cnomrall;
								$allcnomwmale  			+= $cnomwmale;
								$allcnomwfemale 		+= $cnomwfemale;
								$allcnomwother 			+= $cnomwother;
								$allcnomwtotal 			+= $cnomwtotal;
								$allcnomcomale 			+= $cnomcomale;
								$allcnomcofemale 		+= $cnomcofemale;
								$allcnomcother 			+= $cnomcother;
								$allcnomcototal 		+= $cnomcototal;
								$allcnomfdmale			+= $cnomfdmale;
								$allcnomfdfemale 		+= $cnomfdfemale;
								$allcnomfdother 		+= $cnomfdother;
								$allcnomfdtotal 		+= $cnomfdtotal;
				?>
				
				
				@endforeach
				<tr height ="20">
				</tr>
                 <tr style="font-weight:bold;">
					<td><b>Grand Total</b></td>
					<td><b>{{$alltotSeat}}</b></td>
					<td><b>{{$allcandNomMale}}</b></td>
					<td><b>{{$allCandNomFemale}}</b></td>
					<td><b>{{$allCandNomOther}}</b></td>
					<td><b>{{$allCandNomall}}</b></td>
					<td><b>{{$allcnomrmale}}</b></td>
					<td><b>{{$allcnomrfemale}}</b></td>
					<td><b>{{$allcnomrother}}</td>
					<td><b>{{$allcnomrall}}</b></td>
					<td><b>{{$allcnomwmale}}</b></td>
					<td><b>{{$allcnomwfemale}}</b></td>
					<td><b>{{$allcnomwother}}</b></td><b>
					<td><b>{{$allcnomwtotal}}</b></td>
					<td><b>{{$allcnomcomale}}</b></td>
					<td><b>{{$allcnomcofemale}}</b></td>
					<td><b>{{$allcnomcother}}</b></td>
					<td><b>{{$allcnomcototal}}</b></td>
					<td><b>{{$allcnomfdmale}}</b></td>
					<td><b>{{$allcnomfdfemale}}</b></td>
					<td><b>{{$allcnomfdother}}</b></td>
					<td><b>{{$allcnomfdtotal}}</b></td>
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