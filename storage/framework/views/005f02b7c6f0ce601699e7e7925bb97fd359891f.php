<?php $__env->startSection('title', 'Candidate Nomintion Details'); ?>
<?php $__env->startSection('bradcome', 'Symbol Assign to Candidate'); ?>
<?php $__env->startSection('content'); ?>
<style type="text/css">
  
  .col-xl-4 {
  -ms-flex: 0 0 33.333333%;
  flex: 0 0 50%;
  max-width: 50%;
}
.text-warning{color: #4CAF50 !important;}

</style>

<section class="statistics color-grey pt-3 pb-2 border-bottom">
	<div class="container-fluid">
			<div class="row">
			<div class="col">
			 <h5> Symbol Assign to Candidate</h5>
			</div>
       <?php if(\Session::has('success_mes')): ?>
          <div class="alert alert-success"> <?php echo \Session::get('success_mes'); ?> </div>
      <?php endif; ?>
      <?php if(\Session::has('error_mes')): ?>
         <div class="alert alert-danger"> <?php echo \Session::get('error_mes'); ?> </div>
      <?php endif; ?>
			</div>
	</div>
</section>
 
<section class="data_table mt-5 form">
  <div class="container-fluid">
  <p>Disclaimer: Symbols should first be allocated as per the extant provisions and then to be entered matching in Encore.</p><br>
 <div class="row" id="myTable">
  <?php $url = URL::to("/");   $j=0; ?>
  <?php if(!$lists->isEmpty()): ?>
  
      <?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  
          <?php 
              $affidavit=getById('candidate_affidavit_detail','nom_id',$list->nom_id); 
              $party= getpartybyid($list->party_id);
              $symb= getsymbolbyid($list->symbol_id);
              $s= getnameBystatusid($list->application_status);
             $j++;
           ?> 
   
     <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4  mb-3">
    <div class="card">
      <div class="card-header d-flex align-items-center">
      
        <h6 class="mr-auto"><?php if(isset($party)): ?><?php echo e(ucwords($party->PARTYNAME)); ?><?php endif; ?></h6>     
        <!-- <small class="text-data text-success">Status:-<i class="fa fa-check"></i><?php if(isset($s)): ?> <?php echo e(ucwords($s)); ?> <?php endif; ?></small>    -->  
        
      </div>
      <div class="card-body">
      <div class="table-responsive">
      <table class="table">                    
              <tbody>
              <tr class="space">
                <td rowspan="5" class="profileimg td-01">
				<span class="btn-sno"><?php echo e($j); ?></span>
				<?php if($list->cand_image!=''): ?>
                       <img src="<?php echo e($url.'/'.$list->cand_image); ?>" class="prfl-pic img-thumbnail" alt="">
                    <?php else: ?> 
                      <img src="<?php echo e(asset('admintheme/img/male_avatar.png')); ?>" class="prfl-pic img-thumbnail" alt="">
                    <?php endif; ?> </td>
                 <td class="td-02" style="width: 30%"><label for="name">Name: <br> Name in Hindi <br>  Name in Vernacular</label></td>
        <td class="td-03" style="width: 40%"><p><?php echo e($list->cand_name); ?>  <br> <?php if(!empty($list->cand_hname)): ?> <?php echo e($list->cand_hname); ?> <?php endif; ?> <br>  <?php if(!empty($list->cand_vname)): ?><?php echo e($list->cand_vname); ?> <?php endif; ?></p></td>
                
               </tr> 
              <tr class="space">
			  <td><label for="FName">Father's / Mother's Name / Husband's Name:</label></td>
			  <td><p><?php echo e($list->candidate_father_name); ?></p></td>
			  </tr> 
              <tr class="space">
			  <td><label for="DateOfsubmission">Date of Submission:</label></td>
			  <td><p><?php echo e(date("d-F-Y",strtotime($list->date_of_submit))); ?></p></td>
               </tr> 
			   <tr class="space">
				<td><label for="Symbol">Symbol</label></td>
				<td><p><?php if(isset($symb)): ?> <?php echo e($symb->SYMBOL_DES); ?><?php endif; ?></p></td>
			   </tr>
			   <tr>
					<td><label for="Ptype">Party Type</label></td>
					<td><p><?php if(!empty($party)): ?>  <?php if($party->PARTYTYPE=="N"): ?> National   <?php endif; ?> <?php if($party->PARTYTYPE=="S"): ?> State  <?php endif; ?> <?php if($party->PARTYTYPE=="U"): ?> Unrecognized  <?php endif; ?> <?php if($party->PARTYTYPE=="Z"): ?> Independent  <?php endif; ?> <?php endif; ?></p></td>
			   </tr>
			   
          
          
              </tbody>
      </table>
      </div>
      </div>
     <div class="card-footer">
      <div class="row ">
      <div class="col d-flex align-items-center">
      <?php if($list->symbol_id==0 || $list->symbol_id=='200'): ?> 
      <small class="text-muted mr-auto"><i>Symbol is not assign</i></small>
      <div class="btn-group float-right" role="group" aria-label="Basic example">       
        <!--<small class="text-success btn"><i class="fa fa-check"></i> Already Assigned</small> -->     
        <button type="button" id="<?php echo e($list->nom_id); ?>" class="btn btn-primary getdata" data-toggle="modal" data-target="#assignsymbol" data-nomid="<?php echo e($list->nom_id); ?>" 
            data-candname="<?php echo e($list->cand_name); ?>"> Assign Symbol</button>  
          
      </div>
      <?php else: ?> 
        <small class="text-success mr-auto"><i>Symbol is already assigned</i></small>
      
      <!--<div class="btn-group float-right" role="group" aria-label="Basic example">   
        <small class="text-success btn"><i class="fa fa-check"></i> Already Assigned</small>      
        
      </div>-->
      <?php endif; ?>
      </div>
      </div>
      </div>

    </div>
    </div>
    
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <?php else: ?>
    <div class="norecords"><i class="fa fa-ban"></i><h4>No Records Found</h4></div>
  <?php endif; ?>
  </div>
<!-- ==========================-->
    
    
  
</div>
</section>
  <!-- Modal Content Starts here -->
    <!-- Modal -->
<div class="modal fade" id="assignsymbol" tabindex="-1" role="dialog" aria-labelledby="assignsymbol" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Assign Symbol</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form class="form-horizontal" id="election_form" method="POST" action="<?php echo e(url('ropc/updatesymbol')); ?>" >
                <?php echo e(csrf_field()); ?>   
         
      <input type="hidden" name="nom_id" id="nom_id" value="" readonly="readonly">
      <input type="hidden" name="candidate_id" id="candidate_id" value="" readonly="readonly">
    <div class="mb-3">Candidate Name:- <input type="text" name="candidate_name" id="candidate_name" value="" readonly="readonly"></div>
    <div class="mb-3">
    
      Select Symbol : - <span class="pagespanred">*</span></td> <td> 
            <select name="symbol" id="symbol" style="width:200px;">
             <option value="" selected="selected">Selected</option>
                         <?php $__currentLoopData = $sym; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <option value="<?php echo e($s->SYMBOL_NO); ?>"><?php echo e($s->SYMBOL_DES); ?>-<?php echo e($s->SYMBOL_HDES); ?></option>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
            </select> <span id="err" class="text-danger"></span>
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
           $("#election_form").submit(function(){
             if($("#symbol").val()=='')
                    {  
                    $("#err").text("");
                    $("#err").text("Please select symbol");
                    $("#symbol").focus();
                    return false;
                    }
               });
        });
  $(document).on("click", ".getdata", function () { 
       nomid = $(this).attr('data-nomid');
       canid = $(this).attr('data-canid'); 
       candname = $(this).attr('data-candname'); 
       $("#nom_id").val(nomid);
       $("#candidate_id").val(canid);
       $("#candidate_name").val(candname); 
   });

</script>
 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.pc.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/pc/ro/symboldetails.blade.php ENDPATH**/ ?>