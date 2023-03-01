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
              <h3>No. oF Finalized PC</h3>

           </td>
           <td style="text-align: right;">
               <p style="float: right;width: 100%;"><strong>State :</strong> All India </p>
           </td>
       </tr>
   </table>
                <table class="table table-bordered table-striped" cellpadding="5" style="width: 100%;position: relative;top: -115px;">
                    <thead>

                <tr class="table-primary">
                  <th>S.No</th>
                  <th scope="col">State Name </th>
                  <th scope="col">No Of PCs</th>
                  <th scope="col">Finalised </th>
                  <th scope="col">Not Finalised Yet</th>
                </tr>
              </thead>
              <tbody>
                <?php //echo '<pre>';print_r($data);die;
                $count=1;?>
                
                @forelse($data as $row)
                <tr>
                  <td>{{$count}}.</td>
                  <td>{{$row['state']}}</td>
                  <td>{{$row['totalpc']}}</td>
                  <td>{{$row['finalize']}}</td>
				  <td>{{$row['totalpc']-$row['finalize']}}</td>

                </tr>
                 <?php $count++;?>
                @empty
                <tr>
                  <td colspan="3">No Record Found</td>
                </tr>
                
                @endforelse
					<tr><td></td>
				 <td>Total</td>
				 <td>{{$totalpc}}</td>
				 <td>{{$totalfinalize}}</td>
				 <td>{{$totalnotfinalize}}</td></tr>
                
              </tbody>
            </table>
          </div>

       
  
  </body>

