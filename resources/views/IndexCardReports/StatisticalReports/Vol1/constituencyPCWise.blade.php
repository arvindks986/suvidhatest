@extends('admin.layouts.pc.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Constituency PC Wise')
@section('content')
<style>
    @font-face {
        font-family: 'poppinsregular';
        src: url('../fonts/poppins-regular-webfont.woff2') format('woff2'), url('../fonts/poppins-regular-webfont.woff') format('woff');
        font-weight: normal;
        font-style: normal;
    }
    
    body {
        font-family: poppins;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 1.0rem;
    }
    
    #wapper {
        width: 100%;
        margin: auto 0;
    }
    
    h1 {
        font-size: 1.4rem;
        padding: 50px 0 0;
    }
    
    h2 {
        font-size: 1.2rem;
    }
    
    .table-responsive {
        display: table;
    }
    
    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    tr td {
        padding: 10px 8px;
    }
    
    table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #ddd;
    }
    
    th,
    td {
        text-align: left;
        padding: 10px 8px;
    }
    
    th {
        background: rgb(44, 59, 72);
        color: #f1f1f1;
    }
    
    td {
        color: #878686;
    }
    
    table,
    th,
    td {
        border: 1px solid #ddd;
    }
    
    .text-center {
        text-align: center;
    }
    
    .state {
        font-weight: bold;
        padding: 2px 10px;
    }
    
    strong {
        text-transform: uppercase;
        color: #555;
    }
    
    .thead {
        border-bottom: solid #555 1px;
        border-top: solid #555 1px;
    }
    
    tr:nth-child(even) {
        background-color: #f2f2f2
    }
    
    .container {
        width: calc(100% - 20px);
        margin: auto;
    }
    
    .responsive {
        width: 100%;
        height: auto;
    }
    
    tfoot td {
        font-weight: bold;
    }
    
    .totalAmount td {
        font-weight: bold;
        color: #130f0f;
        padding-right: 12px;
        padding-bottom: 12px;
        background: #fff
    }
    
    @media screen {
        p.bodyText {
            font-family: verdana, arial, sans-serif;
        }
    }
    
    @media print {
        p.bodyText {
            font-family: georgia, times, serif;
        }
    }
    
    @media screen,
    print {
        p.bodyText {
            font-size: 10pt
        }
    }
    
    @media (max-width: 575.98px) {
        h1 {
            font-size: 120%
        }
        h2 {
            font-size: 110%;
        }
    }
    
    @media (max-width: 767.98px) {
        h1 {
            font-size: 120%
        }
        h2 {
            font-size: 110%;
        }
    }
    
    @media(min-width:1200px) {
        .container {
            width: 1140px;
            margin: auto;
        }
    }
</style>

<div id="wapper">
    <div class="pull-right exportdiv"> 
            <a href="constituencyPCWisePDF" class="btn show pdfbut"><img src="assets/images/pdficon.png"></a>
            <a href="constituencyPCWiseCSV" class="btn  show pdfbut"><img src="assets/images/excel.png"></a>
       </div>
    <div class="container">
        <div class="" style="width:100%; display:flex;align-items: center;">
            <div style="width:30%; float:left">
                <img src="https://suvidha.eci.gov.in/theme/img/logo/eci-logo.png" alt="logo" class="responsive">
            </div>
            <div style="width:70%;float:left">
                <h1 class="text-center">Election Commission Of India, General Elections, 2019 (17th LOK SABHA )</h1>
                <h2 class="text-center">7. CONSTITUENCY (PC) WISE- SUMMARY TABLE</h2>
            </div>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>PC No</th>
                        <th>PC Name</th>
                        <th>No Of AC Segments</th>
                        <th>No Of Polling Station</th>
                        <th>Electors</th>
                        <th>Avg. No Of Electors Per </th>
                        <th>Nominations</th>
                        <th>Contestants</th>
                        <th>Forefeited Deposits</th>
                        <th>Voters</th>
                        <th>Voters Turn Out (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($aaData as $key => $value)
                    <tr>
                        <td colspan="11">
                            <p class="state">{{$key}}</p>
                        </td>
                    </tr>
                    <?php $grandtotalAC = $grandtotal_no_polling_station = $grande_all_t = $grandAvgElectors = $grandc_nom_a_t = $grandc_nom_co_t = $grandvt_all_t = $grandvotersTurnOut = 0; ?>
                    @foreach($value as $kkey => $vvalue)
                    <tr>
                        <td>{{$vvalue->PC_NO}}</td>
                        <td>{{$kkey}}</td>
                        <td>{{$vvalue->totalAC}}</td>
                        <td>{{$vvalue->total_no_polling_station}}</td>
                        <td>{{$vvalue->e_all_t}}</td>
                        <td>{{$vvalue->AvgElectors}}</td>
                        <td>{{$vvalue->c_nom_a_t}}</td>
                        <td>{{$vvalue->c_nom_co_t}}</td>
                        <td>NA</td>
                        <td>{{$vvalue->vt_all_t}}</td>
                        <td align="center">{{$vvalue->votersTurnOut}}</td>
                    </tr>
                    <?php $grandtotalAC += $vvalue->totalAC;
                        $grandtotal_no_polling_station += $vvalue->total_no_polling_station;
                        $grande_all_t += $vvalue->e_all_t;
                        $grandAvgElectors += $vvalue->AvgElectors;
                        $grandc_nom_a_t += $vvalue->c_nom_a_t;
                        $grandc_nom_co_t += $vvalue->c_nom_co_t;
                        $grandvt_all_t += $vvalue->vt_all_t;
                        $grandvotersTurnOut += $vvalue->votersTurnOut; ?>
                    @endforeach
                    <tr class="totalAmount">
                        <td colspan="2">State Total:</td>
                        <td>{{$grandtotalAC}}</td>
                        <td>{{$grandtotal_no_polling_station}}</td>
                        <td>{{$grande_all_t}}</td>
                        <td>{{$grandAvgElectors}}</td>
                        <td>{{$grandc_nom_a_t}}</td>
                        <td>{{$grandc_nom_co_t}}</td>
                        <td>NA</td>
                        <td>{{$grandvt_all_t}}</td>
                        <td>{{$grandvotersTurnOut}}</td>
                    </tr>
                    @endforeach 
                </tbody>

                <tfoot>
                    <tr>

                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

@endsection