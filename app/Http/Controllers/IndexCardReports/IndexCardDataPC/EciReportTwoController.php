<?php

namespace App\Http\Controllers\IndexCardReports\IndexCardDataPC;
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
use PDF;
use App\commonModel;
use App\models\Admin\ReportModel;
use App\models\Admin\PollDayModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;
use Excel;
class EciReportTwoController extends Controller
{
  public function __construct(){
    $this->middleware('eci');
    $this->commonModel  = new commonModel();
    $this->report_model = new ReportModel();
    $this->voting_model = new PollDayModel();
    $this->commonModel = new commonModel();
    if(!Auth::user()){
      return redirect('/officer-login');
    }
  }

  public function detailsofrepollheld(Request $request)
  {

         $user = Auth::user();
         $uid=$user->id;
     $rowdatas = DB::table('t_pc_ic as ic')
               ->select('r.t_pc_ic_id','r.dt_repoll','r.no_repoll',
                      DB::raw('sum(ic.total_no_polling_station) as tpolling'),
                      'pc.PC_NAME','state.ST_NAME','pc.ST_CODE','pc.PC_NO')
                    ->join('m_state as state', 'ic.st_code', '=', 'state.st_code')
                     ->join('m_pc as pc', function($query) {
                          $query->on('pc.PC_NO', '=', 'ic.PC_NO')
                          ->on('pc.ST_CODE', '=', 'ic.st_code');
                      })
                       ->leftjoin('repoll_pc_ic as r', 'r.t_pc_ic_id', '=', 'ic.id')
                  //->groupby('r.t_pc_ic_id')
                  ->groupby('state.st_code','pc.PC_NO')
                  ->get()->toarray();

                  //dd($data);
                      $data=array();
                      $temp=array();
                      $polling=array();
                      $totalrepoll = 0;
                      $totalpolling = 0;
                      $stname = '';
                      $i=0;
                      $total_no_polling_station = 0;


                  foreach($rowdatas as $key=> $rowdata){

                    $i = ($rowdata->ST_NAME==$stname)?$i:0;

                       $totalpolling = ($rowdata->ST_NAME==$stname)?$totalpolling:0;
                       $data[$rowdata->ST_CODE]['state_name'] = $rowdata->ST_NAME;
                       $data[$rowdata->ST_CODE]['total_no_polling_station'] = $totalpolling + $rowdata->tpolling;
                       $totalpolling  = $rowdata->tpolling;
                      //$polling[] =$rowdata->ST_NAME;
                    //$dataArray['stcode'][$rowdata->PC_NO] = $rowdata->PC_NAME;
                    $data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NO'] = $rowdata->PC_NO;
                    $data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NAME'] = $rowdata->PC_NAME;
                    $data[$rowdata->ST_CODE]['pcinfo'][$i]['no_repoll'] = $rowdata->no_repoll;
                    $data[$rowdata->ST_CODE]['pcinfo'][$i]['dt_repoll'] = $rowdata->dt_repoll;
                    //$data[$rowdata->ST_CODE]['totalrepoll'][] = $totalrepoll+=$rowdata->no_repoll;
                    $data[$rowdata->ST_CODE]['totalrepoll'][] =  $rowdata->no_repoll;
                    $sumdata = @array_sum($data[$rowdata->ST_CODE]['totalrepoll']);

                    $i++;
                    $stname = $rowdata->ST_NAME;
                    //$dataArray['stcode']['totalrepoll']  = $rowdata->dt_repoll;
                }
echo "<pre>";
print_r($data);
die;


      return view('IndexCardReports/StatisticalReports/Vol2/ACdetails-of-repoll-held',  compact('data','user_data'));
  }

public function hello(Request $request){
  $user = Auth::user();
  $uid = $user->id;

dd("hello");
}


public function performanceofnationalparties(Request $request) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $user;
        $data = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
			(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
			 WHERE a.party_id = cp.party_id AND a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
			FROM
			(SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
			 COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
			JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
			JOIN m_party q ON p.party_id=q.ccode
			WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
			(SELECT partyname,SUM(evm_vote)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
			JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
			ON a.partyname=b.partyname');

        $totalElectors = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac` WHERE year = 2019');

        $totalVotes = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');

        return view('IndexCardReports/StatisticalReports/Vol1/acperformanceofnatiionalparties', compact('data', 'totalElectors', 'totalVotes', 'user_data'));
       
    }

    
    public function performanceofnatiionalpartiespdf(Request $request) {

        
        $user = Auth::user();
        $user_data = $user;

        $data = DB::select('SELECT a.partyname,contested,won,vote AS evm_vote,total_vote,
			(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1 where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .16 THEN 1 ELSE 0 END) as fd FROM `counting_pcmaster` as cp
			 WHERE a.party_id = cp.party_id AND a.party_id != (select lead_cand_partyid from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code) group by cp.party_id) as fd
			FROM
			(SELECT party_id,partyname,COUNT(DISTINCT p.candidate_id)contested,
			 COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
			JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
			JOIN m_party q ON p.party_id=q.ccode
			WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
			(SELECT partyname,SUM(evm_vote)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
			JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
			ON a.partyname=b.partyname');

        
        $totalElectors = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac` WHERE year = 2019');


        $totalVotes = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');

        $pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol1.acperformanceofnationalparties-pdf', compact('data', 'totalElectors', 'totalVotes', 'user_data'));
        return $pdf->download('performance-of-national-parties-pdf.pdf');
    }

    public function successfullcondidate(Request $request){

  $user = Auth::user();
  $uid = $user->id;
$user_data = $user;
  $rows = array('m.PC_TYPE','m.PC_NAME','m.PC_NO','winn.st_name','winn.lead_cand_name','winn.lead_party_abbre','symbol.SYMBOL_DES','winn.margin','winn.trail_total_vote','winn.st_code');

  $successfullcondidate = DB::table('winning_leading_candidate as winn')
          ->select($rows)
              ->join('m_pc as m', function($query) {
                    $query->on('m.st_code', 'winn.st_code')
                   ->on('m.pc_no', 'winn.pc_no');
                })
                ->join('candidate_nomination_detail as cond', function($query) {
                    //$query->on('cond.candidate_id', 'personal.candidate_id')
                    $query->on('cond.pc_no', 'winn.pc_no');
                })
              ->join('m_symbol as symbol', 'cond.symbol_id', '=', 'symbol.SYMBOL_NO')
              ->groupby('winn.pc_no' ,'m.PC_NAME')
                    //->skip(0)->take(10)
                ->get()->toarray();

  foreach($successfullcondidate as $key=>$listofsuccessfulldata){

  $arraydata[$listofsuccessfulldata->st_name]['state'] = $listofsuccessfulldata->st_name;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Pc_Name'] = $listofsuccessfulldata->PC_NAME;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['PC_TYPE'] = $listofsuccessfulldata->PC_TYPE;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Cand_Name'] = $listofsuccessfulldata->lead_cand_name;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_Abbre'] = $listofsuccessfulldata->lead_party_abbre;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_symbol'] = $listofsuccessfulldata->SYMBOL_DES;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Vote_Margin'] = $listofsuccessfulldata->trail_total_vote;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['margin'] = $listofsuccessfulldata->margin;
  if($listofsuccessfulldata->trail_total_vote != 0){
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] = round($listofsuccessfulldata->margin/$listofsuccessfulldata->trail_total_vote*100,2);
  }else {
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] ='0%';
  }
  }
  
//echo "<pre>";print_r($arraydata);die;
 return view('IndexCardReports/StatisticalReports/Vol2/acsuccessfullcondidate',  compact('arraydata','user_data'));
  }
  
public function pdfsuccessfullcondidate(Request $request){

  $user = Auth::user();
  $uid = $user->id;
$user_data = $user;
  $rows = array('m.PC_TYPE','m.PC_NAME','m.PC_NO','winn.st_name','winn.lead_cand_name','winn.lead_party_abbre','symbol.SYMBOL_DES','winn.margin','winn.trail_total_vote','winn.st_code');

  $successfullcondidate = DB::table('winning_leading_candidate as winn')
          ->select($rows)
              ->join('m_pc as m', function($query) {
                    $query->on('m.st_code', 'winn.st_code')
                   ->on('m.pc_no', 'winn.pc_no');
                })
                ->join('candidate_nomination_detail as cond', function($query) {
                    //$query->on('cond.candidate_id', 'personal.candidate_id')
                    $query->on('cond.pc_no', 'winn.pc_no');
                })
              ->join('m_symbol as symbol', 'cond.symbol_id', '=', 'symbol.SYMBOL_NO')
              ->groupby('winn.pc_no' ,'m.PC_NAME')
                    //->skip(0)->take(10)
                ->get()->toarray();

  foreach($successfullcondidate as $key=>$listofsuccessfulldata){

  $arraydata[$listofsuccessfulldata->st_name]['state'] = $listofsuccessfulldata->st_name;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Pc_Name'] = $listofsuccessfulldata->PC_NAME;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['PC_TYPE'] = $listofsuccessfulldata->PC_TYPE;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Cand_Name'] = $listofsuccessfulldata->lead_cand_name;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_Abbre'] = $listofsuccessfulldata->lead_party_abbre;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_symbol'] = $listofsuccessfulldata->SYMBOL_DES;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Vote_Margin'] = $listofsuccessfulldata->trail_total_vote;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['margin'] = $listofsuccessfulldata->margin;
  if($listofsuccessfulldata->trail_total_vote != 0){
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] = round($listofsuccessfulldata->margin/$listofsuccessfulldata->trail_total_vote*100,2);
  }else {
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] ='0%';
  }
  }
    $pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/successfull-candidate-pdf',  compact('arraydata','user_data'));
        return $pdf->download('list-of-successfull-candidate.pdf');
  }
  
  
  public function successfullcondidatexls(Request $request){
       $user = Auth::user();
        $uid = $user->id;
        $user_data = $user;
        
          $rows = array('m.PC_TYPE','m.PC_NAME','m.PC_NO','winn.st_name','winn.lead_cand_name','winn.lead_party_abbre','symbol.SYMBOL_DES','winn.margin','winn.trail_total_vote','winn.st_code');

  $successfullcondidate = DB::table('winning_leading_candidate as winn')
          ->select($rows)
              ->join('m_pc as m', function($query) {
                    $query->on('m.st_code', 'winn.st_code')
                   ->on('m.pc_no', 'winn.pc_no');
                })
                ->join('candidate_nomination_detail as cond', function($query) {
                    //$query->on('cond.candidate_id', 'personal.candidate_id')
                    $query->on('cond.pc_no', 'winn.pc_no');
                })
              ->join('m_symbol as symbol', 'cond.symbol_id', '=', 'symbol.SYMBOL_NO')
              ->groupby('winn.pc_no' ,'m.PC_NAME')
                    //->skip(0)->take(10)
                ->get()->toarray();

  foreach($successfullcondidate as $key=>$listofsuccessfulldata){

  $arraydata[$listofsuccessfulldata->st_name]['state'] = $listofsuccessfulldata->st_name;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Pc_Name'] = $listofsuccessfulldata->PC_NAME;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['PC_TYPE'] = $listofsuccessfulldata->PC_TYPE;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Cand_Name'] = $listofsuccessfulldata->lead_cand_name;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_Abbre'] = $listofsuccessfulldata->lead_party_abbre;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Party_symbol'] = $listofsuccessfulldata->SYMBOL_DES;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['Vote_Margin'] = $listofsuccessfulldata->trail_total_vote;
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['margin'] = $listofsuccessfulldata->margin;
  if($listofsuccessfulldata->trail_total_vote != 0){
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] = round($listofsuccessfulldata->margin/$listofsuccessfulldata->trail_total_vote*100,2);
  }else {
  $arraydata[$listofsuccessfulldata->st_name]['pc'][$listofsuccessfulldata->PC_NO]['percent'] ='0%';
  }
  }
    
    
    return Excel::create('successfull_candidate_list', function($excel) use ($arraydata) {
                    $excel->sheet('mySheet', function($sheet) use ($arraydata) {
                      $sheet->mergeCells('A1:H1');             
                        $sheet->cells('A1', function($cells) {
                            $cells->setValue('List of Successfull Condiate');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });

                        $sheet->cell('A2', function($cell) {
                            $cell->setValue('State');
                        });
                        $sheet->cell('B2', function($cell) {
                            $cell->setValue('PC Name');
                        });
                        $sheet->cell('C2', function($cell) {
                            $cell->setValue('CONSTITUENCY');
                        });

                        $sheet->cell('D2', function($cell) {
                            $cell->setValue('WINNER');
                        });
                        $sheet->cell('E2', function($cell) {
                            $cell->setValue('PARTY');
                        });
                        $sheet->cell('F2', function($cell) {
                            $cell->setValue('PARTY SYMBOL');
                        });

                        $sheet->cell('G2', function($cell) {
                            $cell->setValue('MARGIN');
                        });
                        $sheet->cell('H2', function($cell) {
                            $cell->setValue('%');
                        });
//                        
//                        $sheet->cell('B' . $last, function($cell) {
//                            $cell->setValue('Grand Total');
//                        });
    $i =  3;
    
                            foreach ($arraydata as  $value) {
                                
                                $sheet->cell('A' . $i, $value['state']);
                                
                                echo "<pre>";
                                  //print_r($value['pc']);
                                $k =  0;
                                echo sizeof($value['pc']);
                                
                                foreach ($value['pc'] as $catwise) {
                                    
                                $sheet->cell('B' . $i, $catwise['Pc_Name']);
                                $sheet->cell('C' . $i, $catwise['PC_TYPE']);
                                $sheet->cell('D' . $i, $catwise['Cand_Name']);
                                $sheet->cell('E' . $i, $catwise['Party_Abbre']);
                                $sheet->cell('F' . $i, $catwise['Party_symbol']);
                                $sheet->cell('G' . $i, $catwise['margin']);
                                $sheet->cell('H' . $i, $catwise['percent']);
                                
                              $k++;  
                            }
                            
                            }
                            die;

                    });
                })->export();
  
  
  
}

//PCWiseDistributionVotesPolled
    public function getPCWiseDistributionVotesPolled(Request $request)
    {      
    	 $user = Auth::user();
       $user_data = $user;   
		  //end session data
		    DB::enableQueryLog();
       
       $pcwisedata = DB::select("SELECT st_code, st_name,pc_no, PC_NAME,SUM(egeneral) AS egeneral,SUM(eservice) AS eservice, SUM(evm_vote) AS evm_vote,SUM(postal_vote) AS postal_vote,SUM(tended_votes) AS tended_votes, SUM(nota_vote) AS nota_vote,
SUM(postal_votes_rejected) AS postal_votes_rejected,SUM(test_votes_49_ma) AS test_votes_49_ma,SUM(votes_not_retreived_from_evm) AS votes_not_retreived_from_evm,SUM(lead_total_vote) AS lead_total_vote,SUM(voters) AS voters
FROM (
SELECT TEMP1.*,ecoi.postal_votes_rejected,ecoi.test_votes_49_ma,ecoi.votes_not_retreived_from_evm,SUM(ecoi.general_male_voters+ecoi.general_female_voters+ecoi.general_other_voters+ecoi.nri_male_voters+ecoi.nri_female_voters+ecoi.nri_other_voters+service_postal_votes_under_section_8+service_postal_votes_gov) AS voters
FROM
(
SELECT TEMP.*,cpm.evm_vote,cpm.postal_vote,cpm.tended_votes, nota.nota_vote,wlc.lead_total_vote
FROM (
SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.electors_total) AS egeneral,SUM(cda.electors_service) AS eservice
FROM electors_cdac cda,m_pc mpc ,m_state m
WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019
GROUP BY mpc.st_code ,mpc.pc_no
)TEMP,counting_pcmaster cpm,winning_leading_candidate wlc, (SELECT f_nota.st_code,f_nota.pc_no,SUM(f_nota.total_vote) AS 'nota_vote',f_mpc.pc_type FROM counting_pcmaster f_nota, m_pc f_mpc WHERE f_mpc.st_code = f_nota.st_code AND f_mpc.pc_no = f_nota.pc_no AND f_nota.candidate_id = 4319  GROUP BY f_mpc.st_code,f_mpc.pc_no) nota
WHERE TEMP.st_code=cpm.st_code AND TEMP.pc_no=cpm.pc_no AND TEMP.st_code=wlc.st_code AND TEMP.pc_no=wlc.pc_no AND TEMP.st_code=nota.st_code AND TEMP.pc_no = nota.pc_no
GROUP BY TEMP.st_code,TEMP.pc_no
)TEMP1
LEFT JOIN electors_cdac_other_information ecoi
ON TEMP1.st_code = ecoi.st_code AND TEMP1.pc_no = ecoi.pc_no
GROUP BY TEMP1.st_code,TEMP1.PC_no) xyz
GROUP BY st_code,PC_no WITH ROLLUP");
       

				if($request->path() == 'eci/PCWiseDistributionVotesPolled'){
					 return view('IndexCardReports.IndexCardEciReport.Vol2.pcwise-distribution-of-valid-votes',compact('pcwisedata','user_data'));
				}elseif($request->path() == 'eci/PCWiseDistributionVotesPolledPDF'){
					//return view('IndexCardReports.StatisticalReportsCurrent.Vol2.pcwise-distribution-of-valid-votes-pdf',compact('data','stname','year'));
					 $pdf=PDF::loadView('IndexCardReports.IndexCardEciReport.Vol2.pcwise-distribution-of-valid-votes-pdf',[					
					'pcwisedata'=>$pcwisedata,
					'user_data'=>$user_data]);
				return $pdf->download('pcwise-distribution-votes-polled.pdf');
				}
        elseif($request->path() == 'eci/PCWiseDistributionVotesPolledXls')
        {
            $pcwisedata = json_decode( json_encode($pcwisedata), true);            

            return Excel::create('PCWiseDistributionVotesPolled', function($excel) use ($pcwisedata) {
             $excel->sheet('mySheet', function($sheet) use ($pcwisedata)
             {
            $sheet->mergeCells('A1:Q1');
            $sheet->cells('A1:Q1', function($cells) {
                $cells->setFont(array(
                    'size'       => '15',
                    'bold'       => true
                ));
            });
           $sheet->cell('A1', function($cells) {
                $cells->setValue('14-PC WISE DISTRIBUTION OF VOTES POLLED');
            });


            $sheet->cell('A2', function($cells) {
                $cells->setValue('State/UT');
            });

           $sheet->cell('B2', function($cells) {
                $cells->setValue('Sl. No.');
            });
            
            $sheet->cell('C2', function($cells) {
                $cells->setValue('PC No.');
            });
             $sheet->cell('D2', function($cells) {
                $cells->setValue('PC Name');
            });

            $sheet->mergeCells('E2:F2');

            $sheet->cell('E2', function($cells) {
                $cells->setValue('Electors');
            });
            $sheet->mergeCells('G2:H2');

            $sheet->cell('G2', function($cells) {
                $cells->setValue('Valid Votes Polled');
            });
            $sheet->cell('I2', function($cells) {
                $cells->setValue('NOTA');
            });
            $sheet->mergeCells('J2:K2');
            $sheet->cell('J2', function($cells) {
                $cells->setValue('Rejected/Not Retrived Votes');
            });
             $sheet->cell('L2', function($cells) {
                $cells->setValue('Total Voters');
            });
            $sheet->cell('M2', function($cells) {
                $cells->setValue('Tendered Votes');
            });
            $sheet->cell('N2', function($cells) {
                $cells->setValue('Test Votes');
            });

            $sheet->cell('O2', function($cells) {
                $cells->setValue('Voter Turn Out(%)');
            });
            $sheet->cell('P2', function($cells) {
                $cells->setValue('% Votes to Winner out of total');
            });
            $sheet->cell('Q2', function($cells) {
                $cells->setValue('% Votes to NOTA out of total votes');
            });
            
            $sheet->cell('E3', function($cells) {
                $cells->setValue('General');
            });
            $sheet->cell('F3', function($cells) {
                $cells->setValue('Service');
            });
            $sheet->cell('G3', function($cells) {
                $cells->setValue('EVM');
            });

            $sheet->cell('H3', function($cells) {
                $cells->setValue('Postal');
            });
            $sheet->cell('J3', function($cells) {
                $cells->setValue('EVM');
            });
            $sheet->cell('K3', function($cells) {
                $cells->setValue('Postal');
            });
           
           

             if (!empty($pcwisedata)) {
                 $count=1; $i=4;
              foreach ($pcwisedata as $row) {  
                 if($row['pc_no']==NULL) {
                  $sheet->cell('A'.$i,''); 
                  $sheet->cell('B'.$i,'' ); 
                  $sheet->cell('C'.$i, 'Sub Total'); 

                }  
                            
                      $totalelectors = $totalvoters = $voterturnout  = $notapercent = $candvote = $votertowinnerout = $voters = 0;
                     $totalelectors = $row['egeneral']+$row['eservice'];
                      $voters=$row['voters'];
                    
                      if($totalelectors>0)
                      {
                           $voterturnout=round((($voters/$totalelectors)*100),2);
                      }
                      if($voters>0)
                      {
                          $totalv=(($row['nota_vote']/$voters)*100);
                          $notapercent=round($totalv,2);
                      }
                      if($voters>0)
                      {
                          $candvote=$row['lead_total_vote'];
                          $votertowinnerout=(($candvote/$voters)*100);
                      }
              
                      if($row['pc_no']!=NULL || $row['pc_no']!="")
                      {                  
                                   
                        $sheet->cell('A'.$i ,$row['st_name']); 
                        $sheet->cell('B'.$i ,$count); 
                        $sheet->cell('C'.$i, $row['pc_no']); 
                        $sheet->cell('D'.$i, $row['PC_NAME']);
                        $sheet->cell('E'.$i, ($row['egeneral']) ? $row['egeneral'] : '=(0)'); 
                        $sheet->cell('F'.$i, ($row['eservice']) ? $row['eservice'] : '=(0)'); 
                        $sheet->cell('G'.$i, ($row['evm_vote']) ? $row['evm_vote'] : '=(0)'); 
                        $sheet->cell('H'.$i, ($row['postal_vote']) ? $row['postal_vote'] : '=(0)'); 
                        $sheet->cell('I'.$i, ($row['nota_vote']) ? $row['nota_vote'] : '=(0)'); 
                        $sheet->cell('J'.$i, ($row['votes_not_retreived_from_evm']) ? $row['votes_not_retreived_from_evm'] : '=(0)'); 

                        $sheet->cell('K'.$i, ($row['postal_votes_rejected']) ? $row['postal_votes_rejected'] : '=(0)'); 
                        $sheet->cell('L'.$i, ($row['voters']) ? $row['voters'] : '=(0)');  
                        $sheet->cell('M'.$i, ($row['tended_votes']) ? $row['tended_votes'] : '=(0)'); 
                        $sheet->cell('N'.$i, ($row['test_votes_49_ma']) ? $row['test_votes_49_ma'] : '=(0)'); 
                        $sheet->cell('O'.$i, ($voterturnout) ? $voterturnout : '=(0)'); 
                       // $sheet->cell('O'.$i, ($row['voternri']) ? $row['voternri'] : '=(0)');  
                        $sheet->cell('P'.$i, ($votertowinnerout) ? round($votertowinnerout,2): '=(0)'); 

                         $sheet->cell('Q'.$i, ($notapercent) ? $notapercent : '=(0)'); 
                        
                          $i++;  
                      }   

                      

                      
                    }
                  }
                });
             })->download('xls');

        }

    }
}