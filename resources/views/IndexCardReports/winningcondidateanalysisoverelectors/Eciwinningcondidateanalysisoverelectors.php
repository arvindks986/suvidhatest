<?php

namespace App\Http\Controllers\IndexCardReports\winningcondidateanalysisoverelectors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use App\commonModel;
use App\models\Admin\ReportModel;
use App\models\Admin\PollDayModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;
use Excel;

class Eciwinningcondidateanalysisoverelectors extends Controller
{
  public function __construct(){
  $this->middleware('eci');
  $this->commonModel  = new commonModel();
  $this->report_model = new ReportModel();
  $this->voting_model = new PollDayModel();
  if(!Auth::user()){
    return redirect('/officer-login');
  }
  }

public function index(Request $request){
  $user_data = Auth::user();

  $arrayData = DB::select("select ST_CODE,st_name,lead_total_vote1 as total_lead_vote,electors_total1 as E_total,cc1 as Total_Sheet,
zero_to_10,one_to_20,two_to_30,three_to_40,four_to_50,five_to_60,six_to_70,seven_to_80
from(select temp.*,count(cc)as cc1,group_concat(lead_total_vote)lead_total_vote1,
group_concat(electors_total)as electors_total1,
sum(CASE WHEN temp.cc >= '1' AND temp.cc <= '10' = '1' THEN 1 ELSE 0 END) AS zero_to_10,
sum(CASE WHEN temp.cc >= '11' AND temp.cc <= '20' = '1' THEN 1 ELSE 0 END) AS one_to_20,
sum(CASE WHEN temp.cc >= '21' AND temp.cc <= '30' = '1' THEN 1 ELSE 0 END )AS two_to_30,
sum(CASE WHEN temp.cc >= '31' AND temp.cc <= '40' = '1' THEN 1 ELSE 0 END) AS three_to_40,
sum(CASE WHEN temp.cc >= '41' AND temp.cc <= '50' = '1' THEN 1 ELSE 0 END) AS four_to_50,
sum(CASE WHEN temp.cc >= '51' AND temp.cc <= '60' = '1' THEN 1 ELSE 0 END) AS five_to_60,
sum(CASE WHEN temp.cc >= '61' AND temp.cc <= '70' = '1' THEN 1 ELSE 0 END)AS six_to_70,
sum(CASE WHEN temp.cc >= '71' AND temp.cc <= '80' = '1' THEN 1 ELSE 0 END )AS seven_to_80
from ( SELECT mpc.ST_CODE,cdac.PC_NO,
winn.lead_total_vote,sum(cdac.electors_total) as electors_total ,m.st_name,
round((lead_total_vote/sum(cdac.electors_total) *100),0) AS CC
FROM `winning_leading_candidate` AS `winn` INNER JOIN `m_pc` AS `mpc`
ON `mpc`.`ST_CODE` = `winn`.`st_code` AND `mpc`.`PC_NO` = `winn`.`pc_no`
INNER JOIN m_state m ON m.st_code = winn.st_code
INNER JOIN `electors_cdac` AS `cdac` ON `winn`.`st_code` = `cdac`.`st_code`
AND `winn`.`pc_no` = `cdac`.`pc_no` AND cdac.year = 2019
GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
)temp
group by st_code
)
temp1");

///echo "<pre>"; print_r($arrayData);die;

 return view('IndexCardReports/StatisticalReports/Vol2/eciwinning-condidate-analysis-over-total-electors', compact('arrayData','user_data'));

}

public function parveen(Request $request){
  $user_data = Auth::user();

  $arrayData = DB::select("select ST_CODE,st_name,lead_total_vote1 as total_lead_vote,total_vote as E_total,cc1 as Total_Sheet,
zero_to_10,one_to_20,two_to_30,three_to_40,four_to_50,five_to_60,six_to_70,seven_to_80
from(
select temp.*,count(cc)as cc1,group_concat(lead_total_vote)lead_total_vote1,
group_concat(total_vote)as electors_total1,
sum(CASE WHEN temp.cc >= '1' AND temp.cc <= '10' = '1' THEN 1 ELSE 0 END) AS zero_to_10,
sum(CASE WHEN temp.cc >= '11' AND temp.cc <= '20' = '1' THEN 1 ELSE 0 END) AS one_to_20,
sum(CASE WHEN temp.cc >= '21' AND temp.cc <= '30' = '1' THEN 1 ELSE 0 END )AS two_to_30,
sum(CASE WHEN temp.cc >= '31' AND temp.cc <= '40' = '1' THEN 1 ELSE 0 END) AS three_to_40,
sum(CASE WHEN temp.cc >= '41' AND temp.cc <= '50' = '1' THEN 1 ELSE 0 END) AS four_to_50,
sum(CASE WHEN temp.cc >= '51' AND temp.cc <= '60' = '1' THEN 1 ELSE 0 END) AS five_to_60,
sum(CASE WHEN temp.cc >= '61' AND temp.cc <= '70' = '1' THEN 1 ELSE 0 END)AS six_to_70,
sum(CASE WHEN temp.cc >= '71' AND temp.cc <= '80' = '1' THEN 1 ELSE 0 END )AS seven_to_80
from (
SELECT mpc.ST_CODE,cdac.pc_no,
winn.lead_total_vote,sum(cdac.total_vote) as total_vote ,m.st_name,
round((lead_total_vote/sum(cdac.total_vote) *100),0) AS CC
FROM `winning_leading_candidate` AS `winn` INNER JOIN `m_pc` AS `mpc`
ON `mpc`.`ST_CODE` = `winn`.`st_code` AND `mpc`.`PC_NO` = `winn`.`pc_no`
INNER JOIN m_state m ON m.st_code = winn.st_code
INNER JOIN `counting_pcmaster` AS `cdac` ON `winn`.`st_code` = `cdac`.`st_code`
AND `winn`.`pc_no` = `cdac`.`pc_no`
GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
)temp
group by st_code
)
temp1");

///echo "<pre>"; print_r($arrayData);die;

 return view('IndexCardReports/StatisticalReports/Vol2/parveeneciwinning-condidate-analysis-over-total-electors', compact('arrayData','user_data'));

}

    public function winningcpndidateanalysisoverelectorpdf()
    {

      $user_data = Auth::user();

      $arrayData = DB::select("select ST_CODE,st_name,lead_total_vote1 as total_lead_vote,electors_total1 as E_total,cc1 as Total_Sheet,
      zero_to_10,one_to_20,two_to_30,three_to_40,four_to_50,five_to_60,six_to_70,seven_to_80
      from(select temp.*,count(cc)as cc1,group_concat(lead_total_vote)lead_total_vote1,
      group_concat(electors_total)as electors_total1,
      sum(CASE WHEN temp.cc >= '1' AND temp.cc <= '10' = '1' THEN 1 ELSE 0 END) AS zero_to_10,
      sum(CASE WHEN temp.cc >= '11' AND temp.cc <= '20' = '1' THEN 1 ELSE 0 END) AS one_to_20,
      sum(CASE WHEN temp.cc >= '21' AND temp.cc <= '30' = '1' THEN 1 ELSE 0 END )AS two_to_30,
      sum(CASE WHEN temp.cc >= '31' AND temp.cc <= '40' = '1' THEN 1 ELSE 0 END) AS three_to_40,
      sum(CASE WHEN temp.cc >= '41' AND temp.cc <= '50' = '1' THEN 1 ELSE 0 END) AS four_to_50,
      sum(CASE WHEN temp.cc >= '51' AND temp.cc <= '60' = '1' THEN 1 ELSE 0 END) AS five_to_60,
      sum(CASE WHEN temp.cc >= '61' AND temp.cc <= '70' = '1' THEN 1 ELSE 0 END)AS six_to_70,
      sum(CASE WHEN temp.cc >= '71' AND temp.cc <= '80' = '1' THEN 1 ELSE 0 END )AS seven_to_80
      from ( SELECT mpc.ST_CODE,cdac.PC_NO,
      winn.lead_total_vote,sum(cdac.electors_total) as electors_total ,m.st_name,
      round((lead_total_vote/sum(cdac.electors_total) *100),0) AS CC
      FROM `winning_leading_candidate` AS `winn` INNER JOIN `m_pc` AS `mpc`
      ON `mpc`.`ST_CODE` = `winn`.`st_code` AND `mpc`.`PC_NO` = `winn`.`pc_no`
      INNER JOIN m_state m ON m.st_code = winn.st_code
      INNER JOIN `electors_cdac` AS `cdac` ON `winn`.`st_code` = `cdac`.`st_code`
      AND `winn`.`pc_no` = `cdac`.`pc_no` AND cdac.year = 2019
      GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
      )temp
      group by st_code
      )
      temp1");
     $pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/eciwinning-condidate-analysis-over-total-electors-pdf', compact('arrayData'));
       return $pdf->download('winning-condidate-analysis-over-total-electors.pdf');
//
        //winning-condidate-analysis-over-total-electors-pdf
    }



      public function winningcpndidateanalysisoverelectorxls()
    {
      $arrayData = DB::select("select ST_CODE,st_name,lead_total_vote1 as total_lead_vote,electors_total1 as E_total,cc1 as Total_Sheet,
      zero_to_10,one_to_20,two_to_30,three_to_40,four_to_50,five_to_60,six_to_70,seven_to_80
      from(select temp.*,count(cc)as cc1,group_concat(lead_total_vote)lead_total_vote1,
      group_concat(electors_total)as electors_total1,
      sum(CASE WHEN temp.cc >= '1' AND temp.cc <= '10' = '1' THEN 1 ELSE 0 END) AS zero_to_10,
      sum(CASE WHEN temp.cc >= '11' AND temp.cc <= '20' = '1' THEN 1 ELSE 0 END) AS one_to_20,
      sum(CASE WHEN temp.cc >= '21' AND temp.cc <= '30' = '1' THEN 1 ELSE 0 END )AS two_to_30,
      sum(CASE WHEN temp.cc >= '31' AND temp.cc <= '40' = '1' THEN 1 ELSE 0 END) AS three_to_40,
      sum(CASE WHEN temp.cc >= '41' AND temp.cc <= '50' = '1' THEN 1 ELSE 0 END) AS four_to_50,
      sum(CASE WHEN temp.cc >= '51' AND temp.cc <= '60' = '1' THEN 1 ELSE 0 END) AS five_to_60,
      sum(CASE WHEN temp.cc >= '61' AND temp.cc <= '70' = '1' THEN 1 ELSE 0 END)AS six_to_70,
      sum(CASE WHEN temp.cc >= '71' AND temp.cc <= '80' = '1' THEN 1 ELSE 0 END )AS seven_to_80
      from ( SELECT mpc.ST_CODE,cdac.PC_NO,
      winn.lead_total_vote,sum(cdac.electors_total) as electors_total ,m.st_name,
      round((lead_total_vote/sum(cdac.electors_total) *100),0) AS CC
      FROM `winning_leading_candidate` AS `winn` INNER JOIN `m_pc` AS `mpc`
      ON `mpc`.`ST_CODE` = `winn`.`st_code` AND `mpc`.`PC_NO` = `winn`.`pc_no`
      INNER JOIN m_state m ON m.st_code = winn.st_code
      INNER JOIN `electors_cdac` AS `cdac` ON `winn`.`st_code` = `cdac`.`st_code`
      AND `winn`.`pc_no` = `cdac`.`pc_no` AND cdac.year = 2019
      GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
      )temp
      group by st_code
      )
      temp1");

return Excel::create('winning-condidate-analysis-over-electors'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($arrayData) {
                    $excel->sheet('mySheet', function($sheet) use ($arrayData) {
                        $sheet->mergeCells('A1:K1');
                        $sheet->mergeCells('C1:J1');

                        $sheet->cells('A1', function($cells) {
                            $cells->setValue('Winning candidate analysis over total electors');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('A2', function($cells) {
                            $cells->setValue('Name of State/UT');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('B2', function($cells) {
                            $cells->setValue('No. Of Seats');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('C2', function($cells) {
                            $cells->setValue('No. Of Candidates Secured The % Of Votes Over The Total Electors In The Constituency');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });

                        $last_key = 0;
                        $last = $last_key + 10;
                        $col = 'B' . $last . ':' . 'J' . $last;

                        $sheet->cells($col, function($cells) {
                            $cells->setFont(array(
                                'name' => 'Times New Roman',
                                'size' => 12,
                                'bold' => true
                            ));

                            $cells->setAlignment('center');
                        });


                        $sheet->cell('A3', function($cell) {
                            $cell->setValue('');
                        });
                        $sheet->cell('B3', function($cell) {
                            $cell->setValue('');
                        });
                        $sheet->cell('C3', function($cell) {
                            $cell->setValue('Winner with <= 10%');
                        });

                        $sheet->cell('D3', function($cell) {
                            $cell->setValue('Winner with >10% to <= 20%');
                        });
                        $sheet->cell('E3', function($cell) {
                            $cell->setValue('Winner with >20% to <=30%');
                        });
                        $sheet->cell('F3', function($cell) {
                            $cell->setValue('Winner with >30% to <=40%');
                        });

                        $sheet->cell('G3', function($cell) {
                            $cell->setValue('Winner with >40% to <=50%');
                        });
                        $sheet->cell('H3', function($cell) {
                            $cell->setValue('Winner with >50% to <=60%');
                        });
                        $sheet->cell('I3', function($cell) {
                            $cell->setValue('Winner with >60% to <=70%');
                        });

                        $sheet->cell('J3', function($cell) {
                            $cell->setValue('Winner with > 70%');
                        });
                        $i = 4;
                        if (!empty($arrayData)) {
                            foreach ($arrayData as $key => $values) {

                                $sheet->cell('A' . $i, $values->st_name);
                                $sheet->cell('B' . $i, $values->Total_Sheet);
                                $sheet->cell('C' . $i, $values->zero_to_10);
                                $sheet->cell('E' . $i, $values->one_to_20);
                                $sheet->cell('F' . $i, $values->two_to_30);
                                $sheet->cell('G' . $i, $values->three_to_40);
                                $sheet->cell('H' . $i, $values->four_to_50);
                                $sheet->cell('I' . $i, $values->five_to_60);
                                $sheet->cell('J' . $i, $values->six_to_70);
                                $sheet->cell('K' . $i, $values->seven_to_80);

                           $i++; }
                        }
                    });
                })->export();

    }


}
