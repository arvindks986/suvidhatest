 <?php $__env->startSection('title', 'Affidavit e-File'); ?> <?php $__env->startSection('content'); ?>
<style type="text/css">
    .error {
        font-size: 12px;
        color: red;
    }
    .step-wrap.mt-4 ul li {
        margin-bottom: 21px;
    }
</style>

<link rel="stylesheet" href="<?php echo e(asset('admintheme/css/nomination.css')); ?>" id="theme-stylesheet" />
<link rel="stylesheet" href="<?php echo e(asset('admintheme/css/jquery-ui.css')); ?>" id="theme-stylesheet" />

<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/bootstrap.min.css')); ?> " type="text/css" />
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom.css')); ?> " type="text/css" />
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-dark.css')); ?> " type="text/css" />
<main role="main" class="inner cover mb-3">     
    <section>
        <div class="container pt-3">
            <div class="row">
                <div class="card">
                    <div class="tab-content">
		  <div id="nomin" class="tab-pane active">
			<div class="header-title">
				<div class="row align-items-center">
					<div class="col-12">
						<h4><?php echo e(Lang::get('affidavit.affidavit')); ?> <a href="<?php echo e(url()->previous()); ?>" class="btn btn-default float-right"> <?php echo e(Lang::get('affidavit.back')); ?> </a></h4> 
					</div> 
				</div>
			</div>
		 <div class="affidavitId">
		  <div class="tab-body">
		  	 <div class="home">
			    <div class="row">
				  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn d-inline-flex">
						   <span class="apply-icon"></span><a href="<?php echo e(route('affidavit.dashboard')); ?>"><?php echo e(Lang::get('affidavit.file')); ?> <br /><?php echo e(Lang::get('affidavit.e_affidavit')); ?></a>
						   <div class="help-txt"><?php echo e(Lang::get('affidavit.here_you_can_apply_for_new_affidavit_application')); ?></div>
					   </div>
					 </div>	
				  </div>
                  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn my-apped-btn d-inline-flex">
						   <span class="my-apped-icon"></span><a href="<?php echo e(route('my.affidavit')); ?>"><?php echo e(Lang::get('affidavit.my')); ?><br><?php echo e(Lang::get('affidavit.e_affidavit_s')); ?></a>
						   <div class="help-txt"><?php echo e(Lang::get('affidavit.all_your_saved_and_submitted_applications_are_listed_here')); ?> </div>
					   </div>
					 </div>	
				  </div>				  
				 </div>
			  </div><!-- End Of home Div -->
			</div>
		  </div><!-- End Of nomin Div -->  
	  </div>
		</div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php $__env->stopSection(); ?> <?php $__env->startSection('script'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/affidavit/affidavit-e-file.blade.php ENDPATH**/ ?>