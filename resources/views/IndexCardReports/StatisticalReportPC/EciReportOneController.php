<?php namespace App\Http\Controllers\IndexCardReports\StatisticalReportPC;
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

class EciReportOneController extends Controller {


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

    public function getStatewiseSeatWon(Request $request){

    $user = Auth::user();
    $user_data = $user;
    // DB::enableQueryLog();
    $getuserrecord = DB::select("select temp2.*,IFNULL (temp1.win,0) AS win
from(SELECT *,(SELECT SUM(ecd.electors_total)AS totaleelctors FROM electors_cdac ecd
WHERE ecd.st_code=temp.st_code group by temp.st_code,temp.PARTY_ID )AS totaleelctors,(SELECT SUM(cp.total_vote)AS totalvalid_st_vote  FROM counting_pcmaster cp
WHERE cp.st_code=temp.`st_code` group by cp.st_code )AS totalvalid_st_vote
FROM(SELECT ms.st_code,ms.ST_NAME,mp.PARTYABBRE,mp.PARTYNAME,mp.PARTYTYPE,cp.party_id,SUM(total_vote)AS totalvalidvote FROM m_state ms,counting_pcmaster cp,m_party mp WHERE cp.st_code=ms.st_code AND cp.party_id=mp.ccode GROUP BY cp.st_code,cp.party_id) temp)temp2 left join(select st_code,lead_cand_party,lead_cand_hparty,lead_cand_partyid,sum(CASE status WHEN '1' THEN '1' else 0 END) as win FROM winning_leading_candidate WHERE  trail_cand_party!='null' and lead_cand_party!=''
GROUP BY lead_cand_party,st_code order by st_code asc)temp1  on temp2.st_code=temp1.st_code
AND temp2.party_id=temp1.lead_cand_partyid");

		if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}


    if($request->path() == "$prefix/StatewiseSeatWon"){
    return view('IndexCardReports.IndexCardEciReport.Vol2.statewise-seat-won', ['user_data'=>$user_data,'getuserrecord'=>$getuserrecord]);
    }elseif($request->path() == "$prefix/StatewiseSeatWonPDF"){
    //return view('IndexCardReports.StatisticalReportsCurrent.Vol2.statewise-seat-won-pdf',compact('data','totalelectors','stname','user_data','sched'));
    $pdf=PDF::loadView('IndexCardReports.IndexCardEciReport.Vol2.statewise-seat-won-pdf',[

    'getuserrecord'=>$getuserrecord,
    'user_data'=>$user_data]);
    return $pdf->download('statewise-seat-won.pdf');
    }
    elseif($request->path() == "$prefix/StatewiseSeatWonXls")
    {

       $data = json_decode( json_encode($getuserrecord), true);

      // echo "pre"; print_r($data); die;

       return Excel::create('StatewiseSeatWonXls', function($excel) use ($data) {
                         $excel->sheet('mySheet', function($sheet) use ($data)
                         {
                        $sheet->mergeCells('A1:H1');
                        $sheet->cells('A1:H1', function($cells) {
                            $cells->setFont(array(
                                'size'       => '15',
                                'bold'       => true
                            ));
                            $cells->setAlignment('center');
                        });
                       $sheet->cell('A1', function($cells) {
                            $cells->setValue('State wise seat won and valid votes polled by political party');
                        });

                         //$sheet->mergeCells('A2:A3');
                        $sheet->cell('A2', function($cells) {
                            $cells->setValue('State Name');
                        });

                       $sheet->cell('B2', function($cells) {
                            $cells->setValue('Party Type');
                        });

                        $sheet->cell('C2', function($cells) {
                            $cells->setValue('Party Name');
                        });

                        $sheet->cell('D2', function($cells) {
                            $cells->setValue('Total Valid Votes Polled in the State');
                        });
                        $sheet->cell('E2', function($cells) {
                            $cells->setValue('Total Electors in the State');
                        });

                        $sheet->cell('F2', function($cells) {
                            $cells->setValue('Seats Won');
                        });
                         $sheet->cell('G2', function($cells) {
                            $cells->setValue('Total Valid Votes Polled by Party');
                        });
                          $sheet->cell('H2', function($cells) {
                            $cells->setValue('% Valid Votes Polled by Party');
                        });


                         if (!empty($data)) {

                          //echo '<pre>';print_r($data);die;
                           $i= 3;

                            foreach ($data as $row) {
                              $validvotepolledbyparty=0;

                                if($row['totalvalid_st_vote']!=0)
                                {
                                   $validvotepolledbyparty= ROUND((($row['totalvalidvote']/$row['totalvalid_st_vote'])*100),2);
                                }
                                  $sheet->cell('A'.$i, $row['ST_NAME']);
                                  $sheet->cell('B'.$i, $row['PARTYTYPE']);
                                  $sheet->cell('C'.$i, $row['PARTYNAME']);
                                  $sheet->cell('D'.$i, $row['totalvalid_st_vote']);
                                  $sheet->cell('E'.$i, $row['totaleelctors']);
                                  $sheet->cell('F'.$i, ($row['win']? :'=(0)'));
								                  // $sheet->fromArray($data, null, 'F'.$i, true);
                          //         $sheet->cell('F'.$i, $row['win']);
                                  $sheet->cell('G'.$i, $row['totalvalidvote']);
                                  $sheet->cell('H'.$i, $validvotepolledbyparty);

                              $i++;
                         }
                        }
                         });
                })->download('xls');

        }else{
            echo "Result not found";
        }
  }


   public function getParticipationofWomenInNationalParties(Request $request)
    {
    // session data
    $user = Auth::user();
    $user_data = $user;
     //end session data

    DB::enableQueryLog();
    $data=DB::select("SELECT *,
          (select contested from (
          SELECT COUNT(cp.candidate_id) AS 'contested',cp.party_id
          FROM m_party m JOIN counting_pcmaster cp
          ON m.ccode= cp.party_id
          JOIN candidate_personal_detail cpd
          ON cpd.candidate_id = cp.candidate_id
          LEFT JOIN winning_leading_candidate wlc
          ON wlc.candidate_id = cp.candidate_id
          AND m.ccode= wlc.lead_cand_partyid
          WHERE partytype ='N'
          AND cand_gender = 'female'
          GROUP BY partyabbre,party_id)BB
           WHERE BB.PARTY_ID=TEMP.party_id)as contested,
          (select won
          from
          (SELECT COUNT(lead_total_vote) AS 'won',cp.party_id
          FROM m_party m JOIN counting_pcmaster cp
          ON m.ccode= cp.party_id
          JOIN candidate_personal_detail cpd
          ON cpd.candidate_id = cp.candidate_id
          LEFT JOIN winning_leading_candidate wlc
          ON wlc.candidate_id = cp.candidate_id
          AND m.ccode= wlc.lead_cand_partyid
          WHERE partytype ='N'
          AND cand_gender = 'female'
          GROUP BY partyabbre) CC
          WHERE CC.PARTY_ID=TEMP.party_id)as WON,
          (SELECT SUM(df) FROM (
          SELECT lead_total_vote,partyabbre,cpd.candidate_id,cp.candidate_name,cp.party_id,
          CASE WHEN SUM(cp1.total_vote)/6 > cp.total_vote THEN 1 ELSE 0 END AS 'DF' FROM m_party m
          JOIN counting_pcmaster cp ON m.ccode= cp.party_id
          JOIN counting_pcmaster cp1
          ON cp.st_code = cp1.st_code
          AND cp.pc_no = cp1.pc_no
          JOIN candidate_personal_detail cpd
          ON cpd.candidate_id = cp.candidate_id
          LEFT JOIN winning_leading_candidate wlc
          ON wlc.candidate_id = cp.candidate_id
          AND m.ccode= wlc.lead_cand_partyid
          WHERE partytype ='N'
          AND cand_gender = 'female'
          AND lead_total_vote IS NULL
          GROUP BY cp.candidate_id,cp1.st_code, cp1.pc_no
          ) DD WHERE DD.party_id=TEMP.party_id) as DF,
          (SELECT Total_electros_female
          from (
          SELECT partyabbre, party_id,PARTYNAME,SUM(electors_female) AS Total_electros_female
          FROM m_party m
          JOIN counting_pcmaster cp
          ON m.ccode= cp.party_id
          JOIN candidate_personal_detail cpd
          ON cpd.candidate_id = cp.candidate_id
          JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
          WHERE partytype ='N'
          AND cand_gender = 'female'
          GROUP BY partyabbre )EEE WHERE EEE.party_id=TEMP.party_id) as Total_electros_female,
          ( SELECT electrols_Total
            FROM (
          SELECT partyabbre, party_id,PARTYNAME,SUM(electors_total) AS electrols_Total
          FROM m_party m
          JOIN counting_pcmaster cp
          ON m.ccode= cp.party_id
          JOIN candidate_personal_detail cpd
          ON cpd.candidate_id = cp.candidate_id
          JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
          WHERE partytype ='N'
          AND cand_gender = 'female'
          GROUP BY partyabbre ) FFF WHERE FFF.party_id=TEMP.party_id) AS electrols_Total,

          (SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
          WHERE party_id=TEMP.party_id group by party_id)AS totalvalid_valid_vote,

          (SELECT SUM(electors_total)AS totaleelctors FROM electors_cdac
          WHERE party_id=TEMP.party_id group by PARTY_ID )AS sum_of_total_eelctors,
          (SELECT SUM(total_vote) FROM counting_pcmaster ) AS OVER_ALL_TOTAL_VOTE
          FROM
          (
          SELECT partyabbre, party_id,PARTYNAME,SUM(total_vote) AS votes_secured_by_Women
          FROM m_party m
          JOIN counting_pcmaster cp
          ON m.ccode= cp.party_id
          JOIN candidate_personal_detail cpd
          ON cpd.candidate_id = cp.candidate_id
          WHERE partytype ='N'
          AND cand_gender = 'female'
          GROUP BY partyabbre
          )TEMP");
		     $queue = DB::getQueryLog();
			//echo'<pre>'; print_r($queue);die;
			
			if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}
			
			

            if($request->path() == "$prefix/ParticipationofWomenInNationalParties"){
                return view('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-national-parties',compact('data','user_data'));
            }elseif($request->path() == "$prefix/ParticipationofWomenInNationalPartiesPDF"){
                $pdf=PDF::loadView('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-national-parties-pdf',[
                'data'=>$data,
                'user_data'=>$user_data
          ]);
           return $pdf->download('participation-of-women-in-national-parties.pdf');
           }elseif ($request->path() == "$prefix/ParticipationofWomenInNationalPartiesXls") {
               $data = json_decode( json_encode($data), true);
                return Excel::create('ParticipationofWomenInNationalPartiesXls', function($excel) use ($data) {
                         $excel->sheet('mySheet', function($sheet) use ($data)
                         {
                        $sheet->mergeCells('A1:H1');
                        $sheet->cells('A1:H1', function($cells) {
                            $cells->setFont(array(
                                'size'       => '15',
                                'bold'       => true
                            ));
                            $cells->setAlignment('center');
                        });
                       $sheet->cell('A1', function($cells) {
                            $cells->setValue('26-Participation of Women In National Parties');
                        });

                         $sheet->mergeCells('A2:A3');
                        $sheet->cell('A2', function($cells) {
                            $cells->setValue('Party Name');
                        });

                       $sheet->cell('B2', function($cells) {
                            $cells->setValue('Candidates');
                        });

                        $sheet->cell('C2', function($cells) {
                            $cells->setValue('Percentage');
                        });

                        $sheet->mergeCells('C2:D2');

                         $sheet->mergeCells('E2:E3');

                        $sheet->cell('E2', function($cells) {
                            $cells->setValue('Votes Secured By Women Candidates');
                        });
                        $sheet->mergeCells('F2:H2');

                        $sheet->cell('F2', function($cells) {
                            $cells->setValue('% of votes secured');
                        });


                        $sheet->cell('B3', function($cells) {
                            $cells->setValue('Contested');
                        });
                        $sheet->cell('C3', function($cells) {
                            $cells->setValue('Won');
                        });
                         $sheet->cell('D3', function($cells) {
                            $cells->setValue('DF');
                        });
                        $sheet->cell('F3', function($cells) {
                            $cells->setValue('Over total electors');
                        });
                        $sheet->cell('G3', function($cells) {
                            $cells->setValue('Over total valid votes');
                        });

                        $sheet->cell('H3', function($cells) {
                            $cells->setValue('Over Votes secured by the party');
                        });

                         if (!empty($data)) {
                             $i= 4;
                                $totalcontested = $twon = $won= $fd =  $secure = $electorspercent = $overtotalvaliedpercent = $ovsbp= $tfd = $totalVoteSecured = $totalElectors  = $tvv = 0;
                             //echo "<pre>";print_r($data);die;
                            foreach ($data as $rows) {
                                $totalcontested+=$rows['contested'];
                                $twon+=$rows['WON'];
                                $tfd+=$rows['DF'];
                                $twonper=round((($twon/$totalcontested)*100),2);
                                $tdfper=round((($tfd/$totalcontested)*100),2);
                                $totalVoteSecured+=$rows['votes_secured_by_Women'];
                                $totalElectors+=$rows['electrols_Total'];
                                $ttotalElectors=($totalVoteSecured/$totalElectors)*100;
                                $totvv=($totalVoteSecured/$rows['OVER_ALL_TOTAL_VOTE'])*100;
                                $tvv+=$rows['totalvalid_valid_vote'];
                                $totvsp=($totalVoteSecured/$tvv)*100;

                                //
                                $peroverelectors = ($rows['votes_secured_by_Women']/$rows['electrols_Total'])*100;

                                $overTotalValidVotes = ($rows['votes_secured_by_Women']/$rows['OVER_ALL_TOTAL_VOTE'])*100;

                                $ovsbp = ($rows['votes_secured_by_Women']/$rows['totalvalid_valid_vote'])*100;

                                    $sheet->cell('A'.$i, $rows['partyabbre']);
                                    $sheet->cell('B'.$i, ($rows['contested']) ? $rows['contested'] : '=(0)');
                                    $sheet->cell('C'.$i, round((($rows['WON']/$rows['contested'])*100),2));
                                    $sheet->cell('D'.$i, round((($rows['DF']/$rows['contested'])*100),2));
                                    $sheet->cell('E'.$i, ($rows['votes_secured_by_Women']) ? $rows['votes_secured_by_Women'] : '=(0)');
                                    $sheet->cell('F'.$i, round($peroverelectors,2));

                                    $sheet->cell('G'.$i, round($overTotalValidVotes,2));
                                    $sheet->cell('H'.$i,round($ovsbp,2));

                                      $i++;
                                  }  // $i +=1;

                                    $sheet->cell('A'.$i, 'Total');
                                    $sheet->cell('B'.$i, ($totalcontested > 0) ? $totalcontested:'=(0)' );
                                    $sheet->cell('C'.$i, ($twonper > 0) ? $twonper:'=(0)' );
                                    $sheet->cell('D'.$i, $tdfper);
                                    $sheet->cell('E'.$i, ($totalVoteSecured > 0) ? $totalVoteSecured:'=(0)' );
                                    $sheet->cell('F'.$i, round($ttotalElectors,2));
                                    $sheet->cell('G'.$i, round($totvv,2));
                                    $sheet->cell('H'.$i, round($totvsp,2));
                                 }
                    });
                })->download('xls');

        }else{
            echo "Result not found";
        }


    }
	  //women as independent candidates

     public function getParticipationofWomenAsIndependentCandidates(Request $request)
    {

    $user = Auth::user();
    $user_data = $user;

    DB::enableQueryLog();
    $data=DB::select("SELECT *,
    (SELECT contested FROM (
    SELECT COUNT(cp.candidate_id) AS 'contested',cp.party_id
    FROM m_party m JOIN counting_pcmaster cp
    ON m.ccode= cp.party_id
    JOIN candidate_personal_detail cpd
    ON cpd.candidate_id = cp.candidate_id
    LEFT JOIN winning_leading_candidate wlc
    ON wlc.candidate_id = cp.candidate_id
    AND m.ccode= wlc.lead_cand_partyid
    WHERE partytype ='Z'
    AND cand_gender = 'female'
    GROUP BY partyabbre,party_id)BB
     WHERE BB.PARTY_ID=TEMP.party_id)AS contested,
    (SELECT won
    FROM
    (SELECT COUNT(lead_total_vote) AS 'won',cp.party_id
    FROM m_party m JOIN counting_pcmaster cp
    ON m.ccode= cp.party_id
    JOIN candidate_personal_detail cpd
    ON cpd.candidate_id = cp.candidate_id
    LEFT JOIN winning_leading_candidate wlc
    ON wlc.candidate_id = cp.candidate_id
    AND m.ccode= wlc.lead_cand_partyid
    WHERE partytype ='Z'
    AND cand_gender = 'female'
    GROUP BY partyabbre) CC
    WHERE CC.PARTY_ID=TEMP.party_id)AS WON,
    (SELECT SUM(df) FROM (
    SELECT lead_total_vote,partyabbre,cpd.candidate_id,cp.candidate_name,cp.party_id,
    CASE WHEN SUM(cp1.total_vote)/6 > cp.total_vote THEN 1 ELSE 0 END AS 'DF' FROM m_party m
    JOIN counting_pcmaster cp ON m.ccode= cp.party_id
    JOIN counting_pcmaster cp1
    ON cp.st_code = cp1.st_code
    AND cp.pc_no = cp1.pc_no
    JOIN candidate_personal_detail cpd
    ON cpd.candidate_id = cp.candidate_id
    LEFT JOIN winning_leading_candidate wlc
    ON wlc.candidate_id = cp.candidate_id
    AND m.ccode= wlc.lead_cand_partyid
    WHERE partytype ='Z'
    AND cand_gender = 'female'
    AND lead_total_vote IS NULL
    GROUP BY cp.candidate_id,cp1.st_code, cp1.pc_no
    ) DD WHERE DD.party_id=TEMP.party_id) AS DF,
    (SELECT Total_electros_female
    FROM (
    SELECT partyabbre, party_id,PARTYNAME,SUM(electors_female) AS Total_electros_female
    FROM m_party m
    JOIN counting_pcmaster cp
    ON m.ccode= cp.party_id
    JOIN candidate_personal_detail cpd
    ON cpd.candidate_id = cp.candidate_id
    JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
    WHERE partytype ='Z' AND cdac.year = 2019
    AND cand_gender = 'female'
    GROUP BY partyabbre )EEE WHERE EEE.party_id=TEMP.party_id) AS Total_electros_female,
    ( SELECT electrols_Total
    FROM (
    SELECT partyabbre, party_id,PARTYNAME,SUM(electors_total) AS electrols_Total
    FROM m_party m
    JOIN counting_pcmaster cp
    ON m.ccode= cp.party_id
    JOIN candidate_personal_detail cpd
    ON cpd.candidate_id = cp.candidate_id
    JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
    WHERE partytype ='Z' AND cdac.year = 2019
    AND cand_gender = 'female'
    GROUP BY partyabbre ) FFF WHERE FFF.party_id=TEMP.party_id) AS electrols_Total,

    (SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
    WHERE party_id=TEMP.party_id GROUP BY party_id)AS totalvalid_valid_vote,

    (SELECT SUM(electors_total)AS totaleelctors FROM electors_cdac
    WHERE party_id=TEMP.party_id AND `year` = 2019 GROUP BY PARTY_ID )AS sum_of_total_eelctors,
    (SELECT SUM(total_vote) FROM counting_pcmaster ) AS OVER_ALL_TOTAL_VOTE
    FROM
    (
    SELECT partyabbre, party_id,PARTYNAME,SUM(total_vote) AS votes_secured_by_Women
    FROM m_party m
    JOIN counting_pcmaster cp
    ON m.ccode= cp.party_id
    JOIN candidate_personal_detail cpd
    ON cpd.candidate_id = cp.candidate_id
    JOIN m_election_details med 
    ON med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO 
    WHERE partytype ='Z' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1'
    
    AND cand_gender = 'female'
    GROUP BY partyabbre
    )TEMP");

   //echo"<pre>";print_r($data);die;
   
   
   if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}
   

            if($request->path() == "$prefix/ParticipationofWomenAsIndependentCandidates"){
                return view('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-as-independent-candidate',compact('data','user_data'));
            }elseif($request->path() == "$prefix/ParticipationofWomenAsIndependentCandidatesPDF"){
                $pdf=PDF::loadView('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-as-independent-candidate-pdf',[
                'data'=>$data,
                'user_data'=>$user_data
          ]);
            return $pdf->download('participation-of-women-as-independent-candidate.pdf');
	 }
           elseif($request->path() == "$prefix/ParticipationofWomenAsIndependentCandidatesXls")
          {
           $data = json_decode( json_encode($data), true);
           //echo"<pre>";print_r($data1);die;
           return Excel::create('participationOfwomenAsIndependentCandidateXls', function($excel) use ($data) {
                         $excel->sheet('mySheet', function($sheet) use ($data)
                         {
                        $sheet->mergeCells('A1:I1');
                        $sheet->cells('A1:I1', function($cells) {
                            $cells->setFont(array(
                                'size'       => '15',
                                'bold'       => true
                            ));
                            $cells->setAlignment('center');
                        });
                       $sheet->cell('A1', function($cells) {
                            $cells->setValue('29-Participation of Women as independent Candidate');
                        });


                        $sheet->mergeCells('B1:D2');

                       $sheet->cell('B2', function($cells) {
                            $cells->setValue('Candidates');
                        });

                        $sheet->mergeCells('E2:F2');

                        $sheet->cell('E2', function($cells) {
                            $cells->setValue('Percentage');
                        });

                         $sheet->mergeCells('H2:I2');
                        $sheet->cell('H2', function($cells) {
                            $cells->setValue('% of Votes Secured');
                        });
                        $sheet->cell('A3', function($cells) {
                            $cells->setValue('Party Name');
                        });

                        $sheet->cell('B3', function($cells) {
                            $cells->setValue('Contested');
                        });
                         $sheet->cell('C3', function($cells) {
                            $cells->setValue('Won');
                        });
                        $sheet->cell('D3', function($cells) {
                            $cells->setValue('DF');
                        });
                        $sheet->cell('E3', function($cells) {
                            $cells->setValue('Won');
                        });

                        $sheet->cell('F3', function($cells) {
                            $cells->setValue('DF');
                        });
                        $sheet->cell('G3', function($cells) {
                            $cells->setValue('Votes Secured By Women Candidates');
                        });
                        $sheet->cell('H3', function($cells) {
                            $cells->setValue('Over Total Electors In Country');
                        });
                         $sheet->cell('I3', function($cells) {
                            $cells->setValue('Over Total Valid Votes In Country');
                        });
                         if (!empty($data)) {
                           $i= 4;

                            foreach ($data as $row) {
                              $wonper=0;

                                if($row['contested']!=0)
                                {
                                   $wonper= round((($row['WON']/$row['contested'])*100),2);
                                }
                                  $sheet->cell('A'.$i, $row['partyabbre']);
                                  $sheet->cell('B'.$i, $row['contested']);
                                  $sheet->cell('C'.$i, $row['WON']);
                                  $sheet->cell('D'.$i, $row['DF']);
                                  $sheet->cell('E'.$i, round((($row['WON']/$row['contested'])*100),2));
                                  $sheet->cell('F'.$i, round((($row['DF']/$row['contested'])*100),2));
                                  $sheet->cell('G'.$i, $row['votes_secured_by_Women']);
                                  $sheet->cell('H'.$i, round((($row['votes_secured_by_Women']/$row['sum_of_total_eelctors'])*100),2));
                                  $sheet->cell('I'.$i, round((($row['votes_secured_by_Women']/$row['OVER_ALL_TOTAL_VOTE'])*100),2));
                                  $i++;

                               $totalc = $totalallwon = $totalvs = $totaloe = $totalvv = $totalwonpercent = $ttwonper = $tdf= $tdfpercent = 0;

                                $totalc += ($row['contested'])?$row['contested']:0;
                                $totalallwon += ($row['WON'])?$row['WON']:0;
                                $totalvs += ($row['votes_secured_by_Women'])?$row['votes_secured_by_Women']:0;
                                $totaloe += ($row['sum_of_total_eelctors'])?$row['sum_of_total_eelctors']:0;
                                $totalvv += ($row['votes_secured_by_Women'])?$row['votes_secured_by_Women']:0;
                                $totalwonpercent =round((($totalallwon/$totalc)*100),2);
                                $tdf +=$row['DF'];
                               $tdfpercent= round((($tdf/$totalc)*100),2);
                       }

                       $sheet->cell('A'.$i, 'Total');
                                  $sheet->cell('B'.$i,$totalc);
                                  $sheet->cell('C'.$i, $totalallwon);
                                  $sheet->cell('D'.$i, $tdf);
                                  $sheet->cell('E'.$i, $totalwonpercent);
                                  $sheet->cell('F'.$i, $tdfpercent);
                                  $sheet->cell('G'.$i, $totalvs);
                                  $sheet->cell('H'.$i, round((($row['votes_secured_by_Women']/$row['sum_of_total_eelctors'])*100),2));
                                  $sheet->cell('I'.$i, round((($row['votes_secured_by_Women']/$row['OVER_ALL_TOTAL_VOTE'])*100),2));

                      }
                   });
                })->download('xls');

          }
              else{
              echo "Result not found";
          }


    }
	
	//women as registered parties
	 public function getParticipationofWomenInRegisteredParties(Request $request)
    {
    // session data
    $user = Auth::user();
    $user_data = $user;
     //end session data
    
    DB::enableQueryLog();
    $data=DB::select("SELECT *,
        (SELECT contested FROM (
        SELECT COUNT(cp.candidate_id) AS 'contested',cp.party_id
        FROM m_party m JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        LEFT JOIN winning_leading_candidate wlc
        ON wlc.candidate_id = cp.candidate_id
        AND m.ccode= wlc.lead_cand_partyid
        WHERE partytype ='U'
        AND cand_gender = 'female'
        GROUP BY partyabbre,party_id)BB
         WHERE BB.PARTY_ID=TEMP.party_id)AS contested,
        (SELECT won
        FROM
        (SELECT COUNT(lead_total_vote) AS 'won',cp.party_id
        FROM m_party m JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        LEFT JOIN winning_leading_candidate wlc
        ON wlc.candidate_id = cp.candidate_id
        AND m.ccode= wlc.lead_cand_partyid
        WHERE partytype ='U'
        AND cand_gender = 'female'
        GROUP BY partyabbre) CC
        WHERE CC.PARTY_ID=TEMP.party_id)AS WON,
        (SELECT SUM(df) FROM (
        SELECT lead_total_vote,partyabbre,cpd.candidate_id,cp.candidate_name,cp.party_id,
        CASE WHEN SUM(cp1.total_vote)/6 > cp.total_vote THEN 1 ELSE 0 END AS 'DF' FROM m_party m
        JOIN counting_pcmaster cp ON m.ccode= cp.party_id
        JOIN counting_pcmaster cp1
        ON cp.st_code = cp1.st_code
        AND cp.pc_no = cp1.pc_no
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        LEFT JOIN winning_leading_candidate wlc
        ON wlc.candidate_id = cp.candidate_id
        AND m.ccode= wlc.lead_cand_partyid
        WHERE partytype ='U'
        AND cand_gender = 'female'
        AND lead_total_vote IS NULL
        GROUP BY cp.candidate_id,cp1.st_code, cp1.pc_no
        ) DD WHERE DD.party_id=TEMP.party_id) AS DF,
        (SELECT Total_electros_female
        FROM (
        SELECT partyabbre, party_id,PARTYNAME,SUM(electors_female) AS Total_electros_female
        FROM m_party m
        JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
        WHERE partytype ='U' AND cdac.year = 2019
        AND cand_gender = 'female'
        GROUP BY partyabbre )EEE WHERE EEE.party_id=TEMP.party_id) AS Total_electros_female,
        ( SELECT electrols_Total
          FROM (
        SELECT partyabbre, party_id,PARTYNAME,SUM(electors_total) AS electrols_Total
        FROM m_party m
        JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
        WHERE partytype ='U' AND cdac.year = 2019
        AND cand_gender = 'female'
        GROUP BY partyabbre ) FFF WHERE FFF.party_id=TEMP.party_id) AS electrols_Total,

        (SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
        WHERE party_id=TEMP.party_id GROUP BY party_id)AS totalvalid_valid_vote,

        (SELECT SUM(electors_total)AS totaleelctors FROM electors_cdac
        WHERE party_id=TEMP.party_id AND `year` = 2019 GROUP BY PARTY_ID )AS sum_of_total_eelctors,
        (SELECT SUM(total_vote) FROM counting_pcmaster ) AS OVER_ALL_TOTAL_VOTE
        FROM
        (
        SELECT partyabbre, party_id,PARTYNAME,SUM(total_vote) AS votes_secured_by_Women
        FROM m_party m
        JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        WHERE partytype ='U'
        AND cand_gender = 'female'
        GROUP BY partyabbre
        )TEMP");

    //echo "<pre>"; print_r($data); die;
		
		
		if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}
		
		
		
		
            

            if($request->path() == "$prefix/ParticipationofWomenInRegisteredParties"){
                return view('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-registered-parties',compact('data','user_data'));
            }elseif($request->path() == "$prefix/ParticipationofWomenInRegisteredPartiesPDF"){
                $pdf=PDF::loadView('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-registered-parties-pdf',[            
                'data'=>$data,
                'user_data'=>$user_data
          ]);
           return $pdf->download('Participation of Women in Registered (Unrecognised) Parties.pdf');
           }elseif($request->path() == "$prefix/ParticipationofWomenInRegisteredPartiesXls"){
              $data = json_decode( json_encode($data), true);  
             //echo'<pre>'; print_r($data);die;               
        
                     return Excel::create('Participation of Women in Registered (Unrecognised) Parties', function($excel) use ($data) {
                         $excel->sheet('mySheet', function($sheet) use ($data)
                         {
                        $sheet->mergeCells('A1:I1');
                        $sheet->cells('A1:I1', function($cells) {
                            $cells->setFont(array(
                                'size'       => '15',
                                'bold'       => true
                            ));
                            $cells->setAlignment('center');
                        });
                       $sheet->cell('A1', function($cells) {
                            $cells->setValue('28 - PARTICIPATION OF WOMEN IN REGISTERED (UNRECOGNISED) PARTIES');
                        });


                        $sheet->mergeCells('B2:D2');

                       $sheet->cell('B2', function($cells) {
                            $cells->setValue('CANDIDATES');
                        });

                        $sheet->mergeCells('E2:F2');

                        $sheet->cell('E2', function($cells) {
                            $cells->setValue('PERCENTAGE');
                        });

                         $sheet->mergeCells('H2:J2');
                        $sheet->cell('H2', function($cells) {
                            $cells->setValue('% OF VOTES SECURED');
                        });
                        $sheet->cell('A3', function($cells) {
                            $cells->setValue('PARTY NAME');
                        });

                        $sheet->cell('B3', function($cells) {
                            $cells->setValue('CONTESTED');
                        });
                         $sheet->cell('C3', function($cells) {
                            $cells->setValue('WON');
                        });
                        $sheet->cell('D3', function($cells) {
                            $cells->setValue('DF');
                        });
                        $sheet->cell('E3', function($cells) {
                            $cells->setValue('WON');
                        });

                        $sheet->cell('F3', function($cells) {
                            $cells->setValue('DF');
                        });
                        $sheet->cell('G3', function($cells) {
                            $cells->setValue('VOTES SECURED BY PARTY');
                        });
                        $sheet->cell('H3', function($cells) {
                            $cells->setValue('OVER TOTAL ELECTORS IN STATE');
                        });
                         $sheet->cell('I3', function($cells) {
                            $cells->setValue('OVER TOTAL VALID VOTES IN STATE');
                        });
						 $sheet->cell('J3', function($cells) {
                            $cells->setValue('OVER VOTES SECURED BY THE PARTY');
                        });
                        
                         if (!empty($data)){
                             $i= 4;
                                $totalcontested = $twon = $won= $fd =  $secure = $electorspercent = $overtotalvaliedpercent = $ovsbp= $tfd = $totalVoteSecured = $totalElectors  = $tvv = 0;
                             //echo "<pre>";print_r($data);die;
                            foreach ($data as $row) {   
                                $totalcontested+=$row['contested'];
                                $twon+=$row['WON'];
                                $tfd+=$row['DF'];
                                $twonper=round(($twon/$totalcontested),2);
                                $tdfper=round(($tfd/$totalcontested),2);
                                $totalVoteSecured+=$row['votes_secured_by_Women'];
                                $totalElectors+=$row['electrols_Total'];
                                $ttotalElectors=($totalVoteSecured/$totalElectors)*100;
                                $totvv=($totalVoteSecured/$row['OVER_ALL_TOTAL_VOTE'])*100;
                                $tvv+=$row['totalvalid_valid_vote'];
                                $totvsp=($totalVoteSecured/$tvv)*100;

                                //
                                $peroverelectors = ($row['votes_secured_by_Women']/$row['electrols_Total'])*100;

                                $overTotalValidVotes = ($row['votes_secured_by_Women']/$row['OVER_ALL_TOTAL_VOTE'])*100;

                                $ovsbp = ($row['votes_secured_by_Women']/$row['totalvalid_valid_vote'])*100;
                                
                                    $sheet->cell('A'.$i, $row['partyabbre']); 
                                    $sheet->cell('B'.$i, ($row['contested']) ? $row['contested'] : '=(0)'); 
                                    $sheet->cell('C'.$i, ($row['WON']) ? $row['contested'] : '=(0)');
                                    $sheet->cell('D'.$i, ($row['DF']) ? $row['contested'] : '=(0)'); 
                                    $sheet->cell('E'.$i, (round((($row['WON']/$row['contested'])*100),2) >0) ? round((($row['WON']/$row['contested'])*100),2): '=(0)' ); 
                                    $sheet->cell('F'.$i, (round((($row['DF']/$row['contested'])*100),2) >0) ? round((($row['DF']/$row['contested'])*100),2): '=(0)' ); 
                                    $sheet->cell('G'.$i, ($row['votes_secured_by_Women']) ? $row['votes_secured_by_Women'] : '=(0)'); 
                                    $sheet->cell('H'.$i, (round($peroverelectors,2) > 0) ? round($peroverelectors,2): '=(0)');

                                    $sheet->cell('I'.$i, (round($overTotalValidVotes,2) > 0) ? round($overTotalValidVotes,2): '=(0)'); 
                                    $sheet->cell('J'.$i, (round($ovsbp,2) > 0) ? round($ovsbp,2): '=(0)'); 
                                    
                                      $i++;  
                                  }   $i +=1;

                                    $sheet->cell('A'.$i, 'TOTAL'); 
                                    $sheet->cell('B'.$i, ($totalcontested > 0) ? $totalcontested:'=(0)' ); 
									$sheet->cell('C'.$i, ($twon > 0) ? $twon:'=(0)' ); 
									$sheet->cell('D'.$i, ($tfd > 0) ? $tfd:'=(0)' );
									  
                                    $sheet->cell('E'.$i, ($twonper > 0) ? $twonper:'=(0)' ); 
                                    $sheet->cell('F'.$i, $tdfper); 
                                    $sheet->cell('G'.$i, ($totalVoteSecured > 0) ? $totalVoteSecured:'=(0)' ); 
                                    $sheet->cell('H'.$i, round($ttotalElectors,2)); 
                                    $sheet->cell('I'.$i, round($totvv,2)); 
                                    //$sheet->cell('J'.$i, round($totvsp,2));
                                 }
                    });
                })->download('xls');

        }else{
            echo "Result not found";
        }        
    }
	
	//voter information
	//Voters Information
     public function getVoterInformation(Request $request)
        {
          $user = Auth::user();
          $user_data = $user;


          $voterarray=DB::select("SELECT st_code,st_name,pc_type,SUM(seats)AS seats, SUM(emale) AS emale,SUM(efemale) AS efemale,SUM(eother) AS eother,SUM(etotal) AS etotal, SUM(nrielectors) AS nrielectors,SUM(serviceelectors) AS serviceelectors,SUM(evm_vote) AS evm_vote,SUM(total_vote) AS total_vote,
        SUM(postaltotalvote)AS postaltotalvote,SUM(tended_votes)AS tended_votes, SUM(nota_vote) AS nota_vote,SUM(general_male_voters) AS general_male_voters,
        SUM(general_female_voters) AS general_female_voters,SUM(general_other_voters) AS general_other_voters,SUM(voternri) AS voternri,SUM(postal_votes_rejected) AS postal_votes_rejected, SUM(votes_not_retreived_from_evm) AS votes_not_retreived_from_evm FROM (
        SELECT TEMP1.*,ecoi.general_male_voters,ecoi.general_female_voters,general_other_voters,ecoi.voternri,ecoi.votes_not_retreived_from_evm
        FROM
        (
        SELECT TEMP.*,cpm.evm_vote,cpm.total_vote,cpm.postaltotalvote,cpm.tended_votes,cpm.postal_votes_rejected, nota.nota_vote
        FROM (
        SELECT m.st_code, m.st_name,mpc.PC_TYPE,COUNT(DISTINCT(mpc.pc_no)) 'seats',mpc.pc_no, mpc.PC_NAME,
        SUM(cda.gen_electors_male+cda.service_male_electors+cda.nri_male_electors) AS emale,
        SUM(cda.gen_electors_female+cda.service_female_electors+cda.nri_female_electors) AS efemale ,
        SUM(cda.nri_third_electors+cda.gen_electors_other+cda.service_third_electors)AS eother ,
        SUM(cda.gen_electors_male+cda.service_male_electors+cda.nri_male_electors
        +cda.gen_electors_female+cda.service_female_electors+cda.nri_female_electors+cda.nri_third_electors+cda.gen_electors_other+cda.service_third_electors) AS etotal,
        SUM(cda.nri_male_electors+cda.nri_female_electors+cda.nri_third_electors) AS nrielectors,
        SUM(cda.service_male_electors+cda.service_female_electors+cda.service_third_electors) AS serviceelectors
        FROM electors_cdac cda,m_pc mpc ,m_state m, m_election_details med
        WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019
        AND med.st_code = cda.st_code AND med.CONST_NO = cda.pc_no AND med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1' AND med.year = '2019'
        GROUP BY mpc.st_code,mpc.PC_TYPE
        )TEMP,( SELECT cp.st_code, v_mpc.pc_type,SUM(cp.evm_vote) AS 'evm_vote',SUM(cp.total_vote) AS 'total_vote',SUM(cp.postaltotalvote) AS 'postaltotalvote',SUM(cp.tended_votes) AS 'tended_votes', sum(cp.rejectedvote) as postal_votes_rejected FROM counting_pcmaster cp, m_pc v_mpc WHERE cp.st_code = v_mpc.st_code AND cp.pc_no = v_mpc.pc_no GROUP BY cp.st_code,v_mpc.pc_type
) cpm,m_pc v_mpc, (SELECT f_nota.st_code,f_nota.pc_no,SUM(f_nota.total_vote) AS 'nota_vote',f_mpc.pc_type FROM counting_pcmaster f_nota, m_pc f_mpc WHERE f_mpc.st_code = f_nota.st_code AND f_mpc.pc_no = f_nota.pc_no AND f_nota.candidate_id = 4319  GROUP BY f_mpc.st_code,f_mpc.pc_type) nota
        WHERE TEMP.st_code=cpm.st_code AND TEMP.pc_type = cpm.pc_type AND TEMP.st_code=nota.st_code AND TEMP.pc_type = nota.pc_type
        GROUP BY TEMP.st_code,TEMP.PC_TYPE
        )TEMP1
        LEFT JOIN ( SELECT ec.st_code,e_mpc.pc_type ,SUM(ec.general_male_voters) AS 'general_male_voters',SUM(ec.general_female_voters) AS 'general_female_voters',SUM(ec.general_other_voters) AS 'general_other_voters',SUM(ec.nri_male_voters+ec.nri_female_voters+ec.nri_other_voters) AS voternri,ec.postal_votes_rejected,ec.votes_not_retreived_from_evm FROM electors_cdac_other_information ec, m_pc e_mpc WHERE ec.st_code = e_mpc.st_code AND ec.pc_no = e_mpc.pc_no GROUP BY ec.st_code,e_mpc.pc_type) ecoi
        ON TEMP1.st_code = ecoi.st_code AND TEMP1.pc_type = ecoi.pc_type
        GROUP BY TEMP1.st_code,TEMP1.PC_TYPE ) inner_tab
        GROUP BY st_code,pc_type");

        

          foreach ($voterarray as $key => $value) {

             

            $voterarraynew[$value->st_name][$value->pc_type] = array(
              'st_code' => $value->st_code,
              'pc_type' => $value->pc_type,
              'seats' => $value->seats,

              'emale' => $value->emale,
              'efemale' => $value->efemale,
              'eother' => $value->eother,
              'etotal' => $value->etotal,

              'nrielectors' => $value->nrielectors,
              'serviceelectors' => $value->serviceelectors,
              'evm_vote' => $value->evm_vote,
              'total_vote' => $value->total_vote,

              'postaltotalvote' => $value->postaltotalvote,
              'tended_votes' => $value->tended_votes,
              'nota_vote' => $value->nota_vote,

              'general_male_voters' => $value->general_male_voters,
              'general_female_voters' => $value->general_female_voters,
              'general_other_voters' => $value->general_other_voters,
              'voternri' => $value->voternri,
              'postal_votes_rejected' => $value->postal_votes_rejected,
              'votes_not_retreived_from_evm' => $value->votes_not_retreived_from_evm,

            );

            
          }

         // dd($voterarraynew);

          $voterarray = $voterarraynew;
         // echo "<pre>"; print_r($voterarray); die;


      		if($user->designation == 'ROPC'){
      			$prefix 	= 'ropc';
      		}else if($user->designation == 'CEO'){	
      			$prefix 	= 'pcceo';
      		}else if($user->role_id == '27'){
      			$prefix 	= 'eci-index';
      		}else if($user->role_id == '7'){
      			$prefix 	= 'eci';
      		}

        DB::enableQueryLog();
     
        if($request->path() == "$prefix/voterInformation"){            
            return view('IndexCardReports.IndexCardEciReport.Vol1.voter-information', compact('user_data','voterarray'));
          }
          elseif($request->path() == "$prefix/voterInformationPDF"){
                $pdf=PDF::loadView('IndexCardReports.IndexCardEciReport.Vol1.voter-information-pdf',[
                  'voterarray'=>$voterarray,
                'user_data'=>$user_data
          ]);
            return $pdf->download("voter-information.pdf");
           }
		    elseif($request->path() == "$prefix/voterInformationXls"){
            $voterquery = json_decode( json_encode($voterarray), true);            

            return Excel::create('voterInformation', function($excel) use ($voterquery) {
             $excel->sheet('mySheet', function($sheet) use ($voterquery)
             {
            $sheet->mergeCells('A1:U1');
            $sheet->cells('A1:U1', function($cells) {
                $cells->setFont(array(
                    'size'       => '15',
                    'bold'       => true
                ));
                $cells->setAlignment('center');
            });
           $sheet->cell('A1', function($cells) {
                $cells->setValue('10-Voters Information');
            });


            $sheet->cell('A2', function($cells) {
                $cells->setValue('State/UT');
            });

           $sheet->cell('B2', function($cells) {
                $cells->setValue('Constituency Type');
            });
            
            $sheet->cell('C2', function($cells) {
                $cells->setValue('Seats');
            });

            $sheet->mergeCells('D2:I2');

            $sheet->cell('D2', function($cells) {
                $cells->setValue('Electors');
            });
            $sheet->mergeCells('J2:P2');

            $sheet->cell('J2', function($cells) {
                $cells->setValue('Voters');
            });
           /* $sheet->cell('Q2', function($cells) {
                $cells->setValue('Rejected Votes');
            });
            $sheet->cell('R2', function($cells) {
                $cells->setValue('Votes Not Retrived From EVM');
            });
             $sheet->cell('S2', function($cells) {
                $cells->setValue('NOTA Votes');
            });
            $sheet->cell('T2', function($cells) {
                $cells->setValue('Valid Votes Polled');
            });
            $sheet->cell('U2', function($cells) {
                $cells->setValue('Tendered Votes');
            });*/

            $sheet->cell('D3', function($cells) {
                $cells->setValue('Male');
            });
            $sheet->cell('E3', function($cells) {
                $cells->setValue('Female');
            });
            $sheet->cell('F3', function($cells) {
                $cells->setValue('Other');
            });
            $sheet->cell('G3', function($cells) {
                $cells->setValue('Total');
            });
            $sheet->cell('H3', function($cells) {
                $cells->setValue('NRI');
            });
            $sheet->cell('I3', function($cells) {
                $cells->setValue('Service');
            });


            $sheet->cell('J3', function($cells) {
                $cells->setValue('Male');
            });
            $sheet->cell('K3', function($cells) {
                $cells->setValue('Female');
            });
            $sheet->cell('L3', function($cells) {
                $cells->setValue('Other');
            });
            $sheet->cell('M3', function($cells) {
                $cells->setValue('Postal');
            });
            $sheet->cell('N3', function($cells) {
                $cells->setValue('Total');
            });
            $sheet->cell('O3', function($cells) {
                $cells->setValue('NRI');
            });
             $sheet->cell('P3', function($cells) {
                $cells->setValue('Poll %');
            });
              $sheet->cell('Q3', function($cells) {
                $cells->setValue('Rejected Votes Postal');
            });
             $sheet->cell('R3', function($cells) {
                $cells->setValue('Votes Not Retrived From EVM');
            });
             $sheet->cell('S3', function($cells) {
                $cells->setValue('NOTA Votes');
            });
              $sheet->cell('T3', function($cells) {
                $cells->setValue('Valid Votes Polled');
            });
               $sheet->cell('U3', function($cells) {
                $cells->setValue('Tendered Votes');
            });
           
           

             if (!empty($voterquery)) {
                 $i= 4;
                 
                 //$sn=1;
             // echo '<pre>';print_r($voterquery);die;
                  //$stname=$session['election_detail']['st_name'];
                foreach ($voterquery as $row) {  
                if($row['pc_type']==NULL) {
                  $sheet->cell('A'.$i,''); 
                  $sheet->cell('B'.$i, 'Sub Total'); 

                }  
                        $sheet->cell('A'.$i ,$row['st_name']); 
                        $sheet->cell('B'.$i, $row['pc_type']); 
                        $sheet->cell('C'.$i, $row['seats']);
                        $sheet->cell('D'.$i, ($row['emale']) ? $row['emale'] : '=(0)'); 
                        $sheet->cell('E'.$i, ($row['efemale']) ? $row['efemale'] : '=(0)'); 
                        $sheet->cell('F'.$i, ($row['eother']) ? $row['eother'] : '=(0)'); 
                        $sheet->cell('G'.$i, ($row['etotal']) ? $row['etotal'] : '=(0)'); 
                        $sheet->cell('H'.$i, ($row['nrielectors']) ? $row['nrielectors'] : '=(0)'); 
                        $sheet->cell('I'.$i, ($row['serviceelectors']) ? $row['serviceelectors'] : '=(0)'); 

                        $sheet->cell('J'.$i, ($row['general_male_voters']) ? $row['general_male_voters'] : '=(0)'); 
                        $sheet->cell('K'.$i, ($row['general_female_voters']) ? $row['general_female_voters'] : '=(0)');  
                        $sheet->cell('L'.$i, ($row['general_other_voters']) ? $row['general_other_voters'] : '=(0)'); 
                        $sheet->cell('M'.$i, ($row['postaltotalvote']) ? $row['postaltotalvote'] : '=(0)'); 
                        $totalvoter= ($row['general_male_voters']+$row['general_female_voters']+$row['general_other_voters']+$row['postaltotalvote']);
                        $sheet->cell('N'.$i, ($totalvoter) ? $totalvoter : '=(0)'); 
                        $sheet->cell('O'.$i, ($row['voternri']) ? $row['voternri'] : '=(0)'); 
                        // 
                        if($row['etotal']!=0)
                        {
                        $pollpercent=round((($totalvoter/$row['etotal'])*100),2);
                        }
                        $sheet->cell('P'.$i, ($pollpercent) ? $pollpercent : '=(0)'); 
                        //
                         $sheet->cell('Q'.$i, ($row['postal_votes_rejected']) ? $row['postal_votes_rejected'] : '=(0)'); 
                         $sheet->cell('R'.$i, ($row['votes_not_retreived_from_evm']) ? $row['votes_not_retreived_from_evm'] : '=(0)'); 
                         $sheet->cell('S'.$i, ($row['nota_vote']) ? $row['nota_vote'] : '=(0)'); 
                         $sheet->cell('T'.$i, ($row['total_vote']) ? $row['total_vote'] : '=(0)'); 
                         $sheet->cell('U'.$i, ($row['tended_votes']) ? $row['tended_votes'] : '=(0)'); 
                          $i++;  
                      }   
                    }
                });
             })->download('xls');

        }
	}
}  // end class
