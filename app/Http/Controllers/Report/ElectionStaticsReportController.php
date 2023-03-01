<?php

namespace App\Http\Controllers\Report;

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
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\commonModel;
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;

date_default_timezone_set('Asia/Kolkata');

class ElectionStaticsReportController extends Controller {
    //USING TRAIT FOR COMMON FUNCTIONS

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware(function (Request $request, $next) {
            if (!\Auth::check()) {
                return redirect('login')->with(Auth::logout());
            }
            $user = Auth::user();
            $this->middleware('eci');

            return $next($request);
        });

        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected function guard() {
        return Auth::guard();
    }

    /**
     * Form 21C
     */
    function getElectionStatics() {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $record = array();
                $sql = "SELECT a.st_name,b.electors_total,b.voter_male,b.voter_female,b.voter_other,b.total_voters,a.evm_vote,a.postal_vote,a.migrate_votes,total_vote AS total_actual_votes FROM
                    (SELECT st_name,SUM(evm_vote)evm_vote,SUM(postal_vote)postal_vote,SUM(migrate_votes)migrate_votes,SUM(`total_vote`) total_vote
                    FROM `counting_pcmaster` m
                    JOIN m_state s ON m.st_code=s.st_code
                    GROUP BY 1 ORDER BY 1)a JOIN
                    (SELECT st_name,SUM(e.electors_total)electors_total,SUM(e.total_male)voter_male,SUM(e.total_female)voter_female,SUM(e.total_other)voter_other,SUM(e.total) total_voters
                    FROM `pd_scheduledetail` e
                    JOIN m_state s ON e.st_code=s.st_code
                    GROUP BY 1 ORDER BY 1) b ON a.st_name=b.st_name";

                $record = DB::select($sql);

                return view('admin.countingReport.electionstatics.electionStatics', ['record' => $record, 'user_data' => $user]);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
        function getElectionStaticsPdf() {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $record = array();
                $sql = "SELECT a.st_name,b.electors_total,b.voter_male,b.voter_female,b.voter_other,b.total_voters,a.evm_vote,a.postal_vote,a.migrate_votes,total_vote AS total_actual_votes FROM
                    (SELECT st_name,SUM(evm_vote)evm_vote,SUM(postal_vote)postal_vote,SUM(migrate_votes)migrate_votes,SUM(`total_vote`) total_vote
                    FROM `counting_pcmaster` m
                    JOIN m_state s ON m.st_code=s.st_code
                    GROUP BY 1 ORDER BY 1)a JOIN
                    (SELECT st_name,SUM(e.electors_total)electors_total,SUM(e.total_male)voter_male,SUM(e.total_female)voter_female,SUM(e.total_other)voter_other,SUM(e.total) total_voters
                    FROM `pd_scheduledetail` e
                    JOIN m_state s ON e.st_code=s.st_code
                    GROUP BY 1 ORDER BY 1) b ON a.st_name=b.st_name";

                $record = DB::select($sql);
                $date = date('Y-m-d');
                //echo "<pre>";print_r($record);die;
                $pdf = PDF::loadView('admin.countingReport.electionstatics.electionStaticsPdf', ['record' => $record, 'user_data' => $user]);
                return $pdf->download($date . '-election-statistics-report.' . 'pdf');
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getElectionStaticsExcel() {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $record = array();
                $sql = "SELECT a.st_name,b.electors_total,b.voter_male,b.voter_female,b.voter_other,b.total_voters,a.evm_vote,a.postal_vote,a.migrate_votes,total_vote AS total_actual_votes FROM
                    (SELECT st_name,SUM(evm_vote)evm_vote,SUM(postal_vote)postal_vote,SUM(migrate_votes)migrate_votes,SUM(`total_vote`) total_vote
                    FROM `counting_pcmaster` m
                    JOIN m_state s ON m.st_code=s.st_code
                    GROUP BY 1 ORDER BY 1)a JOIN
                    (SELECT st_name,SUM(e.electors_total)electors_total,SUM(e.total_male)voter_male,SUM(e.total_female)voter_female,SUM(e.total_other)voter_other,SUM(e.total) total_voters
                    FROM `pd_scheduledetail` e
                    JOIN m_state s ON e.st_code=s.st_code
                    GROUP BY 1 ORDER BY 1) b ON a.st_name=b.st_name";

                $record = DB::select($sql);
                $electors_total = 0;
                $voter_male = 0;
                $voter_female = 0;
                $voter_other = 0;
                $total_voters = 0;
                $evm_vote = 0;
                $postal_vote = 0;
                $migrate_votes = 0;
                $total_actual_votes = 0;
                if (count($record) > 0) {
                    $i = 1;
                    foreach ($record as $k => $v) {
                        $electors_total = $electors_total + $v->electors_total;
                        $voter_male = $voter_male + $v->voter_male;
                        $voter_female = $voter_female + $v->voter_female;
                        $voter_other = $voter_other + $v->voter_other;
                        $total_voters = $total_voters + $v->total_voters;
                        $evm_vote = $evm_vote + $v->evm_vote;
                        $postal_vote = $postal_vote + $v->postal_vote;
                        $migrate_votes = $migrate_votes + $v->migrate_votes;
                        $total_actual_votes = $total_actual_votes + $v->total_actual_votes;
                        $mArray[] = array(
                            'SL.No' => $i,
                            'State Name' => $v->st_name,
                            'Total Electors' => "$v->electors_total",
                            'Total Male Voter' => "$v->voter_male",
                            'Total Female Voter' => "$v->voter_female",
                            'Total Other Voter' => "$v->voter_other",
                            'Total Voters' => "$v->total_voters",
                            'Evm Vote' => "$v->evm_vote",
                            'Postal Vote' => "$v->postal_vote",
                            'Migrant Vote' => "$v->migrate_votes",
                            'Total Actual Votes' => "$v->total_actual_votes"
                        );
                        $i++;
                    }
                    $mArray[] = array(
                        'SL.No' => '',
                        'State Name' => 'Grand Total',
                        'Total Electors' => "$electors_total",
                        'Total Male Voter' => "$voter_male",
                        'Total Female Voter' => "$voter_female",
                        'Total Other Voter' => "$voter_other",
                        'Total Voters' => "$total_voters",
                        'Evm Vote' => "$evm_vote",
                        'Postal Vote' => "$postal_vote",
                        'Migrant Vote' => "$migrate_votes",
                        'Total Actual Votes' => "$total_actual_votes"
                    );

                    $data = json_decode(json_encode($mArray), true);
                    $date = date('Y-m-d');
                    return Excel::create($date . '-election-statistics-report', function($excel) use ($data) {
                                $excel->sheet('mySheet', function($sheet) use ($data) {
                                    $sheet->fromArray($data);
                                });
                            })->download('xls');
                }
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getCandidatePersonalDetais() {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            $all = 'selected';
            $win = '';
            $los = '';
            try {
                $record = array();
                $sql = "SELECT s.st_name,p.pc_name,m.`candidate_id`,`candidate_name`,`candidate_father_name`,`cand_email`,`cand_mobile`,
                        `cand_gender`,`cand_age`,party_name,IF(m.nom_id=w.`nomination_id`,'winner','loser')winning_status,`evm_vote`,`postal_vote`,`migrate_votes`,total_vote
                        FROM `counting_pcmaster` m
                        JOIN `candidate_personal_detail` q ON m.`candidate_id`=q.`candidate_id`
                        JOIN m_state s
                        ON m.st_code=s.st_code
                        JOIN m_pc p ON m.st_code=p.st_code AND m.pc_no=p.pc_no
                        LEFT JOIN `winning_leading_candidate` w ON m.st_code=w.`st_code` AND m.pc_no=w.pc_no AND m.nom_id=w.`nomination_id`
                        ORDER BY 1,2";

                $record = DB::select($sql);

                return view('admin.countingReport.electionstatics.personalDetails', ['record' => $record, 'user_data' => $user,'all'=>$all,'win'=>$win,'los'=>$los,'win_status'=>'all']);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
    function getCandidatePersonalDetaisFilter($win_status) {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $all = '';
                $win = '';
                $los = '';
                $record = array();
                if(isset($win_status) && $win_status <>'all'){
                    $sql = "SELECT * FROM (
                        SELECT s.st_name,p.pc_name,m.`candidate_id`,`candidate_name`,`candidate_father_name`,`cand_email`,`cand_mobile`,
                        `cand_gender`,`cand_age`,party_name,IF(m.nom_id=w.`nomination_id`,'winner','loser')winning_status,`evm_vote`,`postal_vote`,`migrate_votes`,total_vote
                        FROM `counting_pcmaster` m
                        JOIN `candidate_personal_detail` q ON m.`candidate_id`=q.`candidate_id`
                        JOIN m_state s
                        ON m.st_code=s.st_code
                        JOIN m_pc p ON m.st_code=p.st_code AND m.pc_no=p.pc_no
                        LEFT JOIN `winning_leading_candidate` w ON m.st_code=w.`st_code` AND m.pc_no=w.pc_no AND m.nom_id=w.`nomination_id`
                        ORDER BY 1,2) AS dataset WHERE winning_status='$win_status' ORDER BY st_name ASC";
                }else{
                        $sql = "SELECT s.st_name,p.pc_name,m.`candidate_id`,`candidate_name`,`candidate_father_name`,`cand_email`,`cand_mobile`,
                        `cand_gender`,`cand_age`,party_name,IF(m.nom_id=w.`nomination_id`,'winner','loser')winning_status,`evm_vote`,`postal_vote`,`migrate_votes`,total_vote
                        FROM `counting_pcmaster` m
                        JOIN `candidate_personal_detail` q ON m.`candidate_id`=q.`candidate_id`
                        JOIN m_state s
                        ON m.st_code=s.st_code
                        JOIN m_pc p ON m.st_code=p.st_code AND m.pc_no=p.pc_no
                        LEFT JOIN `winning_leading_candidate` w ON m.st_code=w.`st_code` AND m.pc_no=w.pc_no AND m.nom_id=w.`nomination_id`
                        ORDER BY 1,2";
                        
                }
                $record = DB::select($sql);
                
                if($win_status=='winner'){
                    $win = 'selected';
                }
                else if($win_status=='loser'){
                    $los = 'selected';
                }else{
                    $all = 'selected';
                }

                return view('admin.countingReport.electionstatics.personalDetails', ['record' => $record, 'user_data' => $user,'all'=>$all,'win'=>$win,'los'=>$los,'win_status'=>$win_status]);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getCandidatePersonalDetaisExcel() {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $record = array();
                $sql = "SELECT st_name,pc_name,m.`candidate_id`,`candidate_name`,`candidate_father_name`,`cand_email`,`cand_mobile`,
                        `cand_gender`,`cand_age`,party_name,`evm_vote`,`postal_vote`,`migrate_votes`,total_vote
                        FROM `counting_pcmaster` m
                        JOIN `candidate_personal_detail` q ON m.`candidate_id`=q.`candidate_id`
                        JOIN m_state s
                        ON m.st_code=s.st_code
                        JOIN m_pc p ON m.st_code=p.st_code AND m.pc_no=p.pc_no ORDER BY 1,2";

                $record = DB::select($sql);

                $evm_vote = 0;
                $postal_vote = 0;
                $migrate_votes = 0;
                $total_votes = 0;
                $mArray = array();
                if (count($record) > 0) {
                    $i = 1;
                    foreach ($record as $k => $v) {
                        $evm_vote = $evm_vote + $v->evm_vote;
                        $postal_vote = $postal_vote + $v->postal_vote;
                        $migrate_votes = $migrate_votes + $v->migrate_votes;
                        $total_votes = $total_votes + $v->total_vote;

                        $mArray[] = array(
                            'SL.No' => $i,
                            'State Name' => $v->st_name,
                            'PC Name' => $v->pc_name,
                            'Candidate Id' => $v->candidate_id,
                            'Candidate Name' => $v->candidate_name,
                            'Candidate Father Name' => $v->candidate_father_name,
                            'Candidate Email' => $v->cand_email,
                            'Candidate Mobile' => $v->cand_mobile,
                            'Candidate Gender' => $v->cand_gender,
                            'Candidate Age' => $v->cand_age,
                            'Party Name' => $v->party_name,
                            'Evm Vote' => "$v->evm_vote",
                            'Postal Vote' => "$v->postal_vote",
                            'Migrant Vote' => "$v->migrate_votes",
                            'Total Votes' => "$v->total_vote"
                        );
                    }
                    $mArray[] = array(
                            'SL.No' => '',
                            'State Name' => '',
                            'PC Name' => '',
                            'Candidate Id' => '',
                            'Candidate Name' => '',
                            'Candidate Father Name' => '',
                            'Candidate Email' => '',
                            'Candidate Mobile' => '',
                            'Candidate Gender' => '',
                            'Candidate Age' => '',
                            'Party Name' => 'Grand Total',
                            'Evm Vote' => "$evm_vote",
                            'Postal Vote' => "$postal_vote",
                            'Migrant Vote' => "$migrate_votes",
                            'Total Votes' => "$total_votes"
                        );
                }
                $data = json_decode(json_encode($mArray), true);
                $date = date('Y-m-d');
                return Excel::create($date . '-candidate-profile-report', function($excel) use ($data) {
                            $excel->sheet('mySheet', function($sheet) use ($data) {
                                $sheet->fromArray($data);
                            });
                        })->download('xls');
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
    function getCandidatePersonalDetaisExcelFilter($win_status) {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $record = array();
                if(isset($win_status) && $win_status <>'all'){
                    $sql = "SELECT * FROM (
                        SELECT s.st_name,p.pc_name,m.`candidate_id`,`candidate_name`,`candidate_father_name`,`cand_email`,`cand_mobile`,
                        `cand_gender`,`cand_age`,party_name,IF(m.nom_id=w.`nomination_id`,'winner','loser')winning_status,`evm_vote`,`postal_vote`,`migrate_votes`,total_vote
                        FROM `counting_pcmaster` m
                        JOIN `candidate_personal_detail` q ON m.`candidate_id`=q.`candidate_id`
                        JOIN m_state s
                        ON m.st_code=s.st_code
                        JOIN m_pc p ON m.st_code=p.st_code AND m.pc_no=p.pc_no
                        LEFT JOIN `winning_leading_candidate` w ON m.st_code=w.`st_code` AND m.pc_no=w.pc_no AND m.nom_id=w.`nomination_id`
                        ORDER BY 1,2) AS dataset WHERE winning_status='$win_status' ORDER BY st_name ASC";
                }else{
                        $sql = "SELECT s.st_name,p.pc_name,m.`candidate_id`,`candidate_name`,`candidate_father_name`,`cand_email`,`cand_mobile`,
                        `cand_gender`,`cand_age`,party_name,IF(m.nom_id=w.`nomination_id`,'winner','loser')winning_status,`evm_vote`,`postal_vote`,`migrate_votes`,total_vote
                        FROM `counting_pcmaster` m
                        JOIN `candidate_personal_detail` q ON m.`candidate_id`=q.`candidate_id`
                        JOIN m_state s
                        ON m.st_code=s.st_code
                        JOIN m_pc p ON m.st_code=p.st_code AND m.pc_no=p.pc_no
                        LEFT JOIN `winning_leading_candidate` w ON m.st_code=w.`st_code` AND m.pc_no=w.pc_no AND m.nom_id=w.`nomination_id`
                        ORDER BY 1,2";
                        
                }
                $record = DB::select($sql);

                $evm_vote = 0;
                $postal_vote = 0;
                $migrate_votes = 0;
                $total_votes = 0;
                $mArray = array();
                if (count($record) > 0) {
                    $i = 1;
                    foreach ($record as $k => $v) {
                        $evm_vote = $evm_vote + $v->evm_vote;
                        $postal_vote = $postal_vote + $v->postal_vote;
                        $migrate_votes = $migrate_votes + $v->migrate_votes;
                        $total_votes = $total_votes + $v->total_vote;

                        $mArray[] = array(
                            'SL.No' => $i,
                            'State Name' => $v->st_name,
                            'PC Name' => $v->pc_name,
                            'Candidate Id' => $v->candidate_id,
                            'Candidate Name' => $v->candidate_name,
                            'Candidate Father Name' => $v->candidate_father_name,
                            'Candidate Email' => $v->cand_email,
                            'Candidate Mobile' => $v->cand_mobile,
                            'Candidate Gender' => $v->cand_gender,
                            'Candidate Age' => $v->cand_age,
                            'Party Name' => $v->party_name,
                            'Winning Status' => $v->winning_status,
                            'Evm Vote' => "$v->evm_vote",
                            'Postal Vote' => "$v->postal_vote",
                            'Migrant Vote' => "$v->migrate_votes",
                            'Total Votes' => "$v->total_vote"
                        );
                    }
                    $mArray[] = array(
                            'SL.No' => '',
                            'State Name' => '',
                            'PC Name' => '',
                            'Candidate Id' => '',
                            'Candidate Name' => '',
                            'Candidate Father Name' => '',
                            'Candidate Email' => '',
                            'Candidate Mobile' => '',
                            'Candidate Gender' => '',
                            'Candidate Age' => '',
                            'Party Name' => '',
                            'Winning Status' => 'Grand Total',
                            'Evm Vote' => "$evm_vote",
                            'Postal Vote' => "$postal_vote",
                            'Migrant Vote' => "$migrate_votes",
                            'Total Votes' => "$total_votes"
                        );
                }
                $data = json_decode(json_encode($mArray), true);
                $date = date('Y-m-d');
                return Excel::create($date .'-'.$win_status.'-candidate-profile-report', function($excel) use ($data) {
                            $excel->sheet('mySheet', function($sheet) use ($data) {
                                $sheet->fromArray($data);
                            });
                        })->download('xls');
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }



}

// end class
