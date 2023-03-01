<?php

namespace App\Http\Controllers\IndexCardReports\StatisticalReportPC;
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

class StatisticalReportControllertwo extends Controller
{
  public function __construct(){
       $this->middleware(['auth:admin', 'auth']);
       $this->middleware(function (Request $request, $next) {
           if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
           }

           $user = Auth::user();
           switch ($user->role_id) {
               case '7':
                   $this->middleware('eci');
                   break;
               case '4':
                   $this->middleware('ceo');
                   break;
               case '18':
                   $this->middleware('ro');
                   break;
               case '27':
                   $this->middleware('eci_index');
                   break;

               default:
                   $this->middleware('eci');
           }
           return $next($request);
       });

            $this->middleware('adminsession');
            $this->commonModel = new commonModel();
           // $this->ceomodel = new ACCEOModel();
           // $this->acceoreportModel = new ACCEOReportModel();
            //$this->xssClean = new xssClean;
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
AND `winn`.`pc_no` = `cdac`.`pc_no` AND cdac.year = 2019 AND cdac.ELECTION_ID = '1'
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
      AND `winn`.`pc_no` = `cdac`.`pc_no` AND cdac.year = 2019 AND cdac.ELECTION_ID = '1'
      GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
      )temp
      group by st_code
      )
      temp1");
     $pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/eciwinning-condidate-analysis-over-total-electors-pdf', compact('arrayData'));
       return $pdf->download('Winning Candidate Analysis Over Total Electors.pdf');
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
      AND `winn`.`pc_no` = `cdac`.`pc_no` AND cdac.year = 2019 AND cdac.ELECTION_ID = '1'
      GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
      )temp
      group by st_code
      )
      temp1");

return Excel::create('Winning Candidate Analysis Over Total Electors', function($excel) use ($arrayData) {
                    $excel->sheet('mySheet', function($sheet) use ($arrayData) {
                        $sheet->mergeCells('A1:J1');
                        $sheet->mergeCells('A2:J2');

                        $sheet->cells('A1', function($cells) {
                            $cells->setValue('31 - Winning Candidate Analysis Over Total Electors');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                            $cells->setAlignment('center');
                        });
						
						$sheet->cells('A2', function($cells) {
                            $cells->setValue('No. Of Candidates Secured The % Of Votes Over The Total Electors In The Constituency');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                            $cells->setAlignment('center');
                        });

						$sheet->getStyle('A3')->getAlignment()->setWrapText(true);
						$sheet->getStyle('B3')->getAlignment()->setWrapText(true);
						$sheet->getStyle('C3')->getAlignment()->setWrapText(true);
						$sheet->getStyle('D3')->getAlignment()->setWrapText(true);
						$sheet->getStyle('E3')->getAlignment()->setWrapText(true);
						$sheet->getStyle('F3')->getAlignment()->setWrapText(true);
						$sheet->getStyle('G3')->getAlignment()->setWrapText(true);
						$sheet->getStyle('H3')->getAlignment()->setWrapText(true);
						$sheet->getStyle('I3')->getAlignment()->setWrapText(true);
						$sheet->getStyle('J3')->getAlignment()->setWrapText(true);
						
						
						$sheet->setSize('A3', 25,50);
						$sheet->setSize('C3', 20,50);
						$sheet->setSize('D3', 20,50);
						$sheet->setSize('E3', 20,50);
						$sheet->setSize('F3', 20,50);
						$sheet->setSize('G3', 20,50);
						$sheet->setSize('H3', 20,50);
						$sheet->setSize('I3', 20,50);
						$sheet->setSize('J3', 20,50);


                        $sheet->cells('A3', function($cells) {
                            $cells->setValue('NAME OF STATE/UT');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('B3', function($cells) {
                            $cells->setValue('NO. OF SEATS');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('C3', function($cells) {
                            $cells->setValue('WINNER WITH OR BELOW 10%');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });



						$sheet->cells('D3', function($cells) {
                            $cells->setValue('WINNER WITH OR BELOW 20% OR ABOVE 10%');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('E3', function($cells) {
                            $cells->setValue('WINNER WITH OR BELOW 30% OR ABOVE 20%');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('F3', function($cells) {
                            $cells->setValue('WINNER WITH OR BELOW 40% OR ABOVE 30%');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });


						$sheet->cells('G3', function($cells) {
                            $cells->setValue('WINNER WITH OR BELOW 50% OR ABOVE 40%');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('H3', function($cells) {
                            $cells->setValue('WINNER WITH OR BELOW 60% OR ABOVE 50%');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('I3', function($cells) {
                            $cells->setValue('WINNER WITH OR BELOW 70% OR ABOVE 70%');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
						$sheet->cells('J3', function($cells) {
                            $cells->setValue('WINNER WITH OR BELOW 70%');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });


                        $i = 4;
                        if (!empty($arrayData)) {
							
							$all_total_sheet = $zero_to_10 = $one_to_20 = $two_to_30 = $three_to_40 = $four_to_50 = $five_to_60 = $six_to_70 = $seven_to_80 = 0;
							
                            foreach ($arrayData as $key => $values) {
								
								//$sheet->getStyle("A$i")->getAlignment()->setWrapText(true);

                                $sheet->cell('A' . $i, $values->st_name);
                                $sheet->cell('B' . $i, ($values->Total_Sheet > 0 )? $values->Total_Sheet: '=(0)');
                                $sheet->cell('C' . $i, ($values->zero_to_10 > 0 )? $values->zero_to_10: '=(0)');
                                $sheet->cell('D' . $i, ($values->one_to_20 > 0 )? $values->one_to_20: '=(0)');
                                $sheet->cell('E' . $i, ($values->two_to_30 > 0 )? $values->two_to_30: '=(0)');
                                $sheet->cell('F' . $i, ($values->three_to_40 > 0 )? $values->three_to_40: '=(0)');
                                $sheet->cell('G' . $i, ($values->four_to_50 > 0 )? $values->four_to_50: '=(0)');
                                $sheet->cell('H' . $i, ($values->five_to_60 > 0 )? $values->five_to_60: '=(0)');
                                $sheet->cell('I' . $i, ($values->six_to_70 > 0 )? $values->six_to_70: '=(0)');
                                $sheet->cell('J' . $i, ($values->seven_to_80 > 0 )? $values->seven_to_80: '=(0)');

							$all_total_sheet += $values->Total_Sheet;
							$zero_to_10 += $values->zero_to_10;
							$one_to_20 += $values->one_to_20;
							$two_to_30 += $values->two_to_30;
							$three_to_40 += $values->three_to_40;
							$four_to_50 += $values->four_to_50;
							$five_to_60 += $values->five_to_60;
							$six_to_70 += $values->six_to_70;
							$seven_to_80 += $values->seven_to_80;


                           $i++; 
						   }
						   $i++;
						   
						    $sheet->cell('A' . $i, 'Total Seats');
							$sheet->cell('B' . $i, $all_total_sheet);
							$sheet->cell('C' . $i, ($zero_to_10 > 0 )? $zero_to_10: '=(0)');
							$sheet->cell('D' . $i, ($one_to_20 > 0 )? $one_to_20: '=(0)');
							$sheet->cell('E' . $i, ($two_to_30 > 0 )? $two_to_30: '=(0)');
							$sheet->cell('F' . $i, ($three_to_40 > 0 )? $three_to_40: '=(0)');
							$sheet->cell('G' . $i, ($four_to_50 > 0 )? $four_to_50: '=(0)');
							$sheet->cell('H' . $i, ($five_to_60 > 0 )? $five_to_60: '=(0)');
							$sheet->cell('I' . $i, ($six_to_70 > 0 )? $six_to_70: '=(0)');
							$sheet->cell('J' . $i, ($seven_to_80 > 0 )? $seven_to_80: '=(0)');
						   
						   
                        }
                    });
                })->export();

    }


}
