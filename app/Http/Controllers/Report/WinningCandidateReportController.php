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

class WinningCandidateReportController extends Controller {

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
    public function getPdfView() {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
       

        if (Auth::check()) {
            try {
                
                $get_win_candidate = DB::table('winning_leading_candidate')
                        ->select('st_name','st_hname','pc_no','pc_name','pc_hname','lead_cand_name','lead_cand_hname','lead_cand_party','lead_cand_hparty')
                        ->get()->toArray();
                
                $newArr = array();
                if(count($get_win_candidate) >0){
                    foreach($get_win_candidate as $k=>$v){
                        $newArr[$k]['st_name'] = $v->st_name;
                        $newArr[$k]['st_hname'] = $v->st_hname;
                        $newArr[$k]['pc_name'] = $v->pc_no.'- '.$v->pc_name;
                        $newArr[$k]['pc_hname'] = $v->pc_no.'- '.$v->pc_hname;
                        $newArr[$k]['lead_cand_name'] = $v->lead_cand_name;
                        $newArr[$k]['lead_cand_hname'] = $v->lead_cand_hname;
                        $newArr[$k]['lead_cand_party'] = $v->lead_cand_party;
                        $newArr[$k]['lead_cand_hparty'] = $v->lead_cand_hparty;
                    }
                }
                $english_arr = array();        
                $hindi_arr = array();   
                
                $hindi_arr = $this->group_by($newArr,'st_hname');
                $english_arr = $this->group_by($newArr,'st_name');
                
              
       
                return view('admin.countingReport.result.winning-candidate', ['hindiarr' => $hindi_arr,'engarr'=>$english_arr,'user_data'=>$user]);    
                
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    function group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
    }

    function getDownloadPdf() {
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {
                $get_win_candidate = DB::table('winning_leading_candidate')
                        ->select('st_name','st_hname','pc_no','pc_name','pc_hname','lead_cand_name','lead_cand_hname','lead_cand_party','lead_cand_hparty')
                        ->get()->toArray();
                
                $newArr = array();
                if(count($get_win_candidate) >0){
                    foreach($get_win_candidate as $k=>$v){
                        $newArr[$k]['st_name'] = $v->st_name;
                        $newArr[$k]['st_hname'] = $v->st_hname;
                        $newArr[$k]['pc_name'] = $v->pc_no.'- '.$v->pc_name;
                        $newArr[$k]['pc_hname'] = $v->pc_no.'- '.$v->pc_hname;
                        $newArr[$k]['lead_cand_name'] = $v->lead_cand_name;
                        $newArr[$k]['lead_cand_hname'] = $v->lead_cand_hname;
                        $newArr[$k]['lead_cand_party'] = $v->lead_cand_party;
                        $newArr[$k]['lead_cand_hparty'] = $v->lead_cand_hparty;
                    }
                }
                $english_arr = array();        
                $hindi_arr = array();   
                
                $hindi_arr = $this->group_by($newArr,'st_hname');
                $english_arr = $this->group_by($newArr,'st_name');
                $date = date('Y-m-d');
                $pdf = PDF::loadView('admin.countingReport.result.winning-candidate-pdf', ['hindiarr' => $hindi_arr,'engarr'=>$english_arr,'user_data'=>$user]);
                return $pdf->download($date . '-winning-candidates-details' . '.' . 'pdf');
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    function getNationalPerformance(){
        
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {

                $sql = "SELECT a.partyname,contested,won,vote AS evm_vote,total_vote FROM
                        (SELECT partyname,COUNT(DISTINCT p.candidate_id)contested,COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
                        JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
                        JOIN m_party q ON p.party_id=q.ccode
                        WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
                        (SELECT partyname,SUM(evm_vote)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
                        JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
                        ON a.partyname=b.partyname";
            
            $record = DB::select($sql);
            
            $totelctroll = 0;
            $totelservicectroll = 0;
            $final_electrol = 
            
            $totelctroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_total) AS totelectors"))->where('year', '=', '2019')->first();
            if($totelctroll){
                $totelctroll = $totelctroll->totelectors;
            }
            $totelservicectroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_service) AS totserviceelectors"))->where('year', '=', '2019')->first();
            if($totelservicectroll){
                $totelservicectroll = $totelservicectroll->totserviceelectors;
            }
            
            $final_electrol = $totelctroll + $totelservicectroll;
            $tot_valid_polled_votes = 0;
            $total_vote = 0;
            if(count($record)>0){
                foreach($record as $k=>$v){
                    $total_vote = $total_vote + $v->total_vote;
                }
            }
            $tot_valid_polled_votes = $total_vote;
            //echo $tot_valid_polled_votes;die;
            //echo "<pre>";print_r($record);die;
            return view('admin.countingReport.result.national-parties', ['record' => $record,'user_data'=>$user,'tot_electors'=>$final_electrol,'tot_valid_polled_votes'=>$tot_valid_polled_votes]); 
                
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }
    function getNationalPerformancePdf(){
        
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        if (Auth::check()) {
            try {

                $sql = "SELECT a.partyname,contested,won,vote AS evm_vote,total_vote FROM
                        (SELECT partyname,COUNT(DISTINCT p.candidate_id)contested,COUNT(DISTINCT w.`candidate_id`)won FROM `counting_pcmaster` p
                        JOIN `winning_leading_candidate` w ON p.party_id=w.`lead_cand_partyid`
                        JOIN m_party q ON p.party_id=q.ccode
                        WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)a JOIN
                        (SELECT partyname,SUM(evm_vote)vote,SUM(total_vote)total_vote FROM `counting_pcmaster` m
                        JOIN m_party q ON m.party_id=q.ccode WHERE party_id IN (140,369,498,544,547,742,1142) GROUP BY 1)b
                        ON a.partyname=b.partyname";
            
            $record = DB::select($sql);
            
            $totelctroll = 0;
            $totelservicectroll = 0;
            $final_electrol = 
            
            $totelctroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_total) AS totelectors"))->where('year', '=', '2019')->first();
            if($totelctroll){
                $totelctroll = $totelctroll->totelectors;
            }
            $totelservicectroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_service) AS totserviceelectors"))->where('year', '=', '2019')->first();
            if($totelservicectroll){
                $totelservicectroll = $totelservicectroll->totserviceelectors;
            }
            
            $final_electrol = $totelctroll + $totelservicectroll;
            $tot_valid_polled_votes = 0;
            $total_vote = 0;
            if(count($record)>0){
                foreach($record as $k=>$v){
                    $total_vote = $total_vote + $v->total_vote;
                }
            }
            $tot_valid_polled_votes = $total_vote;

            //return view('admin.countingReport.result.national-parties', ['record' => $record,'user_data'=>$user,'tot_electors'=>$final_electrol,'tot_valid_polled_votes'=>$tot_valid_polled_votes]); 
            $date = date('Y-m-d');
            $pdf = PDF::loadView('admin.countingReport.result.national-parties-pdf', ['record' => $record,'user_data'=>$user,'tot_electors'=>$final_electrol,'tot_valid_polled_votes'=>$tot_valid_polled_votes]);
            return $pdf->download($date . '-national-parties-performance' . '.' . 'pdf');    
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }


}

// end class
