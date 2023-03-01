<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
//use App\adminmodel\AROPcPermissionModel;
use Illuminate\Http\Request;
use Session;
use DB;
use App\commonModel;
use App\adminmodel\CandidateModel;
use App\adminmodel\ROPCModel;
use App\Classes\xssClean;
use PDF;
use MPDF;
use Carbon\Carbon;
use Excel;
class AROPermissionController extends Controller {

    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('aro');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
       // $this->AROPM = new AROPcPermissionModel();
    }


/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 15-04-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return permissionrawreport report on ARO Level    
  */	 
  public function permissionrawreport(Request $request)
	  { //dd($request->all());
		if (Auth::check()) 
	    {
		$user = Auth::user();
		$d=$this->commonModel->getunewserbyuserid($user->id);
		$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $user_data = $d;
		$st_code = $d->st_code;
		//dd($d);
		$cur_time    = Carbon::now();

		return Excel::create('AROPermissionRawReport'.trim($st_code).'_'.$cur_time, function($excel) use ($d) {
		$excel->sheet('mySheet', function($sheet) use ($d)
	    {
		$st_code = $d->st_code;
		$ac_no = $d->ac_no;
		$allrecord=  $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->join('m_party as mp','mp.CCODE','=','a.party_id')
                ->join('m_state as ms','ms.ST_CODE','=','a.st_code')
                ->select('a.*','ud.name','c.role_name','mp.PARTYNAME','ms.ST_NAME','m.permission_name as pname','a.id as permission_id','b.id as login _id')
				->where('a.st_code',$st_code)
				->where('a.ac_no',$ac_no)
                ->get()->toArray();
        $arr  = array();
		foreach($allrecord as $excelrecord)
		{	
		$uservalue = DB::table('user_data')
                ->select('*')
				->where('user_login_id',$excelrecord->user_id) 
                ->get();
		$ac = getacbyacno($d->st_code,$d->ac_no);
		$st=getstatebystatecode($d->st_code);
		$dist=getdistrictbydistrictno($d->st_code,$d->dist_no);
		
         
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
		   if($excelrecord->cancel_status== 1)
		   {
			   $cancelstatus = 'Cancel';
		   }
		   else if($excelrecord->cancel_status == 0)
		   {
			   $cancelstatus  =  $status;
		   }
			$data =  array(
			$excelrecord->id,
			$st->ST_NAME,
			$dist->DIST_NAME,
			$ac->AC_NAME,
			$uservalue[0]->name,
			$excelrecord->pname,
			$excelrecord->role_name,
			$excelrecord->PARTYNAME,
			$excelrecord->added_at,
			$excelrecord->updated_at,
			$pmode,
			$status,
			$cancelstatus,
			);
         array_push($arr, $data);
		}
		$sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
		'Permission ID','State Name','District Name','AC Name','User Name','Permission Type', 'User Type','Party Name','Date of Submission','Action Date','Permission Mode','Previous Status','Current Status'
		)
		);
	    });
		})->download();
		
		}
		else 
		{
        return redirect('/officer-login');
        }  
	  } // end permissionrawreport function

/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 15-04-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return partywise permission  report on ARO Level    
  */

public function permissionpartywisereport(Request $request)
{
if (Auth::check()) 
{
$user = Auth::user();
$d=$this->commonModel->getunewserbyuserid($user->id);
$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

$user_data = $d;
$st_code = $d->st_code;
if($user_data)
{
$cur_time    = Carbon::now();

return Excel::create('AROPermissionPartyWiseReport'.trim($st_code).'_'.$cur_time, function($excel) use ($d) {
$excel->sheet('mySheet', function($sheet) use ($d)
{ 
	$st_code = $d->st_code;
	$ac_no = $d->ac_no;
$excelrecord= "SELECT PARTYNAME,permission_name,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel
FROM `permission_request` p
JOIN `permission_type` t ON t.`id`=p.`permission_type_id`
LEFT JOIN permission_master s ON s.id=t.permission_type_id
LEFT JOIN m_party mp ON mp.CCODE=p.party_id 
WHERE p.st_code='$st_code' AND p.ac_no=$ac_no
GROUP BY permission_name,PARTYNAME";

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
$record_data->PARTYNAME,
$record_data->permission_name,
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
'Party Name','Permission Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
)
);  
});
})->download();	
}
}
else 
{
return redirect('/officer-login');
} 
} //end partywise permission report


/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 15-04-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return partywise permission  report EXL on ARO Level    
  */
public function permissionreportbydate(Request $request)
{
	//dd($request->all());
if (Auth::check()) 
{
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

$distvalue = DB::table('m_pc')->where('ST_CODE',$d->st_code)->get();
//dd($d);
$data=DB::table('permission_request as a')
->join('user_login as b','a.user_id','=','b.id')
->join('user_data as ud','ud.user_login_id','=','b.id')
->join('user_role as c','b.role_id','=','c.role_id')
->join('permission_type as d','a.permission_type_id','=','d.id')
->join('permission_master as m','m.id','=','d.permission_type_id')
->select('a.*','ud.name','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
->where('a.st_code',$d->st_code)
->where('a.ac_no',$d->ac_no)
->get()->toArray();
return view('admin.pc.ro.Permission.AroPermissionDatewise-report', ['data' => $data,'distvalue' => $distvalue,'user_data' => $d]);
 } 
 }
 

/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 15-04-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return Datewise permission  report PDF on ARO Level    
  */
public function permissionreportbydateFilter(Request $request)
{ //dd($request->all());
	DB::enableQueryLog();
if (Auth::check()) 
{
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
//dd($d);
$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

$statecode = $d->st_code;
if((!empty($_REQUEST['datefilter'])) && ($_REQUEST['datefilter']!=null) && $_REQUEST['submit']=='Export Excel')
{
$datevalue = $request->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$cur_time    = Carbon::now();
$st_code = $d->st_code;
$ac_no = $d->ac_no;
return Excel::create('AROPermissionDateWiseReport'.trim($st_code).'_'.$ac_no.'_'.$cur_time, function($excel) use ($d,$dte1,$dte2) {
$excel->sheet('mySheet', function($sheet) use ($d,$dte1,$dte2)
{ $st_code = $d->st_code;
  $ac_no = $d->ac_no;
  $pc_no=$d->pc_no;


$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,
COUNT(user_id)total_request,
COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved,
COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, 
COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL)) inprogress, 
COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending,
COUNT(IF(cancel_status=1,user_id,NULL))Cancel
FROM `permission_request` p 
RIGHT JOIN m_ac s 
ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code 
where s.ST_CODE='$st_code' and s.PC_NO = '$pc_no' and s.AC_NO = '$ac_no' and DATE(p.created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";


$records = DB::select($excelrecord);
//dd($records);
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
$ac = getacbyacno($d->st_code,$d->ac_no);
$st=getstatebystatecode($d->st_code);
$data =  array(
$st->ST_NAME,
$ac->AC_NAME,
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
'State Name','AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
)
);  
});
})->download();	
}elseif((empty($_REQUEST['datefilter'])) && ($_REQUEST['datefilter']==null) && $_REQUEST['submit']=='Export Excel')
{  //dd('Filter');
	$cur_time    = Carbon::now();
	$st_code = $d->st_code;
	$ac_no = $d->ac_no;
	
	return Excel::create('AROPermissionDateWiseReport'.trim($st_code).'_'.$ac_no.'_'.$cur_time, function($excel) use ($d) {
	$excel->sheet('mySheet', function($sheet) use ($d)
	{ $st_code = $d->st_code;
	  $ac_no = $d->ac_no;
	  $pc_no=$d->pc_no;
	//$excelrecord= "SELECT s.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";

	$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,
	COUNT(user_id)total_request,
	COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved,
	COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, 
	COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL)) inprogress, 
	COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending,
	COUNT(IF(cancel_status=1,user_id,NULL))Cancel
	FROM `permission_request` p 
	RIGHT JOIN m_ac s 
	ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code 
	where s.ST_CODE='$st_code' and s.PC_NO = '$pc_no' and s.AC_NO = '$ac_no' group by 1,2";

	
	$records = DB::select($excelrecord);

	//dd(DB::getQueryLog());
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
    $ac = getacbyacno($d->st_code,$d->ac_no);
    $st=getstatebystatecode($d->st_code);
	$data =  array(
	$st->ST_NAME,
	$ac->AC_NAME,
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
	'State Name','AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
	)
	);  
	});
	})->download();	
  }elseif((empty($_REQUEST['datefilter'])) && ($_REQUEST['datefilter']==null) && $_REQUEST['submit']=='Export Pdf')
  {
	  $cur_time    = Carbon::now();
	  $st_code = $d->st_code;
	  $ac_no = $d->ac_no;
	  $pc_no=$d->pc_no;
	
	
$pdfrecord= "SELECT s.ST_CODE,s.AC_NAME,
	COUNT(user_id)total_request,
	COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved,
	COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, 
	COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL)) inprogress, 
	COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending,
	COUNT(IF(cancel_status=1,user_id,NULL))Cancel
	FROM `permission_request` p 
	RIGHT JOIN m_ac s 
	ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code 
	where s.ST_CODE='$st_code' and s.PC_NO = '$pc_no' and s.AC_NO = '$ac_no' group by 1,2";

	  
	  $pdfrecordData = DB::select($pdfrecord);
	//  dd(DB::getQueryLog());
	  $pdf = PDF::loadView('admin.pc.ro.Permission.permissiondatewisePDFhtml',compact('d',$d,'pdfrecordData',$pdfrecordData,'ele_details',$ele_details));
	  //return $pdf->download('form4Apdf.pdf');
      //return view('admin.pc.ro.form4Apdf');
	  //$pdf = MPDF::loadView('admin.pc.ro.form4Apdf', compact('date' ,'datewiseform3alist','state','const_name','ele_details'));
	   return $pdf->download($st_code.'/'.$ac_no."-Permission-distwiseReport-".".pdf");
	
	}elseif((!empty($_REQUEST['datefilter'])) && ($_REQUEST['datefilter']!=null) && $_REQUEST['submit']=='Export Pdf')
	{ 	$date=$_REQUEST['datefilter'];
		$date_range = explode('~', $date);
		$from_date=$date_range[0];
		$to_date=$date_range[1];
		$fromdate = date('Y-m-d',strtotime($from_date));
		$todate = date('Y-m-d',strtotime($to_date));
	    $st_code = $d->st_code;
		$ac_no = $d->ac_no;
		$pc_no = $d->pc_no;
	
	$pdfrecord= "SELECT s.ST_CODE,s.AC_NAME,
	COUNT(user_id)total_request,
	COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved,
	COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, 
	COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL)) inprogress, 
	COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending,
	COUNT(IF(cancel_status=1,user_id,NULL))Cancel
	FROM `permission_request` p 
	RIGHT JOIN m_ac s 
	ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code 
	where s.ST_CODE='$st_code' and s.PC_NO = '$pc_no' and s.AC_NO = '$ac_no' and DATE(p.created_at) BETWEEN '$fromdate' AND '$todate' group by 1,2";
		$pdfrecordData = DB::select($pdfrecord);
		//dd($pdfrecordData);
		$pdf = PDF::loadView('admin.pc.ro.Permission.permissiondatewisePDFhtml',compact('d',$d,'pdfrecordData',$pdfrecordData,'ele_details',$ele_details));
	  //return $pdf->download('form4Apdf.pdf');
      //return view('admin.pc.ro.form4Apdf');
	  //$pdf = MPDF::loadView('admin.pc.ro.form4Apdf', compact('date' ,'datewiseform3alist','state','const_name','ele_details'));
	  return $pdf->download($st_code.'/'.$ac_no."-Permission-distwiseReport-".".pdf");
	
	  }
 }
} //end datewise permission exl function


/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 17-04-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return  permission type  report PDF on ARO Level    
  */
public function permissiontype()
{
if (Auth::check()) 
{
$user = Auth::user();
$d=$this->commonModel->getunewserbyuserid($user->id);
$user_data = $d;
$statecode = $d->st_code;
if($user_data)
{	$cur_time    = Carbon::now();
	$st_code = $d->st_code;
	$ac_no = $d->ac_no;
	
return Excel::create('AROPermissionTypeReport'.trim($st_code).'_'.$ac_no.'_'.$cur_time, function($excel) use ($d) {

$excel->sheet('mySheet', function($sheet) use ($d)
{ $st_code = $d->st_code;
	$ac_no = $d->ac_no;
 $excelrecord= "SELECT permission_name, 
 COUNT(user_id)total_request, 
 COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved,
 COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, 
 COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, 
 COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, 
 COUNT(IF(cancel_status=1,user_id,NULL))Cancel 
 FROM `permission_request` p JOIN `permission_type` t 
 ON t.`id`=p.`permission_type_id` LEFT JOIN permission_master s 
 ON s.id=t.permission_type_id 
 where p.st_code = '$st_code' AND p.ac_no=$ac_no
 GROUP BY permission_name";
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
$record_data->permission_name,
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
'Permission Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
)
);  
});
})->download();	
}


}
else 
{
return redirect('/officer-login');
} 
} // end permission type report

} //end class
