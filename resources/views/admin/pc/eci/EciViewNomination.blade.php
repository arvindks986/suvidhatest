@extends('admin.layouts.pc.dashboard-theme')
@section('content')
 <link rel="stylesheet" href="{{ asset('theme/css/nomination.css') }}" id="theme-stylesheet">

<main role="main" class="inner cover mb-3">
  @forelse ($EciViewNomination as $key=>$listdata)

	  <section class="mt-5">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
				<div class="card">
					<div class="card-header d-flex align-items-center">
					  <h4>View Candidate Information</h4>
					</div>
					<div class="card-body">
					<div class="row d-flex align-items-center profileForm">
						<div class="col-md-3">
							<div class="avatar-upload">
								 
								@if($listdata->cand_image != '' )
									<div class="avatar-preview">
										<div id="imagePreview">
											<img src="{{url($listdata->cand_image)}}" height="180" width="180"/>
											
											
										</div>
									</div>
								@else
									<div class="avatar-preview">
										<div id="imagePreview"></div>
									</div>
								@endif
								 
							</div>
							 
						</div>

						<div class="col-md-9"> 

						<div class="form-group row">
						 
						  <div class="col-md-4 col-sm-6">
									<label for="">Candidate Name in English </label>						  
								<div class="" style="width:100%;">{{$listdata->cand_name}}</div>
							</div> 
							<div class="col-md-4 col-sm-6">
									<label for="">Candidate Name in Hindi </label>						  
								<div class="" style="width:100%;">{{$listdata->cand_hname}}</div>
							</div>
							<div class="col-md-4 col-sm-6">
									<label for="">Candidate Name </label>						  
								<div class="" style="width:100%;">{{$listdata->cand_vname}}</div>
							</div>
						</div>         
<hr />						
						<div class="form-group row">
						 
						  <div class="col-sm-6"> 
						   <label>Party Name </label>
								<div class="" style="width:100%;"> {{ $listdata->PARTYNAME }}</div>
						  </div> 
						  
						  <div class="col-sm-6"> 
						   <label>Symbol </label>
							<div class="" style="width:100%;">{{$listdata->SYMBOL_DES}} </div>
						
						  </div>
						</div>
						
					
					 
					  </div>
					</div>
					</div>
				</div>
				</div>
			</div>
		</div>	  
	  </section>
	  <section class="">
		<div class="container">
			<div class="row">
			
				<div class="col-md-12">
				<div class="card">
                <div class="card-header d-flex align-items-center">
                  <h4>Candidate Personal Details</h4>
                </div>
                <div class="card-body">
				  <form class="form-horizontal">
				<div class="row formCode">
				
					<div class="col-md-6">
					 <label>Father's / Husband's Name </label>
                      <div class="">{{$listdata->candidate_father_name}}  </div>  
					   <label class=""></label><div class="col"> </div>
					</div>  
					
					<div class="col-md-6">
					 <label>Father's / Husband's Name Hindi</label>
                      <div class="">{{$listdata->cand_fhname}}</div>  					  
					</div> 
					
					<div class="col-md-6">
					 <label>Email</label>
                      <div class="">{{$listdata->cand_email}} </div>  					  
					</div> 
					
					<div class="col-md-6">
					 <label>Mobile No </label>
                      <div class="">{{$listdata->cand_mobile}} </div>  					  
					</div> 
					
					<div class="col-md-3">
					 <label>Gender</label>
                      <div class="">{{$listdata->cand_gender}} </div>  					  
					</div> 
						<div class="col-md-3">
					 <label>Age</label>
                      <div class="">{{$listdata->cand_age}}  </div>  					  
					</div> 
					<div class="col-md-6">
					 <label>PAN Number</label>
                      <div class="">{{$listdata->cand_panno}} </div>  					  
					</div> 
					
				
					
					<div class="col-md-6">
					 <label>Address in English</label>
                      <div class="">{{$listdata->candidate_residence_address}} </div>  					  
					</div> 
					<div class="col-md-6">
					 <label>Address In Hindi </label>
                      <div class="">{{$listdata->candidate_residence_addressh}} </div>  					  
					</div>  
					
					<div class="col-md-6">
					<label for="statename">State Name </label>
                      <div class="">{{$listdata->ST_NAME}} </div>  					  
					</div> 
					
					<div class="col-md-6">
					<label for="statename">PC Name </label>
                      <div class="">{{$listdata->PC_NAME}} </div>  					  
					</div> 
					
					<div class="col-md-12">
					<label for="statename">Category </label>
                     <div>{{$listdata->cand_category}} </div> 	
  <hr />					 
					</div>                
              </div>
                  
				</div>
				 <div class="col-md-12 card-footer">
						<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
						<a href="{{url('/eci/EciViewNominationPdf')}}/{{base64_encode($nom_id)}}/{{base64_encode($cand_id)}}" class="btn btn-primary float-right">Download PDF</a>
						  	
						
				   </div>
			
				  
				   </form>
                </div>
              </div>
				</div>
			</div>
		</div>	  
	  </section>

	  @empty
        <tr>
          <td colspan="5">No Data Found For Candidate</td>                 
      </tr>
   @endforelse

</main>
@endsection