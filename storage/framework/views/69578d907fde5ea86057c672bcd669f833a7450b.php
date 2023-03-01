<?php $__env->startSection('content'); ?>

<main>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

   
	
	 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/bootstrap.min.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-profile.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-dark.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/font-awesome.min.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/fonts.css')); ?> " type="text/css">
	
   <title>Dashboard</title>
  </head>
  <body> 
   
   <main class="pt-3 pb-5 pl-5 pr-5">
	 <div class="container-fluid">
	 <div class="custom-tab-area">	 
	  <!-- Nav tabs -->
	  <ul class="nav nav-tabs">
		<li class="nav-item nav-md-item">
		  <a class="nav-link active" data-toggle="tab" href="#nomin"><?php echo e(__('dashboard.tag1')); ?></a>
		</li>
		
		<li class="nav-item nav-md-item">
		  <a class="nav-link" href="<?php echo e(route('affidavit.e.file')); ?>" class="Affidavit"><?php echo e(__('dashboard.tag4')); ?></a>
		</li>
		
		<li class="nav-item nav-md-item">
		  <a class="nav-link" data-toggle="tab" href="#permis"><?php echo e(__('dashboard.tag2')); ?></a>
		</li>
		
	  </ul>	 
	  
	  
	  
	  
	  
	  <?php  $acs = 0;	
				 $std='';
				 $acd=0;
				 $std2='';
				 $acd2=0;
				
				$acs=app(App\Http\Controllers\Nomination\NominationController::class)->getAcs();
				if($acs!='0' && $acs!=''){ 
					$exp = explode('***', $acs); 
					$std=encrypt_string($exp[0]);
					$acd=encrypt_string($exp[1]);
					$std2=$exp[0];
					$acd2=$exp[1];
				} else {  
					$std='';
					$acd='';
					$std2='';
					$acd2='';
				}
				
				
				
				$md='';
				 $tststs = app(App\Http\Controllers\Nomination\NominationController::class)->getProfileD(); 
				if($tststs =='One' ){
				  $md = '/nomination/apply-nomination-step-2';
				} else {
				  $md ='/nomination/apply-nomination-step-1';
				}
				
				//echo $acd2.'--'.$std2; die;
				
		  ?>
	  
	  
	 
	   <div class="card card-shadow mt-4">
		<div class="card-body p-0">
	   <!-- Tab panes -->
	  <div class="tab-content">
		  <div id="nomin" class="tab-pane active">
		    <!-- From Here-->
			<div class="header-title">
			<div class="row">
			  <div class="col-6">
				<h5><?php echo e(__('dashboard.tag1')); ?>	</h5> 
			  </div>  
			  <div class="col-6"><div class="text-right"></div></div>  
			</div>  
	      </div>	
			 <!--End From Here--> 
			 
		 <div style="display:block;"> 
		
		
		  
		  <div class="tab-body">
		  	 <div class="home">
			    <div class="row">
				  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn d-inline-flex">
						   <span class="apply-icon"></span><a href="<?php echo url('/'); ?><?php echo e($md); ?>"><?php echo e(__('dashboard.Apply_New')); ?><br/><?php echo e(__('dashboard.tag1')); ?></a>
						   <div class="help-txt"><?php echo e(__('dashboard.new_nomination_message')); ?></div>
					   </div>
					 </div>	
				  </div>
                  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn my-apped-btn d-inline-flex">
						   <span class="my-apped-icon"></span><a href="<?php echo e('nomination/nominations?pcs='.$acd.'&std='.$std); ?>"><?php echo e(__('dashboard.my')); ?><br/> <?php echo e(__('dashboard.tag1')); ?></a>
						   <div class="help-txt"><?php echo e(__('dashboard.saved_and_submitted_nomination')); ?></div>
					   </div>
					 </div>	
				  </div>				  
				 </div>
			  </div><!-- End Of home Div -->
			</div>
			 
		  </div><!-- End Of nomin Div -->  
		   	
		 </div> 
		<div id="permis" class="tab-pane">
		  <div class="header-title">
			<div class="row">
			  <div class="col-6">
				<h5><?php echo e(__('dashboard.tag2')); ?>	</h5> 
			  </div>  
			</div>  
	      </div>	  
		 <!-- From Here-->		  
		  <div class="tab-body">
		   <div class="tab-body">
				<div class="home">
			    <div class="row">
				  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn d-inline-flex">
						   <span class="apply-icon"></span><a href="<?php echo e(url('/create')); ?>">Apply New<br/>Permission</a>
						   <div class="help-txt">Here you can apply for new Permission</div>
					   </div>
					 </div>	
				  </div>
                  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn my-apped-btn d-inline-flex">
						   <span class="my-apped-icon"></span><a href="<?php echo e(url('/permission')); ?>">My<br/> Permission</a>
						   <div class="help-txt">All your saved and submitted permission are listed here </div>
					   </div>
					 </div>	
				  </div>				  
				 </div>
			  </div><!-- End Of home Div -->
			</div>
			</div>
	 <!--End From Here-->
		  </div><!-- End Of permis Div -->
		<div id="adver" class="tab-pane fade">
		  <div class="header-title">
			<div class="row">
			  <div class="col-6">
				<h5><?php echo e(__('dashboard.tag3')); ?></h5> 
			  </div>  
			  <div class="col-6">
			  
			  </div>  
			</div>  
	      </div>
			<div>&nbsp;</div>
		<?php if(!empty($applicant_id)): ?>
	      <div class="appt-status tab-panel-bg p-4" id="one_div">
			<h4 class="text-center">Reference No  - <?php if(!empty($reference_no)): ?> <?php echo e($reference_no); ?> <?php endif; ?></h4>	
			<div class="approvl-wrap progess01">
			  <?php if($applied_date): ?>
			  <div class="aprv-item">
				<div class="day-name"><strong><?php echo e(GetReadableDateFormat($applied_date)); ?></strong></div> 
				 <div class="mark-tick class1"><span>&#10003;</span></div> 
				<div class="aprv-title">Application Applied</div>  
			  </div>
			  <?php endif; ?>

			  <?php if($application_status): ?>
			  <div class="aprv-item">
				<div class="day-name"><strong><?php echo e(GetReadableDateFormat($certificate_generation)); ?></strong></div> 
				 <div class="mark-tick class1"><span>&#10003;</span></div> 
				<div class="aprv-title"><?php echo e(ucfirst($application_status)); ?></div>  
			  </div>
			  <?php endif; ?>
			  
			  <?php if($ad_status=='6'): ?>
			  <div class="aprv-item">
				<div class="day-name"><strong><?php echo e(GetReadableDateFormat($certificate_generation)); ?></strong></div> 
				 <div class="mark-tick class1"><span>&#10003;</span></div> 
				<div class="aprv-title">Certificate Generated</div>  
			  </div>
			  <?php endif; ?>
			  
			  </div>
			  
			  <!-- End Of approvl-wrap Div -->
			 <div class="text-right" style="font-size: 12px; cursor: pointer;"><a class="link-btn linktxt" data-val="1"><span class="headtext">View All</span> <i class="fa fa-caret-down" aria-hidden="true"></i></a></div>
			</div>
			<div class="clearfix"></div>
			<div style="display:none;" id="all_div">
			<?php if(!empty($allStatus)): ?>
			<?php if(count($allStatus)>0): ?>
				  <?php $j=2;?>
				  <?php $__currentLoopData = $allStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				  <div class="appt-status tab-panel-bg p-4">
				  <h4 class="text-center">Reference No  - <?php if(!empty($v['reference_no'])): ?> <?php echo e($v['reference_no']); ?> <?php endif; ?></h4>	
				  <div class="approvl-wrap progess<?php echo e($j); ?>">
				  <?php if($applied_date): ?>
				  <div class="aprv-item">
					<div class="day-name"><strong><?php echo e(GetReadableDateFormat($v['applied_date'])); ?></strong></div> 
					 <div class="mark-tick class<?php echo e($j); ?>"><span>&#10003;</span></div> 
					<div class="aprv-title">Application Applied</div>  
				  </div>
				  <?php endif; ?>

				  <?php if($application_status): ?>
				  <div class="aprv-item">
					<div class="day-name"><strong><?php echo e(GetReadableDateFormat($v['certificate_generation'])); ?></strong></div> 
					 <div class="mark-tick class<?php echo e($j); ?>"><span>&#10003;</span></div> 
					<div class="aprv-title"><?php echo e(ucfirst($v['application_status'])); ?></div>  
				  </div>
				  <?php endif; ?>
				  <?php if($v['ad_status']=='6'): ?>
				  <div class="aprv-item">
					<div class="day-name"><strong><?php echo e(GetReadableDateFormat($v['certificate_generation'])); ?></strong></div> 
					 <div class="mark-tick class<?php echo e($j); ?>"><span>&#10003;</span></div> 
					<div class="aprv-title">Certificate Generated</div>  
				  </div>
				  <?php endif; ?>
				  </div>
				  </div>
				  <?php $j++;?>
				  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			  <?php endif; ?>
			  <?php endif; ?>
			  <div class="text-right" style="font-size: 12px; cursor: pointer;"><a class="link-btn linktxt" data-val="2"><span class="headtext">Show Latest</span> <i class="fa fa-caret-down" aria-hidden="true"></i></a></div>
			</div>
			
			<!-- View all list-->'
			
			<?php endif; ?>
		    <div class="tab-body">
				<div class="home">
			    <div class="row">
				  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn d-inline-flex">
						   <span class="apply-icon"></span><a href="<?php echo e(url('/media/application')); ?>">Apply New<br/>Advertisement</a>
						   <div class="help-txt">Here you can apply for new Advertisement application</div>
					   </div>
					 </div>	
				  </div>
                  <div class="col-md-6 col-12 mt-3 mb-5">
					 <div class="tab-actn-btn my-5">
					   <div class="apply-btn my-apped-btn d-inline-flex">
						   <span class="my-apped-icon"></span><a href="<?php echo e(url('/media/my-applications')); ?>">My<br/> Advertisement</a>
						   <div class="help-txt">All your saved and submitted applications are listed here </div>
					   </div>
					 </div>	
				  </div>				  
				 </div>
			  </div><!-- End Of home Div -->
			</div>
		</div><!-- End Of adver Div -->
	  </div>
		</div>  
	  </div>
	  </div>	 
	 </div>
     
	<script src="<?php echo e(asset('appoinment/js/jQuery.min.v3.4.1.js')); ?>" type="text/javascript"></script>
	<script src="<?php echo e(asset('appoinment/js/bootstrap.min.js')); ?>" type="text/javascript"></script>
	
	
  </body>
</html>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script type="text/javascript">

function showAll(){
	$(".appt-status").show();
}
var appcount = <?php if(!empty($allStatus)){ ?> <?php echo count($allStatus); ?> <?php } ?>
appcount = parseInt(appcount) + parseInt(1);
$(document).ready(function(){	
$(".linktxt").click(function(){
	if($(this).attr("data-val")=='1'){
		$("#all_div").slideToggle('slow');
		$("#one_div").hide();
	}else{
		$("#all_div").hide();
		$("#one_div").slideToggle('slow');
	}
});
$(function(){	 
		 
		//This Function For aprv-item
		var noItem = $('.progess01 .aprv-item').length;
		if(noItem == 1){
          $('.class1').addClass('wdthOne');
		}else if(noItem == 2){
          $('.class1').addClass('wdthTwo');
			  $('.class1').last().removeClass('wdthTwo').addClass('last-progrss');
		}else if(noItem == 3){
		   $('.class1').addClass('wdthThree');
			  $('.class1').last().removeClass('wdthThree').addClass('last-progrss');
		}
		
		if(appcount > 0){
			for(var j=2;j<=appcount; j++){
				var noItem = $('.progess'+j+' .aprv-item').length;
				if(noItem == 1){
				  $('.class'+j).addClass('wdthOne');
				}else if(noItem == 2){
				  $('.class'+j).addClass('wdthTwo');
					  $('.class'+j).last().removeClass('wdthTwo').addClass('last-progrss');
				}else if(noItem == 3){
				   $('.class'+j).addClass('wdthThree');
					  $('.class'+j).last().removeClass('wdthThree').addClass('last-progrss');
				}
			}
		}
		
		
	  });
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/auth/dummy-user-auth/candidate-dashboard.blade.php ENDPATH**/ ?>