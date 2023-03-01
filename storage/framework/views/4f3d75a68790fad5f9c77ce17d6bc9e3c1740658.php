<?php $__env->startSection('content'); ?>
<main>
   <section class="main-box">
    <div class="container-fluid">
     <div class="circle peach-gradient">
            <img src="<?php echo e(asset('theme/img/vendor/background.png')); ?>" alt=""></div>
    <!--LOG OUT BUTTON STARTS-->
      <a href="<?php echo e(url('/candidatelogout')); ?>" class="btn btn-primary nav-link logout float-right mt-3"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a>
    <!--LOG OUT BUTTON ENDS-->
    <div class="row d-flex flex-column flex-md-row align-items-center" style="height:100vh;">
  
  
   <div class="col-md-6 login-page "> 
  <figure class="evm-logo">
  <span style="margin: auto;"><img class="eci-logo" src="<?php echo e(asset('theme/img/logo/eci-logo.png')); ?>"> <p>Election Commission of India</p> </span></figure> </div>
    <div class="col-md-6">
    <div class="login-right">
    
   
                <div class="d-flex align-items-center mb-3">
                  <h4>Select<span> Applicant</span> Type</h4>
                </div>
               
<form method="post" action="<?php echo e(url('/permissionrole')); ?>">
   <?php echo e(csrf_field()); ?>

<div class="form-group">       
<select name="role_id" id="role_id"  class="form-control">
<option value="">Select Applicant Type</option>
<?php if(count($role_type)>0): ?>
<?php $__currentLoopData = $role_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$police_list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<option value="<?php echo e($police_list->role_id); ?>"><?php echo e($police_list->role_name); ?></option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
</select>
<span class="text-danger"><?php echo e($errors->first('role_id')); ?></span>

</div>
<!--
<div class="form-group">       
<select name="party_id" id="role_id"  class="form-control">
<option value="">Select Political Party / Independent </option>
<?php if(count($role)>0): ?>
<?php $__currentLoopData = $role; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<option value="<?php echo e($role->CCODE); ?>"><?php echo e($role->PARTYNAME); ?></option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
</select>
<span class="text-danger"><?php echo e($errors->first('party_id')); ?></span>

</div>
-->
<div class="form-group float-right">       
<input type="submit" class="btn btn-primary" name="submit">
<!--  <input type="submit" value="Login" >   -->                
</div>
</form>
               
              
    
    </div>    
    </div>    
    </div>
        </div>
   </section>
    <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
      <div class="col"></div>
            <div class="col">
              <figure class="foot-lft"><img src="<?php echo e(asset('theme/img/vendor/footer-img.png')); ?>"></figure>
            </div>
            <div class="col text-right">
      
       
       <nav>
       <a href="#">Privacy Policy</a> &nbsp; | &nbsp; 
       <a href="#">Term &amp; Conditions</a> &nbsp; | &nbsp;   
       <a href="#">About ECI</a>
       </nav>
       
      
              <div class="copyright">Copyright @2019    Election Commission of India. All rights reserved.</div>
               
            </div>
          </div>
        </div>
      </footer> 
  </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.login', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/politicalparty/RoleType.blade.php ENDPATH**/ ?>