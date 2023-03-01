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
// use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\commonModel;
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
class VoterTypeWiseReportController extends Controller {

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
                default:
                    $this->middleware('eci');
            }
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
     * Voter type wise report
     */
    public function reportIndex() {

        $users = Session::get('admin_login_details');
        $user = Auth::user();
        //
        $pc_list = array();
        if (Auth::check()) {
            try {
                $uid = $user->id;
                //This code for pc level user start
                $ele_details = '';
                $check_finalize = '';
                $cand_finalize_ceo = '';
                $cand_finalize_ro = '';
                $list_state  = array();
                $pc_list = array();
                $list_party = array();
                if ($user->role_id == 18) {
                    $d = $this->commonModel->getunewserbyuserid($user->id);
                    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
                    $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                    if ($check_finalize == '') {
                        $cand_finalize_ceo = 0;
                        $cand_finalize_ro = 0;
                    } else {
                        $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                        $cand_finalize_ro = $check_finalize->finalized_ac;
                    }
                }//This code for pc level user start
                $list_party = array();
                if ($user->role_id == '7') {

                    $list_state = DB::table('m_state')
                    ->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_state.ST_CODE'],
        ])->where('m_election_details.CONST_TYPE','PC')
     ->where('election_status','1')
     ->select('m_state.ST_CODE', 'm_state.ST_NAME')->orderBy('m_state.ST_CODE', 'ASC')->get();

                } else if ($user->role_id == '4') {
                    $list_state = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->where('ST_CODE', '=', $user->st_code)->orderBy('ST_CODE', 'ASC')->get();
                    $pc_list = DB::table('m_pc')->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO'],
        ])->where('election_status','1')
                    ->select('m_pc.PC_NO', 'PC_NAME')->where('m_pc.ST_CODE', '=', $user->st_code)->orderBy('m_pc.PC_NO', 'ASC')->get();
                    $list_party = DB::table('counting_master_' . strtolower($user->st_code))->select('candidate_id', 'candidate_name', 'party_abbre')->groupBy('candidate_name')->orderBy('candidate_name', 'ASC')->get();
                } else if ($user->role_id == '18' || $user->role_id == '20') {
                    $list_state = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->where('ST_CODE', '=', $user->st_code)->orderBy('ST_CODE', 'ASC')->get();
                    $pc_list = DB::table('m_pc')->select('PC_NO', 'PC_NAME')->where('ST_CODE', '=', $user->st_code)->where('PC_NO', '=', $user->pc_no)->orderBy('PC_NO', 'ASC')->get();
                    $list_party = DB::table('counting_master_' . strtolower($user->st_code))->select('candidate_id', 'candidate_name', 'party_abbre')->where('pc_no', '=', $user->pc_no)->groupBy('candidate_name')->orderBy('candidate_name', 'ASC')->get();
                }

                return view('admin.countingReport.votetypereport.voter-type-wise-report', ['list_state' => $list_state, 'user_data' => $user, 'list_party' => $list_party, 'pc_list' => $pc_list, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'ele_details' => $ele_details]);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /**
     * Get all pc by state code
     */
    function getPcByState($state_code) {
        if (Auth::check()) {
            try {
                $pc_array = array();
                $party_array = array();
                $pc_list = DB::table('m_pc')->join('m_election_details',[
          ['m_election_details.ST_CODE', '=','m_pc.ST_CODE'],
          ['m_election_details.CONST_NO', '=','m_pc.PC_NO'],
        ])->where('election_status','1')
                ->select('m_pc.PC_NO', 'm_pc.PC_NAME')->where('m_pc.ST_CODE', '=', $state_code)->orderBy('m_pc.PC_NO', 'ASC')->get();
                if(count($pc_list)>0){
                    foreach ($pc_list as $dcode => $dval) {
                        $pc_array['id'][] = $dval->PC_NO;
                        $pc_array['val'][] = $dval->PC_NAME;
                    }
                }
                
                $st_tbl = 'counting_master_' . strtolower($state_code);
                $list_party = DB::table($st_tbl)->select('candidate_id', 'candidate_name', 'party_abbre')->groupBy('candidate_name')->orderBy('candidate_name', 'ASC')->get();
                if(count($list_party)>0){
                    foreach ($list_party as $k => $v) {
                        $party_array['id'][] = $v->candidate_id;
                        $party_array['val'][] = $v->candidate_name . ' ( ' . $v->party_abbre . ' )';
                    }
                }
                
                return json_encode(array("pc_arr" => $pc_array, "party_arr" => $party_array));
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /**
     * Get all pc by ac no
     */
    function getAcByPc($pcno, $state_code) {
        if (Auth::check()) {
            try {
                $array = array();
                $pc_list = DB::table('m_ac')->select('AC_NO', 'AC_NAME')->where('ST_CODE', '=', $state_code)->where('PC_NO', '=', $pcno)->orderBy('AC_NO', 'ASC')->get();
                if(count($pc_list)>0){
                    foreach ($pc_list as $dcode => $dval) {
                        $array['id'][] = $dval->AC_NO;
                        $array['val'][] = $dval->AC_NAME;
                    }
                }
                
                return json_encode($array);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /**
     * Get all party by pc no
     */
    function getPartyByPc($pcno, $state_code) {
        if (Auth::check()) {
            try {
                $array = array();
                $st_tbl = 'counting_master_' . strtolower($state_code);
                $pcno = explode(",", $pcno);
                $list_party = DB::table($st_tbl)->select('candidate_id', 'candidate_name', 'party_abbre')->whereIn('pc_no', $pcno)->groupBy('candidate_name')->orderBy('candidate_name', 'ASC')->get();
                if(count($list_party)>0){
                    foreach ($list_party as $dcode => $dval) {
                        $array['id'][] = $dval->candidate_id;
                        $array['val'][] = $dval->candidate_name . ' ( ' . $dval->party_abbre . ' )';
                    }
                }
                
                return json_encode($array);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /**
     * Get report by evm , postal ballet
     */
    /**
     * Get report by evm , postal ballet
     */
    function getReport(Request $request) {
        if (Auth::check()) {
            try {
                $array = array();
                $input = $request->all();
                
                $stateid = $request->stateid;
                $pcno = $request->pcno;
                $party_id = $request->party;
                $show_record = $request->show_record;
                $cand_profile = $request->cand_profile;

                $str = '';
                $template = '';
                $template_end = "</tbody></table></div>";
                $state_name = '';
                $pc_name = '';
                $where = '';
                if ($stateid != '' && $pcno != '' && $party_id != '') {
                    if($stateid=='all' && $party_id=='all' && $pcno=='all' && $show_record=='all'){
                        $where = ' ORDER BY st_name ASC';
                    }else if($stateid<>'all' && $pcno=='all' && $party_id=='all' && $show_record=='all'){
                        $where = " WHERE st_code='".$stateid."' ORDER BY pc_no ASC";
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id=='all' && $show_record=='all'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' ORDER BY st_name ASC";
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id<>'all' && $show_record=='all'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' AND candidate_id='".$party_id."' ORDER BY st_name ASC";
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id<>'all' && $show_record<>'all'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' AND candidate_id='".$party_id."' AND winning_status='winner' ORDER BY st_name ASC";
                    }else if($stateid=='all' && $pcno=='all' && $party_id=='all' && $show_record=='winner'){
                        $where = " WHERE  winning_status='winner' ORDER BY st_name ASC";
                    }else if($stateid<>'all' && $pcno=='all' && $party_id=='all' && $show_record=='winner'){
                        $where = " WHERE st_code='".$stateid."' AND winning_status='winner' ORDER BY st_name ASC";
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id=='all' && $show_record=='winner'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' AND winning_status='winner' ORDER BY st_name ASC";              
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id<>'all' && $show_record=='winner'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' AND candidate_id='".$party_id."' AND  winning_status='winner' ORDER BY st_name ASC";
                    }                  
                    
                    $sql = "SELECT * FROM (
                        SELECT p.st_code,m.party_abbre,s.st_name,p.pc_name,p.pc_no,m.`candidate_id`,`candidate_name`,`candidate_father_name`,`cand_email`,`cand_mobile`,
                        `cand_gender`,`cand_age`,party_name,IF(m.nom_id=w.`nomination_id`,'winner','NA')winning_status,`evm_vote`,`postal_vote`,`migrate_votes`,total_vote
                        FROM `counting_pcmaster` m
                        JOIN `candidate_personal_detail` q ON m.`candidate_id`=q.`candidate_id`
                        JOIN m_state s
                        ON m.st_code=s.st_code
                        JOIN m_pc p ON m.st_code=p.st_code AND m.pc_no=p.pc_no
                        LEFT JOIN `winning_leading_candidate` w ON m.st_code=w.`st_code` AND m.pc_no=w.pc_no AND m.nom_id=w.`nomination_id`
                        ORDER BY 1,2) AS dataset ".$where;

                    $cnt_list = DB::select($sql);

                    if (count($cnt_list) > 0) {
                        $i = 1;
                        foreach ($cnt_list as $dcode => $dval) {
                            if($cand_profile=='yes'){
                                $str .= '<tr>
                                <td>' . $i . '</td>
                                <td>' . $dval->st_name . '</td>
                                <td>' . $dval->pc_no . '-' . $dval->pc_name  . '</td>
                                <td>' . $dval->candidate_id . '</td>
                                <td>' . $dval->candidate_name. '</td>
                                <td>' . $dval->candidate_father_name . '</td>
                                <td>' . $dval->cand_mobile . '</td>
                                <td>' . $dval->cand_email . '</td>
                                <td>' . ucfirst($dval->cand_gender) . '</td>
                                <td>' . $dval->cand_age . '</td>
                                <td>' . $dval->party_name . '</td>
                                <td>' . ucfirst($dval->winning_status) . '</td>
                                <td>' . $dval->evm_vote . '</td>
                                <td>' . $dval->postal_vote . '</td>
                                <td>' . $dval->migrate_votes . '</td>    
                                <td>' . $dval->total_vote . '</td>
                            </tr>';
                            }else{
                            $str .= '<tr>
                                <td>' . $i . '</td>
                                <td>' . $dval->pc_no . '-' . $dval->pc_name  . '</td>
                                <td>' . $dval->candidate_name . '(' . $dval->party_abbre . ' )' . '</td>
                                <td>' . ucfirst($dval->winning_status) . '</td>
                                <td>' . $dval->evm_vote . '</td>
                                <td>' . $dval->postal_vote . '</td>
                                <td>' . $dval->migrate_votes . '</td>    
                                <td>' . $dval->total_vote . '</td>
                            </tr>';    
                            }
                            
                            $i++;
                        }
                    } else {
                        $str .= '<tr colspan="7"><td colspan="7" style="text-align:center;">No record found</td></tr>';
                    }
                    if($cand_profile=='yes'){
                        $template .= "<div class='table-responsive'>
                            <table id='example' class='table table-bordered' style='width:100%'><thead>
                            <th>SL NO.</th>
                            <th>State</th>
                            <th>PC Name</th>
                            <th>Candidate Id</th>				   
                            <th>Candidate Name</th>				                      
                            <th>Candidate Father Name</th>
                            <th>Candidate Mobile</th>
                            <th>Candidate Email</th>
                            <th>Candidate Gender</th>
                            <th>Candidate Age</th>				   
                            <th>Party Name</th>				                      				   
                            <th>Winning Status</th>				                      				   
                            <th>EVM Votes</th>				                      				   
                            <th>Postal Votes</th>				                      				   
                            <th>Migrant Votes</th>				                      				   
                            <th>Total Votes</th></thead><tbody>";
                    }else{
                        $template .= "<div class='table-responsive'>
                            <table id='example' class='table table-bordered' style='width:100%'><thead>
                            <th>SL NO.</th>
                            <th>PC Name</th>				   
                            <th>Candidate Name</th>				                      			   			                      				   
                            <th>Winning Status</th>				                      				   
                            <th>EVM Votes</th>				                      				   
                            <th>Postal Votes</th>				                      				   
                            <th>Migrant Votes</th>				                      				   
                            <th>Total Votes</th></thead><tbody>";
                    }
                    
                }


                return $template . $str . $template_end . '|||' . count($cnt_list);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /**
     * Get report excel
     */
    function getReportExcel(Request $request) {
        if (Auth::check()) {
            try {
                $input = $request->all();
                $stateid = $request->statevalue;
                $pcno = $request->pcvalue;
                $party_id = $request->partyvalue;
                $show_record = $request->show_record_value;
                $cand_profile = $request->cand_profile_value;
                $state_name = '';
                $where = '';

                $pc_name = array();
                if ($stateid != '' && $pcno != '' && $party_id != '') {
                    if($stateid=='all' && $party_id=='all' && $pcno=='all' && $show_record=='all'){
                        $where = ' ORDER BY st_name ASC';
                    }else if($stateid<>'all' && $pcno=='all' && $party_id=='all' && $show_record=='all'){
                        $where = " WHERE st_code='".$stateid."' ORDER BY st_name ASC";
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id=='all' && $show_record=='all'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' ORDER BY st_name ASC";
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id<>'all' && $show_record=='all'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' AND candidate_id='".$party_id."' ORDER BY st_name ASC";
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id<>'all' && $show_record<>'all'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' AND candidate_id='".$party_id."' AND winning_status='winner' ORDER BY st_name ASC";
                    }else if($stateid=='all' && $pcno=='all' && $party_id=='all' && $show_record=='winner'){
                        $where = " WHERE  winning_status='winner' ORDER BY st_name ASC";
                    }else if($stateid<>'all' && $pcno=='all' && $party_id=='all' && $show_record=='winner'){
                        $where = " WHERE st_code='".$stateid."' AND winning_status='winner' ORDER BY st_name ASC";
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id=='all' && $show_record=='winner'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' AND winning_status='winner' ORDER BY st_name ASC";              
                    }else if($stateid<>'all' && $pcno<>'all' && $party_id<>'all' && $show_record=='winner'){
                        $where = " WHERE st_code='".$stateid."' AND pc_no='".$pcno."' AND candidate_id='".$party_id."' AND  winning_status='winner' ORDER BY st_name ASC";
                    }       
                    
                    if($where==''){
                        $where = ' ORDER BY st_name ASC';
                    }
                    
                    $sql = "SELECT * FROM (
                        SELECT p.st_code,m.party_abbre,s.st_name,p.pc_name,p.pc_no,m.`candidate_id`,`candidate_name`,`candidate_father_name`,`cand_email`,`cand_mobile`,
                        `cand_gender`,`cand_age`,party_name,IF(m.nom_id=w.`nomination_id`,'winner','NA')winning_status,`evm_vote`,`postal_vote`,`migrate_votes`,total_vote
                        FROM `counting_pcmaster` m
                        JOIN `candidate_personal_detail` q ON m.`candidate_id`=q.`candidate_id`
                        JOIN m_state s
                        ON m.st_code=s.st_code
                        JOIN m_pc p ON m.st_code=p.st_code AND m.pc_no=p.pc_no
                        LEFT JOIN `winning_leading_candidate` w ON m.st_code=w.`st_code` AND m.pc_no=w.pc_no AND m.nom_id=w.`nomination_id`
                        ORDER BY 1,2) AS dataset ".$where;

                    $cnt_list = DB::select($sql);
                    
                    $evm_vote = 0;
                    $postal_vote = 0;
                    $migrate_votes = 0;
                    $total_votes = 0;
                    $mArray = array();
                    if (count($cnt_list) > 0) {
                        
                        
                        if($cand_profile=='yes'){
                            $i = 1;
                            $export_data[]=['SL.No','State Name','PC Name','Candidate Id','Candidate Name','Candidate Father Name','Candidate Email','Candidate Mobile','Candidate Gender','Candidate Age','Party Name','Winning Status','Evm Votes','Postal Votes','Migrant Votes','Total Votes'];
                            $headings[]=[];
                            foreach ($cnt_list as $k => $v) {
                                $evm_vote = $evm_vote + $v->evm_vote;
                                $postal_vote = $postal_vote + $v->postal_vote;
                                $migrate_votes = $migrate_votes + $v->migrate_votes;
                                $total_votes = $total_votes + $v->total_vote;

                                $export_data[] = [
                                        $i,
                                        $v->st_name,
                                        $v->pc_no . '-' . $v->pc_name,
                                        $v->candidate_id,
                                        $v->candidate_name,
                                        $v->candidate_father_name,
                                        $v->cand_email,
                                        $v->cand_mobile,
                                        ucfirst($v->cand_gender),
                                        $v->cand_age,
                                        $v->party_name,
                                        ucfirst($v->winning_status),
                                        $v->evm_vote,
                                        $v->postal_vote,
                                        $v->migrate_votes,
                                        $v->total_vote

                                   ];
                            //    $mArray[] = array(
                            //     'SL.No' => $i,'State Name' => $v->st_name,'PC Name' => $v->pc_no . '-' . $v->pc_name ,'Candidate Id' => $v->candidate_id,
                            //     'Candidate Name' => $v->candidate_name,'Candidate Father Name' => $v->candidate_father_name,
                            //     'Candidate Email' => $v->cand_email,'Candidate Mobile' => $v->cand_mobile,'Candidate Gender' => ucfirst($v->cand_gender),
                            //     'Candidate Age' => $v->cand_age,'Party Name' => $v->party_name,'Winning Status' => ucfirst($v->winning_status),
                            //     'Evm Votes' => "$v->evm_vote",'Postal Votes' => "$v->postal_vote",'Migrant Votes' => "$v->migrate_votes",'Total Votes' => "$v->total_vote"
                            //     ); 
                               $i++;
                            }
                            // $mArray[] = array(
                            // 'SL.No' => '','State Name' => '','PC Name' => '','Candidate Id' => '','Candidate Name' => '','Candidate Father Name' => '',
                            // 'Candidate Email' => '','Candidate Mobile' => '','Candidate Gender' => '','Candidate Age' => '',
                            // 'Party Name' => '','Winning Status' => 'Grand Total','Evm Votes' => "$evm_vote",'Postal Votes' => "$postal_vote",
                            // 'Migrant Votes' => "$migrate_votes",'Total Votes' => "$total_votes"
                            // );
                            
                        }else{
                            $i = 1;
                            $export_data[]=['SL.No','PC Name','Candidate Name','Winning Status','Evm Vote','Postal Vote','Migrant Votes','Total Votes'];
                            $headings[]=[];
                            foreach ($cnt_list as $k => $v) {
                                $evm_vote = $evm_vote + $v->evm_vote;
                                $postal_vote = $postal_vote + $v->postal_vote;
                                $migrate_votes = $migrate_votes + $v->migrate_votes;
                                $total_votes = $total_votes + $v->total_vote;
                                $export_data[] = [
                                    $i,
                                    $v->pc_no . '-' . $v->pc_name,
                                    $v->candidate_name . '(' . $v->party_abbre . ' )',
                                  
                                    ucfirst($v->winning_status),
                                    $v->evm_vote,
                                    $v->postal_vote,
                                    $v->migrate_votes,
                                    $v->total_vote

                               ];
                                // $mArray[] = array(
                                // 'SL.No' => $i,'PC Name' => $v->pc_no . '-' . $v->pc_name,
                                // 'Candidate Name' => $v->candidate_name . '(' . $v->party_abbre . ' )',
                                // 'Winning Status' => ucfirst($v->winning_status),'Evm Vote' => "$v->evm_vote",
                                // 'Postal Vote' => "$v->postal_vote",'Migrant Votes' => "$v->migrate_votes",'Total Votes' => "$v->total_vote"
                                // );
                                $i++;
                            }

                            // $mArray[] = array(
                            // 'SL.No' => '','PC Name' => '','Candidate Name' => '',
                            // 'Winning Status' => 'Grand Total','Evm Votes' => "$evm_vote",'Postal Votes' => "$postal_vote",
                            // 'Migrant Vote' => "$migrate_votes",'Total Votes' => "$total_votes"
                            // );
                            
                        }
                        
                        $data = json_decode(json_encode($mArray), true);
                        $date = date('Y-m-d');

                        $name_excel = $date .'-contested-candidate-report';
                        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 

                        // return Excel::create($date .'-contested-candidate-report', function($excel) use ($data) {
                        //             $excel->sheet('mySheet', function($sheet) use ($data) {
                        //                 $sheet->fromArray($data);
                        //             });
                        //         })->download('xls');
                    }
                }
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /**
     * Get report pdf
     */
    function getReportPdf(Request $request) {
        if (Auth::check()) {
            try {
                $input = $request->all();
                $stateid = $request->statevalue;
                $pcno = explode(",", $request->pcvalue);
                $party_id = explode(",", $request->partyvalue);
                $state_name = '';
                $pc_name = '';
                $user = Auth::user();
                if ($stateid != '' && $pcno != '' && $party_id != '') {
                    $cnt_list = DB::table('counting_pcmaster')
                            ->select('*')
                            ->whereIn('candidate_id', $party_id)
                            ->whereIn('pc_no', $pcno)
                            ->where('st_code', '=', $stateid)
                            ->orderBy('pc_no', 'ASC')
                            ->get();
                    $array = array();
                    if (count($cnt_list) > 0) {
                        foreach ($cnt_list as $k => $dval) {
                            $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $dval->st_code)->first();
                            if($state_name){
                                $state_name = $state_name->ST_NAME;
                            }
                            $pc_name = DB::table('m_pc')->select('PC_NO', 'PC_NAME')->where('ST_CODE', $dval->st_code)->where('PC_NO', '=', $dval->pc_no)->first();
                            if($pc_name){
                                $pc_name = $pc_name->PC_NO.'-'.$pc_name->PC_NAME;
                            }
                            $array[] = array('state_name' => $state_name, 'pc_name' => $pc_name, 'candidate_name' => $dval->candidate_name . '(' . $dval->party_abbre . ' )', 'evm_vote' => $dval->evm_vote, 'postal_vote' => $dval->postal_vote, 'total_vote' => $dval->total_vote);
                        }
                        $date = date('Y-m-d');

                        $pdf = PDF::loadView('admin.countingReport.votetypereport.voter-type-wise-report-pdf', ['array' => $array, 'user_data' => $user]);
                        return $pdf->download($date . '-candidate-wise-report.' . 'pdf');
                    }
                }
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    /*     * ***********************************  Form 21 Generate  ********************************************** */

    /**
     * Voter type wise report
     */
    public function getForm21() {
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $pc_list = array();
        if (Auth::check()) {
            try {
                $uid = $user->id;
                $ele_details = '';
                $check_finalize = '';
                $cand_finalize_ceo = '';
                $cand_finalize_ro = '';

                $d = $this->commonModel->getunewserbyuserid($uid);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
                $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                if ($check_finalize == '') {
                    $cand_finalize_ceo = 0;
                    $cand_finalize_ro = 0;
                } else {
                    $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                    $cand_finalize_ro = $check_finalize->finalized_ac;
                }

                $pcno = $user->pc_no;
                $stateid = $user->st_code;
                $cnt_list = array();
                $state_name = '';
                $pc_name = '';
                $winning_candidate = '';
                $totelctroll = 0;
                $totelservicectroll = 0;
                $validpolled = '';

                $cnt_list = DB::table('counting_pcmaster')
                        ->select('*')
                        ->where('pc_no', '=', $pcno)
                        ->where('st_code', '=', $stateid)
                        ->where('candidate_name', '<>', 'NOTA')
                        ->orderBy('id', 'ASC')
                        ->get();
						
				$total_nota = 0;
                $nota_count = DB::table('counting_pcmaster')
                            ->select('total_vote')
                            ->where('pc_no', '=', $pcno)
                            ->where('st_code', '=', $stateid)
                            ->where('candidate_name', '=', 'NOTA')
                            ->first();
       
                // if(count($nota_count) >0){
                   if($nota_count){
                        $total_nota = $nota_count->total_vote;
                   }
                // }

                $array = array();

                $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $stateid)->first();
                if($state_name){
                    $state_name = $state_name->ST_NAME;
                }
                $pc_name = DB::table('m_pc')->select('PC_NO', 'PC_NAME','PC_TYPE')->where('ST_CODE', $stateid)->where('PC_NO', '=', $pcno)->first();
                if($pc_name){
					if($pc_name->PC_TYPE <>'GEN'){
						$pc_name = $pc_name->PC_NO.'-'.$pc_name->PC_NAME.' ('.$pc_name->PC_TYPE.')';
					}else{
						$pc_name = $pc_name->PC_NO.'-'.$pc_name->PC_NAME;
					} 
                }

                $can_district='';$cand_state='';
                $totelctroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_total) AS totelectors"))->where('pc_no', '=', $pcno)->where('st_code', '=', $stateid)->where('year', '=', '2019')->first();
                if($totelctroll){
                    $totelctroll = $totelctroll->totelectors;
                }
                $totelservicectroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_service) AS totserviceelectors"))->where('pc_no', '=', $pcno)->where('st_code', '=', $stateid)->where('year', '=', '2019')->first();
                if($totelservicectroll){
                    $totelservicectroll = $totelservicectroll->totserviceelectors;
                }
                $totpolled_votes = 0;
                if (count($cnt_list) > 0) {
                    foreach ($cnt_list as $k => $dval) {
                        $array[] = array('candidate_name' => $dval->candidate_name, 'party_name' => $dval->party_name, 'total_vote' => $dval->total_vote, 'rejectedvote' => $dval->rejectedvote, 'tended_votes' => $dval->tended_votes, 'candidate_id' => $dval->candidate_id);
                        $totpolled_votes = $totpolled_votes + $dval->total_vote;
                    }
                    $date = date('Y-m-d');
                    //$array  = array_merge($array,$array1);

                    $validpolled = $totelctroll - $totpolled_votes;
                    $winning_candidate = DB::table('winning_leading_candidate as wincan')
                    ->leftJoin('candidate_personal_detail as can_perd', 'wincan.candidate_id', '=', 'can_perd.candidate_id')
                    ->select('wincan.lead_cand_name','wincan.status', 'wincan.lead_cand_party','can_perd.candidate_residence_districtno','can_perd.candidate_residence_stcode', 'can_perd.candidate_id', 'can_perd.candidate_residence_address')
                    ->where('wincan.st_code', '=', $stateid)->where('wincan.pc_no', '=', $pcno)
                    ->first();

                    if($winning_candidate){
                        if($winning_candidate->status=='1'){
                                $can_district = DB::table('m_district')->select('DIST_NAME')->where('ST_CODE', '=', $winning_candidate->candidate_residence_stcode)->where('DIST_NO', '=', $winning_candidate->candidate_residence_districtno)->first();
                                if($can_district){
                                    $can_district = $can_district->DIST_NAME;
                                }
                                $cand_state = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $winning_candidate->candidate_residence_stcode)->first();
                                if($cand_state){
                                    $cand_state = $cand_state->ST_NAME;
                                }
                        }
                    }
                }

                return view('admin.countingReport.votetypereport.form21-report', ['user_data' => $user,'pc_list' => $pc_list, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'ele_details' => $ele_details, 'array' => $array, 'user_data' => $user, 'tot_electrol' => $totelctroll, 'total_validpol' => $totpolled_votes,'state'=>$state_name,'candstate' => $cand_state,'dist'=>$can_district, 'pcname' => $pc_name, 'win_can' => $winning_candidate,'total_nota'=>$total_nota,'service_vote'=>$totelservicectroll]);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getForm21Pdf() {
        if (Auth::check()) {
            try {
                $user = Auth::user();
                $pcno = $user->pc_no;
                $stateid = $user->st_code;
                
                $cnt_list = array();
                $state_name = '';
                $pc_name = '';
                $winning_candidate = '';
                $totelctroll = 0;
                $totelservicectroll = 0;
                $validpolled = '';
                
                if ($stateid != '' && $pcno != '') {
                    $cnt_list = DB::table('counting_pcmaster')
                            ->select('*')
                            ->where('pc_no', '=', $pcno)
                            ->where('st_code', '=', $stateid)
                            ->where('candidate_name', '<>', 'NOTA')
                            ->orderBy('id', 'ASC')
                            ->get();
							
					$total_nota = 0;
					$nota_count = DB::table('counting_pcmaster')
								->select('total_vote')
								->where('pc_no', '=', $pcno)
								->where('st_code', '=', $stateid)
								->where('candidate_name', '=', 'NOTA')
								->first();
		   
					if($nota_count){
					   if($nota_count){
						   $total_nota = $nota_count->total_vote;
					   }
					}

                    $array = array();
                    
                    $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $stateid)->first();
                    if($state_name){
                        $state_name = $state_name->ST_NAME;
                    }
                    $pc_name = DB::table('m_pc')->select('PC_NO', 'PC_NAME','PC_TYPE')->where('ST_CODE', $stateid)->where('PC_NO', '=', $pcno)->first();
                    if($pc_name){
                        if($pc_name->PC_TYPE <>'GEN'){
							$pc_name = $pc_name->PC_NO.'-'.$pc_name->PC_NAME.' ('.$pc_name->PC_TYPE.')';
						}else{
							$pc_name = $pc_name->PC_NO.'-'.$pc_name->PC_NAME;
						} 
                    }

                    $totelctroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_total) AS totelectors"))->where('pc_no', '=', $pcno)->where('st_code', '=', $stateid)->where('year', '=', '2019')->first();
                    if($totelctroll){
                        $totelctroll = $totelctroll->totelectors;
                    }
                    
                    $totelservicectroll = DB::table('electors_cdac')->select(DB::raw("SUM(electors_service) AS totserviceelectors"))->where('pc_no', '=', $pcno)->where('st_code', '=', $stateid)->where('year', '=', '2019')->first();
                    if($totelservicectroll){
                        $totelservicectroll = $totelservicectroll->totserviceelectors;
                    }
                    
                    $totpolled_votes = 0;
                    $can_district='';$cand_state='';
                    if (count($cnt_list) > 0) {
                        foreach ($cnt_list as $k => $dval) {
                            $array[] = array('candidate_name' => $dval->candidate_name, 'party_name' => $dval->party_name, 'total_vote' => $dval->total_vote, 'rejectedvote' => $dval->rejectedvote, 'tended_votes' => $dval->tended_votes, 'candidate_id' => $dval->candidate_id);
                            $totpolled_votes = $totpolled_votes + $dval->total_vote;
                        }
                        $date = date('Y-m-d');

                        $validpolled = $totelctroll - $totpolled_votes;

                        $winning_candidate = DB::table('winning_leading_candidate as wincan')
                        ->leftJoin('candidate_personal_detail as can_perd', 'wincan.candidate_id', '=', 'can_perd.candidate_id')
                        ->select('wincan.lead_cand_name','wincan.status', 'wincan.lead_cand_party', 'can_perd.candidate_id','can_perd.candidate_residence_districtno','can_perd.candidate_residence_stcode', 'can_perd.candidate_residence_address')
                        ->where('wincan.st_code', '=', $stateid)->where('wincan.pc_no', '=', $pcno)
                        ->first();

                        if($winning_candidate){
                            //if($winning_candidate->status=='1'){
                                    $can_district = DB::table('m_district')->select('DIST_NAME')->where('ST_CODE', '=', $winning_candidate->candidate_residence_stcode)->where('DIST_NO', '=', $winning_candidate->candidate_residence_districtno)->first();
                                    if($can_district){
                                        $can_district = $can_district->DIST_NAME;
                                    }
                                    $cand_state = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $winning_candidate->candidate_residence_stcode)->first();
                                    if($cand_state){
                                        $cand_state = $cand_state->ST_NAME;
                                    }
                            //}
                        }
                        //echo "<pre>";print_r($winning_candidate);die;
                        $pdf = PDF::loadView('admin.countingReport.votetypereport.form21-report-pdf', ['array' => $array, 'user_data' => $user, 'tot_electrol' => $totelctroll, 'total_validpol' => $totpolled_votes, 'pcname' => $pc_name,'state'=>$state_name,'candstate' => $cand_state,'dist'=>$can_district, 'win_can' => $winning_candidate,'total_nota'=>$total_nota,'service_vote'=>$totelservicectroll]);
                        return $pdf->download($date . '-form-21' . $pc_name . '.' . 'pdf');
                    }
                }
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

}

// end class
