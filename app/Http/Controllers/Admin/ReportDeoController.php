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
// use Excel;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
class ReportDeoController extends Controller
{
/**
* Create a new controller instance.
*
* @return void
*/
public function __construct(){   
$this->middleware('adminsession');
$this->middleware(['auth:admin', 'auth']);
$this->middleware('deo');
$this->commonModel = new commonModel();
}

/**
* Show the application dashboard.
*
* @return \Illuminate\Http\Response
*/

protected function guard()
{
return Auth::guard();
}

public function reportdeo()
{
if (Auth::check()) 
{
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$distvalue = DB::table('m_ac')->where('ST_CODE',$d->st_code)->where('DIST_NO_HDQTR',$d->dist_no)->get();
return view('admin.pc.deo.Permission.reportdeo', ['distvalue' => $distvalue,'user_data' => $d]);
} 

}

public function partywise()
{
if (Auth::check()) 
{
$user = Auth::user();
$d=$this->commonModel->getunewserbyuserid($user->id);
$user_data = $d;
$statecode = $d->st_code;
$dist_no = $d->dist_no;
if($user_data)
{

	$excelrecord= "SELECT PARTYNAME,permission_name,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel
FROM `permission_request` p
JOIN `permission_type` t ON t.`id`=p.`permission_type_id`
LEFT JOIN permission_master s ON s.id=t.permission_type_id
LEFT JOIN m_party mp ON mp.CCODE=p.party_id  WHERE p.st_code='$statecode' and p.dist_no = '$dist_no' GROUP BY PARTYNAME,permission_name";


$records = DB::select($excelrecord);
$arr = array();
$export_data[] = ['Party Name','Permission Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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


	$name_excel = 'party wise report';
	return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');        


// return Excel::create('party wise report', function($excel) use ($d,$statecode,$dist_no) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$dist_no)
// {

// $excelrecord= "SELECT PARTYNAME,permission_name,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel
// FROM `permission_request` p
// JOIN `permission_type` t ON t.`id`=p.`permission_type_id`
// LEFT JOIN permission_master s ON s.id=t.permission_type_id
// LEFT JOIN m_party mp ON mp.CCODE=p.party_id  WHERE p.st_code='$statecode' and p.dist_no = '$dist_no' GROUP BY PARTYNAME,permission_name";


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
// 'Party Name','Permission Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
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
$distcode = $d->dist_no;
$allrecord= DB::table('permission_request as a')
->join('user_login as b','a.user_id','=','b.id')
->join('user_data as ud','ud.user_login_id','=','b.id')
->join('user_role as c','b.role_id','=','c.role_id')
->join('permission_type as d','a.permission_type_id','=','d.id')
->join('permission_master as m','m.id','=','d.permission_type_id')
->join('m_party as mp','mp.CCODE','=','a.party_id')
->join('m_state as ms','ms.ST_CODE','=','a.st_code')
->select('a.*','ud.name','c.role_name','mp.PARTYNAME','ms.ST_NAME','m.permission_name as pname','a.id as permission_id','b.id as login _id')
->where('a.st_code',$st_code)
->where('a.dist_no',$distcode)
->get()->toArray();
$arr  = array();

$export_data[] = ['Permission ID','State Name','District Name','AC Name','User Name','Permission Type', 'User Type','Party Name','Date of Submission','Action Date','Event Start Date','Event End Date','Permission Mode','Previous Status','Current Status'];
$headings[]=[];


foreach($allrecord as $excelrecord)
{	
$uservalue = DB::table('user_data')
->select('*')
->where('user_login_id',$excelrecord->user_id) 
->get();
$stvalue = array('ST_CODE'=>$excelrecord->st_code);
$datastate =DB::table('m_state')->select('ST_NAME')->where($stvalue)->get();
if($excelrecord->dist_no != 0 or $excelrecord->dist_no != '' )
{
$datavalue = array('ST_CODE'=>$excelrecord->st_code,'DIST_NO'=>$excelrecord->dist_no);
$g = DB::table('m_district')->select('DIST_NAME')->where($datavalue)->get();
}
if($excelrecord->ac_no != 0 or $excelrecord->ac_no != '' or $excelrecord->ac_no != NULL )
{
$acvalue = array('ST_CODE'=>$excelrecord->st_code,'AC_NO'=>$excelrecord->ac_no);
$acname = DB::table('m_ac')->select('AC_NAME')->where($acvalue)->get();
$ac_name = $acname[0]->AC_NAME;
}
else
{
$ac_name= "";		
}

if($excelrecord->cancel_status== 1)
{
$cancelstatus = 'Cancel';
}
else if($excelrecord->cancel_status == 0)
{
//$cancelstatus  = 'Non Cancel';
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
$datastate[0]->ST_NAME,
$g[0]->DIST_NAME,
$ac_name,
$uservalue[0]->name,
$excelrecord->pname,
$excelrecord->role_name,
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

$name_excel = 'permission raw report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


// return Excel::create('permission raw report', function($excel) use ($d) {
// $excel->sheet('mySheet', function($sheet) use ($d)
// {

// $st_code = $d->st_code;
// $distcode = $d->dist_no;
// $allrecord= DB::table('permission_request as a')
// ->join('user_login as b','a.user_id','=','b.id')
// ->join('user_data as ud','ud.user_login_id','=','b.id')
// ->join('user_role as c','b.role_id','=','c.role_id')
// ->join('permission_type as d','a.permission_type_id','=','d.id')
// ->join('permission_master as m','m.id','=','d.permission_type_id')
// ->join('m_party as mp','mp.CCODE','=','a.party_id')
// ->join('m_state as ms','ms.ST_CODE','=','a.st_code')
// ->select('a.*','ud.name','c.role_name','mp.PARTYNAME','ms.ST_NAME','m.permission_name as pname','a.id as permission_id','b.id as login _id')
// ->where('a.st_code',$st_code)
// ->where('a.dist_no',$distcode)
// ->get()->toArray();
// $arr  = array();
// foreach($allrecord as $excelrecord)
// {	
// $uservalue = DB::table('user_data')
// ->select('*')
// ->where('user_login_id',$excelrecord->user_id) 
// ->get();
// $stvalue = array('ST_CODE'=>$excelrecord->st_code);
// $datastate =DB::table('m_state')->select('ST_NAME')->where($stvalue)->get();
// if($excelrecord->dist_no != 0 or $excelrecord->dist_no != '' )
// {
// $datavalue = array('ST_CODE'=>$excelrecord->st_code,'DIST_NO'=>$excelrecord->dist_no);
// $g = DB::table('m_district')->select('DIST_NAME')->where($datavalue)->get();
// }
// if($excelrecord->ac_no != 0 or $excelrecord->ac_no != '' or $excelrecord->ac_no != NULL )
// {
// $acvalue = array('ST_CODE'=>$excelrecord->st_code,'AC_NO'=>$excelrecord->ac_no);
// $acname = DB::table('m_ac')->select('AC_NAME')->where($acvalue)->get();
// $ac_name = $acname[0]->AC_NAME;
// }
// else
// {
// $ac_name= "";		
// }

// if($excelrecord->cancel_status== 1)
// {
// $cancelstatus = 'Cancel';
// }
// else if($excelrecord->cancel_status == 0)
// {
// //$cancelstatus  = 'Non Cancel';
// if($excelrecord->approved_status == 0)
// {
// $cancelstatus = 'Pending';
// }
// else if($excelrecord->approved_status == 1)
// {
// $cancelstatus = 'Inprogress';
// }
// else if($excelrecord->approved_status == 2)
// {
// $cancelstatus = 'Accepted';
// }
// else if($excelrecord->approved_status == 3)
// {
// $cancelstatus = 'Rejected';
// }
// }
// if($excelrecord->permission_mode== 0)
// {
// $pmode = 'Offline';
// }
// else if($excelrecord->permission_mode == 1)
// {
// $pmode  = 'Online';
// }
// if($excelrecord->approved_status == 0)
// {
// $status = 'Pending';
// }
// else if($excelrecord->approved_status == 1)
// {
// $status = 'Inprogress';
// }
// else if($excelrecord->approved_status == 2)
// {
// $status = 'Accepted';
// }
// else if($excelrecord->approved_status == 3)
// {
// $status = 'Rejected';
// }
// $data =  array(
// $excelrecord->id,
// $datastate[0]->ST_NAME,
// $g[0]->DIST_NAME,
// $ac_name,
// $uservalue[0]->name,
// $excelrecord->pname,
// $excelrecord->role_name,
// $excelrecord->PARTYNAME,
// $excelrecord->added_at,
// $excelrecord->updated_at,
// $excelrecord->date_time_start,
// $excelrecord->date_time_end,
// $pmode,
// $status,
// $cancelstatus,

// );
// array_push($arr, $data);
// }
// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
// 'Permission ID','State Name','District Name','AC Name','User Name','Permission Type', 'User Type','Party Name','Date of Submission','Action Date','Event Start Date','Event End Date','Permission Mode','Previous Status','Current Status'
// )
// );
// });
// })->download();



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
$dist_no = $d->dist_no;
if($user_data)
{

	$excelrecord= "SELECT permission_name, COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` LEFT JOIN permission_master s ON s.id=t.permission_type_id where p.st_code = '$statecode' and p.dist_no = '$dist_no'  GROUP BY permission_name";


	$records = DB::select($excelrecord);
	$arr = array();
	$export_data[] = ['Permission Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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

	$name_excel = 'permission wise report';
	return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
	
	
// return Excel::create('permission wise report', function($excel) use ($d,$statecode,$dist_no) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$dist_no)
// {


//  $excelrecord= "SELECT permission_name, COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` LEFT JOIN permission_master s ON s.id=t.permission_type_id where p.st_code = '$statecode' and p.dist_no = '$dist_no'  GROUP BY permission_name";


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
// 'Permission Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
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


public function reportdates(Request $req)
{
if (Auth::check()) 
{
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$statecode = $d->st_code;
$distval = $d->dist_no;
$cur_time  = Carbon::now();
//print_r($_REQUEST);
if($req->input('excel'))
{
if((!empty($_REQUEST['ac'])) and (empty($_REQUEST['datefilter'])))
{
$acid = $req->input('ac');
$d=$this->commonModel->getunewserbyuserid($user->id);
return Excel::create('datewise report', function($excel) use ($d,$statecode,$acid) {
$excel->sheet('mySheet', function($sheet) use ($d,$statecode,$acid)
{
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
$data =  array(
$record_data->AC_NAME,
$record_data->total_request,
$record_data->approved,
$record_data->rejected,
$record_data->inprogress,
$record_data->pending,
$record_data->Cancel,
);
array_push($arr, $data);
}

$sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
'AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
)
);  
});



})->download();	
}

else if(!empty($_REQUEST['ac']) and (!empty($_REQUEST['datefilter'])))
{
	
$acid = $req->input('ac');
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$acid' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
$arr = array();
$export_data[]=['AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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


$name_excel = 'datewise report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');        


// return Excel::create('datewise report', function($excel) use ($d,$statecode,$acid,$dte1,$dte2) {
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
// 'AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
// )
// );  
// });
// })->download();	


}

else if(($_REQUEST['ac'] == '0') and (empty($_REQUEST['datefilter'])))
{
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$distval' group by 1,2";

$records = DB::select($excelrecord);
$arr = array();
$export_data[]=['AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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


$name_excel = 'datewise report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');  


// return Excel::create('datewise report', function($excel) use ($d,$statecode,$distval) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$distval)
// {


// $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$distval' group by 1,2";

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
// 'AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
// )
// );  
// });
// })->download();	


}

else if(($_REQUEST['ac'] == '0') and (!empty($_REQUEST['datefilter'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];

$d=$this->commonModel->getunewserbyuserid($user->id);

$arr = array();
$export_data[]=['AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
$headings[]=[];
$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$distval' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";

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


$name_excel = 'datewise report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');  

// return Excel::create('datewise report', function($excel) use ($d,$dte1,$dte2,$statecode,$distval) {
// $excel->sheet('mySheet', function($sheet) use ($d,$dte1,$dte2,$statecode,$distval)
// {
// $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$distval' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";

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
// 'AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
// )
// );  
// });
// })->download();	


}
}
else
{
	if((!empty($_REQUEST['ac'])) and (empty($_REQUEST['datefilter'])))
{
$acid = $req->input('ac');
$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.ac_no = '$acid' group by 1,2";
$records = DB::select($excelrecord);
$pdf = PDF::loadView('admin.pc.deo.Permission.reportac',['user_data' => $d,'records' =>$records]);
return $pdf->download('datewise report'.$cur_time.'.pdf');
return view('admin.pc.deo.Permission.reportac'); 
}

else if(!empty($_REQUEST['ac']) and (!empty($_REQUEST['datefilter'])))
{
$acid = $req->input('ac');
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$acid' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
$pdf = PDF::loadView('admin.pc.deo.Permission.reportac',['user_data' => $d,'records' =>$records]);
return $pdf->download('datewise report'.$cur_time.'.pdf');
return view('admin.pc.deo.Permission.reportac'); 

}

else if(($_REQUEST['ac'] == '0') and (empty($_REQUEST['datefilter'])))
{
$d=$this->commonModel->getunewserbyuserid($user->id);
$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$distval' group by 1,2";
$records = DB::select($excelrecord);
$pdf = PDF::loadView('admin.pc.deo.Permission.reportac',['user_data' => $d,'records' =>$records]);
 return $pdf->download('datewise report'.$cur_time.'.pdf');
 return view('admin.pc.deo.Permission.reportac'); 
}

else if(($_REQUEST['ac'] == '0') and (!empty($_REQUEST['datefilter'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];

$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO_HDQTR = '$distval' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
$pdf = PDF::loadView('admin.pc.deo.Permission.reportac',['user_data' => $d,'records' =>$records]);
 return $pdf->download('datewise report'.$cur_time.'.pdf');
 return view('admin.pc.deo.Permission.reportac'); 

}
}
}

}
}  // end class