<?php $__env->startSection('title', 'Candidate Nomintion Details'); ?>
<?php $__env->startSection('bradcome', 'List of All Applications'); ?>
<?php $__env->startSection('content'); ?> 

<?php  
    
    $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();
     
     ?>
<main>
<style type="text/css">
th, td {white-space: normal!important;}
.data_table td label {
    font-size: 12px!important;
}
.table tr td p {
    margin: 0px;
    font-size: 13px;
}
.col-xl-4 {
  -ms-flex: 0 0 33.333333%;
  flex: 0 0 50%;
  max-width: 50%;
}
.text-warning{color: #4CAF50 !important;}
</style>
<section class="statistics color-grey pt-5 pb-5" style="border-bottom:1px solid #eee;">
        <div class="container-fluid">
          <div class="row d-flex">
            <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                <div class="icon"><img src="<?php echo e(asset('admintheme/img/icon/applied.png')); ?>" alt="" /></div>
                <div class="number yellow"><?php echo e($total); ?></div><p>Applications<strong class="text-primary">Applied</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                  <div class="icon"><img src="<?php echo e(asset('admintheme/img/icon/verified.png')); ?>" alt="" /></div>
                <div class="number green"><?php echo e($totaccepted); ?></div><p>Applications<strong class="text-primary">Accepted </strong></p>
               
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="<?php echo e(asset('admintheme/img/icon/generate.png')); ?>" alt="" /></div>
                <div class="number orange"><?php echo e($totrej); ?></div><p>Total Receipt<strong class="text-primary">Rejected</strong></p>
                
              </div>
            </div> 
      <div class="col-md-3">
              <!-- Income-->
              <div class="card income text-center">
                   <div class="icon"><img src="<?php echo e(asset('admintheme/img/icon/notverified.png')); ?>" alt="" /></div>
                <div class="number red"><?php echo e($totalwith); ?></div><p>Applications<strong class="text-primary">Withdrawn</strong></p>
              </div>
            </div>
          </div>
        
        </div>
</section>
<section>
	
		<div class="row">
			<div class="col">
				  <?php if($cand_finalize_ro==0): ?>
     <div class="alert alert-danger"> Candidate Nominations details has not been finalized</div>
     <?php elseif($checkval==1): ?>
                    <div class="alert alert-success">  Candidate Nominations details has been finalized </div>
            <?php endif; ?></div>
			</div>
		
	
</section>
<section class="data_table mt-5 form">
  <div class="container-fluid">
 
	<div class="row">
	    <?php if(session('success_mes')): ?>
          <div class="alert alert-success"> <?php echo e(session('success_mes')); ?></div>
        <?php endif; ?>
         <?php if(session('error_mes')): ?>
          <div class="alert alert-danger"> <?php echo e(session('error_mes')); ?></div>
        <?php endif; ?>
         <?php if(session('finalize_mes')): ?>
          <div class="alert alert-success"> <?php echo e(session('finalize_mes')); ?></div>
        <?php endif; ?>
	</div>
	<div class="row d-flex align-items-center mb-3">
	<div class="col">
		<h5>List of All Applications</h5>
	</div>
		<div class="col-md-8">
		<form class="form-inline pull-right">
         
          
			<div class="form-group float-right"> 
				<label for="noofcards" class="mr-3">Select Status</label> 
				<form name="frmstatus" id="frmstatus" method="POST"  action="" >
				<select name="cand_status" id="cand_status" onchange="this.form.submit();">
              <option value="" <?php if($status==''): ?> selected="selected" <?php endif; ?>>All</option>
              <?php if(isset($status_list)): ?>
              <?php $__currentLoopData = $status_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if($s->id==1 || $s->id==4 ||$s->id==5|| $s->id==6): ?>
              <option value="<?php echo e($s->id); ?>" <?php if($status==$s->id): ?> selected="selected" <?php endif; ?> ><?php if(isset($s)): ?><?php echo e(ucwords($s->status)); ?>  <?php endif; ?></option>
              <?php endif; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?>
        </select>
		    </div>				
		    <div class="form-group float-right ml-4">
                <div class="input-group ">
                    <input type="text" class="form-control input-lg" name="search" placeholder="Search By Candidate Name"  />
					&nbsp;
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
        </form>
		</div>
		</div>
	 
	<div class="row" id="myTable">
	<?php $url = URL::to("/");    $i=1;   ?>
	<?php if(!$lists->isEmpty()): ?>
	<?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<?php   $getid = Crypt::encrypt($list->nom_id);
	      
		 $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);// \app( 
		 $party= getpartybyid($list->party_id);
		 $symb= getsymbolbyid($list->symbol_id);
		 $s= getnameBystatusid($list->application_status);
	?>   
	
		<div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex" data-id="key<?php echo e($s); ?>">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<h6 class="mr-auto">
					<?php if(!empty($party)): ?>
						<?php echo e($party->PARTYNAME); ?>/<?php echo e(!empty($party->PARTYHNAME) ? trim($party->PARTYHNAME) : ''); ?> 
					<?php endif; ?></h6>
	<!-- <?php if($cand_finalize_ro==0): ?>				 
	<button type="button" id="<?php echo e($list->nom_id); ?>" class="btn btn-link btn-sm getdata" data-toggle="modal" data-target="#changestatus" data-nomid="<?php echo e($list->nom_id); ?>" data-canid="<?php echo e($list->candidate_id); ?>"> Drop <i class="fa fa-times" aria-hidden="true"></i></button> 
	<?php endif; ?>  -->
				 
				</div>


			 
			<div class="table-responsive card-body">
		
			<table class="table " border="0">                    
			  <tbody>
				<tr class="space">
				<td rowspan="6" class="profileimg td-01" style="width: 30%">
				<span class="btn-sno"><?php echo e($i); ?></span>	<?php if($list->cand_image!=''): ?>
                      <img src="<?php echo e($url.'/'.$list->cand_image); ?>" class="prfl-pic img-thumbnail" alt="no images">
                    <?php else: ?> 
                      <img src="<?php echo e(asset('admintheme/images/User-Icon.png')); ?>" class="prfl-pic img-thumbnail" alt="">
                    <?php endif; ?>
				</td>
				<td class="td-02" style="width: 30%"><label for="name">Name: <br> Name in Hindi <br>  Name in Vernacular</label></td>
				<td class="td-03" style="width: 40%"><p><?php echo e($list->cand_name); ?>  <br> <?php if(!empty($list->cand_hname)): ?> <?php echo e($list->cand_hname); ?> <?php endif; ?> <br>  <?php if(!empty($list->cand_vname)): ?><?php echo e($list->cand_vname); ?> <?php endif; ?></p></td>
				</tr>
				<tr class="space">
				<td><label for="FName">Candidate ID:</label></td>
				<td><p><?php echo e($list->candidate_id); ?> </p></td>
				
				</tr>   
				<tr class="space">
				<td><label for="FName">Father's / Husband's Name:</label></td>
				<td><p><?php echo e($list->candidate_father_name); ?></p></td>
				
				</tr> 
				<tr class="space">
				<td><label for="DateOfsubmission">Date of Submission:</label></td>
				<td><p><?php echo e(date("d M Y",strtotime($list->date_of_submit))); ?></p></td>
				
				</tr> 
				<tr class="space">
 
				<td>
				<label for="Symbol">Symbol</label></td><td><p><?php if(!empty($symb)): ?> <?php echo e($symb->SYMBOL_DES); ?> <?php endif; ?></p>				
				</td>
				</tr>
				<tr class="space">
				<td><label for="Ptype">Party Type</label></td>
				<td>
						<p>
							<?php if($party->PARTYTYPE=="N"): ?> 
								National  
							<?php endif; ?> 
							<?php if($party->PARTYTYPE=="S"): ?> 
								State  
							<?php endif; ?> 
							<?php if($party->PARTYTYPE=="U"): ?> 
								Unrecognized  
							<?php endif; ?> 
							<?php if($party->PARTYTYPE=="Z"): ?> 
								Independent  
							<?php endif; ?>
						</p></td>
				</tr> 
		
	
		  
		
				</tbody>
			</table>
			</div>
				<div class="card-footer">
      <div class="row d-flex align-items-center">
	  <div class="col md-3">
	  <?php if($s == "accepted"): ?>
						<small class="text-data text-success"><i class="fa fa-check"></i> Accepted </small>
					<?php elseif($s == "rejected"): ?>
						<small class="text-data text-danger"><i class="fa fa-check"></i> Rejected </small>
					<?php elseif($s == "withdrawn"): ?>
						<small class="text-data text-secondary"><i class="fa fa-check"></i> Withdrwan </small>
					<?php else: ?>
						<small class="text-data text-warning"><i class="fa fa-check"></i><?php echo e($s); ?> </small>
					<?php endif; ?>
					</div>
      <div class="col"> 
     
      <div class="btn-group float-right" role="group" aria-label="Basic example">
     		<?php if(!empty($affidavit->affidavit_name)): ?>
				<a href="<?php echo e(asset($affidavit->affidavit_path)); ?>" class="btn btn-primary btn-sm" download>Download Affidavit</a>&nbsp;&nbsp;
			<?php endif; ?>
			<?php if($cand_finalize_ro==0): ?>
           		<a href="<?php echo e('updatenomination/'.$getid); ?>" class="btn btn-primary btn-sm">Update Profile</a>&nbsp;&nbsp;
           <?php endif; ?>
           <?php if($list->cand_name!="NOTA"): ?>
				<a href="<?php echo e('viewnomination/'.$getid); ?>" class="btn btn-primary btn-sm">View Profile</a>
			<?php endif; ?>
		
		
      </div>
      </div>
      </div>
      </div>
			
			</div>
			
		</div>
	<?php $i++; ?>	 
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	<?php else: ?>
	  <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
	<?php endif; ?>
	</div>
</div>
</section>
 <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <small class="modal-title" id="exampleModalLabel">Remove Duplicate Candidate Entry.</small>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="<?php echo e(url('ropc/duplicate-drop')); ?>" >
                <?php echo e(csrf_field()); ?>   
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
     <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <div class="mb-3">
    	
		 <p style="font-size:14px;" class="">Are you sure. You want to drop this duplicate record<sup>*</sup>
		 <br /> </p>
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline1" name="marks" value="11" class="custom-control-input" required="required">
        <label class="custom-control-label" for="customRadioInline1" >Duplicate Drop</label>
      </div>
	  <br />
	 
     </div>
     <div class="mb-3">
      <small class="text-secondary">Incase if the entry has been made wrongly, can be removed by this option</small>
      </div> 
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Remove</button>
      </div>
    </form>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->
</main>  
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script type = "text/javascript">  
window.onload = function () {  
	document.onkeydown = function (e) {  
		return (e.which || e.keyCode) != 116;  
	};  
}  
jQuery(document).ready(function(){
	//By Dropdown 
	jQuery("select[name='cand_status']").change(function(){
		var cand_status = jQuery(this).val();
		//alert(candStatus);
		jQuery.ajax({
            url: "<?php echo e(url('/listnomination')); ?>",
            type: 'POST',
            data: {cand_status:cand_status},
            success: function(result){
			}
		});
	});
	
	//By Searh Text
	jQuery("#myInput").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		jQuery("#myTable div").filter(function() {
			jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
});

$(document).on("click", ".getdata", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
        
   });
</script>  
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.pc.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/pc/ro/listnomination.blade.php ENDPATH**/ ?>