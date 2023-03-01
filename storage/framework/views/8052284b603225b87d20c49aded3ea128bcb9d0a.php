      
      <?php $__env->startSection('title', 'Nomination'); ?>
      <?php $__env->startSection('content'); ?>
      <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>" id="theme-stylesheet">
	  <link rel="stylesheet" href="<?php echo e(asset('css/custom-dark.css')); ?>" id="theme-stylesheet">	  
		<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/bootstrap.min.css')); ?> " type="text/css">
		<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-profile.css')); ?> " type="text/css">
		<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom.css')); ?> " type="text/css">
		<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-dark.css')); ?> " type="text/css">
		<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/font-awesome.min.css')); ?> " type="text/css">
		<link rel="stylesheet" href="<?php echo e(asset('appoinment/fonts.css')); ?> " type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,400i,500,500i,600,700,700i,800,900&display=swap" rel="stylesheet">
      <style type="text/css">
        .fullwidth{
          width: 100%;
          float: left;
        }
        .button-next{
          margin-top: 30px;
        }
        .button-next button{
          float: right;
        }
        .affidavit-preview{
          min-height: 600px;
        }
		
      </style>
      <main role="main" class="inner cover mb-3">
        <section>
          <div class="container">
            <div class="row">

            <?php if(count($errors->all())>0): ?>
               <div class="alert alert-danger">
                <ul>
                 <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 <li><p class="text-left"><?php echo $iterate_error; ?></p></li>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
               </ul>
             </div>
             <?php endif; ?>

             <?php if(session('flash-message')): ?>
             <div class="alert alert-success"> <?php echo e(session('flash-message')); ?></div>
             <?php endif; ?>
         </div>
       </div>    
     </section>
	<div class="container">
	 <div class="step-wrap mt-4" id="ttttt">
		<ul>
		   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step1')); ?></span></li>
		   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step2')); ?></span></li>
		   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step3')); ?></span></li>
		   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step4')); ?></span></li>
		   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step5')); ?></span></li>
		   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step6')); ?></span></li>
		   <li class="step-current"><b>&#10004;</b><span><?php echo e(__('step1.step7')); ?></span></li>
		</ul>
	 </div>
	</div>
     <section>
      <div class="container p-0">
         <div class="row">
			
			
			
			<form method="post" name="preview" action="<?php echo $action; ?>" enctype="multipart/form-data">
			<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
			<input type="hidden" name="nomination_id" value="<?php echo e($nomination_id); ?>">		  
			 <div class="container-fluid">
			  <div class="card card-shadow">
				<div class="row" style="margin-top:15px;margin-right:10px;">
				<div class="fullwidth" style="float: left;width: 100%;">
				<?php if(isset($reference_id) && isset($href_download_application)): ?>
                <div class="col-md-5 float-right">
                  <ul class="list-inline float-right">
                    <li class="list-inline-item text-right"><?php echo e(__('election_details.ref')); ?>: <b style="text-decoration: underline;"><?php echo e($reference_id); ?></b></li>
                    <li class="list-inline-item text-right"><a href="<?php echo $href_download_application; ?>" class="btn btn-primary" target="_blank"><?php echo e(__('election_details.down')); ?></a></li>
                  </ul>
                </div>
                <?php endif; ?>
              </div>
           </div>
				
				  <table class="customTable">
					<tbody>
					
					  <tr>
						<td class="td-center"><h5 style="color: #ee577e; font-size: 28px; padding: 12px;"><?php echo e(__('finalize.Preview')); ?></h5></td>
					  </tr> 
					  
					  <tr>
						<td class="td-center"><h5><?php echo e(__('step3.form2b')); ?></h5></td>
					  </tr> 
					  <tr>
						<td class="td-center">(<?php echo e(__('step3.rule4')); ?>)</td>
					  </tr> 
					  <tr>
						<td class="td-center"><h5><?php echo e(__('step3.nomp')); ?></h5></td>
					  </tr> 
					  <tr>
						<td class="td-center"><i><?php echo e(__('step3.nommessage')); ?><span><?php echo e($st_name); ?></span>(<?php echo e(__('finalize.State')); ?>) </i></td>
					  </tr> 
					  <tr>
						<td><div class="col-lg-2 pull-left">
                                    <img src="<?php echo $qr_code; ?>" style="max-width: 150px;">
                                  </div>
								<div class="passport-img">
								<!--<img src="#" alt="">-->
								<img src="<?php echo $profileimg; ?>" style="height: 160px;">
							</div>
						 </td>
					</tr>	
			  <tr>
				<td class="td-center"><?php echo e(__('finalize.STRIKE_OFF')); ?></td>
			  </tr>
			
			    <?php if($recognized_party == '1' or $recognized_party == '0' or $recognized_party == '3'): ?>
			  <tr>
				<!--<td class="td-center td-bold">PART I</td>-->
				<td class="td-center"><b><?php echo e(__('finalize.PART1')); ?></b></td>
			  </tr> 
			  <tr>
				<td class="td-center">(<?php echo e(__('finalize.recognized_party')); ?>) </td> 
			  </tr> 
			  <tr>
			  	<td><?php echo e(__('finalize.nominate_ac')); ?><span><b>&nbsp; <?php echo e($legislative_assembly); ?>-  <?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $legislative_assembly); ?></b></span><?php echo e(__('finalize.Assembly_Constituency')); ?>. </td>
			  </tr>	
			 <tr>
			 	<td class="param-area">
					<p><?php echo e(__('finalize.Candidate_name')); ?><span> <b>&nbsp;<?php echo e($name); ?></b></span> <?php echo e(__('finalize.Father_husband_mother')); ?> <span>&nbsp; <b><?php echo $father_name; ?></b></span><?php echo e(__('finalize.His_postal_address')); ?><span style="width:auto;">&nbsp;  <b><?php echo $address; ?></b> </span> <?php echo e(__('finalize.His_name_is_entered_at_Sl')); ?> <span>&nbsp; <b><?php echo e($serial_no); ?></b></span> <?php echo e(__('finalize.in_Part_No')); ?> <span>&nbsp;<b><?php echo e($part_no); ?></b></span> <?php echo e(__('finalize.of_the_electoral_roll_for')); ?> <span>&nbsp; <?php echo e($resident_ac_no); ?>-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $resident_ac_no); ?></b></span><?php echo e(__('finalize.Assembly_Constituency')); ?>. 
				    </p>
					<br>
					<p> <?php echo e(__('finalize.My_name_is')); ?> <span>&nbsp;<b><?php echo e($proposer_name); ?></b> </span> <?php echo e(__('finalize.and_it_is_entered_at_Sl')); ?> <span>&nbsp; <b><?php echo e($proposer_serial_no); ?></b> </span> <?php echo e(__('finalize.in_Part_No')); ?> <span>&nbsp; <b><?php echo e($proposer_part_no); ?></b> </span> <?php echo e(__('finalize.of_the_electoral_roll_for')); ?> <span>&nbsp; <b><?php echo e($proposer_assembly); ?>-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $proposer_assembly); ?>	</b> </span> <?php echo e(__('finalize.Assembly_Constituency')); ?>. </p>
				 </td>
			 </tr>
			 <tr>
			 	<td>
				  <table style="width: 100%; margin: 1.5rem 0;">
					 <tbody>
					   <tr>
					   	<td><?php echo e(__('finalize.Date')); ?> <span>&nbsp; <b><?php echo e($apply_date); ?></b></span></td>
					   	<td class="td-right">
							<div><?php echo e(__('finalize.Signature_of_the_Proposer')); ?> </div>
						</td>
					   </tr> 
					 </tbody>
				  </table> 
				</td>
			 </tr>			

			 <!-- For Strike -->
			 
			 
			<?php if($recognized_party != '3'): ?>	 
			 <tr>
				<!--<td class="td-center td-bold bordr-one"><div class="pt-one">PART II</div> </td>-->
				<td class="td-center td-bold"><div class="pt-one"><?php echo e(__('finalize.PART2')); ?></div> </td> 
			  </tr> 
			  <tr>
			  	<td class="param-area">
				  <p>
					<hr style="width: 85%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;"><?php echo e(__('finalize.nominate_ac')); ?> <span>&nbsp; <b></b></span><?php echo e(__('finalize.Assembly_Constituency')); ?>. </hr>
				  </p>  
				  <p><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;"> <?php echo e(__('finalize.Candidate_name')); ?><span>&nbsp; <b></b></span><?php echo e(__('finalize.Father_husband_mother')); ?><span>&nbsp; <b></b></span><?php echo e(__('finalize.His_postal_address')); ?><span style="width:auto;">&nbsp;  <b></b> </span></hr>
                  
				<hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	<?php echo e(__('finalize.His_name_is_entered_at_Sl')); ?> <span>&nbsp; <b></b></span> <?php echo e(__('finalize.in_Part_No')); ?> <span>&nbsp; <b></b></span><span>&nbsp; <b></b></span><?php echo e(__('finalize.Assembly_Constituency')); ?>. </hr>
					 
				</p>	
				</td>
			  </tr>	
				<tr>
					<td>
					  <p><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">
						<?php echo e(__('finalize.We_declare_that_we_are_electors')); ?>:- </hr>
					  </p>
					</td>
				</tr>
				<tr> 
					<td class="td-center"><h6 class="pt-one"><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	<?php echo e(__('finalize.Particulars_of_the_proposers')); ?></hr></h6></td>
				</tr>
				
				<tr>
					<td>
					  <table style="width:100%; text-align: center;" border="1">
						  <tr>
						  	<th style="width: 55px;"><?php echo e(__('finalize.serial_no')); ?></th>
						  	<th style="padding: 0;">
							  <table style="width:100%;" border="0">
									<tr>
										<th colspan="2" class="td-center" style="border-bottom: 1px solid #313131;"><?php echo e(__('finalize.Elector_Roll_No')); ?></th>
									</tr>
									<tr>
										<th  style=" width: 50%; border-right: 1px solid #313131;"><?php echo e(__('finalize.Part_No_of_Electoral')); ?></th>
										<th><?php echo e(__('finalize.SNo_in_that_part')); ?></th>
									</tr>
							  </table>  
							</th>
							
						  	<th><?php echo e(__('finalize.Full_Name')); ?></th>
						  	<th><?php echo e(__('finalize.Signature')); ?></th>
						  	<th><?php echo e(__('finalize.Date')); ?> </th>
						  </tr>
						<?php $i=1;  for($k=0; $k<10; $k++){ ?>
							<tr>
								<td><?php echo e($k+1); ?></td>
								<td style="padding: 0;">
								 <table style="width:100%;" border="0">
								   <tr>
									<td style="width: 50%; border-right: 1px solid #313131;height: 27px;">&nbsp;</td>
									<td>&nbsp;</td>
								   </tr>	
								 </table>
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						
						<?php } ?>
					  </table>
					</td>
				</tr>
				<tr>
					<td>
						<div class="pb-three"><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	<strong>N.B.-</strong><?php echo e(__('finalize.There_should_be')); ?> .</hr></div>
					</td>
				</tr> 
				
			<?php endif; ?>	
			 
			 <!-- EndForStrike -->
















			 
			 <?php endif; ?>
			  <?php if($recognized_party == '2' or $recognized_party == '3'): ?>
				  
			  
			     <?php if($recognized_party != '3'): ?>
			  <!-- Strike Start  -->			  
			  <tr>
				<!--<td class="td-center td-bold">PART I</td>-->
				<td class="td-center"><div class="pt-one"><b><?php echo e(__('finalize.PART1')); ?></b></div></td>
			  </tr> 
			  <tr>
				<td class="td-center"><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">(<?php echo e(__('finalize.recognized_party')); ?>) </hr></td> 
				
			  </tr> 
			  <tr>
			  	<td><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;"><?php echo e(__('finalize.nominate_ac')); ?><span><b>&nbsp; </hr>  </td>
			  </tr>	
			 <tr>
			 	<td class="param-area">
					<p><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;"><?php echo e(__('finalize.Candidate_name')); ?><span> <b>&nbsp;</b></span> <?php echo e(__('finalize.Father_husband_mother')); ?> <span>&nbsp; <b></b></span><?php echo e(__('finalize.His_postal_address')); ?> </hr>
					<hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">
					<span style="width:auto;">&nbsp;  <b></b> </span> <?php echo e(__('finalize.His_name_is_entered_at_Sl')); ?> <span>&nbsp; <b> </b></span> <?php echo e(__('finalize.in_Part_No')); ?> <span>&nbsp;<b> </b></span> <?php echo e(__('finalize.of_the_electoral_roll_for')); ?> <span>&nbsp; </b></span><?php echo e(__('finalize.Assembly_Constituency')); ?>. </hr>
				    </p>
					<br>
					
					<p> <hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;"> <?php echo e(__('finalize.My_name_is')); ?> <span>&nbsp;<b> </b> </span> <?php echo e(__('finalize.and_it_is_entered_at_Sl')); ?> <span>&nbsp; <b> </b> </span> <?php echo e(__('finalize.in_Part_No')); ?> <span>&nbsp; <b>  </b> </span> </hr>
					
					<hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;"><?php echo e(__('finalize.of_the_electoral_roll_for')); ?> <span>&nbsp; <b></b> </span> <?php echo e(__('finalize.Assembly_Constituency')); ?>. </hr> </p>
				 </td>
			 </tr>
			 <tr>
			 	<td>
				  <table style="width: 100%; margin: 1.5rem 0;">
					 <tbody>
					   <tr>
					   	<td><hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;"><?php echo e(__('finalize.Date')); ?> <span>&nbsp; <b>  </b></span></td>
					   	<td class="td-right">
							<div><?php echo e(__('finalize.Signature_of_the_Proposer')); ?> </div>
						</td>
					   </tr> 
					 </tbody>
				  </table> 
				</td>
			 </tr>
			<?php endif; ?>  
			  
			  <!--End Strike Start  -->
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  
			<tr>
				<!--<td class="td-center td-bold bordr-one"><div class="pt-one">PART II</div> </td>-->
				<td class="td-center td-bold"><div class="pt-one"><?php echo e(__('finalize.PART2')); ?></div> </td> 
			  </tr> 
			  <tr>
			  	<td class="param-area">
				  <p>
					<?php echo e(__('finalize.nominate_ac')); ?> <span>&nbsp; <b><?php echo e($legislative_assembly); ?>-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $legislative_assembly); ?></b></span><?php echo e(__('finalize.Assembly_Constituency')); ?>. 
				  </p>  
				  <p><?php echo e(__('finalize.Candidate_name')); ?><span>&nbsp; <b><?php echo e($name); ?></b></span><?php echo e(__('finalize.Father_husband_mother')); ?><span>&nbsp; <b><?php echo $father_name; ?></b></span><?php echo e(__('finalize.His_postal_address')); ?><span style="width:auto;">&nbsp;  <b><?php echo $address; ?></b> </span>
                  
					<?php echo e(__('finalize.His_name_is_entered_at_Sl')); ?> <span>&nbsp; <b><?php echo e($serial_no); ?></b></span> <?php echo e(__('finalize.in_Part_No')); ?> <span>&nbsp; <b><?php echo e($part_no); ?></b></span><?php echo e(__('finalize.of_the_electoral_roll_for')); ?> <span>&nbsp; <b><?php echo e($resident_ac_no); ?>-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getAcName($st_code, $resident_ac_no); ?></b></span><?php echo e(__('finalize.Assembly_Constituency')); ?>. 
				</p>	
				</td>
			  </tr>	
				<tr>
					<td>
					  <p>
						<?php echo e(__('finalize.We_declare_that_we_are_electors')); ?>:- 
					  </p>
					</td>
				</tr>
				<tr>
					<td class="td-center"><h6 class="pt-one"><?php echo e(__('finalize.Particulars_of_the_proposers')); ?></h6></td>
				</tr>
				
				<tr>
					<td>
					  <table style="width:100%; text-align: center;" border="1">
						  <tr>
						  	<th style="width: 55px;"><?php echo e(__('finalize.serial_no')); ?></th>
						  	<th style="padding: 0;">
							  <table style="width:100%;" border="0">
									<tr>
										<th colspan="2" class="td-center" style="border-bottom: 1px solid #313131;"><?php echo e(__('finalize.Elector_Roll_No')); ?></th>
									</tr>
									<tr>
										<th  style=" width: 50%; border-right: 1px solid #313131;"><?php echo e(__('finalize.Part_No_of_Electoral')); ?></th>
										<th><?php echo e(__('finalize.SNo_in_that_part')); ?></th>
									</tr>
							  </table>  
							</th>
						  	<th><?php echo e(__('finalize.Full_Name')); ?></th>
						  	<th><?php echo e(__('finalize.Signature')); ?></th>
						  	<th><?php echo e(__('finalize.Date')); ?> </th>
						  </tr>
						<?php $i=1; if(count($non_recognized_proposers)!=0){  
						 foreach($non_recognized_proposers as $iterate_proposer){ ?> 
		                  <tr>
		                 	<td><?php echo e($i); ?>.</td>
		                 	<td style="padding: 0;">
							 <table style="width:100%;" border="0">
							   <tr>
							   	<td style="width: 50%; border-right: 1px solid #313131;height: 27px;"><?php if($iterate_proposer['part_no']!=0): ?><?php echo e($iterate_proposer['part_no']); ?><?php endif; ?></td>
							   	<td><?php if($iterate_proposer['serial_no']!=0): ?><?php echo e($iterate_proposer['serial_no']); ?><?php endif; ?></td>
							   </tr>	
						     </table>
							</td>
		                 	<td><?php echo e($iterate_proposer['fullname']); ?></td>
		                 	<td>&nbsp; <?php echo e($iterate_proposer['signature']); ?></td>
		                 	<td><?php if($iterate_proposer['part_no']!=0 or 
												$iterate_proposer['serial_no']!=0 or
												$iterate_proposer['fullname']!=0 ): ?>
												<?php if(!empty($iterate_proposer['date'])): ?><?php echo e(date('d/m/Y',strtotime($iterate_proposer['date']))); ?><?php endif; ?>
											<?php endif; ?>	
							</td>
		                 </tr>
						<?php $i++; } } else { for($k=0; $k<10; $k++){ ?>
							<tr>
								<td><?php echo e($k+1); ?></td>
								<td style="padding: 0;">
								 <table style="width:100%;" border="0">
								   <tr>
									<td style="width: 50%; border-right: 1px solid #313131;height: 27px;">&nbsp;</td>
									<td>&nbsp;</td>
								   </tr>	
								 </table>
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						
						<?php } } ?>
					  </table>
					</td>
				</tr>
				<tr>
					<td>
						<div class="pb-three"><strong>N.B.-</strong><?php echo e(__('finalize.There_should_be')); ?> .</div>
					</td>
				</tr> 
			   <?php endif; ?>	
				<tr>
				 <td class="td-center td-bold bordr-one"><div class="pt-one"><?php echo e(__('finalize.PART3')); ?></div> </td>
			    </tr>
				<tr>
				  <td class="param-area">
					<p><?php echo e(__('finalize.I_the_candidate_mentioned')); ?> -
					  </p>
					 <p><b>(a)</b> <?php echo e(__('finalize.I_AM_ACITIZEN')); ?> </p> 
					 <p><b>(b)</b> <?php echo e(__('finalize.that_I_have_completed')); ?> <span>&nbsp; <b><?php echo e($age); ?></b> </span> <?php echo e(__('finalize.years_of_age')); ?> </p>
					 <p><h6 class="td-center pt-one pb-three">[ <?php echo e(__('finalize.STRIKE_OUT')); ?> ]</h6></p> 
					 
				     
					 
					 <?php if($recognized_party==0 or $recognized_party=='1' or $recognized_party=='' or $recognized_party==3): ?>  
					 <p><b>(c)</b> (i) <?php echo e(__('finalize.I_am_set_up')); ?> <span style="width: auto;"><b> &nbsp; <?php echo e($party_id); ?> </b> </span>  <?php echo e(__('finalize.party_which_is_recognized')); ?> 
					  </p>
					  <!-- Stike Start -->
						<?php if($recognized_party!=3): ?>  		
						 <h6 class="td-center"><?php echo e(__('finalize.OR')); ?></h6>
						 <p><strike><b>(c)</b> (ii) <?php echo e(__('nomination.i_am_set_1')); ?>   <span ><b></b> </span>  <?php echo e(__('nomination.i_am_set_3')); ?>   <?php echo e(__('nomination.i_am_set_333')); ?> </strike></p>	
						<p>
							<hr style="width: 85%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">
							  <?php echo e(__('part3.spre')); ?> <span style="width: auto;"><b> &nbsp; 1................................... </b> <b> &nbsp; 2...................................  </b> <b> &nbsp; 3...................................	   </b> </span> 
							</hr>
							</p> 
						<?php endif; ?>	  
				   <!--End Stike Start -->
					<?php endif; ?>
					
					
					<?php if($recognized_party==3): ?>    
							<h6 class="td-center"> <?php echo e(__('finalize.OR')); ?> </h6> 
					<?php endif; ?>	
					
					<?php if($recognized_party==2 or $recognized_party==3): ?>	  
					<?php if($recognized_party!=3): ?>    
					  <p><b>(c)</b> (i) <strike> <?php echo e(__('finalize.I_am_set_up')); ?> <span style="width: auto;"><b> &nbsp;</b> </span>  <?php echo e(__('finalize.party_which_is_recognized')); ?> </strike>
					  </p>
					  <h6 class="td-center"> <?php echo e(__('finalize.OR')); ?> </h6>					  
					<?php endif; ?>  
					
					<?php if($party_id2!=743): ?>
					<p><b>(c)</b> (ii) <?php echo e(__('nomination.i_am_set_1')); ?>  <span style="width: auto;"><b> &nbsp; <?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getPartyName($party_id2); ?>  </b> </span>  <?php echo e(__('nomination.i_am_set_3')); ?>  <strike> / <?php echo e(__('nomination.i_am_set_333')); ?> </strike></p>					
					<?php endif; ?>
					
					<?php if($party_id2==743): ?>
					<p><b>(c)</b> (ii) <strike> <?php echo e(__('nomination.i_am_set_1')); ?>  <span style="width: auto;"><b> &nbsp;... </b> </span>  <?php echo e(__('nomination.i_am_set_3')); ?> </strike> / <?php echo e(__('nomination.i_am_set_333')); ?></p>				
					<?php endif; ?>
					
					
					 
					<p><b></b> <?php echo e(__('part3.spre')); ?> <span style="width: auto;"><b> &nbsp; 1. <?php echo e($suggest_symbol_1); ?> </b> <b> &nbsp; 2. <?php echo e($suggest_symbol_2); ?> </b> <b> &nbsp; 3. <?php echo e($suggest_symbol_3); ?> </b> </span> </p> 
					<?php endif; ?>
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 <p><b>(d)</b> <?php echo e(__('finalize.my_name_and_my_father')); ?> <span>&nbsp; <b><?php echo e($language); ?></b></span><?php echo e(__('finalize.name_of_the')); ?>

                     </p>
				     <p><b>(e)</b> <?php echo e(__('finalize.That_to_the_best_of_my_knowledge_and_belief')); ?> </p>
					 
					<?php if(!empty($part3_address)): ?>				
				     <p>
				        * <?php echo e(__('finalize.I_further_declare')); ?> <span>&nbsp; <b><?php echo e($category); ?></b></span>** <?php echo e(__('finalize.Caste_tribe_which')); ?>

						** <?php echo e(__('finalize.Caste_tribe_state')); ?>


				<?php if($category!='general'): ?>
					<span>&nbsp;<b><?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($part3_cast_state); ?></b></span><?php echo e(__('finalize.in_relation_to')); ?> <span>&nbsp;<b><?php echo e($part3_address); ?></b></span> <?php echo e(__('finalize.in_that_State')); ?>. 
					<?php else: ?>
					<hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	
					<span>&nbsp;<b> </b></span>
					<?php echo e(__('finalize.in_relation_to')); ?> <span>&nbsp;
					<b></b></span> <?php echo e(__('finalize.in_that_State')); ?>.
					</hr>
				<?php endif; ?>










				     </p>  
					 <?php else: ?> 
					 <p>
				        * <?php echo e(__('finalize.I_further_declare')); ?> <span>&nbsp; <b><?php echo e($category); ?></b></span>** <?php echo e(__('finalize.Caste_tribe_which')); ?>

						** <?php echo e(__('finalize.Caste_tribe_state')); ?>

					 
					 
					 <?php if($category=='general'): ?>
							
							<hr style="width: 97%; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">	
							  <span>&nbsp;<b> </b></span>
								<?php echo e(__('finalize.in_relation_to')); ?> <span>&nbsp;
								<b></b></span> <?php echo e(__('finalize.in_that_State')); ?>.
							</hr>	
						
						<?php else: ?>
						<span>&nbsp;<b>
						<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($part3_cast_state); ?></b></span>
							<?php echo e(__('finalize.in_relation_to')); ?><span>&nbsp;
							<b></b></span> <?php echo e(__('finalize.in_that_State')); ?>.
						<?php endif; ?>
						
					 
					 
					 
					 
					 
					 </p>  	 
					 
					 
					 
					 <?php endif; ?>  
				     <p> 
				        <?php echo e(__('finalize.That_to_the_best_of_my_knowledge')); ?> <span>&nbsp;<b><?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($part3_legislative_state); ?></b></span> <?php echo e(__('finalize.more_than_two')); ?>. 
				     </p> 
				  </td>
				</tr>
				
				
				
		       <tr>
			 	<td>
				  <table style="width: 100%; margin: 1.5rem 0;">
					 <tbody>
					   <tr>  
					   	<td><?php echo e(__('finalize.Date')); ?> <span>&nbsp;<b><?php echo e(date("d-m-Y", strtotime($part3_date))); ?></b></span></td>
					   	<td class="td-right">
							<div><?php echo e(__('finalize.Signature_of_Candidate')); ?> </div>
						</td>
					   </tr> 
					 </tbody>
				  </table> 
				</td>
			 </tr>
		     <tr>
		       <td>
				 <div class="sm-note">
				   * <?php echo e(__('finalize.Score_out_this_paragraph')); ?>.<br>
				** <?php echo e(__('finalize.Score_out_the_words')); ?>.<br> 
				<b>N.B.—</b> <?php echo e(__('finalize.recognized_political_party_text')); ?><br>  
				 </div> 
			   </td>
		     </tr>
		      <tr>
				<td class="td-center td-bold bordr-one">
					<div class="pt-one"><?php echo e(__('finalize.PART3A')); ?></div>
					<p>(<?php echo e(__('step3.To_be_filled_by_the_candidate')); ?>)</p>
				</td>
			  </tr>
		     <tr>
		       <td>
				  <table style="width: 100%">
				    <tr>
				    	<td style="width:80%;">
						  <div class="param-area">
							<p><b>(1)</b> <?php echo e(__('part3a.whether')); ?>—</p>
							<div class="sub-area" style="border-right: 1px solid #313131;">
							  <p>(i)  <?php echo e(__('part3a.conv')); ?>— </p> 
							   <ul class="list-area">
								<li>(a) <?php echo e(__('part3a.offe')); ?> </li>
								<li>(b) <?php echo e(__('part3a.oro')); ?> </li>
							   </ul>
							  <p>(ii) <?php echo e(__('part3a.impo')); ?> <b><?php echo e(ucfirst($have_police_case)); ?></b></p> 
							</div><!-- End Of sub-area Div -->  
						  </div>
						</td>
				    	<td style="width:20%" valign="middle"><?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?></td>
				    </tr>
				  </table> 
			   </td>
		     </tr>
			 
			  <?php if($have_police_case == 'yes'): ?>
		     <tr>
		     	<td>   <?php $i = 1; ?>
				    <?php echo e(__('part3a.ifye')); ?>

				     <?php $__currentLoopData = $police_cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_police_case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="sub-area">
						<p><?php echo e(__('part3a.case')); ?> <span>&nbsp; <b><?php echo e($i); ?></span></b></p> 
						<p>(i) <?php echo e(__('part3a.ca1')); ?>. <span>&nbsp; <b><?php echo e($iterate_police_case['case_no']); ?></span></b></p> 
						<p>(ii) <?php echo e(__('part3a.pol')); ?> <span>&nbsp; <b><?php echo e($iterate_police_case['police_station']); ?></b></span> <?php echo e(__('part3a.dist')); ?> <span>&nbsp;<b><?php echo e($iterate_police_case['case_dist_no']); ?>-<?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getDist($iterate_police_case['st_code'], $iterate_police_case['case_dist_no']); ?></b></span>&nbsp; <?php echo e(__('part3a.st')); ?>  <span>&nbsp;<b><?php echo app(App\Http\Controllers\Nomination\NominationController::class)->getState($iterate_police_case['st_code']); ?></b></span>.</p>
						<p>(iii) <?php echo e(__('part3a.sec1')); ?>   <span style="width:100%;">&nbsp;<b><?php echo e($iterate_police_case['convicted_des']); ?></b></span></p>
						<p>(iv) <?php echo e(__('part3a.cdat')); ?> <span style="width:100%;">&nbsp;<b><?php echo e($iterate_police_case['date_of_conviction']); ?></b></span></p>
						<p>(v)  <?php echo e(__('part3a.cour')); ?> <span style="width:100%;">&nbsp;<b><?php echo e($iterate_police_case['court_name']); ?></b></span></p>
						<p>(vi) <?php echo e(__('part3a.puni')); ?> <span style="width:100%;">&nbsp;<b><?php echo e($iterate_police_case['punishment_imposed']); ?></b></span>.</p>					
						 <?php $dt='NA'; ?>		
						  <?php if($iterate_police_case['date_of_release']!='1970-01-01'): ?>
						  <?php $dt=$iterate_police_case['date_of_release']; ?>		
						  <?php endif; ?>	
						
						<p>(vii) <?php echo e(__('part3a.rele')); ?> <span>&nbsp;<b><?php echo e($dt); ?></b></span></p>
						<p>(viii) <?php echo e(__('part3a.aga')); ?> <span>&nbsp;<b><?php echo e($iterate_police_case['revision_against_conviction']); ?></b></span><?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?></p>
						<p>(ix) <?php echo e(__('part3a.agad')); ?>  <span>&nbsp;<b><?php echo e($iterate_police_case['revision_appeal_date']); ?></b></span>.</p>
						<p>(x) <?php echo e(__('part3a.revf')); ?>  <span style="width:100%;">&nbsp;<b><?php echo e($iterate_police_case['rev_court_name']); ?></b></span></p>
						<p>(xi) <?php echo e(__('part3a.dips')); ?> <span>&nbsp;<b><?php echo e($iterate_police_case['status']); ?></b></span></p>
						<p>(xii) <?php echo e(__('part3a.diee')); ?>—</p>
						<ul>
							<li>(a) <?php echo e(__('part3a.didd')); ?> <span>&nbsp;<b><?php echo e($iterate_police_case['revision_disposal_date']); ?></b></span></li>
							<li>(b) <?php echo e(__('part3a.nat')); ?> <span style="width:100%;">&nbsp;<b><?php echo e($iterate_police_case['revision_order_description']); ?></b></span></li>
						</ul>
					</div><!-- End Of sub-area Div -->
					  <?php $i++; ?>	
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</td>
		     </tr>
			<?php endif; ?> 
			 
			 
		     <tr>
		     	<td>
				   <b>(2)</b> <?php echo e(__('part3a.prop')); ?>

					<div class="sub-area">
					    <p><span>&nbsp;<b><?php echo e(ucfirst($profit_under_govt)); ?></b></span>(<?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?>)</p>
						  <?php if($profit_under_govt == 'yes'): ?>
 						<p>-<?php echo e(__('part3a.ifyes1')); ?> <span style="width:100%;">&nbsp;<b><?php echo e(ucfirst($office_held)); ?></b></span></p>
						 <?php endif; ?>
					</div>
				</td>
		     </tr>
		  <tr>
			<td>
				<b>(3)</b>  <?php echo e(__('part3a.inso')); ?> <span>&nbsp;<b><?php echo e(ucfirst($court_insolvent)); ?></b></span> (<?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?>)
				<div class="sub-area">
				  <?php if($court_insolvent == 'yes'): ?>
					<p>- <?php echo e(__('part3a.disc')); ?><span style="width:100%;"> &nbsp;<b><?php echo e(ucfirst($discharged_insolvency)); ?></b></span></p>
				  <?php endif; ?>
				</div>					
			</td>
		  </tr>
		  <tr>
			<td>
				<b>(4)</b> <?php echo e(__('part3a.alle')); ?><span>&nbsp; <b><?php echo e(ucfirst($allegiance_to_foreign_country)); ?></b></span> (<?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?>)
				<div class="sub-area">
					 <?php if($allegiance_to_foreign_country == 'yes'): ?>
					<p>- <?php echo e(__('part3a.alled')); ?><span style="width:100%;">&nbsp;<b><?php echo e(ucfirst($country_detail)); ?></b></span></p>
					<?php endif; ?>
				</div>					
			</td>
		  </tr>
		  <tr>
			<td>
				<b>(5)</b> <?php echo e(__('part3a.disq')); ?>  <span>&nbsp;<b><?php echo e(ucfirst($disqualified_section8A)); ?></b></span> (<?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?>))
				<div class="sub-area">
					 <?php if($disqualified_section8A == 'yes'): ?>
					<p>- <?php echo e(__('part3a.peri')); ?><span>&nbsp;<b><?php echo e(ucfirst($disqualified_section8A)); ?></b></span></p>
					<?php endif; ?>
				</div>					
			</td>
		  </tr>
		  <tr>
			<td>
				<b>(6)</b> <?php echo e(__('part3a.corr')); ?> <span>&nbsp;<b><?php echo e(ucfirst($disloyalty_status)); ?></b></span> (<?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?>))
				<div class="sub-area">
					 <?php if($disloyalty_status == 'yes'): ?>
                    <p>-- <?php echo e(__('part3a.cord')); ?> <span>&nbsp;<b><?php echo e(ucfirst($date_of_dismissal)); ?></b></span></p>
					<?php endif; ?>
				</div>					
			</td>
		  </tr>
		  <tr>
			<td>
				<b>(7)</b> <?php echo e(__('part3a.subs')); ?>  <span>&nbsp;<b><?php echo e(ucfirst($subsiting_gov_taken)); ?></b></span> (<?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?>))
				<div class="sub-area">
			<?php if($subsiting_gov_taken == 'yes'): ?>
			  <p>-  <?php echo e(__('part3a.subp')); ?><span style="width:100%;">&nbsp;<b><?php echo e(ucfirst($subsitting_contract)); ?></b></span></p>
			<?php endif; ?>
				</div>					
			</td>
		  </tr>
		  <tr>
			<td>
				<b>(8)</b> <?php echo e(__('part3a.agen')); ?><span>&nbsp;<b><?php echo e(ucfirst($managing_agent)); ?></b></span> (<?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?>))
				<div class="sub-area">
					<?php if($managing_agent == 'yes'): ?>
                    <p>- <?php echo e(__('part3a.aged')); ?> <span style="width:100%;">&nbsp;<b><?php echo e(ucfirst($gov_detail)); ?></b></span></p>
					<?php endif; ?>

				</div>					
			</td>
		  </tr>
		  <tr>
			<td>
				<b>(9)</b> <?php echo e(__('part3a.comm')); ?> <span>&nbsp;<b><?php echo e(ucfirst($disqualified_by_comission_10Asec)); ?></b></span> (<?php echo e(__('part3a.Yes')); ?>/<?php echo e(__('part3a.No')); ?>))
				<div class="sub-area">
					<?php if($disqualified_by_comission_10Asec=='yes'): ?>
                    <p>- <?php echo e(__('part3a.comd')); ?> <span>&nbsp;<b><?php echo e(ucfirst($date_of_disqualification)); ?></b></span></p>
					<?php endif; ?>
				</div>					
			</td>
		  </tr>
		   <tr>
			 	<td>
				  <table style="width: 100%; margin: 1.5rem 0;">
					 <tbody>
					   <tr>
					   	<td>
							<div><?php echo e(__('finalize.Place')); ?>: </div>
							<div><?php echo e(__('part3a.Date')); ?>: <b><?php echo e(date("d/m/Y", strtotime($date_of_disloyal))); ?></b></div>
						</td>
					   	<td class="td-right">
							<div><?php echo e(__('finalize.Signature_of_Candidate')); ?></div>
						</td>
					   </tr> 
					 </tbody>
				  </table> 
				</td>
			 </tr>
			 
			  <tr>
			 	<td>
				  <table style="width: 100%; margin: 1.5rem 0;">
					 <tbody>
					   <tr>
					   	<td>
						
			<?php if($affidavit!='NA'){ ?>				
			<fieldset class="fullwidth">
			  <div id="affidavit-preview" class="affidavit-preview">
				<embed src="<?php echo $affidavit; ?>" width='100%' height='500px' />
			  </div>
			</fieldset>
			<?php } ?>				
			
						</td>
					   	<td class="td-right">
							
						</td>
					   </tr> 
					 </tbody>
				  </table> 
				</td>
			 </tr>
		   </tbody> 
		   </table>
		    <div class="fullwidth" style="margin-top: 30px;"> 
	  <div class="form-group">
		<div class="col">
		  <a href="<?php echo e($href_back); ?>" id="" class="btn btn-secondary float-left font-big"><?php echo e(__('step1.Back')); ?></a>
		</div>
		<div class="col ">
		  <div class="form-group row float-right">
		  <div style="background:#ee577f;margin-right:50px;color:white;" class="btn btn-primary save_next font-big" ><a style="background:#ee577f; color:white;" href="<?php echo url('/'); ?>/dashboard-nomination-new"><?php echo e(__('step1.Cancel')); ?></a></div>  
		  <?php if($finalize!='yes'): ?>
		  <div style="background:#D04A8A;margin-right:50px;" class="btn btn-primary save_next font-big" onclick="return finalize();"><?php echo e(__('messages.proSec')); ?>

		  <?php else: ?>
		  <div style="background:#D04A8A;margin-right:50px;" class="btn btn-primary save_next font-big" onclick="return finalize();"><?php echo e(__('messages.proSec')); ?>

		  <?php endif; ?>	
		  </div>
		</div>
		</div>
		</div>
	  </div>
		</div> 
	  </div>	
	  </form>
	 </div>
			  
        </div>
      </div>    
    </section>
	
	
	<!-- Modal confirm schedule -->
    <div class="modal fade modal-confirm" id="Pre">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
       <div class="pop-header pt-3 pb-1">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h5 class="modal-title"></h5>
		<div class="header-caption">
		  <p><?php echo e(__('messages.defectMessage')); ?></p>	
		  
		</div>		
        </div>
        
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn" data-dismiss="modal"><?php echo e(__('nomination.ok')); ?></button>
          <!--<button type="button" class="btn dark-purple-btn">Print</button>-->
        </div>
        
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->
	
	
	
	
	<!-- The Confirmation Modal Starts Here -->
  <div class="modal fade modal-confirm" id="confirm">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="pop-header py-4">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h2 class="modal-title"><?php echo e(__('finalize.Confirmation')); ?></h2> 
		<div class="header-caption px-4">
		   <!--<p class="font-big"><?php echo e(__('finalize.are_you_sure')); ?> </p>	-->
		   <p class="font-big"><?php echo e(__('messages.However')); ?>

		    </p> 
			<!--<p><?php echo e(__('messages.printout')); ?></p>-->
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn font-big mr-4" data-dismiss="modal"><?php echo e(__('step1.Cancel')); ?></button>
          <button type="button" class="btn dark-purple-btn font-big" onclick="submitForm();"><?php echo e(__('finalize.Ok')); ?></button>
        </div>
		<span style="text-align: center;display:none;" id="loader">
		 <!--<span><?php echo e(__('messages.emailsms')); ?> </span> -->
		 <br>
		 <img src="<?php echo e(asset('appoinment/loader.gif')); ?>" height="70" width="70"></img> &nbsp; <?php echo e(__('finalize.Please_Wait')); ?>

		</span>
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->
  <!-- The Confirmation Modal Starts Here -->
  <div class="modal fade modal-confirm" id="formf">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="pop-header py-4">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h2 class="modal-title"><?php echo e(__('finalize.Confirmation')); ?></h2> 
		<div class="header-caption px-4">
		  <p class="font-big"><?php echo e(__('finalize.finalize_nomination')); ?> </p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn font-big mr-4" data-dismiss="modal"><?php echo e(__('step1.Cancel')); ?></button>
          <button type="button" class="btn dark-purple-btn font-big" onclick="formFinalize('<?php echo $reference_id; ?>');"><?php echo e(__('finalize.Ok')); ?></button>
        </div>
		<span style="text-align: center;display:none;" id="load">
		 <img src="<?php echo e(asset('appoinment/loader.gif')); ?>" height="70" width="70"></img> &nbsp; <?php echo e(__('finalize.Please_Wait')); ?>

		</span>
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div --> 
  <!-- The Confirmation Modal Starts Here -->
  <div class="modal fade modal-confirm" id="successm">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="pop-header py-4">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h2 class="modal-title"><?php echo e(__('finalize.Confirmation')); ?></h2> 
		<div class="header-caption px-4">
		  <p class="font-big"><?php echo e(__('finalize.s')); ?></p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn font-big mr-4" data-dismiss="modal" onclick="return goback();"><?php echo e(__('finalize.Ok')); ?></button>
        </div>
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->
	<!-- The Confirmation Modal Starts Here -->
  <div class="modal fade modal-confirm" id="fails">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="pop-header py-4">
		  <div class="animte-tick"><span>&#10003;</span></div>	
          <h2 class="modal-title"><?php echo e(__('finalize.Confirmation')); ?></h2> 
		<div class="header-caption px-4">
		  <p class="font-big"><?php echo e(__('finalize.f')); ?></p>	
		</div>		
        </div>
        <!-- Modal footer -->
        <div class="confirm-footer">
		  <button type="button" class="btn dark-pink-btn font-big mr-4" data-dismiss="modal"><?php echo e(__('finalize.Ok')); ?></button>
        </div>
      </div>
    </div>
  </div><!-- End Of confirm Modal popup Div -->
	
	
	
   
  </main>
  
  <script src="<?php echo e(asset('appoinment/js/jQuery.min.v3.4.1.js')); ?>" type="text/javascript"></script>
	<script src="<?php echo e(asset('appoinment/js/bootstrap.min.js')); ?>" type="text/javascript"></script>
  <?php $__env->stopSection(); ?>

  <?php $__env->startSection('script'); ?>
  <script> 
   /* function goback(){
		window.location.href="<?php echo url('/'); ?>/nomination/prescootiny/<?php echo encrypt_string($nomination_id); ?>?acs=<?php echo encrypt_string($legislative_assembly); ?>&std=<?php  echo encrypt_string($st_code); ?>&did=<?php echo encrypt_string($nomination_id);  ?>"
	} */
  
	function formFinalize(nid){
		$("#load").show();
		$.ajax({
		type: "POST",
		url: "<?php echo url('/'); ?>/nomination/make-finalize", 
		data: {
			"_token": "<?php echo e(csrf_token()); ?>",
			"nid": nid
			},
		dataType: "html",
		success: function(msg){
		    if(msg==1){
			 $('#successm').modal('show');	
			 $('#formf').modal('hide');	
			} else {
			 $('#fails').modal('show');	
			 $('#formf').modal('hide');		
			}
		  
		},
		error: function(error){
			console.log("Error"+error);
			console.log(error.responseText);				
			var obj =  $.parseJSON(error.responseText);
		}
	  });
	}
  
  
	function submitForm(){
		document.preview.submit();
		var j = jQuery.noConflict();		
		j("#loader").show();
	} 
	function finalize(){
	 $('#confirm').modal('show');
	}
	function formf(){
	 $("#load").hide();	
	 $('#formf').modal('show');
	}
    function Pre(){
	 $('#Pre').modal('show');
	} 
  
  
    $(document).ready(function(){  
     if($('#breadcrumb').length){
       var breadcrumb = '';
       $.each(<?php echo json_encode($breadcrumbs); ?>,function(index, object){
        breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
      });
       $('#breadcrumb').html(breadcrumb);
     }
   });
  </script>
  <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/nomination/apply-nomination-finalize.blade.php ENDPATH**/ ?>