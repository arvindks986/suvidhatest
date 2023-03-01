<html>
  <head>
      <style>
    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #4a4646 !important;
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
    font-size: 13px;
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
                <td>
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
          <td>
             <h3>31.Winning Candidate Analysis Over Total Electors</h3>

          </td>
          <td style="text-align: right;">
          </td>
      </tr>
  </table>


                    <table class="table table-bordered table-striped" style="width: 100%;">

                       <thead>                          
                    <tr class="table-primary">
                  <th scope="col">Name of State/UT</th>
                  <th scope="col">No. Of Seats</th>
                  <th colspan="8">No. Of Candidates Secured The % Of Votes Over The Total Electors In The Constituency</th>
           
                </tr>
                <tr>
                     <th></th>
                    <th></th>

                    <th>Winner with <= 10%</th>
                    <th>Winner with >10% to <= 20%</th>
                    <th>Winner with >20% to <=30%</th>
                    <th>Winner with >30% to <=40%</th>
                    <th>Winner with >40% to <=50%</th>
                    <th>Winner with >50% to <=60%</th>
                    <th>Winner with >60% to <=70%</th>
                    <th>Winner with > 70%</th>
</tr>
                </thead>



                        <tbody>
  
                    

@forelse($arrayData as $values)


<tr>
    <td>{{$values['stname']}}</td>
    <td>{{$values['totalseat']}}</td>
    <td><?php echo ($values['count']!=0)?$values['count']['10']:0; ?></td>
    <td><?php echo ($values['count']!=0)?$values['count']['20']:0; ?></td>
    <td><?php echo ($values['count']!=0)?$values['count']['30']:0; ?></td>
    <td><?php echo ($values['count']!=0)?$values['count']['40']:0; ?></td>
    <td><?php echo ($values['count']!=0)?$values['count']['50']:0; ?></td>
    <td><?php echo ($values['count']!=0)?$values['count']['60']:0; ?></td>
    <td><?php echo ($values['count']!=0)?$values['count']['70']:0; ?></td>
    <td><?php echo ($values['count']!=0)?$values['count']['80']:0; ?></td>
   
    
</tr>
@empty
<tr>
    <td>Data Not Found</td>
    
</tr>
@endforelse
                </tbody>
                
                    </table>
                </div>

                </html>