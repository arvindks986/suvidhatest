@extends('IndexCardReports.layouts.theme')
@section('title', 'StatistiCal Report')
@section('bradcome', 'Number Of Candidate Per Constitution')
@section('content')

<style>
.reportsection {
        text-align: center;
    }


    td {
        font-size: 14px !important;
    }

    .blc {
        background: #000;
        color: #fff;
    }

    .headerreport h2 {
        background: #005aab;
        color: #ffff;
        padding: 10px;
        text-transform: capitalize;
        border-radius: 10px;
        font-size: 22px;

    }

    .bordertestreport {
        text-align: center;
        border: 1px solid #ddd;
        padding: 30px;
        background-image: url(../images/grid.png);
        background: #f4f6f9;
        background-repeat: repeat;
    }

    .headerreport h4 {
        text-transform: capitalize;
        font-size: 18px;
        font-family: 'poppinsregular';

    }



    .headerreport {
        margin: auto;
    }

    .tablecenterreport td {
        font-size: 15px;
    }

    span.devil {
        float: right;
        padding: 11px;
        font-size: 17px;
        position: relative;
        right: 16%;
        border: 2px dotted #4da1ed;
        background: #4da1edab;
        color: #fff;
    }

    td {
        text-align: left;
    }

    div#example_wrapper {
        position: relative;
        top: 40px;
    }

    .pagination {
        margin: 10px 0px;
    }

    div#example_paginate {
        top: -16px;
        position: relative;
    }

    .rightfl {
        display: inline-block;
        float: right;
        word-spacing: -1px;
    }

    .contituency {
        text-align: left;
        padding: 4px;
        font-size: 17px;
        font-weight: bold;
        display: inline-block;
        padding: 0px 60px;
    }


    th {
        background: #4da1ed;
        color: #fff !important;
        text-align: center;
    
        font-size: 14px;
    }

   
    td.dev {
        text-align: center;
    }

    img.img-responsivesreport {
        height: 130px;
        margin: auto;
        position: relative;
        display: block;
    }


    .contituency span {
        padding: 6px 46px !important;
        background: #4da1ed;
        color: #fff;
        text-decoration: none !important;
    }
    </style>
  


<section class="">
    <div class="container-fluid">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(7.Number Of Candidate Per Constitution)</h4></div> 
                        <div class="col">
                            <p class="mb-0 text-right">
                            <a href="numberofcandidateperconstituencyPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                            <a href="indexCardReportCSV" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
                            </p>
                        </div>
                    </div>
                </div>



            <div class="table-responsive">
            <table class="table table-bordered" style="width: 100%;table-layout: inherit;">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th colspan="4">Constiturncies with Candidate Numbering</th>
                        <th colspan="4">Candidates in a Constituency</th>

                    </tr>

                </thead>

                <tbody>
                    <tr>
                        <th>State/UT</th>
                        <th>No. oF Seats</th>
                        <th>1</th>
                        <th><=16</th>
                        <th>>16 <=32</th>
                        <th>>32 <=48</th>
                        <th>>48 <=64</th>
						<th>>64</th>
						<th>Total</th>
						<th>Min</th>
						<th>Max</th>
						<th>Avg</th>
                    </tr>
                    <?php foreach ($data as $value) { //echo "<pre>"; print_r($value);?>
                    	<tr>
	                        <td><?php echo $value['ST_NAME']; ?></td>
	                        <td><?php echo isset($value['TotalSeats'])?$value['TotalSeats']:0; ?></td>
	                        <td><?php echo isset($value['count1'])?$value['count1']:0; ?></td>
	                        <td><?php echo isset($value['count16'])?$value['count16']:0; ?></td>
	                        <td><?php echo isset($value['count32'])?$value['count32']:0; ?></td>
	                        <td><?php echo isset($value['count48'])?$value['count48']:0; ?></td>
	                        <td><?php echo isset($value['count64'])?$value['count64']:0; ?></td>
	                        <td><?php echo isset($value['count65'])?$value['count65']:0; ?></td>
	                        <td><?php echo isset($value['totalCandidate'])?$value['totalCandidate']:0; ?></td>
	                        <td><?php echo ($value['min'])?$value['min']:0; ?></td>
	                        <td><?php echo ($value['max'])?$value['max']:0; ?></td>
	                        <td><?php echo isset($value['avg'])?$value['avg']:0; ?></td>
	                    </tr>
                    <?php  } ?>
                    <tr>
                        <td style="background:#eee;color: #000;"><b>Grand Total</b></td>
                        <td>{{$grandData['TotalSeats']}}</td>
                        <td>{{$grandData['count1']}}</td>
                        <td>{{$grandData['count16']}}</td>
                        <td>{{$grandData['count32']}}</td>
                        <td>{{$grandData['count48']}}</td>
                        <td>{{$grandData['count64']}}</td>
                        <td>{{$grandData['count65']}}</td>
                        <td>{{$grandData['totalCandidate']}}</td>
                        <td>{{$grandData['min']}}</td>
                        <td>{{$grandData['max']}}</td>
                        <td>{{$grandData['avg']}}</td>
                    </tr>
                    <tbody>
            </table>
            </div>
        </div>
    </div>
</div>

</div>

@endsection