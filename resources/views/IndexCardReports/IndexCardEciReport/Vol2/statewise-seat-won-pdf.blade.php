<html>
  <head>
      <style>
        @page { sheet-size: A4-L; }
@page bigger { sheet-size: 420mm 370mm; }
@page toc { sheet-size: A4; }
@page {
            header: page-header;
            footer: page-footer;
        }

        td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: left;
    padding: 10px;
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

   
    .border{
    border: 1px solid #000;
    }
    th {
    color: #000 !important;
    font-size: 12px;
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
                        <p style="font-size: 20px !important; text-transform: uppercase;"><b>17 - State Wise Seat Won & Valid Votes Polled  by Political Parties </b></p>
                  </td>
              </tr>


</table>
  <table>
     <?php  if (verifyreport(17) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } ?>


  </table> 



  
  
            <table class="table table-bordered table-striped" style="width: 100%;">
                <thead>
               <tr class="">
                  <th scope="col">State Name</th>
                  <th scope="col">PartyType</th>
                  <th scope="col">Party Name</th>
                  <th>Total Valid Votes <br> Polled in the State</th>
                  <th>Total Electors in the State</th>
                  <th>Seats Won</th>
                  <th>Total Valid Votes <br> Polled by Party</th>
                  <th>% Valid Votes <br> Polled by Party</th>
           
                </tr>
                </thead>
                <?php //echo'<pre>';print_r($data);die;?>
 			    @forelse($getuserrecord as $row)
               <?php
                $validvotepolledbyparty=0;
               
				if($row->totalvalid_st_vote!=0)
				{
                $validvotepolledbyparty= ROUND((($row->totalvalidvote/$row->totalvalid_st_vote)*100),2);
				}

                ?>
                <tr style="background: #fff !important;">
                     
                    <td>{{$row->ST_NAME}}</td>
                     <td style="text-align: center;">{{$row->PARTYTYPE}}</td>
                     <td>{{$row->PARTYNAME}}</td>
                     <td>{{$row->totalvalid_st_vote}}</td>
                     <td>{{$row->totaleelctors}}</td>                     
                     	<td>{{$row->win}}</td>                    
                     <td>{{$row->totalvalidvote}}</td>                     
                     <td>{{$validvotepolledbyparty}}</td>




</tr> 
@empty
<tr>
    <td colspan="8">Result No Found</td>
</tr>     
@endforelse
{{-- $getuserrecord->links()--}}



            </table>
          </div>



 <h4 style="border-top: 2px solid #000;padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>



		<htmlpagefooter name='page-footer'>
 <table>
 <tr>
 <?php if (verifyreport(17) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>
 </htmlpagefooter>


</html>