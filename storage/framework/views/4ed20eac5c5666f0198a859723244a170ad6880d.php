<section class="breadcrumb-section mybradcom">
<div class="container-fluid">
<div class="row">
  <div class="col">
    <ul id="breadcrumb" class="pt-2 mr-auto">
      <li><a href="<?php echo e(url('/roac/dashboard')); ?>"><span class="icon icon-home"> </span></a></li>
      <li><a href="#"><span class="icon icon-beaker"> </span> Candidate Nomination  and Counting</a></li>
      <li><span class="icon icon-double-angle-right"></span> <?php echo $__env->yieldContent('bradcome'); ?></li>  
    </ul>
	<div class="nav-header welcome float-right">
   <ul class="float-right"> 
       <li>
	   <a href="javascript:window.print()"> 
                           <i class="fa fa-print"></i>  </a> &nbsp;&nbsp;&nbsp;
	   <a href="javascript:void(0)">   Welcome :- <?php echo e($user_data->designation); ?> LoginId:- <?php echo e($user_data->officername); ?></a> </li>
      </ul>
	  <input type="hidden" value="<?php echo e($_SERVER["SERVER_ADDR"]); ?>" readonly>
</div>
  </div>
</div>
</div>
</section> 
<!-- print header start -->
<style>
    th{color: black !important;
    }
</style>
<!-- print header end --><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/includes/ac/adminbradcom.blade.php ENDPATH**/ ?>