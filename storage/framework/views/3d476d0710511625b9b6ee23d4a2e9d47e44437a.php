<?php $__env->startSection('title', 'Candidate Nomintion Details'); ?>
<?php $__env->startSection('bradcome', 'Withdrawn of Candidate'); ?>
<?php $__env->startSection('content'); ?> 
<?php 
  $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
  $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'4'])->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'5'])->get()->count() ;
    
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['application_status' =>'6'])->where('party_id', '!=' ,'1180')->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where('application_status','!=','11')->where('party_id', '!=' ,'1180')->get()->count();
   ?>

   <style type="text/css">
     
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
<section class="data_table mt-5 form">
  <div class="container-fluid">
         <?php if(session('success_mes')): ?>
          <div class="alert alert-success"> <?php echo e(session('success_mes')); ?></div>
        <?php endif; ?>
<?php $i=0; $url = URL::to("/");  ?>
   <?php if(!$lists->isEmpty()): ?>
  <div class="row d-flex align-items-center">
<div class="col"> <h5>Withdrawl of Candidate</h5></div>
    <div class="col">
    <form class="form-inline float-right">
         
          
   
        
      <div class="form-group "> 
        <label for="noofcards" class="mr-3">Select Status</label> 
        <form name="frmstatus" id="frmstatus" method="GET"  action="" >
        <select name="cand_status" id="cand_status" onchange="this.form.submit();">
              <option value="" <?php if($status==''): ?> selected="selected" <?php endif; ?>>All</option>
              <?php $__currentLoopData = $status_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>   
               <?php if($s->id==5|| $s->id==6): ?> 
              <option value="<?php echo e($s->id); ?>" <?php if($status==$s->id): ?> selected="selected" <?php endif; ?> ><?php if(isset($s)): ?> <?php echo e(ucwords($s->status)); ?> <?php endif; ?></option>
              <?php endif; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
  &nbsp;  &nbsp;
                <div class="input-group ">
                    <input type="text" class="form-control input-lg"  name="search" placeholder="Search By Candidate Name"  />
          &nbsp;
                    <span class="input-group-btn">
                        <button class="btn btn-primary  btn-lg" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>



        </div>        
        <div class="form-group float-right ml-4">
               <!--  <div class="input-group ">
                    <input type="text" class="form-control input-lg"  name="search" placeholder="Search By Candidate Name"  />
          &nbsp;
                    <span class="input-group-btn">
                        <button class="btn btn-primary  btn-lg" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div> -->
            </div>
        </form>
    </div>
    </div>
    <div class="row" id="myTable">
  
      <?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  
  <?php  $i++;
    $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id);// \app(App\adminmodel\Candidateaffidavit::class)->where(['nom_id' =>$list->nom_id])->first(); 
     $party= getpartybyid($list->party_id);
     $symb= getsymbolbyid($list->symbol_id);
     $s= getnameBystatusid($list->application_status);
  ?>   
  
  
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 mb-3 allnom d-flex">
    <div class="card">
      <div class="card-header d-flex align-items-center">
       <h6 class="mr-auto"><?php if(isset($party)): ?><?php echo e(ucwords($party->PARTYNAME)); ?><?php endif; ?></h6>    
      </div>
      <div class="card-body">
      <div class="table-responsive">
      <table class="table">                    
              <tbody>
              <tr class="space">
                <td rowspan="5" class="profileimg" class="td-01">
				  <span class="btn-sno"><?php echo e($i); ?></span>				<?php if($list->cand_image!=''): ?>
                       <img src="<?php echo e($url.'/'.$list->cand_image); ?>" class="prfl-pic img-thumbnail" alt="">
                    <?php else: ?> 
                      <img src="<?php echo e(asset('admintheme/img/male_avatar.png')); ?>" class="prfl-pic img-thumbnail" alt="">
				
                    <?php endif; ?> </td>
                 <td class="td-02" style="width: 30%"><label for="name">Name: <br> Name in Hindi <br>  Name in Vernacular</label></td>
        <td class="td-03" style="width: 40%"><p><?php echo e($list->cand_name); ?>  <br> <?php if(!empty($list->cand_hname)): ?> <?php echo e($list->cand_hname); ?> <?php endif; ?> <br>  <?php if(!empty($list->cand_vname)): ?><?php echo e($list->cand_vname); ?> <?php endif; ?></p></td>
               </tr> 
              <tr>	<td><label for="FName">Father's Name:</label></td>
					<td><p><?php echo e($list->candidate_father_name); ?></p></td>
			  </tr> 
			  
              <tr><td><label for="DateOfsubmission">Date of Submission:</label></td>
			  <td><p><?php echo e(date("d-F-Y",strtotime($list->date_of_submit))); ?></p></td>
               </tr> 
            
			  <tr>
				<td><label for="Symbol">Symbol</label></td>
				<td><p><?php if(isset($symb)): ?> <?php echo e($symb->SYMBOL_DES); ?><?php endif; ?></p></td>
			  </tr>
         <tr>
                
                  <td class="td-02" style="width: 30%"><label for="name">Candidate ID: <br> Nomination ID: <br>  Party Type: </label></td>
        <td class="td-03" style="width: 40%"><p> <?php echo e(isset($list->candidate_id) ? $list->candidate_id : ''); ?> <br>  <?php echo e(isset($list->nom_id) ? $list->nom_id : ''); ?>  <br>  <?php if(!empty($party)): ?>  <?php if($party->PARTYTYPE=="N"): ?> National <?php endif; ?> <?php if($party->PARTYTYPE=="S"): ?> State  <?php endif; ?> <?php if($party->PARTYTYPE=="U"): ?> Unrecognized  <?php endif; ?> 
                    <?php if($party->PARTYTYPE=="Z"): ?> Independent  <?php endif; ?> <?php endif; ?> </p></td>                         
               </tr>
			  <!-- <tr>
				<td><label for="Ptype">Party Type</label></td>
				<td><p><?php if(!empty($party)): ?>  <?php if($party->PARTYTYPE=="N"): ?> National   <?php endif; ?> <?php if($party->PARTYTYPE=="S"): ?> State  <?php endif; ?> <?php if($party->PARTYTYPE=="U"): ?> Unrecognized  <?php endif; ?> 
                    <?php if($party->PARTYTYPE=="Z"): ?> Independent  <?php endif; ?> <?php endif; ?></p></td>
			  </tr> -->
             
             </tbody>
      </table>
      </div>
      </div>
      <div class="card-footer">
      <div class="row d-flex align-items-left">
	  <div class="col"> <small class="text-data text-success"><i class="fa fa-check"></i> <?php if(isset($s)): ?><?php echo e(ucwords($s)); ?> <?php endif; ?></small> </div>
      <div class="col"> 
     
      <div class="btn-group float-right" role="group" aria-label="">
      <?php if(!empty($affidavit->affidavit_name)): ?> <a href="<?php echo e(asset($affidavit->affidavit_path)); ?>" class="btn btn-primary" target="_blank" >Download Affidavit</a>  <?php else: ?> <a href="#" class="btn btn-light">No Affidavit</a> <?php endif; ?>     
       &nbsp;&nbsp;     
      
        <button type="button" id="<?php echo e($list->nom_id); ?>" class="btn btn-primary  btn-sm getdata" data-toggle="modal" data-target="#changestatus" data-nomid="<?php echo e($list->nom_id); ?>" data-canid="<?php echo e($list->candidate_id); ?>" data-status="<?php echo e($list->application_status); ?>" data-message="<?php echo e($list->rejection_message); ?>"> Withdraw Candidate</button> 
      </div>
      </div>
      </div>
      </div>
    </div>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <?php else: ?>
    <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
 
  </div>
   <?php endif; ?>
<!-- ==========================-->
    
    
  
</div>
</section>
  <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Withdrawn Candidate Status</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST"  action="<?php echo e(url('ropc/withstatusvalidation')); ?>" >
                <?php echo e(csrf_field()); ?>   
         
    <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
     <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <div class="mb-3">
      <!--<div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline1" name="marks" value="4" class="custom-control-input" checked>
        <label class="custom-control-label" for="customRadioInline1">Rejected</label>
      </div>-->
       <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline2" name="marks" value="5" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline2">Withdrawn</label>
      </div> 
      <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="customRadioInline3" name="marks" value="6" class="custom-control-input">
        <label class="custom-control-label" for="customRadioInline3">Not Withdrawn</label>
      </div>
      </div>
    
       <div class="mb-3">
      
    <label class="sr-only" for="validationTextarea">I have examined the withdrawn papers <sup>*</sup></label>
    <textarea class="form-control " id="rejection_message" name="rejection_message" placeholder="Required example textarea" required="required"></textarea>
    <span id="err" class="text-danger"></span>
    <div class="invalid-feedback">
      Please enter a message in the textarea.
    </div>
    
    

  </div>
  
   
 
   
  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </form>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->


<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script type="text/javascript">
           jQuery(document).ready(function(){
          //By Dropdown 
          jQuery("select[name='cand_status']").change(function(){
            var cand_status = jQuery(this).val();
            //alert(candStatus);
            jQuery.ajax({
                    url: "<?php echo e(url('/listnomination')); ?>",
                    type: 'GET',
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

          $("#election_form").submit(function(){
             if($("#rejection_message").val()=='')
                    {  
                    $("#err").text("");
                    $("#err").text("Please enter message");
                    $("#rejection_message").focus();
                    return false;
                    }
               });
        });
  
  $(document).on("click", ".getdata", function () {
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       var s = $(this).attr('data-status');
       var message = $(this).attr('data-message');
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
       $("#rejection_message").val(message);
       if(s==6){
            $("#customRadioInline3").attr ( "checked" ,"checked" );
          }
       
      if(s==5){
            $("#customRadioInline2").attr ( "checked" ,"checked" );
          }  
   });
    
    
</script>
 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.pc.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/pc/ro/withdrawn_candidates.blade.php ENDPATH**/ ?>