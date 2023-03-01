<html>
  <head>
           <style>
    td {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #4a4646 !important;
    text-align: center;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
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

    .bordertestreport{
      border:1px solid #000;
    }
    .border{
    border-bottom: 1px solid #000;
    }
    th {
    background: #eff2f4;
    color: #000 !important;
    text-align: center;
    font-size: 11px;
    font-weight: bold !important;
    }
    
    table{
    width: 100%;
    }
    
    </style>
  </head>
  <div class="bordertestreport">
      <table class="border">
          <tr>
                <td style="text-align: left;">
                    <p> <img src="img/Cyber-Security-Logo.png" class="img-responsive" style="width:100px;" alt="">  </p>
                </td>
              <td style="text-align: right;">
                <p style="float: right;width: 100%;">ELECTION COMMISSION OF INDIA, <br>Nirvachan Sadan, Ashoka Road, New Delhi-110001
                 <br> General Elections, 2019 </p>
          </td>
      </tr>
  </table>

<table> 
<tr>  

<td style="font-size: 14px;">17.Statewise Seat Won and Valid votes polled by political party</td>
</tr>
</table>
  
  
  
            <table class="table table-bordered table-striped" style="width: 100%;">
              <thead>
                <tr class="table-primary">
                  <th scope="col">State Name</th>
                  <th scope="col">Party Type</th>
                  <th scope="col">Party NAme</th>
                  <th>Total Valid Votes Polled in the State</th>
                  <th>Total Electors in the State</th>
                  <th>Seats Won</th>
                  <th>Total Valid Votes Polled by Party</th>
                  <th>% Valid Votes Polled by Party</th>
           
                </tr>

               <?php 
               //dd($totalelectors);
               ?>
                @forelse($data as $row)
               <?php
                $validvotepolledbyparty=0;if($totalelectors->totalvalidvotes!=0)
				{
                $validvotepolledbyparty=round(((($row->totalvotes/$totalelectors->totalvalidvotes)*100)),2);
				}
                ?>
                <tr>
                     
                    <td>{{$stname}}</td>
                     <td>{{$row->PARTYTYPE}}</td>
                     <td>{{$row->PARTYABBRE}}</td>
                     <td>{{$totalelectors->totalvalidvotes}}</td>
                     <td>{{$totalelectors->totalelectors}}</td>
                     <td>{{$row->wonseat}}</td>
                     <td>{{$row->totalvotes}}</td>                     
                     <td>{{$validvotepolledbyparty}}</td>




</tr> 
@empty
<tr>
    <td colspan="8">Result No Found</td>
</tr>     
@endforelse
</tbody>
            </table>
          </div>

</html>