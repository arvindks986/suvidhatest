<?php $__env->startSection('title', 'Nomination'); ?>
<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>" id="theme-stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('admintheme/css/jquery-ui.css')); ?>" id="theme-stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('css/custom-dark.css')); ?>" id="theme-stylesheet">





<link rel="stylesheet" href="<?php echo e(asset('admintheme/css/nomination.css')); ?>" id="theme-stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('admintheme/css/jquery-ui.css')); ?>" id="theme-stylesheet">	
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/bootstrap.min.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-profile.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-dark.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/font-awesome.min.css')); ?> " type="text/css">





<style type="text/css">
  .input-group.col-xs-12.formData { width: 100%;   padding-bottom: 26px;}
  .formData input { padding: 30px 20px;}
  .formData button {padding: 16.5px 20px; border-radius: 0 3px 3px 0;  font-size: 18px;}
  .file-frame img{ width: 100px; height:100px; float: left;}
  .file {
    float: right;
    background-color:#bb4292;
    border-color:#bb4292;
    color:#fff;
    width: 100%;
  }
  .file-frame-error{
    border: 2px solid red;
  }
  
  
</style>


<main class="pt-3 pb-5 pl-5 pr-5">


  <section class="mt-3">
    <div class="container">
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
  </section>
  
<div class="container">
 <div class="step-wrap mt-4">
      <ul>
	   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step1')); ?></span></li>
       <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step2')); ?></span></li>
       <li class="step-current"><b>&#10004;</b><span><?php echo e(__('step1.step3')); ?></span></li>
       <li class=""><b>&#10004;</b><span><?php echo e(__('step1.step4')); ?></span></li>
       <li class=""><b>&#10004;</b><span><?php echo e(__('step1.step5')); ?></span></li>
       <li class=""><b>&#10004;</b><span><?php echo e(__('step1.step6')); ?></span></li>
       <li class=""><b>&#10004;</b><span><?php echo e(__('step1.step7')); ?></span></li>
     </ul>
 </div>
</div>



  <div class="container-fluid">
    <div class="card">
	
	 <div class="row" style="display:none;">
         <div class="col-md-6"> 
		 </div>
          <?php if(isset($reference_id) && isset($href_download_application)): ?>
          <div class="col-md-6 text-right">
            <ul class="list-inline">
              <li class="list-inline-item"><?php echo e(__('election_details.ref')); ?>: <b style="text-decoration: underline;"><?php echo e($reference_id); ?></b></li>
			  <?php if($stepCond >=2): ?>
              <li class="list-inline-item"><a href="<?php echo $href_download_application; ?>" class="btn btn-primary" target="_blank"><?php echo e(__('election_details.down')); ?></a></li>
			  <?php endif; ?> 	
            </ul>
          </div>
          <?php endif; ?>
      </div>
	
	
      <div class="card-header text-center">
        <div class="">
          <h4><?php echo e(__('step3.form2b')); ?></h4>
          <div>(<?php echo e(__('step3.rule4')); ?>)</div>
          <div><?php echo e(__('step3.nomp')); ?></div>
          <div><?php echo e(__('step3.nommessage')); ?> <span class="">(<?php echo e($st_name); ?>)</span></div>
        </div>
      </div>
      <div class="card-body">
	  <div class="part-1">
	  
	  
	
			
		    <?php $idpd=0; ?>
			
		 
	        <div class="row mt-3 mb-4">
            <div class="col-sm-5 col-12 text-right">
              <div class="custom-control custom-radio customRadioBtn">
			  <?php if($recognized_party == 'recognized'): ?>
                <input type="radio" class="custom-control-input recognized_party" id="one" name="recognized_party_p" value="recognized" checked="checked" onclick="return showForm(1);">
				<?php else: ?> 
				<input type="radio" class="custom-control-input recognized_party" id="one" name="recognized_party_p" value="recognized" checked="checked" onclick="return showForm(1);">
			    <?php endif; ?>
				<label class="custom-control-label" for="one"><?php echo e(__('step3.rec')); ?></label>				
              </div>
            </div>
            <div class="col-sm-5 col-12">
              <div class="custom-control custom-radio customRadioBtn">  
                 <?php if($recognized_party == 'not-recognized' && $recognized_party != '0'): ?>
				  <?php $idpd=2; ?>	 
				<input type="radio" class="custom-control-input recognized_party" name="recognized_party_p" value="not-recognized" checked="checked" id="nonreq" onclick="return showForm(2);">
			    <?php else: ?>
				<?php $idpd=2; ?>	
                <input type="radio" class="custom-control-input recognized_party"  name="recognized_party_p" value="not-recognized" id="nonreq" onclick="return showForm(2);">
			    <?php endif; ?>
				<label class="custom-control-label" for="nonreq"><?php echo e(__('step3.nonerec')); ?></label>
              </div>
            </div>
			
			 <div class="col-sm-2 col-12">
              <div class="custom-control custom-radio customRadioBtn">  
                 <?php if($recognized_party == 'both' && $recognized_party != '0'): ?>
				  <?php $idpd=3; ?>	 
				<input type="radio" class="custom-control-input recognized_party" name="recognized_party_p" value="both" checked="checked" id="both" onclick="return showForm(3);">
			    <?php else: ?>
				<?php $idpd=3; ?>	
                <input type="radio" class="custom-control-input recognized_party"  name="recognized_party_p" value="both" id="both" onclick="return showForm(3);">
			    <?php endif; ?>
				<label class="custom-control-label" for="both"><?php echo e(__('messages.Both')); ?></label>
              </div>
            </div>
			
          </div>
		 
		  <?php 
		   $disp1="";
		   $disp2="";
		   $disp3="";
		  if($recognized_party == 'recognized'){
			  $disp1="block";
			  $disp2="none";
			  $disp3="none";
		  }
		  if($recognized_party == 'not-recognized' && $recognized_party != '0'){
			  $disp1="none";
			  $disp2="block";
			  $disp3="none";
		  }
		  
		  if($recognized_party == 'both' && $recognized_party != '0'){ 
			  $disp1="none";
			  $disp2="none";
			  $disp3="block";
		  }
		 // echo $disp1.'-'.$disp2.'-'.$disp3;
		
		  ?>
		  
		  
		  
		  <form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" class="customForm" id="part_1" style="display:<?php echo $disp1; ?>">  
		  
		  
		  <input type="hidden" name="user_profile_state" value="<?php echo e($user_profile_state); ?>">
		  <input type="hidden" name="st_code" value="<?php echo e($st_code); ?>">
		  
		 <h3 class="part-title"><span><?php echo e(__('step3.partn')); ?></span></h3>
          <h6 class="part-sub-title mb-5">(<?php echo e(__('step3.recc')); ?>)</h6>
				  <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
		  
		  
		   <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
		   <input type="hidden" name="recognized_party" value="recognized">
		   <input type="hidden" name="nomination_id" value="<?php echo e($nomination_id); ?>">
		  
          
		  
		  <div class="form-group ">
                  <div class="fullwidth float-right animate-wrap" style="width: 100%;">
				  <div class="animate-help-text profile_img" style="display:<?php echo (!empty($profileimg))?'none':'block';?>">
				    <div class="help-text"><?php echo e(__('messages.arpro')); ?></div>
					<div class="animate-icon">
					      <div class="box bounce-2"><i class="fa fa-hand-o-right" aria-hidden="true"></i></div>
					</div>
				  </div><!-- End Of animate-help-text Div -->
                    <div class="browse_image_outer">
						<span  style="font-size: 11px; margin-left: 8px;">Size (2cm X 2.5cm)</span>
                      <div class="avatar-upload btn file-frame">
                              <img src="<?php echo e($thumb); ?>" class="img-responsive">
                              <button class="file btn" type="button">Browse <i class="fa fa-upload"></i></button>
                              <input type="hidden" name="image" class="image" value="<?php echo e($profileimg); ?>">
                      </div>
                    </div>
                  </div>
                </div>
				
			
			<div class="form-group row align-items-center justify-contant-around py-2 px-5">
              <label for="" class="lbl-mandry col-sm-6 col-12 text-right pr-4"><?php echo e(__('step3.nomac')); ?></label>
			  <select name="legislative_assembly" id="legislative_assembly" class="form-control col-sm-4 col-12 ac_no" disabled>
			  <option value=""><?php echo e(__('step3.select')); ?></option>
			  <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			
			  <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
			  
			  <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
			  
			  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</select>		  
            </div>
			
			<input type="hidden" name="legislative_assembly" id="legislative_assembly" class="form-control nomination-field-2" value="<?php echo e($pc_no); ?>">
			
			
			
			
		  <fieldset class="py-4 px-5 mb-4">
            <legend><?php echo e(__('step3.candinfo')); ?></legend>
			
			<div class="row">
			   <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class=""><?php echo e(__('step3.fetch')); ?></label>
				  <div class="input-group"  style="width: 250px;">
								  <input type="text" name="epic_no" id="epic_no" class="form-control"  placeholder="<?php echo e(__('step3.enterepic')); ?>"  value="<?php echo e($epic_no); ?>" readonly>
								  <div class="input-group-append">
									<!--<button class="btn btn-success" type="button" id="epic_no_search" onclick="return getDetailsPartOne();">-->
									<button class="btn btn-success" type="button" id="epic_no_search">
									<i class="fa fa-search" aria-hidden="true"></i>
									</button>
								  </div>								 
								</div>
								 <span style="color:red;font-size:12px;" id="error_1"></span>
                </div>
              </div>
			</div>  
			
            <div class="row">
			  
			   
			
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.candidatename')); ?></label> 
                   <input type="text" name="name" id="name" class="form-control nomination-field-2 alphaonly" placeholder="<?php echo e(__('step3.candidatename')); ?>" value="<?php echo e($name); ?>" readonly>
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.father_husband')); ?></label>
                   <input type="text" name="father_name" id="father_name" placeholder="<?php echo e(__('step3.father_husband')); ?>" value="<?php echo $father_name; ?>" class="form-control nomination-field-3 alphaonly" readonly>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.portal_address')); ?></label>
                  <input type="text" name="address" id="address" placeholder="<?php echo e(__('step3.portal_address')); ?>" value="<?php echo $address; ?>" class="form-control nomination-field-12" readonly> 
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.sno')); ?></label>
                  <input type="number" name="serial_no" id="serial_no" class="form-control nomination-field-2" placeholder="<?php echo e(__('step3.sno')); ?>" value="<?php echo e($serial_no); ?>" min="1" readonly>
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.pno')); ?></label>
                  <input type="number" name="part_no" id="part_no" placeholder="<?php echo e(__('step3.pno')); ?>" class="form-control nomination-field-2" value="<?php if($part_no!=0): ?><?php echo e($part_no); ?><?php endif; ?>" min="1" readonly>
                </div>
              </div>
			 
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.elecpc')); ?></label>
                  <select name="resident_ac_no" id="resident_ac_no" class="form-control nomination-field-2" disabled>
                      <option value=""><?php echo e(__('step3.select')); ?> </option>
                      <?php $__currentLoopData = $resident_acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if($iterate_ac['pc_no'] == $resident_ac_no): ?>
                      <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
                      <?php else: ?>
                      <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
                      <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
					<span id="state_error_cand" style="color:red;font:8px;"></span>
                </div>
              </div>
            </div>
          </fieldset>
            <input type="hidden" name="resident_ac_no" id="resident_ac_no" class="form-control nomination-field-2" value="<?php echo e($resident_ac_no); ?>">
		  
          <fieldset class="py-4 px-5 mt-2 mb-4">
            <legend><?php echo e(__('step3.pinfo')); ?></legend>
			<?php
			$abc1='';
			$abc2='';
			if(!empty($epic_no_proposer_serch)){
			$abc1='disabled';
			$abc2='blcok;';
			} else {
			$abc1='';
			$abc2='none;';	
			}
			
			
			
			
			
			?> 
			<div class="row">
			   <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2 animate-wrap">
				   <div class="animate-help-text dir-lft epic_wrap1" style="display:<?php echo (!empty($epic_no_proposer_serch))?'none':'block';?>">
				    <div class="help-text"><?php echo e(__('messages.arepic')); ?></div>
					<div class="animate-icon">
					      <div class="box bounce-2"><i class="fa fa-hand-o-left" aria-hidden="true"></i></div>
					</div>
				  </div><!-- End Of animate-help-text Div -->
                  <label for="" class=""><?php echo e(__('step3.fetchprop')); ?></label>
				  <div class="input-group"  style="width: 250px;">
								  <input type="text" name="epic_no_proposer_serch" id="epic_no_p" class="form-control"  placeholder="<?php echo e(__('step3.enterepic')); ?>" value="<?php echo e($epic_no_proposer_serch); ?>" />
								  
								  
								  
								  
								  <div class="input-group-append">
									<button class="btn btn-success" type="button" id="epic_no_search_p" onclick="return getDetailsPartOne_p();">
									<i class="fa fa-search" aria-hidden="true"></i>
									</button>
									<!--<img src="<?php echo e(asset('img/cancel.jpg')); ?>" height="30" width="30" id="mg" style="display:<?php echo e($abc2); ?>;cursor:pointer;" onclick="return clearPro();">-->
								  </div>								 
								</div>
								 <span style="color:red;font-size:12px;" id="error_p"></span>
                </div>
              </div>
			</div>  
			<!--<input type="hidden" name="epic_no_proposer_serch" id="epic7" class="form-control" value="<?php echo e($epic_no_proposer_serch); ?>"  />-->
            <div class="row">
              <div class="col-sm-6 col-12">
                <div class="form-group mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.pname')); ?></label>
                   <input type="text" name="proposer_name" id="proposer_name" value="<?php echo e($proposer_name); ?>" class="form-control nomination-field-2 alphaonly" placeholder="<?php echo e(__('step3.pname')); ?>" > 
                </div>
              </div>              
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.psno')); ?></label>
                  <input type="number" name="proposer_serial_no" id="proposer_serial_no" class="form-control nomination-field-2" value="<?php if($proposer_serial_no!=0): ?><?php echo e($proposer_serial_no); ?><?php endif; ?>" placeholder="<?php echo e(__('step3.psno')); ?>" min="1" > 
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.ppno')); ?></label>
                  <input type="number" name="proposer_part_no" id="proposer_part_no" value="<?php if($proposer_part_no!=0): ?><?php echo e($proposer_part_no); ?><?php endif; ?>" placeholder="<?php echo e(__('step3.ppno')); ?>" class="form-control nomination-field-2" min="1" > 
                </div>
              </div>
			  
			  <div class="col-sm-6 col-12">
                <div class="form-group mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.ppc')); ?></label>
                  <select name="proposer_assembly" id="proposer_assembly" class="form-control nomination-field-2" onchange="return checkProposalAc(this.value, 'state_error', 'sub');">
                      <option value=""><?php echo e(__('step3.select')); ?></option>
                      <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if($iterate_ac['pc_no'] == $proposer_assembly): ?>
                      <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
                      <?php else: ?>
                      <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
                      <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
					<span id="state_error" style="color:red;font:8px;"></span>
                </div>
              </div>
            </div>
          </fieldset>
		  <!--<input type="hidden" name="proposer_assembly" id="proposer_assembly2" value="<?php echo e($proposer_assembly); ?>">-->
          <div class="row my-3">
            <div class="col-sm-6 col-12"><strong><?php echo e(__('step3.date')); ?>:</strong> <span> 
					  <input type="hidden" name="apply_date"  id="apply_date" value="<?php echo e($apply_date); ?>" readonly="readonly">
                      <input type="text" name="apply_date" id="apply_date" value="<?php echo e($apply_date); ?>" readonly="readonly" disabled></span></div>
            <div class="col-sm-6 col-12"></div>
          </div>
		  
        
   
      <div class="nomination-note"> 
      	<!-- <small>*<?php echo e(__('step3.bottom_text1')); ?></small>  -->
        <small> *<?php echo e(__('step3.bottom_text2')); ?>.</small>
        <small> **<?php echo e(__('step3.bottom_text3')); ?>.</small> 
	</div>
	
	
	<div class="card-footer">
        <div class="row align-items-center">
          <div class="col-sm-6 col-12"> <a href="<?php echo e($href_back); ?>" id="" class="btn btn-lg btn-secondary font-big"><?php echo e(__('step1.Back')); ?></a> </div>
          <div class="col-sm-6 col-12">
            <div class="apt-btn text-right"> 
			<div class="col ">
                      <div class="form-group row float-right">
					  <a href="<?php echo url('/'); ?>/dashboard-nomination-new" class="btn btn-lg font-big dark-pink-btn"><?php echo e(__('step1.Cancel')); ?></a> 
					  &nbsp;	
					  &nbsp;
					  &nbsp;
					  &nbsp;
					<?php if($user_profile_state==$st_code): ?>	
					<button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn" id="sub" onclick="return checkEPic();"><?php echo e(__('step1.Save_Next')); ?></button>
					<?php else: ?>  
					<button type="button" class="btn btn-lg font-big dark-purple-btn pop-actn"  onclick="return showValMessage();"><?php echo e(__('step1.Save_Next')); ?></button>
					<?php endif; ?>
                      </div>
            </div>
			
			</div>
          </div>
        </div>
      </div>	  
		  
	</form>
	
	    </div><!-- End Of part-1 Div -->
		
		<div class="part-2" id="part_2" style="display:<?php echo $disp2; ?>">
		 <form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
		 
			<input type="hidden" name="user_profile_state" value="<?php echo e($user_profile_state); ?>">
			<input type="hidden" name="st_code" value="<?php echo e($st_code); ?>">
		  
		 
          <h3 class="part-title"><span><?php echo e(__('step3.part2')); ?></span></h3>
          <h6 class="part-sub-title mb-5">(<?php echo e(__('step3.notrec')); ?>)</h6>
				  <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                  <input type="hidden" name="recognized_party" value="not-recognized">
                  <input type="hidden" name="nomination_id" value="<?php echo e($nomination_id); ?>">
                  <div class="fullwidth">
                    <div class="text-center fullwidth">
					
                      <div class="form-group ">
                        <div class="fullwidth float-right animate-wrap" style="width: 100%;"> 
							<div class="animate-help-text profile_img" style="display:<?php echo (!empty($profileimg))?'none':'block';?>">
								<div class="help-text"><?php echo e(__('messages.arpro')); ?></div>
								<div class="animate-icon">
									  <div class="box bounce-2"><i class="fa fa-hand-o-right" aria-hidden="true"></i></div>
								</div>
						    </div><!-- End Of animate-help-text Div -->
						
                          <div class="browse_image_outer">
							<span  style="font-size: 11px; margin-left: -4px;">Size (2cm X 2.5cm)</span>
                            <div class="avatar-upload btn file-frame">
                              <img src="<?php echo e($thumb); ?>" class="img-responsive">
                              <button class="file btn" type="button">Browse <i class="fa fa-upload"></i></button>
                              <input type="hidden" name="image" class="image" value="<?php echo e($profileimg); ?>">
                            </div>
                          </div>
                        </div>
                      </div>
                     </div>
					 
			
			
            <div class="form-group row align-items-center justify-contant-around py-2 px-5">
              <label for="" class="lbl-mandry col-sm-6 col-12 text-right pr-4"><?php echo e(__('step3.nomac')); ?></label>
			  <select name="legislative_assembly" id="legislative_assembly" class="form-control col-sm-4 col-12 ac_no" disabled>
			  <option value=""><?php echo e(__('step3.select')); ?></option>
			  <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			  <?php if($iterate_ac['pc_no'] == $pc_no): ?>
			  <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
			  <?php else: ?>
			  <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
			  <?php endif; ?>
			  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</select>		  
            </div>
			<input type="hidden" name="legislative_assembly" id="legislative_assembly" value="<?php echo e($ac_no); ?>">
			
			
			
			
            <fieldset class="py-4 px-5 mb-4">
              <legend><?php echo e(__('step3.candinfo')); ?></legend>
			  
			  <div class="row">
			   <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="" style="font-weight:500"><?php echo e(__('step3.fetch')); ?></label>
				  <div class="input-group"  style="width: 250px;">
								  <input type="text" name="epic_no" id="epic_no2" class="form-control"  placeholder="<?php echo e(__('step3.enterepic')); ?>"   value="<?php echo e($epic_no); ?>"  readonly>
								  <div class="input-group-append">
									<!--<button class="btn btn-success" type="button" id="epic_no_search2" onclick="return getDetailsPartTwo();">-->
									<button class="btn btn-success" type="button" id="epic_no_search2">
									<i class="fa fa-search" aria-hidden="true"></i>
									</button>
								  </div>								 
								</div>
								 <span style="color:red;font-size:12px;" id="error_2"></span>
                </div>
              </div>
			</div> 
			<br>
			  
              <div class="row">
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.candidatename')); ?></label>
                     <input type="text" name="name" id="namett" class="form-control nomination-field-2" placeholder="<?php echo e(__('step3.candidatename')); ?>" value="<?php echo e($name); ?>" class="form-control" readonly> 
                  </div>
                </div>
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.father_husband')); ?></label>
                    <input type="text" name="father_name" id="father_namett" placeholder="<?php echo e(__('step3.father_husband')); ?>" value="<?php echo $father_name; ?>" class="form-control" readonly>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.portal_address')); ?></label>
                     <input type="text" name="address" id="addresstt" placeholder="<?php echo e(__('step3.portal_address')); ?>" value="<?php echo $address; ?>" class="form-control" readonly> 
                  </div>
                </div>
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.sno')); ?></label>
                    <input type="number" name="serial_no" id="serial_nott" class="form-control" placeholder="<?php echo e(__('step3.sno')); ?>" value="<?php if($serial_no > 0 ): ?><?php echo e($serial_no); ?><?php endif; ?>" min="1" readonly>
                  </div>
                </div>
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.pno')); ?></label>
                     <input type="number" name="part_no" id="part_nott" placeholder="<?php echo e(__('step3.pno')); ?>" class="form-control" value="<?php if($part_no> 0 ): ?><?php echo e($part_no); ?><?php endif; ?>" min="1" readonly>
                  </div>
                </div>
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.cac')); ?>   </label>
                    <select name="resident_ac_no" id="resident_ac_nott" class="form-control" disabled>
					  <option value=""><?php echo e(__('step3.select')); ?></option>
					  <?php $__currentLoopData = $resident_acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					  <?php if($iterate_ac['pc_no'] == $resident_ac_no): ?>
					  <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
					  <?php else: ?>
					  <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
					  <?php endif; ?>
					  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
					<span id="state_error_cand2" style="color:red;font:8px;"></span>
                  </div>
                </div>
              </div>
            </fieldset>
			 <input type="hidden" name="resident_ac_no" id="resident_ac_nott" value="<?php echo e($resident_ac_no); ?>">
            <div class="row my-3">
              <div class="col-sm-6 col-12"><span class="nomination-date left"><?php echo e(__('step3.date')); ?> 
							<input type="hidden" name="apply_date" id="apply_date" value="<?php echo e($apply_date); ?>" readonly="readonly" >
                            <input type="text" name="apply_date"  id="apply_date" value="<?php echo e($apply_date); ?>" readonly="readonly" disabled>
                          </span></div>
              <div class="col-sm-6 col-12"></div>
            </div>
          <p class="my-4"><?php echo e(__('step3.decl')); ?> : - </p>
          <h5 class="text-center pb-2"><?php echo e(__('step3.particular')); ?> </h5> 
          <div class="table-responsive part-table">
          
			
                    <table class="table table-bordered proposers-table">
                      <thead>
                        <tr  style="background:#f0587e">
                          <th><?php echo e(__('step3.sno')); ?></th>
						  <th><?php echo e(__('step3.fetchprop')); ?></th>
                          <th colspan="2"><?php echo e(__('step3.eroll')); ?></th>
                          <th><?php echo e(__('step3.fullnam')); ?></th>
                          <th><?php echo e(__('step3.date')); ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr style="background:#f0587e;">
                          <td>&nbsp;</td>
						  <td>&nbsp;</td>
                          <td style="color:white;"><?php echo e(__('step3.epart')); ?></td>
                          <td style="color:white;"><?php echo e(__('step3.spart')); ?></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                        <?php $key = 0; $i=1;
                        foreach($non_recognized_proposers as $iterate_proposer){ 
						if($iterate_proposer['part_no'] == 0){
							$iterate_proposer['part_no']='';
						}
						if($iterate_proposer['serial_no'] == 0){
							$iterate_proposer['serial_no']='';
						}
						
						//echo "<pre>"; print_r($iterate_proposer); ?>
                          <tr class="non_recognized_proposers_row">
							<td><?php echo e($i); ?>

                              <input type="hidden" name="non_recognized_proposers[<?php echo e($key); ?>][s_no]" value="<?php echo e($iterate_proposer['s_no']); ?>">
                              <input type="hidden" name="non_recognized_proposers[<?php echo e($key); ?>][candidate_id]" value="<?php echo e($iterate_proposer['candidate_id']); ?>">
                              <input type="hidden" name="non_recognized_proposers[<?php echo e($key); ?>][nomination_id]" value="<?php echo e($iterate_proposer['nomination_id']); ?>">
                            </td>
							
							
                            <td>
								<div class="input-group"  style="width: 250px;">
								  <input type="text"  name="non_recognized_proposers[<?php echo e($key); ?>][epic_no_proposer_serch_part_2]" value="<?php echo e($iterate_proposer['epic_no_proposer_serch_part_2']); ?>" id="epic_no_<?php echo e($i); ?>" onkeyup="myFunction(<?php echo e($i); ?>)" class="form-control"  placeholder="<?php echo e(__('step3.enterepic')); ?>"  />
								  <div class="input-group-append">
									<button class="btn btn-success" type="button" id="epic_no_search_<?php echo e($i); ?>" onclick="return getEPicDetails(<?php echo e($i); ?>);">
									<i class="fa fa-search" aria-hidden="true"></i>
									</button>
								  </div>								 
								</div>
								 <span style="color:red;font-size:12px;" id="errorDatat_<?php echo e($i); ?>"></span> 
							</td>
										
							
                            <td><input type="number" placeholder="<?php echo e(__('step3.pno')); ?>" class="form-control  particulars-field-12" name="non_recognized_proposers[<?php echo e($key); ?>][part_no]" value="<?php echo e($iterate_proposer['part_no']); ?>" id="p_<?php echo e($i); ?>" ></td>
							<td><input type="number" placeholder="<?php echo e(__('step3.sno')); ?>" class="form-control  particulars-field-12" name="non_recognized_proposers[<?php echo e($key); ?>][serial_no]" value="<?php echo e($iterate_proposer['serial_no']); ?>" id="s_<?php echo e($i); ?>" ></td>
                            <td><input type="text" placeholder="<?php echo e(__('step3.fullnam')); ?>" class="form-control  particulars-field-12 alphaonly" id="fullname_<?php echo e($i); ?>" name="non_recognized_proposers[<?php echo e($key); ?>][fullname]" value="<?php echo e($iterate_proposer['fullname']); ?>" ><span id="error_message"></span></td>
                            <input type="hidden" class="form-control " name="non_recognized_proposers[<?php echo e($key); ?>][signature]" value="<?php echo e($iterate_proposer['signature']); ?>">
                            <td><input type="text" class="form-control particulars-field-12 recognized_date" name="non_recognized_proposers[<?php echo e($key); ?>][date]" value="<?php echo e($iterate_proposer['date']); ?>" readonly="readonly"></td>
                          </tr>
                          <?php $key++; $i++; } ?>
                        </tbody>
                      </table>
                    
		  
		  </div>
          <!-- End Of responsive table Div -->
          <div class="nomination-note"> 
		  
		  <small>*<?php echo e(__('step3.bottom_text1')); ?></small> 
		  
		  <small> *<?php echo e(__('step3.bottom_text2')); ?></small> 
		  
		  <small> **<?php echo e(__('step3.bottom_text3')); ?></small> </div>
       

		 <div class="card-footer">
        <div class="row align-items-center">
          <div class="col-sm-6 col-12"> <a href="<?php echo e($href_back); ?>" id="" class="btn btn-lg btn-secondary font-big"><?php echo e(__('step1.Back')); ?></a> </div>
          <div class="col-sm-6 col-12">
		  
		  
            <div class="apt-btn text-right"> 
			
			<a href="<?php echo url('/'); ?>/dashboard-nomination-new" class="btn btn-lg font-big dark-pink-btn"><?php echo e(__('step1.Cancel')); ?></a> 
					  &nbsp;	
					  &nbsp;
					  &nbsp;
					  &nbsp;
					<?php if($user_profile_state==$st_code): ?>	
					<button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return checkVal();"><?php echo e(__('step1.Save_Next')); ?></button>
					<?php else: ?>  
					<button type="button" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return showValMessage();"><?php echo e(__('step1.Save_Next')); ?> </button>
					<?php endif; ?>
            
			 </div>
			
          </div>
        </div>
      </div>
	

		</form>
	   </div>
        <!-- End Of part-2 div --> 
	  </div><!-- End Of card-body Div -->  
	  
	  
	  
	  
	<!-- Both Part Start Here  -->  
	
	<div class="part-2" id="part_3" style="display:<?php echo $disp3; ?>">
		 <form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
			<input type="hidden" name="user_profile_state" value="<?php echo e($user_profile_state); ?>">
			<input type="hidden" name="st_code" value="<?php echo e($st_code); ?>">
			
			
		
		  
		 <h3 class="part-title"><span><?php echo e(__('step3.partn')); ?></span></h3>
          <h6 class="part-sub-title mb-5">(<?php echo e(__('step3.recc')); ?>)</h6>
				  <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
		  
		  
		   <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
		   <input type="hidden" name="recognized_party" value="both">
		   <input type="hidden" name="nomination_id" value="<?php echo e($nomination_id); ?>">
		  
          
		  
		  <div class="form-group ">
                  <div class="fullwidth float-right animate-wrap" style="width: 100%;">
					<div class="animate-help-text profile_img" style="display:<?php echo (!empty($profileimg))?'none':'block';?>">
						<div class="help-text"><?php echo e(__('messages.arpro')); ?></div>
						<div class="animate-icon">
							  <div class="box bounce-2"><i class="fa fa-hand-o-right" aria-hidden="true"></i></div>
						</div>
					</div><!-- End Of animate-help-text Div -->
                    <div class="browse_image_outer">
					<span  style="font-size: 11px; margin-left: 8px;">Size (2cm X 2.5cm)</span>
                      <div class="avatar-upload btn file-frame">
                              <img src="<?php echo e($thumb); ?>" class="img-responsive">
                              <button class="file btn" type="button">Browse <i class="fa fa-upload"></i></button>
                              <input type="hidden" name="image" class="image" value="<?php echo e($profileimg); ?>">
                      </div>
                    </div>
                  </div>
                </div>
				
			
			<div class="form-group row align-items-center justify-contant-around py-2 px-5">
              <label for="" class="lbl-mandry col-sm-6 col-12 text-right pr-4"><?php echo e(__('step3.nomac')); ?></label>
			  <select name="legislative_assembly" id="legislative_assembly" class="form-control col-sm-4 col-12 ac_no" disabled>
			  <option value=""><?php echo e(__('step3.select')); ?></option>
			  <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			  <?php if($iterate_ac['pc_no'] == $ac_no): ?>
			  <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
			  <?php else: ?>
			  <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
			  <?php endif; ?>
			  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</select>		  
            </div>
			
			<input type="hidden" name="legislative_assembly" id="legislative_assembly" class="form-control nomination-field-2" value="<?php echo e($ac_no); ?>">
			
			
			
			
		  <fieldset class="py-4 px-5 mb-4">
            <legend><?php echo e(__('step3.candinfo')); ?></legend>
			
			<div class="row">
			   <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class=""><?php echo e(__('step3.fetch')); ?></label>
				  <div class="input-group"  style="width: 250px;">
								  <input type="text" name="epic_no" id="epic_no" class="form-control"  placeholder="<?php echo e(__('step3.enterepic')); ?>"  value="<?php echo e($epic_no); ?>" readonly>
								  <div class="input-group-append">
									<!--<button class="btn btn-success" type="button" id="epic_no_search" onclick="return getDetailsPartOne();">-->
									<button class="btn btn-success" type="button" id="epic_no_search">
									<i class="fa fa-search" aria-hidden="true"></i>
									</button>
								  </div>								 
								</div>
								 <span style="color:red;font-size:12px;" id="error_1"></span>
                </div>
              </div>
			</div>  
			
            <div class="row">
			  
			   
			
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.candidatename')); ?></label> 
                   <input type="text" name="name" id="name" class="form-control nomination-field-2 alphaonly" placeholder="<?php echo e(__('step3.candidatename')); ?>" value="<?php echo e($name); ?>" readonly>
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.father_husband')); ?></label>
                   <input type="text" name="father_name" id="father_name" placeholder="<?php echo e(__('step3.father_husband')); ?>" value="<?php echo $father_name; ?>" class="form-control nomination-field-3 alphaonly" readonly>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.portal_address')); ?></label>
                  <input type="text" name="address" id="address" placeholder="<?php echo e(__('step3.portal_address')); ?>" value="<?php echo $address; ?>" class="form-control nomination-field-12" readonly> 
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.sno')); ?></label>
                  <input type="number" name="serial_no" id="serial_no" class="form-control nomination-field-2" placeholder="<?php echo e(__('step3.sno')); ?>" value="<?php echo e($serial_no); ?>" min="1" readonly>
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.pno')); ?></label>
                  <input type="number" name="part_no" id="part_no" placeholder="<?php echo e(__('step3.pno')); ?>" class="form-control nomination-field-2" value="<?php if($part_no!=0): ?><?php echo e($part_no); ?><?php endif; ?>" min="1" readonly>
                </div>
              </div>
			 
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.elecpc')); ?></label>
                  <select name="resident_ac_no" id="resident_ac_no" class="form-control nomination-field-2" disabled>
                      <option value=""><?php echo e(__('step3.select')); ?> </option>
                      <?php $__currentLoopData = $resident_acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if($iterate_ac['pc_no'] == $resident_ac_no): ?>
                      <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
                      <?php else: ?>
                      <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
                      <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
					<span id="state_error_cand" style="color:red;font:8px;"></span>
                </div>
              </div>
            </div>
          </fieldset>
            <input type="hidden" name="resident_ac_no" id="resident_ac_no" class="form-control nomination-field-2" value="<?php echo e($resident_ac_no); ?>">
		  
          <fieldset class="py-4 px-5 mt-2 mb-4">
            <legend><?php echo e(__('step3.pinfo')); ?></legend>
			<?php
			$abc1='';
			$abc2='';
			if(!empty($epic_no_proposer_serch)){
			$abc1='disabled';
			$abc2='blcok;';
			} else {
			$abc1='';
			$abc2='none;';	
			}
			?>
			<div class="row">
			   <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2 animate-wrap">
				<div class="animate-help-text dir-lft epic_wrap2" style="display:<?php echo (!empty($epic_no_proposer_serch))?'none':'block';?>">
				    <div class="help-text"><?php echo e(__('messages.arepic')); ?></div>
					<div class="animate-icon">
					      <div class="box bounce-2"><i class="fa fa-hand-o-left" aria-hidden="true"></i></div>
					</div>
				</div><!-- End Of animate-help-text Div -->
                  <label for="" class=""><?php echo e(__('step3.fetchprop')); ?></label>
				  <div class="input-group"  style="width: 250px;"> 
								  <input type="text" name="epic_no_proposer_serch" id="epic_no_p_both" class="form-control"  placeholder="<?php echo e(__('step3.enterepic')); ?>" value="<?php echo e($epic_no_proposer_serch); ?>"/>
								  <div class="input-group-append">
									<button class="btn btn-success" type="button" id="epic_no_search_p_2" onclick="return getDetailsPartOne_p_both();">
									<i class="fa fa-search" aria-hidden="true"></i>
									</button>
								<!--	<img src="<?php echo e(asset('img/cancel.jpg')); ?>" height="30" width="30" id="mg_both" style="display:<?php echo e($abc2); ?>;cursor:pointer;" onclick="return clearPro_both();">-->
								  </div>								 
								</div>
								 <span style="color:red;font-size:12px;" id="error_p"></span>
                </div>
              </div>
			</div>  
			<!--<input type="hidden" name="epic_no_proposer_serch" id="epic7_both" class="form-control" value="<?php echo e($epic_no_proposer_serch); ?>"  />-->
            <div class="row">
              <div class="col-sm-6 col-12">
                <div class="form-group mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.pname')); ?></label>
                   <input type="text" name="proposer_name" id="proposer_name_both" value="<?php echo e($proposer_name); ?>" class="form-control nomination-field-2 alphaonly" placeholder="<?php echo e(__('step3.pname')); ?>" > 
                </div>
              </div>              
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.psno')); ?></label>
                  <input type="number" name="proposer_serial_no" id="proposer_serial_no_both" class="form-control nomination-field-2" value="<?php if($proposer_serial_no!=0): ?><?php echo e($proposer_serial_no); ?><?php endif; ?>" placeholder="<?php echo e(__('step3.psno')); ?>" min="1" > 
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.ppno')); ?></label>
                  <input type="number" name="proposer_part_no" id="proposer_part_no_both" value="<?php if($proposer_part_no!=0): ?><?php echo e($proposer_part_no); ?><?php endif; ?>" placeholder="<?php echo e(__('step3.ppno')); ?>" class="form-control nomination-field-2" min="1" > 
                </div>
              </div>
			  
			  <div class="col-sm-6 col-12">
                <div class="form-group mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('step3.ppc')); ?></label>
                  <select name="proposer_assembly" id="proposer_assembly_both" class="form-control nomination-field-2" onchange="return checkProposalAc(this.value, 'state_error_both', 'sub_both');">
                      <option value=""><?php echo e(__('step3.select')); ?></option>
                      <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if($iterate_ac['pc_no'] == $proposer_assembly): ?>
                      <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
                      <?php else: ?>
                      <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
                      <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
					<span id="state_error_both" style="color:red;font:8px;"></span>
                </div>
              </div>
            </div>
          </fieldset>
		  <!--<input type="hidden" name="proposer_assembly" id="proposer_assembly2_both" value="<?php echo e($proposer_assembly); ?>">-->
          <div class="row my-3">
            <div class="col-sm-6 col-12"><strong><?php echo e(__('step3.date')); ?>:</strong> <span> 
					  <input type="hidden" name="apply_date"  id="apply_date" value="<?php echo e($apply_date); ?>" readonly="readonly">
                      <input type="text" name="apply_date" id="apply_date" value="<?php echo e($apply_date); ?>" readonly="readonly" disabled></span></div>
            <div class="col-sm-6 col-12"></div>
          </div>
		  
        
   
      <div class="nomination-note"> <small>*<?php echo e(__('step3.bottom_text1')); ?></small> 
        <small> *<?php echo e(__('step3.bottom_text2')); ?>.</small>
        <small> **<?php echo e(__('step3.bottom_text3')); ?>.</small> 
	</div>	
		  
		 <!--  Below Part Two  -->
          <h3 class="part-title"><span><?php echo e(__('step3.part2')); ?> </span></h3>
          <h6 class="part-sub-title mb-5">(<?php echo e(__('step3.notrec')); ?>)</h6>
                  <div class="fullwidth">
            <div class="form-group row align-items-center justify-contant-around py-2 px-5">
              <label for="" class="lbl-mandry col-sm-6 col-12 text-right pr-4"><?php echo e(__('step3.nomac')); ?></label>
			  <select name="legislative_assembly" id="legislative_assembly" class="form-control col-sm-4 col-12 ac_no" disabled>
			  <option value=""><?php echo e(__('step3.select')); ?></option>
			  <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			  <?php if($iterate_ac['pc_no'] == $ac_no): ?>
			  <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
			  <?php else: ?>
			  <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
			  <?php endif; ?>
			  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</select>		  
            </div>
			<input type="hidden" name="legislative_assembly" id="legislative_assembly" value="<?php echo e($ac_no); ?>">
			
			
			
			
            <fieldset class="py-4 px-5 mb-4">
              <legend><?php echo e(__('step3.candinfo')); ?></legend>
			  
			  <div class="row">
			   <div class="col-sm-6 col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="" style="font-weight:500"><?php echo e(__('step3.fetch')); ?></label>
				  <div class="input-group"  style="width: 250px;">
								  <input type="text" name="epic_no" id="epic_no2" class="form-control"  placeholder="<?php echo e(__('step3.enterepic')); ?>"   value="<?php echo e($epic_no); ?>"  readonly>
								  <div class="input-group-append">
									<!--<button class="btn btn-success" type="button" id="epic_no_search2" onclick="return getDetailsPartTwo();">-->
									<button class="btn btn-success" type="button" id="epic_no_search2">
									<i class="fa fa-search" aria-hidden="true"></i>
									</button>
								  </div>								 
								</div>
								 <span style="color:red;font-size:12px;" id="error_2"></span>
                </div>
              </div>
			</div> 
			<br>
			  
              <div class="row">
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.candidatename')); ?></label>
                     <input type="text" name="name" id="namett" class="form-control nomination-field-2" placeholder="<?php echo e(__('step3.candidatename')); ?>" value="<?php echo e($name); ?>" class="form-control" readonly> 
                  </div>
                </div>
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.father_husband')); ?></label>
                    <input type="text" name="father_name" id="father_namett" placeholder="<?php echo e(__('step3.father_husband')); ?>" value="<?php echo $father_name; ?>" class="form-control" readonly>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.portal_address')); ?></label>
                     <input type="text" name="address" id="addresstt" placeholder="<?php echo e(__('step3.portal_address')); ?>" value="<?php echo $address; ?>" class="form-control" readonly> 
                  </div>
                </div>
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.sno')); ?></label>
                    <input type="number" name="serial_no" id="serial_nott" class="form-control" placeholder="<?php echo e(__('step3.sno')); ?>" value="<?php if($serial_no > 0 ): ?><?php echo e($serial_no); ?><?php endif; ?>" min="1" readonly>
                  </div>
                </div>
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.pno')); ?></label>
                     <input type="number" name="part_no" id="part_nott" placeholder="<?php echo e(__('step3.pno')); ?>" class="form-control" value="<?php if($part_no> 0 ): ?><?php echo e($part_no); ?><?php endif; ?>" min="1" readonly>
                  </div>
                </div>
                <div class="col-sm-6 col-12">
                  <div class="form-group mb-2">
                    <label for="" class="lbl-mandry"><?php echo e(__('step3.cac')); ?></label>
                    <select name="resident_ac_no" id="resident_ac_nott" class="form-control" disabled>
					  <option value=""><?php echo e(__('step3.select')); ?></option>
					  <?php $__currentLoopData = $resident_acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_ac): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					  <?php if($iterate_ac['pc_no'] == $resident_ac_no): ?>
					  <option value="<?php echo $iterate_ac['pc_no']; ?>" selected="selected"><?php echo $iterate_ac['pc_name']; ?></option>
					  <?php else: ?>
					  <option value="<?php echo $iterate_ac['pc_no']; ?>"><?php echo $iterate_ac['pc_name']; ?></option>
					  <?php endif; ?>
					  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
					<span id="state_error_cand2" style="color:red;font:8px;"></span>
                  </div>
                </div>
              </div>
            </fieldset>
			 <input type="hidden" name="resident_ac_no" id="resident_ac_nott" value="<?php echo e($resident_ac_no); ?>">
            <div class="row my-3">
              <div class="col-sm-6 col-12"><span class="nomination-date left"><?php echo e(__('step3.date')); ?> 
							<input type="hidden" name="apply_date" id="apply_date" value="<?php echo e($apply_date); ?>" readonly="readonly" >
                            <input type="text" name="apply_date"  id="apply_date" value="<?php echo e($apply_date); ?>" readonly="readonly" disabled>
                          </span></div>
              <div class="col-sm-6 col-12"></div>
            </div>
          <p class="my-4"><?php echo e(__('step3.decl')); ?> : - </p>
          <h5 class="text-center pb-2"><?php echo e(__('step3.particular')); ?> </h5> 
          <div class="table-responsive part-table">
          
			
                    <table class="table table-bordered proposers-table">
                      <thead>
                        <tr  style="background:#f0587e">
                          <th><?php echo e(__('step3.sno')); ?></th>
						  <th><?php echo e(__('step3.fetchprop')); ?></th>
                          <th colspan="2"><?php echo e(__('step3.eroll')); ?></th>
                          <th><?php echo e(__('step3.fullnam')); ?></th>
                          <th><?php echo e(__('step3.date')); ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr style="background:#f0587e;">
                          <td>&nbsp;</td>
						  <td>&nbsp;</td>
                          <td style="color:white;"><?php echo e(__('step3.epart')); ?></td>
                          <td style="color:white;"><?php echo e(__('step3.spart')); ?></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                        <?php $key = 0; $i=1;
                        foreach($non_recognized_proposers as $iterate_proposer){ 
						if($iterate_proposer['part_no'] == 0){
							$iterate_proposer['part_no']='';
						}
						if($iterate_proposer['serial_no'] == 0){
							$iterate_proposer['serial_no']='';
						}
						
						//echo "<pre>"; print_r($iterate_proposer); ?>
                          <tr class="non_recognized_proposers_row">
							<td><?php echo e($i); ?>

                              <input type="hidden" name="non_recognized_proposers[<?php echo e($key); ?>][s_no]" value="<?php echo e($iterate_proposer['s_no']); ?>">
                              <input type="hidden" name="non_recognized_proposers[<?php echo e($key); ?>][candidate_id]" value="<?php echo e($iterate_proposer['candidate_id']); ?>">
                              <input type="hidden" name="non_recognized_proposers[<?php echo e($key); ?>][nomination_id]" value="<?php echo e($iterate_proposer['nomination_id']); ?>">
                            </td>
							
							
                            <td>
								<div class="input-group"  style="width: 250px;">
								  <input type="text"  name="non_recognized_proposers[<?php echo e($key); ?>][epic_no_proposer_serch_part_2]" value="<?php echo e($iterate_proposer['epic_no_proposer_serch_part_2']); ?>" id="epic_no_both_<?php echo e($i); ?>" class="form-control"  placeholder="<?php echo e(__('step3.enterepic')); ?>"  />
								  <div class="input-group-append">
									<button class="btn btn-success" type="button" id="epic_no_search_both_<?php echo e($i); ?>" onclick="return getEPicDetails_both(<?php echo e($i); ?>);">
									<i class="fa fa-search" aria-hidden="true"></i>
									</button>
								  </div>								 
								</div>
								 <span style="color:red;font-size:12px;" id="errorDatat_both_<?php echo e($i); ?>"></span> 
							</td>
										
							
                            <td><input type="number" placeholder="<?php echo e(__('step3.pno')); ?>" class="form-control  particulars-field-12" name="non_recognized_proposers[<?php echo e($key); ?>][part_no]" value="<?php echo e($iterate_proposer['part_no']); ?>" id="p_both_<?php echo e($i); ?>" ></td>
							<td><input type="number" placeholder="<?php echo e(__('step3.sno')); ?>" class="form-control  particulars-field-12" name="non_recognized_proposers[<?php echo e($key); ?>][serial_no]" value="<?php echo e($iterate_proposer['serial_no']); ?>" id="s_both_<?php echo e($i); ?>" ></td>
                            <td><input type="text" placeholder="<?php echo e(__('step3.fullnam')); ?>" class="form-control  particulars-field-12 alphaonly" id="fullname_both_<?php echo e($i); ?>" name="non_recognized_proposers[<?php echo e($key); ?>][fullname]" value="<?php echo e($iterate_proposer['fullname']); ?>" ><span id="error_message_both"></span></td>
                            <input type="hidden" class="form-control " name="non_recognized_proposers[<?php echo e($key); ?>][signature]" value="<?php echo e($iterate_proposer['signature']); ?>">
                            <td><input type="text" class="form-control particulars-field-12 recognized_date" name="non_recognized_proposers[<?php echo e($key); ?>][date]" value="<?php echo e($iterate_proposer['date']); ?>" readonly="readonly"></td>
                          </tr>
                          <?php $key++; $i++; } ?>
                        </tbody>
                      </table>
                    
		  
		  </div>
          <!-- End Of responsive table Div -->
          <div class="nomination-note"> 
		  
		  <small>*<?php echo e(__('step3.bottom_text1')); ?></small> 
		  
		  <small> *<?php echo e(__('step3.bottom_text2')); ?></small> 
		  
		  <small> **<?php echo e(__('step3.bottom_text3')); ?></small> </div>
       

		 <div class="card-footer">
        <div class="row align-items-center">
          <div class="col-sm-6 col-12"> <a href="<?php echo e($href_back); ?>" id="" class="btn btn-lg btn-secondary font-big"><?php echo e(__('step1.Back')); ?></a> </div>
          <div class="col-sm-6 col-12">
		  
		  
            <div class="apt-btn text-right"> 
			
			<a href="<?php echo url('/'); ?>/dashboard-nomination-new" class="btn btn-lg font-big dark-pink-btn"><?php echo e(__('step1.Cancel')); ?></a> 
					  &nbsp;	
					  &nbsp;
					  &nbsp;
					  &nbsp;

					<?php if($user_profile_state==$st_code): ?>	
					<button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn" id="sub_both" onclick="return checkVal_both();"><?php echo e(__('step1.Save_Next')); ?></button>
					<?php else: ?>  
					<button type="button" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return showValMessage();"><?php echo e(__('step1.Save_Next')); ?> </button>
					<?php endif; ?>
            
			 </div>
			
          </div>
        </div>
      </div>
	

		</form>
	   </div>
        <!-- End Of part-2 div --> 
	  </div><!-- End Of card-body Div -->   
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	<!--End Both Part Start Here  -->  
	  
	  
	  
	  
      
     
  </div>
	</div><!-- End Of container-fluid Div -->	  
</main>

      <?php $__env->stopSection(); ?>

      <?php $__env->startSection('script'); ?> 
	  
	  
	
	  
      <script type="text/javascript" src="<?php echo e(asset('admintheme/js/jquery-ui.js')); ?>"></script>
	  <script type="text/javascript">
	  
	  
	   // if(json['basic'].st_code == '<?php echo $st_code ?>' && json['basic'].ac_no == '<?php echo $ac_no ?>'){       
		function checkProposalAc(val, error, sub){
			
			var selAc = $("#legislative_assembly").val();
						 if(val != selAc){
							 $("#"+sub).prop("disabled", true);
							 $('#'+error).html('<?php echo  __('step3.epic_state_missing') ?>');
						} else {
							$("#"+sub).prop("disabled", false);
							$('#'+error).html('');
						}
		}
	  
		function checkEPic(){
		var cepic =  $("#epic_no").val();	
		var pepic =  $("#epic_no_p").val();	 
			if(pepic.length<10){ 
				alert("<?php echo e(__('step1.Epic_error')); ?> ");	
				$("#epic_no_p").focus();
				return false;	
			}
			if(cepic==pepic){
				alert("<?php echo  __('messages.canprop') ?>");
				return false;
			}
		}
	  
	  
	  
	  
		$( document ).ready(function() {
						$( ".alphaonly" ).keypress(function(e) { 
							 var charCode = e.keyCode;
							if(charCode!=32){
							 if (charCode > 31 && (charCode < 65 || charCode > 90) && (charCode < 97 || charCode > 122)) {
								return false;
							 }
							}
							return true;	
						});
					});
		</script>
	  <script> 
	    function showValMessage(){
			alert("<?php echo  __('messages.canstate') ?>"); 
			return false;
		}
	  
	    function getDetailsPartTwo(){ 
		$.ajax({
        url: "<?php echo url('search-by-epic-cdac-new'); ?>",
        type: 'GET',
        data: 'epic_no='+$('#epic_no2').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.loading_spinner').remove();
          $('.error_message').remove();
          $('#epic_no_search2').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          $('#epic_no_search2').prop('disabled', true);
        },  
        complete: function() {
          $('.loading_spinner').remove();
          $('#epic_no_search2').prop('disabled', false);
        },        
        success: function(json) { 
		  console.log(json); 
          if(json['success'] == false){
           // $('#epic_no').parent('.input-group').after("<span class='text-danger error_message'>"+json['message']+"</span>");
			$("#error_2").html(json['message']);
          }else{
            $(".main_div").removeClass("display_none");
			
			if("<?php echo Session::get('locale') == 'hi' ?>"){			
				if(json['basic'].name_v1 != '' && json['basic'].name_v1 != null){
				   $("#namett").val(json['basic'].name_v1);
				}
				if(json['basic'].rln_name_v1 != '' && json['basic'].rln_name_v1 != null){
				  $("#father_namett").val(json['basic'].rln_name_v1);
				}	
				if(json['address'].Address_V1 != '' && json['address'].Address_V1 != null){
				  $("#addresstt").val(json['address'].Address_V1);
				}
			
			} else {
				if(json['basic'].name != '' && json['basic'].name != null){
				   $("#namett").val(json['basic'].name);
				}
				if(json['basic'].rln_name != '' && json['basic'].rln_name != null){
				  $("#father_namett").val(json['basic'].rln_name);
				}	
				if(json['address'].Address != '' && json['address'].Address != null){
				  $("#addresstt").val(json['address'].Address);
				}
			}
            
            if(json['basic'].slno_inpart != '' && json['basic'].slno_inpart != null){
              $("#serial_nott").val(json['basic'].slno_inpart);
            }
			if(json['basic'].part_no != '' && json['basic'].part_no != null){
              $("#part_nott").val(json['basic'].part_no);
            }
			
			
			if(json['basic'].st_code == '<?php echo $st_code ?>'){            	
				if(json['basic'].ac_no != '' && json['basic'].ac_no != null){ 
				  $('#resident_ac_nott').val(parseInt(json['basic'].ac_no));
				  $('#state_error_cand2').html("");
				} else {
					$('#state_error_cand2').html('<?php echo  __('step3.epic_ac_missing_cand') ?>');
				}
            } else {
				$('#state_error_cand2').html('<?php echo  __('step3.epic_state_missing_cand') ?>');
				$('#resident_ac_nott').val('');
			}
			

          }  
          $('.loading_spinner').remove();    
        },
        error: function(data) {
		  console.log(data);	
          var errors = data.responseJSON;
		   console.log(errors);
        }
      });
		
	}   
	function getDetailsPartOne(){ 
		$.ajax({
        url: "<?php echo url('search-by-epic-cdac-new'); ?>",
        type: 'GET',
        data: 'epic_no='+$('#epic_no').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.loading_spinner').remove();
          $('.error_message').remove();
          $('#epic_no_search').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          $('#epic_no_search').prop('disabled', true);
        },  
        complete: function() {
          $('.loading_spinner').remove();
          $('#epic_no_search').prop('disabled', false);
        },        
        success: function(json) { 
		  console.log(json); 
          if(json['success'] == false){ //alert(json['message']);
            //$('#epic_no').parent('.input-group').after("<span class='text-danger error_message'>"+json['message']+"</span>");
			$("#error_1").html(json['message']);
          }else{
            $(".main_div").removeClass("display_none");
            //alert("<?php echo Session::get('locale') ?>");
			if("<?php echo Session::get('locale') == 'hi' ?>"){			
				if(json['basic'].name_v1 != '' && json['basic'].name_v1 != null){
				   $("#name").val(json['basic'].name_v1);
				}
				if(json['basic'].rln_name_v1 != '' && json['basic'].rln_name_v1 != null){
				  $("#father_name").val(json['basic'].rln_name_v1);
				}	
				if(json['address'].Address_V1 != '' && json['address'].Address_V1 != null){
				  $("#address").val(json['address'].Address_V1);
				}
			
			} else {
				if(json['basic'].name != '' && json['basic'].name != null){
				   $("#name").val(json['basic'].name);
				}
				if(json['basic'].rln_name != '' && json['basic'].rln_name != null){
				  $("#father_name").val(json['basic'].rln_name);
				}	
				if(json['address'].Address != '' && json['address'].Address != null){
				  $("#address").val(json['address'].Address);
				}
			}			
			
			
			if(json['basic'].st_code == '<?php echo $st_code ?>'){            	
				if(json['basic'].ac_no != '' && json['basic'].ac_no != null){ 
				  $('#resident_ac_no').val(parseInt(json['basic'].pc_no));
				  $('#state_error_cand').html("");
				} else {
					$('#state_error_cand').html('<?php echo  __('step3.epic_ac_missing_cand') ?>');
				}
            } else {
				$('#state_error_cand').html('<?php echo  __('step3.epic_state_missing_cand') ?>');
				$('#resident_ac_no').val('');
			}
			
			
			
			
			
			
            if(json['basic'].slno_inpart != '' && json['basic'].slno_inpart != null){
              $("#serial_no").val(json['basic'].slno_inpart);
            }
			if(json['basic'].part_no != '' && json['basic'].part_no != null){
              $("#part_no").val(json['basic'].part_no);
            }


          }  
          $('.loading_spinner').remove();    
        },
        error: function(data) {
		  console.log(data);	
          var errors = data.responseJSON;
		   console.log(errors);
        }
      });
		
	}
	
	$("#epic_no_p").blur(function(){
		var epic_val = $(this).val();
		if(epic_val==''){
			$(".epic_wrap1").show();
		}else{
			$(".epic_wrap1").hide();
		}
	});
	function getDetailsPartOne_p(){ 
		$.ajax({
        url: "<?php echo url('search-by-epic-cdac-new'); ?>",
        type: 'GET',
        data: 'epic_no='+$('#epic_no_p').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.loading_spinner').remove();
          $('.error_message').remove();
          $('#epic_no_search_p').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          $('#epic_no_search_p').prop('disabled', true);
        },  
        complete: function() {
          $('.loading_spinner').remove();
          $('#epic_no_search_p').prop('disabled', false);
        },        
        success: function(json) { 
		  console.log(json); 
          if(json['success'] == false){
           // $('#epic_no').parent('.input-group').after("<span class='text-danger error_message'>"+json['message']+"</span>");
			$("#error_p").html(json['message']);
          }else{
            $(".main_div").removeClass("display_none");
			$(".epic_wrap1").hide();
			if("<?php echo Session::get('locale') == 'hi' ?>"){			
				if(json['basic'].name_v1 != '' && json['basic'].name_v1 != null){
				   $("#proposer_name").val(json['basic'].name_v1);
				}
			
			} else {
				if(json['basic'].name != '' && json['basic'].name != null){
				   $("#proposer_name").val(json['basic'].name);
				}
			}
			
		
			if(json['basic'].st_code === "<?php echo $st_code ?>"){          	
				if(json['basic'].ac_no != '' && json['basic'].ac_no != null){ 
				    if(json['basic'].ac_no == '<?php echo $ac_no ?>'){           
					  $('#proposer_assembly').val(parseInt(json['basic'].ac_no)); 
					  $('#proposer_assembly2').val(parseInt(json['basic'].ac_no)); 
					  $("#sub").prop("disabled", false);
					  $('#state_error').html('');
					  $('#state_error').html("");
					}   else {
							$('#state_error').html('<?php echo  __('step3.epic_state_missing') ?>');
						}
				} else {
					$('#state_error').html('<?php echo  __('step3.epic_ac_missing') ?>');
				}
            } else {
				$('#state_error').html('<?php echo  __('step3.epic_state_missing') ?>');
				$('#proposer_assembly').val('');
				$('#proposer_assembly2').val('');
			}
			
			
            if(json['basic'].slno_inpart != '' && json['basic'].slno_inpart != null){
              $("#proposer_serial_no").val(json['basic'].slno_inpart);
            }
			if(json['basic'].part_no != '' && json['basic'].part_no != null){
              $("#proposer_part_no").val(json['basic'].part_no);
            }
          }  
          $('.loading_spinner').remove();    
		 // $("#epic_no_p").prop("disabled", true);
		  //$("#epic7").val($('#epic_no_p').val());
		  $("#epic_no_p").val($('#epic_no_p').val());
		  $("#mg").show();
        },
        error: function(data) {
		  console.log(data);	
          var errors = data.responseJSON;
		   console.log(errors);
        }
      });		
	}
	$("#epic_no_p_both").blur(function(){
		var epic_val = $(this).val();
		if(epic_val==''){
			$(".epic_wrap2").show();
		}else{
			$(".epic_wrap2").hide();
		}
	});
	
	function getDetailsPartOne_p_both(){ 
		$.ajax({
        url: "<?php echo url('search-by-epic-cdac-new'); ?>",
        type: 'GET',
        data: 'epic_no='+$('#epic_no_p_both').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.loading_spinner').remove();
          $('.error_message').remove();
          $('#epic_no_search_p_2').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          $('#epic_no_search_p_2').prop('disabled', true);
        },  
        complete: function() {
          $('.loading_spinner').remove();
          $('#epic_no_search_p_2').prop('disabled', false);
        },        
        success: function(json) { 
		  console.log(json); 
          if(json['success'] == false){
            $('#epic_no_p_both').parent('.input-group').after("<span class='text-danger error_message'>"+json['message']+"</span>");
			$("#error_p_both").html(json['message']);
          }else{
            $(".main_div").removeClass("display_none");
			$(".epic_wrap2").hide();
			if("<?php echo Session::get('locale') == 'hi' ?>"){			
				if(json['basic'].name_v1 != '' && json['basic'].name_v1 != null){
				   $("#proposer_name_both").val(json['basic'].name_v1);
				}
			
			} else {
				if(json['basic'].name != '' && json['basic'].name != null){
				   $("#proposer_name_both").val(json['basic'].name);
				}
			}
			
		
			if(json['basic'].st_code === "<?php echo $st_code ?>"){          	
				if(json['basic'].ac_no != '' && json['basic'].ac_no != null){ 
				    if(json['basic'].ac_no == '<?php echo $ac_no ?>'){           
					  $('#proposer_assembly_both').val(parseInt(json['basic'].ac_no)); 
					  $('#proposer_assembly2_both').val(parseInt(json['basic'].ac_no)); 
					  $('#state_error_both').html("");
					   $("#sub_both").prop("disabled", false);
					  $('#state_error_both').html('');
					}   else {
							$('#state_error_both').html('<?php echo  __('step3.epic_state_missing') ?>');
						}
				} else {
					$('#state_error_both').html('<?php echo  __('step3.epic_ac_missing') ?>');
				}
            } else {
				$('#state_error_both').html('<?php echo  __('step3.epic_state_missing') ?>');
				$('#proposer_assembly_both').val('');
				$('#proposer_assembly2_both').val('');
			}
			
			
            if(json['basic'].slno_inpart != '' && json['basic'].slno_inpart != null){
              $("#proposer_serial_no_both").val(json['basic'].slno_inpart);
            }
			if(json['basic'].part_no != '' && json['basic'].part_no != null){
              $("#proposer_part_no_both").val(json['basic'].part_no);
            }
          }  
          $('.loading_spinner').remove();    
		  //$("#epic_no_p_both").prop("disabled", true);
		  $("#epic7_both").val($('#epic_no_p_both').val());
		  $("#epic_no_p_both").val($('#epic_no_p_both').val());
		  $("#mg_both").show();
        },
        error: function(data) {
		  console.log(data);	
          var errors = data.responseJSON;
		   console.log(errors);
        }
      });		
	}
	
	function clearPro_both(){
		$("#proposer_name_both").val("");
		$("#proposer_serial_no_both").val("");
		$("#proposer_part_no_both").val("");
		$("#proposer_assembly_both").val("");
		$("#epic7_both").val("");
		$("#epic_no_p_both").prop("disabled", false);
		$("#mg_both").hide();
		$("#state_error_both").html("");
		
	}
	
	function clearPro(){
		$("#proposer_name").val("");
		$("#proposer_serial_no").val("");
		$("#proposer_part_no").val("");
		$("#proposer_assembly").val("");
		$("#epic7").val("");
		$("#epic_no_p").prop("disabled", false);
		$("#mg").hide();
		$("#state_error").html("");
		
	}
	
	function getEPicDetails(idt){	
		
      $.ajax({
        url: "<?php echo url('search-by-epic-cdac-new'); ?>",
        type: 'GET',
        data: 'epic_no='+$('#epic_no_'+idt).val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.loading_spinner').remove();
          $('.error_message').remove();
          $('#epic_no_search_'+idt).append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          $('#epic_no_search_'+idt).prop('disabled', true);
        },  
        complete: function() {
          $('.loading_spinner').remove();
          $('#epic_no_search_'+idt).prop('disabled', false);
        },        
        success: function(json) { 
		
          if(json['success'] == false){  
           //$('#epic_no_'+idt).parent('.input-group').after("<span class='text-danger error_message'>"+json['message']+"</span>");
			$("#errorDatat_"+idt).html(json['message']);
          }else{
            $(".main_div").removeClass("display_none");
			
			
			if(json['basic'].st_code == '<?php echo $st_code ?>' && json['basic'].ac_no == '<?php echo $ac_no ?>'){       
			
				if("<?php echo Session::get('locale') == 'hi' ?>"){			
					if(json['basic'].name_v1 != '' && json['basic'].name_v1 != null){
						$("#fullname_"+idt).val(json['basic'].name_v1);
					}
				} else {
					if(json['basic'].name != '' && json['basic'].name != null){
					  $("#fullname_"+idt).val(json['basic'].name);
					}
				}
				if(json['basic'].slno_inpart != '' && json['basic'].slno_inpart != null){
				  $("#s_"+idt).val(json['basic'].slno_inpart);
				}
				if(json['basic'].part_no != '' && json['basic'].part_no != null){
				  $("#p_"+idt).val(json['basic'].part_no);
				}
				$("#errorDatat_"+idt).html(''); 	
			} else {
				$("#errorDatat_"+idt).html('<?php echo  __('step3.epic_state_missing') ?>'); 
			}

          }  
          $('.loading_spinner').remove();    
        },
        error: function(data) { 
		  console.log(data);	
          var errors = data.responseJSON;
		   console.log(errors);
        }
      });
	  
	}  
	
	function getEPicDetails_both(idt){	
		
      $.ajax({
        url: "<?php echo url('search-by-epic-cdac-new'); ?>",
        type: 'GET',
        data: 'epic_no='+$('#epic_no_both_'+idt).val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.loading_spinner').remove();
          $('.error_message').remove();
          $('#epic_no_search_both_'+idt).append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          $('#epic_no_search_both_'+idt).prop('disabled', true);
        },  
        complete: function() {
          $('.loading_spinner').remove();
          $('#epic_no_search_both_'+idt).prop('disabled', false);
        },        
        success: function(json) { 
		
          if(json['success'] == false){  
           //$('#epic_no_'+idt).parent('.input-group').after("<span class='text-danger error_message'>"+json['message']+"</span>");
			$("#errorDatat_both_"+idt).html(json['message']);
          }else{
            $(".main_div").removeClass("display_none");
			
			
			if(json['basic'].st_code == '<?php echo $st_code ?>' && json['basic'].ac_no == '<?php echo $ac_no ?>'){       
			
				if("<?php echo Session::get('locale') == 'hi' ?>"){			
					if(json['basic'].name_v1 != '' && json['basic'].name_v1 != null){
						$("#fullname_both_"+idt).val(json['basic'].name_v1);
					}
				} else {
					if(json['basic'].name != '' && json['basic'].name != null){
					  $("#fullname_both_"+idt).val(json['basic'].name);
					}
				}
				if(json['basic'].slno_inpart != '' && json['basic'].slno_inpart != null){
				  $("#s_both_"+idt).val(json['basic'].slno_inpart);
				}
				if(json['basic'].part_no != '' && json['basic'].part_no != null){
				  $("#p_both_"+idt).val(json['basic'].part_no);
				}
				
				$("#errorDatat_both_"+idt).html(''); 
				
			} else {
				$("#errorDatat_both_"+idt).html('<?php echo  __('step3.epic_state_missing') ?>'); 
			}

          }  
          $('.loading_spinner').remove();    
        },
        error: function(data) { 
		  console.log(data);	
          var errors = data.responseJSON;
		   console.log(errors);
        }
      });
	  
	}
	
	
	  
    
	  
	  
	  
		function showForm(id){
		 if(id==1){
			$("#part_1").show(); 
			$("#part_2").hide(); 
			$("#part_3").hide(); 
		 }	
		 if(id==2){
			$("#part_2").show(); 
			$("#part_1").hide(); 
			$("#part_3").hide(); 
		 }
		  if(id==3){
			$("#part_2").hide(); 
			$("#part_1").hide(); 
			$("#part_3").show(); 
		 }
		}	
		function checkVal(){
			var nonreq = $("#nonreq").val();
			var cepic =  $("#epic_no").val();	
		    var ar=[];
			for(i=1; i<=10; i++){
			  var fn = $("#fullname_"+i).val();
			  var s = $("#s_"+i).val();
			  var p = $("#p_"+i).val();
			  
			  var cp = $("#epic_no_"+i).val();
			  var aabcd='Yes';
			  if(cp.length<10){
				  alert("<?php echo e(__('step1.Epic_error')); ?> ");	
				  $("#epic_no_"+i).focus();	
				  aabcd='No';
				  return false;	
			  }
			
			  if($("#epic_no_"+i).val()!=''){
				    ar.push($("#epic_no_"+i).val());				    	
			  }	
				
			  if(cepic==cp){
				 alert("<?php echo  __('messages.canprop') ?>");
				 return false;
			  }	
			  
			  if(fn=='' || fn==0 || fn==undefined){ 
				alert("<?php echo  __('step3.praposal_error') ?>");
				return false;  
			  }
			  if(s=='' || s==0 || s==undefined){
				alert("<?php echo  __('step3.praposal_error') ?>");
				return false;  
			  }
			  
			  if(p=='' || p==0 || p==undefined){
				alert("<?php echo  __('step3.praposal_error') ?>");
				return false;  
			  }
			  
			  		
			
					  
			 
			 
			  /*if(fn!=undefined && fn!=''){				  
				  if(p=='' || p==0 || p==undefined){
					alert("<?php echo  __('step3.part_val_message') ?>"+fn);
					return false;
				  }
				  if(s=='' || s==0 || s==undefined){
					alert("<?php echo  __('step3.siriel_val_message') ?>" +fn);
					return false;
				  }				  
			  } */
			}
	
			var sorted_arr = ar.slice().sort();
			var results = [];
			for (var i = 0; i < sorted_arr.length - 1; i++) {
				if (sorted_arr[i + 1] === sorted_arr[i]) {
					results.push(sorted_arr[i]);
				}
			}
		
			if(results.length>0){
				alert("<?php echo  __('messages.notsame') ?>");
				return false;
			}
		}
		
		
		function checkVal_both(){
			var nonreq = $("#nonreq").val();
			var cepic =  $("#epic_no").val();	
			var cand1 =  $("#epic_no_p_both").val();	
			
			if(cand1.length<10){
				  alert("<?php echo e(__('step1.Epic_error')); ?> ");	
				  $("#epic_no_p_both").focus();	
				  return false;	
			  }
			
			
			
			if(cand1==''){
			 alert("<?php echo  __('messages.notempt') ?>");
			 return false;	
			}
			if(cepic==cand1){
			 alert("<?php echo  __('messages.canprop') ?>");
			 return false;	
			}
			
			
			
			
		    var ar=[];
			for(i=1; i<=10; i++){
			  var fn = $("#fullname_both_"+i).val();
			  var s = $("#s_both_"+i).val();
			  var p = $("#p_both_"+i).val();
			  var cp = $("#epic_no_both_"+i).val();
			  
			  if(cp.length<10){
				  alert("<?php echo e(__('step1.Epic_error')); ?> ");	
				  $("#epic_no_both_"+i).focus();	
				  return false;	
			  }
			 
			  if($("#epic_no_both_"+i).val()!=''){
				    ar.push($("#epic_no_both_"+i).val());				    	
			  }	
				
			  if(cepic==cp){
				 alert("<?php echo  __('messages.canprop') ?>");
				 return false;
			  }	
			  
			  	
			  if(cand1==cp){
				 alert("<?php echo  __('messages.notsame') ?>");
				 return false;
			  }
			  
			  if(fn=='' || fn==0 || fn==undefined){ 
				alert("<?php echo  __('step3.praposal_error') ?>");
				return false;  
			  }
			  if(s=='' || s==0 || s==undefined){
				alert("<?php echo  __('step3.praposal_error') ?>");
				return false;  
			  }
			  if(p=='' || p==0 || p==undefined){
				alert("<?php echo  __('step3.praposal_error') ?>");
				return false;  
			  }
			}
	
			var sorted_arr = ar.slice().sort();
			var results = [];
			for (var i = 0; i < sorted_arr.length - 1; i++) {
				if (sorted_arr[i + 1] === sorted_arr[i]) {
					results.push(sorted_arr[i]);
				}
			}
		
			if(results.length>0){ 
				alert("<?php echo  __('messages.notsame') ?>"); 
				return false;
			}
		}
		
		
		
    
        $(document).ready(function(){  

        	
          // if($('#breadcrumb').length){
          //   var breadcrumb = '';
          //   $.each(<?php echo json_encode($breadcrumbs); ?>,function(index, object){
          //     breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
          //   });
          //   $('#breadcrumb').html(breadcrumb);
          // }

          $('#apply_date').datepicker({
            dateFormat: 'dd-mm-yy'
          });

          $('.non_recognized_proposers_row').each(function(index,object){
            $('.recognized_date').datepicker({
              dateFormat: 'dd-mm-yy'
            });
          });

          $('.recognized_party').change(function(e){
            change_recognised();
          });

          change_recognised();
        });

        function change_recognised(){
          if($(".recognized_party:checked").val() == 'recognized'){
            $('.not-recognized').addClass('display_none');
            $('.recognized').removeClass('display_none');
          }else{
            $('.not-recognized').removeClass('display_none');
            $('.recognized').addClass('display_none');
          }
        }

        function read_url(input, part) {
          if (input.files && input.files[0]) {

            var reader = new FileReader();    
            reader.onload = function(e) {
              $('.'+part+' .avatar-preview').html("<img src='"+ e.target.result+"' width='100px' height='100px'>");
            }
            reader.readAsDataURL(input.files[0]);

          }
        }

      </script>
      <script type="text/javascript">
        $(document).on('click', '.browse', function(){

          var file = $(this).parent().parent().parent().find('.file');
          file.trigger('click');
        });
        $(document).on('change', '.file', function(){
          $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
        });
      </script>

      <script type="text/javascript">
        $(document).ready(function () {
          $('.file').on('click', function() { 
            $('#form-upload').remove();
            $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" value="" /></form>');
            $('#form-upload input[name=\'file\']').trigger('click');
            if (typeof timer != 'undefined') {
              clearInterval(timer);
            } 
            timer = setInterval(function() {
              if ($('#form-upload input[name=\'file\']').val() != '') { 
                clearInterval(timer);
                $.ajax({
                  url: "<?php echo $href_file_upload; ?>?_token=<?php echo csrf_token(); ?>",
                  type: 'POST',
                  dataType: 'json',
                  data: new FormData($('#form-upload')[0]),
                  cache: false,
                  contentType: false,
                  processData: false,
                  beforeSend: function() {
                    $('.file-frame').removeClass("file-frame-error");
                    $('.file i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
                    $('.file').prop('disabled', true);
                    $('.text-danger').remove();
                  },
                  complete: function() {
                    $('.file i').replaceWith('<i class="fa fa-upload"></i>');
                    $('.file').prop('disabled', false);
                  },
                  success: function(json) {
                    if(json['success'] == false) {
                      $('.file-frame').after("<span class='text-danger'>"+json['errors']+"</span>");
                      $('.file-frame').addClass("file-frame-error");
                    }
                    if (json['success'] == true) {
                      $('.file-frame').find('.image').val(json['path']);
                      $('.file-frame').find('img').attr("src","<?php echo url('/'); ?>/"+json['path']);
					  $(".profile_img").hide();
                    }
                  },
                  error: function(xhr, ajaxOptions, thrownError) { 
                  	alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    $('.file-frame').after("<span class='text-danger'><?php echo __('messages.file_type_error'); ?></span>");
                  }
                });
              }
            }, 500);
          });
        });
      </script>

 <script type="text/javascript">
$(function () {
$('#epic_no_p').keyup(function (e) {
if (this.value.match(/[^a-zA-Z0-9 ]/g)) {
                    this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '');
                }
});
});
/*
function myFunction(ids)
{
	$('#epic_no_'+ids).keydown(function (e) {
if (e.shiftKey || e.ctrlKey || e.altKey) {
e.preventDefault();
} else {
var key = e.keyCode;
if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
e.preventDefault();
}
}
});

//alert(ids)

}
*/

</script>
      <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/nomination/apply-nomination-step-3.blade.php ENDPATH**/ ?>