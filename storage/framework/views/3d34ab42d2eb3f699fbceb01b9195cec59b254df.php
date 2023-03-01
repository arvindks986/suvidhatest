<?php $__env->startSection('title', 'Candidate Nomintion Details'); ?>
<?php $__env->startSection('bradcome', 'Candidate Nomintion'); ?>
<?php $__env->startSection('content'); ?>

<?php $st = app(App\commonModel::class)->getstatebystatecode($stcode);
$pc = app(App\commonModel::class)->getpcbypcno($stcode, $constno);
?>
<link rel="stylesheet" href="<?php echo e(asset('admintheme/css/nomination.css')); ?>" id="theme-stylesheet">


<main role="main" class="inner cover mb-3">
	<section>

		<form enctype="multipart/form-data" id="election_form" method="POST" action="<?php echo e(url('ropc/createnomination')); ?>" autocomplete='off' enctype="x-www-urlencoded">
			<?php echo e(csrf_field()); ?>


			<div class="container">
				<div class="row">

					<div class="card text-left mt-3" style="width:100%; margin:0 auto 10px auto;">
						<div class=" card-header">
							<div class=" row">
								<div class="col">
									<h4>Candidate Nomintion Details</h4>
								</div>
								<div class="col">
									<p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info"><?php echo e($st->ST_NAME); ?></span> &nbsp;&nbsp; <b class="bolt">PC Name:</b>
										<span class="badge badge-info"><?php echo e($pc->PC_NAME); ?></span>&nbsp;&nbsp;
									</p>
								</div>

							</div>
						</div>
						<div class="alert alert-success">In case of multiple nominations. Enter the first nomination here and then enter multiple nominations go to multiple nominations tab.</div>
						<div class="row">

							<?php if(session('success_mes')): ?>
							<div class="alert alert-success"> <?php echo e(session('success_mes')); ?></div>
							<?php endif; ?>

						</div>
						<div class="card-body">
							<div class="row d-flex  align-items-center">
								<div class="col-md-3 col-lg-3">
									<?php if($errors->has('profileimg')): ?>
										<span style="color:red;"><?php echo e($errors->first('profileimg')); ?></span>
										<?php endif; ?>
									<div class="avatar-upload">
										<label for="imageUpload">Candidate Image</label>
										<div class="avatar-edit">
											<input type='file' id="imageUpload" name="profileimg" accept=".jpg" />
											<label for="imageUpload"> <img src="<?php echo e(asset('admintheme/img/icon/tab-icon-002.png')); ?>" /></label>
										</div>
										
										<div class="avatar-preview">
											<div id="imagePreview"></div>

											 <?php if(session('error_messageis')): ?> <span style="color:red"><?php echo e(session('error_messageis')); ?></span> <?php else: ?> <label  style="color:blue">Note: Allowed Format: .jpg</label> <?php endif; ?>
										</div>
										<div class="profileerrormsg errormsg errorred"></div>
									</div>
								</div>

								<div class="col">

									<div class="form-group row">

										<div class="col">
											<label class="">Party Name <sup>*</sup></label>

											<?php
											$partyd = getallpartylist();
											$symb = getsymbollist();
											$symb1 = getsymboltypelist('T');
											$newst = old('state');
											$newdist = old('district');
											$newac = old('ac');
											if ($newst != '' and $newdist != '') {
												$all_dist = getalldistrictbystate($newst);
												$all_ac = getacbystate($newst);
											}

											?>

											<select name="party_id" class="form-control party_id">
												<option value="">-- Select Party --</option>

												<?php $__currentLoopData = $partyd; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Party): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($Party->CCODE); ?>" <?php if($Party->CCODE==old('party_id')): ?> selected="selected" <?php endif; ?> > <?php echo e($Party->PARTYABBRE); ?>-<?php echo e($Party->PARTYNAME); ?> </option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

											</select>
											<?php if($errors->has('party')): ?>
											<span style="color:red;"><?php echo e($errors->first('party')); ?></span>
											<?php endif; ?>
											<div class="perrormsg errormsg errorred"></div>

										</div>

										<div class="col">
											<label class="">Symbol <sup>*</sup></label>
											<select name="symbol_id" class="form-control">
												<option value="">-- Select Symbol --</option>
												<?php $__currentLoopData = $symb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $symbolDetails): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($symbolDetails->SYMBOL_NO); ?>" <?php if($symbolDetails->SYMBOL_NO==old('symbol_id')): ?> selected="selected" <?php endif; ?>> <?php echo e($symbolDetails->SYMBOL_NO); ?>-<?php echo e($symbolDetails->SYMBOL_DES); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											<?php if($errors->has('symbol_id')): ?>
											<span style="color:red;"><?php echo e($errors->first('symbol_id')); ?></span>
											<?php endif; ?>
											<div class="serrormsg errormsg errorred"></div>
											<div id="mysysDiv" style="display: none;"> <input type="checkbox" name="nosymb" id="nosymb" value="200" checked="checked"> Symbole Not Alloted</div>
										</div>





									</div><!-- end COL-->
								</div><!-- end row-->
							</div>
						</div>
					</div>
				</div>
			</div>
	</section>
	<section>
		<div class="container p-0">
			<div class="row">

				<div class="col-md-12">
					<div class="card">
						<div class="card-header d-flex align-items-center">
							<h4>Candidate Personal Details</h4>
						</div>
						<div class="card-body">
							<div class="row">

								<div class="col">


									<div class="form-group row">
										<label class="col-sm-3">Name<sup>*</sup></label>
										<div class="col">
											<label>Name in English<sup>*</sup></label>
											<?php echo Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Name in English','']); ?>

											<?php if($errors->has('name')): ?>
											<span style="color:red;"><?php echo e($errors->first('name')); ?></span>
											<?php endif; ?>
											<div class="nameerrormsg errormsg errorred"></div>
										</div>
										<div class="col">
											<label>Name in Hindi</label>
											<?php echo Form::text('hname', null, ['class' => 'form-control', 'id' => 'hname', 'placeholder' => 'Name in Hindi','']); ?>

											<?php if($errors->has('hname')): ?>
											<span style="color:red;"><?php echo e($errors->first('hname')); ?></span>
											<?php endif; ?>
											<div class="nhindierrormsg errormsg errorred"></div>
										</div>
										<div class="col">
											<label>Name in Vernacular </label>
											<?php echo Form::text('cand_vname', null, ['class' => 'form-control', 'id' => 'cand_vname', 'placeholder' => 'Name in Vernacular','']); ?>

											<?php if($errors->has('cand_vname')): ?>
											<span style="color:red;"><?php echo e($errors->first('cand_vname')); ?></span>
											<?php endif; ?>
											<div class="vererrormsg errormsg errorred"></div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-3">Candidate Alias Name </label>
										<div class="col">
											<?php echo Form::text('aliasname', null, ['class' => 'form-control', 'id' => 'aliasname', 'placeholder' => 'Alias Name English','']); ?>

											<?php if($errors->has('aliasname')): ?>
											<span style="color:red;"><?php echo e($errors->first('aliasname')); ?></span>
											<?php endif; ?>

										</div>
										<div class="col">
											<?php echo Form::text('aliashname', null, ['class' => 'form-control', 'id' => 'aliashname', 'placeholder' => 'Alias Name In Hindi','']); ?>

											<?php if($errors->has('aliashname')): ?>
											<span style="color:red;"><?php echo e($errors->first('aliashname')); ?></span>
											<?php endif; ?>

										</div>
									</div>

									<div class="form-group row">
										<label class="col-sm-3">Father's / Husband's Name <sup>*</sup></label>
										<div class="col">
											<?php echo Form::text('fname', null, ['class' => 'form-control', 'id' => 'fname', 'placeholder' => 'In English','']); ?>

											<?php if($errors->has('fname')): ?>
											<span style="color:red;"><?php echo e($errors->first('fname')); ?></span>
											<?php endif; ?>
											<div class="ferrormsg errormsg errorred"></div>
										</div>
										<div class="col">
											<?php echo Form::text('fhname', null, ['class' => 'form-control', 'id' => 'fhname', 'placeholder' => 'In Hindi','']); ?>

											<?php if($errors->has('fhname')): ?>
											<span style="color:red;"><?php echo e($errors->first('fhname')); ?></span>
											<?php endif; ?>
											<div class="fhindierrormsg errormsg errorred"></div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-3">Category <sup>*</sup></label>
										<div class="col">
											<select name="cand_category" class="form-control">
												<option value="">--Select Category--</option>
												<option value="general" <?php if("general"==old('cand_category')): ?> selected="selected" <?php endif; ?>>General</option>
												<option value="sc" <?php if("sc"==old('cand_category')): ?> selected="selected" <?php endif; ?>>SC</option>
												<option value="st" <?php if("st"==old('cand_category')): ?> selected="selected" <?php endif; ?>>ST</option>
												<!-- <option value="obc" <?php if("obc"==old('cand_category')): ?> selected="selected" <?php endif; ?>>OBC</option> -->
											</select>
											<?php if($errors->has('cand_category')): ?>
											<span style="color:red;"><?php echo e($errors->first('cand_category')); ?></span>
											<?php endif; ?>
											<div class="caterrormsg errormsg errorred"></div>

										</div>
										<div class="col">

										</div>
									</div>
									<div class="line"></div>

									<div class="form-group row">
										<label class="col-sm-2">Email </label>
										<div class="col">
											<?php echo Form::text('email', null, ['class' => 'form-control', 'id' => 'email','']); ?>

											<?php if($errors->has('email')): ?>
											<span style="color:red;"><?php echo e($errors->first('email')); ?></span>
											<?php endif; ?>
											<div class="eerrormsg errormsg errorred"></div>
										</div>
										<label class="col-sm-2">Mobile No </label>
										<div class="col">
											<?php echo Form::text('cand_mobile', null, ['class' => 'form-control', 'id' => 'cand_mobile','','maxlength' => 10]); ?>

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
												<input type="radio" name="gender" class="custom-control-input" id="customControlValidation2" value="female" <?php if("female"==old('gender')): ?> checked="checked" <?php endif; ?>>
												<label class="custom-control-label" for="customControlValidation2">Female</label>
											</div>
											<div class="custom-control custom-radio ">
												<input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male" id="radio2" <?php if("male"==old('gender')): ?> checked="checked" <?php endif; ?>>
												<label class="custom-control-label" for="customControlValidation3">Male</label>

											</div>
											<div class="custom-control custom-radio mb-3">
												<input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third" <?php if("third"==old('gender')): ?> checked="checked" <?php endif; ?>>
												<label class="custom-control-label" for="customControlValidation4">Others</label>
											</div>
											<div class="gerrormsg errormsg errorred"></div>
										</div>
										<label class="col-sm-2">PAN Number </label>
										<div class="col">
											<?php echo Form::text('panno', null, ['class' => 'form-control', 'id' => 'panno','maxlength' => 10]); ?>

											<?php if($errors->has('panno')): ?>
											<span style="color:red;"><?php echo e($errors->first('panno')); ?></span>
											<?php endif; ?>
											<div class="pannoerrormsg errormsg errorred"></div>
										</div>
									</div>
									<div class="form-group row">
										<!--<label class="col-sm-2">Date of Birth  </label>
							<div class="col">
								 <input name="dob" type="text" class="form-control" placeholder="" id='dob' value="<?php echo e(old('dob')); ?>">
                                <span class="text-danger"><?php echo e($errors->error->first('dob')); ?></span>
								<div class="doberrormsg errormsg errorred"></div>
							</div> -->
										<label class="col-sm-2">Age <sup>*</sup></label>
										<div class="col">
											<?php echo Form::text('age', null, ['class' => 'form-control', 'maxlength'=>'2', 'id' => 'age','']); ?>

											<div class="ageerrormsg errormsg errorred"></div>
										</div>
										<div class="col">
											&nbsp;
										</div>
									</div>
									<div class="line"></div>

									<div class="form-group row">
										<label class="col-sm-2">Address Line1<sup>*</sup></label>
										<div class="col">
											 <label>Full Address in English  print as form 7A <sup>*</sup></label>
											<?php echo Form::text('addressline1', null, ['class' => 'form-control', 'id' => 'addressline1','placeholder'=>'In English']); ?>

											<?php if($errors->has('addressline1')): ?>
											<span style="color:red;"><?php echo e($errors->first('addressline1')); ?></span>
											<?php endif; ?>
											<div class="addressline1errormsg errormsg errorred"></div>
										</div>
										<div class="col">
											<label>Full Address in Hindi  print as form 7A  </label>
											<?php echo Form::text('addresshline1', null, ['class' => 'form-control', 'id' => 'addresshline1','placeholder'=>'In Hindi']); ?>

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
											<?php echo Form::text('addressline2', null, ['class' => 'form-control', 'id' => 'addressline2','placeholder'=>'In English']); ?>

											<div class="addressline2errormsg errormsg errorred"></div>
										</div>
										<div class="col">
											<?php echo Form::text('addresshline2', null, ['class' => 'form-control', 'id' => 'addresshline2','placeholder'=>'In Hindi']); ?>

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
													<?php if(isset($all_state)): ?> <?php $__currentLoopData = $all_state; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($st->ST_CODE); ?>" <?php if($st->ST_CODE==old('state')): ?> selected="selected" <?php endif; ?> > <?php echo e($st->ST_NAME); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
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
													<option value="<?php echo e($district->DIST_NO); ?>" <?php if($district->DIST_NO==old('district')): ?> selected="selected" <?php endif; ?> >
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
										<div class="col-sm-2"><label for="statename">Candidate's AC Name  <sup>*</sup></label></div>
										<div class="col">
											<div class="" style="width:100%;">
												<select name="ac" class="consttype form-control">
													<option value="">-- Select AC --</option>
													<?php $__currentLoopData = $all_ac; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $getAc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($getAc->AC_NO); ?>" <?php if($getAc->AC_NO==old('ac')): ?> selected="selected" <?php endif; ?>>
														<?php echo e($getAc->AC_NO); ?> - <?php echo e($getAc->AC_NAME); ?> - <?php echo e($getAc->AC_NAME_V1); ?>

													</option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
												<?php if($errors->has('ac')): ?>
												<span style="color:red;"><?php echo e($errors->first('pc')); ?></span>
												<?php endif; ?>
												<div class="consterrormsg errormsg errorred"></div>
											</div>
										</div>
									</div>
									<!-- When candidate CA status is Yes then this option will enabled to upload affidavit Start 
									Level - Required
									Size - 3MB
									Type - PDF only
									-->
									<div class="form-group row">
										<div class="col-sm-3"><label for="statename">Candidate have Shown Criminal antecedents <sup>*</sup></label></div> 
										<div class="col"> 
											<div class="custom-control custom-radio">
												<input type="radio" name="is_criminal" class="custom-control-input" id="customControl1" value="1" 
												<?php if("1"==old('is_criminal')): ?> checked="checked" <?php endif; ?>>
												<label class="custom-control-label" for="customControl1">Yes</label>
											</div>
											<div class="custom-control custom-radio">
													<input type="radio" name="is_criminal" class="custom-control-input" id="customControl2" value="0" 
													<?php if("0"==old('is_criminal')): ?> checked="checked" <?php endif; ?>>
													<label class="custom-control-label" for="customControl2">No</label>
											</div>
											<div class="cerrormsg errormsg errorred" style="font-size:12px;"></div>
										</div>
										<?php
											$display='none';
											if(old('is_criminal')=='1' || !empty(session('error_mes'))){
												$display='block';
											}
											
										?>
										<div class="caa" style="display:<?php echo e($display); ?>;">
											<div class="col">
											<label for="affidavit" class="col-form-label">Candidate Criminal Antecedents File <span class="errorred">*</span> (Maximum size 3 MB - Only PDF)</label>
											<div class="file-upload">
												<div class="file-select">
												<div class="file-select-name" id="noFile">Document not selected</div> 
												<input type="file" name="affidavit" id="affidavit" class="custom-file-input affidavit form-control mr-auto" accept=".pdf" >
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
											<button type="submit" id="candnomination" class="btn btn-primary">Submit</button>
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
	</form>


            <div class="modal fade" id="candModal" role="dialog">
    <div class="modal-dialog">
 
     <!-- Modal content-->
     <div class="modal-content">
      <div class="modal-header" style="background-color:thistle">
        <h6 class="modal-title"><span > ⓘ Info</span></h6>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
 
      </div>
      <div class="modal-footer">
       <button type="button" class="btn btn-default" data-dismiss="modal" style="background-color:thistle">Continue</button>
      </div>
     </div>
    </div>
   </div>
	  




</main>
<?php $__env->stopSection(); ?>
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.min.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.jquery.min.js"></script>-->
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

	jQuery("input[name='is_criminal']").click(function(){
		var caaValue = jQuery("input[name='is_criminal']:checked").val();
		jQuery(".cerrormsg").text(" ");
		jQuery(".fileerrormsg").text(" ");
		if(caaValue =='1'){
			jQuery(".caa").show();
		}else{
			jQuery("#noFile").text('Document not selected');
			jQuery(".file-upload").removeClass("active");
			document.getElementById("affidavit").value = null;
			jQuery(".caa").hide();
		}
	});
	jQuery("#imageUpload").change(function() {
		readURL(this);
	});
	jQuery(document).ready(function() {
		var d = new Date();
		var year = d.getFullYear() - 25;
		var year1 = d.getFullYear() - 85;

		jQuery('#dob').datetimepicker({

			format: 'DD-MM-YYYY',
			useCurrent: false,
			maxDate: new Date()

		});

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
					//console.log(result);
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
				url: '<?php echo url('/') ?>/ropc/getallac',
				type: 'GET',
				data: {
					stcode: stcode,
					district: district
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





	 jQuery("select[name='ac']").change(function(){

	    var name = jQuery('input[name="name"]').val();
	    var fname = jQuery('input[name="fname"]').val();
	    var state = jQuery('select[name="state"]').val();
		var distt = jQuery('select[name="district"]').val();
		var consttype = jQuery('.consttype').val();
           jQuery.ajax({ 
            url: "<?php echo e(url('/ropc/getcandidateexit')); ?>",
            type: 'GET',
            dataType: 'json',
            
            data: {name:name,fname:fname,stcode:state,dist:distt,ac:consttype},

            success: function(data){   
          
            if (!$.trim(data)){   
    
}
else{ 
	var achtml = '';
                achtml = achtml + ' Candidate Profile already exists with these details! ';
	$('.modal-body').html(achtml);
$("#candModal").modal("show");  
   // alert("What follows is not blank: " + data.cand_name+" Father Name:" + data.candidate_father_name);
}	
            	
			}

			});

	 });


		// jQuery('input[name="age"]').mouseover(function(){
		// 	if(jQuery(this).val() != ''){
		// 		//Get Birth Year
		// 		var age = jQuery(this).val() ;
		// 		var accepted_age = 25;
		// 		if(age >= accepted_age){
		// 			jQuery('.errormsg').html('');
		// 		}else{
		// 			jQuery('.errormsg').html('');
		// 			jQuery('.ageerrormsg ').html('Age should be greater than 25');
		// 			jQuery( "input[name='age']" ).focus();
		// 			return false;
		// 		}

		// 	}
		// // }); 
		// //Check Validation
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
			var panno = jQuery('input[name="panno"]').val();
			var addressline1 = jQuery('input[name="addressline1"]').val();
			var addresshline1 = jQuery('input[name="addresshline1"]').val();
			var cand_vname = jQuery('input[name="cand_vname"]').val();
			//var addresshline2 = jQuery('input[name="addresshline2"]').val();
			var state = jQuery('select[name="state"]').val();
			var distt = jQuery('select[name="district"]').val();
			var consttype = jQuery('.consttype').val();
			var candcategory = jQuery('select[name="cand_category"]').val();

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
			if (candcategory == '') {
				jQuery('.errormsg').html('');
				jQuery('.caterrormsg').html('Please select candidate category');
				jQuery("select[name='cand_category']").focus();
				return false;
			}

			if ($('input[type=radio][name=gender]:checked').length == 0) {
				jQuery('.errormsg').html('');
				jQuery('.gerrormsg').html('Please select gender');
				jQuery('input[type=radio][name=gender]:checked').focus();
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
			if($('input[type=radio][name=is_criminal]:checked').length == 0)
			{
				jQuery('.errormsg').html('');
				jQuery('.cerrormsg').html('Please select criminal antecedents.');
				jQuery('input[type=radio][name=is_criminal]:checked').focus();
				return false;
			}else{
				
				if( document.getElementById("affidavit").files.length == 0 ){
					if(jQuery('input[name="is_criminal"]:checked').val() =='0'){
						return true;
					}else{
						jQuery('.errormsg').html('');
						jQuery('.fileerrormsg').html('Please select criminal antecedents.');
						jQuery('input[type=radio][name=is_criminal]:checked').focus();
						return false;
					}
					
				}else{
					var file_size = $('#affidavit')[0].files[0].size;
					if(file_size>3145728) {
						$(".fileerrormsg").html("File size is greater than 3 MB");
						return false;
					}else{
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
<?php echo $__env->make('admin.layouts.pc.dashboard-theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/pc/ro/createnomination.blade.php ENDPATH**/ ?>