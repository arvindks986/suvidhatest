<?php $__env->startSection('bradcome', 'Print Receipt'); ?>
<?php $__env->startSection('content'); ?>  
<?php   
          $url = URL::to("/"); $j=0;
        if($caddata->cand_name == $caddata->nomination_submittedby){
          $applied_by = '(candidate)';
        }else {
          $applied_by = '(proposer)';
        }  
    ?>

<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/bootstrap.min.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('theme/css/custom.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('theme/css/custom-dark.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/font-awesome.min.css')); ?> " type="text/css">
<link rel="stylesheet" href="<?php echo e(asset('appoinment/fonts.css')); ?> " type="text/css">

  <div class="container">
    <div class="step-wrap mt-4 text-center">
      <ul>
        <li class="step-success"><b>&#10004;</b><span>Verify Nomination Details</span></li>
        <li class="step-success"><b>&#10004;</b><span>Decision by RO (Part IV)</span></li>
        <li class="step-success"><b>&#10004;</b><span>Genrate Receipt (Part VI)</span></li>
        <li class="step-current"><b>&#10004;</b><span>Print Receipt</span></li>
      </div>
    </div>

  <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">
  <div class="row">
            
  <div class="card mt-3">
                <div class="card-header">
                <div class="row">
                 <div class="col"> <h3>Print Receipt </h3> </div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info"><?php echo e($st_name); ?></span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info"><?php echo e($ac_name); ?></span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
    
    <div class="card-border">  
  <div class="nomination-fieldset">
            
              <div class="nomination-form-heading text-center">
                  <span class="fillupbold">PART VI </span><br/>
                  <b>Receipt for Nomination Paper and Notice of Scrutiny </b> <br>
                  (To be handed over to the person presenting the Nomination Paper) 
                </div>
              <div class="nomination-parts box recognised">
              <div class="nomination-detail m-4" style="font-size:15px;">
               <div class="one-param">
                 <p>Serial No. of nomination paper <span class="fillupbold dashed"><?php echo e($caddata->nomination_papersrno); ?></span></p>    
                  <p> The nomination paper of  <span class="fillupbold dashed"><?php echo e(strtoupper($caddata->cand_name)); ?> </span> a candidate for election from the 
                    <?php if(!empty($ac)): ?>  <span class="fillupbold dashed"><?php echo e(strtoupper($ac->PC_NAME)); ?> </span>  Parliament constituency <?php endif; ?> 
                    was delivered to me at my office  at  <span class="fillupbold dashed"><?php echo e($caddata->rosubmit_time); ?> </span>
                     (hour) on  <span class="fillupbold dashed"><?php echo e(date('d-m-Y', strtotime($caddata->rosubmit_date))); ?> </span>  (date) by <span class="dashed"><?php echo e($caddata->nomination_submittedby); ?></span> <?php echo e($applied_by); ?>. All nomination papers will be taken up for scrutiny at  <span class="fillupbold dashed"><?php echo e($caddata->scrutiny_time); ?> </span>   (hour) on  <span class="fillupbold dashed"><?php echo e($caddata->scrutiny_date); ?> </span> (date) at  
                     <span class="fillupbold dashed"><?php echo e(strtoupper($caddata->place)); ?> </span>  Place.  </p>
               </div>

                 
                </div><!--Nomination Details-->
                 
                 
                 <div class="btns-actn p-3" style="border-top: 1px solid #d7d7d7;">
                  <div class="row">
                    <div class="col">
                      <a class="btn btn-secondary" href="<?php echo e(url('ropc/finalreceipt?nom_id='.encrypt_string($caddata->nom_id))); ?>">Back</a>
                     </div>
                    <div class="col text-right">
                      <strong>Receipt</strong>
                      <i class="fa fa-download" aria-hidden="true"><a  href="<?php echo e(url('/ropc/nomination-receipt-print?nom_id='.$caddata->nom_id)); ?>" download="download" title="Download PDF">English</a></i> /
                      <i class="fa fa-download" aria-hidden="true"><a  href="<?php echo e(url('/ropc/nomination-receipt-print/Hindi?nom_id='.$caddata->nom_id)); ?>"  download="download" title="Download PDF">Hindi</a></i>
                    <a href="<?php echo e(url('ropc/listallapplicant')); ?>" class="btn dark-purple-btn">Proceed For Other Phycial Verifiction</a>
                        </div>
                  </div>
                  </div>
              </div><!--Nomination Parts-->
            </div>
    </div>
  
  
  </div>
  </div>
</div>
  </section>
  </main>
 
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('admin.layouts.ac.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/candform/printreceipt.blade.php ENDPATH**/ ?>