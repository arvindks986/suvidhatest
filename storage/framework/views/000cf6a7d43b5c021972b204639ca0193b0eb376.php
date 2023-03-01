<?php $__env->startSection('bradcome', 'Decision By RO (Part IV)'); ?>
<?php $__env->startSection('content'); ?>  
<?php 
      if($caddata->cand_name == $caddata->nomination_submittedby){
        $applied_by = '(Candidate).';
      }elseif(empty($caddata->nomination_submittedby)) {
        $applied_by = '(Candidate/Proposer).';
      }else{
        $applied_by = '(Proposer).';
      }

      if($caddata->nomination_submittedby!='') $submiteed=$caddata->nomination_submittedby;
          else  $submiteed=old('nomination_submittedby');
   
      if($caddata->rosubmit_date!='') $rosubmit_date=$caddata->rosubmit_date;
              elseif(old('nomination_date')!='')  $rosubmit_date=old('nomination_date');
              else  $rosubmit_date=Carbon\Carbon::parse(now())->format('d-m-Y') ;

      if($caddata->rosubmit_time!='') $rosubmit_time=$caddata->rosubmit_time;
          elseif(old('nomination_hour')!='') $rosubmit_time=old('nomination_hour');
              else{
                    if(isset($ro_scaned_time->ro_submit_time) && !empty($ro_scaned_time->ro_submit_time)){
                    $currentTime = Carbon\Carbon::now();
                    $diff=$currentTime->diffInSeconds($ro_scaned_time->ro_submit_time);
                    }else{ 
                        $diff=600; 
                    }
                  if($diff>601){
                    $rosubmit_time = '';
                  }else{
                    $rosubmit_time = isset($ro_scaned_time->ro_submit_time) ? $ro_scaned_time->ro_submit_time : '';
                  }
              }           
?>
<?php   
          $url = URL::to("/"); $j=0;
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
          <li class="step-current"><b>&#10004;</b><span>Decision by RO (Part IV)</span></li>
          <li class=""><b>&#10004;</b><span>Genrate Receipt (Part VI)</span></li>
          <li class=""><b>&#10004;</b><span>Print Receipt</span></li>
        </div>
      </div>
  <main role="main" class="inner cover mb-3">
  <section class="mt-3">
  <div class="container">   
  <div class="row">
            
  <div class="card mt-3" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class="row align-items-center">
                 <div class="col"> <h3>Decision By Ro (Part IV)</h3> </div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info"><?php echo e($st_name); ?></span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info"><?php echo e($ac_name); ?></span>&nbsp;&nbsp;  
            </p></div>
         
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
         <form class="form-horizontal" id="election_form" method="post" action="<?php echo e(url('ropc/decisionvalidate')); ?>" >
                <?php echo e(csrf_field()); ?>   
            <input type="hidden" name="candidate_id" value="<?php echo e(isset($caddata) ?$caddata->candidate_id:old('candidate_id')); ?>">
            <input type="hidden" name="nom_id" value="<?php echo e(isset($caddata) ?$caddata->nom_id:old('nom_id')); ?>">
            <div class="nomination-fieldset">
            
              <div class="nomination-form-heading text-center">
                  <strong>PART IV </strong><br/> (To be filled by the Returning Officer)  
              </div>
             
              <div class="nomination-parts box recognised">
              <div class="nomination-detail m-4" style="font-size: 15px;">
              <div class="one-param">
               <p>Serial No. of nomination paper <input type="text" name="nomination_srno" class="nomination-field-1 form-control" value="<?php echo e(isset($caddata->nomination_papersrno) ?$caddata->nomination_papersrno:old('nomination_srno')); ?>" /> <?php if($errors->has('nomination_srno')): ?>
                <span style="color:red;"><strong><?php echo e($errors->first('nomination_srno')); ?></strong></span>
           <?php endif; ?></p>  
           <p>
            This nomination was delivered to me at my office at 
            <input type='text' name="nomination_hour" class="nomination-field-1 time-pick  form-control" value="<?php echo e($rosubmit_time); ?>"/> (hour) on <input type='text' readonly="readonly" name="nomination_date" class="nomination-field-1 form-control" value="<?php echo e(date('d-m-Y', strtotime($rosubmit_date))); ?>"/>(date) by 
            <select name="nomination_submittedby" class="nomination-field-2  form-control">
              <option value="" selected="selected">Select One</option>
              <option value="<?php echo e($caddata->cand_name); ?>" candi_type='cand' <?php if($caddata->cand_name==$submiteed): ?> selected="selected" <?php endif; ?>><?php echo e($caddata->cand_name); ?></option>
               <?php if($caddata->party_type == 1): ?>
                  <option value="<?php echo e($caddata->proposer_name); ?>" candi_type='prop' <?php if($caddata->proposer_name==$submiteed): ?> selected="selected" <?php endif; ?>><?php echo e($caddata->proposer_name); ?></option>
                <?php else: ?>
                    <?php $__currentLoopData = $non_recognized_proposers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($item['fullname']); ?>" candi_type='prop' <?php if($item['fullname']==$submiteed): ?> selected="selected" <?php endif; ?>><?php echo e($item['fullname']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </select>
            <?php if($errors->has('nomination_submittedby')): ?>
            <span style="color:red;"><strong><?php echo e($errors->first('nomination_submittedby')); ?></strong></span>
          <?php endif; ?>
          <span id="prop_type"> <?php echo e($applied_by); ?></span>
          </p>
        </div>    
                </div><!--Nomination Details-->
          <div class="btns-actn p-3" style="border-top: 1px solid #d7d7d7">      
              <div class="row">
                <div class="col">
                  <a href="<?php echo e(url('ropc/candidateinformation?nom_id='.encrypt_string($caddata->nomination_no))); ?>" class="btn btn-secondary font-big">Back</a>
                </div> 
                <div class="col text-right"><button class="btn dark-purple-btn font-big" type="submit">Save & Next</button> </div> 
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
<script>
  $(document).ready(function(e) {
    var status_txt = '';
    $('.nomination-field-2').change(function(e) {
      var cand_type = $('option:selected', this).attr('candi_type');
      if(cand_type == 'cand'){
        status_txt = '(Candidate).';
      }else if(cand_type == 'prop'){
        status_txt = '(Proposer).';
      }else{
        status_txt = '(Candidate/Proposer).';
      }
      $('#prop_type').html(status_txt);
    })

    $('.time-pick').datetimepicker({
        format: 'HH:mm:ss'
    });
  });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.ac.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/candform/decisionbyro.blade.php ENDPATH**/ ?>