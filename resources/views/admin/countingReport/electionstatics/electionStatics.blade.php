@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Election Statistics')
@section('content')

<main class="mb-auto">

    <div class="loader" style="display:none;"></div>
    <section class="statistics color-grey pt-4 pb-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9 pull-left">
                    <h4>Election Statistics</h4>
                </div>
                <div class="col-md-3  pull-right text-right report_section" style="">
                        <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="{{url('/eci/election-statistics-excel')}}" title="Download Excel" onclick="DownloadExcel();" target="_blank">Export Excel</a></span>
                    <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('/eci/election-statistics-pdf')}}"  title="Download PDF" target="_blank">Export PDF</a></span>
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
                        <div id="datashow" class="head-title" style="">
                            <div class="table-responsive">
                                <div id="example_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">       
                                    <div class="row"><div class="col-sm-12">
                                            <table class="table-strip"  border="1" style="width: 100%;" align="center;">   
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2" style="border-bottom:3px solid #000;">S No</th>
                                                        <th rowspan="2" style="border-bottom:3px solid #000;">State Name</th>
                                                        <th rowspan="2" style="border-bottom:3px solid #000;">Total Electors</th>
                                                        <th colspan="4">Total Voter turnout</th>
                                                        <th colspan="4">Total Votes Casted</th>
                                                    </tr>

                                                    <tr style="border-bottom:3px solid #000;">

                                                        <th>Male</th>
                                                        <th>Female</th>
                                                        <th>Other</th>				   
                                                        <th>Total Voters</th>				                      
                                                        <th>Evm Vote</th>
                                                        <th>Postal Vote</th>
                                                        <th>Migrant Vote</th>				   
                                                        <th>Total Actual Votes</th>				                      				   
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(count($record)>0){?>
                                                    <tr>
                                                        <td border="1"></td>
                                                        <td border="1" align="center">1</td>
                                                        <td border="1" align="center">2</td>
                                                        <td border="1" align="center">3</td>
                                                        <td border="1" align="center">4</td>
                                                        <td border="1" align="center">5</td>
                                                        <td border="1" align="center">6</td>
                                                        <td border="1" align="center">7</td>
                                                        <td border="1" align="center">8</td>
                                                        <td border="1" align="center">9</td>
                                                        <td border="1" align="center">10</td>					 
                                                    </tr>
                                                    <?php }
                                                    $electors_total = 0;
                                                    $voter_male = 0;
                                                    $voter_female = 0;
                                                    $voter_other = 0;
                                                    $total_voters = 0;
                                                    $evm_vote = 0;
                                                    $postal_vote = 0;
                                                    $migrate_votes = 0;
                                                    $total_actual_votes = 0;
                                                    if(count($record)>0){
                                                    $i=1;
                                                    foreach($record as $k=>$v){
                                                        $electors_total = $electors_total + $v->electors_total;
                                                        $voter_male = $voter_male + $v->voter_male;
                                                        $voter_female = $voter_female + $v->voter_female ;
                                                        $voter_other = $voter_other + $v->total_voters;
                                                        $total_voters = $total_voters + $v->total_voters;
                                                        $evm_vote = $evm_vote + $v->evm_vote;
                                                        $postal_vote = $postal_vote + $v->postal_vote;
                                                        $migrate_votes = $migrate_votes + $v->migrate_votes;
                                                        $total_actual_votes = $total_actual_votes + $v->total_actual_votes;
                                                    ?>
                                                    <tr>
                                                        <td border="1"><?php echo $i;?></td>
                                                        <td border="1"><?php echo $v->st_name;?></td>
                                                        <td border="1" align="right"><?php echo $v->electors_total;?></td>
                                                        <td border="1" align="right"><?php echo $v->voter_male;?></td>
                                                        <td border="1" align="right"><?php echo $v->voter_female;?></td>
                                                        <td border="1" align="right"><?php echo $v->voter_other;?></td>
                                                        <td border="1" align="right"><?php echo $v->total_voters;?></td>
                                                        <td border="1" align="right"><?php echo $v->evm_vote;?></td>
                                                        <td border="1" align="right"><?php echo $v->postal_vote;?></td>
                                                        <td border="1" align="right"><?php echo $v->migrate_votes;?></td>				 
                                                        <td border="1" align="right"><?php echo $v->total_actual_votes;?></td>				 
                                                    </tr>
                                                    
                                                    <?php $i++; }}else{?>
                                                    <tr colspan="11">
                                                        <td colspan="11" style="text-align: center;">No record found.</td>
                                                    </tr>
                                                    <?php }?>                                                    
                                                </tbody>
                                                <thead>
                                                    <tr style="border-bottom:3px solid #000; border-top:3px solid #000;">
                                                        <th></th>
                                                        <th>Grand Total</th>				   
                                                        <th style="text-align: right">{{$electors_total}}</th>
                                                        <th style="text-align: right">{{$voter_male}}</th>
                                                        <th style="text-align: right">{{$voter_female}}</th>
                                                        <th style="text-align: right">{{$voter_other}}</th>
                                                        <th style="text-align: right">{{$total_voters}}</th>				   
                                                        <th style="text-align: right">{{$evm_vote}}</th>				                      
                                                        <th style="text-align: right">{{$postal_vote}}</th>
                                                        <th style="text-align: right">{{$migrate_votes}}</th>
                                                        <th style="text-align: right">{{$total_actual_votes}}</th>				   
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div><!-- End Of intra-table Div -->   


                </div><!-- End Of random-area Div -->

            </div><!-- End OF page-contant Div -->
        </div>      
    </div><!-- End Of parent-wrap Div -->


</main>
@endsection
