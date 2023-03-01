<section class="breadcrumb-section">
<div class="container-fluid">
<div class="row">
  <div class="col">
    <ul id="breadcrumb" class="pt-1 mr-auto">
      <li><a href="<?php echo e(url('/ropc/dashboard')); ?>"><span class="icon icon-home"> </span></a></li>
      <li><a href="<?php echo e(url('/dashboard-nomination-new')); ?>"><span class="icon icon-beaker"> </span> Nomination</a></li>
      <!-- <li><span class="icon icon-double-angle-right"></span> <?php echo $__env->yieldContent('bradcome'); ?></li>   -->
    </ul>
	<div class="nav-header float-right welcome">
	
	<ul class="float-right">
         
          <li><a href="javascript:void(0)" >Welcome:- <b>
            <?php if(Session::has('Applicant_type')): ?>
                           <?php echo e(Session::get('Applicant_type')); ?>

                   <?php else: ?>
                    0
                   <?php endif; ?>
          <!-- <?php echo e($users=Session::get('Applicant_type')); ?> -->
          </b></a></li>
        </ul>
	



</div>
  </div>
</div>
</div>
</section> <?php /**PATH E:\xampp\htdocs\suvidha\resources\views/includes/bradcom.blade.php ENDPATH**/ ?>