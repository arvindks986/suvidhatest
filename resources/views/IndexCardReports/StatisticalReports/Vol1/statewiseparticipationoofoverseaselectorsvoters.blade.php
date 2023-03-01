<!DOCTYPE html>
<html>  
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

        <style>

            @font-face {
                font-family: 'poppinsregular';
                src: url('../fonts/poppins-regular-webfont.woff2') format('woff2'),
                    url('../fonts/poppins-regular-webfont.woff') format('woff');
                font-weight: normal;
                font-style: normal;

            }

            body {
                padding-bottom: 20px;
                background: url(./assets/images/grid.png);
                font-family: 'poppinsregular';

            }

            td {
                font-size: 14px !important;
                font-weight: 500 !important;
                color: #4a4646 !important;
            }


            .headerreport h2 {
                background: #959798;
                color: #ffff;
                padding: 5px;
                text-transform: capitalize;
                border-radius: 10px;
                font-size: 19px;

            }

            .bordertestreport {
                border: 1px solid #ddd;
                padding: 30px 0px;
                background-image: url(../images/grid.png);
                background: #fff;
                background-repeat: repeat;
            }

            .headerreport h4 {
                text-transform: capitalize;
                font-size: 18px;
                font-family: 'poppinsregular';

            }



            th {
                background: #959798;
                color: #fff !important;
                text-align: center;

                font-size: 14px;
            }

            tr:nth-child(even) {
                background: #8e99ab29;

            }


        </style>


    </head>

    <body>




        <div class="headerreport">
            <div class="container-fluid">
                <div class="bordertestreport">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-3" style="position: relative;top: -17px;">
                                <img src="https://suvidha.eci.gov.in/theme/img/logo/eci-logo.png" class="img-responsive" style="width:100px !important;" alt="">
                            </div>

                            <div class="col-sm-9" style="display: grid;justify-content: right;">
                                <h4 style="font-size: 21px;    font-family: 'poppinsregular';">Election Commission Of India, General Elections, 2019</h4>  
                                <h5 style="font-family: 'poppinsregular'; font-weight: bold;text-align: center; font-size: 17px;text-decoration: underline;">(State Wise Participation of Overseas Electors Voters )</h5>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <hr>             
                    </div> 
                    <div class="row">

                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <strong>GENERAL ELECTIONS - INDIA, 2019</p> 
                            </div>

                            <div class="col-sm-6" style="display: grid;justify-content: right;">
            <!--                 <p>(Year : 2019)</p>
                                --></div>
                        </div>
                    </div>



                    <div class="">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" style="width: 100%;">
                                <thead>

<div class="exportdivw"> 
                                 
                 <a href="{{'State-wise-overseas-electors-voters-pdf'}}" class="btn pdf show pdfbut">pdf<!---<img src="assets/images/pdf.png">--></a>
               
                   <a href="{{'State-wise-overseas-electors-voters-Excel'}}" class="btn excel show pdfbut">xls<!--<img src="assets/images/excel.jpg">--></a>
                  </div>
                                    
                                    
                                    
                                    <tr class="table-primary">
                                        <th scope="col">PC Type</th>
                                        <th colspan="4">Electors</th>
                                        <th colspan="4">Voters</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>PC</td>
                                        <td>Male</td>
                                        <td>Female</td>
                                        <td>Other</td>
                                        <td>Total Electors</td>
                                        <td>Male</td>
                                        <td>Female</td>
                                        <td>Other</td>
                                        <td>Total Electors</td>
                                    </tr>  
                                    
                                    @forelse($data as $key=>$values)
                                    <tr>
                                        <td>{{$values->PC_TYPE}}</td>
                                        <td>{{$values->emale}}</td>
                                        <td>{{$values->efemale}}</td>
                                        <td>{{$values->eother}}</td>
                                        <td>{{$values->etotal}}</td>
                                        <td>{{$values->votermale}}</td>
                                        <td>{{$values->voterfemale}}</td>
                                        <td>{{$values->totalvotes}}</td>
                                        <td>{{$values->totalvalidvote}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td>Data not found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </body>

</html>