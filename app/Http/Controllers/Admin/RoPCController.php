<?php

namespace App\Http\Controllers\Admin;

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
use MPDF;
use App\commonModel;
use App\adminmodel\CandidateModel;
use App\adminmodel\PartyMaster;
use App\adminmodel\CandidateNomination;
use App\Helpers\SmsgatewayHelper;
use App\adminmodel\ROPCModel;
use App\Classes\xssClean;
use App\adminmodel\SymbolMaster;
//use Spatie\MixedContentScanner\MixedContentScanner;
use Illuminate\Support\Facades\Crypt;


class RoPCController extends Controller
{
	//
	public function __construct()
	{

		$this->middleware('adminsession');
		$this->middleware(['auth:admin', 'auth']);
		$this->middleware('ro');
		$this->middleware('clean_url');
		$this->commonModel = new commonModel();
		$this->CandidateModel = new CandidateModel();
		$this->romodel = new ROPCModel();
		$this->xssClean = new xssClean;
		$this->sym = new SymbolMaster();
		if (!Auth::check()) {
			return redirect('/officer-login');
		}
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	protected function guard()
	{
		return Auth::guard('admin');
	}

	public function index()
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');


			if (!is_null($ele_details)) {
				$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);

				
				$seched = getschedulebyid($ele_details->ScheduleID);
			}


			$data['user_data'] = $d;
			$data['ele_details'] = $ele_details;
			$data['sched'] = $seched;
			$data['check_finalize'] = $check_finalize;
			//return view('admin.pc.ro.nomination.dashboard', ['user_data' => $d,'cand_finalize_ceo' =>$cand_finalize_ceo,'cand_finalize_ro' => $cand_finalize_ro, 'sched'=>$seched,'ele_details'=>$ele_details]);	
			return view('admin.pc.ro.dashboard', $data);
		} else {
			return redirect('/officer-login');
		}
	}  // end index function


	public function listallcandidate(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
			$seched = getschedulebyid($ele_details->ScheduleID);


			$val = $this->romodel->checkfinalize_acbyro($d->st_code, $d->ac_no, $d->officerlevel);
			$cand_status = $request->input('cand_status');
			$search = $request->input('search');
			$list = $this->romodel->Allcandidatelist($ele_details, $cand_status, $search);
			$status = allstatus();

			$data['user_data'] = $d;
			$data['ele_details'] = $ele_details;
			$data['sched'] = $seched;
			$data['check_finalize'] = $check_finalize;
			$data['cand_finalize_ceo'] = $check_finalize->finalize_by_ceo;
			$data['cand_finalize_ro'] = $check_finalize->finalized_ac;
			$data['checkval'] = $val;
			$data['status_list'] = $status;
			$data['lists'] = $list;
			$data['status'] = $cand_status;

			return view('admin.pc.ro.listallcandidate', $data);
		} else {
			return redirect('/officer-login');
		}
	}  // end  function   
	public function withdrawn_candidates(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);

			if (isset($ele_details))
				$seched = getschedulebyid($ele_details->ScheduleID);
			else
				$seched = '';
			$sechdul = checkscheduledetails($seched);

			$val = $this->romodel->checkfinalize_acbyro($d->st_code, $d->ac_no, $d->officerlevel);
			$cand_status = $request->input('cand_status');
			$search = $request->input('search');
			$list = $this->romodel->withdrawn($ele_details, $cand_status, $search);
			$status = allstatus();

			$data['user_data'] = $d;
			$data['ele_details'] = $ele_details;
			$data['sched'] = $seched;
			$data['check_finalize'] = $check_finalize;
			$data['cand_finalize_ceo'] = $check_finalize->finalize_by_ceo;
			$data['cand_finalize_ro'] = $check_finalize->finalized_ac;
			$data['checkval'] = $val;
			$data['status_list'] = $status;
			$data['lists'] = $list;
			$data['status'] = $cand_status;

			return view('admin.pc.ro.withdrawn_candidates', $data);
		} else {
			return redirect('/officer-login');
		}
	}  // end  function   withdrawn_candidates



	public function statusvalidation(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);


			$this->validate(
				$request,
				[
					//'verifyotp' => 'required|numeric',
					//'affidavit' => 'required',
					'rejection_message' => 'required',
				],
				[
					// 'verifyotp.required' => 'Please enter your valid Otp', 
					//'verifyotp.numeric' => 'Please enter your valid Otp',
					//'affidavit.required' => 'Please check the affidavit',
					'rejection_message.required' => 'Please enter Message',
				]
			);
			//$verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
			$candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
			$nom_id = $this->xssClean->clean_input($request->input('nom_id'));
			$marks = $this->xssClean->clean_input($request->input('marks'));
			$rejection_message = $this->xssClean->clean_input($request->input('rejection_message'));
			//$affidavit = $this->xssClean->clean_input($request->input('affidavit'));  
			$st = array('rejection_message' => $rejection_message, 'application_status' => $marks, 'affidavit_public' => 'yes', 'scrutiny_date' => date('Y-m-d'));
			$i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			\Session::flash('ro_admin', 'Action successfully Change');

			$this->commonModel->Audit_log_data('0', $d->id, 'candidate_nomination_detail', $nom_id, 'application_status', 'receipt_generated', $marks, request()->ip(), 'NA', 'N/A', '3', 'Complete', date("Y-m-d"));
			\Session::flash('success_mes', 'Candidate status successfully changed');
			return Redirect::to('ropc/scrutiny-candidates');
		} else {
			return redirect('/officer-login');
		}
	}
	public function withstatusvalidation(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);


			$this->validate(
				$request,
				[
					//'verifyotp' => 'required|numeric',
					//'affidavit' => 'required',
					'rejection_message' => 'required',
				],
				[
					// 'verifyotp.required' => 'Please enter your valid Otp', 
					//'verifyotp.numeric' => 'Please enter your valid Otp',
					//'affidavit.required' => 'Please check the affidavit',
					'rejection_message.required' => 'Please enter Message',
				]
			);
			//$verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
			$candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
			$nom_id = $this->xssClean->clean_input($request->input('nom_id'));
			$marks = $this->xssClean->clean_input($request->input('marks'));
			$rejection_message = $this->xssClean->clean_input($request->input('rejection_message'));
			//$affidavit = $this->xssClean->clean_input($request->input('affidavit'));  
			$st = array('rejection_message' => $rejection_message, 'application_status' => $marks, 'affidavit_public' => 'yes');
			$i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			\Session::flash('ro_admin', 'Action successfully Change');

			$this->commonModel->Audit_log_data('0', $d->id, 'candidate_nomination_detail', $nom_id, 'application_status', 'receipt_generated', $marks, request()->ip(), 'NA', 'N/A', '3', 'Complete', date("Y-m-d"));
			\Session::flash('success_mes', 'Candidate withdrawn status successfully changed');

			return Redirect::to('ropc/withdrawn-candidates');
		} else {
			return redirect('/officer-login');
		}
	}
	public function accepted_application(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);

			if (isset($ele_details))
				$seched = getschedulebyid($ele_details->ScheduleID);
			else
				$seched = '';
			$sechdul = checkscheduledetails($seched);

			$val = $this->romodel->checkfinalize_acbyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
			$search = $request->input('search');
			$list = $this->romodel->contestingcandidate($ele_details, $search);

			$data['user_data'] = $d;
			$data['ele_details'] = $ele_details;
			$data['sched'] = $seched;
			$data['check_finalize'] = $check_finalize;
			$data['cand_finalize_ceo'] = $check_finalize->finalize_by_ceo;
			$data['cand_finalize_ro'] = $check_finalize->finalized_ac;
			$data['checkval'] = $val;
			$data['sechdul'] = $sechdul;
			$data['lists'] = $list;

			return view('admin.pc.ro.listaccepted', $data);
		} else {
			return Redirect::to('/officer-login');
		}
	}
	public function change_sequence(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');

			$noval = $this->xssClean->clean_input($request->input('noval'));
			$v = $this->xssClean->clean_input($request->input('totalvalue'));
			$input = $request->all();
			$rules = ['Please enter all new serial number'];
			for ($i = 1; $i < $noval; $i++) {
				$this->validate(
					$request,
					['newsrno' . $i => 'required|integer',],
					[
						'newsrno' . $i . 'required' => 'Please enter all new serial number ',
					]
				);
			}
			for ($i = 1; $i < $noval; $i++) {
				$k = $i + 1;
				$s = $this->xssClean->clean_input($request->input('newsrno' . $i));
				$s1 = $this->xssClean->clean_input($request->input('newsrno' . $k));
				if ($s > $v) {
					\Session::flash('error_mes', 'Enter valid new serial number ');
					return Redirect::to('/ropc/contested-application');
				}
				if ($s == $s1) {
					\Session::flash('error_mes1', 'Dublicate Sr. number ');
					return Redirect::to('/ropc/contested-application');
				}
				if ($s == 0) {
					\Session::flash('error_mes1', 'please not entry zero');
					return Redirect::to('/ropc/contested-application');
				}
			}
			$rec = DB::table('candidate_nomination_detail')->where('party_id', '1180')->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->where('election_id', $ele_details->ELECTION_ID)->first();
			//dd($rec);	
			for ($i = 1; $i < $noval; $i++) {
				$s = trim($request->input('newsrno' . $i));
				$candidate_id = trim($request->input('nom_id' . $i));
				$no = array('new_srno' => $s);
				DB::table('candidate_nomination_detail')->where('nom_id', $candidate_id)->update($no);
				$this->commonModel->Audit_log_data('0', $d->id, 'candidate_nomination_detail', $candidate_id, 'new_srno', 'NO', $s, request()->ip(), 'NA', 'N/A', '3', 'Complete', date("Y-m-d"));
			}
			//$n=$noval;

			if (isset($rec)) {
				$no = array('new_srno' => $noval);
				DB::table('candidate_nomination_detail')->where('nom_id', $rec->nom_id)->update($no);
			}
			\Session::flash('success_mes', 'Candidate New sr.no successfully Updated');
			return Redirect::to('/ropc/contested-application');
		} else {
			return Redirect::to('/officer-login');
		}
	}
	public function pdfview(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');

			if ($ele_details->CONST_TYPE == "AC") {
				$v = 'candidate_nomination_detail.ac_no';
				$m = $ele_details->CONST_NO;
			} elseif ($ele_details->CONST_TYPE == "PC") {
				$v = 'candidate_nomination_detail.pc_no';
				$m = $ele_details->CONST_NO;
			}
			$a = 'N';
			$a1 = 'S';
			$a2 = 'U';
			$a3 = '0';
			$a4 = 'Z';
			$candn = $this->romodel->partywiseallcontenestingcandidate($ele_details);
			$cands = $this->romodel->partywisecontenestingcandidate($ele_details, $a1, $a1);
			$candu = $this->romodel->partywisecontenestingcandidate($ele_details, $a2, $a3);
			$candz = $this->romodel->partywisecontenestingcandidate($ele_details, $a4, $a4);


			$pc = '';
			$ac = '';
			if (!empty($d->ac_no))
				$ac = getacbyacno($d->st_code, $d->ac_no);
			if (!empty($d->pc_no))
				$pc = getpcbypcno($d->st_code, $d->pc_no);

			$state = getstatebystatecode($d->st_code);
			//$ac=getacbyacno($d->st_code,$d->ac_no);
			// print_r($cands); print_r($candu);  print_r($candz); die;
			view()->share('candn', $candn, 'cands', $cands, 'candu', $candu, 'candz', $candz, 'state', $state, 'pc', $pc);
			// view()->share('candn',$candn,'st',$state,'pc',$pc);

			if ($request->has('download')) {
				$pdf = PDF::loadView('admin.pc.nomination.pdfview', compact('candn', $candn, 'cands', $cands, 'candu', $candu, 'candz', $candz, 'state', $state, 'pc', $pc));
				return $pdf->download('contesting-candidates.pdf');
			}


			return view('contesting-candidates');
		} else {
			return redirect('/officer-login');
		}
	}
	public function symbol_upload()
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);

			if (isset($ele_details))
				$seched = getschedulebyid($ele_details->ScheduleID);
			else
				$seched = '';
			$val = $this->romodel->checkfinalize_acbyro($d->st_code, $d->ac_no, $d->officerlevel);
			$list = $this->romodel->Symbolcandidate($ele_details);

			$sym = DB::table("m_symbol")->whereNOTIn('SYMBOL_NO', function ($query) {
				$query->select('PARTYSYM')->from('m_party')->where('PARTYTYPE', ['N', 'S', 'Z', '-Z']);
			})->get();

			$data['user_data'] = $d;
			$data['ele_details'] = $ele_details;
			$data['sched'] = $seched;
			$data['check_finalize'] = $check_finalize;
			$data['cand_finalize_ceo'] = $check_finalize->finalize_by_ceo;
			$data['cand_finalize_ro'] = $check_finalize->finalized_ac;
			$data['checkval'] = $val;
			$data['lists'] = $list;
			$data['sym'] = $sym;

			return view('admin.pc.ro.symboldetails', $data);
		} else {
			return Redirect::to('/officer-login');
		}
	}
	public function assign_symbol($nom_id)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			if (!empty($nom_id)) {
				$list = $this->romodel->Symbolassign($nom_id);

				return view('admin.pc.ro.symbolassign', ['user_data' => $d, 'lists' => $list, 'showpage' => 'candidate']);
			} else {
				return Redirect::to('/ropc');
			}
		} else {
			return Redirect::to('/officer-login');
		}
	}
	public function updatesymbol(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$this->validate(
				$request,
				[
					'symbol' => 'required',
				],
				[
					'symbol.required' => 'Please select symbol',
				]
			);
			$candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
			$nom_id = $this->xssClean->clean_input($request->input('nom_id'));
			$symbol = $this->xssClean->clean_input($request->input('symbol'));
			$check = DB::table('candidate_nomination_detail')->where('symbol_id', $symbol)->where('st_code', $ele_details->ST_CODE)->where('pc_no', $ele_details->CONST_NO)->first();
			// $check=getById('candidate_nomination_detail','symbol_id',$symbol);
			$udata = array('symbol_id' => $symbol);
			//echo $candidate_id;  echo "=".$nom_id; candidate_nomination_detail.ST_CODE

			if (!isset($check)) {
				$n = $this->commonModel->updatedata('candidate_nomination_detail', 'nom_id', $nom_id, $udata);
				$this->commonModel->Audit_log_data('0', $d->id, 'candidate_nomination_detail', $nom_id, 'symbol_id', 'NO', $symbol, request()->ip(), 'NA', 'N/A', '3', 'Complete', date("Y-m-d"));


				\Session::flash('success_mes', 'Symbol successfully Assigned');
				return Redirect::to('/ropc/symbol-upload');
			} else {
				\Session::flash('error_mes', 'Symbol has already been taken,please choose another symbol');
				return Redirect::to('/ropc/symbol-upload');
			}
		} else {
			return Redirect::to('/officer-login');
		}
	}
	function finalize_ac()
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);

			if (isset($ele_details))
				$seched = getschedulebyid($ele_details->ScheduleID);
			else
				$seched = '';
			$sechdul = checkscheduledetails($seched);

			$check_ac = DB::table('candidate_finalized_ac')->where('st_code', $ele_details->ST_CODE)
				->where('const_no', $ele_details->CONST_NO)
				->where('const_type', $ele_details->CONST_TYPE)
				->where('election_id', $ele_details->ELECTION_ID)->first();

			$date = Carbon::now();
			$currentTime = $date->format('Y-m-d H:i:s');
			$otp = "123456";
			//$otp= rand(100000,999999);
			//$mob_message = "Dear Sir/Madam Your OTP is " . $otp . " for finalized PC in Suvidha Portal.Please enter the OTP  to proceed.This OTP is valid only for 10 minutes.Do not share this OTP. Regards  Team ICT  ";

			 $mob_message = "Dear Sir/Madam, your OTP is ".$otp." for ECI Candidate Portal. Please enter the OTP to proceed.Do not share this OTP Team ECI.";

			if (!isset($check_ac)) {
				$st = array('st_code' => $ele_details->ST_CODE, 'const_no' => $ele_details->CONST_NO, 'const_type' => $ele_details->CONST_TYPE, 'election_id' => $ele_details->ELECTION_ID, 'finalized_ac' => '0', 'mobile_otp' => $otp, 'otp_time' => $currentTime, 'created_at' => date("Y-m-d H:i:s"), 'created_by' => $d->officername);
				$r = $this->commonModel->insertData('candidate_finalized_ac', $st);
				$check_ac = DB::table('candidate_finalized_ac')->where('st_code', $ele_details->ST_CODE)->where('const_no', $ele_details->CONST_NO)->where('const_type', $ele_details->CONST_TYPE)->where('election_id', $ele_details->ELECTION_ID)->first();
			} else {
				$st = array('mobile_otp' => $otp, 'otp_time' => $currentTime);
				$i = DB::table('candidate_finalized_ac')->where('id', $check_ac->id)->update($st);
			}

			$html = $otp;
			if (!empty($d->email)) {
				// sendotpmail($d->email,'Otp details',$html);  
				mail($d->email, 'otp details', $html, 'suvidha.eci.gov.in');
			}
			$response = SmsgatewayHelper::gupshup($d->Phone_no, $mob_message);

			return view('admin.pc.ro.finalize-ac', ['user_data' => $d, 'cand_finalize_ceo' => $check_finalize->finalize_by_ceo, 'cand_finalize_ro' => $check_finalize->finalized_ac, 'sechdul' => $sechdul, 'sched' => $seched, 'ele_details' => $ele_details, 'lists' => $check_ac, 'otp' => $otp, 'otp_time' => $currentTime]);
		} else {
			return Redirect::to('/officer-login');
		}
	}
	function finalize_candidate(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$this->validate(
				$request,
				[
					'verifyotp' => 'required|numeric',
					//'finalized_message' => 'required',
				],
				[
					'verifyotp.required' => 'Please enter your valid Otp',
					'verifyotp.numeric' => 'Please enter your valid Otp',
					//'finalized_message.required' => 'Please check the affidavit',
				]
			);
			$verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
			$finalized_message = $this->xssClean->clean_input($request->input('finalized_message'));
			$id = $this->xssClean->clean_input($request->input('id'));
			$cons_no = $this->xssClean->clean_input($request->input('cons_no'));
			$st_code = $this->xssClean->clean_input($request->input('st_code'));
			$CONS_TYPE = $this->xssClean->clean_input($request->input('CONS_TYPE'));
			$ELECTION_ID = $this->xssClean->clean_input($request->input('ELECTION_ID'));
			$otp = $this->xssClean->clean_input($request->input('otp'));
			$otp_time = $this->xssClean->clean_input($request->input('otp_time'));

			$date = Carbon::now()->subMinutes(10);
			$currentTime = $date->format('Y-m-d H:i:s');
			//echo $currentTime; echo $otp_time;
			if ($otp != $verifyotp) {
				\Session::flash('ro_opt_messsage', 'Your Otp Message Invalide');
				return Redirect::to('/ropc/finalize-ac');
			}
			if ($otp_time < $currentTime) {
				\Session::flash('ro_opt_messsage', 'Your Otp time Expair');
				return Redirect::to('/ropc/finalize-ac');
			}
			$ins_data = array('finalized_ac' => '1', 'finalized_message' => $finalized_message, 'finalize_date' => date('Y-m-d'));
			$state = $this->commonModel->getstatebystatecode($d->st_code);
			$ac = $this->commonModel->getacbyacno($d->st_code, $d->ac_no);
			$ddeo = DB::table('officer_login')->where('st_code', $d->st_code)
				->where('dist_no', $d->dist_no)->where('officerlevel', 'DEO')->first();
			$cceo = DB::table('officer_login')->where('st_code', $d->st_code)
				->where('officerlevel', 'CEO')->first();

			// $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');

			$list = $this->romodel->finalize_candidate_ac($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE, $ins_data);

			$html = $state->ST_NAME;

			//sendlevelmail($ddeo->email,$d->name,$ac->AC_NAME,$html);
			//sendlevelmail($cceo->email,$d->name,$ac->AC_NAME,$html);

			\Session::flash('success_mes', 'Finalized successfully');
			return Redirect::to('/ropc/contested-application');
		} else {
			return Redirect::to('/officer-login');
		}
	}
	function public_affidavit()
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);

			$list = $this->romodel->public_affidavit_ac($d->st_code, $d->ac_no);
			\Session::flash('success_mes', 'After Scrutiny All affidavit Public');
			return Redirect::to('/ro/contested-application');
		} else {
			return Redirect::to('/officer-login');
		}
	}
	public function ballotpaperpdfview(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);

			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');

			if ($ele_details->CONST_TYPE == "AC") {
				$v = 'candidate_nomination_detail.ac_no';
				$m = $ele_details->CONST_NO;
			} elseif ($ele_details->CONST_TYPE == "PC") {
				$v = 'candidate_nomination_detail.pc_no';
				$m = $ele_details->CONST_NO;
			}

			$cand = DB::table('candidate_nomination_detail')
				->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
				->leftjoin('m_symbol', 'candidate_nomination_detail.symbol_id', '=', 'm_symbol.SYMBOL_NO')
				->where('candidate_nomination_detail.st_code', '=', $d->st_code)->where($v, '=', $m)
				->where('candidate_nomination_detail.application_status', '=', '6')
				->orderBy('candidate_nomination_detail.new_srno', 'asc')
				->select('candidate_personal_detail.cand_name', 'candidate_personal_detail.cand_image', 'candidate_nomination_detail.new_srno', 'm_symbol.*')->get();

			view()->share('cand', $cand);

			//if($request->has('download')){
			$pdf = MPDF::loadView('admin.ballotview', compact('cand', $cand));
			return $pdf->download('dadmin.ballotview.pdf');
			//$pdf = PDF::loadView('admin.ballotview',compact('cand',$cand));
			//return $pdf->download('admin.ballotview.pdf');
			///}
			return view('admin.ballotview');
		} else {
			return redirect('/officer-login');
		}
	}

	public function listnomination(request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);

			if (isset($ele_details))
				$seched = getschedulebyid($ele_details->ScheduleID);
			else
				$seched = '';

			$val = $this->romodel->checkfinalize_acbyro($ele_details->ST_CODE, $ele_details->CONST_NO, 'PC');

			$cand_status = '';
			$search = '';
			$cand_status = $request->input('cand_status');
			$search = $request->input('search');
			$status = allstatus();
			$list = $this->romodel->Allcandidatelist($ele_details, $cand_status, $search);

			$data['user_data'] = $d;
			$data['ele_details'] = $ele_details;
			$data['lists'] = $list;
			$data['status'] = $cand_status;
			$data['checkval'] = $val;
			$data['status_list'] = $status;
			$data['cand_finalize_ceo'] = $check_finalize->finalize_by_ceo;
			$data['cand_finalize_ro'] = $check_finalize->finalized_ac;


			return view('admin.pc.ro.listnomination', $data);
		} else {
			return redirect('/officer-login');
		}
	}
	public function reports(request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$val = $this->romodel->checkfinalize_acbyro($ele_details->ST_CODE, $ele_details->CONST_NO, 'PC');
			// dd($request);
			$cand_status = '';
			$search = '';
			$cand_status = $request->input('cand_status');
			$search = $request->input('search');
			$status = allstatus();
			$list = $this->romodel->Allcandidatelist($ele_details, $cand_status, $search);

			return view('admin.pc.ro.reports', ['user_data' => $d, 'cand_finalize_ceo' => $check_finalize->finalize_by_ceo, 'cand_finalize_ro' => $check_finalize->finalized_ac, 'sechdul' => $sechdul, 'sched' => $seched, 'lists' => $list, 'status' => $cand_status, 'checkval' => $val, 'status_list' => $status, 'edetails' => $ele_details]);
		} else {
			return redirect('/officer-login');
		}
	}
	public function viewnomination($nomid)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
			$nomid   = Crypt::decrypt($nomid);

			if (isset($ele_details))
				$seched = getschedulebyid($ele_details->ScheduleID);
			else
				$seched = '';


			$nom = getById('candidate_nomination_detail', 'nom_id', $nomid);
			$cand = getById('candidate_personal_detail', 'candidate_id', $nom->candidate_id);
			$st_code = $ele_details->ST_CODE;
			$const_no = $ele_details->CONST_NO;
			$distno = $d->dist_no;

			$st = getallstate($st_code);
			$pc = getpcbypcno($st_code, $const_no);
			$dist = getdistrictbydistrictno($st_code, $distno);
			$all_state = getallstate();
			$all_dist = getalldistrictbystate($st_code);
			$all_ac = getacbystate($st_code);

			$data['user_data'] = $d;
			$data['ele_details'] = $ele_details;
			$data['nomDetails'] = $nom;
			$data['sechdul'] = $seched;
			$data['nomid'] = $nomid;
			$data['all_state'] = $all_state;
			$data['cand_finalize_ceo'] = $check_finalize->finalize_by_ceo;
			$data['cand_finalize_ro'] = $check_finalize->finalized_ac;
			$data['persoanlDetails'] = $cand;
			$data['all_dist'] = $all_dist;
			$data['all_ac'] = $all_ac;
			$data['pc'] = $pc;
			//dd($data);

			return view('admin.pc.ro.viewnomination', $data);
		} else {
			return redirect('/officer-login');
		}
	}



	public function officerList(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$officerlist = DB::table('officer_login')
				->orWhere('designation', 'LIKE', 'CEO')
				->orWhere('designation', 'LIKE', 'DEO')
				->orWhere('designation', 'LIKE', 'ROPC')
				->orWhere('designation', 'LIKE', 'ARO')
				->where('st_code', $d->st_code)->get();
			// print_r($officerlist);  die;
			return view('admin.pc.ceo.officer-details', ['user_data' => $d, 'ele_details' => $ele_details, 'officerlist' => $officerlist]);
		} else {
			return redirect('/officer-login');
		}
	}   // end candidateListbyPC function  


	public function officerProfileUpdate(Request $request, $id = '')
	{

		//  dd($request->all());

		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');

			if (!empty($_POST['profileUpdate'])) {
				$validator = $request->validate([
					'name' => 'required',
					'email' => 'required',
					'Phone_no' => 'required|string|min:10|numeric|digits:10',
				]);

				// if ($validator->passes()) {
				if ($validator) {
					if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['Phone_no'])) {

						$name =  strip_tags($_POST['name']);
						$email =  strip_tags($_POST['email']);
						$Phone_no = strip_tags($_POST['Phone_no']);
						// $Phone_no = $this->xssClean($_POST['profileUpdate']);
						$officerdata = array(
							'name' => $name,
							'email' => $email,
							'Phone_no' => $Phone_no,
							/*'modified_by' => $d->id,*/
							'added_update_at' => date('Y-m-d'),
							'updated_at' => date('Y-m-d H:i:s')
						);
						// dd($officerdata);
						$where = array('id' => $_POST['profileUpdate']);
						$result = DB::table('officer_login')->where($where)->update($officerdata);

						\Session::flash('success_success', 'You have Successfully Updated!. ');
						// return redirect()->back();
						return redirect('/pcceo/officer-details');
					}
				} else {
					\Session::flash('success_error', 'You have some Error!. ');
					return redirect('/pcceo/officer-details');
					//  return redirect()->back()->withErrors($validator, 'error');
				}
			} else {
				$decryptedid = decrypt($id);
				$getofficerdetails = DB::table('officer_login')->where('id', $decryptedid)->get();
				return view('admin.pc.ceo.officer-profile')->with(array('user_data' => $d, 'showpage' => 'officer-profile', 'getofficerdetails' => $getofficerdetails));
			}
		} else {
			return redirect('/officer-login');
		}
	}



	public function psinfoList(Request $request)
	{
		//dd($request->all());

		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$all_state = $this->commonModel->getallstate();
			$all_dist = $this->commonModel->getalldistrictbystate($d->st_code);
			$all_ac = $this->commonModel->getacbystate($d->st_code);
			// $officerlist =DB::table('officer_login')->where('st_code',$d->st_code)->get();
			// print_r($officerlist);  die;
			return view('admin.pc.ceo.psinfo', ['user_data' => $d, 'ele_details' => $ele_details, 'all_state' => $all_state, 'all_dist' => $all_dist, 'all_ac' => $all_ac]);
		} else {
			return redirect('/officer-login');
		}
	}   // end candidateListbyPC function  


	public function getaclist(request $request)
	{
		//dd($request->all());
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);

			$district = $request->input('district');
			$stcode = $d->st_code;
			$acdata = $this->commonModel->getAcByst($stcode, $district);
		}
		return $acdata;
	}

	public function psresultList(Request $request)
	{
		//dd($request->all());
		$url = 'http://eronetservices.ecinet.in/api/ERONet/GetPSDetailsAcWise';
		$st_code = $request->st_code;
		$ac_no = $request->ac;
		// $st_code='S11';
		//$ac_no='2';
		//$secureKey = "ABCD1234#123521GISTECIKEY";
		$method = 'POST';
		$resultData = $this->pcceoreportModel->ComputeSha512Hash($st_code, $ac_no);
		// dd($resultData);
		$data = array(
			"ST_CODE" => $st_code,
			"ac_no" => $ac_no,
			"Client_HASHCode" => $resultData,
		);
		$data_string = json_encode($data);
		$jsonResult = $this->pcceoreportModel->callAPI($method, $url, $data_string);
		$dist_no = $request->district;
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');


			return view('admin.pc.ceo.psinfo', ['user_data' => $d, 'dist_no' => $dist_no, 'ac_no' => $ac_no, 'st_code' => $st_code, 'jsonResult' => $jsonResult]);
		} else {
			return redirect('/officer-login');
		}
	}   // end candidateListbyPC function  

	public function duplicate_drop(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);

			$candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
			$nom_id = $this->xssClean->clean_input($request->input('nom_id'));
			$marks = $this->xssClean->clean_input($request->input('marks'));
			if ($marks == 11) {
				$st = array('application_status' => $marks);
				$i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);

				$this->commonModel->Audit_log_data('0', $d->id, 'candidate_nomination_detail', $nom_id, 'application_status', 'duplicate_drop', $marks, request()->ip(), 'NA', 'N/A', '3', 'Complete', date("Y-m-d"));
				\Session::flash('success_mes', 'Candidate Duplicate drop successfully changed');
				return Redirect::to('ropc/listnomination');
			} else {
				\Session::flash('error_mes', 'please select duplicate status');
				return Redirect::to('ropc/listnomination');
			}
		} else {
			return redirect('/officer-login');
		}
	}
	public function accepted_candidate(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);

			if (isset($ele_details))
				$seched = getschedulebyid($ele_details->ScheduleID);
			else
				$seched = '';


			$val = $this->romodel->checkfinalize_acbyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
			$search = $request->input('search');
			$cand_status = $request->input('cand_status');
			$list = $this->romodel->acceptedcandidate($ele_details, $search);

			$data['user_data'] = $d;
			$data['ele_details'] = $ele_details;
			$data['lists'] = $list;
			$data['status'] = $cand_status;
			$data['checkval'] = $val;
			//$data['status_list']=$status;  
			$data['cand_finalize_ceo'] = $check_finalize->finalize_by_ceo;
			$data['cand_finalize_ro'] = $check_finalize->finalized_ac;

			return view('admin.pc.ro.accepted-candidate', $data);
		} else {
			return Redirect::to('/officer-login');
		}
	}
	/*
	public function finalaccepted(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$this->validate(
				$request,
				[
					'marks' => 'required',
				],
				[
					'marks.required' => 'Please select option',
				]
			);
			$candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
			$nom_id = $this->xssClean->clean_input($request->input('nom_id'));
			$marks = $this->xssClean->clean_input($request->input('marks'));

			$st = array('finalaccepted' => $marks,);
			$i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			\Session::flash('ro_admin', 'Contesting Status successfully Changed');

			$this->commonModel->Audit_log_data('0', $d->id, 'candidate_nomination_detail', $nom_id, 'finalaccepted', 'finalaccepted', $marks, request()->ip(), 'NA', 'N/A', '3', 'Complete', date("Y-m-d"));
			\Session::flash('success_mes', 'Contesting Status successfully Changed');
			return Redirect::to('ropc/accepted-candidate');
		} else {
			return redirect('/officer-login');
		}
	}

*/

            public function finalaccepted(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$this->validate(
				$request,
				[
					'marks' => 'required',
				],
				[
					'marks.required' => 'Please select option',
				]
			);
			$candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
			$nom_id = $this->xssClean->clean_input($request->input('nom_id'));
			$marks = $this->xssClean->clean_input($request->input('marks'));

			$rec= DB::table('candidate_nomination_detail')->where('candidate_id',$candidate_id)->get();
              
                $nominatioID=array();
                foreach ($rec as  $value) {

                	$nominatioID[] = $value->nom_id;  	
                }
               
				if (($key = array_search($nom_id, $nominatioID)) !== false) {
				    unset($nominatioID[$key]);
				}
				if($marks==1)
                {

                   $st = array('finalaccepted'=>$marks,); 
			       $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			       if(isset($nominatioID) && !empty($nominatioID )){
			       $stno = array('finalaccepted'=> 0);
			       $validno = DB::table('candidate_nomination_detail')->whereIn('nom_id', $nominatioID)->update($stno);
			        }

                }else{
                 
                   $st = array('finalaccepted'=>$marks,); 
			       $i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);

	     		}

				

			//$st = array('finalaccepted' => $marks,);
			$i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			\Session::flash('ro_admin', 'Contesting Status successfully Changed');

			$this->commonModel->Audit_log_data('0', $d->id, 'candidate_nomination_detail', $nom_id, 'finalaccepted', 'finalaccepted', $marks, request()->ip(), 'NA', 'N/A', '3', 'Complete', date("Y-m-d"));
			\Session::flash('success_mes', 'Contesting Status successfully Changed');
			return Redirect::to('ropc/accepted-candidate');
		} else {
			return redirect('/officer-login');
		}
	}












	public function ac_wise_electors_details(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
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
			$listac = getallacbypcno($ele_details->ST_CODE, $ele_details->CONST_NO);
			return view('admin.pc.ro.listelectors', ['user_data' => $d, 'ele_details' => $ele_details, 'listac' => $listac]);
		} else {
			return redirect('/officer-login');
		}
	}
	public function verifyac_wise_electors_details(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);
			$ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, 'PC');
			$cur_time  = Carbon::now();

			$validator = Validator::make($request->all(), [
				'electors_male'     => 'required|numeric|min:1|integer|between:1,9999999',
				'electors_female'   => 'required|numeric|min:1|integer|between:1,9999999',
				'electors_other'    => 'required|numeric|min:0|integer|between:0,9999999',
				'electors_total'    => 'required|numeric|min:1|integer|between:1,9999999',
				'service_total'     => 'required|numeric|min:1|integer|between:0,9999999',

			]);


			if ($validator->fails()) {
				return Redirect::back()
					->withErrors($validator)
					->withInput();
			}


			

			$xss = new xssClean;

			$request              = $request->all();
			$electors_male        = $xss->clean_input($request['electors_male']);
			$electors_female      = $xss->clean_input($request['electors_female']);
			$electors_other       = $xss->clean_input($request['electors_other']);
			$electors_total       = $xss->clean_input($request['electors_total']);
			$service_total        = $xss->clean_input($request['service_total']);
			$ac_no                = $xss->clean_input($request['ac_no']);

			if(($electors_male + $electors_female  + $electors_other) != $electors_total){
				\Session::flash('errors_mes', 'Electrol total is not equal to sum of male, female and other electrols');
				return Redirect('/ropc/ac-wise-electors-details');
			}

			$update_fields = array(

				'electors_male'      => $electors_male,
				'electors_female'    => $electors_female,
				'electors_other'     => $electors_other,
				'electors_total'     => $electors_total,
				'electors_service'   => $service_total,

			);
			$elec_fields = array('electors_total'     => $electors_total);
			$ElectorsWhere = ['st_code' => $ele_details->ST_CODE, 'ac_no' => $ac_no];
			$Data = DB::table('electors_cdac')->where($ElectorsWhere)->update($update_fields);
			$Data1 = DB::table('pd_scheduledetail')->where($ElectorsWhere)->update($elec_fields);
			\Session::flash('success_mes', 'Electrol Data Updated Successfully !');
			return Redirect('/ropc/ac-wise-electors-details')->with('error', 'Electrol Data Updated Successfully !');
		} else {
			return redirect('/officer-login');
		}
	}


          public function statusvalidation_reject(Request $request)
	{
		if (Auth::check()) {
			$user = Auth::user();
			$d = $this->commonModel->getunewserbyuserid($user->id);


			$this->validate(
				$request,
				[
					//'verifyotp' => 'required|numeric',
					//'affidavit' => 'required',
					'rejection_message' => 'required',
				],
				[
					// 'verifyotp.required' => 'Please enter your valid Otp', 
					//'verifyotp.numeric' => 'Please enter your valid Otp',
					//'affidavit.required' => 'Please check the affidavit',
					'rejection_message.required' => 'Please enter Message',
				]
			);
			//$verifyotp = $this->xssClean->clean_input($request->input('verifyotp'));
			$candidate_id = $this->xssClean->clean_input($request->input('candidate_id'));
			$nom_id = $this->xssClean->clean_input($request->input('nom_id'));
			$marks = $this->xssClean->clean_input($request->input('marks'));
			$rejection_message = $this->xssClean->clean_input($request->input('rejection_message'));
			//$affidavit = $this->xssClean->clean_input($request->input('affidavit'));  
			$st = array('rejection_message' => $rejection_message, 'application_status' => $marks, 'affidavit_public' => 'yes', 'scrutiny_date' => date('Y-m-d'));
			$i = DB::table('candidate_nomination_detail')->where('nom_id', $nom_id)->update($st);
			\Session::flash('ro_admin', 'Action successfully Change');

			$this->commonModel->Audit_log_data('0', $d->id, 'candidate_nomination_detail', $nom_id, 'application_status', 'receipt_generated', $marks, request()->ip(), 'NA', 'N/A', '3', 'Complete', date("Y-m-d"));
			\Session::flash('success_mes', 'Candidate status successfully changed');
			return Redirect::to('ropc/scrutiny-candidates');
		} else {
			return redirect('/officer-login');
		}
	}


















}  // end class  //accepted_candidate  
