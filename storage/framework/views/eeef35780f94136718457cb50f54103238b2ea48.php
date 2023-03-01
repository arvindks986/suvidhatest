<?php $__env->startSection('title', 'Affidavit Cadidate Details'); ?> <?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/custom-dark.css')); ?> " type="text/css" />
<link rel="stylesheet" href="<?php echo e(asset('appoinment/css/bootstrap-multiselect.css')); ?> " type="text/css" />
<style type="text/css">
.affidavit_nav .step-current a,.affidavit_nav .step-success a{
    color:#fff!important;
}
.affidavit_nav a{
    color:#999!important;
}
.error {
    font-size: 12px;
    color: red;
}
.step-wrap.mt-4 ul li {
    margin-bottom: 21px;
}
.more-less {
    float: right;
    color: #212121;
}
.width100{
    width: 100px !important;
}
.err{
    white-space: pre;
    color: red;
    font-size: 11px;
    font-weight: 600;
}
.accordion_head {
    font-size: 20px;
    padding: 8px 15px 1px;
    background-color: #e91e63;
    color: white;
    cursor: pointer;                
    margin: 5px 0 10px 0;               
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 4px 4px -2px rgba(0, 0, 0, 0.5);          
}
.accordion_head .lefts{
    width: calc(100% - 55px);
    float: left;
}
 .accordion_head .rights{
    width: 50px;
    float: right;
}
.accordion_body {
    width: 100%;
    padding: 1em;
    box-shadow: 0 4px 4px -2px rgba(0, 0, 0, 0.5);
    margin-top: -10px;
    background: #fafafa;
    border: #e9e9e9 solid 1px;
}            
.plusminus {
  float: right;
  font-size: 30px;
  margin-top: -5px;
}
.purple {
    background-color: #9b59b6;
}
.purpleTable th{
    background-color: #9b59b6!important;
    color: #ffffff;
}
.nextBtn, button.nextBtn {
    border: 2px solid #9b59b6;
    padding: 0.65em 1.2em;
    border-radius: 2.5em;
    cursor: pointer;
    min-width: 131px;
    text-align: center;
    transition: all 0.25s;
    margin: 1em auto;
    box-sizing: border-box;               
    display: block;
    font-weight: 500;
    color: #9b59b6;
    outline: none;
    white-space: nowrap;
}
.nextBtn:hover , button.nextBtn:hover {
    background-color: #9b59b6;
    color: white;
    outline: none;
    box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
}  

.cencelBtn, button.cencelBtn {
    min-width: 131px;
    text-align: center;
    border: 2px solid #dc3545;
    padding: 0.65em 1.2em;
    border-radius: 2.5em;
    cursor: pointer;
    transition: all 0.25s;
    margin: 1em auto;
    box-sizing: border-box;               
    display: block;
    font-weight: 500;
    outline: none;
    white-space: nowrap;
    color: #dc3545;
    text-decoration: none!important;
}
.cencelBtn:hover , button.cencelBtn:hover {
    background-color: #dc3545;
    color: white;
    outline: none;
    box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
}  

.backBtn, button.backBtn {
    min-width: 131px;
    text-align: center;
    border: 2px solid #868e96;
    padding: 0.65em 1.2em;
    border-radius: 2.5em;
    cursor: pointer;
    transition: all 0.25s;
    margin: 1em auto;
    box-sizing: border-box;               
    display: block;
    font-weight: 400;
    outline: none;
    white-space: nowrap;
    text-decoration: none;
    color:#868e96;
}
.backBtn:hover , button.backBtn:hover {
    background-color: #868e96;
    color: white;
    outline: none;
    text-decoration: none;
    box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
} 
.footerSection{
    width: 100%;
    background: transparent!important;
}
.main_heading {
    position: relative;
    font-size: 1.50rem;
    font-weight: 600;
    margin-top: 12px;
    margin-bottom: 10px;
    text-align: center;
    color: #101010;
    padding-bottom: 7px;
}
.main_heading::before {
    background: #d0d0d0;
    bottom: -2px;
    content: "";
    height: 1px;
    left: 50%;
    position: absolute;
    transform: translateX(-50%);
    width: 200px;
}
.main_heading::after {
    background: #ed457e;
    bottom: -3px;
    content: "";
    height: 3px;
    left: 50%;
    position: absolute;
    transform: translateX(-50%);
    width: 50px;
}
.modal-dialog .close, .modal-content button:hover {
    opacity: 1;
    color: #fff;                
    box-shadow: none;
    outline: 0;
}
.modal button.close {
    background-color: #f0587e;
    padding: 8px 16px;
    border: none;
    font-size: 20px;
    border: none;
    border: 1px solid #f0587e;
}
.step-wrap {
    text-align: center;
}
.step-wrap>ul>li {      
    border-radius: 25px;            
    padding: 0.15rem 1.05rem 0.15rem 0.18rem;
}
.step-wrap>ul>li>span {
    display: inline-block;
    vertical-align: middle;
    width: 60px!important;
    color: #999;
    font-size: 0.80rem!important;
    text-align: center;
    line-height: 0.95rem!important;
}
.step-wrap>ul>li>b {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    font-size: 1.5rem;
    text-align: center;
    background-color: #ffffff;
    color: #e8e8e8;
    display: inline-block;
    line-height: 35px;
    vertical-align: middle;
    margin-right: 0.25rem;
    margin-left: 0; 
}
</style>
<main role="main" class="inner cover mb-3">
<section>
<div class="container">
    <?php if(session('flash-message')): ?>
    <div class="alert alert-success mt-4"><?php echo e(session('flash-message')); ?></div>
    <?php endif; ?> <?php if($message = Session::get('Init')): ?>
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong><?php echo e($message); ?></strong>
    </div>
    <?php endif; ?>
</div>
</section>

<?php if(Auth::user()->role_id == '19'){
	$menu_action = 'roac/';
}else{
	$menu_action = '';
} ?>



<div class="container-fluid">
<div class="step-wrap mt-4">
            <ul class="affidavit_nav">
                <li class="step-success"><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'affidavitdashboard')); ?>"><?php echo e(Lang::get('affidavit.initial_details')); ?></a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'affidavit/candidatedetails')); ?>"><?php echo e(Lang::get('affidavit.candidate_details')); ?></a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'affidavit/pending-criminal-cases')); ?>"><?php echo e(Lang::get('affidavit.court_cases')); ?></a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'Affidavit/MovableAssets')); ?>"><?php echo e(Lang::get('affidavit.movable_assets')); ?></a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'immovable-assets')); ?>"><?php echo e(Lang::get('affidavit.immovable_assets')); ?></a></span></li>
                <li class="step-success"><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'liabilities')); ?>"><?php echo e(Lang::get('affidavit.liabilities')); ?></a></span></li>
                <li class="step-current"><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'Profession')); ?>"><?php echo e(Lang::get('affidavit.profession')); ?></a></span></li>
                <li class=""><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'education')); ?>"><?php echo e(Lang::get('affidavit.education')); ?></a></span></li>
                <li class=""><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'preview')); ?>"><?php echo e(Lang::get('affidavit.preview_finalize')); ?></a></span></li>
                <li class=""><b>&#10004;</b><span><a href="<?php echo e(url($menu_action.'part-a-detailed-report')); ?>"><?php echo e(Lang::get('affidavit.reports')); ?></a></span></li>
            </ul>
        </div>
</div>
<section>
<div class="container p-0">
    <div class="row">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="main_heading"><?php echo e(Lang::get('affidavit.profession')); ?></h4>
                    </div>
                </div>
                <div class="card-body">
                    <!--  Self Spouse -->
                    <div class="accordion_head"><?php echo e(Lang::get('affidavit.occupation_of_self_and_spouse')); ?><span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                        <?php if(!empty($data)): ?>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $self_spouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>                                            
                            <?php if($self_spouse->relation_type_code==1 || $self_spouse->relation_type_code==2 || $self_spouse->relation_type_code==3 || $self_spouse->relation_type_code==4 ): ?>
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            <?php echo e($self_spouse->relation_type); ?> : <?php echo e($self_spouse->name); ?>

                            </h6>
                            <div class="table-responsive">
                            <table id="self_spouse<?php echo e($self_spouse->relation_type_code); ?>" class="table table-striped table-bordered table-hover purpleTable" >
                            <thead>
                                <tr>
                                    <th><?php echo e(Lang::get('affidavit.occupation')); ?></th>
                                    <th><?php echo e(Lang::get('affidavit.action')); ?></th>          
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($self_spouse_details)): ?>
                                    <?php $__currentLoopData = $self_spouse_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ss_row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($self_spouse->relation_type_code==$ss_row->relation_type_code): ?>
                                    <tr id="trss<?php echo e($ss_row->id); ?>">                     
                                        <td><?php echo e($ss_row->occupation); ?></td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get('affidavit.edit')); ?>" onclick="javascript:edit_self_spouse(<?php echo e($ss_row->id); ?>)"
                                            data-id="<?php echo e($ss_row->id); ?>"
                                            data-occupation="<?php echo e($ss_row->occupation); ?>"
                                            data-relation_type="<?php echo e($ss_row->relation_type); ?>"
                                            data-name="<?php echo e($ss_row->name); ?>"
                                            data-relation_type_id="<?php echo e($ss_row->relation_type_code); ?>"
                                            data-candidate_id="<?php echo e($ss_row->candidate_id); ?>"
                                            id="edit_self_spouse<?php echo e($ss_row->id); ?>">
                                        <i class="fa fa-edit"></i> <?php echo e(Lang::get('affidavit.edit')); ?></a>
										
										<?php if(Auth::user()->role_id != '19'): ?>
										<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get('affidavit.delete')); ?>" onclick="javascript:delete_self_spouse(<?php echo e($ss_row->id); ?>)">
											<i class="fa fa-times"></i> <?php echo e(Lang::get('affidavit.delete')); ?>

										</a>
										<?php endif; ?>
										
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                                        
                                <?php endif; ?>
								
								<?php if(Auth::user()->role_id != '19'): ?>
                                <form>
                                <tr id="self_spouse_form<?php echo e($self_spouse->relation_type_code); ?>">
                                    <td>
                                       <input type="text" class="form-control" name="occupation<?php echo e($self_spouse->relation_type_code); ?>" id="occupation<?php echo e($self_spouse->relation_type_code); ?>"  class="form-control" maxlength="200" required="required" onkeypress="return blockSpecialChar_name(event)">
                                        <input type="hidden" name="ss_relation_type<?php echo e($self_spouse->relation_type_code); ?>" id="ss_relation_type<?php echo e($self_spouse->relation_type_code); ?>" value="<?php echo e($self_spouse->name); ?>">

                                         <input type="hidden" name="ss_name<?php echo e($self_spouse->relation_type_code); ?>" id="ss_name<?php echo e($self_spouse->relation_type_code); ?>" value="<?php echo e($self_spouse->relation_type); ?>">
                                    </td>
                                    <td> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="<?php echo e(Lang::get('affidavit.save')); ?>" onclick="javascript:save_self_spouse_occupation(<?php echo e($self_spouse->candidate_id); ?>, <?php echo e($self_spouse->relation_type_code); ?> )">
                                        <i class="fa fa-check"></i> <?php echo e(Lang::get('affidavit.save')); ?></a>
                                    </td>
                                </tr>
                            </form>
							
							<?php endif; ?>
							
                            </tbody>
                            </table>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                    <!--  Self Spouse -->
                    <!--  Source of Income of All Dependants -->
                    <div class="accordion_head"><?php echo e(Lang::get('affidavit.source_of_income_of_all_dependants')); ?><span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none">
                        <?php if(!empty($data)): ?>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dependant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>    
                            <h6 class="text-left pt-2 py-3 text-uppercase">
                            <?php echo e($dependant->relation_type); ?> : <?php echo e($dependant->name); ?>

                            </h6>
                            <div class="table-responsive">
                            <table id="dependant<?php echo e($dependant->relation_type_code); ?>" class="table table-striped table-bordered table-hover purpleTable">
                            <thead>
                                <tr>
                                    <th><?php echo e(Lang::get('affidavit.source_of_income')); ?></th>
                                    <th><?php echo e(Lang::get('affidavit.action')); ?></th>          
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($dependant_income)): ?>
                                    <?php $__currentLoopData = $dependant_income; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depen_row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($dependant->relation_type_code==$depen_row->relation_type_code): ?>
                                    <tr id="trdepen<?php echo e($depen_row->id); ?>">                     
                                        <td><?php echo e($depen_row->source_of_income); ?></td>     
                                        <td nowrap="nowrap">
                                            <a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get('affidavit.edit')); ?>"   onclick="javascript:edit_depen_income(<?php echo e($depen_row->id); ?>)"
                                            data-id="<?php echo e($depen_row->id); ?>"
                                            data-source_of_income="<?php echo e($depen_row->source_of_income); ?>"
                                            data-relation_type_id="<?php echo e($depen_row->relation_type_code); ?>"
                                            data-candidate_id="<?php echo e($depen_row->candidate_id); ?>"
                                            id="edit_depen_income<?php echo e($depen_row->id); ?>">
                                        <i class="fa fa-edit"></i> <?php echo e(Lang::get('affidavit.edit')); ?></a>
										
									<?php if(Auth::user()->role_id != '19'): ?>	
										
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get('affidavit.delete')); ?>" onclick="javascript:delete_depen_income(<?php echo e($depen_row->id); ?>)">
                                        <i class="fa fa-times"></i> <?php echo e(Lang::get('affidavit.delete')); ?></a>   
										
									<?php endif; ?>
									
									
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                                        
                                <?php endif; ?>
								
								<?php if(Auth::user()->role_id != '19'): ?>
                                <form>
                                <tr id="depen_income_form<?php echo e($dependant->relation_type_code); ?>">
                                    <td>
                                       <input type="text" name="source_of_income<?php echo e($dependant->relation_type_code); ?>" id="source_of_income<?php echo e($dependant->relation_type_code); ?>"  class="form-control" maxlength="200" required="required" onkeypress="return blockSpecialChar_name(event)">
                                    </td>
                                    <td> 
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm" title="<?php echo e(Lang::get('affidavit.save')); ?>" onclick="javascript:save_depen_income(<?php echo e($dependant->candidate_id); ?>, <?php echo e($dependant->relation_type_code); ?> )" >
                                        <i class="fa fa-check"></i> <?php echo e(Lang::get('affidavit.save')); ?></a>
                                    </td>
                                </tr>
                            </form>
							<?php endif; ?>
							
                            </tbody>
                            </table>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?> 
                    </div>
                    <!--  Source of Income of All Dependants -->

                    <!--  Details of contract with Gov’t or public company -->
                    <div class="accordion_head"><?php echo e(Lang::get('affidavit.details_of_contract_with_govt_or_public_company')); ?><span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none"> 
                    <?php if(!empty($data)): ?>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dependant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>    
                        <h6 class="text-left pt-2 py-3 text-uppercase">
                        <?php echo e($dependant->relation_type); ?> : <?php echo e($dependant->name); ?>

                        </h6>
                        <div class="table-responsive">
                        <table id="govt_public<?php echo e($dependant->relation_type_code); ?>" class="table table-striped table-bordered table-hover purpleTable">
                        <thead>
                            <tr>
                                <th><?php echo e(Lang::get('affidavit.name_of_government_or_public_company')); ?></th>
                                <th><?php echo e(Lang::get('affidavit.details_of_contract_entered')); ?></th>
                                <th><?php echo e(Lang::get('affidavit.action')); ?></th>          
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($govt_public)): ?>
                                <?php $__currentLoopData = $govt_public; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depen_row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($dependant->relation_type_code==$depen_row->relation_type_code): ?>
                                <tr id="trgovt_public<?php echo e($depen_row->id); ?>">                     
                                    <td><?php echo e($depen_row->govt_public_company); ?></td>     
                                    <td><?php echo e($depen_row->details); ?></td>     
                                    <td nowrap="nowrap">
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get('affidavit.edit')); ?>" onclick="javascript:edit_govt_public(<?php echo e($depen_row->id); ?>)"
                                        data-id="<?php echo e($depen_row->id); ?>"
                                        data-govt_public_company="<?php echo e($depen_row->govt_public_company); ?>"
                                        data-govt_public_details="<?php echo e($depen_row->details); ?>"
                                        data-details="<?php echo e($depen_row->details); ?>"
                                        data-relation_type_id="<?php echo e($depen_row->relation_type_code); ?>"
                                        data-candidate_id="<?php echo e($depen_row->candidate_id); ?>"
                                        id="edit_govt_public<?php echo e($depen_row->id); ?>">
                                    <i class="fa fa-edit"></i> <?php echo e(Lang::get('affidavit.edit')); ?> </a>
									<?php if(Auth::user()->role_id != '19'): ?>
										<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get('affidavit.delete')); ?>" onclick="javascript:delete_govt_public(<?php echo e($depen_row->id); ?>)">
										<i class="fa fa-times"></i> <?php echo e(Lang::get('affidavit.delete')); ?>

										</a>
									<?php endif; ?>
									
                                </tr>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                                        
                            <?php endif; ?>
							
							<?php if(Auth::user()->role_id != '19'): ?>
                            <form>
								<tr id="govt_public<?php echo e($dependant->relation_type_code); ?>">    
									<td>
									   <input type="text" name="govt_public_company<?php echo e($dependant->relation_type_code); ?>" id="govt_public_company<?php echo e($dependant->relation_type_code); ?>"  class="form-control" maxlength="200" required="required" onkeypress="return blockSpecialChar_name(event)">
									</td>
									<td>
									   <textarea col="10" row="5" class="form-control" name="govt_public_details<?php echo e($dependant->relation_type_code); ?>" id="govt_public_details<?php echo e($dependant->relation_type_code); ?>" required="required" onkeypress="return blockSpecialChar_name(event)">
									   </textarea>
									</td>
									<td nowrap="nowrap"> 
										<a href="javascript:void(0)" class="btn btn-success btn-sm" title="<?php echo e(Lang::get('affidavit.save')); ?>" onclick="javascript:save_govt_public(<?php echo e($dependant->candidate_id); ?>, <?php echo e($dependant->relation_type_code); ?> )" >
										<i class="fa fa-check"></i> <?php echo e(Lang::get('affidavit.save')); ?>

									</a>
									</td>
								</tr>
							</form>
						<?php endif; ?>
						
                        </tbody>
                        </table>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>    
                    </div>
                    <!--  Details of contract with Gov’t or public company -->
                    <!-- ---------------HUF------------ -->
                    <div class="accordion_head"><?php echo e(Lang::get('affidavit.details_of_contracts_entered_into_by_hindu')); ?><span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none">
                    <?php if(!empty($data)): ?>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dependant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>    
                        <h6 class="text-left pt-2 py-3 text-uppercase">
                        <?php echo e($dependant->relation_type); ?> : <?php echo e($dependant->name); ?>

                        </h6>
                        <div class="table-responsive">
                        <table id="huf<?php echo e($dependant->relation_type_code); ?>" class="table table-striped table-bordered table-hover purpleTable">
                        <thead>
                            <tr>
                                <th><?php echo e(Lang::get('affidavit.name_of_hindu_undivided_family_or_trust')); ?></th>
                                <th><?php echo e(Lang::get('affidavit.details_of_contract_entered')); ?></th>
                                <th><?php echo e(Lang::get('affidavit.action')); ?></th>          
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($huf_trsut)): ?>
                                <?php $__currentLoopData = $huf_trsut; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depen_row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($dependant->relation_type_code==$depen_row->relation_type_code): ?>
                                <tr id="trhuf<?php echo e($depen_row->id); ?>">                     
                                    <td><?php echo e($depen_row->huf_trust_contracts); ?></td>     
                                    <td><?php echo e($depen_row->details); ?></td>     
                                    <td nowrap="nowrap">
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get('affidavit.edit')); ?>" onclick="javascript:edit_huf(<?php echo e($depen_row->id); ?>)"
                                        data-id="<?php echo e($depen_row->id); ?>"
                                        data-huf_trust_contracts="<?php echo e($depen_row->huf_trust_contracts); ?>"
                                        data-details="<?php echo e($depen_row->details); ?>"
                                        data-relation_type_id="<?php echo e($depen_row->relation_type_code); ?>"
                                        data-candidate_id="<?php echo e($depen_row->candidate_id); ?>"
                                        id="edit_huf<?php echo e($depen_row->id); ?>">
                                    <i class="fa fa-edit"></i> <?php echo e(Lang::get('affidavit.edit')); ?></a>
									
									<?php if(Auth::user()->role_id != '19'): ?>
										<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="De<?php echo e(Lang::get('affidavit.delete')); ?>lete" onclick="javascript:delete_huf(<?php echo e($depen_row->id); ?>)">
										<i class="fa fa-times"></i> <?php echo e(Lang::get('affidavit.delete')); ?></a>  
									<?php endif; ?>
									
                                </tr>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
							
							<?php if(Auth::user()->role_id != '19'): ?>
                            <form>
								<tr id="huf<?php echo e($dependant->relation_type_code); ?>">         
									<td>
									   <input type="text" name="huf_trust_contracts<?php echo e($dependant->relation_type_code); ?>" id="huf_trust_contracts<?php echo e($dependant->relation_type_code); ?>"  class="form-control" maxlength="200" required="required">
									</td>
									<td>
									   <textarea col="10" row="5" class="form-control" name="huf_details<?php echo e($dependant->relation_type_code); ?>" id="huf_details<?php echo e($dependant->relation_type_code); ?>" required="required" onkeypress="return blockSpecialChar_name(event)">
											</textarea>
									</td>
									<td nowrap="nowrap"> 
										<a href="javascript:void(0)" class="btn btn-success btn-sm" title=" <?php echo e(Lang::get('affidavit.save')); ?>"  onclick="javascript:save_huf(<?php echo e($dependant->candidate_id); ?>, <?php echo e($dependant->relation_type_code); ?> )" >
										<i class="fa fa-check"></i>  <?php echo e(Lang::get('affidavit.save')); ?></a>
									</td>
								</tr>
							</form>
							<?php endif; ?>
						
                        </tbody>
                        </table>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?> 
                    </div>
                    <!-- ---------------HUF------------ -->
                    <!-- ---------------Partnership------------ -->
                    <div class="accordion_head"><?php echo e(Lang::get('affidavit.details_of_contracts_entered_into_by_partnership_firms')); ?><span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none">
                    <?php if(!empty($data)): ?>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dependant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>    
                        <h6 class="text-left pt-2 py-3 text-uppercase">
                        <?php echo e($dependant->relation_type); ?> : <?php echo e($dependant->name); ?>

                        </h6>
                        <div class="table-responsive">
                        <table id="partner<?php echo e($dependant->relation_type_code); ?>" class="table table-striped table-bordered table-hover purpleTable">
                        <thead>
                            <tr>
                                <th><?php echo e(Lang::get('affidavit.name_of_partnership_firms')); ?></th>
                                <th><?php echo e(Lang::get('affidavit.details_of_contract_entered')); ?></th>
                                <th><?php echo e(Lang::get('affidavit.action')); ?></th>          
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($partnership)): ?>
                                <?php $__currentLoopData = $partnership; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depen_row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($dependant->relation_type_code==$depen_row->relation_type_code): ?>
                                <tr id="trpartner<?php echo e($depen_row->id); ?>">
                                    <td><?php echo e($depen_row->name_partnership_firm); ?></td>     
                                    <td><?php echo e($depen_row->details); ?></td>     
                                    <td nowrap="nowrap">
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get('affidavit.edit')); ?>" onclick="javascript:edit_partner(<?php echo e($depen_row->id); ?>)"
                                        data-id="<?php echo e($depen_row->id); ?>"
                                        data-name_partnership_firm="<?php echo e($depen_row->name_partnership_firm); ?>"
                                        data-details="<?php echo e($depen_row->details); ?>"
                                        data-relation_type_id="<?php echo e($depen_row->relation_type_code); ?>"
                                        data-candidate_id="<?php echo e($depen_row->candidate_id); ?>"
                                        id="edit_partner<?php echo e($depen_row->id); ?>">
                                    <i class="fa fa-edit"></i> <?php echo e(Lang::get('affidavit.edit')); ?></a>
									<?php if(Auth::user()->role_id != '19'): ?>
										<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get('affidavit.delete')); ?>" onclick="javascript:delete_partner(<?php echo e($depen_row->id); ?>)">
										<i class="fa fa-times"></i> <?php echo e(Lang::get('affidavit.delete')); ?></a>   
									<?php endif; ?>
									
                                </tr>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>   
                            <?php endif; ?>
							
							<?php if(Auth::user()->role_id != '19'): ?>
                            <form>
								<tr id="partner<?php echo e($dependant->relation_type_code); ?>">     
									<td>
									   <input type="text" name="name_partnership_firm<?php echo e($dependant->relation_type_code); ?>" id="name_partnership_firm<?php echo e($dependant->relation_type_code); ?>"  class="form-control" maxlength="200" required="required" onkeypress="return blockSpecialChar_name(event)">
									</td>
									<td>
									   <textarea col="10" row="5" class="form-control" name="partner_details<?php echo e($dependant->relation_type_code); ?>" id="partner_details<?php echo e($dependant->relation_type_code); ?>" required="required" onkeypress="return blockSpecialChar_name(event)">
											</textarea>
									</td>
									<td nowrap="nowrap"> 
										<a href="javascript:void(0)" class="btn btn-success btn-sm" title="<?php echo e(Lang::get('affidavit.save')); ?>" onclick="javascript:save_partner(<?php echo e($dependant->candidate_id); ?>, <?php echo e($dependant->relation_type_code); ?> )"><i class="fa fa-check"></i> <?php echo e(Lang::get('affidavit.save')); ?></a>
									</td>
								</tr>
							</form>
							<?php endif; ?>
						
                        </tbody>
                        </table>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?> 
                    </div>
                    <!-- ---------------Partnership------------ -->
                     <!-- ---------------Private------------ -->
                    <div class="accordion_head"><?php echo e(Lang::get('affidavit.details_of_contracts_entered_into_by_private_companies')); ?><span class="plusminus">+</span></div>
                    <div class="accordion_body" style="display: none">
                    <?php if(!empty($data)): ?>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dependant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>    
                        <h6 class="text-left pt-2 py-3 text-uppercase">
                        <?php echo e($dependant->relation_type); ?> : <?php echo e($dependant->name); ?>

                        </h6>
                        <div class="table-responsive">
                        <table id="private<?php echo e($dependant->relation_type_code); ?>" class="table table-striped table-bordered table-hover purpleTable">
                        <thead>
                            <tr>
                                <th><?php echo e(Lang::get('affidavit.name_of_private_company')); ?></th>
                                <th><?php echo e(Lang::get('affidavit.details_of_contract_entered')); ?></th>
                                <th><?php echo e(Lang::get('affidavit.action')); ?></th>          
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($private)): ?>
                                <?php $__currentLoopData = $private; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depen_row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($dependant->relation_type_code==$depen_row->relation_type_code): ?>
                                <tr id="trprivate<?php echo e($depen_row->id); ?>">
                                    <td><?php echo e($depen_row->name_private_company); ?></td>     
                                    <td><?php echo e($depen_row->details); ?></td>     
                                    <td nowrap="nowrap">
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get('affidavit.edit')); ?>" onclick="javascript:edit_private(<?php echo e($depen_row->id); ?>)"
                                        data-id="<?php echo e($depen_row->id); ?>"
                                        data-name_private_company="<?php echo e($depen_row->name_private_company); ?>"
                                        data-details="<?php echo e($depen_row->details); ?>"
                                        data-relation_type_id="<?php echo e($depen_row->relation_type_code); ?>"
                                        data-candidate_id="<?php echo e($depen_row->candidate_id); ?>"
                                        id="edit_private<?php echo e($depen_row->id); ?>">
                                    <i class="fa fa-edit"></i> <?php echo e(Lang::get('affidavit.edit')); ?></a>
									<?php if(Auth::user()->role_id != '19'): ?>
									<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get('affidavit.delete')); ?>" onclick="javascript:delete_private(<?php echo e($depen_row->id); ?>)">
                                    <i class="fa fa-times"></i> <?php echo e(Lang::get('affidavit.delete')); ?></a> 
									<?php endif; ?>
                                </tr>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  
                            <?php endif; ?>
							
							<?php if(Auth::user()->role_id != '19'): ?>
                            <form>
								<tr id="private<?php echo e($dependant->relation_type_code); ?>">     
									<td>
									   <input type="text" name="name_private_company<?php echo e($dependant->relation_type_code); ?>" id="name_private_company<?php echo e($dependant->relation_type_code); ?>"  class="form-control" maxlength="200" required="required" onkeypress="return blockSpecialChar_name(event)">
									</td>
									<td>
									   <textarea col="10" row="5" class="form-control" name="private_details<?php echo e($dependant->relation_type_code); ?>" id="private_details<?php echo e($dependant->relation_type_code); ?>" required="required" onkeypress="return blockSpecialChar_name(event)"></textarea>
									</td>
									<td nowrap="nowrap"> 
										<a href="javascript:void(0)" title="<?php echo e(Lang::get('affidavit.save')); ?>" onclick="javascript:save_private(<?php echo e($dependant->candidate_id); ?>, <?php echo e($dependant->relation_type_code); ?> )" >
										<span class="btn btn-success btn-sm"><i class="fa fa-check"></i> <?php echo e(Lang::get('affidavit.save')); ?></span>
									</a>
									</td>
								</tr>
							</form>
							<?php endif; ?>
						
                        </tbody>
                        </table>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?> 
                    </div>
                     <!-- Private -->
                </div>
                <div class="card-footer footerSection"> 
                    <div class="row">
                        <div class="col-12">
                            <a href="<?php echo e(url($menu_action.'liabilities')); ?>" class="backBtn float-left"><?php echo e(Lang::get('affidavit.back')); ?></a>
                            <a href="<?php echo e(url($menu_action.'education')); ?>" class="nextBtn float-right"><?php echo e(Lang::get('affidavit.save')); ?> &amp; <?php echo e(Lang::get('affidavit.next')); ?></a>
                            <a href="<?php echo e(url()->previous()); ?>" class="cencelBtn float-right mr-2"><?php echo e(Lang::get('affidavit.cancel')); ?></a
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
</main>
<!-- Self Spouse -->
<div class="modal fade" id="selfSpouseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.edit_occupation_of_self_and_spouse')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_selfSpouseModal">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo e(Lang::get('affidavit.occupation')); ?>:</label>
                    <input type="text" name="modal_ss_occupation" id="modal_ss_occupation"  class="form-control" maxlength="200"  required="required">
                </div>
            </div>                        
        </div>
        <input type="hidden" name="modal_ss_cand_id" id="modal_ss_cand_id">
        <input type="hidden" name="modal_ss_rel_id" id="modal_ss_rel_id">
        <input type="hidden" name="modal_ss_id" id="modal_ss_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.close')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_self_spouse()"><?php echo e(Lang::get('affidavit.update')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Self Spouse -->

<!-- Delete Self Spouse -->
<div class="modal fade" id="deleteselfSpouseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.delete_occupation_of_self_and_spouse')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5><?php echo e(Lang::get('affidavit.are_you_sure_to_delete_this_entry')); ?></h5>
        <input type="hidden" name="modal_delete_ss_id" id="modal_delete_ss_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.no')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_self_spouse_entry()"><?php echo e(Lang::get('affidavit.yes')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Delete Self Spouse -->

<!-- Dependant Income -->
<div class="modal fade" id="dependantModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.edit_source_of_income_of_all_dependants')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_dependantModal">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo e(Lang::get('affidavit.source_of_income')); ?>:</label>
                    <input type="text" name="modal_source_of_ncome" id="modal_source_of_ncome"  class="form-control" maxlength="200"required="required" onkeypress="return blockSpecialChar_name(event)">
                </div>
            </div>                        
        </div>
        <input type="hidden" name="modal_depen_cand_id" id="modal_depen_cand_id">
        <input type="hidden" name="modal_depen_rel_id" id="modal_depen_rel_id">
        <input type="hidden" name="modal_depen_id" id="modal_depen_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.close')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_depen_income()"><?php echo e(Lang::get('affidavit.update')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Dependant Income -->

<!-- Delete Dependant Income -->
<div class="modal fade" id="deleteDepenModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.delete_occupation_of_self_and_spouse')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5><?php echo e(Lang::get('affidavit.are_you_sure_to_delete_this_entry')); ?></h5>
        <input type="hidden" name="modal_delete_depen_id" id="modal_delete_depen_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.no')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_depen_income_entry()"><?php echo e(Lang::get('affidavit.yes')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Delete Dependant Income -->

<!-- Govt Public Source -->
<div class="modal fade" id="govtPublicModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.edit_details_of_contract_with_govt_or_public_company')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_govtPublicModal">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label"><?php echo e(Lang::get('affidavit.name_of_government_or_public_company')); ?>:</label>
                    <input type="text" name="modal_govt_public_company" id="modal_govt_public_company"  class="form-control" maxlength="200" required="required" onkeypress="return blockSpecialChar_name(event)">
                </div>
            </div>                        
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label"><?php echo e(Lang::get('affidavit.details_of_contract_entered')); ?>:</label>
                    <textarea col="10" row="5" class="form-control" name="modal_govt_public_details" id="modal_govt_public_details" required="required" onkeypress="return blockSpecialChar_name(event)"></textarea>
                </div>
            </div>                        
        </div>
        <input type="hidden" name="modal_govt_public_cand_id" id="modal_govt_public_cand_id">
        <input type="hidden" name="modal_govt_public_rel_id" id="modal_govt_public_rel_id">
        <input type="hidden" name="modal_govt_public_id" id="modal_govt_public_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.close')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_govt_public()"><?php echo e(Lang::get('affidavit.update')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Govt Public Source -->

<!-- Delete Govt Public Source -->
<div class="modal fade" id="deleteGovtPublicModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.delete_details_of_contract_with_govt_or_public_company')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5><?php echo e(Lang::get('affidavit.are_you_sure_to_delete_this_entry')); ?></h5>
        <input type="hidden" name="modal_delete_govt_public_id" id="modal_delete_govt_public_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.no')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_govt_public_entry()"><?php echo e(Lang::get('affidavit.yes')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Delete Govt Public Source -->

<!-- HUF -->
<div class="modal fade" id="hufModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.edit_details_of_contracts_entered_into_by_hindu')); ?> </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_hufModal">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo e(Lang::get('affidavit.name_of_hindu_undivided_family_or_trust')); ?>:</label>
                    <input type="text" name="modal_huf_trust_contracts" id="modal_huf_trust_contracts"  class="form-control" maxlength="200" required="required">
                </div>
            </div>                        
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo e(Lang::get('affidavit.details_of_contract_entered')); ?>:</label>
                    <textarea col="10" row="5" class="form-control" name="modal_huf_details" id="modal_huf_details" required="required" onkeypress="return blockSpecialChar_name(event)"></textarea>
                </div>
            </div>                        
        </div>
        <input type="hidden" name="modal_huf_cand_id" id="modal_huf_cand_id">
        <input type="hidden" name="modal_huf_rel_id" id="modal_huf_rel_id">
        <input type="hidden" name="modal_huf_id" id="modal_huf_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.close')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_huf()"><?php echo e(Lang::get('affidavit.update')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- HUF -->

<!-- Delete HUF -->
<div class="modal fade" id="deleteHufModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.delete_details_of_contracts_entered_into_by_hindu')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5><?php echo e(Lang::get('affidavit.are_you_sure_to_delete_this_entry')); ?></h5>
        <input type="hidden" name="modal_delete_huf_id" id="modal_delete_huf_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.no')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_huf_entry()"><?php echo e(Lang::get('affidavit.yes')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Delete HUF -->


<!-- Partner -->
<div class="modal fade" id="partnerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.edit_details_of_contracts_entered_into_by_partnership_firms')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_partnerModal">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo e(Lang::get('affidavit.name_of_partnership_firms')); ?>:</label>
                    <input type="text" name="modal_name_partnership_firm" id="modal_name_partnership_firm"  class="form-control" maxlength="200" required="required" onkeypress="return blockSpecialChar_name(event)">
                </div>
            </div>                        
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo e(Lang::get('affidavit.details_of_contract_entered')); ?>:</label>
                    <textarea col="10" row="5" class="form-control" name="modal_partner_details" id="modal_partner_details" required="required" onkeypress="return blockSpecialChar_name(event)"></textarea>
                </div>
            </div>                        
        </div>
        <input type="hidden" name="modal_partner_cand_id" id="modal_partner_cand_id">
        <input type="hidden" name="modal_partner_rel_id" id="modal_partner_rel_id">
        <input type="hidden" name="modal_partner_id" id="modal_partner_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.close')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_partner()"><?php echo e(Lang::get('affidavit.update')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Partner -->

<!-- Delete Partner -->
<div class="modal fade" id="deletePartnerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(Lang::get('affidavit.delete_details_of_contracts_entered_into_by_partnership_firms')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5><?php echo e(Lang::get('affidavit.are_you_sure_to_delete_this_entry')); ?></h5>
        <input type="hidden" name="modal_delete_partner_id" id="modal_delete_partner_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.no')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_partner_entry()"><?php echo e(Lang::get('affidavit.yes')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Delete Partner -->


<!-- Private -->
<div class="modal fade" id="privateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title"><?php echo e(Lang::get('affidavit.edit_details_of_contracts_entered_into_by_private_companies')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="form_privateModal">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo e(Lang::get('affidavit.name_of_partnership_firms')); ?>:</label>
                    <input type="text" name="modal_name_private_company" id="modal_name_private_company"  class="form-control" maxlength="200" required="required" onkeypress="return blockSpecialChar_name(event)">
                </div>
            </div>                        
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo e(Lang::get('affidavit.details_of_contract_entered')); ?>:</label>
                    <textarea col="10" row="5" class="form-control" name="modal_private_details" id="modal_private_details" required="required" onkeypress="return blockSpecialChar_name(event)"></textarea>
                </div>
            </div>                        
        </div>
        <input type="hidden" name="modal_private_cand_id" id="modal_private_cand_id">
        <input type="hidden" name="modal_private_rel_id" id="modal_private_rel_id">
        <input type="hidden" name="modal_private_id" id="modal_private_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.close')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:update_private()"><?php echo e(Lang::get('affidavit.update')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Private -->

<!-- Delete Private -->
<div class="modal fade" id="deletePrivateModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title"><?php echo e(Lang::get('affidavit.delete_details_of_contracts_entered_into_by_private_companies')); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form>
        <h5><?php echo e(Lang::get('affidavit.are_you_sure_to_delete_this_entry')); ?></h5>
        <input type="hidden" name="modal_delete_private_id" id="modal_delete_private_id">
    </form>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(Lang::get('affidavit.no')); ?></button>
    <button type="button" class="btn btn-primary" onclick="javascript:delete_private_entry()"><?php echo e(Lang::get('affidavit.yes')); ?></button>
    </div>
    </div>
    </div>
</div>
<!-- Delete Private -->


<?php $__env->stopSection(); ?> <?php $__env->startSection('script'); ?>
<!-- <script type="text/javascript" src="<?php echo e(asset('admintheme/js/jquery-ui.js')); ?>"></script> -->
<script type="text/javascript" src="<?php echo e(asset('affidavit/js/remove_special_character.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('affidavit/js/affidavit_validation.js')); ?>"></script>
<script>
function NumbersOnly(evt,obj)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    } else {
        if(charCode != 32)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
</script>
<script type="text/javascript">
    jQuery(function ($) {
    var $active = $('#accordion .panel-collapse.in').prev().addClass('active');
    $active.find('a').prepend('<i class="glyphicon glyphicon-minus"></i>');
    $('#accordion .panel-heading').not($active).find('a').prepend('<i class="glyphicon glyphicon-plus"></i>');
    $('#accordion').on('show.bs.collapse', function (e) {
      $('#accordion .panel-heading.active').removeClass('active').find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
      $(e.target).prev().addClass('active').find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
    })
    });
</script>

<link rel="stylesheet" type="text/css" href="<?php echo url('admintheme/css/jquery-ui.css'); ?>">
<script type="text/javascript" src="<?php echo url('admintheme/js/jquery-ui.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {  
    $(".datepicker").datepicker({
    dateFormat: 'yy-mm-dd',
    maxDate: 0
    });
    }); 
</script>


<!-- Self Spouse -->
    <script type="text/javascript">
    function save_self_spouse_occupation(cand_id, rel_id)
    {
        var occupation = $("#occupation"+rel_id).val();
        var relation_type = $("#ss_relation_type"+rel_id).val();
        var name = $("#ss_name"+rel_id).val();

        if(validate("self_spouse_form"+rel_id))
        {
            $.ajax({
            url: "<?php echo e(url('save_self_spouse')); ?>",
            type: 'Post',
            data: { 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    occupation:occupation
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                     
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_self_spouse('+datas.id+')"  data-occupation="'+occupation+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_self_spouse'+datas.id+'"> <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?> </a>';

                    var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_self_spouse('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?></a>';

                     $('#self_spouse'+rel_id).prepend('<tr id="trss'+datas.id+'"><td>'+occupation+'</td><td>'+edit+' '+del+'</td></tr>');

                    $("#occupation"+rel_id).val('');
                }
            }
            });
        }
    }
    </script>

    <script type="text/javascript">
    function edit_self_spouse(id)
    {
        var occupation =  "";
        var relation_type_id =  "";
        var candidate_id =  "";

        occupation = $("#edit_self_spouse"+id).data("occupation");
        relation_type_id = $("#edit_self_spouse"+id).data("relation_type_id");
        candidate_id = $("#edit_self_spouse"+id).data("candidate_id");

        $("#modal_ss_occupation").val(occupation);
        $("#modal_ss_rel_id").val(relation_type_id);
        $("#modal_ss_cand_id").val(candidate_id);
        $("#modal_ss_id").val(id);
        $("#selfSpouseModal").modal('show');
    }
    </script>

    <script type="text/javascript">
    function update_self_spouse()
    {
       
        var occupation = $("#modal_ss_occupation").val();
        var rel_id = $("#modal_ss_rel_id").val();
        var cand_id = $("#modal_ss_cand_id").val();
        var ss_id = $("#modal_ss_id").val();

        if(validate("form_selfSpouseModal"))
        {
            $.ajax({
            url: "<?php echo e(url($menu_action.'update_self_spouse')); ?>",
            type: 'GET',
            data: { 
                    ss_id:ss_id, 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    occupation:occupation 
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                    $('#trss'+ss_id).html('');
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_self_spouse('+datas.id+')"  data-occupation="'+occupation+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_self_spouse'+datas.id+'">  <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?>  </a>';

					<?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_self_spouse('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>

                    


                    $('#trss'+ss_id).html('<td>'+occupation+'</td><td>'+edit+' '+del+'</td>');
                    $("#selfSpouseModal").modal('hide');
                }
            }
            });
        }
    }
    </script>
    <script type="text/javascript">
    function delete_self_spouse(id)
    {
        $("#modal_delete_ss_id").val(id);
        $("#deleteselfSpouseModal").modal('show');
    }
    </script>
    <script type="text/javascript">
    function delete_self_spouse_entry()
    {
        var id = $("#modal_delete_ss_id").val();
        if(id)
        {
        $.ajax({
            url: "<?php echo e(url('delete_self_spouse')); ?>",
            type: 'GET',
            data: {  id:id },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
            if(data==1)
            {
                $('#trss'+id).remove();
                $("#deleteselfSpouseModal").modal('hide');
            }
            }
        });
        }
    }
    </script>
<!-- Self Spouse -->

<!-- Dependent Occupation -->
    <script type="text/javascript">
    function save_depen_income(cand_id, rel_id)
    {
        var source_of_income = $("#source_of_income"+rel_id).val();

        if(validate("depen_income_form"+rel_id))
        {
            $.ajax({
            url: "<?php echo e(url('save_dependent_income')); ?>",
            type: 'Post',
            data: { 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    source_of_income:source_of_income
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                     
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_depen_income('+datas.id+')"  data-source_of_income="'+source_of_income+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_depen_income'+datas.id+'"> <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?>  </a>';

                    var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_depen_income('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';

                     $('#dependant'+rel_id).prepend('<tr id="trdepen'+datas.id+'"><td>'+source_of_income+'</td><td>'+edit+' '+del+'</td></tr>');

                    $("#source_of_income"+rel_id).val('');
                }
            }
            });
        }
    }
    </script>

    <script type="text/javascript">
    function edit_depen_income(id)
    {
        var source_of_income =  "";
        var relation_type_id =  "";
        var candidate_id =  "";

        source_of_income = $("#edit_depen_income"+id).data("source_of_income");
        relation_type_id = $("#edit_depen_income"+id).data("relation_type_id");
        candidate_id = $("#edit_depen_income"+id).data("candidate_id");

        $("#modal_source_of_ncome").val(source_of_income);
        $("#modal_depen_rel_id").val(relation_type_id);
        $("#modal_depen_cand_id").val(candidate_id);
        $("#modal_depen_id").val(id);
        $("#dependantModal").modal('show');
    }
    </script>

    <script type="text/javascript">
    function update_depen_income()
    {
       
        var source_of_income = $("#modal_source_of_ncome").val();
        var rel_id = $("#modal_depen_rel_id").val();
        var cand_id = $("#modal_depen_cand_id").val();
        var depen_id = $("#modal_depen_id").val();

        if(validate("form_dependantModal"))
        {
            $.ajax({
            url: "<?php echo e(url($menu_action.'update_dependent_income')); ?>",
            type: 'GET',
            data: { 
                    depen_id:depen_id, 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    source_of_income:source_of_income 
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                    $('#trdepen'+depen_id).html('');
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_depen_income('+datas.id+')"  data-source_of_income="'+source_of_income+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_depen_income'+datas.id+'"> <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?> </a>';

					<?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_depen_income('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>

                    


                    $('#trdepen'+depen_id).html('<td>'+source_of_income+'</td><td>'+edit+' '+del+'</td>');
                    $("#dependantModal").modal('hide');
                }
            }
            });
        }
    }
    </script>
    <script type="text/javascript">
    function delete_depen_income(id)
    {
        $("#modal_delete_depen_id").val(id);
        $("#deleteDepenModal").modal('show');
    }
    </script>
    <script type="text/javascript">
    function delete_depen_income_entry()
    {
        var id = $("#modal_delete_depen_id").val();
        if(id)
        {
        $.ajax({
            url: "<?php echo e(url('delete_dependent_income')); ?>",
            type: 'GET',
            data: {  id:id },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
            if(data==1)
            {
                $('#trdepen'+id).remove();
                $("#deleteDepenModal").modal('hide');
            }
            }
        });
        }
    }
    </script>
<!-- Dependent Occupation -->

<!-- Governmwent Public Company -->
    <script type="text/javascript">
    function save_govt_public(cand_id, rel_id)
    {
        var govt_public_company = $("#govt_public_company"+rel_id).val();
        var govt_public_details = $("#govt_public_details"+rel_id).val();

        if(validate("govt_public"+rel_id))
        {
            $.ajax({
            url: "<?php echo e(url('save_govt_public')); ?>",
            type: 'Post',
            data: { 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    govt_public_company:govt_public_company,
                    details:govt_public_details
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                     
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_govt_public('+datas.id+')"  data-govt_public_company="'+govt_public_company+'"data-govt_public_details="'+govt_public_details+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_govt_public'+datas.id+'"> <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?> </a>';

                    var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_govt_public('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';

                     $('#govt_public'+rel_id).prepend('<tr id="trgovt_public'+datas.id+'"><td>'+govt_public_company+'</td><td>'+govt_public_details+'</td><td>'+edit+' '+del+'</td></tr>');

                    $("#govt_public_company"+rel_id).val('');
                    $("#govt_public_details"+rel_id).val('');
                }
            }
            });
        }
    }
    </script>

    <script type="text/javascript">
    function edit_govt_public(id)
    {
        var govt_public_company =  "";
        var relation_type_id =  "";
        var candidate_id =  "";


        govt_public_company = $("#edit_govt_public"+id).data("govt_public_company");
        govt_public_details = $("#edit_govt_public"+id).data("govt_public_details");
        relation_type_id = $("#edit_govt_public"+id).data("relation_type_id");
        candidate_id = $("#edit_govt_public"+id).data("candidate_id");

        $("#modal_govt_public_company").val(govt_public_company);
        $("#modal_govt_public_details").val(govt_public_details);
        $("#modal_govt_public_rel_id").val(relation_type_id);
        $("#modal_govt_public_cand_id").val(candidate_id);
        $("#modal_govt_public_id").val(id);
        $("#govtPublicModal").modal('show');
    }
    </script>

    <script type="text/javascript">
    function update_govt_public()
    {
       
        var govt_public_company = $("#modal_govt_public_company").val();
        var govt_public_details = $("#modal_govt_public_details").val();
        var rel_id = $("#modal_govt_public_rel_id").val();
        var cand_id = $("#modal_govt_public_cand_id").val();
        var govt_public_id = $("#modal_govt_public_id").val();

        if(validate("form_govtPublicModal"))
        {
            $.ajax({
            url: "<?php echo e(url($menu_action.'update_govt_public')); ?>",
            type: 'GET',
            data: { 
                    govt_public_id:govt_public_id, 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    govt_public_company:govt_public_company,
                    details:govt_public_details 
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                    $('#trgovt_public'+govt_public_id).html('');
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_govt_public('+datas.id+')"  data-govt_public_company="'+govt_public_company+'" data-govt_public_details="'+govt_public_details+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_govt_public'+datas.id+'"> <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?> </a>';

					<?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_govt_public('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>

                    


                    $('#trgovt_public'+govt_public_id).html('<td>'+govt_public_company+'</td><td>'+govt_public_details+'</td><td>'+edit+' '+del+'</td>');
                    $("#govtPublicModal").modal('hide');
                }
            }
            });
        }
    }
    </script>
    <script type="text/javascript">
    function delete_govt_public(id)
    {
        $("#modal_delete_govt_public_id").val(id);
        $("#deleteGovtPublicModal").modal('show');
    }
    </script>
    <script type="text/javascript">
    function delete_govt_public_entry()
    {
        var id = $("#modal_delete_govt_public_id").val();
        if(id)
        {
        $.ajax({
            url: "<?php echo e(url('delete_govt_public')); ?>",
            type: 'GET',
            data: {  id:id },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
            if(data==1)
            {
                $('#trgovt_public'+id).remove();
                $("#deleteGovtPublicModal").modal('hide');
            }
            }
        });
        }
    }
    </script>
<!-- Governmwent Public Company -->

<!-- HUF -->
    <script type="text/javascript">
    function save_huf(cand_id, rel_id)
    {
        var huf_trust_contracts = $("#huf_trust_contracts"+rel_id).val();
        var huf_details = $("#huf_details"+rel_id).val();

        if(validate("huf"+rel_id))
        {
            $.ajax({
            url: "<?php echo e(url('save_huf')); ?>",
            type: 'Post',
            data: { 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    huf_trust_contracts:huf_trust_contracts,
                    details:huf_details
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                     
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_huf('+datas.id+')"  data-huf_trust_contracts="'+huf_trust_contracts+'"data-details="'+huf_details+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_huf'+datas.id+'">  <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?> </a>';

                    var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_huf('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';

                     $('#huf'+rel_id).prepend('<tr id="trhuf'+datas.id+'"><td>'+huf_trust_contracts+'</td><td>'+huf_details+'</td><td>'+edit+' '+del+'</td></tr>');

                    $("#huf_trust_contracts"+rel_id).val('');
                    $("#huf_details"+rel_id).val('');
                }
            }
            });
        }
    }
    </script>

    <script type="text/javascript">
    function edit_huf(id)
    {
        var govt_public_company =  "";
        var relation_type_id =  "";
        var candidate_id =  "";


        huf_trust_contracts = $("#edit_huf"+id).data("huf_trust_contracts");
        huf_details = $("#edit_huf"+id).data("details");
        relation_type_id = $("#edit_huf"+id).data("relation_type_id");
        candidate_id = $("#edit_huf"+id).data("candidate_id");

        $("#modal_huf_trust_contracts").val(huf_trust_contracts);
        $("#modal_huf_details").val(huf_details);
        $("#modal_huf_rel_id").val(relation_type_id);
        $("#modal_huf_cand_id").val(candidate_id);
        $("#modal_huf_id").val(id);
        $("#hufModal").modal('show');
    }
    </script>

    <script type="text/javascript">
    function update_huf()
    {
       
        var huf_trust_contracts = $("#modal_huf_trust_contracts").val();
        var huf_details = $("#modal_huf_details").val();
        var rel_id = $("#modal_huf_rel_id").val();
        var cand_id = $("#modal_huf_cand_id").val();
        var huf_id = $("#modal_huf_id").val();

        if(validate("form_hufModal"))
        {
            $.ajax({
            url: "<?php echo e(url($menu_action.'update_huf')); ?>",
            type: 'GET',
            data: { 
                    huf_id:huf_id, 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    huf_trust_contracts:huf_trust_contracts,
                    details:huf_details
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                    $('#trhuf'+huf_id).html('');
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_huf('+datas.id+')"  data-huf_trust_contracts="'+huf_trust_contracts+'" data-details="'+huf_details+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_huf'+datas.id+'">  <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?>  </a>';

					<?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_huf('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>


                    $('#trhuf'+huf_id).html('<td>'+huf_trust_contracts+'</td><td>'+huf_details+'</td><td>'+edit+' '+del+'</td>');
                    $("#hufModal").modal('hide');
                }
            }
            });
        }
    }
    </script>
    <script type="text/javascript">
    function delete_huf(id)
    {
        $("#modal_delete_huf_id").val(id);
        $("#deleteHufModal").modal('show');
    }
    </script>
    <script type="text/javascript">
    function delete_huf_entry()
    {
        var id = $("#modal_delete_huf_id").val();
        if(id)
        {
        $.ajax({
            url: "<?php echo e(url('delete_huf')); ?>",
            type: 'GET',
            data: {  id:id },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
            if(data==1)
            {
                $('#trhuf'+id).remove();
                $("#deleteHufModal").modal('hide');
            }
            }
        });
        }
    }
    </script>
<!-- HUF -->


<!-- Partnership -->
    <script type="text/javascript">
    function save_partner(cand_id, rel_id)
    {
        var name_partnership_firm = $("#name_partnership_firm"+rel_id).val();
        var partner_details = $("#partner_details"+rel_id).val();

        if(validate("partner"+rel_id))
        {
            $.ajax({
            url: "<?php echo e(url('save_partner')); ?>",
            type: 'Post',
            data: { 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    name_partnership_firm:name_partnership_firm,
                    details:partner_details
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                     
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_partner('+datas.id+')"  data-name_partnership_firm="'+name_partnership_firm+'"data-details="'+partner_details+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_partner'+datas.id+'">  <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?>  </a>';

                    var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_partner('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';

                     $('#partner'+rel_id).prepend('<tr id="trpartner'+datas.id+'"><td>'+name_partnership_firm+'</td><td>'+partner_details+'</td><td>'+edit+' '+del+'</td></tr>');

                    $("#name_partnership_firm"+rel_id).val('');
                    $("#partner_details"+rel_id).val('');
                }
            }
            });
        }
    }
    </script>

    <script type="text/javascript">
    function edit_partner(id)
    {
        var govt_public_company =  "";
        var relation_type_id =  "";
        var candidate_id =  "";


        name_partnership_firm = $("#edit_partner"+id).data("name_partnership_firm");
        partner_details = $("#edit_partner"+id).data("details");
        relation_type_id = $("#edit_partner"+id).data("relation_type_id");
        candidate_id = $("#edit_partner"+id).data("candidate_id");

        $("#modal_name_partnership_firm").val(name_partnership_firm);
        $("#modal_partner_details").val(partner_details);
        $("#modal_partner_rel_id").val(relation_type_id);
        $("#modal_partner_cand_id").val(candidate_id);
        $("#modal_partner_id").val(id);
        $("#partnerModal").modal('show');
    }
    </script>

    <script type="text/javascript">
    function update_partner()
    {
       
        var name_partnership_firm = $("#modal_name_partnership_firm").val();
        var partner_details = $("#modal_partner_details").val();
        var rel_id = $("#modal_partner_rel_id").val();
        var cand_id = $("#modal_partner_cand_id").val();
        var huf_id = $("#modal_partner_id").val();

        if(validate("form_partnerModal"))
        {
            $.ajax({
            url: "<?php echo e(url($menu_action.'update_partner')); ?>",
            type: 'GET',
            data: { 
                    huf_id:huf_id, 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    name_partnership_firm:name_partnership_firm,
                    details:partner_details
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                    $('#trpartner'+huf_id).html('');
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_partner('+datas.id+')"  data-name_partnership_firm="'+name_partnership_firm+'" data-details="'+partner_details+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_partner'+datas.id+'">  <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?> </a>';

					<?php if(Auth::user()->role_id != '19') { ?>
					
					var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_partner('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>


                    $('#trpartner'+huf_id).html('<td>'+name_partnership_firm+'</td><td>'+partner_details+'</td><td>'+edit+' '+del+'</td>');
                    $("#partnerModal").modal('hide');
                }
            }
            });
        }
    }
    </script>
    <script type="text/javascript">
    function delete_partner(id)
    {
        $("#modal_delete_partner_id").val(id);
        $("#deletePartnerModal").modal('show');
    }
    </script>
    <script type="text/javascript">
    function delete_partner_entry()
    {
        var id = $("#modal_delete_partner_id").val();
        if(id)
        {
        $.ajax({
            url: "<?php echo e(url('delete_partner')); ?>",
            type: 'GET',
            data: {  id:id },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
            if(data==1)
            {
                $('#trpartner'+id).remove();
                $("#deletePartnerModal").modal('hide');
            }
            }
        });
        }
    }
    </script>
<!-- Partnership -->

<!-- Private -->
    <script type="text/javascript">
    function save_private(cand_id, rel_id)
    {
        var name_private_company = $("#name_private_company"+rel_id).val();
        var private_details = $("#private_details"+rel_id).val();

        if(validate("private"+rel_id))
        {
            $.ajax({
            url: "<?php echo e(url('save_private')); ?>",
            type: 'Post',
            data: { 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    name_private_company:name_private_company,
                    details:private_details
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                     
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_private('+datas.id+')"  data-name_private_company="'+name_private_company+'"data-details="'+private_details+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_private'+datas.id+'">  <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?>  </a>';

                    var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_private('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';

                     $('#private'+rel_id).prepend('<tr id="trprivate'+datas.id+'"><td>'+name_private_company+'</td><td>'+private_details+'</td><td>'+edit+' '+del+'</td></tr>');

                    $("#name_private_company"+rel_id).val('');
                    $("#private_details"+rel_id).val('');
                }
            }
            });
        }
    }
    </script>

    <script type="text/javascript">
    function edit_private(id)
    {
        var name_private_company =  "";
        var relation_type_id =  "";
        var candidate_id =  "";


        name_private_company = $("#edit_private"+id).data("name_private_company");
        private_details = $("#edit_private"+id).data("details");
        relation_type_id = $("#edit_private"+id).data("relation_type_id");
        candidate_id = $("#edit_private"+id).data("candidate_id");

        $("#modal_name_private_company").val(name_private_company);
        $("#modal_private_details").val(private_details);
        $("#modal_private_rel_id").val(relation_type_id);
        $("#modal_private_cand_id").val(candidate_id);
        $("#modal_private_id").val(id);
        $("#privateModal").modal('show');
    }
    </script>

    <script type="text/javascript">
    function update_private()
    {
       
        var name_private_company = $("#modal_name_private_company").val();
        var private_details = $("#modal_private_details").val();
        var rel_id = $("#modal_private_rel_id").val();
        var cand_id = $("#modal_private_cand_id").val();
        var private_id = $("#modal_private_id").val();

        if(validate("form_privateModal"))
        {
            $.ajax({
            url: "<?php echo e(url($menu_action.'update_private')); ?>",
            type: 'GET',
            data: { 
                    private_id:private_id, 
                    cand_id:cand_id, 
                    rel_type_id:rel_id,
                    name_private_company:name_private_company,
                    details:private_details
            },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                if(data!=0)
                {
                    datas = JSON.parse(data);
                    $('#trprivate'+private_id).html('');
                    var edit = '<a href="javascript:void(0)" class="btn btn-info btn-sm" title="<?php echo e(Lang::get("affidavit.edit")); ?>" onclick="javascript:edit_private('+datas.id+')"  data-name_private_company="'+name_private_company+'" data-details="'+private_details+'" data-relation_type_id="'+rel_id+'" data-candidate_id="'+cand_id+'" id="edit_private'+datas.id+'"> <i class="fa fa-edit"></i> <?php echo e(Lang::get("affidavit.edit")); ?> </a>';

					<?php if(Auth::user()->role_id != '19') { ?>
					
				   var del = '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="<?php echo e(Lang::get("affidavit.delete")); ?>" onclick="javascript:delete_private('+datas.id+')"> <i class="fa fa-times"></i> <?php echo e(Lang::get("affidavit.delete")); ?> </a>';
					
					<?php } else { ?>
					var del = '';	
					<?php } ?>

                    


                    $('#trprivate'+private_id).html('<td>'+name_private_company+'</td><td>'+private_details+'</td><td>'+edit+' '+del+'</td>');
                    $("#privateModal").modal('hide');
                }
            }
            });
        }
    }
    </script>
    <script type="text/javascript">
    function delete_private(id)
    {
        $("#modal_delete_partner_id").val(id);
        $("#deletePrivateModal").modal('show');
    }
    </script>
    <script type="text/javascript">
    function delete_private_entry()
    {
        var id = $("#modal_delete_partner_id").val();
        if(id)
        {
        $.ajax({
            url: "<?php echo e(url('delete_private')); ?>",
            type: 'GET',
            data: {  id:id },            
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
            if(data==1)
            {
                $('#trprivate'+id).remove();
                $("#deletePrivateModal").modal('hide');
            }
            }
        });
        }
    }
    </script>
<!-- Private -->

<!-- validation -->
<script type="text/javascript">
function validate(formval)
{
    if(formval)
    {
        var result = true;
        $('#'+formval+' :input').each(function()
        {
            if($(this).prop('required')) 
            {
                var value = $(this).val();
                var id = $(this).attr('id');
                $("#span_"+id).remove();
                /*if(id=="modal_make")
                {
                    alert(id);
                    alert(value);
                    alert(value.length);
                }*/
                if(!value || value=='' || value.length==0 || value <= 0)
                {                  
                    $('#'+id).after('<span class="err" id="span_'+id+'"><?php echo e(Lang::get("affidavit.this_field_is_required")); ?></span>');      
                    $('#'+formval).css("border-color", "solid 1px red");          
                    result =  false;
                }
            }
        });
        return result;
    }
}
$(document).ready(function() {            
    $(".accordion_head").click(function() {               
      if ($('.accordion_body').is(':visible')) {
         $(".accordion_body").slideUp(500);
         $(".plusminus").text('+');                                
      }
      if ($(this).next(".accordion_body").is(':visible')) {
        $(this).next(".accordion_body").slideUp(500);
        $(this).children(".plusminus").text('+');                
      } else {
        $(this).next(".accordion_body").slideDown(500);
        $(this).children(".plusminus").text('-');               
      }
    });
}); 
</script>
<script type="text/javascript" src="<?php echo e(asset('appoinment/js/bootstrap-multiselect.js')); ?>" /></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.selectOne').multiselect();
    });
</script>
<!-- validation -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make( (Auth::user()->role_id != '19') ? 'layouts.theme' : 'admin.layouts.ac.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/affidavit/affidavit_profession.blade.php ENDPATH**/ ?>