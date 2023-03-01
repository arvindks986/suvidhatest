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
    use App\commonModel;  
    use App\adminmodel\PCRoreportModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
	// use Excel;

	use App\Exports\ExcelExport;
    use Maatwebsite\Excel\Facades\Excel;

class PCRoreportsController extends Controller {
  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct()
  {
        $this->middleware(['auth:admin','auth']);
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ro');
        $this->commonModel = new commonModel();
        $this->ropcreport = new PCRoreportModel();
   }

  /**
  * Show the application dashboard.
  *
  * @return \Illuminate\Http\Response
  */

   protected function guard(){
        return Auth::guard();
    }
	
		public function partywise()
	{

	if (Auth::check()) 
	{
	$user = Auth::user();
	$d=$this->commonModel->getunewserbyuserid($user->id);
	$user_data = $d;
	$statecode = $d->st_code;
	$details=$this->commonModel->getallacbypcno($d->st_code,$d->pc_no);
	$pc = $details->PC_NO;
	if($user_data)
	{


		$excelrecord = "SELECT PARTYNAME,st.ST_CODE,st.AC_NAME,permission_name,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` RIGHT JOIN permission_master sq ON sq.id=t.permission_type_id RIGHT JOIN m_ac st ON st.AC_NO=p.ac_no AND st.ST_CODE=p.st_code RIGHT JOIN m_party mp ON mp.CCODE=p.party_id where st.ST_CODE='$statecode' and st.pc_no ='$pc' group by 1,2,3,4";
		$records = DB::select($excelrecord);
		$arr = array();
		$headings[]=[];
		$export_data[] = ['Party Name','Permission Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];

		foreach($records as $record_data)
		{ 
		if($record_data->pending == '')
		{
		$record_data->pending = '0';
		}
		if($record_data->total_request == '')
		{
		$record_data->total_request = '0';
		}
		if($record_data->approved == '')
		{
		$record_data->approved = '0';
		}
		if($record_data->inprogress == '')
		{
		$record_data->inprogress = '0';
		}
		if($record_data->rejected == '')
		{
		$record_data->rejected = '0';
		}
		if($record_data->Cancel == '')
		{
		$record_data->Cancel = '0';
		}
		$export_data[] = [
            $record_data->PARTYNAME,
		$record_data->permission_name,
		$record_data->total_request,
		$record_data->approved,
		$record_data->rejected,
		$record_data->inprogress,
		$record_data->pending,
		$record_data->Cancel,
         
      ];

		}


		$name_excel = 'Party Wise report';
		return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


	// return Excel::create('Party Wise report', function($excel) use ($d,$statecode,$pc) {
	// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$pc)
	// {

	// $excelrecord = "SELECT PARTYNAME,st.ST_CODE,st.AC_NAME,permission_name,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` RIGHT JOIN permission_master sq ON sq.id=t.permission_type_id RIGHT JOIN m_ac st ON st.AC_NO=p.ac_no AND st.ST_CODE=p.st_code RIGHT JOIN m_party mp ON mp.CCODE=p.party_id where st.ST_CODE='$statecode' and st.pc_no ='$pc' group by 1,2,3,4";
	// $records = DB::select($excelrecord);
	// $arr = array();
	// foreach($records as $record_data)
	// { 
	// if($record_data->pending == '')
	// {
	// $record_data->pending = '0';
	// }
	// if($record_data->total_request == '')
	// {
	// $record_data->total_request = '0';
	// }
	// if($record_data->approved == '')
	// {
	// $record_data->approved = '0';
	// }
	// if($record_data->inprogress == '')
	// {
	// $record_data->inprogress = '0';
	// }
	// if($record_data->rejected == '')
	// {
	// $record_data->rejected = '0';
	// }
	// if($record_data->Cancel == '')
	// {
	// $record_data->Cancel = '0';
	// }
	// $data =  array(
	// $record_data->PARTYNAME,
	// $record_data->permission_name,
	// $record_data->total_request,
	// $record_data->approved,
	// $record_data->rejected,
	// $record_data->inprogress,
	// $record_data->pending,
	// $record_data->Cancel,
	// );
	// array_push($arr, $data);
	// }
	// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
	// 'Party Name','Permission Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
	// )
	// );  
	// });
	// })->download();	


	}


	}
	else 
	{
	return redirect('/officer-login');
	} 
	}

	public function permissiontype()
	{
	if (Auth::check()) 
	{
	$user = Auth::user();
	$d=$this->commonModel->getunewserbyuserid($user->id);
	$user_data = $d;
	$statecode = $d->st_code;
	$pc = $d->pc_no;
	if($user_data)
	{

		$excelrecord = "SELECT st.ST_CODE,st.AC_NAME,permission_name,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` RIGHT JOIN permission_master sq ON sq.id=t.permission_type_id LEFT JOIN m_ac st ON st.AC_NO=p.ac_no AND st.ST_CODE=p.st_code where st.ST_CODE='$statecode' and st.pc_no ='$pc' group by 1,2,3";

	$records = DB::select($excelrecord);
	$arr = array();
	$export_data[] = ['Permission Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];
    $headings[]=[];

	foreach($records as $record_data)
	{ 
	if($record_data->pending == '')
	{
	$record_data->pending = '0';
	}
	if($record_data->total_request == '')
	{
	$record_data->total_request = '0';
	}
	if($record_data->approved == '')
	{
	$record_data->approved = '0';
	}
	if($record_data->inprogress == '')
	{
	$record_data->inprogress = '0';
	}
	if($record_data->rejected == '')
	{
	$record_data->rejected = '0';
	}
	if($record_data->Cancel == '')
	{
	$record_data->Cancel = '0';
	}

	$export_data[] = [
		$record_data->permission_name,
	$record_data->total_request,
	$record_data->approved,
	$record_data->rejected,
	$record_data->inprogress,
	$record_data->pending,
	$record_data->Cancel,
	 
      ];

	
	}

		$name_excel = 'Permission Wise report';
		return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

	// return Excel::create('Permission Wise report', function($excel) use ($d,$statecode,$pc) {
	// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$pc)
	// {

	// $excelrecord = "SELECT st.ST_CODE,st.AC_NAME,permission_name,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` RIGHT JOIN permission_master sq ON sq.id=t.permission_type_id LEFT JOIN m_ac st ON st.AC_NO=p.ac_no AND st.ST_CODE=p.st_code where st.ST_CODE='$statecode' and st.pc_no ='$pc' group by 1,2,3";

	// $records = DB::select($excelrecord);
	// $arr = array();
	// foreach($records as $record_data)
	// { 
	// if($record_data->pending == '')
	// {
	// $record_data->pending = '0';
	// }
	// if($record_data->total_request == '')
	// {
	// $record_data->total_request = '0';
	// }
	// if($record_data->approved == '')
	// {
	// $record_data->approved = '0';
	// }
	// if($record_data->inprogress == '')
	// {
	// $record_data->inprogress = '0';
	// }
	// if($record_data->rejected == '')
	// {
	// $record_data->rejected = '0';
	// }
	// if($record_data->Cancel == '')
	// {
	// $record_data->Cancel = '0';
	// }
	// $data =  array(
	// $record_data->permission_name,
	// $record_data->total_request,
	// $record_data->approved,
	// $record_data->rejected,
	// $record_data->inprogress,
	// $record_data->pending,
	// $record_data->Cancel,
	// );
	// array_push($arr, $data);
	// }
	// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
	// 'Permission Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
	// )
	// );  
	// });
	// })->download();	



	}


	}
	else 
	{
	return redirect('/officer-login');
	} 
	} 
	public function permissionraw()
	{
		
	if (Auth::check()) 
	{
	$user = Auth::user();
	$d=$this->commonModel->getunewserbyuserid($user->id);
	$user_data = $d;
	$st_code = $d->st_code;

	$pcno = $d->pc_no;
	$details=$this->commonModel->getallacbypcno($d->st_code,$d->pc_no);
	$acno = $details->AC_NO;
	$allrec= "SELECT p.id,user_id,p.st_code,p.dist_no,p.ac_no,p.pc_no,p.permission_type_id,p.approved_status,p.cancel_status,p.permission_mode,p.added_at,p.updated_at,sq.permission_name,mp.PARTYNAME,p.date_time_start,p.date_time_end FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` RIGHT JOIN permission_master sq ON sq.id=t.permission_type_id RIGHT JOIN m_ac st ON st.AC_NO=p.ac_no AND st.ST_CODE=p.st_code RIGHT JOIN m_party mp ON mp.CCODE=p.party_id RIGHT JOIN m_state mst ON mst.ST_CODE=p.st_code where st.ST_CODE='$st_code' and st.pc_no ='$pcno'";

//exit;
    $allrecord = DB::select($allrec);
	/*   echo "<pre>";
	 print_r($allrecord); 
	 exit;  */
	$arr = array();
	$export_data[] = ['Permission ID','State Name','District Name','AC Name','User Name','Permission Type', 'User Type','Party Name','Date of Submission','Action Date','Event Start Date','Event End Date','Permission Mode','Previous Status','Current Status'];
	$headings[]=[];	
	foreach($allrecord as $excelrecord)
	{
    if($excelrecord->user_id != ''  or $excelrecord->user_id != 0 )
	{		
	$uservalue = DB::table('user_data')
	->select('name')
	->where('user_login_id',$excelrecord->user_id) 
	->get();
	
	
	$user_login = DB::table('user_login')
	->select('role_id')
	->where('id',$excelrecord->user_id) 
	->get();
	
	$userrole = DB::table('user_role')
	->select('*')
	->where('role_id',$user_login[0]->role_id) 
	->get();
     }
	$statename = DB::table('m_state')->select('ST_NAME')->where('ST_CODE',$st_code)->get();
	if($excelrecord->dist_no != 0  or  $excelrecord->dist_no = '' )
	{
	$g = DB::table('m_district')->select('DIST_NAME')->where('DIST_NO',$excelrecord->dist_no)
	->where('ST_CODE',$st_code)->get();
	}
	if($excelrecord->ac_no != 0  or  $excelrecord->ac_no = '' )
	{
	$ac = DB::table('m_ac')->select('AC_NAME')->where('AC_NO',$excelrecord->ac_no)
	->where('ST_CODE',$st_code)->get();
	}

	if($excelrecord->cancel_status== 1)
	{
	$cancelstatus = 'Cancel';
	}
	else if($excelrecord->cancel_status == 0)
	{
	if($excelrecord->approved_status == 0)
	{
	$cancelstatus = 'Pending';
	}
	else if($excelrecord->approved_status == 1)
	{
	$cancelstatus = 'Inprogress';
	}
	else if($excelrecord->approved_status == 2)
	{
	$cancelstatus = 'Accepted';
	}
	else if($excelrecord->approved_status == 3)
	{
	$cancelstatus = 'Rejected';
	}
	}
	if($excelrecord->permission_mode== 0)
	{
	$pmode = 'Offline';
	}
	else if($excelrecord->permission_mode == 1)
	{
	$pmode  = 'Online';
	}
	if($excelrecord->approved_status == 0)
	{
	$status = 'Pending';
	}
	else if($excelrecord->approved_status == 1)
	{
	$status = 'Inprogress';
	}
	else if($excelrecord->approved_status == 2)
	{
	$status = 'Accepted';
	}
	else if($excelrecord->approved_status == 3)
	{
	$status = 'Rejected';
	}
	$export_data[] = [
		$excelrecord->id,
    $statename[0]->ST_NAME,
    $g[0]->DIST_NAME,
    $ac[0]->AC_NAME,
	$uservalue[0]->name,
	$excelrecord->permission_name,
	$userrole[0]->role_name,
	$excelrecord->PARTYNAME,
	$excelrecord->added_at,
	$excelrecord->updated_at,
	$excelrecord->date_time_start,
    $excelrecord->date_time_end,
	$pmode,
	$status,
	$cancelstatus,
	 
  ];


	}

	$name_excel = 'Permission Raw report';
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


// 	return Excel::create('Permission Raw report', function($excel) use ($d) {
// 	$excel->sheet('mySheet', function($sheet) use ($d)
// 	{

// 	$st_code = $d->st_code;
// 	$pcno = $d->pc_no;
// 	$details=$this->commonModel->getallacbypcno($d->st_code,$d->pc_no);
// 	$acno = $details->AC_NO;
// 	$allrec= "SELECT p.id,user_id,p.st_code,p.dist_no,p.ac_no,p.pc_no,p.permission_type_id,p.approved_status,p.cancel_status,p.permission_mode,p.added_at,p.updated_at,sq.permission_name,mp.PARTYNAME,p.date_time_start,p.date_time_end FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` RIGHT JOIN permission_master sq ON sq.id=t.permission_type_id RIGHT JOIN m_ac st ON st.AC_NO=p.ac_no AND st.ST_CODE=p.st_code RIGHT JOIN m_party mp ON mp.CCODE=p.party_id RIGHT JOIN m_state mst ON mst.ST_CODE=p.st_code where st.ST_CODE='$st_code' and st.pc_no ='$pcno'";

// //exit;
//     $allrecord = DB::select($allrec);
// 	/*   echo "<pre>";
// 	 print_r($allrecord); 
// 	 exit;  */
// 	$arr = array();
// 	foreach($allrecord as $excelrecord)
// 	{
//     if($excelrecord->user_id != ''  or $excelrecord->user_id != 0 )
// 	{		
// 	$uservalue = DB::table('user_data')
// 	->select('name')
// 	->where('user_login_id',$excelrecord->user_id) 
// 	->get();
	
	
// 	$user_login = DB::table('user_login')
// 	->select('role_id')
// 	->where('id',$excelrecord->user_id) 
// 	->get();
	
// 	$userrole = DB::table('user_role')
// 	->select('*')
// 	->where('role_id',$user_login[0]->role_id) 
// 	->get();
//      }
// 	$statename = DB::table('m_state')->select('ST_NAME')->where('ST_CODE',$st_code)->get();
// 	if($excelrecord->dist_no != 0  or  $excelrecord->dist_no = '' )
// 	{
// 	$g = DB::table('m_district')->select('DIST_NAME')->where('DIST_NO',$excelrecord->dist_no)
// 	->where('ST_CODE',$st_code)->get();
// 	}
// 	if($excelrecord->ac_no != 0  or  $excelrecord->ac_no = '' )
// 	{
// 	$ac = DB::table('m_ac')->select('AC_NAME')->where('AC_NO',$excelrecord->ac_no)
// 	->where('ST_CODE',$st_code)->get();
// 	}

// 	if($excelrecord->cancel_status== 1)
// 	{
// 	$cancelstatus = 'Cancel';
// 	}
// 	else if($excelrecord->cancel_status == 0)
// 	{
// 	if($excelrecord->approved_status == 0)
// 	{
// 	$cancelstatus = 'Pending';
// 	}
// 	else if($excelrecord->approved_status == 1)
// 	{
// 	$cancelstatus = 'Inprogress';
// 	}
// 	else if($excelrecord->approved_status == 2)
// 	{
// 	$cancelstatus = 'Accepted';
// 	}
// 	else if($excelrecord->approved_status == 3)
// 	{
// 	$cancelstatus = 'Rejected';
// 	}
// 	}
// 	if($excelrecord->permission_mode== 0)
// 	{
// 	$pmode = 'Offline';
// 	}
// 	else if($excelrecord->permission_mode == 1)
// 	{
// 	$pmode  = 'Online';
// 	}
// 	if($excelrecord->approved_status == 0)
// 	{
// 	$status = 'Pending';
// 	}
// 	else if($excelrecord->approved_status == 1)
// 	{
// 	$status = 'Inprogress';
// 	}
// 	else if($excelrecord->approved_status == 2)
// 	{
// 	$status = 'Accepted';
// 	}
// 	else if($excelrecord->approved_status == 3)
// 	{
// 	$status = 'Rejected';
// 	}
// 	$data =  array(
// 	$excelrecord->id,
//     $statename[0]->ST_NAME,
//     $g[0]->DIST_NAME,
//     $ac[0]->AC_NAME,
// 	$uservalue[0]->name,
// 	$excelrecord->permission_name,
// 	$userrole[0]->role_name,
// 	$excelrecord->PARTYNAME,
// 	$excelrecord->added_at,
// 	$excelrecord->updated_at,
// 	$excelrecord->date_time_start,
//     $excelrecord->date_time_end,
// 	$pmode,
// 	$status,
// 	$cancelstatus,

// 	);
// 	array_push($arr, $data);
// 	}

// 	$sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
// 	'Permission ID','State Name','District Name','AC Name','User Name','Permission Type', 'User Type','Party Name','Date of Submission','Action Date','Event Start Date','Event End Date','Permission Mode','Previous Status','Current Status'
// 	)
// 	);
// 	});
// 	})->download();

	}
	else 
	{
	return redirect('/officer-login');
	}  
	}


      public function reportpc(){   
	 
	    if(Auth::check()){
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
			$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
		    $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		    if($check_finalize=='') 
			{
				$cand_finalize_ceo=0; $cand_finalize_ro=0;
			} 
			else 
			{
           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
            }
			
			$pcnames=DB::table('m_ac')->where('ST_CODE',$d->st_code)->where('PC_NO',$d->pc_no)->get();
            return view('admin.pc.ropc.Permission.report',['edetails' => $ele_details,'user_data' => $d,'cand_finalize_ro' => $cand_finalize_ro,'pcnames' => $pcnames]);        
            }
            else {
                  return redirect('/officer-login');
                } 
      }
	  
		public function reportdates(Request $req)
		{
		if (Auth::check()) 
		{
		$user = Auth::user();
		$d = $this->commonModel->getunewserbyuserid($user->id);
		$statecode = $d->st_code;
		$pcno = $d->pc_no;
        $cur_time  = Carbon::now();
        if($req->input('excel'))
        {
		if((!empty($_REQUEST['pcnames'])) and (empty($_REQUEST['datefilter'])))
		{
		$acid = $req->input('pcnames');
		$d=$this->commonModel->getunewserbyuserid($user->id);

		$export_data[]=['AC Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];
		$headings[]=[];

		$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.ac_no = '$acid' group by 1,2";
		$records = DB::select($excelrecord);
		$arr = array();
		foreach($records as $record_data)
		{ 
		if($record_data->pending == '')
		{
		$record_data->pending = '0';
		}
		if($record_data->total_request == '')
		{
		$record_data->total_request = '0';
		}
		if($record_data->approved == '')
		{
		$record_data->approved = '0';
		}
		if($record_data->inprogress == '')
		{
		$record_data->inprogress = '0';
		}
		if($record_data->rejected == '')
		{
		$record_data->rejected = '0';
		}
		if($record_data->Cancel == '')
		{
		$record_data->Cancel = '0';
		}
		$export_data[] = [
			$record_data->AC_NAME,
		$record_data->total_request,
		$record_data->approved,
		$record_data->rejected,
		$record_data->inprogress,
		$record_data->pending,
		$record_data->Cancel,
         
      ];

		
		}


		$name_excel = 'Datewise Permission Report';
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


		// return Excel::create('Datewise Permission Report', function($excel) use ($d,$statecode,$acid) {
		// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$acid)
		// {
		// $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.ac_no = '$acid' group by 1,2";
		// $records = DB::select($excelrecord);
		// $arr = array();
		// foreach($records as $record_data)
		// { 
		// if($record_data->pending == '')
		// {
		// $record_data->pending = '0';
		// }
		// if($record_data->total_request == '')
		// {
		// $record_data->total_request = '0';
		// }
		// if($record_data->approved == '')
		// {
		// $record_data->approved = '0';
		// }
		// if($record_data->inprogress == '')
		// {
		// $record_data->inprogress = '0';
		// }
		// if($record_data->rejected == '')
		// {
		// $record_data->rejected = '0';
		// }
		// if($record_data->Cancel == '')
		// {
		// $record_data->Cancel = '0';
		// }
		// $data =  array(
		// $record_data->AC_NAME,
		// $record_data->total_request,
		// $record_data->approved,
		// $record_data->rejected,
		// $record_data->inprogress,
		// $record_data->pending,
		// $record_data->Cancel,
		// );
		// array_push($arr, $data);
		// }

		// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
		// 'AC Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
		// )
		// );  
		// });
		// })->download();	



		}

		else if(!empty($_REQUEST['pcnames']) and (!empty($_REQUEST['datefilter'])))
		{
			// echo "er";die;
		$acid = $req->input('pcnames');
		$datevalue = $req->input('datefilter');
		$dates = explode("~",$datevalue);
		$dte1 = $dates[0];
		$dte2 = $dates[1];
		$d=$this->commonModel->getunewserbyuserid($user->id);
		
		$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$acid' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
	
		$records = DB::select($excelrecord);
		$export_data[]=['AC Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];
		$headings[]=[];
		foreach($records as $record_data)
		{ 
		if($record_data->pending == '')
		{
		$record_data->pending = '0';
		}
		if($record_data->total_request == '')
		{
		$record_data->total_request = '0';
		}
		if($record_data->approved == '')
		{
		$record_data->approved = '0';
		}
		if($record_data->inprogress == '')
		{
		$record_data->inprogress = '0';
		}
		if($record_data->rejected == '')
		{
		$record_data->rejected = '0';
		}
		if($record_data->Cancel == '')
		{
		$record_data->Cancel = '0';
		}
		$export_data[] = [
			$record_data->AC_NAME,
			$record_data->total_request,
			$record_data->approved,
			$record_data->rejected,
			$record_data->inprogress,
			$record_data->pending,
			$record_data->Cancel,
         
      ];

		
		}

		$name_excel = 'Datewise Permission Report';
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


		// return Excel::create('Datewise Permission Report', function($excel) use ($d,$statecode,$acid,$dte1,$dte2) {
		// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$acid,$dte1,$dte2)
		// {	
		// $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$acid' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
	
		// $records = DB::select($excelrecord);
		// $arr = array();
		// foreach($records as $record_data)
		// { 
		// if($record_data->pending == '')
		// {
		// $record_data->pending = '0';
		// }
		// if($record_data->total_request == '')
		// {
		// $record_data->total_request = '0';
		// }
		// if($record_data->approved == '')
		// {
		// $record_data->approved = '0';
		// }
		// if($record_data->inprogress == '')
		// {
		// $record_data->inprogress = '0';
		// }
		// if($record_data->rejected == '')
		// {
		// $record_data->rejected = '0';
		// }
		// if($record_data->Cancel == '')
		// {
		// $record_data->Cancel = '0';
		// }
		// $data =  array(
		// $record_data->AC_NAME,
		// $record_data->total_request,
		// $record_data->approved,
		// $record_data->rejected,
		// $record_data->inprogress,
		// $record_data->pending,
		// $record_data->Cancel,
		// );
		// array_push($arr, $data);
		// }

		// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
		// 'AC Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
		// )
		// );  
		// });
		// })->download();	


		}

		else if(($_REQUEST['pcnames'] == '0') and (empty($_REQUEST['datefilter'])))
		{
		$d=$this->commonModel->getunewserbyuserid($user->id);
	    $details=$this->commonModel->getallacbypcno($d->st_code,$d->pc_no);
		$pcno = $d->pc_no;
		$acno = $details->AC_NO;

		$export_data[]=['AC Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];
		$headings[]=[];

		$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.pc_no ='$pcno'  group by 1,2";
	
		$records = DB::select($excelrecord);
		$arr = array();
		foreach($records as $record_data)
		{ 
		if($record_data->pending == '')
		{
		$record_data->pending = '0';
		}
		if($record_data->total_request == '')
		{
		$record_data->total_request = '0';
		}
		if($record_data->approved == '')
		{
		$record_data->approved = '0';
		}
		if($record_data->inprogress == '')
		{
		$record_data->inprogress = '0';
		}
		if($record_data->rejected == '')
		{
		$record_data->rejected = '0';
		}
		if($record_data->Cancel == '')
		{
		$record_data->Cancel = '0';
		}

		$export_data[] = [
			$record_data->AC_NAME,
		$record_data->total_request,
		$record_data->approved,
		$record_data->rejected,
		$record_data->inprogress,
		$record_data->pending,
		$record_data->Cancel,
         
      ];

		
		}


		$name_excel = 'Datewise Permission Report';
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');



		// return Excel::create('Datewise Permission Report', function($excel) use ($d,$statecode,$pcno) {
		// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$pcno)
		// {
	    // $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.pc_no ='$pcno'  group by 1,2";
	
		// $records = DB::select($excelrecord);
		// $arr = array();
		// foreach($records as $record_data)
		// { 
		// if($record_data->pending == '')
		// {
		// $record_data->pending = '0';
		// }
		// if($record_data->total_request == '')
		// {
		// $record_data->total_request = '0';
		// }
		// if($record_data->approved == '')
		// {
		// $record_data->approved = '0';
		// }
		// if($record_data->inprogress == '')
		// {
		// $record_data->inprogress = '0';
		// }
		// if($record_data->rejected == '')
		// {
		// $record_data->rejected = '0';
		// }
		// if($record_data->Cancel == '')
		// {
		// $record_data->Cancel = '0';
		// }
		// $data =  array(
		// $record_data->AC_NAME,
		// $record_data->total_request,
		// $record_data->approved,
		// $record_data->rejected,
		// $record_data->inprogress,
		// $record_data->pending,
		// $record_data->Cancel,
		// );
		// array_push($arr, $data);
		// }

		// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
		// 'AC Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
		// )
		// );  
		// });
		// })->download();	



		}

		else if(($_REQUEST['pcnames'] == '0') and (!empty($_REQUEST['datefilter'])))
		{
		$datevalue = $req->input('datefilter');
		$dates = explode("~",$datevalue);
		$dte1 = $dates[0];
		$dte2 = $dates[1];

		$d=$this->commonModel->getunewserbyuserid($user->id);

		$export_data[]=['AC Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];
		$headings[]=[];

		$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.PC_NO = '$pcno' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
		$records = DB::select($excelrecord);
		$arr = array();
		foreach($records as $record_data)
		{ 
		if($record_data->pending == '')
		{
		$record_data->pending = '0';
		}
		if($record_data->total_request == '')
		{
		$record_data->total_request = '0';
		}
		if($record_data->approved == '')
		{
		$record_data->approved = '0';
		}
		if($record_data->inprogress == '')
		{
		$record_data->inprogress = '0';
		}
		if($record_data->rejected == '')
		{
		$record_data->rejected = '0';
		}
		if($record_data->Cancel == '')
		{
		$record_data->Cancel = '0';
		}
		$export_data[] = [
			$record_data->AC_NAME,
		$record_data->total_request,
		$record_data->approved,
		$record_data->rejected,
		$record_data->inprogress,
		$record_data->pending,
		$record_data->Cancel,
         
      ];

		
		}

		$name_excel = 'Datewise Permission Report';
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');



		// return Excel::create('Datewise Permission Report', function($excel) use ($d,$dte1,$dte2,$statecode,$pcno) {
		// $excel->sheet('mySheet', function($sheet) use ($d,$dte1,$dte2,$statecode,$pcno)
		// {

		// $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.PC_NO = '$pcno' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
		// $records = DB::select($excelrecord);
		// $arr = array();
		// foreach($records as $record_data)
		// { 
		// if($record_data->pending == '')
		// {
		// $record_data->pending = '0';
		// }
		// if($record_data->total_request == '')
		// {
		// $record_data->total_request = '0';
		// }
		// if($record_data->approved == '')
		// {
		// $record_data->approved = '0';
		// }
		// if($record_data->inprogress == '')
		// {
		// $record_data->inprogress = '0';
		// }
		// if($record_data->rejected == '')
		// {
		// $record_data->rejected = '0';
		// }
		// if($record_data->Cancel == '')
		// {
		// $record_data->Cancel = '0';
		// }
		// $data =  array(
		// $record_data->AC_NAME,
		// $record_data->total_request,
		// $record_data->approved,
		// $record_data->rejected,
		// $record_data->inprogress,
		// $record_data->pending,
		// $record_data->Cancel,
		// );
		// array_push($arr, $data);
		// }

		// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
		// 'AC Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
		// )
		// );  
		// });
		// })->download();
		
		
		}
		
		}
		else
		{
		if((!empty($_REQUEST['pcnames'])) and (empty($_REQUEST['datefilter'])))
		{
		$acid = $req->input('pcnames');
		$d=$this->commonModel->getunewserbyuserid($user->id);
		
		$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.ac_no = '$acid' group by 1,2";
		$records = DB::select($excelrecord);
		$pdf = PDF::loadView('admin.pc.ropc.Permission.reportac',['user_data' => $d,'records' =>$records]);
		return $pdf->download('Datewise Permission Report'.$cur_time.'.pdf');
		return view('admin.pc.ropc.Permission.reportac'); 
		}

		else if(!empty($_REQUEST['pcnames']) and (!empty($_REQUEST['datefilter'])))
		{
			
		$acid = $req->input('pcnames');
		$datevalue = $req->input('datefilter');
		$dates = explode("~",$datevalue);
		$dte1 = $dates[0];
		$dte2 = $dates[1];
		$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$acid' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
		$records = DB::select($excelrecord);
		$pdf = PDF::loadView('admin.pc.ropc.Permission.reportac',['user_data' => $d,'records' =>$records]);
		return $pdf->download('Datewise Permission Report'.$cur_time.'.pdf');
		return view('admin.pc.ropc.Permission.reportac'); 
		
		}

		else if(($_REQUEST['pcnames'] == '0') and (empty($_REQUEST['datefilter'])))
		{
		$d=$this->commonModel->getunewserbyuserid($user->id);
	    $details=$this->commonModel->getallacbypcno($d->st_code,$d->pc_no);
		$pcno = $d->pc_no;
		$acno = $details->AC_NO;
	    $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.pc_no ='$pcno'  group by 1,2";
		$records = DB::select($excelrecord);
		$pdf = PDF::loadView('admin.pc.ropc.Permission.reportac',['user_data' => $d,'records' =>$records]);
		return $pdf->download('Datewise Permission Report'.$cur_time.'.pdf');
		return view('admin.pc.ropc.Permission.reportac'); 
		}

		else if(($_REQUEST['pcnames'] == '0') and (!empty($_REQUEST['datefilter'])))
		{
		$datevalue = $req->input('datefilter');
		$dates = explode("~",$datevalue);
		$dte1 = $dates[0];
		$dte2 = $dates[1];
		$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.PC_NO = '$pcno' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
		$records = DB::select($excelrecord);
		$pdf = PDF::loadView('admin.pc.ropc.Permission.reportac',['user_data' => $d,'records' =>$records]);
		return $pdf->download('Datewise Permission Report'.$cur_time.'.pdf');
		return view('admin.pc.ropc.Permission.reportac'); 
		}
		
		}
		}
		}
	  
	  
         
}  // end class