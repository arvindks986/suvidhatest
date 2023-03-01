<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/images/favicon.png" type="image/png">
    <title> REPORT2</title>
    <!--Begin  Page Level  CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
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
        background: #005aab08;
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
        text-align: center;
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

    tr:nth-child(even) {
        background: #8e99ab29;

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
</head>

<body style="display: flex;position: relative;">
    <div class="headerreport">
        <div class="container">
            <div class="bordertestreport">
                <img src="https://eci.gov.in/uploads/monthly_2018_11/cyber-security-logo.png.36764059053d6b18e50e5c809ed6caaa.png" class="img-responsivesreport" alt="">
                <h4>Election Commission of India, General Elections, 2014 (16th LOK SABHA )</h4>
                <h2>NUMBER OF CANDIDATES PER CONSTITUENCY</h2>
                <table class="table table-bordered table-responsive" style="width: 100%;table-layout: inherit;">
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
					@foreach ($candidates as $candidate) 
                        <tr>
                            
                            <td>{{ $candidate->ST_NAME }}</td>
							<td>{{ $candidate->seats }}</td>
                            <td>No. oF Seats</td>
							<td>No. oF Seats</td>
							<td>No. oF Seats</td>
							<td>No. oF Seats</td>
							<td>No. oF Seats</td>
							<td>No. oF Seats</td>
							<td>No. oF Seats</td>
							<td>No. oF Seats</td>
							<td>No. oF Seats</td>
							<td>No. oF Seats</td>
							
                        </tr>
@endforeach
                        

                        
                        
                       
                       
                        <tr>
                            <td>Kerala</td>
                            <td>4</td>
                            <td>7</td>
                            <td>5</td>
                            <td>5</td>
                            <td>2</td>
                            <td>1</td>
                            <td>1</td>
                            <td>12</td>
                            <td>23</td>
                            <td>23</td>
                            <td>23</td>
                        </tr><tr>
                            <td style="background:#005aab;color: #fff;"><b>Grand Total</b></td>
                            <td>4</td>
                            <td>7</td>
                            <td>5</td>
                            <td>5</td>
                            <td>2</td>
                            <td>1</td>
                            <td>1</td>
                            <td>12</td>
                            <td>23</td>
                            <td>23</td>
                            <td>23</td>
                        </tr>
                    <tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>