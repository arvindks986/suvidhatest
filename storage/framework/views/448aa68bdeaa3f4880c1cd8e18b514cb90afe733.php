<?php $__env->startSection('title', 'Candidate Nomintion Details'); ?>
<?php $__env->startSection('bradcome', 'Multiple Candidate Nomintion'); ?>
<?php $__env->startSection('content'); ?>	
 <?php  $st=app(App\commonModel::class)->getstatebystatecode($stcode);  
        $pc=app(App\commonModel::class)->getpcbypcno($stcode,$constno); 
        $partyd=getallpartylist();
        $symb=getsymbollist();
		$symb1=getsymboltypelist('T');
	?>
<link rel="stylesheet" href="<?php echo e(asset('admintheme/css/nomination.css')); ?>" id="theme-stylesheet">
 <style type="text/css">
     html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
 
<main role="main" class="inner cover mb-3">
<section>
	 
	 <form enctype="multipart/form-data" id="election_form" method="POST"  action="<?php echo e(url('ropc/newmultiplenomination')); ?>" autocomplete='off' enctype="x-www-urlencoded">
	  <?php echo e(csrf_field()); ?>

 
  <div class="container">
  <div class="row">
  
  <div class="card text-left mt-3" style="width:100%; margin:0 auto 10px auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>Candidate Multiple Nomintion Details</h4></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info"><?php echo e($st->ST_NAME); ?></span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info"><?php echo e($pc->PC_NAME); ?></span>&nbsp;&nbsp;  </p></div>
         
                </div>
                </div>
     
    <div class="card-body">  
 		<div class="container p-0">
 			<div class="row">
	    <?php if(session('error_mes')): ?>
          <div class="alert alert-danger"><?php echo e(session('error_mes')); ?></div>
        <?php endif; ?>
        
	</div> 
			<div class="row">
			<div class="col">
					<label class="">Select Candidate Name <sup>*</sup></label>
			 <select name="candidate_name" class="form-control candidate_id">
				<option value="">-- Select Candidate Name--</option>
					 
					<?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<option value="<?php echo e($list->candidate_id); ?>" <?php if($list->candidate_id==old('candidate_name')): ?> selected="selected" <?php endif; ?> ><?php echo e($list->candidate_id); ?>- <?php echo e($list->cand_name); ?>-C/O.:-<?php echo e($list->candidate_father_name); ?> </option>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					 
			</select>
		 		<?php if($errors->has('candidate_name')): ?>
                  		  <span style="color:red;"><?php echo e($errors->first('candidate_name')); ?></span>
               			<?php endif; ?>
			<div class="nameerrormsg errormsg errorred"></div>
		  </div>
		  <div class="col"> </div>
		</div>
		</div>				 
	 
	
<div class="form-group row">

<div class="col">
<label class="">Party Name <sup>*</sup></label>
		 
			

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
	<div class="form-group row float-right">       
					  <div class="col">
						<button type="submit" id="candnomination" class="btn btn-primary">Submit</button>
					  </div>
				 </div>
	</div><!-- end row-->
	 
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
  
jQuery(document).ready(function(){  
			 
	  
	jQuery('select[name="party_id"]').change(function(){ 
		var partyid = jQuery(this).val();   
		$('#mysysDiv').hide();  
		jQuery.ajax({
            url: "<?php echo e(url('/ropc/getSymbol')); ?>",
            type: 'GET',
            data: {partyid:partyid},
            success: function(result){  
            	jQuery("select[name='symbol_id']").html(result);
			 },
		       error: function (data, textStatus, errorThrown) {
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
	 
	 
	 
	 
    jQuery('#candnomination').click(function(){
		var partyid = jQuery('select[name="party_id"]').val();
		var symbolid = jQuery('select[name="symbol_id"]').val();
		var candidate_name = jQuery('select[name="candidate_name"]').val();
		 
		if(candidate_name == ''){
            jQuery('.errormsg').html('');
			jQuery('.nameerrormsg').html('Please select candidate name');
			jQuery( "input[name='candidate_name']" ).focus();
			return false;
		} 
		
		if(partyid == ''){
            jQuery('.errormsg').html('');
			jQuery('.perrormsg').html('Please select party');
			jQuery( "input[name='party_id']" ).focus();
			return false;
		}
		 
		if(symbolid == ''){
            jQuery('.errormsg').html('');
			jQuery('.serrormsg').html('Please select symbol');
			jQuery( "input[name='symbol_id']" ).focus();
			return false;
		}
		
	  
	});
	 
});
 
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.pc.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/pc/ro/multiplenomination.blade.php ENDPATH**/ ?>