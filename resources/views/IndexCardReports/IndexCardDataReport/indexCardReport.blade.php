@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Index Card Data Report')
@section('content')
<style>
	    /*body {
	      font-family: 'Roboto', sans-serif;
	      background-image: radial-gradient(circle, #e6f3ff, #e0eefc, #daeaf8, #d4e5f5, #cee1f2);
	      font-size: 0.85rem;
	      padding-bottom: 20px;
	    }*/

    .height-100 {
      height: 150px;
      margin-top: 5%;
      margin-bottom: 15px;
    }
.table-hover .table-primary:hover {
    background-color: #f0587e !important;
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

   
    .table-primary {
      background: rgba(201, 96, 6, 0.5);
    }

    .table td,
    .table th {
      padding: .55rem;
      vertical-align: top;
      border-top: 1px solid #7abaff !important;
    }

    th {
      border-top: 1px solid #7abaff;
    }

    .table thead th {
      vertical-align: middle;
      border-bottom: 1px solid #7abaff;
    }

    .bdr-top {
      border-top: 1px solid #7abaff;
    }

    .topheader {
      position: relative;
      top: 0px;
    }

    .topheader p {
      text-align: right;
      font-weight: 700;
    }

    .bdrLine {
      width: 100%;
      margin: auto 0;
      padding: 0 0 30px 0;
      border-bottom: solid #101010 3px;
    }

    @media (max-width: 991px) {
      .topheader {
        width: 100%;
        position: static;
        margin-top: 15px;
      }

      .topheader p:first-child {
        text-align: left;
        width: 49%;
      }

      .topheader p:last-child {
        text-align: right;
        float: right;
        width: 49%;
        margin-top: -39px;
      }
    }
  </style>
<section class="">
    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(Index Card Data Report)</h4></div> 
                        <div class="col">
                            <p class="mb-0 text-right">
                            <a href="IndexCardDataReportPDF" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
                            <a href="IndexCardDataReportCSV" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
                            </p>
                        </div>
                    </div>
                </div>
<!---Report Loop--->
<?php $count = 1; ?>
@foreach($indexCardData as $value)
    <div class="container">
        <div class="row">
            <div class="col-sm-12 topheader">
                <p>Type of Constituency &nbsp;&nbsp;{{$value->PC_TYPE}}</p>
                <p> Year Of Election &nbsp;&nbsp; 2019</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p><strong>State :</strong> {{$session['admin_login_details']['placename']}} </p>
            </div>
            <div class="col">
                <p class="text-right"> <strong>Number & Name of Parliamentary Constituency</strong>&nbsp;&nbsp;&nbsp; {{$value->PC_NO}} : {{$value->PC_NAME}} </p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="table-primary">
                                <th scope="col"> I</th>
                                <th scope="col">Candidates</th>
                                <th scope="col">Male</th>
                                <th scope="col">Female</th>
                                <th scope="col">Thrid Gender</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Nominated </td>
                                <td>{{$value->c_nom_m_t}}</td>
                                <td>{{$value->c_nom_f_t}}</td>
                                <td>{{$value->c_nom_o_t}}</td>
                                <td>{{$value->c_nom_a_t}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Nomination Rejected</td>
                                <td>{{$value->c_nom_r_m}}</td>
                                <td>{{$value->c_nom_r_f}}</td>
                                <td>{{$value->c_nom_r_o}}</td>
                                <td>{{$value->c_nom_r_a}}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Withdrawn </td>
                                <td>{{$value->c_nom_w_m}}</td>
                                <td>{{$value->c_nom_w_f}}</td>
                                <td>{{$value->c_nom_w_o}}</td>
                                <td>{{$value->c_nom_w_t}}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Contested </td>
                                <td>{{$value->c_nom_co_m}}</td>
                                <td>{{$value->c_nom_co_f}}</td>
                                <td>{{$value->c_nom_co_o}}</td>
                                <td>{{$value->c_nom_co_t}}</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Deposit Forfeited</td>
                                <td>{{$value->c_nom_fd_m}}</td>
                                <td>{{$value->c_nom_fd_f}}</td>
                                <td>{{$value->c_nom_fd_o}}</td>
                                <td>{{$value->c_nom_fd_t}}</td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr class="table-primary">
                                <th rowspan="2" valign="middle"> II</th>
                                <th scope="col" rowspan="2" valign="middle">ELECTORS</th>
                                <th scope="col" colspan="2" align="center" style="text-align:center">GENERAL</th>
                                <th scope="col" rowspan="2">SERVICE </th>
                                <th scope="col" rowspan="2">TOTAL</th>
                            </tr>

                            <tr class="table-primary">
                                <th scope="col" rowspan="2">Other Than NRIs</th>
                                <th scope="col">NRIs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Male </td>
                                <td>{{$value->e_gen_m}}</td>
                                <td>{{$value->e_nri_m}}</td>
                                <td>{{$value->e_ser_m}}</td>
                                <td>{{$value->e_all_t_m}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Female</td>
                                <td>{{$value->e_gen_f}}</td>
                                <td>{{$value->e_nri_f}}</td>
                                <td>{{$value->e_ser_f}}</td>
                                <td>{{$value->e_all_t_f}}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Third Gender (Not applicable to service electors)</td>
                                <td>{{$value->e_gen_o}}</td>
                                <td>{{$value->e_nri_o}}</td>
                                <td>{{$value->e_ser_o}}</td>
                                <td>{{$value->e_all_t_o}}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Total </td>
                                <td>{{$value->e_gen_t}}</td>
                                <td>{{$value->e_nri_t}}</td>
                                <td>{{$value->e_ser_t}}</td>
                                <td>{{$value->e_all_t}}</td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr class="table-primary">
                                <th scope="col" rowspan="2"> III</th>
                                <th scope="col" colspan="2" rowspan="2" align="center" style="text-align:center">VOTERS TURNED UP FOR VOTING
                                </th>
                                <th scope="col" colspan="2">VOTERS TURNED UP FOR VOTING</th>
                                <th rowspan="2">Total</th>
                            </tr>
                            <tr class="table-primary">
                                <th scope="col">Other Than NRIs</th>
                                <th scope="col">NRIs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td colspan="2"> Male </td>
                                <td>{{$value->vt_gen_m}}</td>
                                <td>{{$value->vt_nri_m}}</td>
                                <td>{{$value->vt_m_t}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td colspan="2">Female</td>
                                <td>{{$value->vt_gen_f}}</td>
                                <td>{{$value->vt_nri_f}}</td>
                                <td>{{$value->vt_f_t}}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td colspan="2">Third Gender </td>
                                <td>{{$value->vt_gen_o}}</td>
                                <td>{{$value->vt_nri_o}}</td>
                                <td>{{$value->vt_o_t}}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td colspan="2">Total (Male + Female + Third Gender)</td>
                                </td>
                                <td>{{$value->vt_gen_t}}</td>
                                <td>{{$value->vt_nri_t}}</td>
                                <td>{{$value->vt_all_t}}</td>
                            </tr>

                        </tbody>
                        <thead>
                            <tr class="table-primary bdr-top">
                                <th scope="col"> IV</th>
                                <th scope="col" colspan="5">DETAILS OF VOTES POLLED ON EVM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td colspan="4">Total Votes Polled on EVM </td>
                                <td>{{$value->t_votes_evm}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td colspan="4">Test Votes Under Rule 49 MA</td>
                                <td>{{$value->mock_poll_evm}}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td colspan="4">Votes not Retrieved form EVM</td>
                                <td>{{$value->not_retrieved_vote_evm}}</td>
                            </tr>

                            <tr>
                                <td>4</td>
                                <td colspan="4">Rejected Votes (Due to Other Reasons) </td>
                                <td>{{$value->r_votes_evm}}</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td colspan="4">Votes Polled for 'NOTA' on EVM</td>
                                <td>{{$value->nota_vote_evm}}</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td colspan="4">Total of Test Votes + Votes not Retrieved + Votes Rejected ( due to other reasons) + 'NOTA' [2+3+4+5]
                                </td>
                                <td>{{$value->v_r_evm_all}}</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td colspan="4">Total Valid Votes Counted from EVM [1-6]</td>
                                <td>{{$value->v_votes_evm_all}}</td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr class="table-primary bdr-top">
                                <th scope="col"> V </th>
                                <th scope="col" colspan="5">DETAILS OF POSTAL VOTES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td colspan="4">Postal Votes Counted</td>
                                <td>{{$value->postal_vote_ser_o}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td colspan="4">Postal Votes Rejected</td>
                                <td>{{$value->postal_vote_rejected}}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td colspan="4">Postal Votes Polled for 'NOTA'</td>
                                <td>{{$value->postal_vote_nota}}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td colspan="4">Total of Postal Votes Rejected + 'NOTA' [2+3]</td>
                                <td>{{$value->postal_vote_rejected+$value->postal_vote_nota}}</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td colspan="4">Total Valid Postal Votes [1-4]</td>
                                <td>{{$value->postal_valid_votes}}</td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr class="table-primary bdr-top">
                                <th scope="col"> VI </th>
                                <th scope="col" colspan="5">COMBINED DETAILS OF EVM & POSTAL VOTES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td colspan="4">Total Votes Polled (IV(1) + V(1))</td>
                                <td>{{$value->total_votes_polled}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td colspan="4">Total of Votes not Retrived and Rejected + test Votes +NOTA(IV 6 + V 4) </td>
                                <td>{{$value->total_not_count_votes}}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td colspan="4">Total Valid Votes (IV(7) + V(5)) </td>
                                <td>{{$value->total_valid_votes}}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td colspan="4">Total Votes Polled for NOTA (IV-5 + V-3)</td>
                                <td>{{$value->total_votes_nota}}</td>
                            </tr>
                        </tbody>

                        <thead>
                            <tr class="table-primary bdr-top">
                                <tr class="table-primary bdr-top">
                                    <th scope="col"> VII </th>
                                    <th scope="col" colspan="5">MISCELLANEOUS</th>
                                </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td colspan="4"> Proxy Votes</td>
                                <td>{{$value->proxy_votes}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td colspan="4">Tendered Votes </td>
                                <td>{{$value->tendered_votes}}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td colspan="4">Total Number of polling station set up in the Constituency </td>
                                <td>{{$value->total_no_polling_station}}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td colspan="4">Average Number of Electors per polling station in a Constituency</td>
                                <td>{{$value->avg_elec_polling_stn}}</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td colspan="4">Date(s) of Poll</td>
                                <td><?php echo date('d M-Y', strtotime($value->dt_poll)); ?></td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td colspan="2">Date(s) of Re-poll ,if any</td>
                                <td><?php echo ($value->dt_repoll)?date('d M-Y', strtotime($value->dt_repoll)):0; ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td colspan="2">Number of Polling Station where Repoll was ordered (mention date of order also)</td>
                                <td>{{$value->re_poll_station}}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td colspan="4">Date(s) of Counting</td>
                                <td><?php echo date('d M-Y', strtotime($value->dt_counting)); ?></td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td colspan="4">Date of Decleration of Result</td>
                                <td><?php echo date('d M-Y', strtotime($value->dt_declare)); ?></td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td colspan="4">Whether this is Bye-election or Countermanded Election?</td>
                                <td><?php echo ($value->flag_bye_counter==0) ? 'NO' : 'YES'?></td>
                            </tr>
                            <tr>
                                <td>11</td>
                                <td colspan="3">If Yes, reason thereof</td>
                                <td><?php echo ($value->flag_bye_counter==0) ? 'NO' : 'YES'?></td>
                                <td>{{$value->flag_bye_counter_reason}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered table-striped">
                        <caption>PC No. 1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DETAILS OF VOTES POLLED BY EACH CANDIDATE</caption>
                        <thead>
                            <tr class="table-primary">
                                <th scope="col"> SNo</th>
                                <th scope="col">Candidates Name</th>
                                <th scope="col">Sex</th>
                                <th scope="col">Age</th>
                                <th scope="col">Category </th>
                                <th scope="col">Party Name</th>
                                <th scope="col">Symbol Alloted</th>
                                @foreach($value->allaclist as $ackey => $acvalue)
                                <th scope="col">{{$ackey}} : {{$acvalue}}</th>
                                @endforeach
                                <th scope="col">Valid Postal Votes</th>
                                <th scope="col">Total Valid Votes (EVM+Postal)</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php $cCount = 1; ?>
                        	@foreach($value->candidate_data as $cd)
                            <tr>
                                <td>{{$cCount}}</td>
                                <td>{{ucfirst($cd['candidate_name'])}}</td>
                                <td>{{ucfirst($cd['cand_gender'])}}</td>
                                <td>{{$cd['cand_age']}}</td>
                                <td>{{ucfirst($cd['cand_category'])}}</td>
                                <td>{{$cd['party_name']}}</td>
                                <td>{{$cd['symb_desc']}}</td>
                                <?php $cvTotal = 0; ?>
                                @foreach($cd['votescountacwise'] as $vcac)
                                <?php $cvTotal += $vcac; ?>
                                <td>{{$vcac}}</td>
                                @endforeach
                                <td>{{$cd['postaltotalvote']}}</td>
                                <td>{{$cvTotal}}</td>
                            </tr>
                            <?php $cCount++; ?>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="responsive"></div>
                <p></p>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">

                        <thead>
                            <tr class="table-primary">
                                <th scope="col" rowspan="1">PC No. </th>
                                <th scope="col" colspan="4">{{$value->pc_no}}</th>
                                <th scope="col" colspan="11">DETAILS OF ELECTORS -ASSEMBLY SEGMENT WISE</th>

                            </tr>
                            <tr class="table-primary">
                                <th scope="col" rowspan="2"> AC No. and AC Name </th>
                                <th scope="col" colspan="4">General(Other then NRIs)</th>
                                <th scope="col" colspan="4">General(NRIs)</th>
                                <th scope="col" colspan="3">Service</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr class="table-primary">
                                <th scope="col"> Male </th>
                                <th scope="col">Female</th>
                                <th scope="col">Third Gender</th>
                                <th scope="col">Total</th>
                                <th scope="col"> Male </th>
                                <th scope="col">Female</th>
                                <th scope="col">Third Gender</th>
                                <th scope="col">Total</th>
                                <th scope="col"> Male </th>
                                <th scope="col">Female</th>
                                <th scope="col">Total</th>
                                <th scope="col"> Male </th>
                                <th scope="col">Female</th>
                                <th scope="col">Third Gender</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if(!isset($value->electorsDataACWise) || empty($value->electorsDataACWise)) {?>
                                <tr>
                                    <td colspan="16" style="text-align: center;">No Data Found !!</td>
                                </tr>
                            <?php }else{?>
                            @foreach($value->electorsDataACWise as $edac)
                            <tr>
                                <td>{{$edac->ac_no}} : {{$edac->ac_name}} ({{$edac->ac_type}})</td>
                                <td>{{$edac->e_gen_m}}</td>
                                <td>{{$edac->e_gen_f}}</td>
                                <td>{{$edac->e_gen_o}}</td>
                                <td>{{$edac->e_gen_m + $edac->e_gen_f + $edac->e_gen_o}}</td>
                                <td>{{$edac->e_nri_m}}</td>
                                <td>{{$edac->e_nri_f}}</td>
                                <td>{{$edac->e_nri_o}}</td>
                                <td>{{$edac->e_nri_m + $edac->e_nri_f + $edac->e_nri_o}}</td>
                                <td>{{$edac->e_ser_m}}</td>
                                <td>{{$edac->e_ser_f}}</td>
                                <td>{{$edac->e_ser_m + $edac->e_ser_f}}</td>
                                <td>{{$edac->e_gen_m + $edac->e_nri_m + $edac->e_ser_m}}</td>
                                <td>{{$edac->e_gen_f + $edac->e_nri_f + $edac->e_ser_f}}</td>
                                <td>{{$edac->e_gen_o + $edac->e_nri_o}}</td>
                                <td>{{$edac->e_gen_m + $edac->e_gen_f + $edac->e_gen_o + $edac->e_nri_m + $edac->e_nri_f + $edac->e_nri_o + $edac->e_ser_m + $edac->e_ser_f}}</td>
                            </tr>
                            @endforeach
                            <?php }?>
                            </tr>

                            <tr>
                                <td colspan="16">
                                    <p class="p-2">Certified that the Election Index Card has been checked with Forms 3A, 4, 7A, 20 and 21C or 21D or 21E and R.O.'s Report etc. and that there is no discrapancy. Further it is certified that the Party Affiliations and symbols alloted have been verified from the list of contesting candidates in form 7A.</p>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="8">
                                    <div class="d-block"><strong>(Signature and Seal)</strong></div>
                                    <p class="p-1"></p>
                                    Chief Electoral Officer
                                </td>
                                <td colspan="8">
                                    <div class="d-block"><strong>Signature </strong></div>
                                    <p class="p-1"></p>
                                    (Seal) Returning Officer
                                </td>

                            </tr>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php $count++; break; ?>
@endforeach
<!---Report Loop--->
</div>
@endsection
@push('scripts')

@endpush