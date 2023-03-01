<?php $__env->startSection('title', 'Candidate Nomintion Details'); ?>
<?php $__env->startSection('bradcome', 'Upload Candidate Affidavit'); ?>
<?php $__env->startSection('content'); ?>
 <?php   $st=getstatebystatecode($ele_details->ST_CODE);  
          $pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO); 
          $url = URL::to("/"); $j=0;
    ?>
 <style type="text/css">
     html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
  
 
 <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
<div class="row">
  				
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                <div class="col"> <h4>Upload Candidate Affidavit </h4> </div> 
				<div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info"><?php echo e($st->ST_NAME); ?></span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info"><?php echo e($pc->PC_NAME); ?></span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
        
        <?php if(session('success_mes')): ?>
          <div class="alert alert-success"> <?php echo e(session('success_mes')); ?></div>
        <?php endif; ?>
         <?php if(session('error_mes')): ?>
          <div class="alert alert-danger"> <?php echo e(session('error_mes')); ?></div>
        <?php endif; ?>
         <?php if(\Session::has('success')): ?>
			<div class="alert alert-success">
				<ul>
					<li><?php echo \Session::get('success'); ?></li>
				</ul>
			</div>
		<?php endif; ?>
      
         
    </div>
    </div>
   		
       
    <div class="card-border">  
       <form class="form-horizontal" id="election_form" method="post" action="<?php echo e(url('ropc/verifycandidateaffidavit')); ?>" enctype="multipart/form-data" autocomplete='off'>
  <?php echo e(csrf_field()); ?>

		<input type="hidden" name="affidavit_name" value="Form 26" id='test'/>
	   
			<div class="row">
				<div class="col-md-12">
				
					
					<div class="row d-flex align-items-center ">
						<div class="col">
								<label for="candidate_id" class="col-form-label">Candidate Name <span class="errorred">*</span></label> &nbsp; &nbsp;
								<select name="candidate_id" id="candidate_id" class="form-control">
									<option value="" class=>-- Select Candidate Name --</option>
										<?php $__currentLoopData = $cand_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

										<?php  $cand=getById('candidate_personal_detail','candidate_id',$candidate->candidate_id); 

                   // print_R($candidate); exit;

                    
										if(@$cand->cand_name=="NOTA") continue; ?>      
										<option value="<?php echo e($candidate->nom_id); ?>" <?php if($lastid==$candidate->nom_id): ?> selected="selected" <?php endif; ?> ><?php echo e($candidate->nom_id); ?>-<?php echo e(@$cand->cand_name); ?>-<?php echo e(@$cand->cand_mobile); ?>-C/o:-<?php echo e(@$cand->candidate_father_name); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
								<?php if($errors->has('candidate_id')): ?>
                                     <span style="color:red;"><?php echo e($errors->first('candidate_id')); ?></span>
                                  <?php endif; ?>
													<span id="errmsg" class="text-danger"></span>	
								</div>	
								
											
		
					<div class="col">
					<label for="affidavit" class="col-form-label">Candidate Affidavit File Only PDF <span class="errorred">*</span> (Maximum size 10 MB)</label>
					<div class="file-upload">
						<div class="file-select">
					   	<div class="file-select-name" id="noFile">No file chosen...</div> 
						<input type="file" name="affidavit" id="affidavit" class="custom-file-input affidavit form-control mr-auto" accept=".pdf">
						<div class="file-select-button customchoose" id="fileName">Choose File</div>
  </div>
</div>
					<?php if($errors->has('affidavit')): ?>
                                     <span style="color:red;"><?php echo e($errors->first('affidavit')); ?></span>
                                  <?php endif; ?>
								<span id="errmsg1" class="text-danger"></span>
								
								
							</div>
<div class="col-md-1 p-0 m-0">

<button type="submit" id="candnomination" class="btn btn-primary custombtn">Upload</button></div>
			
			</div>
					
					</div>
					</div>
					 
			 
		</form>   
  
        

    </div>
    </div>
  
  
  </div>
  </div>
  </section>
  <section class="mt-3 dashboard-header section-padding">
	<div class="container">
		<div class="row">
			 <table   class="table table-striped table-bordered table-hover" style="width:100%">
        <thead> <tr> <th>Sl. No.</th> <th>Candidate Name</th><th>Party Name</th><th>Affidavit Details</th></tr></thead>
        <tbody><?php if(!empty($cand_data)): ?>
            <?php $__currentLoopData = $cand_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
            <?php $j++;  	$cand=getById('candidate_personal_detail','candidate_id',$list->candidate_id); 
            				      $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);
                          $party=getpartybyid($list->party_id);
            if(@$cand->cand_name=="NOTA") continue; ?>      
        <tr><td><?php echo e($j); ?></td><td>Nom Id-<?php echo e($list->nom_id); ?>-<?php echo e(@$cand->cand_name); ?>-S/O or W/O :-<?php echo e(@$cand->candidate_father_name); ?></td><td align="left"><?php echo e($party->PARTYABBRE); ?>-<?php echo e($party->PARTYNAME); ?></td>
        	<td> <?php if(!empty($affidavit->affidavit_name)): ?> <a href="<?php echo e(asset($affidavit->affidavit_path)); ?>" download><?php echo e($affidavit->affidavit_name); ?> </a><?php else: ?> No Affidavit <?php endif; ?> </td></tr>
 
          
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
            <?php endif; ?> 
        </tbody>
     
    </table>
		</div>
	</div>
  </section>
  </main>
 
<?php $__env->stopSection(); ?>
 <?php $__env->startSection('script'); ?>

<script type="text/javascript">
   $(document).ready(function () {  
  //called when key is pressed in textbox
   
  $("#election_form").submit(function(){
      
      if($("#candidate_id").val()=='')
          {  
          $("#errmsg").text("");
          $("#errmsg").text("Please select Candidate");
          $("#candidate_id").focus();
          return false;
          }
    if($("#affidavit").val()=='')
          {  
          $("#errmsg").text("");
          $("#errmsg1").text("Please select pdf file");
          $("#affidavit").focus();
          return false;
          }
      

 
    });
});
 </script>


 <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.pc.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/pc/ro/candidateaffidavit.blade.php ENDPATH**/ ?>