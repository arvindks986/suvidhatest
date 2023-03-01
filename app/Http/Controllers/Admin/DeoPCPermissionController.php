<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\adminmodel\DeoPcPermissionModel;
use Illuminate\Http\Request;
use Session;
use DB;
use App\commonModel;
use App\adminmodel\CandidateModel;
use App\adminmodel\ROPCModel;
use App\Classes\xssClean;
use PDF;
use Carbon\Carbon;
use App\Helpers\SendNotification;
class DeoPCPermissionController extends Controller {

    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('deo');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->PM = new DeoPcPermissionModel();
    }
    
    
     public function AgentCreation(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
          
            return view('admin.pc.deo.Permission.Agent', ['user_data' => $d]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddAgent(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
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
                        $data = array('two_step_pin'=>$pin,'officername' => $mb, 'designation' => $designation, 'name' => $uname, 'st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'Phone_no' => $mb, 'email' => $email, 'role_id' => '24', 'officerlevel' => 'DEO-OFFICE', 'password' => $pass);
                        $result = $this->PM->insertdata('officer_login', $data);
                        if ($result == 1) {
                            return redirect('/pcdeo/viewagent')->with('message', 'Successfully Created');
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
            $where = array('st_code' => $d->st_code,'dist_no'=>$d->dist_no);
            $getAgentList = $this->PM->getAgentList($where);
            return view('admin.pc.deo.Permission.ViewAgentList', ['user_data' => $d], ['getAgentList' => $getAgentList]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditAgent(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
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
                    $data = array('designation' => $designation, 'name' => $uname, 'Phone_no' => $mb, 'email' => $email);
                    $where = array('id' => $req->id, 'role_id' => $req->role_id);
                    $update = $this->PM->updatetable('officer_login', $where, $data);
                    return redirect()->back()->with('message', 'Successfully Updated');
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered  mobile no is already Exist!')->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            } else {
                $getAgentDetails = $this->PM->getAgentDetails($req->id);
//                print_r($getAgentDetails);die;
                return view('admin.pc.deo.Permission.EditAgentList', ['user_data' => $d], ['getAgentList' => $getAgentDetails]);
            }
        } else {
            return redirect('/officer-login');
        }
    }
     public function EditAgentStatus(Request $req) {
         if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $data = explode('#', $req->status);
            $status = $data[0];
            $id = $data[1];
            if ($status == 1) {
                $where = array('id' => $id, 'role_id' => '24');
                $cond = array('is_active' => '0');
                $res = $this->PM->updatetable('officer_login', $where, $cond);
                if ($res == 1) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $where = array('id' => $id, 'role_id' => '24');
                $cond = array('is_active' => '1');
                $res = $this->PM->updatetable('officer_login', $where, $cond);
                if ($res == 1) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } 
        else {
            return redirect('/officer-login');
        }
    }
    
    
    public function PermissionCount() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
             
//                $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
//                $seched=getschedulebyid($ele_details->ScheduleID);
//                $sechdul=checkscheduledetails($seched);
//                echo '<pre/>';
//                print_r($d);die;
            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no);
            $allrecord = $this->PM->totalPermissionReport($d->st_code,$d->dist_no);
             $where1 = array($d->st_code, $d->dist_no);
           $totalPermissionReport = $this->PM->totalPermissionReportData($where1);
           $getallac=DB::table('m_ac')->select('AC_NO','AC_NAME')->where('ST_CODE',$d->st_code)->where('DIST_NO_HDQTR',$d->dist_no)->get()->toArray();
//           echo '<pre/>';
//           print_r($totalPermissionReport);die;
            return view('admin.pc.deo.Permission.PermissionReport', ['user_data' => $d,'allrecord' => $allrecord,'totalPermissionReport'=>$totalPermissionReport,'getallac'=>$getallac]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function PermissionCountDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no);
           
            $allrecord = $this->PM->totalPermissionReport($d->st_code,$d->dist_no);
            $where1 = array($d->st_code, $d->dist_no);
            if ($req->statusid != 'NULL') {
                if($req->statusid == '22')
                {
                    $totalReportDetails=$this->PM->totalPermissionReportData($where1);
                
                }
                else if($req->statusid == '01')
                {
                    $totalReportDetails=$this->PM->totalPendingReportDetails($where1);
                }
                else
                {
                    $totalReportDetails = $this->PM->totalReportDetails($where1, $req->statusid);
                     
                }
                return $totalReportDetails;
//                return view('admin.pc.ro.Permission.AllPendingReport', ['user_data' => $d,'allrecord'=>$allrecord,'totalReportDetails'=>$totalReportDetails]);
            }
        } else {
            return redirect('/officer-login');
        }
    }

     public function PermissionDetailsView(Request $req)
    {
//         echo 'ok';die;
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
        $id=$req->id;
        $loc_id=$req->loc_id;
        $getNodaldetails = $this->PM->getNodaldetails($id);
        $getRodetails = $this->PM->getRodetails1($id,$req->status);
//        print_r($getRodetails);die;
//        $getDetailsview = $this->PM->getDetails($id,$loc_id);
        $getDetailsview = $this->PM->getDetails($id, $loc_id);
             if(empty($getDetailsview))
             {
                 $getDetailsview = $this->PM->getIntraDetails($id,$loc_id);
             }
        return view('admin.pc.deo.Permission.ReportView')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails));
        } else {
            return redirect('/officer-login');
        } 
    }
    
    public function generatePDF(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $data = ['title' => 'Welcome to HDTuto.com'];
            $id= $req->id;
            $data1= explode('&',$id);
//            print_r($data1);
            $p_id=$data1[0];
            $status=$data1[1];
//            echo $p_id.'/'.$status;die;
            $prmsndetails=DB::table('permission_request')->select('ac_no','pc_no','dist_no')->where('id',$p_id)->first();
             $getDetailsview = $this->PM->getDetails($p_id,$status);
             if(empty($getDetailsview) && !empty($prmsndetails->pc_no))
             {
                 $getDetailsview = $this->PM->getIntraDetails($p_id,$status);
             }
             else
             {
                 $getDetailsview = $this->PM->getIntradistDetails($p_id,$status);
             }
//            $getDetailsview = $this->PM->getDetails($p_id,$status);
//            $getNodaldetails = $this->PM->getNodaldetails($id);
            $getRodetails = $this->PM->getRodetails($p_id);
//            print_r($getDetailsview);die;
//            $pdf = PDF::loadView('admin.pc.ro.permission.PermissionDetailsPDF', ['getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails]);
            $pdf = PDF::loadView('admin.pc.deo.Permission.Reciept',['getDetails'=>$getDetailsview,'getRodetails'=>$getRodetails]);

            return $pdf->download('mypdf.pdf');
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function GetAllACPermission(Request $req)
    {
       if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel); 
            $acid= $req->acid;
            $allrecord=$this->PM->getAllAcRecord($d->st_code,$acid,$d->dist_no);
            return $allrecord;
            
             } else {
            return redirect('/officer-login');
        }
    }
    
    
    //All access
     public function allMasters() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            return view('admin.pc.deo.Permission.Masters', ['user_data' => $d]);
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

            $getrodetails = $this->PM->getLoginUserdetails($d->id);
 
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
				if($d->role_id != 24)
				{
                $permission_type = DB::table('permission_type as a')
                                ->join('permission_master as m', 'm.id', '=', 'a.permission_type_id')
                                ->select('m.permission_name as pname', 'a.*', 'a.id as permsn_id', 'm.officer_role_id')
                                ->where('a.status', '1')
                                ->where('role_id',$d->role_id)
                                ->where('a.st_code', $d->st_code)->get()->toArray();
				}
				else{
					$permission_type = DB::table('permission_type as a')
                                ->join('permission_master as m', 'm.id', '=', 'a.permission_type_id')
                                ->select('m.permission_name as pname', 'a.*', 'a.id as permsn_id', 'm.officer_role_id')
                                ->where('a.status', '1')
                                ->where('role_id',5)
                                ->where('a.st_code', $d->st_code)->get()->toArray();
				}
                $getAllUserType = $this->PM->getAllUserType();
                $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
                $allParty = DB::table('m_party')->select('*')->orderBy('PARTYNAME')->get()->toArray();
                return view('admin.pc.deo.Permission.OfflinePermissionApply')->with(array('user_data' => $d, 'getrodetails' => $getrodetails, 'showpage' => 'permission', 'permission_type' => $permission_type,'getAllUserType' => $getAllUserType,'allParty' => $allParty,'getAllPC'=>$getAllPC));
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function getUserDetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
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
            
            $time = Carbon::now()->timestamp;
            $rules=[];
            $ptypeid=[];
            $messages=[];
//            echo '<pre/>';
//            print_r($req->all());die;
            $getDistName = getdistrictbydistrictno($d->st_code, $d->dist_no);
            $getStateName = getstatebystatecode($d->st_code);
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
                            'district' => 'required',
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
                            'district' => 'required',
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
                        'district' => 'required',
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
                        'district' => 'required',
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
                        'district' => 'required',
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
                        'district' => 'required',
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
                        for ($i = 0; $i <= count($doc_data); $i++) {
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

                        $permission_data = array('user_id' => strip_tags($getuserloginid->id), 'party_id' => $party, 'st_code' => $state, 'dist_no' => $district, 'ac_no' => $ac,'pc_no'=>$pc ,'permission_type_id' => $permission_type, 'required_files' => $doc_name, 'location_id' => $location, 'Other_location' => $other, 'date_time_start' => $stdate, 'date_time_end' => $enddate, 'assigned_police_st_id' => $police_station, 'approved_status' => '0', 'user_created_by' => '2', 'added_at' =>$subdate, 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
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
//                                            ->where('dist_no', $district)
                                            ->where('pc_no', $pc)
                                            ->whereIn('auth_type_id', $allauthid)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
//                                            ->where('b.dist_no', $district)
                                            ->where('b.pc_no', $pc)
                                    ->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $allauthid)
                                    ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                            }
                            else
                            {
                                    $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
//                                            ->where('dist_no', $district)
                                            ->where('pc_no', $pc)
                                            ->where('ac_no', $ac)
                                            ->whereIn('auth_type_id', $allauthid)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('b.ac_no', $ac)
//                                            ->where('b.dist_no', $district)
                                            ->where('b.pc_no', $pc)
                                    ->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $allauthid)
                                    ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                            }
                            $nodaldetails = array_merge($nodaldetails1,$nodaldetails2);
//                            echo '</pre>';
//                            print_r($nodaldetails);die;
//                            if(!empty($ptypeid))
//                            {
//                                if ($ptypeid[1] != 3 && $ptypeid[1] != 6 && $ptypeid[1] != 8) {
                                    if (!empty($nodaldetails)) {
                                        for ($i = 0; $i < count($nodaldetails); $i++) {
                                            $nodaldata = array('permission_request_id' => $p_data, 'authority_id' => $nodaldetails[$i]->id, 'accept_status' => 0, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                                            $insert = DB::table('permission_assigned_auth')->insert($nodaldata);
                                             $fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();
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
                            return redirect()->back()->with('message', 'Successfully Permission applied');
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
                                $permission_data = array('user_id' => $insertid, 'party_id' => $party, 'st_code' => $state, 'dist_no' => $district, 'ac_no' => $ac,'pc_no'=>$pc, 'permission_type_id' => $permission_type, 'required_files' => $doc_name, 'location_id' => $location, 'Other_location' => $other, 'date_time_start' => $stdate, 'date_time_end' => $enddate, 'assigned_police_st_id' => $police_station, 'approved_status' => '0', 'user_created_by' => '2', 'added_at' =>$subdate, 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
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
//                                            ->where('dist_no', $district)
                                            ->where('pc_no', $d->pc_no)
                                            ->whereIn('auth_type_id', $allauthid)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $state)
//                                            ->where('b.dist_no', $district)
                                            ->where('b.pc_no', $d->pc_no)
                                    ->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $allauthid)
                                    ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                            }
                            else
                            {
                                    $nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $state)
//                                            ->where('dist_no', $district)
                                            ->where('pc_no', $d->pc_no)
                                            ->where('ac_no', $ac)
                                            ->whereIn('auth_type_id', $allauthid)
                                            ->get()->toArray();
                            $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('b.ac_no', $ac)
//                                            ->where('b.dist_no', $district)
                                            ->where('b.pc_no', $d->pc_no)
                                    ->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $allauthid)
                                    ->groupBy('b.authority_masters_id')
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
                                    return redirect()->back()->with('message', 'Successfully permission applied');
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
            if($d->st_code == 'U01' || $d->st_code == 'U02' || $d->st_code == 'U03' || $d->st_code == 'U04' || $d->st_code == 'U05' || $d->st_code == 'U06' || $d->st_code == 'U07' || $d->st_code == 'S16' )
            {
                    $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no,$d->role_id);
            }
            else
            {
                $permissionDetails = $this->PM->getintraPermissionDetails($d->st_code, $d->dist_no,$d->role_id);
            }
            return view('admin.pc.deo.Permission.AllpermissionRequest', ['user_data' => $d], ['permissionDetails' => $permissionDetails]);
        } else {
            return redirect('/officer-login');
        }
    }

    public function getpermissiondetails(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $id = decrypt($req->id);
             $getallpermsndetails=DB::table('permission_request')->select('approved_status','location_id','cancel_status')->where('id',$id)->get()->first();
            if(!empty($getallpermsndetails))
            {
            $prmsndetails=DB::table('permission_request')->select('ac_no','pc_no','dist_no')->where('id',$id)->first();
             $getDetailsview = $this->PM->getDetails($id, $getallpermsndetails->location_id);
             if(empty($getDetailsview) && !empty($prmsndetails->pc_no))
             {
                 $getDetailsview = $this->PM->getIntraDetails($id, $getallpermsndetails->location_id);
             }
             else
             {
                 $getDetailsview = $this->PM->getIntradistDetails($id, $getallpermsndetails->location_id);
             }
            $getRodetails = $this->PM->getRodetails($id);
            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no);
            $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no, $d->ac_no);
            $getNodaldetails = $this->PM->getNodaldetails($id);
            if ($getallpermsndetails->approved_status == 0 && $getallpermsndetails->cancel_status == 0) {
                return view('admin.pc.deo.Permission.Permissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'getRodetails' => $getRodetails));
            } else if ($getallpermsndetails->approved_status == 1 && $getallpermsndetails->cancel_status == 0) {
                return view('admin.pc.deo.Permission.Permissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails,'getRodetails' => $getRodetails));
            } else if ($getallpermsndetails->approved_status == 2 || $getallpermsndetails->cancel_status == 1 || $getallpermsndetails->cancel_status == 0) {
                
                return view('admin.pc.deo.Permission.AcceptPermissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails));
            } else if ($getallpermsndetails->approved_status == 3 || $getallpermsndetails->cancel_status == 1 || $getallpermsndetails->cancel_status == 0) {
                return view('admin.pc.deo.Permission.RejectPermissiondetails')->with(array('user_data' => $d, 'showpage' => 'permission', 'getDetails' => $getDetailsview, 'getNodaldetails' => $getNodaldetails, 'getRodetails' => $getRodetails));
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
            $p_id = strip_tags($req->p_id);
            $time = Carbon::now()->timestamp;
//            $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no);
            $permissionDetails = $this->PM->getPermissionDetails($d->st_code, $d->dist_no, $d->ac_no);
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
                        return redirect('/pcdeo/allPermissionRequest')->with('message', 'Successfully Accepted!');
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
                        $update = $this->PM->updatetable('permission_request', $where, $data);
                        return redirect('/pcdeo/allPermissionRequest')->with('message', 'Successfully Rejected!');
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
                    $insertdata = array('permission_request_id' => $p_id,'ro_cancel_status'=>1,'comment' => strip_tags($req->comment), 'file' => $scanPhysicalDoc, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $d->id);
                    $res = $this->PM->insertdata('permission_request_comment', $insertdata);
                    if ($res == 1) {
                        $data = array('cancel_status' => '1', 'updated_by' => $d->id);
                        $where = array('id' => $p_id);
                        $update = $this->PM->updatetable('permission_request', $where, $data);
                        return redirect('/pcdeo/allPermissionRequest')->with('message', 'Successfully Cancelled!');
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
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
//            $getAllAC=$this->PM->getAllAC($d->dist_no);
            return view('admin.pc.deo.Permission.AddPoliceStation')->with(array('user_data' => $d,'getAllPC'=>$getAllPC));
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function getAllAC(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $pcno=$req->pc_id;
            $getAllAC=$this->PM->getAllAC($pcno,$d->st_code,$d->dist_no);
            return $getAllAC;
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddPSData(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
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
                    'acno' => 'required|not_in:0',
                    'pc' => 'required|not_in:0',
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
                    'acno.required'=>'Please select Ac',
                    'acno.not_in' =>'Please select AC',
                    'pc.required' =>'Please select PC',
                    'pc.not_in' =>'Please select PC',
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
                        if(!empty($_POST['acno']))
                        {
                            $acno=$_POST['acno'];
                        }
                        else
                        {
                            $acno = 0;
                        }
                        if (!empty($_POST['ps_smb'])) {
                            $ps_smb = strip_tags($_POST['ps_smb']);
                        }
                        $where = array('st_code' => $d->st_code, 'ac_no' =>$acno, 'police_st_name' => $ps_name, 'police_station_address' => $ps_addr, 'incharge_name' => $uname);
                        $checkps = DB::table('police_station_master')->where($where)->count();
                        if ($checkps == 0) {
                            $data = array('st_code' => $d->st_code, 'ac_no' =>$acno, 'incharge_name' => $uname, 'police_st_name' => $ps_name, 'police_st_incharge_no' => $ps_imb, 'police_station_no' => $ps_smb, 'police_station_address' => $ps_addr, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                            $result = $this->PM->insertdata('police_station_master', $data);
                            if ($result == 1) {
                                //                        return redirect()->back()->with('message', 'Successfully Added!');
                                return redirect('/pcdeo/viewps')->with('message', 'Successfully Added!');
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
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
            return view('admin.pc.deo.Permission.ViewPoliceStaion')->with(array('user_data' => $d, 'getAllPC' => $getAllPC));
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
            
            $acno=$req->acid;
            $where = array('st_code' => $d->st_code, 'ac_no' => $acno);
            $getAllPSData = $this->PM->getAllPSData($where);
            return $getAllPSData;
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditPS(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            if (!empty($_POST['UpdatePS'])) {
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

                $validator = Validator::make($request->all(), $rules, $messages);
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
                            $data = array('incharge_name' => $uname, 'police_st_name' => $ps_name, 'police_st_incharge_no' => $ps_imb, 'police_station_no' => $ps_smb, 'police_station_address' => $ps_addr, 'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where = array('id' => $_POST['psid']);
                            $result = $this->PM->updatetable('police_station_master', $where, $data);
//                        if ($result == 1) {
                            return redirect()->back()->with('message', 'Successfully Updated!');
                        } else {
                            $data = array('incharge_name' => $uname,'police_st_incharge_no' => $ps_imb, 'police_station_no' => $ps_smb, 'modified_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $where = array('id' => $_POST['psid']);
                            $result = $this->PM->updatetable('police_station_master', $where, $data);
                            ///return redirect()->back()->with('chckmessage', 'Entered Police Station name is already Exist!')->withInput();
                            return redirect()->back()->with('message', 'Successfully Updated!');
                        }

//                        } else {
//                            return redirect()->back()->with('message', 'Some Error Occured');
//                        }
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error');
                }
            } else {
                $p_id = $request->id;
                $getpsdetails = $this->PM->getpsdetails($p_id);
                return view('admin.pc.deo.Permission.EditPoliceStation')->with(array('user_data' => $d, 'showpage' => 'permission', 'getpsdetails' => $getpsdetails));
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    //Autority Master
    public function AddAuthority() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $authority = $this->PM->getAuthority($d->st_code);
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
//            print_r($d);die;
            return view('admin.pc.deo.Permission.AddAuthority')->with(array('user_data' => $d, 'getAllPC' => $getAllPC, 'authority' => $authority));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddAuthorityData(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
//            echo '<pre/>';
//            print_r($request->all());die;
            $rules = [
                'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                'email' => 'required|email',
                'addr' => 'required|not_regex:/([<>@$%?]+)/',
                'mb' => 'required|numeric|digits:10',
//                'eno' => 'required|numeric|digits:16',
                'authid' => 'required|not_in:0',
                'acno' => 'required|not_in:0',
                'pc' => 'required|not_in:0'
            ];
            $messages = [
                'name.required' => ' Name field is required.',
                'name.regex' => 'Please Enter only alphanumeric character.',
                'addr.required' => ' Address field is required.',
                'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                'mb.required' => ' Mobile no is required.',
                'mb.digits' => 'Please Enter valid Mobile Number.',
                'dept.required' => 'Departemnt is required',
                'dept.regex' => 'Please Enter only alphanumeric character.',
                'desig.required' => 'Designation is required Field',
                'desig.regex' => 'Please Enter only alphanumeric character.',
                'email.required' => 'Email is required',
//                'eno.required' => 'Epic No is required',
//                'eno.digits' => 'Epic number must be of 16 digits',
                'authid.required' => 'Select Approving Authority',
                'acno.required'=>'Please select Ac',
                'acno.not_in' =>'Please select AC',
                'pc.required' =>'Please select PC',
                'pc.not_in' =>'Please select PC',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->passes()) {
                if (!empty($request->authid)) {
                    $authid = strip_tags($request->authid);
                }
                if (!empty($request->name)) {
                    $name = strip_tags($request->name);
                }
                if (!empty($request->dept)) {
                    $dept = strip_tags($request->dept);
                }
                if (!empty($request->desig)) {
                    $desig = strip_tags($request->desig);
                }
                if (!empty($request->mb)) {
                    $mb = strip_tags($request->mb);
                }
                if (!empty($request->email)) {
                    $email = strip_tags($request->email);
                }
                if (!empty($request->addr)) {
                    $addr = strip_tags($request->addr);
                }
                if (!empty($request->pc)) {
                    $pc = strip_tags($request->pc);
                }
                if (!empty($request->acno)) {
                    $ac = strip_tags($request->acno);
                }
                 $checkAuthmb = DB::table('authority_masters')->where('mobile', $mb)->count();
                if ($checkAuthmb == 0) {
                    $data = array('st_code' => $d->st_code, 'name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'created_by' => $d->id, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                    $result = DB::table('authority_masters')->insertGetId($data);
                    $mapdata = array('authority_masters_id' => $result, 'dist_no' => $d->dist_no, 'ac_no' =>$ac, 'pc_no' => $pc, 'auth_type_id' => $authid, 'created_by' => $d->id,);
                    $mapresult = $this->PM->insertdata('authority_masters_mapping', $mapdata);
                    if (!empty($result) && !empty($mapresult)) {
                        return redirect('/pcdeo/viewauthority')->with('message', 'Successfully Added');
                    } else {
                        return redirect()->back()->with('message', 'Some error occured');
                    }
                } else {
                    $chckexistuser = DB::table('authority_masters_mapping as a')
                            ->join('authority_masters as b','b.id','=','a.authority_masters_id')
                            ->where(array('a.dist_no' => $d->dist_no, 'a.ac_no' => $ac, 'a.pc_no' => $pc, 'a.auth_type_id' => $authid,'b.mobile'=>$mb))->count();
                  $chckexistuser1 = DB::table('authority_masters')->where(array('dist_no' => $d->dist_no, 'ac_no' => $ac, 'pc_no' => $pc, 'auth_type_id' => $authid,'mobile'=>$mb))->count();
//                  echo $chckexistuser.'#'. $chckexistuser1;die;
//                    print_r(array('dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'auth_type_id' => $authid));die;
                    if ($chckexistuser == 0 && $chckexistuser1 == 0) {
                        $getauthid = DB::table('authority_masters')->select('id')->where('mobile', $mb)->first();
                        if (!empty($getauthid)) {
                            $mapdata = array('authority_masters_id' => $getauthid->id, 'dist_no' => $d->dist_no, 'ac_no' => $ac, 'pc_no' => $pc, 'auth_type_id' => $authid, 'created_by' => $d->id,);
                            $mapresult = $this->PM->insertdata('authority_masters_mapping', $mapdata);
                            if (!empty($mapresult)) {
                                return redirect('/pcdeo/viewauthority')->with('message', 'Successfully Added');
                            } else {
                                return redirect()->back()->with('message', 'Some error occured');
                            }
                        }
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered Authority is already Exist!')->withInput();
                    }
                }
            } else {
                return redirect()->back()->withErrors($validator, 'error')->withInput();
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function ViewAuthority(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
//            $where = array($d->st_code,$d->dist_no,$d->ac_no);
//            $getAllAuthorityData = $this->PM->getAllAuthorityData($d->st_code, $d->dist_no, $d->ac_no);
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
            return view('admin.pc.deo.Permission.ViewAuthority')->with(array('user_data' => $d, 'getAllPC' =>$getAllPC));
        } else {
            return redirect('/officer-login');
        }
    }
    
     public function getallACAuthority(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $acno=$req->acid;
            $pc=$req->pcid;
//            $getAllAuthorityData = $this->PM->getAllACAuthorityData($d->st_code,$acno);
            $getAllAuthorityData = $this->PM->getAllACAuthorityData1($d->id,$d->st_code,$acno,$d->dist_no,$pc);
            return $getAllAuthorityData;
        } else {
            return redirect('/officer-login');
        }
    }

    public function EditAuthority(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            if (!empty($_POST['submit'])) {
                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'dept' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'desig' => 'required|regex:/(^[ A-Za-z]+$)/',
                    'email' => 'required|email',
                    'addr' => 'required|not_regex:/([<>@$%?]+)/',
                    'mb' => 'required|numeric|digits:10',
//                    'eno' => 'required|numeric|digits:16',
                    'authid' => 'required|not_in:0'
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphanumeric character.',
                    'addr.required' => ' Address field is required.',
                    'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'mb.required' => ' Mobile no is required.',
                    'mb.digits' => 'Please Enter valid Mobile Number.',
                    'dept.required' => 'Departemnt is required',
                    'dept.regex' => 'Please Enter only alphanumeric character.',
                    'desig.required' => 'Designation is required Field',
                    'desig.regex' => 'Please Enter only alphanumeric character.',
                    'email.required' => 'Email is required',
//                    'eno.required' => 'Epic No is required',
//                    'eno.digits' => 'Epic number must be of 16 digits',
                    'authid.required' => 'Select Approving Authority'
                ];
                $validator = Validator::make($req->all(), $rules, $messages);
                if ($validator->passes()) {
                    $authid;
                    $name;
                    $dept;
                    $desig;
                    $mb;
                    $email;
                    $addr;
                    $eno;
                    if (!empty($req->authid)) {
                        $authid = strip_tags($req->authid);
                    }
                    if (!empty($req->name)) {
                        $name = strip_tags($req->name);
                    }
                    if (!empty($req->dept)) {
                        $dept = strip_tags($req->dept);
                    }
                    if (!empty($req->desig)) {
                        $desig = strip_tags($req->desig);
                    }
                    if (!empty($req->mb)) {
                        $mb = strip_tags($req->mb);
                    }
                    if (!empty($req->email)) {
                        $email = strip_tags($req->email);
                    }
                    if (!empty($req->addr)) {
                        $addr = strip_tags($req->addr);
                    }
                    if (!empty($req->eno)) {
                        $eno = strip_tags($req->eno);
                    }
                    $getallnodaldetails = DB::table('authority_masters')->where('mobile',$mb)->count();
                    if ($getallnodaldetails == 0) {
                            $data = array('name' => $name, 'department' => $dept, 'designation' => $desig, 'mobile' => $mb, 'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $cond = array('id' => $_POST['nodal_id']);
                            $result = $this->PM->updatetable('authority_masters', $cond, $data);
                            return redirect()->back()->with('message', 'Successfully Updated');
                        
                    } else {
                            $data = array('name' => $name, 'department' => $dept, 'designation' => $desig,'email' => $email, 'address' => $addr, 'status' => 1, 'updated_by' => $d->id, 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                            $cond = array('id' => $_POST['nodal_id']);
                            $result = $this->PM->updatetable('authority_masters', $cond, $data);
                            return redirect()->back()->with('message', 'All details will be Updated if Mobile Number is Different,If Mobile Number already exist then except Mobile number all details will be Updated.');
                           // redirect()->back()->with('message', 'Except Autority Type All data is successfully Updated beacuse Entered Authority is already Exist!')->withInput();
                        }
                } else {
                    return redirect()->back()->withErrors($validator, 'error');
                }
            } else {
                 $data = explode('&', $req->id);
                $nodal_id = $data[0];
                $nodal_auth = $data[1];
                $authority = $this->PM->getAuthority($d->st_code);
                $getAuthorityDetails = $this->PM->getAuthorityDetails($nodal_id);
//                echo $nodal_id;
//                echo '<pre/>';
//                print_r($getAuthorityDetails);
                if (!empty($getAuthorityDetails[0]->ac_no) || !empty($getAuthorityDetails[0]->pc_no) || !empty($getAuthorityDetails[0]->dist_no) || !empty($getAuthorityDetails[0]->auth_type_id)) {
                    if ($getAuthorityDetails[0]->ac_no == $d->ac_no) {
                        $authtype = DB::table('authority_masters as a')->select('a.auth_type_id', 'b.name as auth_type_name')
                                        ->join('authority_type as b', 'a.auth_type_id', '=', 'b.id')
                                        ->where('a.id', $req->id)->get()->first();
                        if($authtype->auth_type_id != $nodal_auth)
                        {
                            $authtype = DB::table('authority_masters_mapping as a')->select('a.auth_type_id', 'b.name as auth_type_name')
                                        ->join('authority_type as b', 'a.auth_type_id', '=', 'b.id')
                                        ->where('a.authority_masters_id', $req->id)->where('auth_type_id', $nodal_auth)->get()->first();
                        }
                    } else {
//                        echo 2;die;
                        $authtype = DB::table('authority_masters_mapping as a')->select('a.auth_type_id', 'b.name as auth_type_name')
                                        ->join('authority_type as b', 'a.auth_type_id', '=', 'b.id')
                                        ->where('a.authority_masters_id', $req->id)->where('auth_type_id', $nodal_auth)->get()->first();
                    }
                } else {
//                    echo 3;die;
                    $authtype = DB::table('authority_masters_mapping as a')->select('a.auth_type_id', 'b.name as auth_type_name')
                                    ->join('authority_type as b', 'a.auth_type_id', '=', 'b.id')
                                    ->where('authority_masters_id', $req->id)->where('auth_type_id', $nodal_auth)->get()->first();
                }
                return view('admin.pc.deo.Permission.EditAuthority')->with(array('user_data' => $d, 'authtype' =>$authtype, 'getAuthorityDetails' => $getAuthorityDetails, 'authority' => $authority));
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
     public function EditAuthorityStatus(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $data = explode('#', $req->status);
            $status = $data[0];
            $id = $data[1];
             if(!empty($data[2]))
            {
            $authid=$data[2];
            }
           
            if(!empty($authid))
                {
                $where = array('authority_masters_id' => $id,'auth_type_id'=>$authid,'created_by' => $d->id);
                }
                else
                {
                     $where = array('authority_masters_id' => $id,'created_by' => $d->id);
                }
                $findauth = DB::table('authority_masters_mapping')->where($where)->select('*')->get()->toArray();
            if ($status == 1) {
                if (!empty($findauth)) {
                    if(count($findauth) == 1) {
                        $cond = array('is_active' => '0');
                        $res = $this->PM->updatetable('authority_masters_mapping', $where, $cond);
                        if ($res == 1) {
                            return 1;
                        } else {
                            return 0;
                        }
                    }
                }
                else {
                    $getdata=DB::table('authority_masters')->select('*')->where('id',$id)->get()->toArray();
                    if(!empty($getdata))
                    {
                       $authdata=array('authority_masters_id'=>$id,'dist_no'=>$getdata[0]->dist_no,'ac_no'=>$getdata[0]->ac_no,'pc_no'=>$getdata[0]->pc_no,'auth_type_id'=>$getdata[0]->auth_type_id,'created_by'=>$d->id);
                       $insetdata=$this->PM->insertdata('authority_masters_mapping',$authdata);
                       
                       if($insetdata == 1)
                       {
                           $authmasterdata=array('dist_no'=>'NULL','ac_no'=>'NULL','pc_no'=>'NULL','auth_type_id'=>'NULL');
                       $idcond=array('id'=>$id);
                       $updateauthmaster=$this->PM->updatetable('authority_masters',$idcond,$authmasterdata);
                           $cond = array('is_active' => '0');
                        $res = $this->PM->updatetable('authority_masters_mapping', $where, $cond);
                        if ($res == 1) {
                            return 1;
                        } else {
                            return 0;
                        }
                       }
                    }
                    }
            } else {
                if(!empty($findauth)) {
                    if (count($findauth) == 1) {
                        $cond = array('is_active' => '1');
                        $res = $this->PM->updatetable('authority_masters_mapping', $where, $cond);
                        if ($res == 1) {
                            return 1;
                        } else {
                            return 0;
                        }
                    }
                }
                else {
                       $getdata=DB::table('authority_masters')->select('*')->where('id',$id)->get()->toArray();
                    if(!empty($getdata))
                    {
                       $authdata=array('authority_masters_id'=>$id,'dist_no'=>$getdata[0]->dist_no,'ac_no'=>$getdata[0]->ac_no,'pc_no'=>$getdata[0]->pc_no,'auth_type_id'=>$getdata[0]->auth_type_id,'created_by'=>$d->id);
                       $insetdata=$this->PM->insertdata('authority_masters_mapping',$authdata);
                       
                       if($insetdata == 1)
                       {
                           $authmasterdata=array('dist_no'=>'NULL','ac_no'=>'NULL','pc_no'=>'NULL','auth_type_id'=>'NULL');
                       $idcond=array('id'=>$id);
                       $updateauthmaster=$this->PM->updatetable('authority_masters',$idcond,$authmasterdata);
                           $cond = array('is_active' => '0');
                        $res = $this->PM->updatetable('authority_masters_mapping', $where, $cond);
                        if ($res == 1) {
                            return 1;
                        } else {
                            return 0;
                        }
                       }
                    }
                    }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    //location Master
    public function AddLocation() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
            return view('admin.pc.deo.Permission.AddLocation')->with(array('user_data' => $d, 'getAllPC' => $getAllPC));
        } else {
            return redirect('/officer-login');
        }
    }

    public function AddLocationinsert(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            if (!empty($_POST['submit'])) {
                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z0-9]+$)/',
                    'addr' => 'required|not_regex:/([<>@$%?]+)/',
                    'acno' => 'required|not_in:0',
                    'pc' => 'required|not_in:0'
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphanumeric character.',
                    'addr.required' => ' Address field is required.',
                    'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                    'acno.required'=>'Please select Ac',
                    'acno.not_in' =>'Please select AC',
                    'pc.required' =>'Please select PC',
                    'pc.not_in' =>'Please select PC'
                ];
                $validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->passes()) {
                    $location_name = strip_tags($request['name']);
                    $address = strip_tags($request['addr']);
                    if (!empty($request->pc)) {
                    $pc = strip_tags($request->pc);
                    }
                    if (!empty($request->acno)) {
                        $ac = strip_tags($request->acno);
                    }
                    /*$array = array();

                       $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($request['addr']).'&key=AIzaSyDfT2Iqt4yvPmQSGRJVApQUHdbv5XR_R-8&callback=initMap');

                       $geo = json_decode($geo,true);

        

                      if ($geo['status'] == 'OK')

                      {

                      $latitude = $geo['results'][0]['geometry']['location']['lat'];

                      $longitude = $geo['results'][0]['geometry']['location']['lng'];

                      $arrayvalue = array('lat'=> $latitude ,'lng'=>$longitude);

                      $latitude_loc = $arrayvalue['lat'];

                      $longitude_loc =  $arrayvalue['lng'];

                      

                      }

                      else

                      {
                          $latitude_loc = 0;

                      $longitude_loc =  0;

//                      return redirect()->back()->with('chckmessage', 'Enter Correct Address');

                       } */
                    $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'location_name' => $location_name, 'location_details' => $address);
                    $chckloc = DB::table('location_master')->where($where)->count();
                    if ($chckloc == 0) {
                        $data = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $ac, 'pc_no' => $pc,'latitude' =>'00.0000','longitude'=>'00.0000', 'created_by' => $d->id, 'location_name' => $location_name, 'location_details' => $address, 'status' => 1, 'added_at' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'));
                        DB::table('location_master')->insert($data);
                        return redirect('/pcdeo/viewaddlocation')->with('message', 'Successfully Added');
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered Location name and address is already Exist!')->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
            }
        } else {
            return redirect('/officer-login');
        }
    }

    public function viewaddlocation() {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $getAllPC=$this->PM->getAllPC($d->st_code,$d->dist_no);
            $getAllPermsDatas = $this->PM->getlocationmaster($d->st_code, $d->ac_no);
            //print_r($getAllPermsDatas);
            //exit;
            return view('admin.pc.deo.Permission.viewaddlocation')->with(array('user_data' => $d, 'getAllPC' => $getAllPC, 'getAllPermsDatas' => $getAllPermsDatas));
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
            
            $acno=$req->acid;
           
            $getAlllocDatas = $this->PM->getlocationmaster($d->st_code,$acno);
            return $getAlllocDatas;
        } else {
            return redirect('/officer-login');
        }
    }

    public function locationeditpermsn(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

            $location_editid = $request->id;
            //print_r($d);exit;
            $getAllPermsDatas = $this->PM->getlocationeditmaster($location_editid);
            return view('admin.pc.deo.Permission.Editaddlocation')->with(array('user_data' => $d, 'showpage' => 'permission', 'getAllPermsDatas' => $getAllPermsDatas));
        } else {
            return redirect('/officer-login');
        }
    }

    public function updateLocationval(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            $users = Session::get('admin_login_details');
            $user = Auth::user();
            if (!empty($_POST['submit'])) {
                $rules = [
                    'name' => 'required|regex:/(^[ A-Za-z0-9]+$)/',
                    'addr' => 'required|not_regex:/([<>@$%?]+)/',
                ];
                $messages = [
                    'name.required' => ' Name field is required.',
                    'name.regex' => 'Please Enter only alphanumeric character.',
                    'addr.required' => ' Address field is required.',
                    'addr.not_regex' => 'These special character are not allowed(<>@$%?).',
                ];
                $validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->passes()) {
                    $location_name = strip_tags($request['name']);
                    $location_detail = strip_tags($request['addr']);
                    /*$array = array();

                       $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($request['addr']).'&key=AIzaSyDfT2Iqt4yvPmQSGRJVApQUHdbv5XR_R-8&callback=initMap');

                       $geo = json_decode($geo,true);

        

                      if ($geo['status'] == 'OK')

                      {

                      $latitude = $geo['results'][0]['geometry']['location']['lat'];

                      $longitude = $geo['results'][0]['geometry']['location']['lng'];

                      $arrayvalue = array('lat'=> $latitude ,'lng'=>$longitude);

                      $latitude_loc = $arrayvalue['lat'];

                      $longitude_loc =  $arrayvalue['lng'];

                      

                      }

                      else

                      {
                          $latitude_loc = 0;

                      $longitude_loc =  0;


//                      return redirect()->back()->with('chckmessage', 'Enter Correct Address');

                       } */
                    $valid = strip_tags($request['updateid']);
                    $where = array('st_code' => $d->st_code, 'dist_no' => $d->dist_no, 'ac_no' => $d->ac_no, 'pc_no' => $d->pc_no, 'location_name' => $location_name, 'location_details' => $location_detail);
                    $chckloc = DB::table('location_master')->where($where)->count();
                    if ($chckloc == 0) {
                        $updateid = array('id' => $valid);
                        $data = array('location_name' => $location_name, 'location_details' => $location_detail,'latitude' =>'00.0000','longitude'=>'00.0000', 'added_update_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s'));
                        $getAllPermsDat = $this->PM->updatetable('location_master', $updateid, $data);
                        $getAllPermsDatas = $this->PM->getlocationmaster($d->st_code, $d->ac_no);

                        return redirect()->back()->with('message', 'Successfully Updated');
                    } else {
                        return redirect()->back()->with('chckmessage', 'Entered Location name and address is already Exist!')->withInput();
                    }
                } else {
                    return redirect()->back()->withErrors($validator, 'error')->withInput();
                }
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
    
    public function getPS(Request $req)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);
            
            $acno=$req->ac_id;
            $stcode=$req->st_code;
//            echo $acno. $stcode;die;
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
            
            $acno=$req->ac_id;
            $stcode=$req->st_code;
            $dist=$req->dist;
            $pcno=$req->pc_id;
            if($pcno != 0)
            {
                $getlocation=DB::table('location_master')
                        ->select('location_name','id')
                        ->where('st_code',$stcode)
//                        ->where('dist_no',$dist)
                        ->where('ac_no',$acno)
                        ->where('pc_no',$pcno)
                        ->get()->toArray();
            }
            else
            {
                $getlocation=DB::table('location_master')
                        ->select('location_name','id')
                        ->where('st_code',$stcode)
//                        ->where('dist_no',$dist)
                        ->where('ac_no',$acno)
//                        ->where('pc_no',$pcno)
                        ->get()->toArray();
            }
            return $getlocation;
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
            
            $stcode=$req->stcode;
            $getAllDist = $this->PM->getAllDist($d->st_code);
            return $getAllDist;
        } else {
            return redirect('/officer-login');
        }
    }
}