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
    $getuserrecord = DB::select("SELECT temp2.*,IFNULL (temp1.win,0) AS win
FROM(SELECT *,(SELECT SUM(ecd.gen_electors_male+ecd.gen_electors_female+ecd.gen_electors_other+ecd.service_male_electors+ecd.service_female_electors+ecd.service_third_electors+ecd.nri_male_electors+ecd.nri_female_electors+ecd.nri_third_electors)AS totaleelctors FROM electors_cdac ecd
WHERE ecd.st_code=temp.st_code GROUP BY temp.st_code,temp.PARTY_ID )AS totaleelctors,(SELECT SUM(cp.total_vote)AS totalvalid_st_vote  FROM counting_pcmaster cp
WHERE cp.st_code=temp.st_code AND cp.party_id != '1180' GROUP BY cp.st_code )AS totalvalid_st_vote
FROM(SELECT ms.st_code,ms.ST_NAME,mp.PARTYABBRE,mp.PARTYNAME,mp.PARTYTYPE,cp.party_id,SUM(total_vote)AS totalvalidvote FROM m_state ms,counting_pcmaster cp,m_party mp WHERE cp.st_code=ms.st_code AND cp.party_id=mp.ccode GROUP BY cp.st_code,cp.party_id ORDER BY ms.st_code,mp.PARTYTYPE,mp.PARTYNAME ASC) temp)temp2 LEFT JOIN(SELECT st_code,lead_cand_party,lead_cand_hparty,lead_cand_partyid,SUM(CASE STATUS WHEN '1' THEN '1' ELSE 0 END) AS win FROM winning_leading_candidate WHERE  trail_cand_party!='null' AND lead_cand_party!=''
GROUP BY lead_cand_party,st_code ORDER BY st_code ASC)temp1  ON temp2.st_code=temp1.st_code
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
   
			
			$pdf = PDF::loadView('IndexCardReports.IndexCardEciReport.Vol2.statewise-seat-won-pdf',[

    'getuserrecord'=>$getuserrecord,
    'user_data'=>$user_data]);

       if(verifyreport(17)){
        
                  $file_name = 'State Wise Seat Won & Valid Votes Polled by Political Parties'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/17/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '17',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
    return $pdf->download('State Wise Seat Won & Valid Votes Polled by Political Parties.pdf');
    }
    elseif($request->path() == "$prefix/StatewiseSeatWonXls")
    {

       $data = json_decode( json_encode($getuserrecord), true);

      // echo "pre"; print_r($data); die;

       return Excel::create('State Wise Seat Won & Valid Votes Polled by Political Parties', function($excel) use ($data) {
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
                                   $validvotepolledbyparty= ROUND((($row['totalvalidvote']/$row['totalvalid_st_vote'])*100),4);
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

                        $i = $i+3;

          

                    $sheet->mergeCells("A$i:B$i");
                    $sheet->cell('A'.$i, function($cells) {
                      $cells->setValue('Disclaimer');
                      $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                    });

                    $i = $i+1;

                    $sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
                    $sheet->setSize('A'.$i, 25,40);



                    $sheet->mergeCells("A$i:I$i");
                    $sheet->cell('A'.$i, function($cells) {
                    $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                    $cells->setFont(array('name' => 'Times New Roman','size' => 10));
                    });
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
		 //     $queue = DB::getQueryLog();
			// echo'<pre>'; print_r($queue);die;
			
			if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}
			
		
		//echo '<pre>'; print_r($data); die;


		

            if($request->path() == "$prefix/ParticipationofWomenInNationalParties"){
                return view('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-national-parties',compact('data','user_data'));
            }elseif($request->path() == "$prefix/ParticipationofWomenInNationalPartiesPDF"){
                $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-national-parties-pdf',[
                'data'=>$data,
                'user_data'=>$user_data
          ]);

                 if(verifyreport(26)){
        
                  $file_name = 'participation-of-women-in-national-parties'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/26/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '26',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
           return $pdf->download('participation-of-women-in-national-parties.pdf');
           }elseif ($request->path() == "$prefix/ParticipationofWomenInNationalPartiesXls") {
               $data = json_decode( json_encode($data), true);
                return Excel::create('ParticipationofWomenInNationalPartiesXls', function($excel) use ($data) {
                         $excel->sheet('mySheet', function($sheet) use ($data)
                         {
                        $sheet->mergeCells('A1:J1');
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

						$sheet->mergeCells('B2:D2');
                       $sheet->cell('B2', function($cells) {
                            $cells->setValue('Candidates');
                        });
						
                        $sheet->cell('E2', function($cells) {
                            $cells->setValue('Percentage');
                        });

                        $sheet->mergeCells('E2:F2');

                         $sheet->mergeCells('G2:G3');

                        $sheet->cell('G2', function($cells) {
                            $cells->setValue('Votes Secured By Women Candidates');
                        });
                        $sheet->mergeCells('H2:J2');

                        $sheet->cell('H2', function($cells) {
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
						
						$sheet->cell('E3', function($cells) {
                            $cells->setValue('Won');
                        });
                         $sheet->cell('F3', function($cells) {
                            $cells->setValue('DF');
                        });
						
						
						
                        $sheet->cell('H3', function($cells) {
                            $cells->setValue('Over total electors');
                        });
                        $sheet->cell('I3', function($cells) {
                            $cells->setValue('Over total valid votes');
                        });

                        $sheet->cell('J3', function($cells) {
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
                                    $sheet->cell('C'.$i, ($rows['WON']) ? $rows['WON'] : '=(0)');
                                    $sheet->cell('D'.$i, ($rows['DF']) ? $rows['DF'] : '=(0)');
                                    $sheet->cell('E'.$i, round((($rows['WON']/$rows['contested'])*100),2));
                                    $sheet->cell('F'.$i, round((($rows['DF']/$rows['contested'])*100),2));
                                    $sheet->cell('G'.$i, ($rows['votes_secured_by_Women']) ? $rows['votes_secured_by_Women'] : '=(0)');
                                    $sheet->cell('H'.$i, round($peroverelectors,2));

                                    $sheet->cell('I'.$i, round($overTotalValidVotes,2));
                                    $sheet->cell('J'.$i,round($ovsbp,2));

                                      $i++;
                                  }  // $i +=1;

                                    $sheet->cell('A'.$i, 'Total');
                                    $sheet->cell('B'.$i, ($totalcontested > 0) ? $totalcontested:'=(0)' );
                                    $sheet->cell('C'.$i, ($twon > 0) ? $twon:'=(0)' );
                                    $sheet->cell('D'.$i, ($tfd > 0) ? $tfd:'=(0)' );
                                    $sheet->cell('E'.$i, ($twonper > 0) ? $twonper:'=(0)' );
                                    $sheet->cell('F'.$i, $tdfper);
                                    $sheet->cell('G'.$i, ($totalVoteSecured > 0) ? $totalVoteSecured:'=(0)' );
                                    $sheet->cell('H'.$i, round($ttotalElectors,2));
                                    $sheet->cell('I'.$i, round($totvv,2));
                                    $sheet->cell('J'.$i, round($totvsp,2));
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
    WHERE partytype ='Z' 
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
    WHERE partytype ='Z'
    AND cand_gender = 'female'
    GROUP BY partyabbre ) FFF WHERE FFF.party_id=TEMP.party_id) AS electrols_Total,

    (SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
    WHERE party_id=TEMP.party_id GROUP BY party_id)AS totalvalid_valid_vote,

    (SELECT SUM(electors_total)AS totaleelctors FROM electors_cdac
    WHERE party_id=TEMP.party_id  GROUP BY PARTY_ID )AS sum_of_total_eelctors,
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
    WHERE partytype ='Z' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'
    
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
                $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-as-independent-candidate-pdf',[
                'data'=>$data,
                'user_data'=>$user_data
          ]);


                 if(verifyreport(29)){
        
                  $file_name = 'participation-of-women-as-independent-candidate'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/29/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '29',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
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
        SELECT partyabbre, party_id,PARTYNAME,SUM(gen_electors_female + nri_female_electors + service_female_electors) AS Total_electros_female
        FROM m_party m
        JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
        WHERE partytype ='U' 
        AND cand_gender = 'female'
        GROUP BY partyabbre )EEE WHERE EEE.party_id=TEMP.party_id) AS Total_electros_female,
        ( SELECT electrols_Total
          FROM (
        SELECT partyabbre, party_id,PARTYNAME,SUM(gen_electors_male + gen_electors_female + gen_electors_other + nri_male_electors + nri_female_electors + nri_third_electors + service_male_electors + service_female_electors + service_third_electors) AS electrols_Total
        FROM m_party m
        JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
        WHERE partytype ='U'
        AND cand_gender = 'female'
        GROUP BY partyabbre ) FFF WHERE FFF.party_id=TEMP.party_id) AS electrols_Total,

        (SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
        WHERE party_id=TEMP.party_id GROUP BY party_id)AS totalvalid_valid_vote,

        (SELECT SUM(gen_electors_male + gen_electors_female + gen_electors_other + nri_male_electors + nri_female_electors + nri_third_electors + service_male_electors + service_female_electors + service_third_electors)AS totaleelctors FROM electors_cdac
        WHERE party_id=TEMP.party_id GROUP BY PARTY_ID )AS sum_of_total_eelctors,
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
                $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-registered-parties-pdf',[            
                'data'=>$data,
                'user_data'=>$user_data
          ]);

                 if(verifyreport(28)){
        
                  $file_name = 'Participation of Women in Registered (Unrecognised) Parties'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/28/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '28',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
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
                            $cells->setValue(' CANDIDATES ');
							$cells->setFont(array('bold' => true));
							$cells->setAlignment('center');
                        });

                        $sheet->mergeCells('E2:F2');

                        $sheet->cell('E2', function($cells) {
                            $cells->setValue(' PERCENTAGE ');
							$cells->setFont(array('bold' => true));
							$cells->setAlignment('center');
                        });

                         $sheet->mergeCells('H2:J2');
                        $sheet->cell('H2', function($cells) {
                            $cells->setValue(' % OF VOTES SECURED ');
							$cells->setFont(array('bold' => true));
							$cells->setAlignment('center');
                        });
						
                        $sheet->cell('A3', function($cells) {
                            $cells->setValue(' PARTY NAME ');
							$cells->setFont(array('bold' => true));
                        });

                        $sheet->cell('B3', function($cells) {
                            $cells->setValue(' CONTESTED ');
							$cells->setFont(array('bold' => true));
                        });
                         $sheet->cell('C3', function($cells) {
                            $cells->setValue(' WON ');
							$cells->setFont(array('bold' => true));
                        });
                        $sheet->cell('D3', function($cells) {
                            $cells->setValue(' DF ');
							$cells->setFont(array('bold' => true));
                        });
                        $sheet->cell('E3', function($cells) {
                            $cells->setValue(' WON ');
							$cells->setFont(array('bold' => true));
                        });

                        $sheet->cell('F3', function($cells) {
                            $cells->setValue(' DF ');
							$cells->setFont(array('bold' => true));
                        });
                        $sheet->cell('G3', function($cells) {
                            $cells->setValue(' VOTES SECURED BY PARTY ');
							$cells->setFont(array('bold' => true));
                        });
                        $sheet->cell('H3', function($cells) {
                            $cells->setValue(' OVER TOTAL ELECTORS IN STATE ');
							$cells->setFont(array('bold' => true));
                        });
                         $sheet->cell('I3', function($cells) {
                            $cells->setValue(' OVER TOTAL VALID VOTES IN STATE ');
							$cells->setFont(array('bold' => true));
                        });
						 $sheet->cell('J3', function($cells) {
                            $cells->setValue(' OVER VOTES SECURED BY THE PARTY ');
							$cells->setFont(array('bold' => true));
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

                                    
									$sheet->cell('A'.$i, function($cells) {
										$cells->setValue(' TOTAL ');
										$cells->setFont(array('bold' => true));
									});
									
									
									$sheet->cell('B'.$i, function($cells) use($totalcontested) {
										$cells->setValue(($totalcontested > 0) ? $totalcontested:'=(0)');
										$cells->setFont(array('bold' => true));
									});
									
									$sheet->cell('C'.$i, function($cells) use($twon) {
										$cells->setValue(($twon > 0) ? $twon:'=(0)');
										$cells->setFont(array('bold' => true));
									});
									
									$sheet->cell('D'.$i, function($cells) use($tfd) {
										$cells->setValue(($tfd > 0) ? $tfd:'=(0)');
										$cells->setFont(array('bold' => true));
									});
									
									
									$sheet->cell('E'.$i, function($cells) use($twonper) {
										$cells->setValue(($twonper > 0) ? $twonper:'=(0)');
										$cells->setFont(array('bold' => true));
									});
									
									$sheet->cell('F'.$i, function($cells) use($tdfper) {
										$cells->setValue(($tdfper > 0) ? $tdfper:'=(0)');
										$cells->setFont(array('bold' => true));
									});
									
									
									
									$sheet->cell('G'.$i, function($cells) use($totalVoteSecured) {
										$cells->setValue(($totalVoteSecured > 0) ? $totalVoteSecured:'=(0)');
										$cells->setFont(array('bold' => true));
									});
									
									
									$sheet->cell('H'.$i, function($cells) use($ttotalElectors) {
										$cells->setValue(round($ttotalElectors,2));
										$cells->setFont(array('bold' => true));
									});
									
									$sheet->cell('I'.$i, function($cells) use($totvv) {
										$cells->setValue(round($totvv,2));
										$cells->setFont(array('bold' => true));
									});
									   
                              
                                    //$sheet->cell('J'.$i, round($totvsp,2));
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



                $sheet->mergeCells("A$i:K$i");
                $sheet->cell('A'.$i, function($cells) {
                $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                $cells->setFont(array('name' => 'Times New Roman','size' => 10));
                });
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




      $voterarray=DB::select("SELECT st_code,st_name,pc_type,SUM(seats)AS seats, SUM(emale) AS emale,SUM(efemale) AS efemale,SUM(eother) AS eother,SUM(etotal) AS etotal, SUM(nrielectors) AS nrielectors,SUM(serviceelectors) AS serviceelectors,SUM(tended_votes)AS tended_votes, SUM(nota_vote) AS nota_vote,SUM(general_male_voters) AS general_male_voters,
      SUM(general_female_voters) AS general_female_voters,SUM(general_other_voters) AS general_other_voters,SUM(voternri) AS voternri,SUM(postal_votes_rejected) AS postal_votes_rejected, SUM(votes_not_retreived_from_evm) AS votes_not_retreived_from_evm, SUM(rejected_votes_due_2_other_reason) AS rejected_votes_due_2_other_reason, totalpostalvote_ecoi,postal_vote_rejected_ecoi , sum(test_votes_49_ma) as test_votes_49_ma FROM (
      SELECT TEMP1.*,ecoi.general_male_voters,ecoi.general_female_voters,general_other_voters,ecoi.voternri
      ,ecoi.votes_not_retreived_from_evm,ecoi.totalpostalvote_ecoi,ecoi.postal_vote_rejected_ecoi,ecoi.rejected_votes_due_2_other_reason, ecoi.test_votes_49_ma
      FROM
      (
      SELECT TEMP.*,cpm.tended_votes,cpm.postal_votes_rejected, nota.nota_vote
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
      WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code
      AND med.st_code = cda.st_code AND med.CONST_NO = cda.pc_no AND med.CONST_TYPE = 'PC' AND  med.election_status = '1' 
      GROUP BY mpc.st_code,mpc.PC_TYPE
      )TEMP,(
       
       SELECT st_code,PC_TYPE,SUM(tended_votes)AS tended_votes,sum(postal_votes_rejected) as postal_votes_rejected
       FROM
       (
       SELECT DISTINCT m.PC_TYPE,cpm.st_code,cpm.tended_votes AS tended_votes, cpm.rejectedvote AS postal_votes_rejected
       FROM counting_pcmaster cpm ,m_pc m
       WHERE cpm.st_code = m.ST_CODE
       AND cpm.pc_no = m.PC_NO
       GROUP BY cpm.st_code,cpm.pc_no,m.PC_TYPE
       )TEMP
       GROUP BY st_code,PC_TYPE       
) cpm,m_pc v_mpc, (SELECT f_nota.st_code,f_nota.pc_no,SUM(f_nota.total_vote) AS 'nota_vote',f_mpc.pc_type FROM counting_pcmaster f_nota, m_pc f_mpc WHERE f_mpc.st_code = f_nota.st_code AND f_mpc.pc_no = f_nota.pc_no AND f_nota.party_id = 1180  GROUP BY f_mpc.st_code,f_mpc.pc_type) nota
      WHERE TEMP.st_code=cpm.st_code AND TEMP.pc_type = cpm.pc_type AND TEMP.st_code=nota.st_code AND TEMP.pc_type = nota.pc_type
      GROUP BY TEMP.st_code,TEMP.PC_TYPE
      )TEMP1
      LEFT JOIN ( SELECT ec.st_code,e_mpc.pc_type ,SUM(ec.general_male_voters) AS 'general_male_voters',
      SUM(ec.general_female_voters) AS 'general_female_voters',SUM(ec.general_other_voters) AS 'general_other_voters',
      SUM(ec.nri_male_voters+ec.nri_female_voters+ec.nri_other_voters) AS voternri,ec.postal_votes_rejected,
      sum(ec.votes_not_retreived_from_evm) as votes_not_retreived_from_evm ,sum(ec.rejected_votes_due_2_other_reason) as rejected_votes_due_2_other_reason,SUM(ec.service_postal_votes_under_section_8+ec.service_postal_votes_gov) AS totalpostalvote_ecoi,  SUM(ec.test_votes_49_ma) AS test_votes_49_ma,
        SUM(ec.postal_votes_rejected) AS postal_vote_rejected_ecoi FROM electors_cdac_other_information ec, m_pc e_mpc WHERE ec.st_code = e_mpc.st_code AND ec.pc_no = e_mpc.pc_no GROUP BY ec.st_code,e_mpc.pc_type) ecoi
      ON TEMP1.st_code = ecoi.st_code AND TEMP1.pc_type = ecoi.pc_type
      GROUP BY TEMP1.st_code,TEMP1.PC_TYPE ) inner_tab
      GROUP BY st_code,pc_type");
//echo "<pre>"; print_r($voterarray); die;
        

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
              
              'total_vote' => $value->general_male_voters+$value->general_female_voters+$value->general_other_voters+$value->totalpostalvote_ecoi+$value->voternri,

              'postaltotalvote' => $value->totalpostalvote_ecoi,
              'tended_votes' => $value->tended_votes,
              'nota_vote' => $value->nota_vote,

              'general_male_voters' => $value->general_male_voters,
              'general_female_voters' => $value->general_female_voters,
              'general_other_voters' => $value->general_other_voters,
              'voternri' => $value->voternri,
              'postal_votes_rejected' => $value->postal_votes_rejected,
              'votes_not_retreived_from_evm' => $value->votes_not_retreived_from_evm,
              'rejected_votes_due_2_other_reason' => $value->rejected_votes_due_2_other_reason,
              'test_votes_49_ma' => $value->test_votes_49_ma,

            );

            
          }

          

          $voterarray = $voterarraynew;
          //echo "<pre>"; print_r($voterarray); die;


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
                $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.IndexCardEciReport.Vol1.voter-information-pdf',[
                  'voterarray'=>$voterarray,
                'user_data'=>$user_data
          ]);

                 if(verifyreport(10)){
        
                  $file_name = 'VOTERS INFORMATION'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/10/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '10',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
            return $pdf->download("VOTERS INFORMATION.pdf");
           }
		    elseif($request->path() == "$prefix/voterInformationXls"){
            $voterquery = json_decode( json_encode($voterarray), true);

            return Excel::create('VOTERS INFORMATION', function($excel) use ($voterarray) {
             $excel->sheet('mySheet', function($sheet) use ($voterarray)
             {

           $sheet->getStyle('A')->getAlignment()->setWrapText(true);
           $sheet->getStyle('C')->getAlignment()->setWrapText(true);
           $sheet->getStyle('B')->getAlignment()->setWrapText(true);

           $sheet->getStyle('C3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('D3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('E3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('F3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('G3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('H3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('I3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('J3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('K3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('L3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('M3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('N3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('O3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('P3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('Q3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('R3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('S3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('T3')->getAlignment()->setWrapText(true);
           $sheet->getStyle('U3')->getAlignment()->setWrapText(true);





           $sheet->mergeCells('A1:U1');

            $sheet->cells('A1:U1', function($cells) {
                $cells->setFont(array(
                    'size'       => '13',
                    'bold'       => true
                ));
                $cells->setAlignment('center');
            });
           $sheet->cell('A1', function($cells) {
                $cells->setValue('10-Voters Information');
            });



            $sheet->cell('A2', function($cells) {
                $cells->setValue('STATE NAME');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });

             $sheet->setSize('A2', 12, 20);

           $sheet->cell('B2', function($cells) {
                $cells->setValue('CONSTITUENCY TYPE');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });

            $sheet->setSize('B2',20, 20);

            $sheet->cell('C3', function($cells) {
                $cells->setValue('NO OF SEATS');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });

            $sheet->setSize('C3', 10, 40);

            $sheet->mergeCells('D2:I2');

            $sheet->cell('D2', function($cells) {
                $cells->setValue('Electors');
                $cells->setAlignment('center');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->mergeCells('J2:P2');

            $sheet->cell('J2', function($cells) {
                $cells->setValue('Voters');
                $cells->setAlignment('center');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });


            $sheet->cell('D3', function($cells) {
                $cells->setValue('MALE');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('D3', 10, 40);
            $sheet->cell('E3', function($cells) {
                $cells->setValue('FEMALE');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('E3', 10, 40);
            $sheet->cell('F3', function($cells) {
                $cells->setValue('THIRD GENDER');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('F3', 10, 40);
            $sheet->cell('G3', function($cells) {
                $cells->setValue('TOTAL');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('G3', 10, 40);
            $sheet->cell('H3', function($cells) {
                $cells->setValue('NRIs');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('H3', 10, 40);
            $sheet->cell('I3', function($cells) {
                $cells->setValue('SERVICE');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('I3', 10, 40);


            $sheet->cell('J3', function($cells) {
                $cells->setValue('MALE');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('J3', 10, 40);
            $sheet->cell('K3', function($cells) {
                $cells->setValue('FEMALE');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('K3', 10, 40);
            $sheet->cell('L3', function($cells) {
                $cells->setValue('THIRD GENDER');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('L3', 10, 40);
            $sheet->cell('M3', function($cells) {
                $cells->setValue('POSTAL');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('M3', 10, 40);
            $sheet->cell('N3', function($cells) {
                $cells->setValue('TOTAL');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('N3', 10, 40);
            $sheet->cell('O3', function($cells) {
                $cells->setValue('NRIs');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('O3', 10, 40);
             $sheet->cell('P3', function($cells) {
                $cells->setValue('POLL %');

                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('P3', 10, 40);
              $sheet->cell('Q3', function($cells) {
                $cells->setValue('Rejected Votes (Postal)');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('Q3', 10, 40);
             $sheet->cell('R3', function($cells) {
                $cells->setValue('(Votes Rejected / Votes Not Retrived From EVM)');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('R3', 10, 40);
             $sheet->cell('S3', function($cells) {
                $cells->setValue('NOTA Votes');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('S3', 10, 40);
              $sheet->cell('T3', function($cells) {
                $cells->setValue('Valid Votes Polled');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('T3', 10, 40);
               $sheet->cell('U3', function($cells) {
                $cells->setValue('Tendered Votes');
                $cells->setFont(array('name' => 'Times New Roman', 'size' => 8, 'bold' => true));
            });
            $sheet->setSize('U3', 10, 40);



             if (!empty($voterarray)) {
                 $i= 4;

                $grandtotal= $grandseattotal= $grandemaletotal= $grandefemaletotal = $grandeothertotal = $grandestatetotal
        = $grandnrielectorstotal =$grandserviceelectorstotal = $grandgenmalevotertotal = $grandgenfemalevotertotal 
        = $grandgenothervotertotal = $grandpostaltotalstate = $grandtotalvotestate =$grandtotalnristate 
        = $grandpostalrejectedtotal = $grandvotesnotretrivedtotal = $grandnotavotetotal = $grandtendedvotetotal = $grandtestvote = $grandduetoother = 0;
                foreach($voterarray as $row1 => $value1){
                       $sheet->cell('A'.$i ,$row1);
                       $seattotal = $emaletotal= $efemaletotal = $eothertotal = $estatetotal = $nrielectorstotal 
      = $serviceelectorstotal = $genmalevotertotal = $genfemalevotertotal = $genothervotertotal = $postaltotalstate 
      = $totalvotestate = $totalnristate = $postalrejectedtotal = $votesnotretrivedtotal = $notavotetotal = $tendedvotetotal =  $testtotal = $duetototal =0;
                foreach($value1 as $row2 => $value2) {
                        //echo '<pre>';print_r($row);die;

                        $sheet->cell('B'.$i, $value2['pc_type']);
                        $sheet->cell('C'.$i, $value2['seats']);
                        $sheet->cell('D'.$i, ($value2['emale']) ? $value2['emale'] : '=(0)');
                        $sheet->cell('E'.$i, ($value2['efemale']) ? $value2['efemale'] : '=(0)');
                        $sheet->cell('F'.$i, ($value2['eother']) ? $value2['eother'] : '=(0)');
                        $sheet->cell('G'.$i, ($value2['etotal']) ? $value2['etotal'] : '=(0)');
                        $sheet->cell('H'.$i, ($value2['nrielectors']) ? $value2['nrielectors'] : '=(0)');
                        $sheet->cell('I'.$i, ($value2['serviceelectors']) ? $value2['serviceelectors'] : '=(0)');

                        $sheet->cell('J'.$i, ($value2['general_male_voters']) ? $value2['general_male_voters'] : '=(0)');
                        $sheet->cell('K'.$i, ($value2['general_female_voters']) ? $value2['general_female_voters'] : '=(0)');
                        $sheet->cell('L'.$i, ($value2['general_other_voters']) ? $value2['general_other_voters'] : '=(0)');
                        $sheet->cell('M'.$i, ($value2['postaltotalvote']) ? $value2['postaltotalvote'] : '=(0)');
                        $totalvoter= ($value2['total_vote']);
                        $sheet->cell('N'.$i, ($totalvoter) ? $totalvoter : '=(0)');
                        $sheet->cell('O'.$i, ($value2['voternri']) ? $value2['voternri'] : '=(0)');
                        //
                        if($value2['eother']!=0)
                        {
                        $pollpercent=round((($totalvoter/$value2['etotal'])*100),2);
                        }
                        $sheet->cell('P'.$i, ($pollpercent) ? $pollpercent : '=(0)');
                        //
                         $sheet->cell('Q'.$i, ($value2['postal_votes_rejected']) ? $value2['postal_votes_rejected'] : '=(0)');
                         $sheet->cell('R'.$i, ($value2['votes_not_retreived_from_evm'] + $value2['rejected_votes_due_2_other_reason']) ? ($value2['votes_not_retreived_from_evm'] + $value2['rejected_votes_due_2_other_reason']) : '=(0)');
                         $sheet->cell('S'.$i, ($value2['nota_vote']) ? $value2['nota_vote'] : '=(0)');
                         $sheet->cell('T'.$i, ($value2['total_vote']-($value2['postal_votes_rejected']+$value2['votes_not_retreived_from_evm']+$value2['nota_vote']+$value2['rejected_votes_due_2_other_reason'])) ? ($value2['total_vote']-($value2['postal_votes_rejected']+$value2['votes_not_retreived_from_evm']+$value2['nota_vote']+$value2['rejected_votes_due_2_other_reason'])) : '=(0)');
                         $sheet->cell('U'.$i, ($value2['tended_votes']) ? $value2['tended_votes'] : '=(0)');

                         $seattotal += $value2['seats'];
                         $emaletotal += $value2['emale'];
                         $efemaletotal += $value2['efemale'];
                         $eothertotal += $value2['eother'];
                         $estatetotal += $value2['etotal'];

                         $nrielectorstotal += $value2['nrielectors'];
                         $serviceelectorstotal += $value2['serviceelectors'];
                         $genmalevotertotal += $value2['general_male_voters'];
                         $genfemalevotertotal += $value2['general_female_voters'];
                         $genothervotertotal += $value2['general_other_voters'];

                         $postaltotalstate += $value2['postaltotalvote'];
                         $totalvotestate += $value2['total_vote'];
                         $totalnristate += $value2['voternri'];
                         $postalrejectedtotal += $value2['postal_votes_rejected'];
                         $votesnotretrivedtotal  += $value2['votes_not_retreived_from_evm'];

                         $notavotetotal += $value2['nota_vote'];
                         $tendedvotetotal += $value2['tended_votes'];

                         $testtotal += $value2['test_votes_49_ma'];
                         $duetototal += $value2['rejected_votes_due_2_other_reason'];



                         $i++;







                      }


                      $sheet->cell('A'.$i, function($cell) {
                       $cell->setValue('State Total');
                       $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                       });
                       $sheet->cell('C'.$i, function($cell) use($seattotal) {
                        $cell->setValue($seattotal);
                        $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('D'.$i, function($cell) use($emaletotal) {
                         $cell->setValue($emaletotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('E'.$i, function($cell) use($efemaletotal) {
                          $cell->setValue($efemaletotal);
                          $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('F'.$i, function($cell) use($eothertotal) {
                           $cell->setValue($eothertotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('G'.$i, function($cell) use($estatetotal) {
                           $cell->setValue($estatetotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('H'.$i, function($cell) use($nrielectorstotal) {
                           $cell->setValue($nrielectorstotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('I'.$i, function($cell) use($serviceelectorstotal) {
                           $cell->setValue($serviceelectorstotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('J'.$i, function($cell) use($genmalevotertotal) {
                           $cell->setValue($genmalevotertotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('K'.$i, function($cell) use($genfemalevotertotal) {
                           $cell->setValue($genfemalevotertotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('L'.$i, function($cell) use($genothervotertotal) {
                           $cell->setValue($genothervotertotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('M'.$i, function($cell) use($postaltotalstate) {
                           $cell->setValue($postaltotalstate);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('N'.$i, function($cell) use($totalvotestate) {
                           $cell->setValue($totalvotestate);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('O'.$i, function($cell) use($totalnristate) {
                           $cell->setValue($totalnristate);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });


                      $sheet->cell('P'.$i, function($cell) use($totalvotestate,$estatetotal) {
                           $cell->setValue(round($totalvotestate/$estatetotal*100,2));
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });


                      $sheet->cell('Q'.$i, function($cell) use($postalrejectedtotal) {
                           $cell->setValue($postalrejectedtotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                     $sheet->cell('R'.$i, function($cell) use($votesnotretrivedtotal,$duetototal) {
                          $cell->setValue($votesnotretrivedtotal + $duetototal);
                          $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                     });
                      $sheet->cell('S'.$i, function($cell) use($notavotetotal) {
                           $cell->setValue($notavotetotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                      $sheet->cell('T'.$i, function($cell) use($totalvotestate,$postalrejectedtotal,$votesnotretrivedtotal,$notavotetotal, $testtotal,$duetototal) {
                           $cell->setValue($totalvotestate-($postalrejectedtotal+$votesnotretrivedtotal+$notavotetotal+$duetototal));
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });
                      $sheet->cell('U'.$i, function($cell) use($tendedvotetotal) {
                           $cell->setValue($tendedvotetotal);
                           $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                      });

                     

                      $grandseattotal += $seattotal;
                      $grandemaletotal += $emaletotal;
                      $grandefemaletotal += $efemaletotal;
                      $grandeothertotal += $eothertotal;
                      $grandestatetotal += $estatetotal;

                      $grandnrielectorstotal += $nrielectorstotal;
                      $grandserviceelectorstotal += $serviceelectorstotal;

                      $grandgenmalevotertotal += $genmalevotertotal;
                      $grandgenfemalevotertotal += $genfemalevotertotal;
                      $grandgenothervotertotal += $genothervotertotal;

                      $grandpostaltotalstate += $postaltotalstate;
                      $grandtotalvotestate += $totalvotestate;
                      $grandtotalnristate += $totalnristate;

                      $grandpostalrejectedtotal += $postalrejectedtotal;
                      $grandvotesnotretrivedtotal += $votesnotretrivedtotal;

                      $grandnotavotetotal += $notavotetotal;
                      $grandtendedvotetotal += $tendedvotetotal;

                      $grandtestvote += $testtotal;
                      $grandduetoother += $duetototal;


                        $i++;

                    }

                    $sheet->cell('A'.$i, function($cell) {
                     $cell->setValue('Grand Total');
                     $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                     });
                     $sheet->cell('C'.$i, function($cell) use($grandseattotal) {
                      $cell->setValue($grandseattotal);
                      $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('D'.$i, function($cell) use($grandemaletotal) {
                       $cell->setValue($grandemaletotal);
                       $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('E'.$i, function($cell) use($grandefemaletotal) {
                        $cell->setValue($grandefemaletotal);
                        $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('F'.$i, function($cell) use($grandeothertotal) {
                         $cell->setValue($grandeothertotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('G'.$i, function($cell) use($grandestatetotal) {
                         $cell->setValue($grandestatetotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('H'.$i, function($cell) use($grandnrielectorstotal) {
                         $cell->setValue($grandnrielectorstotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });



                    $sheet->cell('I'.$i, function($cell) use($grandserviceelectorstotal) {
                         $cell->setValue($grandserviceelectorstotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('J'.$i, function($cell) use($grandgenmalevotertotal) {
                         $cell->setValue($grandgenmalevotertotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('K'.$i, function($cell) use($grandgenfemalevotertotal) {
                         $cell->setValue($grandgenfemalevotertotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('L'.$i, function($cell) use($grandgenothervotertotal) {
                         $cell->setValue($grandgenothervotertotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });



                    $sheet->cell('M'.$i, function($cell) use($grandpostaltotalstate) {
                         $cell->setValue($grandpostaltotalstate);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('N'.$i, function($cell) use($grandtotalvotestate) {
                         $cell->setValue($grandtotalvotestate);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('O'.$i, function($cell) use($grandtotalnristate) {
                         $cell->setValue($grandtotalnristate);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });


                    $sheet->cell('P'.$i, function($cell) use($grandtotalvotestate,$grandestatetotal) {
                         $cell->setValue(round($grandtotalvotestate/$grandestatetotal*100,2));
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    

                    $sheet->cell('Q'.$i, function($cell) use($grandpostalrejectedtotal) {
                         $cell->setValue($grandpostalrejectedtotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('R'.$i, function($cell) use($grandvotesnotretrivedtotal,$grandduetoother) {
                         $cell->setValue($grandvotesnotretrivedtotal + $grandduetoother);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('S'.$i, function($cell) use($grandnotavotetotal) {
                         $cell->setValue($grandnotavotetotal);
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });

                    $sheet->cell('T'.$i, function($cell) use($grandtotalvotestate,$grandpostalrejectedtotal,$grandvotesnotretrivedtotal,$grandnotavotetotal,
                      $grandtestvote,$grandduetoother) {
                         $cell->setValue($grandtotalvotestate-($grandpostalrejectedtotal+$grandvotesnotretrivedtotal+$grandnotavotetotal+$grandduetoother));
                         $cell->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                    });
                    $sheet->cell('U'.$i, function($cell) use($grandtendedvotetotal) {
                         $cell->setValue($grandtendedvotetotal);
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
}  // end class
