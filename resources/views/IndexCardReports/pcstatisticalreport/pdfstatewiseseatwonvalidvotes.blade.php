  <!DOCTYPE html>
<html>
   <head>


   </head>
   <body>
      <div class="headerreport">
         <div class="container-fluid">
            <div class="bordertestreport">
               <div class="col-sm-12">
                  <div class="row">
                     <div class="col-sm-9" style="display: grid;justify-content: right;">
                        <h4 style="font-size: 21px;font-family: 'poppinsregular';">Election Commission Of India, General Elections, 2019</h4>
                        <h5 style="font-family: 'poppinsregular'; font-weight: bold;text-align: center; font-size: 17px;text-decoration: underline;">( Party Wise Seat Won Valid Votes - 2019 )</h5>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <hr>
               </div>
               <div class="row">
                  <div class="col-sm-12">

                     <div class="col-sm-6" style="display: grid;justify-content: right;">
                        <p>(Year : 2019)</p>
                     </div>
                  </div>
               </div>
               <div class="">
                  <div class="">
                     <table class="table table-bordered table-striped" style="width: 100%;">
              <thead>


<tr class="table-primary">

  <th>State Name </th>
  <th>Party Type</th>
  <th>Party Name</th>
  <th>Total Valid Votes Polled in the State</th>
  <th>Total Electors in the State</th>
  <th>Seats Won</th>
  <th>Total Valid Votes Polled by Party</th>
  <th>% Valid Votes Polled By Party</th>
</tr>



</thead>

<tbody>
  @foreach($datanew as $value)

     <tr>

       <th>{{$value->st_name}}</th>
       <th>{{$value->lead_party_type}}</th>
       <th>{{$value->lead_cand_party}}</th>
       <th>{{$value->v_votes_evm_all}}</th>
       <th>{{$value->e_all_t}}</th>
       <th>{{$value->seatwon}}</th>
       <th>{{$value->totalvotebyparty}}</th>

       <th><?php  echo round($value->totalvotebyparty/$value->v_votes_evm_all *100,2);?></th>



     </tr>
   @endforeach

</tbody>
            </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>
