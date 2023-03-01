@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Contested Candidate Profile')
@section('content')

<main class="mb-auto">

    <div class="loader" style="display:none;"></div>
    <section class="statistics color-grey pt-4 pb-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 pull-left">
                    <h4>Contested Candidate Profile</h4>
                </div>
                <div class="col-md-4 pull-left">
                    <?php 
                    
                    ?>
                    <select name="win_status" id="win_status" class="form-control" onchange="getRecord(this.value);">
                                    <option value="">Select</option>
                                    <option value="winner" <?php echo $win;?>>Winner</option>
                                    <option value="loser" <?php echo $los;?>>Loser</option>
                                    <option value="all" <?php echo $all;?>>All</option>
                            </select>
                </div>
                <div class="col-md-4 pull-right">
                    <span class="report-btn pull-right" id="export-csv-btn"><a class="btn btn-primary" href='{{url("/eci/candidate-profile-excel/$win_status")}}' title="Download Excel" onclick="DownloadExcel();" target="_blank">Export Excel</a></span>
                </div>
                
                        
                </div>

            </div>
        </div>
    </section>

    <div class="container-fluid">
        <!-- Start parent-wrap div -->  
        <div class="parent-wrap">
            <!-- Start child-area Div --> 
            <div class="child-area">
                <div class="page-contant">
                    <div class="random-area">
                        <br>
                        <div class="row"><div class="col-sm-12">
                                            <table id="example" class="table table-bordered dataTable no-footer" style="width: 100%;" role="grid" aria-describedby="example_info">   
                                                <thead>
                                                    <tr style="border-bottom:3px solid #000;">
                                                        <th>SL NO.</th>
                                                        <th>State</th>
                                                        <th>PC Name</th>
                                                        <th>Candidate Id</th>				   
                                                        <th>Candidate Name</th>				                      
                                                        <th>Candidate Father Name</th>
                                                        <th>Candidate Mobile</th>
                                                        <th>Candidate Email</th>
                                                        <th>Candidate Gender</th>
                                                        <th>Candidate Age</th>				   
                                                        <th>Party Name</th>				                      				   
                                                        <th>Winning Status</th>				                      				   
                                                        <th>EVM Vote</th>				                      				   
                                                        <th>Postal Vote</th>				                      				   
                                                        <th>Migrate Vote</th>				                      				   
                                                        <th>Total Vote</th>				                      				   
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $evm_vote = 0;
                                                    $postal_vote = 0;
                                                    $migrate_votes = 0;
                                                    $total_actual_votes = 0;
                                                    if(count($record)>0){
                                                    $i=1;
                                                    foreach($record as $k=>$v){
                                                        $evm_vote = $evm_vote + $v->evm_vote;
                                                        $postal_vote = $postal_vote + $v->postal_vote;
                                                        $migrate_votes = $migrate_votes + $v->migrate_votes;
                                                        $total_actual_votes = $total_actual_votes + $v->total_vote;
                                                    ?>
                                                    <tr>
                                                        <td border="1"><?php echo $i;?></td>
                                                        <td border="1"><?php echo $v->st_name;?></td>
                                                        <td border="1"><?php echo $v->pc_name;?></td>
                                                        <td border="1"><?php echo $v->candidate_id;?></td>
                                                        <td border="1"><?php echo $v->candidate_name;?></td>
                                                        <td border="1"><?php echo $v->candidate_father_name;?></td>
                                                        <td border="1"><?php echo $v->cand_email;?></td>
                                                        <td border="1"><?php echo $v->cand_mobile;?></td>
                                                        <td border="1"><?php echo $v->cand_gender;?></td>
                                                        <td border="1"><?php echo $v->cand_age;?></td>
                                                        <td border="1"><?php echo $v->party_name;?></td>				 
                                                        <td border="1"><?php echo ucfirst($v->winning_status);?></td>				 
                                                        <td border="1" align="right"><?php echo $v->evm_vote;?></td>				 
                                                        <td border="1" align="right"><?php echo $v->postal_vote;?></td>				 
                                                        <td border="1" align="right"><?php echo $v->migrate_votes;?></td>				 
                                                        <td border="1" align="right"><?php echo $v->total_vote;?></td>				 
                                                    </tr>
                                                    
                                                    <?php $i++; }}else{?>
                                                    <tr colspan="11">
                                                        <td colspan="11" style="text-align: center;">No record found.</td>
                                                    </tr>
                                                    <?php }?>                                                    
                                                </tbody>
                                                
                                            </table>
                                        </div>
                                    </div>


                    </div><!-- End Of intra-table Div -->   
                </div><!-- End Of random-area Div -->

            </div><!-- End OF page-contant Div -->
        </div>      
    </div><!-- End Of parent-wrap Div -->


</main>
<script>
    function getRecord(status){
        if(status !=''){
            document.location.href='<?php echo url('/'); ?>/eci/candidate-profile/'+status;
        }
    }
</script>
@endsection
