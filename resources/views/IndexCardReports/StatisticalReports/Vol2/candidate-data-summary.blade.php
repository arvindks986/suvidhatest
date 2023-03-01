@extends('layouts.app')
@push('stylesheet')
<style>
th{
    font-size: 13px;
}
td{
    font-size: 12px;
}
  
    .height-100 {
      height: 150px;
      margin-top: 20px;
      margin-bottom: 15px;
    }
    .date{
      margin-top: 10px;
    }
    .wapper {
      width: 100%;
    }
    .topheading {
      padding: 6px 0;
      background: #005AAB;
      color: #f1f1f1;
      width: 100%;
      border-radius: 4px;
      font-size: 1.3rem;
      text-transform: uppercase;
      margin: 10px 0 10px;
    }
    .table-striped>tbody>tr:nth-child(2n+1)>td,
    .table-striped>tbody>tr:nth-child(2n+1)>th {
      background-color: #fff;
    }
    .table-striped>tbody>tr:nth-child(2n+2)>td,
    .table-striped>tbody>tr:nth-child(2n+2)>th {
      background-color: #F1F1F1;
    }
    .table-primary {
      background: rgba(201, 96, 6, 0.5);
    }
    .table td,
    .table th {
      padding: .55rem;
      vertical-align: top;
      border-top: 1px solid #7abaff;
    }
    .table thead th {
      vertical-align: bottom;
      border-bottom: 1px solid #7abaff;
    }
  </style>
@endpush
@section('content')

<div class="wapper">
    <div class="whole">
    <section class="bdrLine">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    
                </div>
                <div class="col-12 text-center">
          <h4 class="display">Election Commission Of India, General Elections, 2019  </h4>
                    <h5 class="display" style="font-weight: bold;text-decoration: underline;padding: 3px;font-size: 18px;">(Candidate Data Summary on Nominations , Rejections,Withdrawals and Deposits Forfeited)</h5>
                </div>



<div class="pull-right exportdiv"> 
        <a href="{{'candidateDataSummaryPDF'}}" class="btn show pdfbut"><img src="./assets/images/pdf.png" style="width: 53px !important;"></a>
        <a href="{{'Candidate-DataSummary-Excel'}}" class="btn  show pdfbut"><img src="./assets/images/excel.jpg" style="position: relative;
    top: -3px;"></a>
   </div>



            </div>
        </div>
    </section>


                            <div class="table-responsive">
                                <table class="table table-bordered" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th colspan="3">Nominations Filed</th>
                                            <th colspan="3">Nominations Rejected</th>
                                            <th colspan="3">Nominations Withdrawn</th>
                                            <th colspan="3">Contesting Candidates</th>
                                            <th colspan="3"> Forfeited Contesting Candidates</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="blc">State/UT</td>
                                            <td class="blc">Men</td>
                                            <td class="blc">Women</td>
                                            <td class="blc">Total</td>
                                            <td class="blc">Men</td>
                                            <td class="blc">Women</td>
                                            <td class="blc">Total</td>
                                            <td class="blc">Men</td>
                                            <td class="blc">Women</td>
                                            <td class="blc">Total</td>
                                            <td class="blc">Men</td>
                                            <td class="blc">Women</td>
                                            <td class="blc">Total</td>
                                            <td class="blc">Men</td>
                                            <td class="blc">Women</td>
                                            <td class="blc">Total</td>
                                        </tr>
                                        <?php $cnom_m_t = 0;?>
                                        
                                        @forelse($pcdetails as $pc)
                                        <tr>
                                            <th colspan="4">Type Of Constituency : </th>
                                            <td colspan="4" style="background: #fff;color: #000;">{{$pc->PC_NAME}}</td>
                                            <th colspan="4">No. oF Seats : </th>
                                            <td colspan="4" style="background: #fff;color: #000;">{{$pc->PC_NO}}</td>
                                        </tr> 
                                        <?php 
                                        
                                        $data = DB::table('m_pc')
                                                ->join('t_pc_ic', 'm_pc.st_code', '=', 't_pc_ic.ST_CODE')
                                                ->where('t_pc_ic.ST_CODE', $stcode)
                                                ->where('t_pc_ic.pc_no', $pc->PC_NO)
                                                ->where('m_pc.PC_NO', $pc->PC_NO)
                                                ->GroupBy('m_pc.PC_TYPE')
                                                ->get();
                                        ?>      

                                        @forelse($data as $condatasummary) 

                                        <tr>
                                            <td><b>{{$condatasummary->PC_TYPE}}</b></td>
                                            <td class="c_nom_m_t"> {{$condatasummary->c_nom_m_t}}</td>
                                            <td class="c_nom_f_t">{{$condatasummary->c_nom_f_t}}</td>
                                            <td class="c_nom_o_t">{{$condatasummary->c_nom_o_t}}</td>
                                            
                                            <td class="c_nom_r_m">{{$condatasummary->c_nom_r_m}}</td>
                                            <td class="c_nom_r_f">{{$condatasummary->c_nom_r_f}}</td>
                                            <td class="c_nom_r_o">{{$condatasummary->c_nom_r_o}}</td>
                                            
                                            <td class="c_nom_w_m">{{$condatasummary->c_nom_w_m}}</td>
                                            <td class="c_nom_w_f">{{$condatasummary->c_nom_w_f}}</td>
                                            <td class="c_nom_w_o">{{$condatasummary->c_nom_w_o}}</td>
                                            
                                            <td class="c_nom_co_m">{{$condatasummary->c_nom_co_m}}</td>
                                            <td class="c_nom_co_f">{{$condatasummary->c_nom_co_f}}</td>
                                            <td class="c_nom_co_t">{{$condatasummary->c_nom_co_t}}</td>
                                            
                                            <td class="c_nom_fd_m">{{$condatasummary->c_nom_fd_m}}</td>
                                            <td class="c_nom_fd_f">{{$condatasummary->c_nom_fd_f}}</td>
                                            <td class="c_nom_fd_t">{{$condatasummary->c_nom_fd_t}}</td>
                                        </tr>
                                        <?php $cnom_m_t += $condatasummary->c_nom_m_t; ?>  
                                        
                                        @empty
                            </tr>
                            <td>Data Not Available</td>
                            </tr>
                            @endforelse
                            
                            @empty
                            </tr>
                            <td>Data Not Available</td>
                            </tr>
                            @endforelse
                                        <tr>
                                            <td style="background: #005aab;color: #fff;font-weight: bold;">Grand Total </td>
                                            <td id="c_nom_m_t"></td>
                                            <td id="c_nom_f_t"></td>
                                            <td id="c_nom_o_t"></td>
                                            <td id="c_nom_r_m"></td>
                                            <td id="c_nom_r_f"></td>
                                            <td id="c_nom_r_o"></td>
                                            <td id="c_nom_w_m"></td>
                                            <td id="c_nom_w_f"></td>
                                            <td id="c_nom_w_o"></td>
                                            <td id="c_nom_co_m"></td>
                                            <td id="c_nom_co_f"></td>
                                            <td id="c_nom_co_t"></td>
                                            <td id="c_nom_fd_m"></td>
                                            <td id="c_nom_fd_f"></td>
                                            <td id="c_nom_fd_t"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                   @endsection
        <script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
        </script>
        <script type="text/javascript">
            var sum = 0;
            $(".c_nom_m_t").each(function () {
                sum += parseFloat($(this).text());
            });

            $('#c_nom_m_t').text(sum);
            var sumtwo = 0;
            $(".c_nom_f_t").each(function () {
                sumtwo += parseFloat($(this).text());
            });
            $('#c_nom_f_t').text(sumtwo);

            var sumthree = 0;
            $(".c_nom_o_t").each(function () {
                sumthree += parseFloat($(this).text());
            });
            $('#c_nom_o_t').text(sumthree);

            var sumfour = 0;
            $(".c_nom_r_m").each(function () {
                sumfour += parseFloat($(this).text());
            });
            $('#c_nom_r_m').text(sumfour);

            var sumfive = 0;
            $(".c_nom_r_f").each(function () {
                sumfive += parseFloat($(this).text());
            });
            $('#c_nom_r_f').text(sumfive);
            var sumsix = 0;
            $(".c_nom_r_o").each(function () {
                sumsix += parseFloat($(this).text());
            });
            $('#c_nom_r_o').text(sumsix);

            var sumseven = 0;
            $(".c_nom_w_m").each(function () {
                sumseven += parseFloat($(this).text());
            });
            $('#c_nom_w_m').text(sumseven);

            var sumeight = 0;
            $(".c_nom_w_f").each(function () {
                sumeight += parseFloat($(this).text());
            });
            $('#c_nom_w_f').text(sumeight);
            var sumnine = 0;
            $(".c_nom_w_o").each(function () {
                sumnine += parseFloat($(this).text());
            });
            $('#c_nom_w_o').text(sumnine);
            var sumten = 0;
            $(".c_nom_co_m").each(function () {
                sumten += parseFloat($(this).text());
            });
            $('#c_nom_co_m').text(sumten);
            var sumeleven = 0;
            $(".c_nom_co_f").each(function () {
                sumeleven += parseFloat($(this).text());
            });
            $('#c_nom_co_f').text(sumeleven);
            var sumtwelve = 0;
            $(".c_nom_co_t").each(function () {
                sumtwelve += parseFloat($(this).text());
            });
            $('#c_nom_co_t').text(sumtwelve);
            var sumthirteen = 0;
            $(".c_nom_fd_m").each(function () {
                sumthirteen += parseFloat($(this).text());
            });
            $('#c_nom_fd_m').text(sumthirteen);

            var sumfourteen = 0;
            $(".c_nom_fd_f").each(function () {
                sumfourteen += parseFloat($(this).text());
            });
            $('#c_nom_fd_f').text(sumfourteen);

            var sumfiveteen = 0;
            $(".c_nom_fd_t").each(function () {
                sumfiveteen += parseFloat($(this).text());
                
            });
            $('#c_nom_fd_t').text(sumfiveteen);
        </script>

    