@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Party Wise Expenditure Details')
@section('bradcome', 'Party Wise Expenditure')
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
if(!empty($party)){
$partydetails=getpartybyid($party);
$partyName=!empty($partydetails->PARTYNAME)?$partydetails->PARTYNAME:'';
$graphText.=' '.$partyName.'(Party)';
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
                    <form method="get" action="{{url('/eci-expenditure/getPartyWiseExpenditure')}}" id="EcidashboardFilter">           
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
                            <div class="col-sm-3">
                                <label for="" class="mr-3">Select Party</label>    
                                <select name="party" id="party" class="form-control">
                                    <option value="">All Party</option>
                                    @php $patrylist = getallpartylist(); @endphp
                                    @foreach ($patrylist as $party_List ))
                                    <option value="{{ $party_List->CCODE }}" <?php
                                    if (!empty($_GET['party']) && $party_List->CCODE == $_GET['party']) {
                                        echo "selected";
                                    }
                                    ?>> {{$party_List->PARTYNAME}}-{{$party_List->PARTYABBRE}}</option>
                                    @endforeach

                                    @if ($errors->has('party'))
                                    <span class="help-block">
                                        <strong class="user">{{ $errors->first('party') }}</strong>
                                    </span>
                                    @endif
                                    <div class="stateerrormsg errormsg errorred"></div>
                                </select> 
                            </div>




                            <!--STATE LIST DROPDOWN ENDS-->

                            <div class="col-sm-2 mt-2">
                                <p class="mt-4 text-left">
                                    <!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
                                    <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
<!--  <a href="{{url('/eci-expenditure/getPartyWiseExpenditure')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a> -->
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
                            <div class="col-sm-5"><h2 class="mr-auto">Party Wise Expenditure</h2></div> 
                            <div class="col-sm-7"><p class="mb-0 text-right">
                                    <b>State Name:</b> 
                                    <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                                    <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                                    <b>PC:</b> <span class="badge badge-info">{{ $pcName}}</span>
                                    <span class="badge badge-info"></span>&nbsp;&nbsp;
                                    <a href="{{url('/eci-expenditure/getPartyWiseExpenditure')}}?party={{$party}}&pc={{$pc}}&state={{$st_code}}&pdf=yes" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                                    <a href="{{url('/eci-expenditure/getPartyWiseExpenditure')}}?party={{$party}}&pc={{$pc}}&state={{$st_code}}&exl=yes" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp; <a href="{{url('/')}}/eci-expenditure/EciExpdashboard/"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
                                </p>
                            </div>
                        </div><!-- end row-->
                    </div><!-- end card-header-->
                    <?php
                    $j = 1;
                    $allPartylist = [];
                    $grandTotal = 0;
                    ?>

                    @if(!empty($partylist))
                    @foreach($partylist as $partylists)  

                    @php
                    $totalexpen=\app(App\models\Expenditure\ExpenditureModel::class)->getpartytotalexpenditure($partylists->CCODE,$st_code,$pc);
                    $grandTotal += $totalexpen; 

                    $allPartylist[]=[
                    'PARTYABBRE'=>$partylists->PARTYABBRE,
                    'PARTYNAME'=>$partylists->PARTYNAME,
                    'totalexpen'=>$totalexpen
                    ]; @endphp
                    @endforeach  
                    @endif
                    <?php 
                    $amount = array_column($allPartylist, 'totalexpen');
                    array_multisort($amount, SORT_DESC, $allPartylist);
                      $noData=  empty($allPartylist)?'No Data Available Graph':'';
                    ?>
                    <div class="card-body"> 
                        <div class="row">

                            <div class="col-sm-7" >
                                <div class="table-responsive">
                                    <table id="example1" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>S.no</th>
                                                <th>Party Name</th>
                                                <th>Total Expenditure(Rs.)</th>
                                            </tr>
                                        </thead>
                                        @if(!empty($allPartylist))
                                        @foreach($allPartylist as $partylists) 
                                        <tr>
                                            <td><?php echo $j++; ?></td>
                                            <td>{{$partylists['PARTYABBRE']}} - {{$partylists['PARTYNAME']}}</td>

                                            <td align="right"> {{$partylists['totalexpen']}}</td>

                                        </tr>
                                        @endforeach  
                                        @endif
                                        <tfoot>
                                            <tr>
                                                <td><b>Total Expenditure(Rs.)</b></td>
                                                <td><b>All Parties</b></td>
                                                <td align="right"><b> {{$grandTotal}}</b></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div> <!-- end responcive-->
                            </div> 
                                <div class="col-sm-5" >
                           
                                
                                            @if(!empty($allPartylist))
                                               <div class="text-center mt-3">
                                        <h2 class="mr-auto">Graph Party Wise Expenditure</h2>
                                    </div>
                                 

                                    <div id="piechart" style="width: 680px; height: 500px;"></div>

                               
                                  @else
                                      
                                         {{$noData}}
                                          
                                         @endif
                             
                        </div> 


                        </div>
                    </div>

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
$finalData = [
    ['Party Wise Expenditure', 'Party Wise Expenditure'],
];
if (!empty($allPartylist)) {
    $toptenrecords = array_slice($allPartylist, 0, 9);
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
                        [<?php echo '"' . $item['PARTYNAME'] . '(' . $item['PARTYABBRE'] . ')",', $item['totalexpen'] ?>],
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
    $('#example1').append('<caption style="caption-side: top;">Party Wise Expenditure</caption>');
 
    var table = $('#example1').DataTable({   
     dom: 'lBfrtip', 
     lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
     pageLength: 10,
     buttons: [
            {
                extend: 'pdfHtml5',               
                pageSize: 'LEGAL',
                header:true,
                footer:true,
               filename: function() {
                return 'getPartyWiseExpenditure-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   PC:'.$pcName.''; ?>'
              },
            }],
           
         
      
    });
  })
  </script>
 
@endsection
