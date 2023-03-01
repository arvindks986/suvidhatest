      
      <?php $__env->startSection('title', 'Nomination'); ?>
      <?php $__env->startSection('content'); ?>
      <style type="text/css">
        .error{
          font-size: 12px; 
          color: red;
        }
        .display_none{
          display: none;
        }
        .form_steps p{
          padding: 15px 15px;
        }
        .heading-part1 p{
          padding: 0px !important;
        }
        .fullwidth{
          width: 100%;
          float: left;
        }
        #imagePreview{
          width: 150px;
          height: 150px;
          border: 1px solid #efefef;
        }
        .button-next{
          margin-top: 30px;
        }
        .button-next button{
          float: right;
        }
		
			/*Help Animate CSS*/
  .animate-wrap{position:relative; display: block;}
  .animate-help-text {
	position: absolute; 
	top: 0.85rem; 
	right: 12rem;  
    background-color: #fbfbfb;
    color: #ee577e;
    border: 1px dashed #ee577e;
    padding: 1rem;
    border-radius: 0;
    font-size: 14px;
    box-shadow: 1px 1px 2px #999;
    display: block;
    align-items: center;
    width: auto;
}
  
.animate-icon {
    font-size: 2.5rem;
    position: absolute;
    right: -2.5rem;
	top: 0;
}
    .box {
        align-self: flex-end;
        animation-duration: 3s;
        animation-iteration-count: infinite;
        margin: 0 auto 0 auto;
        transform-origin: bottom;
    } 
   .bounce-1 {
        animation-name: bounce-1;
        animation-timing-function: linear;
    }
    @keyframes  bounce-1 {
        0%   { transform: translateY(0); }
        50%  { transform: translateY(-25px); }
        100% { transform: translateY(0); }
    }
  .bounce-2 {
        animation-name: bounce-2;
        animation-timing-function: linear;
    }
    @keyframes  bounce-2 {
        0%   { transform: translateX(0); }
        50%  { transform: translateX(25px); }
        100% { transform: translateX(0); }
    }	
	
	.dir-lft{right: 0rem;}
	.dir-lft .animate-icon {right: auto; left: -4rem;}
	
	.dir-dwn{bottom: 0rem;}
	.dir-dwn .animate-icon {right: auto; left: 5rem;top: 4rem;}
      </style>
      <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>" id="theme-stylesheet">
      <link rel="stylesheet" href="<?php echo e(asset('admintheme/css/jquery-ui.css')); ?>" id="theme-stylesheet">
	  <link rel="stylesheet" href="<?php echo e(asset('css/custom-dark.css')); ?>" id="theme-stylesheet">
	  
	

	<link rel="stylesheet" href="<?php echo e(asset('admintheme/css/jquery-ui.css')); ?>" id="theme-stylesheet">	
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/bootstrap.min.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-profile.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-dark.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/font-awesome.min.css')); ?> " type="text/css">
	<link rel="stylesheet" href="<?php echo e(asset('appoinment/fonts.css')); ?> " type="text/css">
	  
	<main class="pt-3 pb-5 pl-5 pr-5">
	
	<?php if(count($errors->all())>0 || session('flash-message')): ?>
        <section class="mt-3">
       <div class="container">            
               <?php if(count($errors->all())>0): ?>
               <div class="alert alert-danger">
                 <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 <p class="text-left">
				 <?php echo $iterate_error; ?>

				 </p>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
               </div>
               <?php endif; ?>
               <?php if(session('flash-message')): ?>
               <div class="alert alert-success"> <?php echo e(session('flash-message')); ?></div>
               <?php endif; ?>    
       </div>
	   </section>
     <?php endif; ?>
	 
	 <div class="container">
		 <div class="step-wrap mt-4">
			 <ul>
			   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step1')); ?></span></li>
			   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step2')); ?></span></li>
			   <li class="step-success"><b>&#10004;</b><span><?php echo e(__('step1.step3')); ?></span></li>
			   <li class="step-current"><b>&#10004;</b><span><?php echo e(__('step1.step4')); ?></span></li>
			   <li class=""><b>&#10004;</b><span><?php echo e(__('step1.step5')); ?></span></li>
			   <li class=""><b>&#10004;</b><span><?php echo e(__('step1.step6')); ?></span></li>
			   <li class=""><b>&#10004;</b><span><?php echo e(__('step1.step7')); ?></span></li>
			 </ul>
		 </div>
		</div>
	
     <div class="container-fluid">
        <div class="card">
        <div class="card-header text-center">
	    
		<div class="row">
			<div class="fullwidth" style="float: left;width: 100%;">                
                <?php if(isset($reference_id) && isset($href_download_application)): ?>
                <div class="col-md-5 float-right">
                  <ul class="list-inline float-right">
                    <li class="list-inline-item text-right"><?php echo e(__('election_details.ref')); ?>: <b style="text-decoration: underline;"><?php echo e($reference_id); ?></b></li>
                    <li class="list-inline-item text-right"><a href="<?php echo $href_download_application; ?>" class="btn btn-primary" target="_blank"><?php echo e(__('election_details.down')); ?></a></li>
                  </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
	  
	  
         <div class="">
          <h4><?php echo e(__('step3.form2b')); ?></h4>
          <div>(<?php echo e(__('step3.rule4')); ?>)</div>
          <div><?php echo e(__('step3.nomp')); ?></div>
          <div><?php echo e(__('step3.nommessage')); ?> <span class="">(<?php echo e($st_name); ?>)</span></div>
        </div>
      </div>
      <div class="card-body">
	  <div class="part-3">
		<h3 class="part-title mt-2 mb-5"><span><?php echo e(__('part3.Part3')); ?></span></h3>  
         <form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
		  <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"/>
		  <input type="hidden" name="nomination_id" value="<?php echo e($nomination_id); ?>"/>
		  
							  
	       <fieldset class="py-4 px-5 mt-2 mb-4">
            <legend><?php echo e(__('part3.DECLARATION')); ?></legend>
			<p> <?php echo e(__('part3.i')); ?><b> 
			<?php if($recognized_party==0 or $recognized_party==1 ): ?> 
			<?php echo e(__('step3.partn')); ?>  
			<?php elseif($recognized_party==3 ): ?> <?php echo e(__('step3.partn')); ?> , <?php echo e(__('step3.part2')); ?>  
			<?php else: ?> <?php echo e(__('step3.part2')); ?> 
			<?php endif; ?> </b>
			
			<?php echo e(__('part3.assent')); ?>—</p>	
			<div class="info-checkbox">
			 (a)&nbsp; 
				<div class="custom-control custom-checkbox customCheckBtn mr-2">
					<input type="checkbox" class="custom-control-input" id="customCheck" name="example1" checked disabled>
					<label class="custom-control-label" for="customCheck"></label>
				</div>
				<p><?php echo e(__('part3.citi')); ?></p> 
			</div>
			<div class="row mt-2 mb-4 align-items-center">
			  <div class="col-sm-3 col-12 pr-0">(b)&nbsp; <?php echo e(__('part3.age')); ?></div>
			  <div class="col-sm-3 col-12 pl-0">
				<input type="number" name="age" placeholder="Enter Age Of Years" min="25" class="form-control" value="<?php echo e($age); ?>">
			  </div>
			</div>   
            <div class="row align-items-center">
			<?php if($recognized_party==0 or $recognized_party=='1' or $recognized_party=='' or $recognized_party==3): ?> 
			<?php 
			$dis1='';
			$rec='';
			$rec2='';	
			$d1='';
			$d2='';
			
			
			if($recognized_type==0 or $recognized_type=='1' or $recognized_type=='') {	
			$rec='block';
			$rec2='none';	
			$d2='disabled';
			$d1='';
			}
			if($recognized_type==2){
			$rec='none';
			$rec2='block';	
			$d2='';
			$d1='disabled';
			}
			
			?>	
			
		
			<div class="col-sm-12 col-12">
				<div class="d-flex align-items-center my-3">
                 <p class="mr-2">C(i)&nbsp; <?php echo e(__('part3.rec')); ?></p>
				  <div class="custom-control custom-radio customRadioBtn mr-3" onclick="return national();">
					<input type="radio" class="custom-control-input" id="nParty" name="recognized_type" value="1" <?php if($recognized_type==1 or $recognized_type==''): ?> <?php echo e('checked'); ?><?php endif; ?>>
					<label class="custom-control-label" for="nParty"><?php echo e(__('part3.nat')); ?></label>
				  </div>
				  <div class="custom-control custom-radio customRadioBtn mr-3" onclick="return state();">
					<input type="radio" class="custom-control-input" id="nonRecg" name="recognized_type" value="2" <?php if($recognized_type==2): ?> <?php echo e('checked'); ?><?php endif; ?>>
					<label class="custom-control-label" for="nonRecg"><?php echo e(__('part3.stp')); ?></label>
				  </div>	
				</div>
			 </div>	
			 
			<div class="col-sm-12">
			<div class="d-flex my-3">
			  <div class="lbl-mandry mt-4 mr-5"><?php echo e(__('part3.pname')); ?></div>	
			  <div class="mt-4" style="width:70%;">
			  <select name="party_id" class="form-control" id="national" style="display:<?php echo e($rec); ?>" onchange="getPartyVal(this.value);" <?php echo e($d1); ?>>
				<option value="">-- <?php echo e(__('part3.ps')); ?> --</option>    
				<?php $__currentLoopData = $parties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_party): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				   <?php if($party_id == $iterate_party['party_id']): ?>
				   <option value="<?php echo e($iterate_party['party_id']); ?>" selected="selected"><?php echo e($iterate_party['name']); ?></option>
				   <?php else: ?> 
				   <option value="<?php echo e($iterate_party['party_id']); ?>"><?php echo e($iterate_party['name']); ?></option>
				   <?php endif; ?>
				 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			  </select>
			
			 <select name="party_id" class="form-control" id="state" style="display:<?php echo e($rec2); ?>" onchange="getPartyVal(this.value);" <?php echo e($d2); ?>>
				<option value="">-- <?php echo e(__('part3.ps')); ?> --</option>    
				<?php $__currentLoopData = $parties_state; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_party_state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				   <?php if($party_id == $iterate_party_state['party_id_state']): ?>
				   <option value="<?php echo e($iterate_party_state['party_id_state']); ?>" selected="selected"><?php echo e($iterate_party_state['name_party_id_state']); ?></option>
				   <?php else: ?> 
				   <option value="<?php echo e($iterate_party_state['party_id_state']); ?>"> <?php echo e($iterate_party_state['name_party_id_state']); ?></option>
				   <?php endif; ?>
				 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			  </select>	
			<div><small class="text-black-50"><?php echo e(__('part3.symbol')); ?></small></div></div>		
				<div class="animate-wrap party_wrap" style="width:30%" style="display:<?php echo (!empty($setup_partyDatat['party_id']))?'none':'block';?>">
					<div class="animate-help-text dir-lft">
				    <div class="help-text"><?php echo e(__('messages.arparty')); ?></div>
					<div class="animate-icon">
					      <div class="box bounce-2"><i class="fa fa-hand-o-left" aria-hidden="true"></i></div>
					</div>
				  </div>
				</div>
			</div>
			</div>
			
			
			<?php if($recognized_party!=3): ?>
			<!-- Need to strike part 3(II) -->
			<div class="col-sm-12 col-12">
			    <div class="d-flex align-items-center my-3">
				  <div> <hr style="width: 64%; height: -31px; display: block; border-top:2px solid #000; position: absolute; margin-top: 10px;">c(ii)&nbsp;</hr></div>	
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input pParty" >
					<label class="custom-control-label" for="first"  ><?php echo e(__('part3.recp')); ?></label>
				  </div> 
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input"> 
					<label class="custom-control-label" for="second"  ><?php echo e(__('part3.ind')); ?></label>
				  </div>				  
				  <div class="mr-3">			  
				  </div>				  
				</div>	
			  </div>	
			<!--End Need to strike part 3(II) -->
			<?php endif; ?>
			
			
			
			
			
			
			
			<?php endif; ?>
			
			
			<?php if($recognized_party==2 or $recognized_party==3): ?>
			<?php 
			$dis1='';
			$dis2='';
			if($unrecognized_type==1 or $unrecognized_type==0 or $unrecognized_type==''){
			$dis1='block';
			$dis2='none';	
			}
			
			if($unrecognized_type==2){
			$dis1='none';
			$dis2='block';	
			}
			?>
			<?php if($recognized_party!=3): ?>
			<!-- Need to strike part 3(I) -->
			<div class="col-sm-12 col-12">
				<div class="d-flex align-items-center my-3">
                 <p class="mr-2"><hr style="width:82%; height: -31px; display: block; border-top:2px solid #000; position: absolute; margin-top: 0px;"> C(i)&nbsp; <?php echo e(__('part3.rec')); ?> </hr></p>
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input">
					<label class="custom-control-label" for="nonRecg"><?php echo e(__('part3.nat')); ?></label>
				  </div>	
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input">
					<label class="custom-control-label" for="nonRecg"><?php echo e(__('part3.stp')); ?></label>
				  </div>	
				</div>
			 </div>	
			 <!-- Need to strike part 3(I) -->
			 <?php endif; ?>
			
			<div class="col-sm-12 col-12">
			    <div class="d-flex align-items-center my-3">
				  <div>c(ii)&nbsp;</div>	
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input pParty" id="first" name="unrecognized_type" value="1" 
					<?php if($unrecognized_type==1 or $unrecognized_type==''): ?> <?php echo e('checked'); ?> <?php endif; ?>>
					<label class="custom-control-label" for="first"   onclick="return shows('1');"><?php echo e(__('part3.recp')); ?></label>
				  </div> 
				  <div class="custom-control custom-radio customRadioBtn mr-3">
					<input type="radio" class="custom-control-input" id="second" name="unrecognized_type" value="2" <?php if($unrecognized_type==2): ?> <?php echo e('checked'); ?> <?php endif; ?>> 
					<label class="custom-control-label" for="second"  onclick="return shows('2');"><?php echo e(__('part3.ind')); ?></label>
				  </div>				  
				  <div class="mr-3">
				  
				  <div id="setupDiv" style="display:<?php echo $dis1; ?>" >
				   <select name="party_id2" class="form-control js-example-basic-single" title="<?php echo e(__('part3.ps')); ?>" id="setup" style="display:<?php echo $dis1; ?>">
				   <option value=""></option>     
					<?php $__currentLoopData = $setup_party; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setup_partyDatat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					   <?php if($party_id2 == $setup_partyDatat['party_id'] and $setup_partyDatat['party_id']!=743): ?>
					   <option value="<?php echo e($setup_partyDatat['party_id']); ?>" selected="selected"><?php echo e($setup_partyDatat['name']); ?></option>
					   <?php else: ?> selected
					   <option value="<?php echo e($setup_partyDatat['party_id']); ?>"> <?php echo e($setup_partyDatat['name']); ?> </option>
					   <?php endif; ?>
					 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				  </select>	
				  </div>
				
				  <select name="party_id2" class="form-control" id="setup_independent" style="display:<?php echo $dis2; ?>;" disabled>
					<?php $__currentLoopData = $setup_party; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setup_partyDatat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					   <?php if($setup_partyDatat['PARTYTYPE']=='Z'): ?>
					   <option value="<?php echo e($setup_partyDatat['party_id']); ?>" selected=""><?php echo e($setup_partyDatat['name']); ?></option>
					   <?php endif; ?>
					 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				  </select>				  
				  </div>				  
				</div>	
			  </div>	
              <div class="col-12">
                <div class="form-group mt-2 mb-2">
                  <label for="" class="lbl-mandry"><?php echo e(__('part3.spre')); ?></label>
				  <div class="d-flex">
					<div class="col-sm-4 col-12">1.<input list="sym1" type="text" name="suggest_symbol_1"  id="suggest_symbol_1" class="form-control" value="<?php echo e($suggest_symbol_1); ?>" onmouseover="return getSymbol(1);" onkeypress="return getSymbol(1);" >
					  <datalist id="sym1">
						<option value="Edge">
					  </datalist>
					  </div>
					  <div class="col-sm-4 col-12">2. <input list="sym2" type="text" name="suggest_symbol_2" id="suggest_symbol_2" class="form-control" value="<?php echo e($suggest_symbol_2); ?>" onmouseover="return getSymbol(2);" onkeypress="return getSymbol(2);">
					  <datalist id="sym2">
						<option value="Edge">
					  </datalist>
					  </div>
					    <div class="col-sm-4 col-12">3. <input list="sym3" type="text" name="suggest_symbol_3" id="suggest_symbol_3" class="form-control"  value="<?php echo e($suggest_symbol_3); ?>" onmouseover="return getSymbol(3);" onkeypress="return getSymbol(3);">
						<datalist id="sym3">
						<option value="Edge">
					  </datalist>
						</div>
				  </div>	
                </div>
              </div>
			  
			  
			  
			   <?php if($dis2=='block'){ ?>
				<input type="hidden" name="party_id2" id='cstpid' value="743">
			  <?php } ?>
			   <?php if($recognized_party=='2'){ ?>
				<input type="hidden" name="party_id"  value="0">
			  <?php } ?>
			  
			  
			  
			 <?php endif; ?> 
			 
					  
					  
					  
			  <div class="col-sm-12 col-12">
                <div class="d-flex align-items-center my-3">
                  <div class="lbl-mandry mr-4">(d)&nbsp; <?php echo e(__('part3.lang')); ?></div>
				<div style="width: 12.50%;">
                  <input type="text" list="language" id="langData" name="language" class="form-control alphaonly" value="<?php echo e($language); ?>" onmouseover="return getLangauge();" style="width: 120%;" autocomplete="off">
				</div>		
				<datalist id="language">
						<option >
					  </datalist>
                </div>
              </div>
		   <div class="col-sm-12 col-12">		
			 <div class="info-checkbox mb-3">
			(e)	 
			    <div class="custom-control custom-checkbox customCheckBtn">
					<input type="checkbox" class="custom-control-input" id="customCheck01" name="" checked disabled>
					<label class="custom-control-label" for="customCheck01"></label>
				</div>
				 
				 <p><?php echo e(__('part3.dec')); ?></p>
				
			</div>
		   </div>
		   <div class="col-sm-12 col-12">
			<div class="one-param">
			
			
			<?php if(isset($category)): ?>
			<?php if($category=='general'): ?>	
			<div class="col-sm-12 col-12">		
			 <div class="info-checkbox mb-3">			 
			    <div class="custom-control custom-checkbox customCheckBtn">
					<input type="checkbox" class="custom-control-input" name="not_applicable" id="customCheck013" checked disabled
					<?php if($not_applicable=='on'): ?><?php echo e('checked'); ?><?php endif; ?>>
					<label class="custom-control-label" for="customCheck013" style="background: none; position: static; margin-top: -15px; margin-left: 18px;"><?php echo e(__('part3.np')); ?>,</label>
				</div>
				
				
				
			</div>
		   </div>
			<input type="hidden" name="part3_cast_state" id="two" value="">
			<input type="hidden" name="part3_address" id="three" value="">
			
			
			
			
			<!-- Strike COntent -->
			
			<span>
			   *<?php echo e(__('part3.further')); ?> 
			   <select name="category" class="form-control" disabled>
			  <option value=""><?php echo e(__('part3.Select')); ?></option>
			  <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			   <?php if($category == $iterate_category['id']): ?>
			   <option value="<?php echo e($iterate_category['id']); ?>" selected="selected"><?php echo e($iterate_category['name']); ?></option>
			   <?php else: ?>
			   <option value="<?php echo e($iterate_category['id']); ?>"><?php echo e($iterate_category['name']); ?></option>
			   <?php endif; ?>
			   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</select>
			    <hr style="width:65%; height: -31px; display: block; border-top:2px solid #000; position: absolute; margin-top: 15px;">  <?php echo e(__('part3.caste')); ?> </hr>
				<select   class="form-control" disabled>
              <option value="">-- <?php echo e(__('part3.Select')); ?> --</option>
             
           </select>
			<?php echo e(__('part3.rel')); ?> <input type="text" class="form-control" readonly><?php echo e(__('part3.area')); ?>.
			 </span>  
			<!--End Strike Content -->
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		   <?php endif; ?>
		   <?php endif; ?>

		   
		   <?php $chk=''; ?>	
			<?php if($not_applicable=='on'): ?>
			<?php $chk='none'; ?>
			<?php else: ?>
			<?php $chk='black'; ?>	
			<?php endif; ?>
			
			<?php if(isset($category)): ?>
			<?php if($category!='general'): ?>
				
			
			
			
			
			
			<span id="TTT" style="display:<?php echo $chk; ?>;">
			   *<?php echo e(__('part3.further')); ?> 
			  <select name="category" class="form-control" disabled>
			  <option value=""><?php echo e(__('part3.Select')); ?></option>
			  <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			   <?php if($category == $iterate_category['id']): ?>
			   <option value="<?php echo e($iterate_category['id']); ?>" selected="selected"><?php echo e($iterate_category['name']); ?></option>
			   <?php else: ?>
			   <option value="<?php echo e($iterate_category['id']); ?>"><?php echo e($iterate_category['name']); ?></option>
			   <?php endif; ?>
			   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</select>
			  <?php echo e(__('part3.caste')); ?>

				<select name="part3_cast_state" class="form-control" id="part3_cast_state">
             <option value="">-- <?php echo e(__('part3.Select')); ?> --</option>
             <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
               <?php if($part3_cast_state == $iterate_state['st_code']): ?>
               <option value="<?php echo e($iterate_state['st_code']); ?>" selected="selected"><?php echo e($iterate_state['st_name']); ?></option>
               <?php else: ?> 
               <option value="<?php echo e($iterate_state['st_code']); ?>"> <?php echo e($iterate_state['st_name']); ?></option>
               <?php endif; ?>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
           </select>
			<?php echo e(__('part3.rel')); ?> <input type="text" name="part3_address" id="part3_address" class="form-control" value="<?php echo e($part3_address); ?>"><?php echo e(__('part3.area')); ?>.
			 </span>  
			<?php endif; ?>
			<?php endif; ?>
			   <br/>
           
				<?php echo e(__('part3.also')); ?>

			<select name="part3_legislative_state" class="form-control" id="part3_legislative_state" disabled>
             <option value="">-- <?php echo e(__('part3.Select')); ?> --</option>
             <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iterate_state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
               <?php if( $iterate_state['st_code'] == $st_code): ?>
               <option value="<?php echo e($iterate_state['st_code']); ?>" selected="selected"><?php echo e($iterate_state['st_name']); ?></option>
               <?php else: ?> 
               <option value="<?php echo e($iterate_state['st_code']); ?>"> <?php echo e($iterate_state['st_name']); ?></option>
               <?php endif; ?>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
           </select><?php echo e(__('part3.aca')); ?>

		   
		   <input type="hidden" name="part3_legislative_state" value="<?php echo e($st_code); ?>" id="part3_legislative_state" >
			
                              
				
			</div>	
		   </div>		
			 
            </div>
          </fieldset>
		  
			<input type="hidden" name="category" id="one" value="<?php echo e($category); ?>">

				
          <div class="row my-3">
            <div class="col-sm-6 col-12"><strong><?php echo e(__('part3.date')); ?>:</strong> <span>
			<input type="hidden" name="part3_date" id="part3_date" value="<?php echo e($part3_date); ?>" readonly="readonly">
			<input type="text" name="part3_date" id="part3_date" value="<?php echo e($part3_date); ?>" readonly="readonly" disabled>
			</span></div>
            <div class="col-sm-6 col-12"></div>
          </div>
	
      <div class="nomination-note"> <small>*<?php echo e(__('part3.bm1')); ?>.</small> 
        <small>** <?php echo e(__('part3.bm2')); ?>.</small>
        <small><?php echo e(__('part3.bm3')); ?></small> 
	</div>
	
	
	<div class="card-footer">
			<div class="row align-items-center">
			  <div class="col-sm-6 col-12"> <a href="<?php echo e($href_back); ?>" id="" class="btn btn-lg btn-secondary font-big"><?php echo e(__('step1.Back')); ?></a> </div>
			  <div class="col-sm-6 col-12">
				<div class="apt-btn text-right"> 
				
				<a href="<?php echo url('/'); ?>/dashboard-nomination-new" class="btn btn-lg font-big dark-pink-btn"><?php echo e(__('step1.Cancel')); ?></a> 
			   &nbsp;	
				
				<?php if($recognized_party=='2'){ ?>
				 <button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return chkCat();"><?php echo e(__('step1.Save_Next')); ?></button>
				<?php } else { ?> 
				  <button type="submit" class="btn btn-lg font-big dark-purple-btn pop-actn" onclick="return chkCat();"><?php echo e(__('step1.Save_Next')); ?></button>
				<?php }  ?> 
				
				</div>
			  </div>
			</div>
		  </div>
	
   </form>
	</div><!-- End Of part-1 Div -->	  
         
	  </div><!-- End Of card-body Div -->  
      
		  
	  </div>
		</div><!-- End Of container-fluid Div -->	  
	</main>
	
     <?php $__env->stopSection(); ?>

     <?php $__env->startSection('script'); ?>
    <script type="text/javascript" src="<?php echo e(asset('admintheme/js/jquery-ui.js')); ?>"></script>
	<script type="text/javascript" src="<?php echo e(asset('appoinment/js/jQuery.min.v3.4.1.js')); ?>"></script>
	<script type="text/javascript" src="<?php echo e(asset('appoinment/js/bootstrap.min.js')); ?>"></script>
	<script type="text/javascript" src="<?php echo e(asset('appoinment/js/owl.carousel.js')); ?>"></script>
	
	<!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
   <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>-->
    <link rel="stylesheet" href="<?php echo e(asset('appoinment/select-search/select2.min.css')); ?>" id="theme-stylesheet">	
	<script type="text/javascript" src="<?php echo e(asset('appoinment/select-search/select2.min.js')); ?>"></script>
	
	 <script type="text/javascript">
		var searh = jQuery.noConflict();	
		searh(document).ready(function() {
			searh('.js-example-basic-single').select2({
			placeholder: "<?php echo e(__('part3.ps')); ?>",
			allowClear: true
		});
		});
		function chkCat(){ 
			if("<?php echo $category!='general' ?>"){
				var part3_cast_state = $("#part3_cast_state").val();
				var part3_address = $("#part3_address").val();
				
				if(part3_cast_state==''){
				alert("<?php echo __('messages.tribcaststate') ?>");
				$("#part3_cast_state").focus();
				return false;
				}
				if(part3_address==''){
				alert("<?php echo __('messages.tribcaststatearea') ?>");
				$("#part3_address").focus();
				return false;
				}
			}	
		}
		
		
		function getPartyVal(partyid){
			if(partyid !=''){
				$(".party_wrap").hide();
			}else{
				$(".party_wrap").show();
			}
		}
		
		function getLangauge(){ 
		   
		   $("#langData").val();
		   $("#language").val();
		   $("#langData").empty();
		   $("#language").empty();
		
		   var html = [];
			
			var lang = ['Hindi', 'Urdu',  'Marathi',  'Gujarati',  'Bengali',  'Punjabi',  'Malayalam',  'Kannada',  'Assamese',  'Bodo',  'Dogri',  'Kashmiri',  'Konkani',  'Maithili',  'Manipuri',  'MeeteiMayek',  'Nepali',  'OlChiki',  'Oriya',  'Sanskrit',  'Santali',  'SindhiDev',  'Tamil', 'Telugu' ];
			
			for (var i=0; i<lang.length;i++){
				html.push('<option>' + lang[i] + '</option>');
			}
			
			$('#language').append(html);
						
			 var html = [];
			
			   
		}
	 
	 
		function getSymbol(id){ 
			$.ajax({
				url: "<?php echo url('nomination/get-symbol'); ?>",
				type: 'GET',
				dataType: 'json',        
				success: function(json) { 
				  $('#sym1').html('');
				    if(id==1){
					  
					  var s2=$("#suggest_symbol_2").val();
					  var s3=$("#suggest_symbol_3").val();
					  	
					  for(i=0; i<json.length; i++)
					  {	  
						 var datat = json[i];
						 if(s2!=datat && s3!=datat){
						   $('#sym1').append('<option value="'+datat+'">');
						 }
					  }
					  json=[];
					}
					if(id==2){ 
					  var s1=$("#suggest_symbol_1").val();
					  var s3=$("#suggest_symbol_3").val();	
					
					  $('#sym2').html('');
					  for(i=0; i<json.length; i++)
					  {	  
						 var datat2 = json[i];
						  if(s1!=datat2 && s3!=datat2){
						    $('#sym2').append('<option value="'+datat2+'">');
						  }
					  }
					  json=[];
					}
					if(id==3){
					  var s1=$("#suggest_symbol_1").val();
					  var s2=$("#suggest_symbol_2").val();		
					  $('#sym3').html('');	
					  for(i=0; i<json.length; i++)
					  {	  
						 var datat3 = json[i];
						  if(s1!=datat3 && s2!=datat3){
						   $('#sym3').append('<option value="'+datat3+'">');
						  }  
					  }
					  json=[];
					}
				},
				error: function(data) {
				  console.log(data);	
				  var errors = data.responseJSON;
				  console.log(errors);
			}
		});	
		}
	 
	 
	 
		$( document ).ready(function() {
						$( ".alphaonly" ).keypress(function(e) { 
							 var charCode = e.keyCode;
							if(charCode!=32){
							 if (charCode > 31 && (charCode < 65 || charCode > 90) && (charCode < 97 || charCode > 122)) {
								return false;
							 }
							}
							return true;	
						});
					});
		</script>
	 
	<script type="text/javascript">
		function chkParty(){
		 var pp = $("#setup").val();	
		 if(pp==''){
			alert("Please select party");
			return false;	
		 }	
		}
	
	  function national(){
		 $("#national").show(); 
		 $("#state").hide(); 
		 $("#state").prop("disabled", true);
		 $("#national").prop("disabled", false);
	  }	
	   function state(){
		 $("#national").hide(); 
		 $("#state").show(); 
		 $("#state").prop("disabled", false);
		 $("#national").prop("disabled", true);
	  }	
		
		
	  function shohide(){
		
		if($('input[name="not_applicable"]:checked').length > 0 ){
			$("#TTT").hide();  
			$("#one").prop("disabled", false);
			$("#two").prop("disabled", false);
			$("#three").prop("disabled", false);
		} else {
			$("#TTT").show();  
			$("#one").prop("disabled", true);
			$("#two").prop("disabled", true);
			$("#three").prop("disabled", true);
		}
	  } 
	
	  $(function(){
		  $('.pParty').on('click',function(){
			 $('.selectParty').fadeIn(500); 
		  });
		  
	  });
	  
	  function shows(nu){ 
		
		if(nu==1){
		 $("#setup").show();	
		 $("#setupDiv").show();	
		 $("#setup_independent").hide();	
		 $("#setup_independent").prop("disabled", true);	
		 $("#cstpid").prop("disabled", true);	
		}  
		if(nu==2){
		 $("#setup").hide();	
		 $("#setupDiv").hide();	
		 $("#setup_independent").show();	
		 $("#setup_independent").prop("disabled", false);	
		 $("#cstpid").prop("disabled", false);	
		}  
	  }
	  
      $(document).ready(function(){ 
       
       // if($('#breadcrumb').length){
       //   var breadcrumb = '';
       //   $.each(<?php echo json_encode($breadcrumbs); ?>,function(index, object){
       //    breadcrumb += "<li><a href='"+object.href+"'>"+object.name+"</a></li>";
       //  });
       //   $('#breadcrumb').html(breadcrumb);
       // }

       $('#part3_date').datepicker({
        dateFormat: 'dd-mm-yy'
       });

     });
   </script>
   <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/nomination/apply-nomination-step-4.blade.php ENDPATH**/ ?>