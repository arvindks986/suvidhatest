<?php $__env->startSection('title', 'Candidate Nomintion Details'); ?>
<?php $__env->startSection('bradcome', 'Update Candidate Profile'); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admintheme/css/nomination.css')); ?>" id="theme-stylesheet">

<?php
$getDetails = getacbyacno($ele_details->ST_CODE, $ele_details->CONST_NO);
$partyd = getallpartylist();
$symb = getsymbollist();
$url = URL::to("/");
$sys_id = $nomDetails->symbol_id;
if ($sys_id == '0' || $sys_id == '') $sys_id = 200;
//dd($persoanlDetails);
?>
<main role="main" class="inner cover mb-3">

	<form method="post" action="<?php echo e(url('ropc/newupdatenomination/'.$nomid1)); ?>" enctype="multipart/form-data" autocomplete='off'>
		<?php echo e(csrf_field()); ?>

		<section class="mt-5">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						 <?php if($errors->any()): ?>
				<div class="alert alert-danger" role="alert">
				<?php echo e(implode('', $errors->all(':message'))); ?>

				</div>
			<?php endif; ?>
						<div class="card">
							<div class="card-header d-flex align-items-center">
								<h4>Update Candidate</h4>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-4">
										<div class="avatar-upload">
											<label for="imageUpload">Candidate Image</label>
											<div class="avatar-edit">
												<input type='file' id="imageUpload" name="profileimg" accept=".jpg" />
												<label for="imageUpload"><img src="<?php echo e(asset('admintheme/img/icon/tab-icon-002.png')); ?>" /></label>
											</div>
											<?php if($persoanlDetails->cand_image != '' ): ?>
											<div class="avatar-preview">
												<div id="imagePreview">
													<img src="<?php echo e(url($persoanlDetails->cand_image)); ?>" height="180" width="180" />
												</div>
											</div>
											<?php else: ?>
											<div class="avatar-preview">
												<div id="imagePreview"></div>
												 <?php if(session('error_messageis')): ?> <span style="color:red"><?php echo e(session('error_messageis')); ?></span> <?php else: ?> <label  style="color:blue">Note: Allowed Format: .jpg</label> <?php endif; ?>
											</div>
											<?php endif; ?>
											<div class="profileerrormsg errormsg errorred"></div>
										</div>
										<?php /*<img class="rounded-circle" src="{{ asset('admintheme/img/vendor/avtar.jpg')}}" alt="" />*/ ?>
										<!-- <label  style="color:blue">Note: Allowed Format: .jpg </label> -->
									</div>
									<div class="col">
										<div class="form-group row mt-5">
											<label class="col-sm-4">Party Name <sup>*</sup></label>
											<div class="col-sm-8">
												<div class="" style="width:100%;">
													<select name="party_id" class="form-control party_id">
														<option value="">-- Select Party --</option>
														<?php $__currentLoopData = $partyd; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Party): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($Party->CCODE); ?>" <?php if($nomDetails->party_id == $Party->CCODE): ?> selected <?php endif; ?> > <?php echo e($Party->PARTYABBRE); ?>-<?php echo e($Party->PARTYNAME); ?> </option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
													<div class="perrormsg errormsg errorred"></div>
												</div>
											</div>
										</div>
										<?php   //dd($symb); 
										?>
										<div class="form-group row">
											<label class="col-sm-4">Symbol <sup>*</sup></label>
											<div class="col-sm-8">
												<div class="" style="width:100%;">
													<select name="symbol_id" class="form-control">
														<option value="">-- Select Symbol --</option>
														<?php foreach ($symb as $symbolDetails) {
															echo $symbolDetails->SYMBOL_NO;
														?>
															<option value="<?php echo e($symbolDetails->SYMBOL_NO); ?>" <?php if( $sys_id==$symbolDetails->SYMBOL_NO): ?> selected <?php endif; ?> <?php echo e($symbolDetails->SYMBOL_DES); ?>> <?php echo e($symbolDetails->SYMBOL_DES); ?></option>
														<?php } ?>
													</select>
													<div id="mysysDiv" style="display: none;"> <input type="checkbox" name="nosymb" id="nosymb" value="200" checked="checked"> Symbole Not Alloted</div>
													<div class="serrormsg errormsg errorred"></div>
												</div>
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
								<div class="row">

									<div class="col">
										<form class="form-horizontal">
											<div class="form-group row">
												<label class="col-sm-3">Name<sup>*</sup></label>
												<div class="col">
													<label>Name in English<sup>*</sup></label>
													<?php echo Form::text('name', $persoanlDetails->cand_name, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'In English','']); ?>

													<?php if($errors->has('name')): ?>
													<span style="color:red;"><?php echo e($errors->first('name')); ?></span>
													<?php endif; ?>
													<div class="nameerrormsg errormsg errorred"></div>
												</div>
												<div class="col">
													<label>Name in Hindi</label>
													<?php echo Form::text('hname', $persoanlDetails->cand_hname, ['class' => 'form-control', 'id' => 'hname', 'placeholder' => 'In Hindi','']); ?>

													<?php if($errors->has('hname')): ?>
													<span style="color:red;"><?php echo e($errors->first('hname')); ?></span>
													<?php endif; ?>
													<div class="nhindierrormsg errormsg errorred"></div>
												</div>
												<div class="col">
													<label>Name in Vernacular </label>
													<?php echo Form::text('cand_vname', $persoanlDetails->cand_vname, ['class' => 'form-control', 'id' => 'cand_vname', 'placeholder' => 'Name in Vernacular','']); ?>

													<?php if($errors->has('cand_vname')): ?>
													<span style="color:red;"><?php echo e($errors->first('cand_vname')); ?></span>
													<?php endif; ?>
													<div class="nhindierrormsg errormsg errorred"></div>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-3">Candidate Alias Name </label>
												<div class="col">
													<?php echo Form::text('aliasname', $persoanlDetails->cand_alias_name, ['class' => 'form-control', 'id' => 'aliasname', 'placeholder' => 'Alias Name English','']); ?>

													<?php if($errors->has('aliasname')): ?>
													<span style="color:red;"><?php echo e($errors->first('aliasname')); ?></span>
													<?php endif; ?>

												</div>
												<div class="col">
													<?php echo Form::text('aliashname', $persoanlDetails->cand_alias_hname, ['class' => 'form-control', 'id' => 'aliashname', 'placeholder' => 'Alias Name In Hindi','']); ?>

													<?php if($errors->has('aliashname')): ?>
													<span style="color:red;"><?php echo e($errors->first('aliashname')); ?></span>
													<?php endif; ?>

												</div>
											</div>

											<div class="form-group row">
												<label class="col-sm-3">Father's / Husband's Name <sup>*</sup></label>
												<div class="col">
													<?php echo Form::text('fname', $persoanlDetails->candidate_father_name, ['class' => 'form-control', 'id' => 'fname', 'placeholder' => 'In English','']); ?>

													<?php if($errors->has('fname')): ?>
													<span style="color:red;"><?php echo e($errors->first('fname')); ?></span>
													<?php endif; ?>
													<div class="ferrormsg errormsg errorred"></div>
												</div>
												<div class="col">
													<?php echo Form::text('fhname', $persoanlDetails->cand_fhname, ['class' => 'form-control', 'id' => 'fhname', 'placeholder' => 'In Hindi','']); ?>

													<?php if($errors->has('fhname')): ?>
													<span style="color:red;"><?php echo e($errors->first('fhname')); ?></span>
													<?php endif; ?>
													<div class="fhindierrormsg errormsg errorred"></div>
												</div>
											</div>
											<div class="line"></div>

											<div class="form-group row">
												<label class="col-sm-2">Email </label>
												<div class="col">
													<?php echo Form::text('email', $persoanlDetails->cand_email, ['class' => 'form-control', 'id' => 'email','']); ?>

													<?php if($errors->has('email')): ?>
													<span style="color:red;"><?php echo e($errors->first('email')); ?></span>
													<?php endif; ?>
													<div class="eerrormsg errormsg errorred"></div>
												</div>
												<label class="col-sm-2">Mobile No </label>
												<div class="col">
													<?php echo Form::text('cand_mobile', $persoanlDetails->cand_mobile, ['class' => 'form-control', 'id' => 'cand_mobile','','maxlength' => 10]); ?>

													<?php if($errors->has('cand_mobile')): ?>
													<span style="color:red;"><?php echo e($errors->first('cand_mobile')); ?></span>
													<?php endif; ?>
													<div class="merrormsg errormsg errorred"></div>
												</div>
											</div>


											<div class="form-group row">
												<label class="col-sm-2">Gender <sup>*</sup></label>

												<div class="col">
													<div class="custom-control custom-radio">
														<input type="radio" name="gender" class="custom-control-input" id="customControlValidation2" value="female" <?php if($persoanlDetails->cand_gender == 'female'): ?> checked <?php endif; ?> id="radio1">
														<label class="custom-control-label" for="customControlValidation2">Female</label>
													</div>
													<div class="custom-control custom-radio ">
														<input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male" id="radio2" <?php if($persoanlDetails->cand_gender == 'male'): ?> checked <?php endif; ?> id="radio2">
														<label class="custom-control-label" for="customControlValidation3">Male</label>

													</div>
													<div class="custom-control custom-radio mb-3">
														<input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third" <?php if($persoanlDetails->cand_gender == 'third'): ?> checked <?php endif; ?> id="radio3">
														<label class="custom-control-label" for="customControlValidation4">Others</label>
													</div>
													<?php if($errors->has('gender')): ?>
													<span style="color:red;"><?php echo e($errors->first('gender')); ?></span>
													<?php endif; ?>
													<div class="gerrormsg errormsg errorred"></div>
												</div>
												<label class="col-sm-2">PAN Number </label>
												<div class="col">
													<?php echo Form::text('panno', $persoanlDetails->cand_panno, ['class' => 'form-control', 'id' => 'panno','','maxlength' => 10]); ?>

													<?php if($errors->has('panno')): ?>
													<span style="color:red;"><?php echo e($errors->first('panno')); ?></span>
													<?php endif; ?>
													<div class="pannoerrormsg errormsg errorred"></div>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2">Age <sup>*</sup></label>
												<div class="col">
													<?php echo Form::text('age', $persoanlDetails->cand_age, ['class' => 'form-control', 'maxlength'=>'2', 'id' => 'age','']); ?>

													<?php if($errors->has('age')): ?>
													<span style="color:red;"><?php echo e($errors->first('age')); ?></span>
													<?php endif; ?>
													<div class="ageerrormsg errormsg errorred"></div>
												</div>
												<div class="col">&nbsp;</div>
											</div>
											<div class="line"></div>
											<?php

											$address = $persoanlDetails->candidate_residence_address;

											$resAddress = '';
											if (strpos($address, ',') !== false) {
												$resAddress = explode(",", $address);
											} else {
												$resAddress = '';
											}

											$addressHindi = $persoanlDetails->candidate_residence_addressh;
											//	echo $addressHindi ; exit;
											$resAddressHindi = '';
											if (strpos($addressHindi, ',') !== false) {
												$resAddressHindi = explode(",", $addressHindi);
											} else {
												$resAddressHindi == '';
											}
											?>
											<div class="form-group row">
												<label class="col-sm-2">Address Line1<sup>*</sup></label>
												<div class="col">
													<?php if($resAddress != '' ): ?>
													<?php echo Form::text('addressline1', $resAddress[0], ['class' => 'form-control', 'id' => 'addressline1','placeholder'=>'In English']); ?>

													<?php else: ?>
													<?php echo Form::text('addressline1', $address, ['class' => 'form-control', 'id' => 'addressline1','placeholder'=>'In English']); ?>


													<?php endif; ?>
													<?php if($errors->has('addressline1')): ?>
													<span style="color:red;"><?php echo e($errors->first('addressline1')); ?></span>
													<?php endif; ?>
													<div class="addressline1errormsg errormsg errorred"></div>
												</div>
												<div class="col">
													<?php if($resAddressHindi != '' ): ?>
													<?php echo Form::text('addresshline1', $resAddressHindi[0], ['class' => 'form-control', 'id' => 'addresshline1','placeholder'=>'In Hindi']); ?>

													<?php else: ?>
													<?php echo Form::text('addresshline1', $addressHindi, ['class' => 'form-control', 'id' => 'addresshline1','placeholder'=>'In Hindi']); ?>


													<?php endif; ?>
													<?php if($errors->has('addresshline1')): ?>
													<span style="color:red;"><?php echo e($errors->first('addresshline1')); ?></span>
													<?php endif; ?>
													<div class="addresshline1errormsg errormsg errorred"></div>
												</div>
											</div>

											<div class="line"></div>

											<div class="form-group row">
												<label class="col-sm-2">Address Line2<sup></sup></label>
												<div class="col">
													<?php if($resAddress != '' ): ?>
													<?php echo Form::text('addressline2', $resAddress[1], ['class' => 'form-control', 'id' => 'addressline2','placeholder'=>'In English']); ?>

													<?php else: ?>
													<?php echo Form::text('addressline2', null, ['class' => 'form-control', 'id' => 'addressline2','placeholder'=>'In English']); ?>

													<?php endif; ?>
													<div class="addressline2errormsg errormsg errorred"></div>
												</div>

												<div class="col">
													<?php if($resAddressHindi != '' ): ?>
													<?php echo Form::text('addresshline2', $resAddressHindi[1], ['class' => 'form-control', 'id' => 'addresshline2','placeholder'=>'In Hindi']); ?>

													<?php else: ?>
													<?php echo Form::text('addresshline2', null, ['class' => 'form-control', 'id' => 'addresshline2','placeholder'=>'In Hindi']); ?>

													<?php endif; ?>
													<div class="addresshline2errormsg errormsg errorred"></div>
												</div>
											</div>
											<div class="line"></div>

											<div class="form-group row">
												<div class="col-sm-2"><label for="statename">Candidate's State Name<sup>*</sup></label></div>
												<div class="col">
													<div class="" style="width:100%;">
														<select name="state" class="form-control">
															<option value="">-- Select States --</option>

															<?php $__currentLoopData = $all_state; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $State): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($State->ST_CODE); ?>" <?php if($persoanlDetails->candidate_residence_stcode == $State->ST_CODE ): ?> selected <?php endif; ?>> <?php echo e($State->ST_NAME); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php if($errors->has('state')): ?>
														<span style="color:red;"><?php echo e($errors->first('state')); ?></span>
														<?php endif; ?>
														<div class="stateerrormsg errormsg errorred"></div>
													</div>
												</div>
												<div class="col-sm-2"><label for="statename">Candidate's District Name <sup>*</sup></label></div>
												<div class="col">
													<div class="" style="width:100%;">
														<select name="district" class="form-control">
															<option value="">-- Select Ditricts --</option>
															<?php $__currentLoopData = $all_dist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($district->DIST_NO); ?>" <?php if($persoanlDetails->candidate_residence_stcode == $State->ST_CODE ): ?> selected <?php endif; ?>>
																<?php echo e($district->DIST_NO); ?> - <?php echo e($district->DIST_NAME); ?> - <?php echo e($district->DIST_NAME_HI); ?>

															</option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php if($errors->has('district')): ?>
														<span style="color:red;"><?php echo e($errors->first('district')); ?></span>
														<?php endif; ?>
														<div class="districterrormsg errormsg errorred"></div>
													</div>
												</div>
											</div>
											<div class="form-group row">
												
												<div class="col-sm-2"><label for="statename">Candidate's AC Name <sup>*</sup></label></div>
												<div class="col">
													<div class="" style="width:100%;">
														<select name="ac" class="consttype form-control">
															<option value="">-- Select AC --</option>
															<?php $__currentLoopData = $all_ac; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $getAc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($getAc->AC_NO); ?>">
																<?php echo e($getAc->AC_NO); ?> - <?php echo e($getAc->AC_NO); ?> - <?php echo e($getAc->AC_NAME_HI); ?>

															</option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php if($errors->has('ac')): ?>
														<span style="color:red;"><?php echo e($errors->first('ac')); ?></span>
														<?php endif; ?>
														<div class="consterrormsg errormsg errorred"></div>
													</div>
												</div>
												
												
												<div class="col-sm-2"><label for="statename">Category <sup>*</sup></label></div>
												<div class="col">
													<div class="" style="width:100%;">
														<select name="cand_category" class="form-control">
															<option value="">--Select Category--</option>
															<option value="general" <?php if($persoanlDetails->cand_category == 'general' ): ?> selected <?php endif; ?> >General</option>
															<option value="sc" <?php if($persoanlDetails->cand_category == 'sc' ): ?> selected <?php endif; ?> >SC</option>
															<option value="st" <?php if($persoanlDetails->cand_category == 'st' ): ?> selected <?php endif; ?> >ST</option>
															<option value="obc" <?php if($persoanlDetails->cand_category == 'obc' ): ?> selected <?php endif; ?> >OBC</option>
														</select>
														<?php if($errors->has('cand_category')): ?>
														<span style="color:red;"><?php echo e($errors->first('cand_category')); ?></span>
														<?php endif; ?>
														<div class="caterrormsg errormsg errorred"></div>
													</div>
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-3"><label for="statename">Candidate have Shown Criminal antecedents <sup>*</sup></label></div>
												<div class="col">
													<div class="custom-control custom-radio">
														<input type="radio" name="is_criminal" class="custom-control-input" id="customControl1" value="1" <?php if($persoanlDetails->is_criminal == '1'): ?> checked <?php endif; ?>>
														<label class="custom-control-label" for="customControl1">Yes</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" name="is_criminal" class="custom-control-input" id="customControl2" value="0" <?php if($persoanlDetails->is_criminal == '0'): ?> checked <?php endif; ?> >
														<label class="custom-control-label" for="customControl2">No</label>
													</div>
													<div class="cerrormsg errormsg errorred" style="font-size:12px;"></div>
												</div>
												<?php
												$display='none';
												if(old('is_criminal')=='1' || !empty(session('error_mes')) || $persoanlDetails->is_criminal == '1'){
												$display='block';
												}
												if($persoanlDetails->is_criminal == '0'){
												$display='none';
												}
												?>
												<div class="caa" style="display:<?php echo e($display); ?>;">
													<div class="col">
														<label for="affidavit" class="col-form-label">Candidate Criminal Antecedents File <span class="errorred">*</span> (Maximum size 3 MB - Only PDF)</label>
														<div class="file-upload">
															<div class="file-select">
																<div class="file-select-name" id="noFile">Document not selected</div>
																<input type="file" name="affidavit" id="affidavit" class="custom-file-input affidavit form-control mr-auto" accept=".pdf">
																<div class="file-select-button customchoose" id="fileName">Choose File</div>
															</div>
														</div>
														<?php if($errors->has('affidavit')): ?>
														<span style="color:red;"><?php echo e($errors->first('affidavit')); ?></span>
														<?php endif; ?>
														<span id="fileerrormsg" class="fileerrormsg errormsg errorred" style="font-size:12px;"><?php if(session('error_mes')): ?> <?php echo e(session('error_mes')); ?> <?php endif; ?></span>
													</div>
												</div>
											</div>
											<div class="form-group row float-right">
												<div class="col">
													<button type="button" id="Cancel" class="btn btn-primary" onclick="location.href ='<?php echo e($url); ?>/ropc/listnomination';">Cancel</button>
													<button type="submit" id="candnomination" class="btn btn-primary">Submit</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</form>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

<script>
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				jQuery('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
				jQuery('#imagePreview').hide();
				jQuery('#imagePreview').fadeIn(650);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	jQuery("#imageUpload").change(function() {
		readURL(this);
	});
	jQuery(document).ready(function() {
		var d = new Date();
		var year = d.getFullYear() - 25;
		jQuery('#dob').datetimepicker({

			format: 'DD-MM-YYYY',
			useCurrent: false,
			maxDate: new Date()

		});

		jQuery("input[name='is_criminal']").click(function() {
			var caaValue = jQuery("input[name='is_criminal']:checked").val();
			jQuery(".cerrormsg").text(" ");
			jQuery(".fileerrormsg").text(" ");
			if (caaValue == '1') {
				jQuery(".caa").show();
			} else {
				jQuery("#noFile").text('Document not selected');
				jQuery(".file-upload").removeClass("active");
				document.getElementById("affidavit").value = null;
				jQuery(".caa").hide();
			}
		});


		if (jQuery('select[name="state"]').val() != '') {
			var stcode = jQuery('select[name="state"]').val();
			var selconst = '';
			var ac = '<?php echo $persoanlDetails->candidate_residence_acno ?>';
			var pc = '<?php echo $persoanlDetails->candidate_residence_pcno ?>';
			if (ac != '') {
				selconst = '<?php echo $persoanlDetails->candidate_residence_acno ?>';
			} else if (pc != '') {
				selconst = '<?php echo $persoanlDetails->candidate_residence_pcno ?>';
			}
			jQuery.ajax({
				url: "<?php echo e(url('/ropc/getDistricts')); ?>",
				type: 'GET',
				data: {
					stcode: stcode
				},
				success: function(result) {
					var districtselect = jQuery('form select[name=district]');
					var seldistrict = '<?php echo $persoanlDetails->candidate_residence_districtno ?>';

					districtselect.empty();
					var districthtml = '';
					if (result != '') {
						districthtml = districthtml + '<option value="">-- Select District --</option> ';
						var selectedcons = '<?php echo $persoanlDetails->candidate_residence_acno ?>';
						var distval = '';
						jQuery.each(result, function(key, value) {
							if (seldistrict == value.DIST_NO) {
								distval = value.DIST_NO;
								districthtml = districthtml + '<option value="' + value.DIST_NO + '" selected="selected">' + value.DIST_NO + ' - ' + value.DIST_NAME + ' - ' + value.DIST_NAME_HI + '</option>';
							} else {
								districthtml = districthtml + '<option value="' + value.DIST_NO + '">' + value.DIST_NO + ' - ' + value.DIST_NAME + ' - ' + value.DIST_NAME_HI + '</option>';
							}

							jQuery("select[name='district']").html(districthtml);
						});
						var districthtml_end = '';
						if (jQuery('select[name="district"]').val() != '') {
							var stcode = jQuery('select[name="state"]').val();
							var district = jQuery('select[name="district"]').val();
							jQuery.ajax({
								url: '<?php echo url('/') ?>/ropc/getallac',
								type: 'GET',
								data: {
									district: district,
									stcode: stcode
								},
								success: function(result) {
									var distselect = jQuery('form select[name=ac]');
									distselect.empty();
									var achtml = '';
									//alert(selectedcons);
									achtml = achtml + '<option value="">-- Select AC --</option> ';
									jQuery.each(result, function(key, value) {
										//alert(value.AC_NO);
										if (selconst == value.AC_NO) {
											//alert('test');
											achtml = achtml + '<option value="' + value.AC_NO + '" selected="selected">' + value.AC_NO + ' - ' + value.AC_NAME + ' - ' + value.AC_NAME_HI + '</option>';
										} else {
											achtml = achtml + '<option value="' + value.AC_NO + '">' + value.AC_NO + ' - ' + value.AC_NAME + ' - ' + value.AC_NAME_HI + '</option>';
										}
										jQuery("select[name='ac']").html(achtml);
									});
									var achtml_end = '';
									jQuery("select[name='ac']").append(achtml_end)
								}
							});
						}
					} else {
						districthtml = districthtml + '<option value="0">No Symbol Found</option>';
					}

					jQuery("select[name='district']").html(districthtml);

					var districthtml_end = '';
					jQuery("select[name='district']").append(districthtml_end)
				}
			});
		}

		jQuery('select[name="party_id"]').change(function() {
			var partyid = jQuery(this).val();
			$('#mysysDiv').hide();
			jQuery.ajax({
				url: "<?php echo e(url('/ropc/getSymbol')); ?>",
				type: 'GET',
				data: {
					partyid: partyid
				},
				success: function(result) {
					jQuery("select[name='symbol_id']").html(result);
				},
				error: function(data, textStatus, errorThrown) {
					var symbolselect = jQuery('form select[name=symbol_id]');
					symbolselect.empty();
					var symbolhtml = '';
					symbolhtml = symbolhtml + '<option value="200">200 - Not Alloted</option>';
					jQuery("select[name='symbol_id']").html(symbolhtml);
					var symbolhtml_end = '';
					jQuery("select[name='symbol_id']").append(symbolhtml_end);
				}
			});
		});
		jQuery("select[name='state']").change(function() {
			var stcode = jQuery(this).val();
			jQuery.ajax({
				url: "<?php echo e(url('/ropc/getDistricts')); ?>",
				type: 'GET',
				data: {
					stcode: stcode
				},
				success: function(result) {
					var distselect = jQuery('form select[name=district]');
					distselect.empty();
					var districthtml = '';
					districthtml = districthtml + '<option value="">-- Select District --</option> ';
					jQuery.each(result, function(key, value) {
						districthtml = districthtml + '<option value="' + value.DIST_NO + '">' + value.DIST_NO + ' - ' + value.DIST_NAME + ' - ' + value.DIST_NAME_HI + '</option>';
						jQuery("select[name='district']").html(districthtml);
					});
					var districthtml_end = '';
					jQuery("select[name='district']").append(districthtml_end)
				}
			});
		});
		jQuery("select[name='district']").change(function() {
			var district = jQuery(this).val();
			var stcode = jQuery('select[name="state"]').val();
			jQuery.ajax({
				url: "<?php echo e(url('/ropc/getallac')); ?>",
				type: 'GET',
				data: {
					district: district,
					stcode: stcode
				},
				success: function(result) {
					var distselect = jQuery('form select[name=ac]');
					distselect.empty();
					var achtml = '';
					achtml = achtml + '<option value="">-- Select AC --</option> ';
					jQuery.each(result, function(key, value) {
						achtml = achtml + '<option value="' + value.AC_NO + '">' + value.AC_NO + ' - ' + value.AC_NAME + ' - ' + value.AC_NAME_HI + '</option>';
						jQuery("select[name='ac']").html(achtml);
					});
					var achtml_end = '';
					jQuery("select[name='ac']").append(achtml_end)
				}
			});
		});
		// Validation
		jQuery('#candnomination').click(function() {
			var partyid = jQuery('select[name="party_id"]').val();
			var symbolid = jQuery('select[name="symbol_id"]').val();
			var name = jQuery('input[name="name"]').val();
			var hindiname = jQuery('input[name="hname"]').val();
			var fname = jQuery('input[name="fname"]').val();
			var fhname = jQuery('input[name="fhname"]').val();
			var email = jQuery('input[name="email"]').val();
			var cand_mobile = jQuery('input[name="cand_mobile"]').val();
			var dob = jQuery('input[name="dob"]').val();
			var age = jQuery('input[name="age"]').val();
			var addressline1 = jQuery('input[name="addressline1"]').val();
			var addresshline1 = jQuery('input[name="addresshline1"]').val();
			var state = jQuery('select[name="state"]').val();
			var distt = jQuery('select[name="district"]').val();
			var consttype = jQuery('.consttype').val();
			var candcategory = jQuery('select[name="cand_category"]').val();
			var candimage = '<?php echo $persoanlDetails->cand_image ?>';

			if (partyid == '') {
				jQuery('.errormsg').html('');
				jQuery('.perrormsg').html('Please select party');
				jQuery("input[name='party_id']").focus();
				return false;
			}
			if (symbolid == '') {
				jQuery('.errormsg').html('');
				jQuery('.serrormsg').html('Please select symbol');
				jQuery("input[name='symbol_id']").focus();
				return false;
			}
			if (name == '') {
				jQuery('.errormsg').html('');
				jQuery('.nameerrormsg').html('Please enter name in english');
				jQuery("input[name='name']").focus();
				return false;
			}
			// if (hindiname == '') {
			// 	jQuery('.errormsg').html('');
			// 	jQuery('.nhindierrormsg').html('Please enter name in hindi');
			// 	jQuery("input[name='hname']").focus();
			// 	return false;
			// }
			if (cand_vname == '') {
				jQuery('.errormsg').html('');
				jQuery('.vererrormsg').html('Please enter name in vernacular');
				jQuery("input[name='cand_vname']").focus();
				return false;
			}
			if (fname == '') {
				jQuery('.errormsg').html('');
				jQuery('.ferrormsg').html('Please enter father/husband name in english');
				jQuery("input[name='fname']").focus();
				return false;
			}
			// if(fhname == ''){
			// 	jQuery('.errormsg').html('');
			// 	jQuery('.fhindierrormsg').html('Please enter father/husband name in hindi');
			// 	jQuery( "input[name='fhname']" ).focus();
			// 	return false;
			// }

			if (jQuery('input[type=radio][name=gender]:checked').length == 0) {
				jQuery('.errormsg').html('');
				jQuery('.gerrormsg').html('Please select gender');
				//jQuery('input[type=radio][name=gender]:checked').focus();
				return false;
			}
			if (age == '') {
				jQuery('.ageerrormsg').html('');
				jQuery('.ageerrormsg').html('please enter candidate age');
				jQuery("input[name='age']").focus();
				return false;
			}

			if (addressline1 == '') {
				jQuery('.errormsg').html('');
				jQuery('.addressline1errormsg').html('Please enter address line1 in english');
				jQuery("input[name='addressline1']").focus();
				return false;
			}

			if (state == '') {
				jQuery('.errormsg').html('');
				jQuery('.stateerrormsg').html('Please select state');
				jQuery("input[name='state']").focus();
				return false;
			}
			if (distt == '') {
				jQuery('.errormsg').html('');
				jQuery('.districterrormsg').html('Please select district');
				jQuery("input[name='district']").focus();
				return false;
			}
			if (consttype == '') {
				jQuery('.errormsg').html('');
				jQuery('.consterrormsg').html('Please select const type');
				jQuery("input[name='district']").focus();
				return false;
			}
			if (candcategory == '') {
				jQuery('.errormsg').html('');
				jQuery('.caterrormsg').html('Please select candidate category');
				jQuery("select[name='cand_category']").focus();
				return false;
			}

			if ($('input[type=radio][name=is_criminal]:checked').length == 0) {
				jQuery('.errormsg').html('');
				jQuery('.cerrormsg').html('Please select criminal antecedents.');
				jQuery('input[type=radio][name=is_criminal]:checked').focus();
				return false;
			} else {

				if (document.getElementById("affidavit").files.length == 0) {
					if (jQuery('input[name="is_criminal"]:checked').val() == '0') {
						return true;
					} else {
						if (jQuery('input[name="is_criminal"]:checked').val() != '1') {
							jQuery('.errormsg').html('');
							jQuery('.fileerrormsg').html('Please select criminal antecedents.');
							jQuery('input[type=radio][name=is_criminal]:checked').focus();
							return false;
						}
					}

				} else {
					var file_size = $('#affidavit')[0].files[0].size;
					if (file_size > 3145728) {
						$(".fileerrormsg").html("File size is greater than 3 MB");
						return false;
					} else {
						$(".fileerrormsg").html(" ");
					}

				}
			}

		});
		jQuery("#cand_mobile").keypress(function(e) {
			//if the letter is not digit then display error and don't type anything
			var length = jQuery(this).val().length;
			if (length > 9) {
				return false;
			} else if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				jQuery('.errormsg').html('');
				jQuery('.merrormsg').html('Digits Only').show().fadeOut("slow");
				jQuery("input[name='cand_mobile']").focus();
				return false;
			} else if ((length == 0) && (e.which == 48)) {
				return false;
			}
		});
	});

	function IsEmail(email) {
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!regex.test(email)) {
			return false;
		} else {
			return true;
		}
	}

$('.affidavit').bind('change', function () {
  var filename = $(".affidavit").val();
  if (/^\s*$/.test(filename)) {
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen..."); 
  }
  else {
            $(".file-upload").addClass('active');
            $("#noFile").text(filename.replace("C:\\fakepath\\", "")); 
      }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.pc.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/pc/ro/updatenomination.blade.php ENDPATH**/ ?>