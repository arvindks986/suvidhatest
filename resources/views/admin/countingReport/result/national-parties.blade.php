@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha PC')
@section('bradcome', 'Winning Candidate Details')
@section('content')
<style type="text/css">
.main{
		width:892.98px;
		background-color:#fff;
		margin:0 auto;
		padding:0;
		padding-bottom:40px;
	}	
	table{
		max-width:800px;
		margin:0 auto;
		width:100%;
	}	
	.hr{
	     border:0;
     	 border-top:1px solid #000;
		 padding-top:5px; 
		 font-weight:bold;
		 border-bottom:1px solid #000;		 
	}
		  
	.h1{
	 font-weight:bold;	
	 padding:5px;
	 text-transform:uppercase;
	 font-size:28px;
	 margin-top:0;
	 text-align:center;
	}	
   .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center; padding:8px; font-size:14px;}
        	
</style>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
 <div class="row">
  
     <div class="col-md-12  pull-right text-right report_section">
         <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('/eci/national-parties-performance-pdf')}}" title="Download PDF" >Export PDF</a></span>
     </div>

 </div>
</div>
</section>

<div class="main">
			<p style="text-align:center; padding-top:30px; margin-bottom:0">Election Commission Of india, General Election, 2019(17th LOK SABHA)</p>	
			<h1 class="h1">20 - performance of national Parties</h1>
        <table class="table-strip"  border="1" style="padding:30px 40px 0;" align="center;">   
            <thead>
			  <tr>
                   <th colspan="4"><span style="font-style:italic; border-bottom:1px solid #000;">Candidates</span></th>
                   <th>Votes</th>

                   <th colspan="2" rows="2"><span style="font-style:italic; border-bottom:1px solid #000;">% of votes secured</span></th>
                      
                </tr>
                <tr style="border-bottom:3px solid #000;">
                   <th>Party Name</th>
                   <th>Contested</th>
                   <th>Won</th>
                   <th>DF</th>
                   <th>Secured by<br/>Party</th>
                   <th>Over total<br/>electors</th>				   
                   <th>Over total valid<br/>votes Polled</th>				                      
                </tr>
            </thead>
            <tbody>
                <?php
                $tot_cont = 0;
                $tot_won = 0;
                $tot_df = 0;
                $tot_votes = 0;
                $tot_per_ele=0;
                $tot_per_valid_pol = 0;
                $ele_per = 0;
                $votes_per = 0;
                $tot_votes_polled_per = 0;
                if(count($record)>0){
                    
                 foreach($record as $k=>$v){
                     $tot_cont = $tot_cont + $v->contested;
                     $tot_won = $tot_won + $v->won;
                     $tot_votes = $tot_votes + $v->total_vote;
                     $ele_per = ($v->total_vote*100)/$tot_electors;
                     $tot_per_ele = $tot_per_ele + $ele_per;
                     $votes_per = ($v->total_vote*100)/$tot_valid_polled_votes;
                     $tot_votes_polled_per = $tot_votes_polled_per + $votes_per;
                 ?>
                <tr>
                     <td border="1"><?php echo $v->partyname;?></td>
                     <td border="1"><?php echo $v->contested;?></td>
                     <td border="1"><?php echo $v->won;?></td>
                     <td border="1">0</td>
		     <td border="1"><?php echo $v->total_vote;?></td>
                     <td border="1"><?php echo round($ele_per,2);?></td>
                     <td border="1"><?php echo round($votes_per,2);?></td>				 
                </tr>
                 <?php }}else{?>
                 <tr colspan="7">
                     <td border="1">No record found.</td>			 
                </tr>
                 <?php }
                
                 ?>
            </tbody>
			<thead>
                <tr style="border-bottom:3px solid #000; border-top:3px solid #000;">
                   <th>Grand Total</th>
                   <th><?php echo $tot_cont;?></th>
                   <th><?php echo $tot_won;?></th>
                   <th><?php echo $tot_df;?></th>
                   <th><?php echo $tot_votes;?></th>
                   <th><?php echo round($tot_per_ele);?></th>				   
                   <th><?php echo round($tot_votes_polled_per);?></th>				                      
                </tr>
            </thead>
            <tbody>
				<tr style="background-color:transparent;">
                     <td colspan="6" style="text-transform:uppercase; font-weight:bold; font-size:14px">Total Electors in the country (Including Service Electors)</td>
                     <td border="1" style="font-weight:bold;"><?php echo $tot_electors;?></td>
                </tr>
				<tr>
                     <td colspan="6" style="text-transform:uppercase; font-weight:bold; font-size:14px">Total Valid votes polled in the country (Including Service Votes)</td>
                     <td border="1" style="font-weight:bold;"><?php echo $tot_valid_polled_votes;?></td>
                </tr>			
            </tbody>
        </table>
    
	  </div>

@endsection
