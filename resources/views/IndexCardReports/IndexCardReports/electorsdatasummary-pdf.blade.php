<?php  $st=getstatebystatecode($st_code);   ?>
<html>
  <head>
    <style>
    td {
    font-size: 12px !important;
    font-weight: 500;
    text-align: left;
    padding: 6px 0px;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
      .bold{
    padding: 12px 0px 0px 30px !important;
    font-weight: bold;
  }
.bolds{
  font-weight: bold;
}

.bolds span{
  font-weight: normal;
}

.bold span{
  font-weight: normal;
}
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .blc{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    border-spacing: 0px;
    }
    .border{
    border: 1px solid #000;
    text-align: left;
    }
    th {
    font-size: 12px;
    font-weight: bold !important;
    }

    table{
    width: 100%;
    border-collapse: collapse;
    font-weight: bold;
    }
    </style>
  </head>
  <div class="bordertestreport">
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p> <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>  </p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><b>SECRETARIAT OF THE <br>ELECTION COMMISSION OF INDIA
            </b>
          <br><b>Nirvachan Sadan, Ashoka Road, New Delhi-110001</b></p>
        </td>
      </tr>
    </table>
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p style="font-size: 15px;"><b>6 - ELECTORS DATA SUMMARY
          </b></p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><strong>State :</strong>{{$st->ST_NAME}}</p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> 27-06-2019</p></td>
      </tr>
    </table>

    <table class="table" style="width: 100%;">
      <thead>
        <tr>
          <th></th>
          <th colspan="3" class="bolds" style="text-align: center;">TYPE OF CONSTITUENCY</th>
          <th></th>
        </tr>
        <tr>
          <th class="bolds blc"></th>
          <th class="bolds blc">GEN</th>
          <th class="bolds blc">SC</th>
          <th class="bolds blc">ST</th>
          <th class="bolds blc">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="bolds">1. NO. OF CONSTITUENCIES
          </td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalgenac']) ? ($electorsvotersdataNew['GEN']['totalgenac']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['totalscac']) ? ($electorsvotersdataNew['SC']['totalscac']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['totalstac']) ? ($electorsvotersdataNew['ST']['totalstac']) :0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalgenac']) ? ($electorsvotersdataNew['GEN']['totalgenac']) : 0) +(isset($electorsvotersdataNew['SC']['totalscac'])? ($electorsvotersdataNew['SC']['totalscac']) :0) + (isset($electorsvotersdataNew['ST']['totalstac'])?$electorsvotersdataNew['ST']['totalstac']:0)}}</td>
        </tr>
        <tr>
          <td colspan="4"><b>2. ELECTORS</b> (Including SERVICE VOTERS)
          </td>
        </tr>
        <tr>
          <td class="bold">a.MALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['maleElectors']) ? ($electorsvotersdataNew['GEN']['maleElectors']) :0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['maleElectors']) ? ($electorsvotersdataNew['SC']['maleElectors']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['maleElectors']) ? ($electorsvotersdataNew['ST']['maleElectors']) : 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['maleElectors']) ? ($electorsvotersdataNew['GEN']['maleElectors']) :0)+(isset($electorsvotersdataNew['SC']['maleElectors'])? ($electorsvotersdataNew['SC']['maleElectors']):0)+(isset($electorsvotersdataNew['ST']['maleElectors'])? ($electorsvotersdataNew['ST']['maleElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b.FEMALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['femaleElectors'])? ($electorsvotersdataNew['GEN']['femaleElectors']):0 )}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['femaleElectors']) ? ($electorsvotersdataNew['SC']['femaleElectors']): 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['femaleElectors']) ? ($electorsvotersdataNew['ST']['femaleElectors']) : 0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['femaleElectors']) ? ($electorsvotersdataNew['GEN']['femaleElectors']) :0)+ (isset($electorsvotersdataNew['SC']['femaleElectors']) ? ($electorsvotersdataNew['SC']['femaleElectors']):0)+(isset($electorsvotersdataNew['ST']['femaleElectors'])? ($electorsvotersdataNew['ST']['femaleElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c.THIRD GENDER</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['thirdElectors']) ? ($electorsvotersdataNew['GEN']['thirdElectors']) : 0) }}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['thirdElectors'])? ($electorsvotersdataNew['SC']['thirdElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['thirdElectors'])? ($electorsvotersdataNew['ST']['thirdElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['thirdElectors']) ? ($electorsvotersdataNew['GEN']['thirdElectors']):0)+ (isset($electorsvotersdataNew['SC']['thirdElectors'])?($electorsvotersdataNew['SC']['thirdElectors']):0)+(isset($electorsvotersdataNew['ST']['thirdElectors'])?($electorsvotersdataNew['ST']['thirdElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">d.TOTAL</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['totalElectors'])? ($electorsvotersdataNew['GEN']['totalElectors']):0)+ (isset($electorsvotersdataNew['SC']['totalElectors']) ? ($electorsvotersdataNew['SC']['totalElectors']):0)+ (isset($electorsvotersdataNew['ST']['totalElectors'])? ($electorsvotersdataNew['ST']['totalElectors']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">3. ELECTORS WHO VOTED
          </td>
        </tr>
        <tr>
          <td class="bold">a.MALE</td>
          <td>{{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b.FEMALE</td>
           <td>{{(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalFemaleVoters'])? ($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c.THIRD GENDER</td>
          <td>{{(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0)+(isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0)}}</td>
        </tr>

        <tr>
          <td class="bold">d. POSTAL <span style="font-weight: normal;"> (Details given in Annxure-1)</span>
          </td>
          <td>{{(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">e.TOTAL</td>
          <td>{{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['GEN']['totalOtherVoters']) ? ($totalvoteNew['GEN']['totalOtherVoters']):0)}}</td>

          <td>{{(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}</td>
          <td>
            {{(isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)+(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0)+(isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)+(isset($totalvoteNew['SC']['totalOtherVoters'])? ($totalvoteNew['SC']['totalOtherVoters']):0)+(isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)}}
        </td>
        </tr>
        <tr>
          <td class="bold">PROXY <span style="font-weight: normal;">(already included in 3.a/3.b)</span>
          </td>
          <td>{{(isset($totalvoteNew['GEN']['proxy_votes'])?($totalvoteNew['GEN']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['proxy_votes'])?($totalvoteNew['SC']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['proxy_votes'])?($totalvoteNew['ST']['proxy_votes']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['proxy_votes'])?($totalvoteNew['GEN']['proxy_votes']):0)+(isset($totalvoteNew['SC']['proxy_votes'])?($totalvoteNew['SC']['proxy_votes']):0)+(isset($totalvoteNew['ST']['proxy_votes'])?($totalvoteNew['ST']['proxy_votes']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">4. OVERSEAS ELECTORS
          </td>
        </tr>
        <tr>
          <td class="bold">a.MALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)}}
          </td>
        </tr>
        <tr>
          <td class="bold">b.FEMALE</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c.THIRD GENDER</td>
           <td>{{(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">d.TOTAL</td>
          <td>{{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)}}</td>
          <td>{{(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}</td>
          <td>
            {{(isset($electorsvotersdataNew['GEN']['overseasmaleElector'])?($electorsvotersdataNew['GEN']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasFemaleElector'])?($electorsvotersdataNew['GEN']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['GEN']['overseasthirdElector'])?($electorsvotersdataNew['GEN']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['SC']['overseasmaleElector'])?($electorsvotersdataNew['SC']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasFemaleElector'])?($electorsvotersdataNew['SC']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['SC']['overseasthirdElector'])?($electorsvotersdataNew['SC']['overseasthirdElector']):0)+(isset($electorsvotersdataNew['ST']['overseasmaleElector'])?($electorsvotersdataNew['ST']['overseasmaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasFemaleElector'])?($electorsvotersdataNew['ST']['overseasFemaleElector']):0)+(isset($electorsvotersdataNew['ST']['overseasthirdElector'])?($electorsvotersdataNew['ST']['overseasthirdElector']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">5. OVERSEAS ELECTORS WHO VOTED
          </td>
        </tr>
        <tr>
          <td class="bold">a.MALE</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b.FEMALE</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">c.THIRD GENDER</td>
           <td>{{(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasthirdvoters'])?$totalvoteNew['GEN']['overseasthirdvoters']:0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">d.TOTAL</td>
          <td>{{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}</td>
          <td>
            {{(isset($totalvoteNew['GEN']['overseasmalevoters'])?($totalvoteNew['GEN']['overseasmalevoters']):0)+(isset($totalvoteNew['GEN']['overseasFemalevoters'])?($totalvoteNew['GEN']['overseasFemalevoters']):0)+(isset($totalvoteNew['GEN']['overseasthirdvoters'])?($totalvoteNew['GEN']['overseasthirdvoters']):0)+(isset($totalvoteNew['SC']['overseasmalevoters'])?($totalvoteNew['SC']['overseasmalevoters']):0)+(isset($totalvoteNew['SC']['overseasFemalevoters'])?($totalvoteNew['SC']['overseasFemalevoters']):0)+(isset($totalvoteNew['SC']['overseasthirdvoters'])?($totalvoteNew['SC']['overseasthirdvoters']):0)+(isset($totalvoteNew['ST']['overseasmalevoters'])?($totalvoteNew['ST']['overseasmalevoters']):0)+(isset($totalvoteNew['ST']['overseasFemalevoters'])?($totalvoteNew['ST']['overseasFemalevoters']):0)+(isset($totalvoteNew['ST']['overseasthirdvoters'])?($totalvoteNew['ST']['overseasthirdvoters']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds" colspan="4">6. REJECTED VOTES
          </td>
        </tr>
        <tr>
          <td class="bold">a. VOTES <span style="font-weight: normal;"> (POSTAL)</span>
          </td>
          <td>{{(isset($totalpostalvoteNew['GEN']['postalrejected'])?($totalpostalvoteNew['GEN']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['SC']['postalrejected'])?($totalpostalvoteNew['SC']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['ST']['postalrejected'])?($totalpostalvoteNew['ST']['postalrejected']):0)}}</td>
          <td>{{(isset($totalpostalvoteNew['GEN']['postalrejected'])?($totalpostalvoteNew['GEN']['postalrejected']):0)+(isset($totalpostalvoteNew['SC']['postalrejected'])?($totalpostalvoteNew['SC']['postalrejected']):0)+(isset($totalpostalvoteNew['ST']['postalrejected'])?($totalpostalvoteNew['ST']['postalrejected']):0)}}</td>
        </tr>
        <tr>
          <td class="bold">b. PERCENTAGE <span style="font-weight: normal;">(to Postal <br> Votes)</td>

          <td>
            {{round((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)/(isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)*100,2)}}
          </td>
          <?php if(isset($totalpostalvoteNew['SC']['postaltotalreceived']) && ($totalpostalvoteNew['SC']['postaltotalreceived'] > 0 )) { ?>
          <td>



          {{round((isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)/(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)*100,2)}}

        </td>
      <?php } else { ?>
        <td>0</td>

      <?php } ?>

      <?php if(isset($totalpostalvoteNew['ST']['postaltotalreceived']) && ($totalpostalvoteNew['ST']['postaltotalreceived'] > 0 )) { ?>

          <td>
          {{round((isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0)/(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0)*100,2)}}

        </td>
         <?php } else { ?>
            <td>0</td>

          <?php } ?>

          <td>{{round(((isset($totalpostalvoterejectedNew['GEN']['postalrejected'])?($totalpostalvoterejectedNew['GEN']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['SC']['postalrejected'])?($totalpostalvoterejectedNew['SC']['postalrejected']):0)+(isset($totalpostalvoterejectedNew['ST']['postalrejected'])?($totalpostalvoterejectedNew['ST']['postalrejected']):0))/((isset($totalpostalvoteNew['GEN']['postaltotalreceived'])?($totalpostalvoteNew['GEN']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['SC']['postaltotalreceived'])?($totalpostalvoteNew['SC']['postaltotalreceived']):0)+(isset($totalpostalvoteNew['ST']['postaltotalreceived'])?($totalpostalvoteNew['ST']['postaltotalreceived']):0))*100,2)}}</td>
        </tr>
        <tr>
        
        <tr>
          <td class="bold">c. VOTES REJECTED FROM <br>EVM <span style="font-weight: bold;">(NOT RETRIVED+TEST <br> VOTES+REJECTED DUE TO OTHER <br> REASON)</span>
          </td>
          <td>{{(isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0)+
          (isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}</td>
          <td>
            {{(isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0)+(isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0)+(isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0)}}

          </td>
        </tr>
        <tr>
          <td class="bolds">7. NOTA VOTES <span style="font-weight: bold;">(POSTAL + EVM)</span></td>
          <td>{{(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0)}}</td>
          <td>{{(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0)+(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0)+(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds">8. VALID VOTES <span style="font-weight: bold;">(EXCLUDING NOTA VOTES) <br> 3.e-(6.a+6.c+7)</span>
          </td>


          <?php $total1 = ((isset($totalvoteNew['GEN']['totalMaleVoters'])?($totalvoteNew['GEN']['totalMaleVoters']):0)+(isset($totalvoteNew['GEN']['totalFemaleVoters'])?($totalvoteNew['GEN']['totalFemaleVoters']):0)
          +(isset($totalvoteNew['GEN']['totalOtherVoters'])?($totalvoteNew['GEN']['totalOtherVoters']):0))-((isset($totalpostalvoteNew['GEN']['postalrejected'])?($totalpostalvoteNew['GEN']['postalrejected']):0)
          +((isset($totalvoteNew['GEN']['votes_not_retreived_from_evm'])?($totalvoteNew['GEN']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['GEN']['rejected_votes_due_2_other_reason'])?($totalvoteNew['GEN']['rejected_votes_due_2_other_reason']):0)+
          (isset($totalvoteNew['GEN']['test_votes_49_ma'])?($totalvoteNew['GEN']['test_votes_49_ma']):0))+(isset($notavoteNew['GEN']['totalEvmPostalvotenota'])?($notavoteNew['GEN']['totalEvmPostalvotenota']):0)); ?>

          <?php $total2 = ((isset($totalvoteNew['SC']['totalMaleVoters'])?($totalvoteNew['SC']['totalMaleVoters']):0)+(isset($totalvoteNew['SC']['totalFemaleVoters'])?($totalvoteNew['SC']['totalFemaleVoters']):0)
          +(isset($totalvoteNew['SC']['totalOtherVoters'])?($totalvoteNew['SC']['totalOtherVoters']):0))-((isset($totalpostalvoteNew['SC']['postalrejected'])?($totalpostalvoteNew['SC']['postalrejected']):0)+((isset($totalvoteNew['SC']['votes_not_retreived_from_evm'])?($totalvoteNew['SC']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['SC']['rejected_votes_due_2_other_reason'])?($totalvoteNew['SC']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['SC']['test_votes_49_ma'])?($totalvoteNew['SC']['test_votes_49_ma']):0))
          +(isset($notavoteNew['SC']['totalEvmPostalvotenota'])?($notavoteNew['SC']['totalEvmPostalvotenota']):0)); ?>

          <?php $total3 = ((isset($totalvoteNew['ST']['totalMaleVoters'])?($totalvoteNew['ST']['totalMaleVoters']):0)+(isset($totalvoteNew['ST']['totalFemaleVoters'])?($totalvoteNew['ST']['totalFemaleVoters']):0)+
          (isset($totalvoteNew['ST']['totalOtherVoters'])?($totalvoteNew['ST']['totalOtherVoters']):0))-((isset($totalpostalvoteNew['ST']['postalrejected'])?($totalpostalvoteNew['ST']['postalrejected']):0)+((isset($totalvoteNew['ST']['votes_not_retreived_from_evm'])?
          ($totalvoteNew['ST']['votes_not_retreived_from_evm']):0)+(isset($totalvoteNew['ST']['rejected_votes_due_2_other_reason'])?($totalvoteNew['ST']['rejected_votes_due_2_other_reason']):0)+(isset($totalvoteNew['ST']['test_votes_49_ma'])?($totalvoteNew['ST']['test_votes_49_ma']):0))+(isset($notavoteNew['ST']['totalEvmPostalvotenota'])?($notavoteNew['ST']['totalEvmPostalvotenota']):0)); ?>

          <td>
            {{ $total1 }}

          </td>
          <td>
             {{ $total2 }}

          </td>
          <td>
             {{ $total3 }}

          </td>
          <td>{{$total1+$total2+$total3}}</td>

        </tr>
        <tr>
          <td class="bolds">9. POLL PERCENTAGE
          </td>
          <?php if(isset($electorsvotersdataNew['GEN']['totalElectors']) && ($electorsvotersdataNew['GEN']['totalElectors'] > 0)) { ?>
            <td>{{round($total1/(isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)*100,2)}}</td>
          
          <?php } else { ?>
          <td>0</td>
        <?php } ?>

          <?php if(isset($electorsvotersdataNew['SC']['totalElectors']) && ($electorsvotersdataNew['SC']['totalElectors'] > 0)) { ?>
          <td>{{round($total2/$electorsvotersdataNew['SC']['totalElectors']*100,2)}}</td>
           <?php } else { ?>
          <td>0</td>
        <?php } ?>

        <?php if(isset($electorsvotersdataNew['ST']['totalElectors']) && ($electorsvotersdataNew['ST']['totalElectors'] > 0)) { ?>
          <td>{{round($total3/(isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)*100,2)}}</td>
           <?php } else { ?>
          <td>0</td>
        <?php } ?>


          <td>{{round(($total1+$total2+$total3)/((isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)+(isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)
            +(isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0))*100,2)}}</td>
        </tr>
        <tr>
          <td class="bolds">10. NO. OF POLLING STATIONS
          </td>
          <td>{{(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0)}}</td>
          <td>{{(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0)
            +(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0)
            +(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0)}}</td>
        </tr>
        <tr>
          <td class="bolds">11. AVERAGE NO. OF <br> ELECTORS PER POLLING <br>STATION <span style="font-weight: bold;">(including Service <br>Electors)</span>
          </td>
          <?php
                if(isset($totalvoteNew['GEN']['totalpollingstation']) && ($totalvoteNew['GEN']['totalpollingstation'] > 0)) { 


              $total4 = (isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)/(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0);

            }else{
              $total4 = 0;
            }
            if(isset($electorsvotersdataNew['SC']['totalElectors']) && ($electorsvotersdataNew['SC']['totalElectors'] > 0)) {
              $total5 = (isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)/(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0);
            } else{
              $total5 = 0;
            }

            if(isset($totalvoteNew['ST']['totalpollingstation']) && ($totalvoteNew['ST']['totalpollingstation'] > 0)) {
              $total6 = (isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)/(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0);
            } else{
              $total6 = 0;
            }
            

          ?>
          <?php if(isset($totalvoteNew['GEN']['totalpollingstation']) && ($totalvoteNew['GEN']['totalpollingstation'] > 0)) { ?>

          <td>{{round((isset($electorsvotersdataNew['GEN']['totalElectors'])?($electorsvotersdataNew['GEN']['totalElectors']):0)/(isset($totalvoteNew['GEN']['totalpollingstation'])?($totalvoteNew['GEN']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>

        <?php if(isset($totalvoteNew['SC']['totalpollingstation']) && ($totalvoteNew['SC']['totalpollingstation']) > 0) { ?>
          <td>{{round((isset($electorsvotersdataNew['SC']['totalElectors'])?($electorsvotersdataNew['SC']['totalElectors']):0)/(isset($totalvoteNew['SC']['totalpollingstation'])?($totalvoteNew['SC']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>
        <?php if(isset($totalvoteNew['ST']['totalpollingstation']) && ($totalvoteNew['ST']['totalpollingstation']) > 0) { ?>
          <td>{{round((isset($electorsvotersdataNew['ST']['totalElectors'])?($electorsvotersdataNew['ST']['totalElectors']):0)/(isset($totalvoteNew['ST']['totalpollingstation'])?($totalvoteNew['ST']['totalpollingstation']):0),0)}}</td>
        <?php } else { ?>
          <td>0</td>
        <?php } ?>
          <td>{{round($total4+$total5+$total6,0)}}</td>
        
        </tr>
      </tbody>
    </table>
    <table>
      <tr style="width: 100%;">
        <td colspan="5" style="text-align: center;border-top: 1px solid #000;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
      </tr>
    </table>
  </div>
</html>
