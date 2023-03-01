<?php $__env->startSection('bradcome', 'Print Receipt'); ?>
<?php $__env->startSection('content'); ?>
<?php    $url = URL::to("/"); $j=0;
    if($caddata->scrutiny_time!='') $scrutiny_time=$caddata->scrutiny_time;
              elseif(old('scrutiny_time')!='')  $scrutiny_time=old('scrutiny_time');
              else $scrutiny_time='23:59:59';
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
      <li class="step-current"><b>&#10004;</b><span>Genrate Receipt(Part VI)</span></li>
      <li class=""><b>&#10004;</b><span>Print Receipt</span></li>
  </div>
</div>
<main role="main" class="inner cover mb-3">
  <section class="mt-3">
    <div class="container">
      <div class="row">

        <div class="card mt-3">
          <div class=" card-header">
            <div class=" row align-items-center">
              <div class="col">
                <h3>Genrate Receipt (Part VI)</h3>
              </div>
              <div class="col">
                <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span
                    class="badge badge-info"><?php echo e($st_name); ?></span> &nbsp;&nbsp; <b class="bolt">PC Name:</b>
                  <span class="badge badge-info"><?php echo e($ac_name); ?></span>&nbsp;&nbsp;
                </p>
              </div>

            </div>
          </div>

          <?php if(session('success_mes')): ?>
          <div class="alert alert-success"> <?php echo e(session('success_mes')); ?></div>
          <?php endif; ?>
          <?php if(session('error_mes')): ?>
          <div class="alert alert-danger"> <?php echo e(session('error_mes')); ?></div>
          <?php endif; ?>
          <?php if(session('success')): ?>
          <div class="alert alert-success"> <?php echo e(session('success')); ?></div>
          <?php endif; ?>
          <?php if(!empty($errors->first())): ?>
          <div class="alert alert-danger"> <span><?php echo e($errors->first()); ?></span> </div>
          <?php endif; ?>


          <div class="card-border">

            <form method="POST" action="<?php echo e(url('ropc/print-receipt')); ?>" onsubmit="return ">
              <?php echo e(csrf_field()); ?>

              <input type="hidden" name="candidate_id" value="<?php echo e($caddata->candidate_id); ?>">
              <input type="hidden" name="nom_id" value="<?php echo e($caddata->nom_id); ?>">
              <div class="nomination-fieldset">

                <div class="nomination-form-heading text-center">
                  <span class="fillupbold">PART VI </span><br />
                  <b>Receipt for Nomination Paper and Notice of Scrutiny </b> <br>
                  (To be handed over to the person presenting the Nomination Paper)
                </div>

                <div class="nomination-parts box recognised">
                  <div class="nomination-detail m-4" style="font-size:15px;">
                    <div class="one-param">
                      <p>
                        Serial No. of nomination paper <span
                          class="fillupbold dashed"><?php echo e($caddata->nomination_papersrno); ?> </span>
                      </p>
                      <p>
                        The nomination paper of <span class="fillupbold dashed"><?php echo e($caddata->cand_name); ?> </span> a
                        candidate for election from the <span class="fillupbold dashed"><?php echo e($ac_name); ?> </span> Parliament
                        constituency.

                        was delivered to me at my office at <span class="fillupbold dashed"><?php echo e($caddata->rosubmit_time); ?>

                        </span> (hour) on <span
                          class="fillupbold dashed"><?php echo e(date('d-m-Y', strtotime($caddata->rosubmit_date))); ?> </span>
                        (date) by <span class="dashed"><?php echo e($caddata->nomination_submittedby); ?></span> <?php echo e($applied_by); ?>.
                        All nomination papers will be taken up for scrutiny at
                        <span><input type="text" name="scrutiny_time" class="nomination-field-1 form-control dashed"
                            id="scrutiny_time" value="<?php echo e($scrutiny_time); ?>" /> </span>
                        <?php if($errors->has('scrutiny_time')): ?>
                        <span style="color:red;"><strong><?php echo e($errors->first('scrutiny_time')); ?></strong></span>
                        <?php endif; ?>

                        (hour) on <input type="text" name="scrutiny_date" id="scrutiny_date"
                          class="nomination-field-2 form-control dashed"
                          value="<?php echo e(date('d-m-Y', strtotime($scrutiny_date))); ?>" placeholder="scrutiny Date"
                          readonly="readonly" />
                        <?php if($errors->has('scrutiny_date')): ?>
                        <span style="color:red;"><strong><?php echo e($errors->first('scrutiny_date')); ?></strong></span>
                        <?php endif; ?>

                        (date) at <input type="text" name="place" class="nomination-field-2 form-control dashed"
                          readonly="readonly" value="<?php echo e($ac_name); ?>" /> Place.
                      </p>
                    </div>
                  </div>
                  <!--Nomination Details-->
                  <input type='hidden' name="fdate" class="nomination-field-4" value="<?php echo e(date('d-m-Y')); ?>"
                    readonly="readonly" />

                  <div class="btns-actn p-3" style="border-top: 1px solid #d7d7d7;">
                    <div class="row">
                      <div class="col"><a class="btn btn-secondary font-big"
                          href="<?php echo e(url('ropc/decisionbyro?nom_id='.encrypt_string($caddata->nom_id))); ?>">Back</a>
                      </div>
                      <div class="col text-right"> <button class="btn dark-purple-btn font-big" type="submit">Save &
                          Print Receipt</button></div>
                    </div>

                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>


      </div>
    </div>
  </section>
</main>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script type="text/javascript" src="<?php echo e(asset('admintheme/js/jquery-ui.js')); ?>"></script>

<script>
  $(document).ready(function(){  
   
  jQuery('#scrutiny_time').datetimepicker({
           format:'HH:mm:ss',
          //  minDate: new Date()
          });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.ac.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/candform/finalreceipt.blade.php ENDPATH**/ ?>