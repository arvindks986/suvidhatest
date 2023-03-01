<?php $__env->startSection('title', 'Permission'); ?>
<?php $__env->startSection('content'); ?>

<main role="main" class="inner cover mb-3 mb-auto">

    <section>
        <?php if(session::has('msg')): ?>
                            <div class="alert alert-success">
                                <?php echo e(session()->get('msg')); ?>

                            </div>
                        <?php endif; ?>
        <?php if($total>0): ?>
        
        <div class="tabs-inner ">
            <div class="row d-flex align-items-md-stretch">
                <div class="col">
                    <ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Total Applied Permission (<?php echo e($total[0]->total); ?>)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Accepted Permission (<?php echo e($total[0]->Accepted); ?>)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Rejected Permission (<?php echo e($total[0]->Rejected); ?>)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-pending-tab" data-toggle="pill" href="#pills-pending" role="tab" aria-controls="pills-pending" aria-selected="false">Pending Permission (<?php echo e($total[0]->Pending); ?>)</a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" id="pills-cancle-tab" data-toggle="pill" href="#pills-cancle" role="tab" aria-controls="pills-cancel" aria-selected="false">Cancel Permission (<?php echo e($total[0]->cancle); ?>)</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="tabs-inner mt-5">
                <div class="row d-flex align-items-md-stretch">
                    <div class="col">
                        <ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Total Applied Permission (0)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Accepted Permission (0) </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Rejected Permission (0) </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-pending-tab" data-toggle="pill" href="#pills-pending" role="tab" aria-controls="pills-pending" aria-selected="false">Pending Permission (0)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-cancle-tab" data-toggle="pill" href="#pills-cancle" role="tab" aria-controls="pills-cancel" aria-selected="false">Cancel Permission (0)</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
         <?php endif; ?>
        <?php if(Session::has('message')): ?>
        <div class="alert alert-success">
            <?php echo e(session()->get('message')); ?>

        </div>
        <?php endif; ?>
        <?php if(count($errors)): ?>
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <br/>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </section>
    <section class="dashboard-header section-padding">
        <div class="container-fluid">
            <!-- <div class="line"></div> -->
            <div class="row">
                <div class="col">
                    <div class="tab-content tabular-pane" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($permissionDetails)): ?>
                                    <?php $__currentLoopData = $permissionDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <?php if($data->approved_status == 0): ?>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="<?php echo e(url('/getpermissiondetails')); ?>/<?php echo e($data->permission_id); ?>/<?php echo e($data->approved_status); ?>/<?php echo e($data->location_id); ?>"><?php echo e($data->permission_id); ?><i class="fa fa-edit float-right font-size01"></i></a></td>
                                        <?php elseif($data->approved_status == 1): ?>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="<?php echo e(url('/getpermissiondetails')); ?>/<?php echo e($data->permission_id); ?>/<?php echo e($data->approved_status); ?>/<?php echo e($data->location_id); ?>"><?php echo e($data->permission_id); ?><i class="fa fa-edit float-right font-size01"></i></a></td>
                                        <?php elseif($data->approved_status == 2): ?>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="<?php echo e(url('/getpermissiondetails')); ?>/<?php echo e($data->permission_id); ?>/<?php echo e($data->approved_status); ?>/<?php echo e($data->location_id); ?>"><?php echo e($data->permission_id); ?><i class="fa fa-edit float-right font-size01"></i></a></td>
                                        <?php elseif($data->approved_status == 3): ?>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="<?php echo e(url('/getpermissiondetails')); ?>/<?php echo e($data->permission_id); ?>/<?php echo e($data->approved_status); ?>/<?php echo e($data->location_id); ?>"><?php echo e($data->permission_id); ?><i class="fa fa-edit float-right font-size01"></i></a></td>
                                        <?php endif; ?>

                                        <td><?php echo e($data->permission_name); ?></td>
                                        <?php if(($data->permission_mode)==1): ?> 
                                        <td><b>Online</b></td>
                                        <?php else: ?>
                                        <td><b>Offline</b></td>
                                        <?php endif; ?>
                                        <td><?php echo e(GetReadableDateForm($data->created_at)); ?></td>
                                        <td>
                                            <div class="text-warning text-center">
                                                <?php if($data->approved_status == 0 && $data->cancel_status != 1): ?> <?php echo e('Pending'); ?>

                                                <?php elseif($data->approved_status == 1 && $data->cancel_status != 1): ?> <?php echo e('In progress'); ?>

                                                <?php elseif($data->approved_status == 2 && $data->cancel_status != 1): ?><?php echo e('Accept'); ?>

                                                <?php elseif($data->approved_status == 3 && $data->cancel_status != 1): ?><?php echo e('Reject'); ?>

                                                <?php elseif($data->cancel_status == 1): ?><?php echo e('Cancelled'); ?> 
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?> 
                                </tbody>

                            </table>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($permissionDetails)): ?>
                                    <?php $__currentLoopData = $permissionDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rdata): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($rdata->approved_status == 2 && $rdata->cancel_status != 1): ?>
                                    <tr>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="<?php echo e(url('getpermissiondetails')); ?>/<?php echo e($rdata->permission_id); ?>/<?php echo e($rdata->approved_status); ?>/<?php echo e($rdata->location_id); ?>"><?php echo e($rdata->permission_id); ?><i class="fa fa-edit float-right font-size01"></i></a></td>
                                        
                                        
                                        <td><?php echo e($rdata->permission_name); ?></td>
                                        <?php if(($rdata->permission_mode)==1): ?> 
                                        <td><b>Online</b></td>
                                        <?php else: ?>
                                        <td><b>Offline</b></td>
                                        <?php endif; ?>
                                        <td><?php echo e(GetReadableDateForm($rdata->created_at)); ?></td>
                                        <td>
                                            <div class="text-warning text-center">
                                                <?php if($rdata->approved_status == 2 && $rdata->cancel_status != 1): ?><?php echo e('Accept'); ?>

                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?> 
                                </tbody>

                            </table>
                        </div>


                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($permissionDetails)): ?>
                                    <?php $__currentLoopData = $permissionDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adata): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($adata->approved_status == 3 && $adata->cancel_status != 1): ?>
                                    <tr>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="<?php echo e(url('getpermissiondetails')); ?>/<?php echo e($adata->permission_id); ?>/<?php echo e($adata->approved_status); ?>/<?php echo e($adata->location_id); ?>"><?php echo e($adata->permission_id); ?><i class="fa fa-edit float-right font-size01"></i></a></td>
                                        
                                        
                                        <td><?php echo e($adata->permission_name); ?></td>
                                        <?php if(($adata->permission_mode)==1): ?> 
                                        <td><b>Online</b></td>
                                        <?php else: ?>
                                        <td><b>Offline</b></td>
                                        <?php endif; ?>
                                        <td><?php echo e(GetReadableDateForm($adata->created_at)); ?></td>
                                        <td>
                                            <div class="text-warning text-center">
                                                <?php if($adata->approved_status == 3 && $adata->cancel_status != 1): ?><?php echo e('Reject'); ?>

                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?> 
                                </tbody>

                            </table>
                        </div>
                        
                        <div class="tab-pane fade" id="pills-pending" role="tabpanel" aria-labelledby="pills-pending-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($permissionDetails)): ?>
                                    <?php $__currentLoopData = $permissionDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pendingdata): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($pendingdata->approved_status == 0 && $pendingdata->cancel_status != 1): ?>

                                    <tr>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="<?php echo e(url('getpermissiondetails')); ?>/<?php echo e($pendingdata->permission_id); ?>/<?php echo e($pendingdata->approved_status); ?>/<?php echo e($pendingdata->location_id); ?>"><?php echo e($pendingdata->permission_id); ?><i class="fa fa-edit float-right font-size01"></i></a></td>
                                       
                                        
                                        <td><?php echo e($pendingdata->permission_name); ?></td>
                                        <?php if(($pendingdata->permission_mode)==1): ?> 
                                        <td><b>Online</b></td>
                                        <?php else: ?>
                                        <td><b>Offline</b></td>
                                        <?php endif; ?>
                                        <td><?php echo e(GetReadableDateForm($pendingdata->created_at)); ?></td>
                                        <td>
                                            <div class="text-warning text-center">
                                               <?php if($pendingdata->approved_status == 0 && $pendingdata->cancel_status != 1): ?><?php echo e('Pending'); ?>

                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?> 
                                </tbody>

                            </table>
                        </div>
                        <div class="tab-pane fade" id="pills-cancle" role="tabpanel" aria-labelledby="pills-cancle-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($permissionDetails)): ?>
                                    <?php $__currentLoopData = $permissionDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cdata): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($cdata->cancel_status == 1): ?>

                                    <tr>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="<?php echo e(url('getpermissiondetails')); ?>/<?php echo e($cdata->permission_id); ?>/<?php echo e($cdata->approved_status); ?>/<?php echo e($cdata->location_id); ?>"><?php echo e($cdata->permission_id); ?><i class="fa fa-edit float-right font-size01"></i></a></td>
                                       
                                        
                                        <td><?php echo e($cdata->permission_name); ?></td>
                                        <?php if(($cdata->permission_mode)==1): ?> 
                                        <td><b>Online</b></td>
                                        <?php else: ?>
                                        <td><b>Offline</b></td>
                                        <?php endif; ?>
                                        <td><?php echo e(GetReadableDateForm($cdata->created_at)); ?></td>
                                        <td>
                                            <div class="text-warning text-center">
                                               <?php if($cdata->cancel_status == 1): ?><?php echo e('Cancelled'); ?>

                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?> 
                                </tbody>

                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.theme', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/politicalparty/permissionone.blade.php ENDPATH**/ ?>