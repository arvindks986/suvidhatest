<?php $__env->startSection('title', 'Appointment Request'); ?>
<?php $__env->startSection('bradcome', 'Appointment Request'); ?>
<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/bootstrap.min.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('theme/css/custom.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('theme/css/custom-dark.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('theme/css/dark_custom.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('theme/css/prenom.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/font-awesome.min.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('appoinment/fonts.css')); ?> " type="text/css">
<?php   $url = URL::to("/");  ?>

<style>
    .w-50 {
        width: 55.5%;
    }
</style>

<main class="pt-3 px-2">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <span><sup class="text-danger">*</sup><em>Note :- Appointment is not a part of system, The preferable
                                dates selected by the candidates are just for the reference of RO. The Returning officer
                                need to give the appointment offline and communicate to the candidate. The system does
                                not send any notification to the candidate regarding appointment.</em></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-left mb-2">
                <a href="<?php echo e(url('/ropc/appointment_request_pdf')); ?>" class="btn btn-primary float-left" id="">Print
                    Pdf</a>
                <a href="<?php echo e(url('/ropc/dashboard')); ?>" class="btn btn-primary float-right" id="">Home</a>
            </div>
        </div>

        <div class="full-search-box">
            <div class="input-group">
                <input type="text" name="qrcode" id="qrcode" class="form-control"
                    placeholder="Search By Candidate Name." value="">
                <div class="input-group-append">
                    <button class="btn btn-lg font-big dark-purple-btn" type="button"><i class="fa fa-search"
                            aria-hidden="true"></i></button>
                </div>
            </div>
        </div><!-- End Of full-search-box Div -->
        <?php $i=1; ?>
        <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="physc-wrap <?php echo e(($result['appointment_details'][0]->is_ro_acccept==1) ? 'appnt-given' : ''); ?>">
            <div class="d-flex tr-bg shadow-sm mb-3 myTable">
                <div class="py-3">
                    <figure class="img-id">
                        <figcaption><?php echo e($i); ?></figcaption>
                        <?php if(!empty($result['image'])): ?>
                        <img src="<?php echo e($url.'/'.$result['image']); ?>" class="prfl-pic img-thumbnail" alt="">
                        <?php else: ?>
                        <img src="<?php echo e(asset('theme/img/nominator-icon.png')); ?>" class="prfl-pic img-thumbnail" alt="">
                        <?php endif; ?>
                    </figure>
                </div>
                <div class="py-4 px-3 w-30 phys-bdy">
                    <div class="full-name">
                        <?php 
                    if($result['gender'] =='male'){
                        $gen = '(M)';
                        $hgen = '(पु)';
                    }elseif ($result['gender'] =='female') {
                          $gen = '(F)';
                            $hgen = '(म)';
                    }else{
                          $gen = '(O)';
                            $hgen = 'अ';
                    } ?>
                        <h5><?php echo e(!empty($result['hname']) ? $result['hname'] : ''); ?> <span><?php echo e($hgen); ?></span></h5>
                        <h5><?php echo e($result['name']); ?> <?php echo e($gen); ?></h5>
                    </div>

                    <div class="d-inline-flex align-items-center mt-1">
                        <figure class="mb-0"><img src="<?php echo e(asset('theme/img/vendor/icon-001.png')); ?>"></figure>
                        <div>
                            <h6 class="mb-0"><?php echo e($result['father_name']); ?></h6>
                            <h6><?php echo e(!empty($result['father_hname']) ? $result['father_hname'] : ''); ?></h6>
                        </div>
                    </div>
                    <div class="d-inline-flex align-items-center mt-1">
                        <figure class="mb-0"><img src="<?php echo e(asset('theme/img/vendor/icon-003.png')); ?>"></figure>
                        <div>
                            <h6><?php if(isset($result['address'])): ?><?php echo e($result['address']); ?><?php endif; ?></h6>
                        </div>
                    </div>

                    <div class="d-inline-flex align-items-center mt-1">
                        <figure class="mb-0"><img src="<?php echo e(asset('theme/img/vendor/icon-002.png')); ?>"></figure>
                        <div>
                            <h6><?php echo e($result['age']); ?></h6>
                        </div>
                    </div>
                    <div class="d-inline-flex align-items-center mt-1">
                        <figure class="mb-0"><img src="<?php echo e(asset('theme/img/vendor/icon-004.png')); ?>"></figure>
                        <div>
                            <h6><?php echo e($result['gender']); ?></h6>
                        </div>
                    </div>
                </div>
                <div class="bg-light p-2 custom-border-right w-50">
                    <strong>Nomination details</strong>
                    <?php 
				if(empty($result['prescrutiny_status'])){
					$status = 'submitted For Pre-Scrutiny';
					$status_color = 'pending';
				}elseif($result['prescrutiny_status'] == '1'){
					$status = 'Pre-Scrutiny Cleared';
					$status_color = 'cleared';
				}elseif($result['prescrutiny_status'] == '2'){
					$status = 'Defects in Pre-Scrutiny';
					$status_color = 'defected';
				}
                ?>
                    <h5>Total Nominations:- <?php echo e(count($result['nomination_details'])); ?></h5>
                    <?php if(count($result['nomination_details'])>0): ?>
                    <?php $__currentLoopData = $result['nomination_details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        if($item->recognized_party == '1'){
                            $party=getpartybyid($item->party_id)->PARTYNAME; 
                        }elseif($item->recognized_party == '2'){
                            $party=getpartybyid($item->party_id2)->PARTYNAME; 
                        }else{
                            $party=getpartybyid($item->party_id)->PARTYNAME; 
                        }
                    ?>
                    <h6 class="py-2 d-flex align-items-center justify-content-between">
                        <div><span class="mr-1 prtNm"><i class="fa fa-address-book-o"
                                    aria-hidden="true"></i></span><?php echo e($item->nomination_no.' - '.$party); ?></div> <a
                            href="<?php echo e(url('/ropc/detail/'.encrypt_string($item->id))); ?>"
                            class="btn btn-primary p-1 float-right">View</a>
                    </h6>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>

                <div class="bg-custom-deposit w-35 p-2">
                    <strong>Preferable Appointment Date</strong>
                    <?php if(count($result['appointment_details'])>0): ?>
                    <?php $__currentLoopData = $result['appointment_details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <h5 class="my-3 dyTm"><i class="fa fa-calendar mr-1"
                            aria-hidden="true"></i><?php echo e(date('d-m-Y', strtotime($item->appointment_date))); ?><span
                            class="ml-2 mr-1"><i class="fa fa-clock-o"
                                aria-hidden="true"></i></span><?php echo e(date('h:i A', strtotime($item->appointment_time))); ?>

                    </h5>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <div class="row m-1">
                        <div class="col">
                            <fieldset class="mb-2">
                                <legend>Mark as <sup>*</sup></legend>
                                <div class="row">
                                    <div class="col">
                                        <label class="radioBtn">Appointment Given
                                            <input type="checkbox"
                                                <?php if($result['appointment_details'][0]->is_ro_acccept!='1'): ?>
                                            candidate_id='<?php echo e($result['appointment_details'][0]->candidate_id); ?>'
                                            st_code='<?php echo e($result['appointment_details'][0]->st_code); ?>'
                                            ac_no='<?php echo e($result['appointment_details'][0]->pc_no); ?>'
                                            class="appointment_given" <?php endif; ?> name="appointment_given"
                                            value="1"
                                            <?php echo e($result['appointment_details'][0]->is_ro_acccept=='1' ? 'checked disabled' : ''); ?>>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $i++; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    </div><!-- End Of container-fluid Div -->
</main>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script src="<?php echo e(asset('appoinment/js/bootstrap.min.js')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('appoinment/js/owl.carousel.js')); ?>"></script>
<?php if(session('success_mes')): ?>
<script type="text/javascript">
    success_messages("<?php echo e(session('success_mes')); ?>");
</script>
<?php endif; ?>

<?php if(session('error_mes')): ?>
<script type="text/javascript">
    error_messages("<?php echo e(session('error_mes')); ?>");
</script>
<?php endif; ?>
<script type="text/javascript">
    jQuery(document).ready(function(){

        //By Searh Text
		jQuery("#qrcode").on("keyup", function() {
		var value = $(this).val().toUpperCase();
		jQuery(".myTable").filter(function() {
			// jQuery(this).toggle();
			const display = jQuery(this).text().toUpperCase().indexOf(value) > -1
			if ( display === true ) {
				$(this).addClass('d-flex');
			} else if ( display === false ) {
				$(this).removeClass('d-flex');
				$(this).hide();
			}
		});
		});

		$('.appointment_given').change(function(e) {
			var filter = $('.appointment_given:checked').val();
            if(!filter){
                filter = '';
            }
            candidate_id = $(this).attr('candidate_id');
            st_code = $(this).attr('st_code');
            ac_no = $(this).attr('ac_no');

            
            $.ajax({
			url: "<?php echo e(url('/ropc/appointment_accepted')); ?>",
			type: 'POST',
			data: '_token=<?php echo csrf_token() ?>&is_ro_accepted='+filter+'&candidate_id='+candidate_id+'&st_code='+st_code+'&pc_no='+ac_no,
			dataType: 'json',
			beforeSend: function() {
			},
			complete: function() {
			},
			success: function(json) {
				if(json['success']){
                    location.reload();
                }
			},
			error: function(data) {
			}
			});

			// let new_url = addParam('status', filter);
			// window.location.href = new_url;
		});

		function addParam(key,val) {
			var currentUrl = "<?php echo url()->full(); ?>";
			if(key == 'prescrutiny_status' && val == 'all'){
			currentUrl = "<?php echo e(url()->current()); ?>";
			}
			var url = new URL(currentUrl);
			url.searchParams.set(key, val);
			return url.href;
		}

	});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.ac.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/candform/prescrutiny/appointment_request.blade.php ENDPATH**/ ?>