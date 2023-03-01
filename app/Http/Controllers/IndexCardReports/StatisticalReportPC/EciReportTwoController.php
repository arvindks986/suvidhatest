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
use App\models\Admin\CandidateModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;
use Excel;
use App;
use App\Classes\xssClean;

ini_set("memory_limit","48000M");
        set_time_limit('6000');
        ini_set("pcre.backtrack_limit", "5000000000");

class EciReportTwoController extends Controller
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
        $this->xssClean = new xssClean;
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

        
        $totalElectors = DB::select('SELECT sum(`electors_total`) as total_electors FROM `electors_cdac`');


        $totalVotes = DB::select('SELECT sum(`total_vote`) as totalVotes FROM `counting_pcmaster`');

        $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports/StatisticalReports.Vol1.acperformanceofnationalparties-pdf', compact('data', 'totalElectors', 'totalVotes', 'user_data'));
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

       $pcwisedata = DB::select("SELECT st_code, st_name,pc_no, PC_NAME,SUM(egeneral) AS 'egeneral',SUM(eservice) AS 'eservice', SUM(evm_vote) AS 'evm_vote',
        SUM(postal_vote) AS 'postal_vote',SUM(postal_votes_rejected) AS 'postal_votes_rejected',SUM(tended_votes) AS 'tended_votes', SUM(nota_vote) AS 'nota_vote',
        SUM(rejected_votes_due_2_other_reason) AS 'rejected_votes_due_2_other_reason',SUM(test_votes_49_ma) AS 'test_votes_49_ma',
        SUM(votes_not_retreived_from_evm) AS 'votes_not_retreived_from_evm',SUM(lead_total_vote) AS 'lead_total_vote',SUM(voters) AS
         'voters'
        FROM (
        SELECT TEMP1.*,ecoi.test_votes_49_ma,ecoi.votes_not_retreived_from_evm,ecoi.rejected_votes_due_2_other_reason,
        SUM(ecoi.general_male_voters+ecoi.general_female_voters+ecoi.general_other_voters+ecoi.nri_male_voters+
        ecoi.nri_female_voters+ecoi.nri_other_voters+ecoi.service_postal_votes_under_section_8+ecoi.service_postal_votes_gov) AS 'voters'
        FROM
        (
        SELECT TEMP.*,SUM(cpm.evm_vote+cpm.migrate_votes) AS 'evm_vote',SUM(cpm.postal_vote) AS 'postal_vote',cpm.rejectedvote as postal_votes_rejected, cpm.tended_votes AS 'tended_votes' , nota.nota_vote,wlc.lead_total_vote
        FROM (
        SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.gen_electors_male+cda.gen_electors_female+cda.gen_electors_other
                +cda.nri_male_electors+cda.nri_female_electors+cda.nri_third_electors) AS egeneral,SUM(cda.service_male_electors+cda.service_female_electors+cda.service_third_electors) AS eservice
        FROM electors_cdac cda,m_pc mpc ,m_state m
        WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code
        GROUP BY mpc.st_code ,mpc.pc_no
        )TEMP,counting_pcmaster cpm,winning_leading_candidate wlc, (SELECT f_nota.st_code,f_nota.pc_no,SUM(f_nota.total_vote) AS 'nota_vote',f_mpc.pc_type
        FROM counting_pcmaster f_nota, m_pc f_mpc WHERE f_mpc.st_code = f_nota.st_code AND f_mpc.pc_no = f_nota.pc_no AND f_nota.party_id = 1180
        GROUP BY f_mpc.st_code,f_mpc.pc_no) nota
        WHERE TEMP.st_code=cpm.st_code AND TEMP.pc_no=cpm.pc_no AND TEMP.st_code=wlc.st_code AND TEMP.pc_no=wlc.pc_no AND TEMP.st_code=nota.st_code
        AND TEMP.pc_no = nota.pc_no and cpm.party_id != '1180'
        GROUP BY TEMP.st_code,TEMP.pc_no
        )TEMP1
        LEFT JOIN electors_cdac_other_information ecoi
        ON TEMP1.st_code = ecoi.st_code AND TEMP1.pc_no = ecoi.pc_no
        GROUP BY TEMP1.st_code,TEMP1.PC_no) xyz
        GROUP BY st_code,PC_no");



   //echo "<pre>"; print_r($pcwisedata); die;

       foreach ($pcwisedata as  $value) {


      $pcwisedatanew[$value->st_name][$value->pc_no] = array(

      'pc_no' =>  $value->pc_no,
      'PC_NAME' =>  $value->PC_NAME,
      'egeneral' =>  $value->egeneral,
      'eservice' =>  $value->eservice,
      'evm_vote' =>  $value->evm_vote,
      'postal_vote' =>  $value->postal_vote,
      'tended_votes' =>  $value->tended_votes,
      'nota_vote' =>  $value->nota_vote,
      'postal_votes_rejected' =>  $value->postal_votes_rejected,
      'test_votes_49_ma' =>  $value->test_votes_49_ma,
      'votes_not_retreived_from_evm' =>  $value->votes_not_retreived_from_evm,
      'rejected_votes_due_2_other_reason' =>  $value->rejected_votes_due_2_other_reason,
      'voters' =>  $value->voters,
      'lead_total_vote' =>  $value->lead_total_vote,



      );

    }

    $pcwisedata = $pcwisedatanew;

    //echo "<pre>"; print_r($pcwisedata); die;



     if($user->designation == 'ROPC'){
      $prefix   = 'ropc';
    }else if($user->designation == 'CEO'){
      $prefix   = 'pcceo';
    }else if($user->role_id == '27'){
      $prefix   = 'eci-index';
    }else if($user->role_id == '7'){
      $prefix   = 'eci';
    }

        if($request->path() == "$prefix/PCWiseDistributionVotesPolled"){
           return view('IndexCardReports.IndexCardEciReport.Vol2.pcwise-distribution-of-valid-votes',compact('pcwisedata','user_data'));
        }elseif($request->path() == "$prefix/PCWiseDistributionVotesPolledPDF"){
 
			
			$pdf = PDF::loadView('IndexCardReports.IndexCardEciReport.Vol2.pcwise-distribution-of-valid-votes-pdf',[
          'pcwisedata'=>$pcwisedata,
          'user_data'=>$user_data]);

             if(verifyreport(14)){
        
                  $file_name = 'PC Wise Distribution Of Votes Polled'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/14/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '14',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
        return $pdf->download('PC Wise Distribution Of Votes Polled.pdf');
        }elseif($request->path() == "$prefix/PCWiseDistributionVotesPolledXls")
        {
            $pcwisedata = json_decode( json_encode($pcwisedata), true);

            return Excel::create('PC Wise Distribution Of Votes Polled', function($excel) use ($pcwisedata) {
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
               $cells->setValue('% Votes to Winner out of total Votes Polled');
           });
           $sheet->cell('Q2', function($cells) {
               $cells->setValue('% Votes to NOTA out of total Votes Polled');
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

                 $grandegeneral = $grandeservice = $grandevm_vote = $grandpostal_vote = $grandnota_vote
                 = $grandvotes_not_retreived_from_evm  = $grandpostal_votes_rejected = $grandvoters = $grandtended_votes
                 = $grandtest_votes_49_ma = $grandtotal1 = $grandtotal2 = $grandtotal3 = $grandwinnervote =0;
              foreach($pcwisedata as $key => $value) {

                $totalegeneral = $totaleservice = $totalevm_vote = $totalpostal_vote = $totalnota_vote
                = $totalvotes_not_retreived_from_evm = $totalpostal_votes_rejected = $totalvoters =
               $totaltended_votes = $totaltest_votes_49_ma = $totalwinnervote = 0;

                foreach($value as $key1 => $value1) {




                        $sheet->cell('A'.$i ,$key);
                        $sheet->cell('B'.$i ,$count);
                        $sheet->cell('C'.$i, $value1['pc_no']);
                        $sheet->cell('D'.$i, $value1['PC_NAME']);
                        $sheet->cell('E'.$i, ($value1['egeneral']) ? $value1['egeneral'] : '=(0)');
                        $sheet->cell('F'.$i, ($value1['eservice']) ? $value1['eservice'] : '=(0)');
                        $sheet->cell('G'.$i, ($value1['evm_vote']) ? $value1['evm_vote'] : '=(0)');
                        $sheet->cell('H'.$i, ($value1['postal_vote']) ? $value1['postal_vote'] : '=(0)');
                        $sheet->cell('I'.$i, ($value1['nota_vote']) ? $value1['nota_vote'] : '=(0)');
                        $sheet->cell('J'.$i, ($value1['votes_not_retreived_from_evm']+$value1['rejected_votes_due_2_other_reason']) ? ($value1['votes_not_retreived_from_evm']+$value1['rejected_votes_due_2_other_reason']) : '=(0)');

                        $sheet->cell('K'.$i, ($value1['postal_votes_rejected']) ? $value1['postal_votes_rejected'] : '=(0)');
                        $sheet->cell('L'.$i, ($value1['voters']) ? $value1['voters'] : '=(0)');
                        $sheet->cell('M'.$i, ($value1['tended_votes']) ? $value1['tended_votes'] : '=(0)');
                        $sheet->cell('N'.$i, ($value1['test_votes_49_ma']) ? $value1['test_votes_49_ma'] : '=(0)');
                        $sheet->cell('O'.$i, (round($value1['voters']/($value1['egeneral']+$value1['eservice'])*100,2)) ? round($value1['voters']/($value1['egeneral']+$value1['eservice'])*100,2) : '=(0)');
                       // $sheet->cell('O'.$i, ($row['voternri']) ? $row['voternri'] : '=(0)');
                        $sheet->cell('P'.$i, (round($value1['lead_total_vote']/$value1['voters']*100,2)) ? round(round($value1['lead_total_vote']/$value1['voters']*100,2)): '=(0)');

                         $sheet->cell('Q'.$i, (round($value1['nota_vote']/$value1['voters']*100,2)) ? round($value1['nota_vote']/$value1['voters']*100,2) : '=(0)');



                          $totalegeneral += $value1['egeneral'];
                          $totaleservice += $value1['eservice'];
                          $totalevm_vote += $value1['evm_vote'];
                          $totalpostal_vote += $value1['postal_vote'];
                          $totalnota_vote += $value1['nota_vote'];
                          $totalvotes_not_retreived_from_evm += $value1['votes_not_retreived_from_evm']+$value1['rejected_votes_due_2_other_reason'];
                          $totalpostal_votes_rejected += $value1['postal_votes_rejected'];
                          $totalvoters += $value1['voters'];
                          $totaltended_votes += $value1['tended_votes'];
                          $totaltest_votes_49_ma += $value1['test_votes_49_ma'];
                          $totalwinnervote += $value1['lead_total_vote'];

                          $total1 = round($totalvoters/($totalegeneral+$totaleservice)*100,2);
                          $total2 = round($totalwinnervote/($totalvoters)*100,2);
                          $total3 = round($totalnota_vote/($totalvoters)*100,2);



                          $i++; $count++;
                      }

                      $sheet->cell('A'.$i, function($cell) use($key) {
                       $cell->setValue($key);
                       $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                       });
                       $sheet->cell('B'.$i, function($cell)  {
                        $cell->setValue('State Total');
                        $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('E'.$i, function($cell) use($totalegeneral) {
                         $cell->setValue($totalegeneral);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('F'.$i, function($cell) use($totaleservice) {
                          $cell->setValue($totaleservice);
                          $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('G'.$i, function($cell) use($totalevm_vote) {
                           $cell->setValue($totalevm_vote);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('H'.$i, function($cell) use($totalpostal_vote) {
                           $cell->setValue($totalpostal_vote);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('I'.$i, function($cell) use($totalnota_vote) {
                           $cell->setValue($totalnota_vote);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('J'.$i, function($cell) use($totalvotes_not_retreived_from_evm) {
                           $cell->setValue($totalvotes_not_retreived_from_evm);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('K'.$i, function($cell) use($totalpostal_votes_rejected) {
                           $cell->setValue($totalpostal_votes_rejected);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('L'.$i, function($cell) use($totalvoters) {
                           $cell->setValue($totalvoters);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('M'.$i, function($cell) use($totaltended_votes) {
                           $cell->setValue($totaltended_votes);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('N'.$i, function($cell) use($totaltest_votes_49_ma) {
                           $cell->setValue($totaltest_votes_49_ma);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });



                      $sheet->cell('O'.$i, function($cell) use($totalvoters,$totalegeneral,$totaleservice) {
                           $cell->setValue(round($totalvoters/($totalegeneral+$totaleservice)*100,2));
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('P'.$i, function($cell) use($totalwinnervote,$totalvoters) {
                           $cell->setValue(round($totalwinnervote/($totalvoters)*100,2));
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });


                      $sheet->cell('Q'.$i, function($cell) use($totalnota_vote,$totalvoters) {
                           $cell->setValue(round($totalnota_vote/($totalvoters)*100,2));
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $i++;


                      $grandegeneral += $totalegeneral;
                      $grandeservice += $totaleservice;
                      $grandevm_vote += $totalevm_vote;
                      $grandpostal_vote += $totalpostal_vote;
                      $grandnota_vote += $totalnota_vote;
                      $grandvotes_not_retreived_from_evm += $totalvotes_not_retreived_from_evm;
                      $grandpostal_votes_rejected += $totalpostal_votes_rejected;
                      $grandvoters += $totalvoters;
                      $grandtended_votes += $totaltended_votes;
                      $grandtest_votes_49_ma += $totaltest_votes_49_ma;

                      $grandwinnervote += $totalwinnervote;

                      $grandtotal1 = round($grandvoters/($grandegeneral+$grandeservice)*100,2);
                      $grandtotal2 = round($grandwinnervote/($grandvoters)*100,2);
                      $grandtotal3 = round($grandnota_vote/($grandvoters)*100,2);

                    }


                     $sheet->cell('B'.$i, function($cell)  {
                      $cell->setValue('All India Total');
                      $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('E'.$i, function($cell) use($grandegeneral) {
                       $cell->setValue($grandegeneral);
                       $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('F'.$i, function($cell) use($grandeservice) {
                        $cell->setValue($grandeservice);
                        $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('G'.$i, function($cell) use($grandevm_vote) {
                         $cell->setValue($grandevm_vote);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });



                    $sheet->cell('H'.$i, function($cell) use($grandpostal_vote) {
                         $cell->setValue($grandpostal_vote);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('I'.$i, function($cell) use($grandnota_vote) {
                         $cell->setValue($grandnota_vote);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('J'.$i, function($cell) use($grandvotes_not_retreived_from_evm) {
                         $cell->setValue($grandvotes_not_retreived_from_evm);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('K'.$i, function($cell) use($grandpostal_votes_rejected) {
                         $cell->setValue($grandpostal_votes_rejected);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });




                    $sheet->cell('L'.$i, function($cell) use($grandvoters) {
                         $cell->setValue($grandvoters);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('M'.$i, function($cell) use($grandtended_votes) {
                         $cell->setValue($grandtended_votes);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('N'.$i, function($cell) use($grandtest_votes_49_ma) {
                         $cell->setValue($grandtest_votes_49_ma);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    


                    $sheet->cell('O'.$i, function($cell) use($grandvoters,$grandegeneral,$grandeservice) {
                         $cell->setValue(round($grandvoters/($grandegeneral+$grandeservice)*100,2));
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('P'.$i, function($cell) use($grandwinnervote,$grandvoters) {
                         $cell->setValue(round($grandwinnervote/($grandvoters)*100,2));
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });


                    $sheet->cell('Q'.$i, function($cell) use($grandnota_vote,$grandvoters) {
                         $cell->setValue(round($grandnota_vote/($grandvoters)*100,2));
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });




                    }

                    $i = $i+3;

          

                  $sheet->mergeCells("A$i:B$i");
                  $sheet->cell('A'.$i, function($cells) {
                    $cells->setValue('Disclaimer');
                    $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                  });

                  $i = $i+1;

                  $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
                  $sheet->setSize('A'.$i, 25,30);



                  $sheet->mergeCells("A$i:J$i");
                  $sheet->cell('A'.$i, function($cells) {
                  $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                  $cells->setFont(array('name' => 'Times New Roman','size' => 10));
                  });

                });
             })->download('xls');

        }

    }
}