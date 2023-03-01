@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate Wise Expenditure')
@section('description', '')
@section('content') 

@php 
$pc = !empty($_GET['pc'])?$_GET['pc']:""; 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : $pc;
$party = !empty($_GET['party'])?$_GET['party']:"";


$st=getstatebystatecode($st_code);
$pcdetails=getpcbypcno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
$all_pc=getpcbystate($st_code);
 
$graphText='';
if(!empty($st->ST_NAME)){
$graphText.=$st->ST_NAME;
}
if(!empty($pcdetails->PC_NAME)){
$graphText.=' '.$pcdetails->PC_NAME.'(PC)';
}
 if(empty($graphText)){
  $graphText='All States';
}
 $noData='';

@endphp


<style type="text/css">
    .mt-5, .my-5{margin-top: 1rem!important;}
</style>
<main role="main" class="inner cover mb-3">
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 mt-3">
                    <!--FILTER STARTS FROM HERE-->
                    <form method="get" action="{{url('/eci-expenditure/candidate_wise_expenditure')}}" id="EcidashboardFilter">           
                        <div class="row justify-content-center">
                            <!--STATE LIST DROPDOWN STARTS-->
                            <div class="col-sm-3">
                                <label for="" class="mr-3">Select State</label>    
                                <select name="state" id="state" class="form-control">
                                    <?php if ($stateName == 'ALL') { ?> <option value="">All States</option> <?php } ?>
                                    @foreach ($statelist as $state_List ))
                                    <option value="{{ $state_List->ST_CODE }}" <?php
                                    if (!empty($_GET['state']) && $state_List->ST_CODE == $_GET['state']) {
                                        echo "selected";
                                    }
                                    ?>>{{$state_List->ST_NAME}}</option>
                                    @endforeach

                                    @if ($errors->has('state'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('state') }}</strong>
                                    </span>
                                    @endif
                                    <div class="stateerrormsg errormsg errorred"></div>
                                </select> 
                            </div>
                            <!--STATE LIST DROPDOWN ENDS-->
                            <div class="col-sm-3">
                                <label for="" class="mr-3">Select PC</label>    
                                <select name="pc" id="pc" class="consttype form-control" >
                                    <option value="">-- All PC --</option>
                                    @if (!empty($all_pc))
                                    <?php //dd($all_pc);  ?>
                                    @foreach($all_pc as $getPc) 
                                    @if ($pc ==  $getPc->PC_NO)
                                    <option value="{{ $getPc->PC_NO }}" selected>{{$getPc->PC_NO }} - {{$getPc->PC_NAME }}- {{$getPc->PC_NAME_HI}}</option>
                                    @else
                                    <option value="{{ $getPc->PC_NO }}" <?php
                                    if (!empty($_GET['pc']) && $getPc->PC_NO == $_GET['pc']) {
                                        echo "selected";
                                    }
                                    ?>>{{$getPc->PC_NO }} - {{$getPc->PC_NAME }} - {{$getPc->PC_NAME_HI}}</option>
                                    @endif

                                    @endforeach 
                                    @endif
                                </select>
                                @if ($errors->has('pc'))
                                <span style="color:red;">{{ $errors->first('pc') }}</span>
                                @endif

                                <div class="acerrormsg errormsg errorred"></div>
                            </div>
                            <div class="col-sm-2 mt-2">
                                <p class="mt-4 text-left">
                                    <!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
                                    <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
<!--  <a href="{{url('/eci-expenditure/candidate_wise_expenditure')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a> -->
                                </p>
                            </div>
                        </div>
                    </form> 
                    <!--FILTER ENDS HERE-->
                </div> 
                <div class="card text-left" style="width:100%; margin:0 auto;">
                    <div class=" card-header">
                        @if (Session::has('message'))
                        <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ Session::get('message') }} </div> 
                        @php Session::forget('message'); @endphp
                        @elseif (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            {{ Session::get('error') }} <br/>

                        </div>
                        @php Session::forget('error'); @endphp
                        @endif
                        <div class=" row">
                            <div class="col"><h2 class="mr-auto">Candidate Wise Expenditure</h2></div> 
                            <div class="col"><p class="mb-0 text-right">
                                    <b>State Name:</b> 
                                    <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                                    <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                                    <b>PC:</b> <span class="badge badge-info">{{$pcName}}</span>
                                    <span class="badge badge-info"></span>&nbsp;&nbsp;
                                    <a href="{{url('/eci-expenditure/candidate_wise_expenditure')}}?pc={{$pc}}&state={{$st_code}}&pdf=yes" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                                    <a href="{{url('/eci-expenditure/candidate_wise_expenditure')}}?pc={{$pc}}&state={{$st_code}}&exl=yes" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
                                    <a href="{{url('/')}}/eci-expenditure/EciExpdashboard/"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
                                </p></div>
                        </div><!-- end row-->
                    </div><!-- end card-header-->
                    <div class="card-body"> 
                        <div class="row">
                            <div class="col-sm-7">

                                <?php
                                $allPartylist = [];
                                if (!empty($candList)) {
                                    foreach ($candList as $candDetails) {

                                      //  $totalexpen = \app(App\models\Expenditure\ExpenditureModel::class)->getcandidatetotalexpenditure($candDetails->candidate_id);

                                        $allPartylist[] = [
                                            'st_code' => $candDetails->st_code,
                                            'pc_no' => $candDetails->pc_no,
                                            'YEAR' => $candDetails->YEAR,
                                            'ELECTION_TYPE' => $candDetails->ELECTION_TYPE, 
                                            'candidate_id' => $candDetails->candidate_id,                                            
                                            'cand_name' => $candDetails->cand_name,
                                            'grand_total_election_exp_by_cadidate' => $candDetails->grand_total_election_exp_by_cadidate
                                            
                                        ];
                                    }
                                } 

                                $amount = array_column($allPartylist, 'grand_total_election_exp_by_cadidate');
                                array_multisort($amount, SORT_DESC, $allPartylist);
                                 $noData=  empty($allPartylist)?'No Data Available Graph':'';
                                ?>
                                <div class="table-responsive">
                                    <table id="example1" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>S. No.:</th>
                                                <th>Candidate Name</th>
                                                <th>State</th>
                                                <th>PC No & PC Name</th>
                                                <th>Election Year</th>
                                                <th>Election Type</th>
                                                <th>Total Expenditure Declared <br />By Candidate(Rs.)</th>
												<th>Total Expenditure Assessed <br />By DEO(Rs.)</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $j = 0;
                                        $grandTotal = 0;
										$grandTotalAssessbyDEO=0;
										$avgTotalbycand=0;
										$avgbyAssessbyDEO=0;
                                        ?>
                                        @if(!empty($allPartylist))
                                        @foreach($allPartylist as $candDetails)  
                                        <?php
									 $candidate_id=$candDetails['candidate_id'];
                                     $pcdetails = getpcbypcno($candDetails['st_code'], $candDetails['pc_no']);
                                     $st = getstatebystatecode($candDetails['st_code']);
                                     $candUnderStatasDetails=\app(App\models\Expenditure\ExpenditureModel::class)->GetScrutinyUnderExpByitemData($candidate_id);
										 //dd($candUnderStatasDetails);
										 $totalamntassesbyDEO=0;
										 if(!empty($candUnderStatasDetails[0]->amt_as_per_observation)){
										 foreach($candUnderStatasDetails as $details){
											 $totalamntassesbyDEO +=$details->amt_as_per_observation;
										  }
										 }
                                        ?>
                                        @php 
									
									   $grandTotalAssessbyDEO += $totalamntassesbyDEO;
                                       $totalamount = !empty($candDetails['grand_total_election_exp_by_cadidate'])? $candDetails['grand_total_election_exp_by_cadidate'] : 0; 
                                        $grandTotal += $totalamount;
                                        $allPartylist[]=[
                                        'candidate'=>$candDetails['cand_name'],                     
                                        'totalexpen'=>$totalamount
                                        ];
										
										
                                        @endphp
                                        <tr>
                                            <td>{{++$j}}</td>
                                            <td>@if(!empty($candDetails['cand_name'])) {{$candDetails['cand_name']}} @endif </td>
                                            <td>{{$st->ST_NAME}}</td>
                                            <td>{{$pcdetails->PC_NO}} - {{$pcdetails->PC_NAME}}</td>
                                            <td>@if(!empty($candDetails['YEAR'])) {{$candDetails['YEAR']}} @endif</td>
                                            <td>@if(!empty($candDetails['ELECTION_TYPE'])) {{$candDetails['ELECTION_TYPE']}} @endif</td>
                                            <td align="right">{{$totalamount}}</td>
											<td align="right">{{$totalamntassesbyDEO}}</td>
                                             

                                        </tr>
                                        @endforeach 
                                        @endif 
                                        <tfoot>
                                            <tr>
											<td></td>
                                                <td colspan="5">Total(Rs.)</td>
                                                <td align="right"><b> {{$grandTotal}}</b></td>
												<td align="right"><b> {{$grandTotalAssessbyDEO}}</b></td>
                                            </tr>
											 @php
										$avgTotalbycand= round($grandTotal/$j);
										$avgbyAssessbyDEO= round($grandTotalAssessbyDEO/$j);
										@endphp
											 <tr>
											     <td></td>
                                                <td colspan="5">Average(Rs.)</td>
                                                <td align="right"><b> {{$avgTotalbycand}}</b></td>
												<td align="right"><b> {{$avgbyAssessbyDEO}}</b></td>
                                            </tr>
                                        </tfoot>

                                    </table>
                                </div> <!-- end responcive-->
                            </div>
                                 <div class="col-sm-5" >
                           
                                
                                            @if(!empty($allPartylist))
                                               <div class="text-center mt-3">
                                        <h2 class="mr-auto">Graph Candidate Wise Expenditure</h2>
                                    </div>
                                 

                                    <div id="piechart" style="width: 680px; height: 500px;"></div>

                               
                                  @else
                                      
                                         {{$noData}}
                                          
                                         @endif
                             
                        </div>
                        </div>
                       
                    </div> <!-- end card-body-->
                </div>
            </div>
        </div>
        
    </section>

</main>



<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>






<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery("select[name='state']").change(function () {
        var state = jQuery(this).val();
        // alert(state);
        jQuery.ajax({
            url: '<?php echo url('/') ?>/eci-expenditure/getpcbystate',
            type: 'GET',
            data: {state: state},

            success: function (result) {
                console.log(result);
                var stateselect = jQuery('form select[name=pc]');
                stateselect.empty();
                var pchtml = '';
                pchtml = pchtml + '<option value="">-- All PC --</option> ';
                jQuery.each(result, function (key, value) {
                    pchtml = pchtml + '<option value="' + value.PC_NO + '">' + value.PC_NO + ' - ' + value.PC_NAME + ' - ' + value.PC_NAME_HI + '</option>';
                    jQuery("select[name='pc']").html(pchtml);
                });
                var pchtml_end = '';
                jQuery("select[name='pc']").append(pchtml_end)
            }
        });
    });
});


</script>

<?php
 
if (!empty($allPartylist)) {
    $toptenrecords = array_slice($allPartylist, 0, 9);
    $toptenrecords2=[];
    foreach($toptenrecords as $item2){
        $toptenrecords2[]=[
            'cand_name'=>$item2['cand_name'],
             'totalexpen'=>$item2['grand_total_election_exp_by_cadidate']
        ];
        
    }
    ?>
      <style>
       #piechart svg g text{
          font-size:11px !important;
          
}
    </style>
    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 

    <script type="text/javascript">
    google.charts.load("current", {packages: ["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
           var options = {
             pieHole: 0.1,
              pieSliceTextStyle: {
                  color: 'black',
                  fontSize:0.9
              },
              
        };
        var data = google.visualization.arrayToDataTable(
                [
                    ['<?php echo $graphText; ?>', '<?php echo $graphText; ?>'],
    <?php
    foreach ($toptenrecords as $item) {
        ?>
                        [<?php echo '"' . $item['cand_name'] . '",', $item['grand_total_election_exp_by_cadidate'] ?>],
    <?php }
    ?>
                ]);
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);

    }
    </script>
    <?php
}else{
   $noData='No Data Available Graph'; 
}
?>
<script>
$(document).ready(function() {
    $('#example1').append('<caption style="caption-side: top;">Candidate Wise Expenditure</caption>');
    var table = $('#example1').DataTable({   
     dom: 'lBfrtip', 
     lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
     pageLength: 10,
     buttons: [
            {
                extend: 'pdfHtml5',               
                pageSize: 'LEGAL',
                footer:true,
               filename: function() {
                return 'candidate_wise_expenditure-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   PC:'.$pcName.''; ?>'
              },
            }],
           
         
      
    });
  })
  </script>
@endsection
