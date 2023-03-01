@extends('layouts.app')
@section('content')

<div class="headerreport">
        							<div class="container">
        									<div class="bordertestreport">
        										<img src="./images/Cyber-Security-Logo.png" class="img-responsivesreport" alt="">
        									
					  <h2>DETAILED RESULTS</h2>
        													
        					   

        					   <table id="example" class="table table-striped table-bordered" style="width:100%;">
                <thead>

                        <p class="contituency">CONSTITUENCY <span> 1._ Hachek (ST)</span></p>
                        <p class="contituency">TOTAL ELECTORS <span> 303023</span></p>

                    <tr>
                        <th>Candidates</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Category</th>
                        <th>Party</th>
                        <th>Symbol</th>
                        <th>General</th>
                        <th>Postal</th>
                        <th>Total</th>
                        <th>Pol	</th>
                    </tr>
				</thead>
                <tbody>

				@foreach ($candidates as $candidate) 
                    <tr>
                    	<td>{{ $candidate->cand_name }}</td>
                    	<td>{{ $candidate->cand_gender }}</td>
                    	<td>{{ $candidate->cand_age }}</td>
                    	<td>{{ $candidate->cand_category }}</td>
						<td>{{ $candidate->party_habbre }}</td>
                    	<td>{{ $candidate->total_vote }}</td>
                    	<td>{{ $candidate->pc_type }}</td>
                    	<td>{{ $candidate->postalballot_vote }}</td>
                    	<td>{{ $candidate->total_vote-$candidate->postalballot_vote }}</td>
                    	<td>{{ $candidate->cand_name }}</td>
                    </tr>
                  @endforeach	

						<tr>
							<th>TurnOut</th>
								<td>--</td>
								<td>--</td>
						
								<th>Total</th>
								<td colspan="3"></td>
								
								<td>1212</td>
								<td>21</td>
								<td>32535</td>
								
							</tr>

					





                </tbody>
                
           </table>



        			





        					






						</div>

				</div>
		</div>

@endsection



