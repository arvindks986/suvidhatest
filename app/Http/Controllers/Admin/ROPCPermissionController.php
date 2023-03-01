<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\adminmodel\RoPermissionModel;
use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use DB;
use App\commonModel;
use App\adminmodel\CandidateModel;
use App\adminmodel\ROPCModel;
use App\Classes\xssClean;
use PDF;
use ZipArchive;
use File;
use Carbon\Carbon;
use App\Helpers\SendNotification;

class ROPCPermissionController extends Controller {

    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->CandidateModel = new CandidateModel();
        $this->romodel = new ROPCModel();
        $this->xssClean = new xssClean;
        $this->PM = new RoPermissionModel();
    }

    public function allMasters() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            return view('admin.pc.ropc.Permission.Masters', ['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details]);
        } else {
            return redirect('/officer-login');
        }
    }

// end index function

    public function OfflinePermission(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
//            echo '<pre/>';
//            print_r($d);die;
//            if($d->ac_no !=0)
//            {
            $getrodetails = $this->PM->getLoginUserdetails($d->id);
//            }
//            else
//            {
////                 $getrodetails = $this->PM->getLoginUserpcdetails($d->id);
//            }
//           
            if ($req->view == '0') {
                if (!empty($req->permsn_id)) {
                    $getPermissionDetails = DB::table('permission_required_doc')
                                    ->select('*')->where('permission_id', $req->permsn_id)->where('st_code', $d->st_code)->get()->toArray();
                    if (!empty($getPermissionDetails)) {
//                    print_r($getPermissionDetails);die;
                        return $getPermissionDetails;
                    } else {
                        return '0';
                    }
                }
                $user_details_police = $this->PM->user_details_police($req->stcode, $req->district, $req->ac_no);
                return $user_details_police;
            } else {
                if($d->role_id == 18)
                {
                $permission_type = DB::table('permission_type as a')
                                ->join('permission_master as m', 'm.id', '=', 'a.permission_type_id')
                                ->select('m.permission_name as pname', 'a.*', 'a.id as permsn_id', 'm.officer_role_id')
                                ->where('a.status', '1')
                                ->where('role_id',$d->role_id)
                                ->where('a.st_code', $d->st_code)->get()->toArray();
                }
                else
                {
                    $permission_type = DB::table('permission_type as a')
                                ->join('permission_master as m', 'm.id', '=', 'a.permission_type_id')
                                ->select('m.permission_name as pname', 'a.*', 'a.id as permsn_id', 'm.officer_role_id')
                                ->where('a.status', '1')
                                ->where('role_id',18)
                                ->where('a.st_code', $d->st_code)->get()->toArray();
                }
				$getallDist = DB::table('m_district')->select('DIST_NO','DIST_NAME')->where('ST_CODE',$d->st_code)->get()->toArray();
                $getallAC = $this->PM->getallAC($d->st_code,$d->pc_no);
                $getAllUserType = $this->PM->getAllUserType();
                $allParty = DB::table('m_party')->select('*')->orderBy('PARTYNAME')->get()->toArray();
                return view('admin.pc.ropc.Permission.OfflinePermissionApply')->with(array('user_data' => $d,'getallDist'=>$getallDist,'getallAC'=>$getallAC, 'getrodetails' => $getrodetails, 'showpage' => 'permission', 'permission_type' => $permission_type, 'getAllUserType' => $getAllUserType,'allParty' => $allParty,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
            }
        } else {
            return redirect('/officer-login');
        }
    }
    public function getPS(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            
            $acno=$req->ac_id;
            $stcode=$req->st_code;
            $getallps=$this->PM->getallps($acno,$stcode);
            return $getallps;
            } else {
            return redirect('/officer-login');
        }
    }
    
    public function getlocation(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $acno=$req->ac_id;
            $stcode=$req->st_code;
            $dist=$req->dist;
            $pcno=$req->pc_id;
            if($pcno != 0)
            {
                $getlocation=DB::table('location_master')
                        ->select('location_name','id')
                        ->where('st_code',$stcode)
                        //->where('dist_no',$dist)
                        ->where('ac_no',$acno)
                        ->where('pc_no',$pcno)
                        ->get()->toArray();
            }
            else
            {
                $getlocation=DB::table('location_master')
                        ->select('location_name','id')
                        ->where('st_code',$stcode)
                        //->where('dist_no',$dist)
                        ->where('ac_no',$acno)
//                        ->where('pc_no',$pcno)
                        ->get()->toArray();
            }
            return $getlocation;
            } else {
            return redirect('/officer-login');
        }
    }

    public function getUserDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $usermb = $req->mb_no;
            $chck = DB::table('user_login')->where('mobile', $usermb)->count();
            $chck1 = DB::table('user_data')->where('mobileno', $usermb)->count();
            $chckparty = DB::table('user_login')->where('mobile', $usermb)->whereNotNull('party_id')->where('party_id', '!=', 0)->select('party_id')->count();
            $chckrole = DB::table('user_login')->where('mobile', $usermb)->whereNotNull('role_id')->select('role_id')->count();
//            echo $chckrole;die;
//            echo $chck;die;
            if ($chck != 0 && $chck1 == 0) {

                if ($chckparty != 0 && $chckrole != 0) {
//                    echo '1';die;
                    $res = $this->PM->getLoginCandDetails($usermb);
                } else {
//                    echo '2';die;
                    $res = $this->PM->getLoginappCandDetails($usermb);
                }
//                $res = $this->PM->getUserDetails($usermb);
            } else if ($chck != 0 && $chck1 != 0) {
                if ($chckparty != 0 && $chckrole != 0) {
//                    echo '3';die;
                    $res = $this->PM->getUserDetails($usermb);
                } else {
//                    echo '4';die;
                    $res = $this->PM->getUserappDetails($usermb);
                }
//                $res = $this->PM->getLoginCandDetails($usermb);
            }
            if (!empty($res)) {
//                print_r($res);die;
                return $res;
            } else {
                echo 'No record';
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function UserDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $time = Carbon::now()->timestamp;
            $rules=[];
            $ptypeid=[];
            $messages=[];
            $getDistName = getdistrictbydistrictno($d->st_code, $d->dist_no);
            $getStateName = getstatebystatecode($d->st_code);
//            echo '<pre/>';
//            print_r($req->all());die;
                if (!empty($req->permission_type) && $req->permission_type != 0) {
                    
                    $ptype = $req->permission_type;
                    $ptypeid = explode('#', $ptype);
                    if (!empty($ptypeid) && $ptypeid[1] == 3 || $ptypeid[1] == 6) {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                            'user_email' => 'required|email',
                            'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'gender' => 'required',
                            'dob' => 'required',
                            'address' => 'required|not_regex:/([<>@$%?]+)/',
                            'state' => 'required',
                            'district' => 'required|not_in:0',
                            'ac_no' => 'required',
                            'police_station' => 'required|not_in:0',
                            'permission_type' => 'required|not_in:0',
                            'user_type' => 'required|not_in:0',
                            'stdate' => 'required',
                            'enddate' => 'required',
							'subdate' => 'required',
                            'permsndoc.*.p_doc' => 'mimes:pdf',
                            'political_party' => 'required|not_in:0'
                        ];
                        $messages = [
                            'user_mb.required' => ' Mobile field is required.',
                            'user_mb.digits' => 'Please Enter valid Mobile Number.',
                            'user_email.required' => ' Email field is required.',
                            'user_email.email' => 'Please Enter valid Email',
                            'fathers_name.required' => 'Fathers Name is required.',
                            'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                            'user_name.required' => 'Name is required',
                            'user_name.regex' => 'Please Enter only alphanumeric character.',
                            'gender.required' => 'Gender is required Field',
                            'dob.required' => 'DOB is required',
                            'address.required' => 'Address is required',
                            'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                            'state.required' => 'State is Required field',
                            'district.required' => 'District is Required field',
                            'ac_no.required' => 'AC Required field',
                            'police_station.required' => 'Police Station is Required field',
                            'permission_type.required' => 'Permission Type is Required field',
                            'user_type' => 'User Type Required field',
                            'stdate.required' => 'Select start date',
                            'enddate.required' => 'Select end date',
							'subdate.required' => 'Submission date is Required',
                            'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                            'political_party.required' => 'Please Select Political Party'
                        ];
                    }
                    else if(!empty($ptypeid) && !empty($ptypeid[1] == 8))
                {
                    $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                            'user_email' => 'required|email',
                            'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                            'gender' => 'required',
                            'dob' => 'required',
                            'address' => 'required|not_regex:/([<>@$%?]+)/',
                            'state' => 'required',
                            'district' => 'required|not_in:0',
//                            'ac_no' => 'required',
//                            'police_station' => 'required|not_in:0',
                            'permission_type' => 'required|not_in:0',
                            'user_type' => 'required|not_in:0',
                            'stdate' => 'required',
                            'enddate' => 'required',
							'subdate' => 'required',
                            'permsndoc.*.p_doc' => 'mimes:pdf',
                            'political_party' => 'required|not_in:0'
                        ];
                        $messages = [
                            'user_mb.required' => ' Mobile field is required.',
                            'user_mb.digits' => 'Please Enter valid Mobile Number.',
                            'user_email.required' => ' Email field is required.',
                            'user_email.email' => 'Please Enter valid Email',
                            'fathers_name.required' => 'Fathers Name is required.',
                            'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                            'user_name.required' => 'Name is required',
                            'user_name.regex' => 'Please Enter only alphanumeric character.',
                            'gender.required' => 'Gender is required Field',
                            'dob.required' => 'DOB is required',
                            'address.required' => 'Address is required',
                            'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                            'state.required' => 'State is Required field',
                            'district.required' => 'District is Required field',
//                            'ac_no.required' => 'AC Required field',
//                            'police_station.required' => 'Police Station is Required field',
                            'permission_type.required' => 'Permission Type is Required field',
                            'user_type' => 'User Type Required field',
                            'stdate.required' => 'Select start date',
                            'enddate.required' => 'Select end date',
							'subdate.required' => 'Submission date is Required',
                            'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                            'political_party.required' => 'Please Select Political Party'
                        ];
                }
                else {
                    
                    $rules = [
                        'user_mb' => 'required|numeric|digits:10',
                        'user_email' => 'required|email',
                        'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'gender' => 'required',
                        'dob' => 'required',
                        'address' => 'required|not_regex:/([<>@$%?]+)/',
                        'state' => 'required',
                        'district' => 'required|not_in:0',
                        'ac_no' => 'required',
                        'police_station' => 'required|not_in:0',
                        'permission_type' => 'required|not_in:0',
                        'location' => 'required|not_in:0',
                        'user_type' => 'required|not_in:0',
                        'stdate' => 'required',
                        'enddate' => 'required',
						'subdate' => 'required',
                        'permsndoc.*.p_doc' => 'mimes:pdf',
                        'political_party' => 'required|not_in:0'
                    ];
                    $messages = [
                        'user_mb.required' => ' Mobile field is required.',
                        'user_mb.digits' => 'Please Enter valid Mobile Number.',
                        'user_email.required' => ' Email field is required.',
                        'user_email.email' => 'Please Enter valid Email',
                        'fathers_name.required' => ' Fathers Name is required.',
                        'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                        'user_name.required' => 'Name is required',
                        'user_name.regex' => 'Please Enter only alphanumeric character.',
                        'gender.required' => 'Gender is required Field',
                        'dob.required' => 'DOB is required',
                        'address.required' => 'Address is required',
                        'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'state.required' => 'State is Required field',
                        'district.required' => 'District is Required field',
                        'ac_no.required' => 'AC is Required field',
                        'police_station.required' => 'Police Station is Required field',
                        'permission_type.required' => 'Permission Type is Required field',
                        'location' => 'Location is Required field',
                        'user_type' => 'User Type is Required field',
                        'stdate.required' => 'Select start date',
                        'enddate.required' => 'Select end date',
						'subdate.required' => 'Submission date is Required',
                        'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                        'political_party.required' => 'Please Select Political Party'
                    ];

                    if ($req->location == 'other') {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                        'user_email' => 'required|email',
                        'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'gender' => 'required',
                        'dob' => 'required',
                        'address' => 'required|not_regex:/([<>@$%?]+)/',
                        'state' => 'required',
                        'district' => 'required|not_in:0',
                        'ac_no' => 'required',
                        'police_station' => 'required|not_in:0',
                        'permission_type' => 'required|not_in:0',
                        'location' => 'required|not_in:0',
                        'user_type' => 'required|not_in:0',
                        'stdate' => 'required',
                        'enddate' => 'required',
						'subdate' => 'required',
                        'permsndoc.*.p_doc' => 'mimes:pdf',
                        'political_party' => 'required|not_in:0',
                            'other' => 'required',
                        ];
                        $messages = [
                             'user_mb.required' => ' Mobile field is required.',
                        'user_mb.digits' => 'Please Enter valid Mobile Number.',
                        'user_email.required' => ' Email field is required.',
                        'user_email.email' => 'Please Enter valid Email',
                        'fathers_name.required' => ' Fathers Name is required.',
                        'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                        'user_name.required' => 'Name is required',
                        'user_name.regex' => 'Please Enter only alphanumeric character.',
                        'gender.required' => 'Gender is required Field',
                        'dob.required' => 'DOB is required',
                        'address.required' => 'Address is required',
                        'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'state.required' => 'State is Required field',
                        'district.required' => 'District is Required field',
                        'ac_no.required' => 'AC is Required field',
                        'police_station.required' => 'Police Station is Required field',
                        'permission_type.required' => 'Permission Type is Required field',
                        'location' => 'Location is Required field',
                        'user_type' => 'User Type is Required field',
                        'stdate.required' => 'Select start date',
                        'enddate.required' => 'Select end date',
						'subdate.required' => 'Submission date is Required',
                        'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                        'political_party.required' => 'Please Select Political Party',
                            'other.required' => 'Please Enter Other location name',
                        ];
                    }
                }
                }
                else {
                    
                    $rules = [
                        'user_mb' => 'required|numeric|digits:10',
                        'user_email' => 'required|email',
                        'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'gender' => 'required',
                        'dob' => 'required',
                        'address' => 'required|not_regex:/([<>@$%?]+)/',
                        'state' => 'required',
                        'district' => 'required|not_in:0',
                        'ac_no' => 'required',
                        'police_station' => 'required|not_in:0',
                        'permission_type' => 'required|not_in:0',
                        'location' => 'required|not_in:0',
                        'user_type' => 'required|not_in:0',
                        'stdate' => 'required',
                        'enddate' => 'required',
						'subdate' => 'required',
                        'permsndoc.*.p_doc' => 'mimes:pdf',
                        'political_party' => 'required|not_in:0'
                    ];
                    $messages = [
                        'user_mb.required' => ' Mobile field is required.',
                        'user_mb.digits' => 'Please Enter valid Mobile Number.',
                        'user_email.required' => ' Email field is required.',
                        'user_email.email' => 'Please Enter valid Email',
                        'fathers_name.required' => ' Fathers Name is required.',
                        'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                        'user_name.required' => 'Name is required',
                        'user_name.regex' => 'Please Enter only alphanumeric character.',
                        'gender.required' => 'Gender is required Field',
                        'dob.required' => 'DOB is required',
                        'address.required' => 'Address is required',
                        'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'state.required' => 'State is Required field',
                        'district.required' => 'District is Required field',
                        'ac_no.required' => 'AC is Required field',
                        'police_station.required' => 'Police Station is Required field',
                        'permission_type.required' => 'Permission Type is Required field',
                        'location' => 'Location is Required field',
                        'user_type' => 'User Type is Required field',
                        'stdate.required' => 'Select start date',
                        'enddate.required' => 'Select end date',
						'subdate.required' => 'Submission date is Required',
                        'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                        'political_party.required' => 'Please Select Political Party'
                    ];

                    if ($req->location == 'other') {
                        $rules = [
                            'user_mb' => 'required|numeric|digits:10',
                        'user_email' => 'required|email',
                        'fathers_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'user_name' => 'required|regex:/(^[ A-Za-z.]+$)/',
                        'gender' => 'required',
                        'dob' => 'required',
                        'address' => 'required|not_regex:/([<>@$%?]+)/',
                        'state' => 'required',
                        'district' => 'required|not_in:0',
                        'ac_no' => 'required',
                        'police_station' => 'required|not_in:0',
                        'permission_type' => 'required|not_in:0',
                        'location' => 'required|not_in:0',
                        'user_type' => 'required|not_in:0',
                        'stdate' => 'required',
                        'enddate' => 'required',
						'subdate' => 'required',
                        'permsndoc.*.p_doc' => 'mimes:pdf',
                        'political_party' => 'required|not_in:0',
                            'other' => 'required',
                        ];
                        $messages = [
                             'user_mb.required' => ' Mobile field is required.',
                        'user_mb.digits' => 'Please Enter valid Mobile Number.',
                        'user_email.required' => ' Email field is required.',
                        'user_email.email' => 'Please Enter valid Email',
                        'fathers_name.required' => ' Fathers Name is required.',
                        'fathers_name.regex' => 'Please Enter only alphanumeric character.',
                        'user_name.required' => 'Name is required',
                        'user_name.regex' => 'Please Enter only alphanumeric character.',
                        'gender.required' => 'Gender is required Field',
                        'dob.required' => 'DOB is required',
                        'address.required' => 'Address is required',
                        'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                        'state.required' => 'State is Required field',
                        'district.required' => 'District is Required field',
                        'ac_no.required' => 'AC is Required field',
                        'police_station.required' => 'Police Station is Required field',
                        'permission_type.required' => 'Permission Type is Required field',
                        'location' => 'Location is Required field',
                        'user_type' => 'User Type is Required field',
                        'stdate.required' => 'Select start date',
                        'enddate.required' => 'Select end date',
						'subdate.required' => 'Submission date is Required',
                        'permsndoc.*.p_doc.mimes' => 'Please Upload only (.pdf) document',
                        'political_party.required' => 'Please Select Political Party',
                            'other.required' => 'Please Enter Other location name',
                        ];
                    }
                }
//                echo '<pre/>';
//                print_r($rules);die;
                $type='Nodal';
                $user_mb = strip_tags($req->user_mb);
                $user_name = strip_tags($req->user_name);
                $user_email = strip_tags($req->user_email);
                $fathers_name = strip_tags($req->fathers_name);
                $user_type = strip_tags($req->user_type);
                $gender = strip_tags($req->gender);
                $dob = date('Y-m-d', strtotime(strip_tags($req->dob)));
                $state = strip_tags($req->state);
                $district = strip_tags($req->district);
                if(!empty($req->ac_no))
                {
                  $ac = strip_tags($req->ac_no);
                }
                else
                {
                    $ac='0';
                }
                if(!empty($req->pc))
                {
                $pc=strip_tags($req->pc);
                }
                else
                {
                    $pc='0';
                }
                if(!empty($req->police_station))
                {
                    $police_station = strip_tags($req->police_station);
                }
                else
                {
                    $police_station='0';
                }
                $address = strip_tags($req->address);
                if(!empty($ptypeid[0]))
                {
                    $permission_type = strip_tags($ptypeid[0]);
                }
                if(!empty($req->location))
                {
                $location = strip_tags($req->location);
                }
                else
                {
                    $location = '0';
                }
                $party = strip_tags($req->political_party);
//                date('Y-m-d H:i:s', strtotime($date)); 
                $stdate = date('Y-m-d H:i:s', strtotime(strip_tags($req->stdate)));
                $enddate = date('Y-m-d H:i:s', strtotime(strip_tags($req->enddate)));
				$subdate = date('Y-m-d H:i:s', strtotime(strip_tags($req->subdate)));
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $other = 'NULL';
                    if (!empty($req->other)) {
                        $other = strip_tags($req->other);
                    }
                    $doc_data = $req->file('permsndoc');
                    $doc_name = '';
					
                    if (!empty($doc_data)) {
                        sort($doc_data);
                        for ($i = 0; $i < count($doc_data); $i++) {
                            if (!empty($doc_data[$i])) {
                                $doc_name .= $d->st_code . '_' . $time . '_' . $doc_data[$i]['p_doc']->getClientOriginalName() . ',';
                                $format = $d->st_code . '_' . $time . '_' . $doc_data[$i]['p_doc']->getClientOriginalName();
                                $destinationPath3 = public_path('/uploads/userdoc/permission-document/');
                                $doc_data[$i]['p_doc']->move($destinationPath3, $format);
                            }
                        }
                    }
                    $getuserloginid=DB::table('user_login')->select('id','party_id','role_id','permission_request_status')->where('mobile',$user_mb)->get()->first();
                    $getUserdata=DB::table('user_data')->where('mobileno',$user_mb)->count();
                    if (!empty($getuserloginid)) {
//                        echo 'find';die;

                        if ($getuserloginid->role_id == 0 && $getuserloginid->party_id == 0) {
                            $login_data = array('role_id' => $user_type, 'party_id' => $party, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where = array('id' => strip_tags($getuserloginid->id));
                            $insert = $this->PM->updatetable('user_login', $where, $login_data);
                        }

                        if (!empty($getUserdata) && $getUserdata > 0) {
                            if ($getuserloginid->permission_request_status == 1) {
                                $user_data = array('address' => $address, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                                $wheredata = array('user_login_id' => strip_tags($getuserloginid->id));
                                $result = $this->PM->updatetable('user_data', $wheredata, $user_data);
                            } else {
                                $user_data = array('name' => $user_name, 'fathers_name' => $fathers_name, 'email' => $user_email, 'dob' => $dob, 'address' => $address, 'state_id' => $state, 'district_id' => $district, 'ac_id' => $ac, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                                $wheredata = array('user_login_id' => strip_tags($getuserloginid->id));
                                $result = $this->PM->updatetable('user_data', $wheredata, $user_data);
                            }
                        } else {
                            $user_data = array('user_login_id' => strip_tags($getuserloginid->id), 'party_id' => $party, 'name' => $user_name, 'fathers_name' => $fathers_name, 'email' => $user_email, 'mobileno' => $user_mb, 'gender' => $gender, 'dob' => $dob, 'address' => $address, 'state_id' => $state, 'district_id' => $district, 'ac_id' => $ac, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                            $result = $this->PM->insertdata('user_data', $user_data);
                        }

                        $permission_data = array('user_id' => strip_tags($getuserloginid->id), 'party_id' => $party, 'st_code' => $state, 'dist_no' => $district, 'ac_no' => $ac,'pc_no'=>$pc ,'permission_type_id' => $permission_type, 'required_files' => $doc_name, 'location_id' => $location, 'Other_location' => $other, 'date_time_start' => $stdate, 'date_time_end' => $enddate, 'assigned_police_st_id' => $police_station, 'approved_status' => '0', 'user_created_by' => '2', 'added_at' => $subdate, 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                        $p_data = DB::table('permission_request')->insertGetId($permission_data);
                        if (!empty($p_data) && $p_data != '') {
                            $loginprequest = array('permission_request_status' => '1');
                            $wherelog = array('id' => strip_tags($getuserloginid->id));
                            $updatelog = $this->PM->updatetable('user_login', $wherelog, $loginprequest);
                            
                            
                            $data1 = DB::table('permission_type')
                                            ->select('permission_type.authority_type_id')
                                            ->where('id', $req->permission_type)
                                            ->where('st_code', $d->st_code)
                                            ->get()->toArray();
                            $allauthid = explode(',', $data1[0]->authority_type_id);
                            //print_r($nodalid);die;
                            if($ac == '0' || $ac == 'NULL')
                            {
                                $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            //->where('dist_no', $district)
                                            ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthid)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->join('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            //->where('b.dist_no', $district)
                                            ->where('b.pc_no', $pc)
                                    ->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $allauthid)
                                            ->groupBy('b.authority_masters_id','b.auth_type_id')
                                            ->get()->toArray();
                            }
                            else
                            {
                                $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            //->where('dist_no', $district)
                                            ->where('ac_no', $ac)
                                            ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthid)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->join('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            //->where('b.dist_no', $district)
                                            ->where('b.ac_no', $ac)
                                            ->where('b.pc_no', $pc)
                                    ->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $allauthid)
                                    ->groupBy('b.authority_masters_id','b.auth_type_id')
                                            ->get()->toArray();
                            }
                            $nodaldetails = array_merge($nodaldetails1,$nodaldetails2);
                         //  echo '</pre>';
                         //  print_r($nodaldetails);die;
//                            if(!empty($ptypeid))
//                            {
//                                if ($ptypeid[1] != 3 && $ptypeid[1] != 6 && $ptypeid[1] != 8) {
                                    if (!empty($nodaldetails)) {
                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                            $nodaldata = array('permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                            if (!empty($getStateName) && !empty($getDistName)) {
                                                $msg = 'New permission recived has been recived at ' . Carbon::now() . ' From ' . $getDistName->DIST_NAME . ',' . $getStateName->ST_NAME;
                                            }
                                            if (!empty($fcm_id)) {
//                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
                                                SendNotification::send_notification_fcm('New Permission Recived', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
                                            } 
                                        }
                                    }
//                                }
//                            }
                            return redirect()->back()->with('message', 'Succefully Permission applied');
                        } else {
                            return redirect()->back()->with('message', 'Permission not applied');
                        }
                    } else {
//                         echo 'notfind';die;
                        $login_data = array('name' => $user_name, 'email' => $user_email, 'party_id' => $party, 'mobile' => $user_mb, 'role_id' => $user_type, 'permission_request_status' => '1', 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));

                        $insertid = DB::table('user_login')->insertGetId($login_data);
                        if (!empty($insertid) && $insertid != '') {
                            $user_data = array('user_login_id' => $insertid, 'name' => $user_name, 'party_id' => $party, 'fathers_name' => $fathers_name, 'email' => $user_email, 'mobileno' => $user_mb, 'gender' => $gender, 'dob' => $dob, 'address' => $address, 'state_id' => $state, 'district_id' => $district, 'ac_id' => $ac, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                            $result = $this->PM->insertdata('user_data', $user_data);
                            if ($result == 1) {
                                $permission_data = array('user_id' => $insertid, 'party_id' => $party, 'st_code' => $state, 'dist_no' => $district, 'ac_no' => $ac,'pc_no'=>$pc, 'permission_type_id' => $permission_type, 'required_files' => $doc_name, 'location_id' => $location, 'Other_location' => $other, 'date_time_start' => $stdate, 'date_time_end' => $enddate, 'assigned_police_st_id' => $police_station, 'approved_status' => '0', 'user_created_by' => '2', 'added_at' => $subdate, 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                                $p_data = DB::table('permission_request')->insertGetId($permission_data);
                                if (!empty($p_data) && $p_data != '') {
                                    $loginprequest = array('permission_request_status' => '1');
                                    $wherelog = array('id' => strip_tags($insertid));
                                    $updatelog = $this->PM->updatetable('user_login', $wherelog, $loginprequest);
                                    $data1 = DB::table('permission_type')
                                                    ->select('permission_type.authority_type_id')
                                                    ->where('id', $req->permission_type)
                                                    ->where('st_code', $d->st_code)
                                                    ->get()->toArray();
                                    $allauthid = explode(',', $data1[0]->authority_type_id);
                                    //print_r($nodalid);die;
                            if($ac == '0' || $ac == 'NULL')
                            {
                                $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            //->where('dist_no', $district)
                                            ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthid)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->join('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            //->where('b.dist_no', $district)
                                            ->where('b.pc_no', $pc)
                                    ->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $allauthid)
                                    ->groupBy('b.authority_masters_id','b.auth_type_id')
                                            ->get()->toArray();
                            }
                            else
                            {
                                $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
                                            //->where('dist_no', $district)
                                            ->where('ac_no', $ac)
                                            ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthid)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->join('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
                                            //->where('b.dist_no', $district)
                                            ->where('b.ac_no', $ac)
                                            ->where('b.pc_no', $pc)
                                    ->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $allauthid)
                                    ->groupBy('b.authority_masters_id','b.auth_type_id')
                                            ->get()->toArray();
                            }
                            $nodaldetails = array_merge($nodaldetails1,$nodaldetails2);
//                                    if(!empty($ptypeid))
//                                    {
//                                        if ($ptypeid[1] != 3 && $ptypeid[1] != 6 && $ptypeid[1] != 8) {
                                            if (!empty($nodaldetails)) {
                                                for ($i = 0; $i < count($nodaldetails); $i++) {
                                                    $nodaldata = array('permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                                    $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                                    if (!empty($getStateName) && !empty($getDistName)) {
                                                $msg = 'New permission recived has been recived at ' . Carbon::now() . ' From ' . $getDistName->DIST_NAME . ',' . $getStateName->ST_NAME;
                                            }
                                            if (!empty($fcm_id)) {
//                                                        SendNotification::send_notification_fcm('Permission Assigned','You Have Assigned a Permission.',$fcm_id->fcm_id,$type,$nodaldetails[$i]->id);
                                                SendNotification::send_notification_fcm('New Permission Recived', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
                                            } 
                                                }
                                            }
//                                        }
//                                    }
                                    return redirect()->back()->with('message', 'Succefully permission applied');
                                } else {
                                    return redirect()->back()->with('message', 'Permission not applied');
                                }
                            } else {
                                return redirect()->back()->with('message', 'Some Error Occured!!!');
                            }
                        } else {
                            return redirect()->back()->with('message', 'Some Error Occured!!');
                        }
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            
//        $permission_type=DB::table('permission_type')->where('status','1')->get();
//        return view('admin.ro.Permission.ApplyOfflinePermission')->with(array('user_data'=>$d,'showpage'=>'permission','permission_type'=>$permission_type,'user_details_police'=>$user_details_police));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AllPermissionRequest() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no,$d->pc_no,$d->role_id);
//             $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->pc_no);
            return view('admin.pc.ropc.Permission.AllpermissionRequest', ['user_data' => $d], ['permissionDetails' => $permissionDetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function getpermissiondetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $id = decrypt($req->id);
             $getallpermsndetails=DB::table('permission_request')->select('approved_status','location_id','cancel_status')->where('id',$id)->get()->first();
            if(!empty($getallpermsndetails))
            {

            $getDetailsview = $this->PM->getDetails($id, $getallpermsndetails->location_id);
            if(empty($getDetailsview))
            {
                $getDetailsview = $this->PM->getIntraDetails($id, $getallpermsndetails->location_id);
            }

            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no);
//            echo '<pre/>';
//            print_r($getDetailsview);die;
             $getRodetails = $this->PM->getRodetails($id);
            $getNodaldetails = $this->PM->getNodaldetails($id,$getDetailsview[0]->ac_no,$getDetailsview[0]->pc_no,$getDetailsview[0]->dist_no);
            if ($getallpermsndetails->approved_status == 0 && $getallpermsndetails->cancel_status == 0) {
                return view('admin.pc.ropc.Permission.Permissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
            } else if ($getallpermsndetails->approved_status == 1 && $getallpermsndetails->cancel_status == 0) {
                return view('admin.pc.ropc.Permission.Permissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
            } else if ($getallpermsndetails->approved_status == 2 || $getallpermsndetails->cancel_status == 0 || $getallpermsndetails->cancel_status == 1) {
                return view('admin.pc.ropc.Permission.AcceptPermissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
            } else if ($getallpermsndetails->approved_status == 3 || $getallpermsndetails->cancel_status == 0 || $getallpermsndetails->cancel_status == 1) {
                return view('admin.pc.ropc.Permission.RejectPermissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
            }
			}
        } else {
            return redirect('/officer-login');
        }
    }

    public function UploadNodaldoc(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $time = Carbon::now()->timestamp;
            $p_id = strip_tags($req->p_req_id);
            $auth_id = strip_tags($req->auth_id);
//            if (!empty($_POST['savenodal'])) {
            $allCountAssignAuth = DB::table('permission_assigned_auth')
                            ->where('permission_request_id', $p_id)->count();
            $rules = [
                'nodal-document' => 'required|mimes:pdf',
            ];
            $messages = [
                'nodal-document.required' => 'This field is required.',
                'nodal-document.mimes' => 'Please upload only pdf documents.',
            ];
            $validator = Validator::make($req->all(), $rules, $messages);
            if ($validator->passes()) {
                // when file is selected for upload
                if ($req->hasFile('nodal-document')) {
                    $image = $req->file('nodal-document');
                    $scanPhysicalDoc = $d->st_code . '_' . $time . '_' . $image->getClientOriginalName();
                    $destinationPath3 = public_path('/uploads/Nodal-Uploaddocument/' . trim($p_id));
                    $image->move($destinationPath3, $scanPhysicalDoc);
                    $data = array('file' => $scanPhysicalDoc, 'accept_status' => 1);
                    $where = array('permission_request_id' => $p_id, 'authority_id' => $auth_id);
                    $res = $this->PM->updatetable('permission_assigned_auth', $where, $data);
                    if ($res == 1) {
                        $allCountApprove = DB::table('permission_assigned_auth')
                                ->where('permission_request_id', $p_id)
                                ->where('accept_status', '1')
                                ->count();
                        if ($allCountAssignAuth == $allCountApprove) {
                            $data = array('approved_status' => 1, 'updated_by' => $d->id);
                            $where = array('id' => $p_id);
                            $res1 = $this->PM->updatetable('permission_request', $where, $data);
                            if ($res1 == 1) {
                                return redirect()->back()->with('message', 'Successfully Uploaded');
                            } else {
                                return redirect()->back()->with('message', 'Some Error Occured!');
                            }
                        }
                        return redirect()->back()->with('message', 'Successfully Uploaded');
                    }
                }
            } else {
                return redirect()->back()->withErrors($validator, 'error')->withInput();
//                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function UpdateAction(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $p_id = strip_tags($req->p_id);
            $time = Carbon::now()->timestamp;
//            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no);
//            $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no, $d->ac_no);
            if (!empty($req->accept)) {
                $rules = [
                    'comment' => 'required',
                    'rofile' => 'required|mimes:pdf'
                ];
                $messages = [
                    'comment.required' => 'Comment field is required.',
                    'rofile.required' => 'Document is Required',
                    'rofile.mimes' => 'Please upload only pdf document'
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
//                    if ($req->ro_status == 1) {
                    $scanPhysicalDoc = 'NULL';
                    if ($req->hasFile('rofile')) {
//                            echo $req->hasFile('rofile');die;
                        $image = $req->file('rofile');
                        $scanPhysicalDoc = $d->st_code . '_' . $time . '_' . $image->getClientOriginalName();
                        $destinationPath3 = public_path('/uploads/RO-Uploaddocument/' . trim($p_id));
                        $image->move($destinationPath3, $scanPhysicalDoc);
                    }
                    $insertdata = array('permission_request_id' => $p_id, 'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'user_created_by' => '2', 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                    $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                    if ($res == 1) {
                        $data = array('approved_status' => '2', 'updated_by' => $d->id);
                        $where = array('id' => $p_id);
                        $update = $this->PM->updatetable('permission_request', $where, $data);
                        return redirect('/ropc/permission/allPermissionRequest')->with('message', 'Successfully Accepted!');
                    }
//                    } else {
//                        return redirect()->back()->with('error', 'Not Accepted by Nodals');
//                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            } else if (!empty($req->reject)) {

                $rules = [
                    'comment' => 'required',
//                    'rofile' => 'required|mimes:pdf'
                ];
                $messages = [
                    'comment.required' => 'Comment field is required.',
//                    'rofile.required' => 'Document is Required',
//                    'rofile.mimes' => 'Please upload only pdf document'
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $scanPhysicalDoc = 'NULL';
                    if ($req->hasFile('rofile')) {
                        $image = $req->file('rofile');
                        $scanPhysicalDoc = $image->getClientOriginalName();
                        $destinationPath3 = public_path('/uploads/RO-Uploaddocument/' . trim($p_id));
                        $image->move($destinationPath3, $scanPhysicalDoc);
                    }
                    $insertdata = array('permission_request_id' => $p_id, 'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                    $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                    if ($res == 1) {
                        $data = array('approved_status' => '3', 'updated_by' => $d->id);
                        $where = array('id' => $p_id);
                        $update = $this->PM->updateData('permission_request', $where, $data);
                        return redirect('/ropc/permission/allPermissionRequest')->with('message', 'Successfully Rejected!');
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            }
            else if (!empty($req->cancel)) {

                $rules = [
                    'comment' => 'required',
//                    'rofile' => 'required|mimes:pdf'
                ];
                $messages = [
                    'comment.required' => 'Comment field is required.',
//                    'rofile.required' => 'Document is Required',
//                    'rofile.mimes' => 'Please upload only pdf document'
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $scanPhysicalDoc = 'NULL';
                    if ($req->hasFile('rofile')) {
                        $image = $req->file('rofile');
                        $scanPhysicalDoc = $image->getClientOriginalName();
                        $destinationPath3 = public_path('/uploads/RO-Uploaddocument/' . trim($p_id));
                        $image->move($destinationPath3, $scanPhysicalDoc);
                    }
                    $insertdata = array('permission_request_id' => $p_id,'ro_cancel_status'=>1, 'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                    $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                    if ($res == 1) {
                        $data = array('cancel_status' => '1', 'updated_by' => $d->id);
                        $where = array('id' => $p_id);
                        $update = $this->PM->updateData('permission_request', $where, $data);
                        return redirect('/ropc/permission/allPermissionRequest')->with('message', 'Successfully Cancelled!');
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            }
            else {
                echo 'download';
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddPS() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            return view('admin.pc.ropc.Permission.AddPoliceStation')->with(array('user_data' => $d, 'showpage' => 'permission','cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddPSData(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
//            echo '<pre/>';
//            print_r($user);
//            print_r($d);die;
//            $uid=$user->id;
            if (!empty($_POST['AddPS'])) {
                $rules = [
                    'ps_name' => 'required|regex:/(^[ A-Za-z0-9]+$)/',
                    'ps_addr' => 'required|not_regex:/([<>@$%?]+)/',
                    'ps_imb' => 'required|numeric|digits:10',
                    'ps_smb' => 'required|numeric|digits:10',
                    'uname' => 'required|regex:/(^[ A-Za-z]+$)/',
                ];
                $messages = [
                    'ps_name.required' => 'Name field is required.',
                    'ps_name.regex' => 'Please Enter only alphanumeric character.',
                    'ps_addr.required' => 'Address field is required.',
                    'ps_addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'ps_imb.required' => 'PS Incharge no is required.',
                    'ps_imb.digits' => 'Please Enter valid Mobile Number.',
                    'ps_smb.required' => 'Police Staion Mobile No is required.',
                    'ps_smb.digits' => 'Please Enter valid Mobile Number.',
                    'uname.required' => 'Name field is required.',
                    'uname.regex' => 'Please Enter only alphanumeric character.',
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $ps_smb = "NULL";
                    if (!empty($_POST['ps_name']) && !empty($_POST['ps_addr']) && !empty($_POST['ps_imb'])) {
                        $ps_name = strip_tags($_POST['ps_name']);
                        $ps_addr = strip_tags($_POST['ps_addr']);
                        $ps_imb = strip_tags($_POST['ps_imb']);
                        $uname = strip_tags($_POST['uname']);
                        if (!empty($_POST['ps_smb'])) {
                            $ps_smb = strip_tags($_POST['ps_smb']);
                        }
                        $where = array('st_code' => $d->st_code, 'ac_no' => $d->ac_no, 'police_st_name' => $ps_name, 'police_station_address' => $ps_addr, 'incharge_name' => $uname);
                        $checkps = DB::table('police_station_master')->where($where)->count();
                        if ($checkps == 0) {
                            $data = array('st_code' => $d->st_code, 'ac_no' => $d->ac_no, 'incharge_name' => $uname, 'police_st_name' => $ps_name, 'police_st_incharge_no' => $ps_imb, 'police_station_no' => $ps_smb, 'police_station_address' => $ps_addr, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                            $result = $this->PM->insertdata('police_station_master', $data);
                            if ($result == 1) {
                                //                        return redirect()->back()->with('message', 'Successfully Added!');
                                return redirect('/ropc/permission/viewps')->with('message', 'Successfully Added!');
                            } else {
                                return redirect()->back()->with('message', 'Some Error Occured');
                            }
                        } else {
                            return redirect()->back()->with('chckmessage', 'Entered Police Station name is already Exist!')->withInput();
                        }
                    }
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewPS(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $getallac=$this->PM->getallAC($d->st_code,$d->pc_no);
            return view('admin.pc.ropc.Permission.ViewPoliceStaion')->with(array('user_data' => $d,'getallac'=>$getallac,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
        } else {
            return redirect('/officer-login');
        }
    }
    public function getallACPS(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $acno=$req->acid;
            $where = array('st_code' => $d->st_code, 'ac_no' => $acno);
            $getAllPSData = $this->PM->getAllPSData($where);
//            print_r($getAllPSData);die;
            return $getAllPSData;
        } else {
            return redirect('/officer-login');
        }
    }

    
    public function ViewAuthority(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $getallac=$this->PM->getallAC($d->st_code,$d->pc_no);
            $getAllAuthorityData = $this->PM->getAllAuthorityData($d->st_code, $d->dist_no, $d->ac_no);
            return view('admin.pc.ropc.Permission.ViewAuthority')->with(array('user_data' => $d,'getallac'=>$getallac,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function getallACAuthority(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $acno=$req->acid;
            $getAllAuthorityData = $this->PM->getAllACAuthorityData($d->st_code,$d->dist_no,$d->pc_no,$acno);
            return $getAllAuthorityData;
        } else {
            return redirect('/officer-login');
        }
    }

  
    public function viewaddlocation() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $getallac=$this->PM->getallAC($d->st_code,$d->pc_no);
            $getAllPermsDatas = $this->PM->getlocationmaster($d->st_code, $d->ac_no);
            //print_r($getAllPermsDatas);
            //exit;
            return view('admin.pc.ropc.Permission.viewaddlocation')->with(array('user_data' => $d,'getallac'=>$getallac,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function getallACloc(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $acno=$req->acid;
            $getAlllocDatas = $this->PM->getlocationmaster($d->st_code,$acno);
            return $getAlllocDatas;
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function getalldistrict(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $stcode=$req->stcode;
            $getAllDist = $this->PM->getAllDist($d->st_code);
            return $getAllDist;
        } else {
            return redirect('/officer-login');
        }
    }
    public function getpcalldistrict(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $stcode=$req->stcode;
            $pc=$req->pc;
            $getAllDist = DB::table('dist_pc_mapping')->select('DIST_NAME_EN','DIST_NO')->where(array('ST_CODE'=>$stcode,'PC_NO'=>$pc))->distinct('DIST_NO')->get()->toArray();
           
            return $getAllDist;
        } else {
            return redirect('/officer-login');
        }
    }

   

    public function generatePDF(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $data = ['title' => 'Welcome to HDTuto.com'];
            $id = $req->id;
            $data1 = explode('&', $id);
//            print_r($data1);
            $p_id = $data1[0];
            $locid = $data1[1];
            $getDetailsview = $this->PM->getDetails($p_id, $locid);
            if(empty($getDetailsview))
            {
                $getDetailsview = $this->PM->getIntraDetails($p_id, $locid);
            }
//            $getDetailsview = $this->PM->getDetails($p_id, $locid);
//            $getNodaldetails = $this->PM->getNodaldetails($id);
            $getRodetails = $this->PM->getRodetails($p_id);
//            print_r($getRodetails);die;
//            $pdf = PDF::loadView('admin.pc.ro.permission.PermissionDetailsPDF', ['getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails]);
            $pdf = PDF::loadView('admin.pc.ropc.Permission.Reciept', ['getDetails' => $getDetailsview, 'getRodetails' => $getRodetails]);

            return $pdf->download('mypdf.pdf');
        } else {
            return redirect('/officer-login');
        }
    }

    public function AgentCreation(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            return view('admin.pc.ropc.Permission.Agent', ['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddAgent(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            if (isset($_POST['addag'])) {


                $rules = [
                    'uname' => 'required|regex:/(^[ A-Za-z]+$)/',
//                    'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'email' => 'required|email',
                    'mb' => 'required|numeric|digits:10',
                    'pass' => 'required|min:6',
//                    'address'=>'required|not_regex:/([<>@$%?]+)/',
                ];
                $messages = [
                    'uname.required' => ' Name field is required.',
                    'uname.regex' => 'Please Enter only alphanumeric character.',
//                    'address.required' => ' Address field is required.',
//                    'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'mb.required' => ' Mobile no is required.',
                    'mb.digits' => 'Please Enter valid Mobile Number.',
//                    'dept.required' => 'Departemnt is required',
//                    'dept.regex' => 'Please Enter only alphanumeric character.',
                    'desig.required' => 'Designation is required Field',
                    'desig.regex' => 'Please Enter only alphanumeric character.',
                    'email.required' => 'Email is required',
                    'pass.required' => 'Password Field is required',
                    'pass.min' => 'Min length of password is 6',
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {

                    $randnum = rand(1, 99);
//                    $officerid = 'ROFC' . $d->st_code . $d->ac_no;
//                $pass = bcrypt($officerid);
                    if (!empty($req->desig)) {
                        $designation = strip_tags($req->desig);
                    }
                    if (!empty($req->uname)) {
                        $uname = strip_tags($req->uname);
                    }
                    if (!empty($req->mb)) {
                        $mb = strip_tags($req->mb);
                    }
                    if (!empty($req->email)) {
                        $email = strip_tags($req->email);
                    }
                    if (!empty($req->pass)) {
                        $pass = bcrypt(strip_tags($req->pass));
                    }
					$pin = bcrypt(1234);
//                if(!empty($req->dept))
//                {
//                    $department= strip_tags($req->dept);
//                }
                    $where = array('Phone_no' => $mb);
                    $chckloc = DB::table('officer_login')->where($where)->count();
                    if ($chckloc == 0) {
                        $data = array('two_step_pin'=>$pin,'officername' => $mb, 'designation' => $designation, 'name' => $uname, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'Phone_no' => $mb, 'email' => $email, 'role_id' => '22', 'officerlevel' => 'ROPCOFFICE', 'password' => $pass);
                        $result = $this->PM->insertdata('officer_login', $data);
                        if ($result == 1) {
                            return redirect('/ropc/permission/viewagent')->with('message', 'Successfully Created');
                        } else {
                            return redirect()->back()->with('message', 'Not Created');
                        }
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered  mobile no is already Exist!')->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            }
//            return view('admin.pc.ro.permission.Agent', ['user_data' => $d]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewAgent(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'pc_no' => $d->pc_no);
            $getAgentList = $this->PM->getAgentList($where);
            return view('admin.pc.ropc.Permission.ViewAgentList', ['user_data' => $d], ['getAgentList' => $getAgentList,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditAgent(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            if (isset($_POST['editag'])) {
                $rules = [
                    'uname' => 'required|regex:/(^[ A-Za-z]+$)/',
//                    'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'email' => 'required|email',
                    'mb' => 'required|numeric|digits:10',
//                    'pass'=>'required|min:6',
//                    'address'=>'required|not_regex:/([<>@$%?]+)/',
                ];
                $messages = [
                    'uname.required' => ' Name field is required.',
                    'uname.regex' => 'Please Enter only alphanumeric character.',
//                    'address.required' => ' Address field is required.',
//                    'address.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'mb.required' => ' Mobile no is required.',
                    'mb.digits' => 'Please Enter valid Mobile Number.',
//                    'dept.required' => 'Departemnt is required',
//                    'dept.regex' => 'Please Enter only alphanumeric character.',
                    'desig.required' => 'Designation is required Field',
                    'desig.regex' => 'Please Enter only alphanumeric character.',
                    'email.required' => 'Email is required',
//                    'pass.required'=>'Password Field is required',
//                    'pass.min'=>'Min length of password is 6',
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    if (!empty($req->desig)) {
                        $designation = strip_tags($req->desig);
                    }
                    if (!empty($req->uname)) {
                        $uname = strip_tags($req->uname);
                    }
                    if (!empty($req->mb)) {
                        $mb = strip_tags($req->mb);
                    }
                    if (!empty($req->email)) {
                        $email = strip_tags($req->email);
                    }
//                if(!empty($req->pass))
//                {
//                    $pass= bcrypt(strip_tags($req->pass));
//                }
//                if(!empty($req->dept))
//                {
//                    $department= strip_tags($req->dept);
//                }
                    $where = array('Phone_no' => $mb);
                    $chckloc = DB::table('officer_login')->where($where)->count();
                    if ($chckloc == 0) {
//                        echo '1';die;
                        $data = array('designation' => $designation, 'name' => $uname, 'Phone_no' => $mb, 'email' => $email);
                        $where = array('id' => $req->id, 'role_id' => $req->role_id);
                        $update = $this->PM->updatetable('officer_login', $where, $data);
                        return redirect()->back()->with('message', 'Successfully Updated');
                    } else {
                        $data = array('designation' => $designation, 'name' => $uname,'email' => $email);
                        $where = array('id' => $req->id, 'role_id' => $req->role_id);
                        $update = $this->PM->updatetable('officer_login', $where, $data);
                        return redirect()->back()->with('message', 'Successfully Updated');
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            } else {
                $getAgentDetails = $this->PM->getAgentDetails($req->id);
//                print_r($getAgentDetails);die;
                return view('admin.pc.ropc.Permission.EditAgentList', ['user_data' => $d], ['getAgentList' => $getAgentDetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details]);
            }
        } else {
            return redirect('/officer-login');
        }
    }

    // map integration

    public function getlocationList(Request $request) {
        $state = $request->input('stcode');
        $ac = $request->input('ac');
        $getACLists = DB::table('location_master')->where('ST_CODE', $state)
                ->where('AC_NO', '=', $ac)
                ->get();
        return json_encode($getACLists);
    }

    public function getlatlongs(Request $request) {
        $locationid = $request->input('locationid');
        $locationdetails = DB::table('location_master')->where('id', $locationid)
                ->get();
        return json_encode($locationdetails);
    }

    public function EditAgentStatus(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $data = explode('#', $req->status);
            $status = $data[0];
            $id = $data[1];
            if ($status == 1) {
                $where = array('id' => $id, 'role_id' => '21');
                $cond = array('is_active' => '0');
                $res = $this->PM->updatetable('officer_login', $where, $cond);
                if ($res == 1) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $where = array('id' => $id, 'role_id' => '21');
                $cond = array('is_active' => '1');
                $res = $this->PM->updatetable('officer_login', $where, $cond);
                if ($res == 1) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionCount() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $check_finalize = candidate_finalizebyro($ele_details->ST_CODE, $ele_details->CONST_NO, $ele_details->CONST_TYPE);
            $seched = getschedulebyid($ele_details->ScheduleID);
            $sechdul = checkscheduledetails($seched);
            $where = array($d->st_code,$d->pc_no);
            $allrecord = $this->PM->totalPermissionReport($d->st_code,$d->pc_no);
            $where1 = array($d->st_code, $d->dist_no, $d->pc_no);
            $totalPermissionReport = $this->PM->totalPermissionReportData($where1);
//           echo '<pre/>';
//           print_r($totalPermissionReport);die;
            return view('admin.pc.ropc.Permission.PermissionReport', ['user_data' => $d, 'cand_finalize_ceo' => $check_finalize->finalize_by_ceo, 'cand_finalize_ro' => $check_finalize->finalized_ac, 'sechdul' => $sechdul, 'allrecord' => $allrecord, 'totalPermissionReport' => $totalPermissionReport]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionCountDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $where = array('st_code' => $d->st_code,  'pc_no' => $d->pc_no);

            $allrecord = $this->PM->totalPermissionReport($d->st_code,$d->pc_no);
            $where1 = array($d->st_code, $d->pc_no);
            if ($req->statusid != 'NULL') {
                if ($req->statusid == '22') {
                    $totalReportDetails = $this->PM->totalPermissionReportData($where1);
                } else if ($req->statusid == '01') {
                    $totalReportDetails = $this->PM->totalPendingReportDetails($where1);
                } else {
                    $totalReportDetails = $this->PM->totalReportDetails($where1, $req->statusid);
                }
                return $totalReportDetails;
//                return view('admin.pc.ro.Permission.AllPendingReport', ['user_data' => $d,'allrecord'=>$allrecord,'totalReportDetails'=>$totalReportDetails]);
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionDetailsView(Request $req) {
//         echo 'ok';die;
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $id = $req->id;
            $loc_id = $req->loc_id;
            
            $getRodetails = $this->PM->getRodetails1($id, $req->status);
//        print_r($getRodetails);die;
            $getDetailsview = $this->PM->getDetails($id, $loc_id);
            if(empty($getDetailsview))
            {
                $getDetailsview = $this->PM->getIntraDetails($id, $loc_id);
            }
            if(!empty($getDetailsview))
            {
                $getNodaldetails = $this->PM->getNodaldetails($id,$getDetailsview[0]->ac_no,$getDetailsview[0]->pc_no,$getDetailsview[0]->dist_no);
            }
            return view('admin.pc.ropc.Permission.ReportView')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview,'getNodaldetails' => $getNodaldetails,'getRodetails' => $getRodetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
//            return view('admin.pc.ropc.Permission.AcceptPermissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details));
        } else {
            return redirect('/officer-login');
        }
    }

    public function ManualForward() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no, $d->ac_no);
            return view('admin.pc.ropc.Permission.ManualForward', ['user_data' => $d, 'showpage' => 'permission', 'permissionDetails' => $permissionDetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function ManualForwardDownload(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
            $seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);
            $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no, $d->ac_no);
            $user_doc = DB::table('permission_request')->select('required_files', 'id', 'approved_status')->where('id', $req->id)->get()->toArray();
            if ($user_doc[0]->approved_status == 0) {
                $doc = $user_doc[0]->required_files;
                $docarray = explode(',', $doc);
                // Zip File Name
                $zipFileName = $req->id . '_AllDocuments.zip';
                $public_dir = public_path() . '/uploads/manual_forward/' . $req->id;

                if (!File::exists($public_dir)) {
                    File::makeDirectory($public_dir, 0777, true); // path does not exist
                }

                $path = public_path();

                // Create ZipArchive Obj
                $zip = new ZipArchive;
                if (!file_exists($path . "/uploads/manual_forward/" . $req->id . '/' . $zipFileName)) {
                    if ($zip->open($public_dir . '/' . $zipFileName, ZipArchive::CREATE) === TRUE) {

                        foreach ($docarray as $file) {
                            if ($file != "" && file_exists($path . "/uploads/userdoc/permission_document/" . $file)) {
                                $zip->addFile($path . "/uploads/userdoc/permission_document/" . $file, $file);
                            }
                        }
                        // Close ZipArchive     
                        $zip->close();
                    }
//            
                }
                // Set Header
                $headers = array(
                    'Content-Type' => 'application/octet-stream',
                );
                $filetopath = $public_dir . '/' . $zipFileName;
//            
                // Create Download Response
                if (file_exists($filetopath)) {
                    $updatedata = array('approved_status' => 4);
                    $where = array('id' => $req->id);
                    $res = $this->PM->updatetable('permission_request', $where, $updatedata);
                    return response()->download($filetopath, $zipFileName, $headers);
                    $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no, $d->ac_no);
                    return view('admin.pc.ropc.Permission.ManualForward', ['user_data' => $d, 'showpage' => 'permission', 'permissionDetails' => $permissionDetails,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'sched'=>$seched,'ele_details'=>$ele_details]);
                } else {
                    return redirect()->back()->with('message', 'No document availabe');
                }
            } else {
                return redirect()->back()->with('message', 'Already action taken');
            }
        } else {
            return redirect('/officer-login');
        }
    }

}
