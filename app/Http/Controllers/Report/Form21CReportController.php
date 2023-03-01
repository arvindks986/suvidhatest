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

class Form21CReportController extends Controller {

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
            $this->middleware('ro');

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
    public function getForm21C() {

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

                $pcno = $user->pc_no;
                $stateid = $user->st_code;
                
                $state_name = '';
                $pc_val = '';
                $pc_name = '';
                $npc_no = '';
                $pc_name1 = '';
                
                $can_district = '';
                $cand_state = '';
                
                $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $stateid)->first();
                if($state_name){
                    $state_name = $state_name->ST_NAME;
                }
                
                $pc_val = DB::table('m_pc')->select('PC_NO', 'PC_NAME','PC_TYPE')->where('ST_CODE', $stateid)->where('PC_NO', '=', $pcno)->first();
                if($pc_val){
					if($pc_val->PC_TYPE<>'GEN'){
						$pc_name = $pc_val->PC_NO.'-'.$pc_val->PC_NAME.' ('.$pc_val->PC_TYPE.')';
						$npc_no = ($pc_val->PC_NO < 100) ? '0' . $pc_val->PC_NO : $pc_val->PC_NO;
						$pc_name1 = $npc_no.' '.$pc_val->PC_NAME.'('.$pc_val->PC_TYPE.') ';
					}else{
						$pc_name = $pc_val->PC_NO.'-'.$pc_val->PC_NAME;
						$npc_no = ($pc_val->PC_NO < 100) ? '0' . $pc_val->PC_NO : $pc_val->PC_NO;
						$pc_name1 = $npc_no.' '.$pc_val->PC_NAME;
					}
                    
                }

                $get_win_candidate = DB::table('winning_leading_candidate as wincan')
                        ->leftJoin('candidate_personal_detail as can_perd', 'wincan.candidate_id', '=', 'can_perd.candidate_id')
                        ->select('wincan.lead_cand_name','wincan.status','can_perd.candidate_residence_districtno','can_perd.candidate_residence_stcode', 'wincan.lead_cand_party', 'can_perd.candidate_id', 'can_perd.candidate_residence_address')
                        ->where('wincan.st_code', '=', $stateid)->where('wincan.pc_no', '=', $pcno)
                        ->first();
                if($get_win_candidate){
                    if($get_win_candidate->status=='1'){
                        $can_district = DB::table('m_district')->select('DIST_NAME')->where('ST_CODE', '=', $get_win_candidate->candidate_residence_stcode)->where('DIST_NO', '=', $get_win_candidate->candidate_residence_districtno)->first();
                        if($can_district){
                            $can_district = $can_district->DIST_NAME;
                        }
                        $cand_state = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $get_win_candidate->candidate_residence_stcode)->first();
                        if($cand_state){
                            $cand_state = $cand_state->ST_NAME;
                        }
                    }
                }
				
				if($ele_details->ELECTION_TYPEID == '2'){
					return view('admin.countingReport.form21c.form21d-report', ['user_data' => $user, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'ele_details' => $ele_details, 'state' => $cand_state,'dist'=>$can_district,'pc_state'=>$state_name, 'pcname' => $pc_name, 'pc_name1' => $pc_name1, 'wincan' => $get_win_candidate]);
				}else{
				
				
                return view('admin.countingReport.form21c.form21c-report', ['user_data' => $user, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'ele_details' => $ele_details, 'state' => $cand_state,'dist'=>$can_district,'pc_state'=>$state_name, 'pcname' => $pc_name, 'pc_name1' => $pc_name1, 'wincan' => $get_win_candidate]);
				
				}
				
				
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getForm21CPdf() {
        if (Auth::check()) {
            try {
                $user = Auth::user();
				
				$d=$this->commonModel->getunewserbyuserid($user->id);
		        $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');
				
                $pcno = $user->pc_no;
                $stateid = $user->st_code;
                
                $state_name = '';
                $pc_val = '';
                $pc_name = '';
                $npc_no = '';
                $pc_name1 = '';
                
                $can_district = '';
                $cand_state = '';
                
                $state_name = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $stateid)->first();
                if($state_name){
                    $state_name = $state_name->ST_NAME;
                }
                
                $pc_val = DB::table('m_pc')->select('PC_NO', 'PC_NAME','PC_TYPE')->where('ST_CODE', $stateid)->where('PC_NO', '=', $pcno)->first();
                if($pc_val->PC_TYPE<>'GEN'){
						$pc_name = $pc_val->PC_NO.'-'.$pc_val->PC_NAME.' ('.$pc_val->PC_TYPE.')';
						$npc_no = ($pc_val->PC_NO < 10) ? '0' . $pc_val->PC_NO : $pc_val->PC_NO;
						$pc_name1 = $npc_no.' '.$pc_val->PC_NAME.'('.$pc_val->PC_TYPE.') ';
					}else{
						$pc_name = $pc_val->PC_NO.'-'.$pc_val->PC_NAME;
						$npc_no = ($pc_val->PC_NO < 10) ? '0' . $pc_val->PC_NO : $pc_val->PC_NO;
						$pc_name1 = $npc_no.' '.$pc_val->PC_NAME;
					}

                $get_win_candidate = DB::table('winning_leading_candidate as wincan')
                        ->leftJoin('candidate_personal_detail as can_perd', 'wincan.candidate_id', '=', 'can_perd.candidate_id')
                        ->select('wincan.lead_cand_name','wincan.status','can_perd.candidate_residence_districtno','can_perd.candidate_residence_stcode', 'wincan.lead_cand_party', 'can_perd.candidate_id', 'can_perd.candidate_residence_address')
                        ->where('wincan.st_code', '=', $stateid)->where('wincan.pc_no', '=', $pcno)
                        ->first();
                
                if($get_win_candidate){
                    //if($get_win_candidate->status=='1'){
                        $can_district = DB::table('m_district')->select('DIST_NAME')->where('ST_CODE', '=', $get_win_candidate->candidate_residence_stcode)->where('DIST_NO', '=', $get_win_candidate->candidate_residence_districtno)->first();
                        if($can_district){
                            $can_district = $can_district->DIST_NAME;
                        }
                        $cand_state = DB::table('m_state')->select('ST_NAME')->where('ST_CODE', $get_win_candidate->candidate_residence_stcode)->first();
                        if($cand_state){
                            $cand_state = $cand_state->ST_NAME;
                        }
                    //}
                }
                $date = $pc_name.'-'.time();
								
				if($ele_details->ELECTION_TYPEID == '2'){
					$pdf = PDF::loadView('admin.countingReport.form21c.form21d-report-pdf', ['user_data' => $user, 'pcname' => $pc_name,'pc_state'=>$state_name,'state' => $cand_state,'dist'=>$can_district, 'pc_name1' => $pc_name1, 'wincan' => $get_win_candidate]);
                return $pdf->download($date . '-form-21d' . '.' . 'pdf');
				}else{
					$pdf = PDF::loadView('admin.countingReport.form21c.form21c-report-pdf', ['user_data' => $user, 'pcname' => $pc_name,'pc_state'=>$state_name,'state' => $cand_state,'dist'=>$can_district, 'pc_name1' => $pc_name1, 'wincan' => $get_win_candidate]);
					return $pdf->download($date . '-form-21c' . '.' . 'pdf');
				}
				
				
                
				
				
				
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function getForm21CUpload() {
        $users = Session::get('admin_login_details');
        $user = Auth::user();
        $ele_details = array();
        if (Auth::check()) {
            try {
                $uid = $user->id;
                $ele_details = '';
                $check_finalize = '';
                $cand_finalize_ceo = 0;
                $cand_finalize_ro = 0;
                $d = $this->commonModel->getunewserbyuserid($user->id);
                $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
                if($ele_details){
                    $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
                    if ($check_finalize == '') {
                        $cand_finalize_ceo = 0;
                        $cand_finalize_ro = 0;
                    } else {
                        $cand_finalize_ceo = $check_finalize->finalize_by_ceo;
                        $cand_finalize_ro = $check_finalize->finalized_ac;
                    }   
                }else{
                        $cand_finalize_ceo = 0;
                        $cand_finalize_ro = 0;
                }
                  
                return view('admin.countingReport.form21c.form21c-upload', ['ele_details' => $ele_details, 'user_data' => $d, 'cand_finalize_ceo' => $cand_finalize_ceo, 'cand_finalize_ro' => $cand_finalize_ro, 'ele_details' => $ele_details]);
            } catch (Exception $ex) {
                return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
        } else {
            return redirect('/officer-login');
        }
    }

    function storeFile(Request $request) {
        $users = Session::get('admin_login_details');
        $user = Auth::user();

        if (Auth::check()) {
            $rules = ['form21' => 'required|max:2048|mimes:pdf'];

            $customMessages = [
                'required' => 'Please select file.',
                'max' => 'The file size is large use only 2 mb file.',
                'mimes' => 'Select only pdf file.',
            ];
            $this->validate($request, $rules, $customMessages);

            try {
                // Handle File Upload
                if ($request->hasFile('form21')) {
                    $filenameWithExt = $request->file('form21')->getClientOriginalName();
                    $extension = $request->file('form21')->getClientOriginalExtension();
                    if ($extension != 'pdf') {
                        session()->flash('emsg', 'Please select only pdf file.');
                        return redirect()->back();
                    }
                    $mime_type = $request->file('form21')->getClientMimeType();
                    if ($mime_type != 'application/pdf') {
                        session()->flash('emsg', 'Please select valid pdf file.');
                        return redirect()->back();
                    }
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                    //saving  data to database
                    $uid = $user->id;
                    $d = $this->commonModel->getunewserbyuserid($uid);
                    $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
                    $datetime = date('Y-m-d H:i:s');
                    $date = date('Y-m-d H:i:s');
                    
                    $fileNameToStore = $user->st_code . '_' . time() . '.' . $extension;
                    
                    if($ele_details){
                        //moving file to local storage
                        $file_path = '';

                        $file_path = '/uploads1/form' . $request->form_type . '/' . $ele_details->CONST_TYPE . '/' . date('Y') . '/' . $fileNameToStore;
                        
                        $check_exist = DB::table('counting_form21_detail')->select('id')->where('st_code', $user->st_code)->where('pc_no', '=', $user->pc_no)->where('form_type',$request->form_type)->get();
                        if(count($check_exist)>0){
                            DB::table('counting_form21_detail')->where('st_code', $user->st_code)->where('pc_no', $user->pc_no)->where('form_type',$request->form_type)
                            ->update([
                            'st_code' => $ele_details->ST_CODE, 'pc_no' => $user->pc_no, 'const_type' => $ele_details->CONST_TYPE,
                            'election_type_id' => $ele_details->ELECTION_TYPEID, 'election_id' => $ele_details->ELECTION_ID, 'form21_path' => $file_path, 'form_type' => $request->form_type,
                            'form21_uploaded_time' => $datetime,'added_update_at' => $date,'updated_at' => $datetime,
                            'updated_by' => $user->officername]);
                            session()->flash('smsg', 'Old File updated successfully.');
                        }else{
                            DB::table('counting_form21_detail')->insert([
                            'st_code' => $ele_details->ST_CODE, 'pc_no' => $user->pc_no, 'const_type' => $ele_details->CONST_TYPE,
                            'election_type_id' => $ele_details->ELECTION_TYPEID, 'election_id' => $ele_details->ELECTION_ID, 'form21_path' => $file_path, 'form_type' => $request->form_type,
                            'form21_uploaded_time' => $datetime,'added_update_at' => $date, 'created_at' => $datetime,
                            'created_by' => $user->officername
                            ]);
                            session()->flash('smsg', 'File uploaded successfully.');
                        }
                        
                        $request->file('form21')->move(public_path('/uploads1/form' . $request->form_type . '/' . $ele_details->CONST_TYPE . '/' . date('Y') . '/'), $fileNameToStore);
                    }else{
                        session()->flash('emsg', 'File not uploaded, please try again.');
                    }
                    return redirect()->back();
                } else {
                    session()->flash('emsg', 'Please select file.');
                    return redirect()->back();
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
