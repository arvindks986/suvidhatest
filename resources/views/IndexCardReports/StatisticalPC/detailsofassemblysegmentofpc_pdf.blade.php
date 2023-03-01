<html>
  <head>
    <style>
	@page {
            header: page-header;
            footer: page-footer;
        }
  
    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    padding: 6px;
    text-align: center;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
    .left-al tr td{
    text-align: left;
    }
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;
    color: #212529;
    }
    .blc{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-spacing: 0px 8px;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    }
    .border{
    border: 1px solid #000;
    }
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 12px;
    padding: 4px;
    font-weight: bold !important;
    }
    table{
    width: 100%;
    }
    </style>
  </head>
  <div class="bordertestreport">
      <table class="">
           <tr>
              <td style="text-align: center; font-weight: bold !important;"><p style="font-size: 12px;font-weight: bold;"><strong>Election Commission of India, Elections,2019 ( 17 LOK SABHA )</strong></p></td>
            </tr>
            
  </table>

  <table class="border">
     <tr><td style="text-align: center; font-weight: bold !important;">
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>34 - Details of Assembly Segments of Parliamentary Constituencies</b></p>
                  </td>
              </tr>

  </table>
    <table>
       <?php  if (verifyreport(34) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>
    </table>
    <br>
    <table class="table borders" style="width: 100%;">
      <thead>
        <tr>
          <th class="blcs" style="width: 40%;font-size: 14px;text-align: left;">Candidate No & Name </th>
          <th class="blcs" style="width: 26%; font-size: 14px;">Party</th>
          <th colspan="2" class="blcs" style="width: 22%;font-size: 14px;">Votes Secured</th>
        </tr>
      </thead>
      <tbody>
        @foreach($arrData as $key => $arrData1)
        <tr>
          <td  colspan="4" style="text-align:left;border-bottom: 1px dotted #000;font-size: 14px;"><b>State/UT Code & Name: </b>   {{$key}}</td>
        </tr>
        @foreach($arrData1 as $key1 => $arrData2)
        <tr>
          <td colspan="4" style="text-align:left;border-bottom: 1px dotted #000;font-size: 14px;"><b>PC No. & Name: </b>{{$key1}}</td>
        </tr>
        <?php
        $i = 1;
        ?>
        @foreach($arrData2 as $key2 => $raw1)
        <?php $total_vote = 0; ?>
        @foreach($raw1 as $key => $raw)
        <?php if($key == 0) { ?>
        <tr>
          <td  colspan="2" style="border-bottom: 1px dotted #000;text-align: left;"><b>AC Number and AC Name:</b>  {{$key2}}</td>
          <td colspan="2" style="text-align: left;border-bottom: 1px dotted #000;"><b>Electors: </b> ({{$raw['ac_electors']}})</td>
        </tr>
        <?php } ?>
        <?php   $datarawc = \App\models\Admin\VoterModel::get_candedates_votes_by_ac_no($raw['st_code'],$raw['pc_no'],$ac_no   = $raw['ac_no']); ?>
        @foreach($datarawc as $keyy => $raww)
        <tr>
          <td style="text-align: left;"> {{$keyy +1}}. {{$raww->candidate_name}}</td>
          <td>{{$raww->party_abbre}}</td>
          <td colspan="2" style="text-align:center;">{{$raww->total_vote}}</td>
        </tr>
        <?php $total_vote += $raww->total_vote; ?>
        @endforeach
        <?php
        $i++;
        ?>
        <?php $st_code = $raw['st_code']; ?>
        <?php $pc_no   = $raw['pc_no']; ?>
        <?php $ac_no   = $raw['ac_no']; ?>
        @endforeach
        <tr>
          <td  colspan="2" style="text-align:left;border-top: 1px dotted #000;"><b>Total Valid Votes for the AC :</b></td>
          <td colspan="2" style="text-align:center;border-top: 1px dotted #000;"><b>{{$total_vote}}</b></td>
        </tr>
        <?php $dataraw = \App\models\Admin\VoterModel::get_nota_votes_by_ac_no($st_code,$pc_no,$ac_no);?>
        <tr>
          <td colspan="2" style="text-align:left;"><b>NOTA Votes Polled(Excld. Postal Votes):</b></td>
          <td colspan="2" style="text-align:center;"><b>{{$dataraw[0]->total_vote}}</b></td>
        </tr>
        @endforeach
		<?php if($st_code == 'S09' && in_array($pc_no, [1,2,3]) ){ ?>
						
							<?php if($st_code == 'S09' && $pc_no == 1) { ?>
								<tr>
								   <td  colspan="2" style="text-align: center;"><b>AC Number and AC Name:</b>   88 - DelhiUdhampurJammu</td>
								   
								   <td colspan="2" style="text-align: center;"><b>Electors: </b> 0</td>
								   
								</tr>
							<?php }else if($st_code == 'S09' && $pc_no == 2){ ?>
									<tr>
									   <td  colspan="2" style="text-align: center;"><b>AC Number and AC Name:</b>   89 - DelhiUdhampurJammu</td>									   
									   <td colspan="2" style="text-align: center;"><b>Electors: </b> 0</td>	
									</tr>
							
							<?php }else if($st_code == 'S09' && $pc_no == 3){ ?>							
									<tr>
									   <td  colspan="2" style="text-align: center;"><b>AC Number and AC Name:</b>   90 - DelhiUdhampurJammu</td>									   
									   <td colspan="2" style="text-align: center;"><b>Electors: </b> 0</td>	
									</tr>							
							<?php } ?>
							
							
							<?php $dataraw2 = \App\models\Admin\VoterModel::get_migrante_by_pc_no($st_code,$pc_no); ?>
						
							@foreach($dataraw2 as $kk => $pdata)
								<tr>
								   <td> {{$kk +1}}. {{$pdata->candidate_name}}</td>
								   <td>{{$pdata->party_abbre}}</td>
								   <td colspan="2" style="text-align:center;">{{$pdata->migrate_votes}}</td>
								</tr>
								
							@endforeach
						
						
							<tr>							   
							   <td  colspan="2" style="text-align:center;"><b>Total Valid Votes for the AC :</b></td>
							   <td colspan="2" style="text-align:center;"><b></b></td>
							</tr>
							
							<tr>							   
							   <td colspan="2" style="text-align:center;"><b>NOTA Votes Polled(Excld. Postal Votes):</b></td>
							   <td colspan="2" style="text-align:center;"><b></b></td>
							</tr>
						
						<?php } ?>
        <tr>
          <td  colspan="4" style="text-align: left;"><b>Valid Postal Ballots for each candidate in the PC</b></td>
        </tr>
        <?php $dataraw2 = \App\models\Admin\VoterModel::get_postal_by_pc_no($st_code,$pc_no);
        $post_count = 0
        ?>
        @foreach($dataraw2 as $kk => $pdata)
        <tr>
          <td style="text-align: left;"> {{$kk +1}}. {{$pdata->candidate_name}}</td>
          <td>{{$pdata->party_abbre}}</td>
          <td colspan="2" style="text-align:center;">{{$pdata->postal_vote}}</td>
        </tr>
        <?php $post_count += $pdata->postal_vote; ?>
        @endforeach
        <tr>
          <td colspan="2" class="blc" style="text-align: left;"><b>Total Valid Postal Ballots for PC</b></td>
          <td colspan="2" style="text-align:center;" class="blc"><b>{{$post_count}}</b></td>
        </tr>
        <?php $dataraw3 = \App\models\Admin\VoterModel::get_total_valid_votes_by_pc_no($st_code,$pc_no); ?>
        <tr>
          <td style="text-align: left;"><b>Total Valid Votes for PC</b></td>
          <td><b>{{$key1}}</b></td>
          <td colspan="2" style="text-align:center;"><b>{{$dataraw3[0]->total_vote}}</b></td>
        </tr>
        <?php $dataraw4 = \App\models\Admin\VoterModel::get_nota_potal_votes_by_pc_no($st_code,$pc_no); ?>
        <tr>
          <td colspan="2" class="blc"><b>NOTA Postal Votes : </b></td>
          <td colspan="2" style="text-align:center;" class="blc"><b>{{$dataraw4[0]->postal_vote}}</b></td>
        </tr>
        @endforeach
        <?php $dataraw5 = \App\models\Admin\VoterModel::get_total_valid_votes_by_st_code($st_code); ?>
        <tr>
          <td colspan="2" style="border-top: 1px dotted #000;border-bottom: 1px dotted #000;"><b>Total Valid Votes for the State/UT : </b></td>
          <td colspan="2" style="border-top: 1px dotted #000;border-bottom: 1px dotted #000;text-align: center;"><b>{{$dataraw5[0]->total_vote}}</b></td>
        </tr>
        @endforeach
        <?php $dataraw6 = \App\models\Admin\VoterModel::get_total_valid_votes_by_all(); ?>
        <tr>
          <td colspan="2" style="border-bottom: 1px dotted #000;"><b>Total Valid Votes for the Country : </b></td>
          <td colspan="2" style="border-bottom: 1px dotted #000;text-align: center;"><b>{{$dataraw6[0]->total_vote}}</b></td>
        </tr>
      </tbody>
    </table>
  </div>


 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>




<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 <?php if (verifyreport(34) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>



</html>