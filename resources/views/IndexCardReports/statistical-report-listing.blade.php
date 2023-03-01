@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'PC Wise Statistical Report')
@section('content')


<style>

img#theImg{
    display: none;
}

.fa-eye:before {
    content: "\f06e";
    color: #f15d86;
    font-size: 20px;
    margin: auto;
}
td.dev {
    display: flex;
    max-width: 100%;
    height: 48px;
}

.dev input.checkbox-md.mr-2 {
    width: 18px;
    height: 17px;
        opacity: 0.8;


}


</style>
<?php //$st=getstatebystatecode($user_data->st_code);   ?>
<section class="">

  <!-- Trigger the modal with a button -->

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
 <div class="modal-dialog">
   <!-- Modal content-->
   <div class="modal-content">
    <!--  <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal">&times;</button>
       <h4 class="modal-title"></h4>
     </div> -->
     <div class="modal-body">
       <p>All reports are Click to final preview. Please verify all reports then click Confirm Report Verification Button</p>
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
     </div>
   </div>
 </div>
</div>

<!-- Modal -->
<div id="myModalnew" class="modal fade" role="dialog">
 <div class="modal-dialog">
   <!-- Modal content-->
   <div class="modal-content">
    <!--  <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal">&times;</button>
       <h4 class="modal-title"></h4>
     </div> -->
     <div class="modal-body">
       <p>Once verification confirmed all the editing in the data will be disabled</p>
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
     </div>
   </div>
 </div>
</div>

    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019</h4></div>


                         <?php if(verifyreport(777) == 0){ ?>
                       <div class="col"><a><button type="button" id="btnSubmitToCheckOut" class="btn btn-info float-right" onclick="verifycheck(777)">Confirm Report Verification</button></a></div>
                      <?php } { ?>
                          <div class="col"> All Reports Finalised On: {{date('d-m-Y h:i A', strtotime(verifyreportdate(777)))}}</div>
                      <?php } ?>

                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive" style="width: 100%;">
                        <!-- Content goes Here -->



                        <div class="col-sm-12 text-center">
                            <h5>Statistical Reports</h5>
                        </div>
                        <table class="table table-bordered tablecenterreport">
                            <thead>
                            <th>SL. No.</th>
                            <th>Report Name</th>
                            <th><p style="">View Report</p></th>
                            <th><p style="">Check for final preview</p></th>
                            
                            </thead>
                            <tbody>

                           



                                    <tr><td>1.</td>
                                    <td><a href="scheduleloksabhahighlights"  target="_blank">The Schedule of GE To Lok Sabha
                                        </a></td>

                                        <?php  if (verifyreport(1) != 0){ ?>
                                          <td class="text-center">
                                            <a href="scheduleloksabhahighlights" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="scheduleloksabhahighlights" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>



                                      <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,1)" <?php  if (verifyreport(1) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(1) != 0){ ?>
                                            <div class="w-75"></div>
                                                <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                          <?php } ?>
                                       </td>



                                </tr>

                                <tr><td>2.</td>
                                    <td><a href="highlights" target="_blank"> Highlights</a></td>
                                        <?php  if (verifyreport(2) != 0){ ?> 
                                       <td class="text-center">
                                          <a href="highlights" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                       </td>
                                        <?php   } else { ?>
                                          <td class="text-center">
                                            <a href="highlights" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>

                                        


                                     <td class="dev">
                                        <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,2)" <?php  if (verifyreport(2) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                          <?php  if (verifyreport(2) != 0){ ?>
                                        <div  class="w-75" style=""></div>
                                         <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                      </td>
                                      
                                </tr>

                                <tr><td>3.</td>
                                    <td><a target="_blank" href="listofpoliticalpartiesparticipated">List of Political Parties Participated</a></td>
                                     <?php  if (verifyreport(3) != 0){ ?>
                                          <td class="text-center">
                                            <a href="listofpoliticalpartiesparticipated" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="listofpoliticalpartiesparticipated" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
                                        <td class="dev">
                                          <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,3)" <?php  if (verifyreport(3) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                            <?php  if (verifyreport(3) != 0){ ?>

                                          <div  class="w-75" class="btn btn-info"></div>
                                           <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                        </td>
                                        
                                </tr>



                                  <tr>
                                    <td>4.</td>
                                    <td><a target="_blank" href="List-of-successfull-candidate">List of Successful Candidate
                                        </a></td>

                                        <?php  if (verifyreport(4) != 0){ ?>
                                          <td class="text-center">
                                            <a href="List-of-successfull-candidate" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="List-of-successfull-candidate" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
                                    
                                         <td class="dev">

                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,4)" <?php  if (verifyreport(4) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(4) != 0){ ?>

                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>

                                        </td>
                                        
                                </tr>



                                <tr>
                                    <td>5.</td>
                                    <td><a target="_blank" href="numberandtypesofconstituencies">Number & Type of Constituencies
                                        </a></td>

                                         <?php  if (verifyreport(5) != 0){ ?>
                                          <td class="text-center">
                                            <a href="numberandtypesofconstituencies" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="numberandtypesofconstituencies" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>



                                       <td class="dev">
                                          <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,5)" <?php  if (verifyreport(5) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                            <?php  if (verifyreport(5) != 0){ ?>

                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                <tr>




  <tr>
                                    <td>6.</td>
                                    <td><a target="_blank" href="statewisecandidatedatasummary">State Wise Candidate Data Summary

                                        </a></td>


                                        <?php  if (verifyreport(6) != 0){ ?>
                                          <td class="text-center">
                                            <a href="statewisecandidatedatasummary" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="statewisecandidatedatasummary" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>


                                             <td class="dev">

                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,6)" <?php  if (verifyreport(6) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(6) != 0){ ?>

                                            <div  class="w-75" class="btn btn-info"></div>

                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>

                                          </td>
                                <tr>



                                  @php if($user_data->role_id=='7') { @endphp

                                <tr><td>7.</td>

                                    <td><a target="_blank" href="{{url('/eci/indexcardview/ConstituencyWiseSummary')}}"> Constituency Wise Summary
                                        </a></td>


                                       

                                        <?php  if (verifyreport(7) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{{url('/eci/indexcardview/ConstituencyWiseSummary')}}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{{url('/eci/indexcardview/ConstituencyWiseSummary')}}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>




                                       <td class="dev">

                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,7)" <?php  if (verifyreport(7) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(7) != 0){ ?>

                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>

                                        </td>
                                </tr>

                                @php } else { @endphp

                                <tr><td>7.</td>

                                    <td><a target="_blank" href="{{url('/eci-index/indexcardview/ConstituencyWiseSummary')}}">Constituency Wise Summary
                                        </a></td>

                                        <?php  if (verifyreport(7) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{{url('/eci/indexcardview/ConstituencyWiseSummary')}}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{{url('/eci/indexcardview/ConstituencyWiseSummary')}}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>
                                       
                                       <td class="dev">

                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,7)" <?php  if (verifyreport(7) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(7) != 0){ ?>

                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>

                                        </td>
                                </tr>

                                @php } @endphp





                                      <tr>
                                    <td>8.</td>
                                    <td><a target="_blank" href="noofcandidateperconsitituency">Number of Candidates per Constituency

                                        </a></td>

                                           <?php  if (verifyreport(8) != 0){ ?>
                                          <td class="text-center">
                                            <a href="noofcandidateperconsitituency" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="noofcandidateperconsitituency" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>


                                         <td class="dev">

                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,8)" <?php  if (verifyreport(8) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(8) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                <tr>

                                <tr><td>9.</td>
                                    <td><a target="_blank" href="statewisenumberelectors"> State Wise Number of Electors
                                        </a></td>
                                             <?php  if (verifyreport(9) != 0){ ?>
                                          <td class="text-center">
                                            <a href="statewisenumberelectors" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="statewisenumberelectors" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>


                                        <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,9)" <?php  if (verifyreport(9) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(9) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>


                                <tr><td>10.</td>
                                    <td><a target="_blank" href="voterInformation">Voters Information
                                        </a></td>

                                              <?php  if (verifyreport(10) != 0){ ?>
                                          <td class="text-center">
                                            <a href="voterInformation" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="voterInformation" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>


                                      <td class="dev">

                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,10)" <?php  if (verifyreport(10) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(10) != 0){ ?>

                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>




                                <tr>
                                    <td>11.</td><td><a target="_blank" href="State-wise-overseas-electors-voters">State Wise Participation of Overseas Electors
</a></td>

                                          <?php  if (verifyreport(11) != 0){ ?>
                                          <td class="text-center">
                                            <a href="State-wise-overseas-electors-voters" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="State-wise-overseas-electors-voters" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>



                                <td class="dev">
                                    <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,11)" <?php  if (verifyreport(11) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                      <?php  if (verifyreport(11) != 0){ ?>
                                    <div  class="w-75" class="btn btn-info"></div>
                                       <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                  </td>

                                  </tr>



                                <tr>
                                    <td>12.</td><td><a target="_blank" href="statewisevoterturnout"> State Wise Voters Turnout</a></td>


                                     <?php  if (verifyreport(12) != 0){ ?>
                                          <td class="text-center">
                                            <a href="statewisevoterturnout" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                         <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="statewisevoterturnout" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                        </td>
                                        <?php } ?>


                                    <td class="dev">
                                        <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,12)" <?php  if (verifyreport(12) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                          <?php  if (verifyreport(12) != 0){ ?>
                                        <div  class="w-75" class="btn btn-info"></div>
                                         <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                      </td>

                                  </tr>





                                <tr>
                                    <td>13.</td>
                                    <td><a target="_blank" href="pcwisevoterturnout">PC Wise Voters Turn Out</a></td>

                                
                                        <?php  if (verifyreport(13) != 0){ ?>
                                          <td class="text-center">
                                            <a href="pcwisevoterturnout" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="pcwisevoterturnout" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                <td class="dev">
                                    <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,13)" <?php  if (verifyreport(13) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                      <?php  if (verifyreport(13) != 0){ ?>
                                    <div  class="w-75" class="btn btn-info"></div>
                                      <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                  </td>
                                </tr>




                                <tr>
                                    <td>14.</td>
                                    <td><a target="_blank" style="" href="PCWiseDistributionVotesPolled">PC Wise Distribution Votes Polled</a></td>

                                      <?php  if (verifyreport(14) != 0){ ?>
                                          <td class="text-center">
                                            <a href="PCWiseDistributionVotesPolled" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="PCWiseDistributionVotesPolled" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>




                                   
                                       <td class="dev">
                                        <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,14)" <?php  if (verifyreport(14) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                          <?php  if (verifyreport(14) != 0){ ?>
                                        <div  class="w-75" class="btn btn-info"></div>
                                         <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>

                                          <?php } ?>
                                      </td>
                                </tr>


                                <tr>
                                    <td>15.</td><td><a target="_blank" href="AssemblySegmentWiseInformationElectors"> Assembly Segment Wise Information Of Electors</a></td>

                                    <?php  if (verifyreport(15) != 0){ ?>
                                          <td class="text-center">
                                            <a href="AssemblySegmentWiseInformationElectors" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="AssemblySegmentWiseInformationElectors" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>

                                       <td class="dev">
                                        <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,15)" <?php  if (verifyreport(15) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                          <?php  if (verifyreport(15) != 0){ ?>
                                        <div  class="w-75" class="btn btn-info"></div>
                                         <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                      </td>
                                  </tr>

                                  


                                 <tr><td>16.</td>
                                    <td><a target="_blank" href="details-of-repoll-held">Details of Re-poll Held

                                        </a></td>

                                        <?php  if (verifyreport(16) != 0){ ?>
                                          <td class="text-center">
                                            <a href="details-of-repoll-held" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="details-of-repoll-held" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>


                                          <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,16)" <?php  if (verifyreport(16) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(16) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                               <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>



                                     <tr>
                                    <td>17.</td>
                                    <td><a target="_blank" href="StatewiseSeatWon">Statewise Seat Won and Valid votes polled by political party</a></td>


                                        <?php  if (verifyreport(17) != 0){ ?>
                                          <td class="text-center">
                                            <a href="StatewiseSeatWon" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="StatewiseSeatWon" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>


                                            <td class="dev">
                                        <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,17)" <?php  if (verifyreport(17) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                          <?php  if (verifyreport(17) != 0){ ?>
                                        <div  class="w-75" class="btn btn-info"></div>
                                         <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                      </td>
                                </tr>



                                <tr><td>18.</td>
                                    <td><a target="_blank" href="partywiseseatwonvalidvotes"> Party Wise Seat Won & Valid Votes Polled in Each State

                                        </a></td>
                                        <?php  if (verifyreport(18) != 0){ ?>
                                          <td class="text-center">
                                            <a href="partywiseseatwonvalidvotes" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="partywiseseatwonvalidvotes" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>


                                        <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,18)" <?php  if (verifyreport(18) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(18) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                            <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>




                                <tr><td>19.</td>
                                    <td><a target="_blank" href="Political_party_Wise_Deposits_Forfeited">Political Party wise Deposits Forefeited
                                        </a></td>

                                        <?php  if (verifyreport(19) != 0){ ?>
                                          <td class="text-center">
                                            <a href="Political_party_Wise_Deposits_Forfeited" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="Political_party_Wise_Deposits_Forfeited" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                      <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,19)" <?php  if (verifyreport(19) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(19) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>
                                <tr>
                                    <td>20.</td>
                                    <td><a target="_blank" href="performance-of-national-partys"> Performance Of National Parties
                                        </a></td>

                                        <?php  if (verifyreport(20) != 0){ ?>
                                          <td class="text-center">
                                            <a href="performance-of-national-partys" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="performance-of-national-partys" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                       <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,20)" <?php  if (verifyreport(20) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(20) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                               <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>




                              <tr>
                                    <td>21.</td><td>
                                        <a target="_blank" href="performance-of-state-partys"> Performance Of State Party
                                        </a></td>

                                        <?php  if (verifyreport(21) != 0){ ?>
                                          <td class="text-center">
                                            <a href="performance-of-state-partys" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="performance-of-state-partys" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                         <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,21)" <?php  if (verifyreport(21) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(21) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>



                                  <tr>
                                    <td>22.</td><td>
                                        <a target="_blank" href="performance-of-unrecognised-partys">Performance of Registered Unrecognised Parties

                                        </a></td>

                                        <?php  if (verifyreport(22) != 0){ ?>
                                          <td class="text-center">
                                            <a href="performance-of-unrecognised-partys" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="performance-of-unrecognised-partys" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                         <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,22)" <?php  if (verifyreport(22) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(22) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>

                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>

                                          </td>
                                </tr>




                                 <tr><td>23.</td>
                                    <td><a target="_blank" href="participationofWomeneletorsinPoll">Participation Of Women Electors in Poll
                                        </a></td>


                                        <?php  if (verifyreport(23) != 0){ ?>
                                          <td class="text-center">
                                            <a href="participationofWomeneletorsinPoll" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="participationofWomeneletorsinPoll" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>




                                     <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,23)" <?php  if (verifyreport(23) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(23) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                               <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>

                                          </td>
                                </tr>


                                @php if($user_data->role_id=='7') { @endphp

                                <tr><td>24.</td>

                                    <td><a target="_blank" href="{{url('/eci/indexcardview/WomenParticipation')}}"> Participation of Women candidates in Poll
                                        </a></td>


                                         

                                        <?php  if (verifyreport(24) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{{url('/eci/indexcardview/WomenParticipation')}}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{{url('/eci/indexcardview/WomenParticipation')}}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                         <td class="dev">

                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,24)" <?php  if (verifyreport(24) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(24) != 0){ ?>

                                            <div  class="w-75" class="btn btn-info"></div>
                                              <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>

                                          </td>
                                </tr>

                                @php } else{ @endphp

                                <tr><td>24.</td>

                                    <td><a target="_blank" href="{{url('/eci-index/indexcardview/WomenParticipation')}}"> Participation of Women candidates in Poll
                                        </a></td>


                                           <?php  if (verifyreport(24) != 0){ ?>
                                          <td class="text-center">
                                            <a href="{{url('/eci/indexcardview/WomenParticipation')}}" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="{{url('/eci/indexcardview/WomenParticipation')}}" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>


                                        <td class="dev">

                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,24)" <?php  if (verifyreport(24) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(24) != 0){ ?>

                                            <div  class="w-75" class="btn btn-info"></div>
                                              <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>

                                          </td>
                                </tr>

                                @php } @endphp




                            <tr><td>25.</td>
                                    <td><a target="_blank" href="individualperformanceofwomencandidates"> Individual Performance of Women Candidate

                                        </a></td>


                                           <?php  if (verifyreport(25) != 0){ ?>
                                          <td class="text-center">
                                            <a href="individualperformanceofwomencandidates" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="individualperformanceofwomencandidates" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>


                                         <td class="dev">

                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,25)" <?php  if (verifyreport(25) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(25) != 0){ ?>

                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>




                                <tr>
                                    <td>26.</td>
                                    <td><a target="_blank" href="ParticipationofWomenInNationalParties">Participation of Women In National Parties
                                        </a></td>


                                         <?php  if (verifyreport(26) != 0){ ?>
                                          <td class="text-center">
                                            <a href="ParticipationofWomenInNationalParties" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="ParticipationofWomenInNationalParties" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>


                                     <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,26)" <?php  if (verifyreport(26) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(26) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                               <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>

                                  </tr>


                                <tr><td>27.</td>
                                    <td><a target="_blank" href="ParticipationofWomenInStateParties">Participation of Women in State Parties

                                        </a></td>


                                        <?php  if (verifyreport(27) != 0){ ?>
                                          <td class="text-center">
                                            <a href="ParticipationofWomenInStateParties" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="ParticipationofWomenInStateParties" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>

                                       <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,27)" <?php  if (verifyreport(27) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(27) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                               <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>

                                <tr><td>28.</td>
                                    <td><a target="_blank" href="ParticipationofWomenInRegisteredParties">Participation Of Women In Registered Unrecognised Parties
                                        </a></td>

                                         <?php  if (verifyreport(28) != 0){ ?>
                                          <td class="text-center">
                                            <a href="ParticipationofWomenInRegisteredParties" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="ParticipationofWomenInRegisteredParties" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                        <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,28)" <?php  if (verifyreport(28) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(28) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                </tr>
                                <tr>
                                    <td>29.</td><td><a target="_blank" href="ParticipationofWomenAsIndependentCandidates"> Participation Of Women as Independent Candidates
                                        </a></td>

                                          <?php  if (verifyreport(29) != 0){ ?>
                                          <td class="text-center">
                                            <a href="ParticipationofWomenAsIndependentCandidates" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="ParticipationofWomenAsIndependentCandidates" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                        <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,29)" <?php  if (verifyreport(29) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(29) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>
                                  </tr>



                                <tr><td>30.</td>
                                    <td><a target="_blank" href="winning-condidate-analysis-over-total-voters"> Winning Candidates Analysis Over Total Valid Votes
                                        </a></td>
                                          <?php  if (verifyreport(30) != 0){ ?>
                                          <td class="text-center">
                                            <a href="winning-condidate-analysis-over-total-voters" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="winning-condidate-analysis-over-total-voters" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>


                                       <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,30)" <?php  if (verifyreport(30) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(30) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>

                                          </td>
                                </tr>





                                <tr><td>31.</td>
                                    <td><a target="_blank" href="winning-candidate-analysis-over-total-electors">Winning Candidates Analysis Over Total Electors
                                        </a></td>

                                        <?php  if (verifyreport(31) != 0){ ?>
                                          <td class="text-center">
                                            <a href="winning-candidate-analysis-over-total-electors" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="winning-candidate-analysis-over-total-electors" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                          <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,31)" <?php  if (verifyreport(31) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(31) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>

                                  </tr>


                                  <tr><td>32.</td>
                                    <td><a target="_blank" href="constituencyDataSummaryReport"> Constituency Data Summary

                                        </a></td>
                                        <?php  if (verifyreport(32) != 0){ ?>
                                          <td class="text-center">
                                            <a href="constituencyDataSummaryReport" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="constituencyDataSummaryReport" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                       <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,32)" <?php  if (verifyreport(32) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(32) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>

                                  </tr>



                                      <tr><td>33.</td>
                                    <td><a target="_blank" href="constituencywisedetailedresult">Constituency Wise Detailed Result

                                        </a></td>

                                         <?php  if (verifyreport(33) != 0){ ?>
                                          <td class="text-center">
                                            <a href="constituencywisedetailedresult" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="constituencywisedetailedresult" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                      <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,33)" <?php  if (verifyreport(33) != 0){ ?> checked  <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(33) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>

                                  </tr>



                                      <tr><td>34.</td>
                                    <td><a target="_blank" href="detailsofassemblysegmentofpc">Details of Assembly Segment of PC

                                        </a></td>

                                         <?php  if (verifyreport(34) != 0){ ?>
                                          <td class="text-center">
                                            <a href="detailsofassemblysegmentofpc" target="_blank">Final Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php   } else { ?>
                                           <td class="text-center">
                                            <a href="detailsofassemblysegmentofpc" target="_blank">Internal Preview<i class="fa fa-eye ml-1 position-absolute"></i></a>
                                          </td>
                                        <?php } ?>



                                         <td class="dev">
                                            <input type="checkbox" class="checkbox-md mr-2 checkifset" onchange="insert_verify(this,34)" <?php  if (verifyreport(34) != 0){ ?> checked <?php } ?> <?php  if (verifyreport(777) != 0){ ?> disabled <?php } ?>>
                                              <?php  if (verifyreport(34) != 0){ ?>
                                            <div  class="w-75" class="btn btn-info"></div>
                                             <?php   } else { ?>
                                              <div class="w-75">Click to final preview</div>
                                              <?php } ?>
                                          </td>

                                  </tr>



                            </tbody>
                        </table>
                          <!-- <input type="hidden" name="user_id" id="user_logged_in" value="{{ Auth::user()->name }}"> -->
                        <!-- Content ends Here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">

function insert(report_number) {


// AJAX code to send data to php file.
$.ajax({
type: "GET",
url: "./statistical-report-listing-verify",
data: {report_no:report_number},
dataType: "JSON",
success: function(data) {
location.reload();
},
error: function(data){
        window.console.log(data);
    }

});

}


function insert_verify(obj,report_number) {

  if($(obj).is(":checked")){

    $.ajax({
    type: "GET",
    url: "./statistical-report-listing-verify-checkbox",
    data: {is_verified:1,report_no:report_number},
    dataType: "JSON",
    success: function(data) {
    location.reload();
    },
    error: function(data){
            window.console.log(data);
        }

    });

  }else{
    //alert("Not checked"); //when not checked

    $.ajax({
    type: "GET",
    url: "./statistical-report-listing-verify-checkbox",
    data: {is_verified:0, report_number:report_number},
    dataType: "JSON",
    success: function(data) {
    location.reload();
    },
    error: function(data){
            window.console.log(data);
        }

    });
  }




}


function verifycheck(report_number){
var i=0;
$('.checkifset').each(function () {
         var checked = $(this).val();
         if ($(this).is(':checked')) {
    
                 i++;
             
         } 
     });
  if(i==34){
   // $('.checkifset').prop('disabled',true);\

   

    if(confirm('Once verification confirmed all the editing in the data will be disabled')){

    $.ajax({
    type: "GET",
    url: "./statistical-report-listing-verify-all-report",
    data: {is_verified:1, report_number:report_number},
    dataType: "JSON",
    success: function(data) {
    $('.checkifset').prop('disabled',true);
    
    },
    error: function(data){
            window.console.log(data);
        }

    });

    }
   
  

   

  

  }else{
    $('#myModal').modal('show');
  }
}






</script>
@endsection
