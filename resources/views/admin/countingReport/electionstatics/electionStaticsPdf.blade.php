<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Election Statistics</title>   
    </head>
    <body>
        <!--HEADER STARTS HERE-->
        <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
            <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br>
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
            </thead>
        </table>
        <!--HEADER ENDS HERE-->
        <style type="text/css">
            .table-strip{border-collapse: collapse;}
            .table-strip th,.table-strip td{text-align: center;}
        </style>
        <table style="width:100%; border: 1px solid #000;" border="0" align="center"> 

            <tr>
                <td  style="width:50%;">
                    <table  style="width:100%">
                        <tbody>

                            <tr>  
                                <td><strong>User:</strong> {{$user_data->placename}}</td>
                            </tr>
                        </tbody>
                    </table>  
                </td>
                <td  style="width:50%">
                    <table style="width:100%">
                        <tbody>
                            <tr>
                                <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
                            </tr>
                            <tr>  
                                <td align="right">&nbsp;</td>
                            </tr> 
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <table class="table-strip" style="width: 100%;" border="1" align="center">
            <tbody>
                <tr>
                    <td align="center" ><strong>Election Statistics</strong></td>
                </tr>

            </tbody>
        </table>

        <table class="table-strip"  border="1" style="width: 100%;" align="center;">   
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">S No</th>
                                                        <th rowspan="2">State Name</th>
                                                        <th rowspan="2">Total Electors</th>
                                                        <th colspan="4">Total Voter turnout</th>
                                                        <th colspan="4">Total Votes Casted</th>
                                                    </tr>

                                                    <tr>

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
                                                        <td></td>
                                                        <td align="center">1</td>
                                                        <td align="center">2</td>
                                                        <td align="center">3</td>
                                                        <td align="center">4</td>
                                                        <td align="center">5</td>
                                                        <td align="center">6</td>
                                                        <td align="center">7</td>
                                                        <td align="center">8</td>
                                                        <td align="center">9</td>
                                                        <td align="center">10</td>					 
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
                                                        $voter_other = $voter_other + $v->voter_other;
                                                        $total_voters = $total_voters + $v->total_voters;
                                                        $evm_vote = $evm_vote + $v->evm_vote;
                                                        $postal_vote = $postal_vote + $v->postal_vote;
                                                        $migrate_votes = $migrate_votes + $v->migrate_votes;
                                                        $total_actual_votes = $total_actual_votes + $v->total_actual_votes;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $i;?></td>
                                                        <td><?php echo str_replace(' & ','&nbsp;&&nbsp;',$v->st_name);?></td>
                                                        <td align="right"><?php echo $v->electors_total;?></td>
                                                        <td align="right"><?php echo $v->voter_male;?></td>
                                                        <td align="right"><?php echo $v->voter_female;?></td>
                                                        <td align="right"><?php echo $v->voter_other;?></td>
                                                        <td align="right"><?php echo $v->total_voters;?></td>
                                                        <td align="right"><?php echo $v->evm_vote;?></td>
                                                        <td align="right"><?php echo $v->postal_vote;?></td>
                                                        <td align="right"><?php echo $v->migrate_votes;?></td>				 
                                                        <td align="right"><?php echo $v->total_actual_votes;?></td>				 
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
        <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
                </tr>
            </tbody>
        </table>
    </body>
</html>

