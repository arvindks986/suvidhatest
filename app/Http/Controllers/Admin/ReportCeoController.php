<?php
namespace App\Http\Controllers\Admin;
use App\adminmodel\ReportModel;
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
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;

// use Excel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportCeoController extends Controller
{
/**
* Create a new controller instance.
*
* @return void
*/
public function __construct(){   
$this->middleware('adminsession');
$this->middleware(['auth:admin','auth']);
$this->middleware('ceo');
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

public function reportceo()
{
if (Auth::check()) 
{
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$distvalue = DB::table('m_pc')->where('ST_CODE',$d->st_code)->get();
//dd($distvalue);
$data=DB::table('permission_request as a')
->join('user_login as b','a.user_id','=','b.id')
->join('user_data as ud','ud.user_login_id','=','b.id')
->join('user_role as c','b.role_id','=','c.role_id')
->join('permission_type as d','a.permission_type_id','=','d.id')
->join('permission_master as m','m.id','=','d.permission_type_id')
->select('a.*','ud.name','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
->where('a.st_code',$d->st_code)
->get()->toArray();
return view('admin.pc.ceo.Permission.reportceo', ['data' => $data,'distvalue' => $distvalue,'user_data' => $d]);
} 
}
public function getDistrictsval(Request $req)
{
if (Auth::check()) 
{
if(base64_decode($_REQUEST['pc']))
{
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$pc = base64_decode($_REQUEST['pc']);
$distvalue = DB::table('m_ac')->select('AC_NAME','AC_NO')->where('ST_CODE',$d->st_code)->where('PC_NO',$pc)->get();
return $distvalue;
}
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
if($user_data)
{

    $excelrecord= "SELECT PARTYNAME,permission_name,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel
FROM `permission_request` p
JOIN `permission_type` t ON t.`id`=p.`permission_type_id`
LEFT JOIN permission_master s ON s.id=t.permission_type_id
LEFT JOIN m_party mp ON mp.CCODE=p.party_id  WHERE p.st_code='$statecode' GROUP BY permission_name,PARTYNAME";

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


    $name_excel = 'report';
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 
    
    
// return Excel::create('report', function($excel) use ($d,$statecode) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode)
// {


// $excelrecord= "SELECT PARTYNAME,permission_name,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel
// FROM `permission_request` p
// JOIN `permission_type` t ON t.`id`=p.`permission_type_id`
// LEFT JOIN permission_master s ON s.id=t.permission_type_id
// LEFT JOIN m_party mp ON mp.CCODE=p.party_id  WHERE p.st_code='$statecode' GROUP BY permission_name,PARTYNAME";

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

public function permissiontype()
{
if (Auth::check()) 
{
$user = Auth::user();
$d=$this->commonModel->getunewserbyuserid($user->id);
$user_data = $d;
$statecode = $d->st_code;
if($user_data)
{

    $excelrecord= "SELECT permission_name, COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` LEFT JOIN permission_master s ON s.id=t.permission_type_id where p.st_code = '$statecode' GROUP BY permission_name";
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


    $name_excel = 'report';
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


// return Excel::create('report', function($excel) use ($d,$statecode) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode)
// {


// $excelrecord= "SELECT permission_name, COUNT(user_id)total_request, COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p JOIN `permission_type` t ON t.`id`=p.`permission_type_id` LEFT JOIN permission_master s ON s.id=t.permission_type_id where p.st_code = '$statecode' GROUP BY permission_name";
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
$cur_time  = Carbon::now();
if($req->input('excel'))
{
if(($_REQUEST['pc'] == 'statevalue') and (empty($_REQUEST['ac'])) and (empty($_REQUEST['datefilter'])) )
{
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT sp.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND sp.ST_CODE=p.st_code where sp.ST_CODE='$statecode' group by 1,2";
 

$records = DB::select($excelrecord);
$arr = array();
$export_data[] = ['State Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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
    $record_data->ST_NAME,
$record_data->total_request,
$record_data->approved,
$record_data->rejected,
$record_data->inprogress,
$record_data->pending,
$record_data->Cancel,
   ];


}

$name_excel = 'report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

// return Excel::create('report', function($excel) use ($d,$statecode) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode)
// {


//  $excelrecord= "SELECT sp.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND sp.ST_CODE=p.st_code where sp.ST_CODE='$statecode' group by 1,2";
 

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
// $record_data->ST_NAME,
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
// 'State Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
// )
// );  
// });



// })->download();	
}

else if((($_REQUEST['pc']) == 'statevalue') and (empty($_REQUEST['ac'])) and (!empty($_REQUEST['datefilter'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
$arr = array();
$export_data[] = ['State Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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
    $record_data->ST_NAME,
$record_data->total_request,
$record_data->approved,
$record_data->rejected,
$record_data->inprogress,
$record_data->pending,
$record_data->Cancel,
   ];


}

$name_excel = 'report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');  


// return Excel::create('report', function($excel) use ($d,$statecode,$dte1,$dte2) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$dte1,$dte2)
// {

    
// $excelrecord= "SELECT s.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
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
// $record_data->ST_NAME,
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
// 'State Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
// )
// );  
// });



// })->download();	
}
else if(($_REQUEST['pc'] == 'all') and (empty($_REQUEST['datefilter'])) and (empty($_REQUEST['ac'])))
{
$d=$this->commonModel->getunewserbyuserid($user->id);


$excelrecord= "SELECT s.ST_CODE,s.PC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' group by 1,2";
$records = DB::select($excelrecord);
$arr = array();
$export_data[] = ['PC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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
    $record_data->PC_NAME,
$record_data->total_request,
$record_data->approved,
$record_data->rejected,
$record_data->inprogress,
$record_data->pending,
$record_data->Cancel,
   ];


}

$name_excel = 'report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


// return Excel::create('report', function($excel) use ($d,$statecode) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode)
// {


// $excelrecord= "SELECT s.ST_CODE,s.PC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' group by 1,2";
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
// $record_data->PC_NAME,
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
// 'PC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
// )
// );  
// });



// })->download();	
}

else if((($_REQUEST['pc']) == 'all') and (empty($_REQUEST['ac'])) and (!empty($_REQUEST['datefilter'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,s.PC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
$arr = array();
$export_data[] = ['PC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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
    $record_data->PC_NAME,
$record_data->total_request,
$record_data->approved,
$record_data->rejected,
$record_data->inprogress,
$record_data->pending,
$record_data->Cancel,
   ];



}

$name_excel = 'report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');  


// return Excel::create('report', function($excel) use ($d,$statecode,$dte1,$dte2) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$dte1,$dte2)
// {


// $excelrecord= "SELECT s.ST_CODE,s.PC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
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
// $record_data->PC_NAME,
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
// 'PC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
// )
// );  
// });



// })->download();	
}

else if((!empty($_REQUEST['pc'])) and (empty($_REQUEST['datefilter'])) and (empty($_REQUEST['ac'])))
{
$pc = $req->input('pc');
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code  and s.PC_NO=p.pc_no where s.ST_CODE='$statecode' and s.PC_NO = '$pc' group by 1,2";
 
$records = DB::select($excelrecord);
$arr = array();
$export_data[] = ['AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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

$name_excel = 'report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');  

// return Excel::create('report', function($excel) use ($d,$statecode,$pc) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$pc)
// {


// $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code  and s.PC_NO=p.pc_no where s.ST_CODE='$statecode' and s.PC_NO = '$pc' group by 1,2";
 
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

else if((!empty($_REQUEST['pc'])) and (!empty($_REQUEST['ac'])) and (!empty($_REQUEST['datefilter'])))
{
//print_r($_REQUEST);
$pc = $req->input('pc');
$ac = $req->input('ac');
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac' and s.PC_NO = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";

$records = DB::select($excelrecord);
$arr = array();

$export_data[] = ['AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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

$name_excel = 'report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


// return Excel::create('report', function($excel) use ($d,$statecode,$ac,$pc,$dte1,$dte2) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$ac,$pc,$dte1,$dte2)
// {

// $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac' and s.PC_NO = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";

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

else if((!empty($_REQUEST['pc'])) and (!empty($_REQUEST['ac'])) and (empty($_REQUEST['datefilter'])))
{
$pc = $req->input('pc');
$ac = $req->input('ac');
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac' and s.PC_NO = '$pc' group by 1,2";

$records = DB::select($excelrecord);
$arr = array();

$export_data[] = ['AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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


$name_excel = 'report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 

// return Excel::create('report', function($excel) use ($d,$statecode,$ac,$pc) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$ac,$pc)
// {
// $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac' and s.PC_NO = '$pc' group by 1,2";

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

else if((!empty($_REQUEST['pc'])) and (empty($_REQUEST['ac'])) and (!empty($_REQUEST['datefilter'])))
{
$pc = $req->input('pc');
$acval = $this->commonModel->getallacbypcno($statecode,$pc);
$acvalue = $acval->AC_NO;
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code and s.PC_NO=p.pc_no where s.ST_CODE='$statecode' and s.PC_NO = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
"";


$records = DB::select($excelrecord);
$arr = array();
$export_data[] = ['AC Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'];
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


$name_excel = 'report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


// return Excel::create('report', function($excel) use ($d,$statecode,$pc,$dte1,$dte2) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$pc,$dte1,$dte2)
// {

// $excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code and s.PC_NO=p.pc_no where s.ST_CODE='$statecode' and s.PC_NO = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
// "";


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
if(($_REQUEST['pc'] == 'statevalue') and (empty($_REQUEST['ac'])) and (empty($_REQUEST['datefilter'])))
{
 $excelrecord= "SELECT sp.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND sp.ST_CODE=p.st_code where sp.ST_CODE='$statecode' group by 1,2";
 $records = DB::select($excelrecord);
 $cur_time  = Carbon::now();
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportpage',['user_data' => $d,'records' =>$records]);
 return $pdf->download('report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportpage'); 
}
else if((($_REQUEST['pc']) == 'statevalue') and (empty($_REQUEST['ac'])) and (!empty($_REQUEST['datefilter'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$excelrecord= "SELECT s.ST_CODE,sp.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no RIGHT JOIN m_state sp ON sp.ST_CODE = '$statecode' AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
$cur_time  = Carbon::now();
$pdf = PDF::loadView('admin.pc.ceo.Permission.reportpage',['user_data' => $d,'records' =>$records]);
return $pdf->download('report'.$cur_time.'.pdf');
return view('admin.pc.ceo.Permission.reportpage'); 
}
else if(($_REQUEST['pc'] == 'all') and (empty($_REQUEST['datefilter'])) and (empty($_REQUEST['ac'])))
{
$excelrecord= "SELECT s.ST_CODE,s.PC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' group by 1,2";
$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportpc',['user_data' => $d,'records' =>$records]);
 return $pdf->download('report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportpc'); 
}
else if((($_REQUEST['pc']) == 'all') and (empty($_REQUEST['ac'])) and (!empty($_REQUEST['datefilter'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$excelrecord= "SELECT s.ST_CODE,s.PC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_pc s ON s.PC_NO=p.pc_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportpc',['user_data' => $d,'records' =>$records]);
 return $pdf->download('report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportpc'); 
}
else if((!empty($_REQUEST['pc'])) and (empty($_REQUEST['datefilter'])) and (empty($_REQUEST['ac'])))
{
$pc = $req->input('pc');
$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code  and s.PC_NO=p.pc_no where s.ST_CODE='$statecode' and s.PC_NO = '$pc' group by 1,2";
$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportac',['user_data' => $d,'records' =>$records]);
 return $pdf->download('report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportac'); 
}

else if((!empty($_REQUEST['pc'])) and (!empty($_REQUEST['ac'])) and (!empty($_REQUEST['datefilter'])))
{
//print_r($_REQUEST);
$pc = $req->input('pc');
$ac = $req->input('ac');
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac' and s.PC_NO = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportac',['user_data' => $d,'records' =>$records]);
 return $pdf->download('report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportac'); 
}

else if((!empty($_REQUEST['pc'])) and (!empty($_REQUEST['ac'])) and (empty($_REQUEST['datefilter'])))
{
$pc = $req->input('pc');
$ac = $req->input('ac');
$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.AC_NO = '$ac' and s.PC_NO = '$pc' group by 1,2";

$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportac',['user_data' => $d,'records' =>$records]);
 return $pdf->download('report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportac'); 
}

else if((!empty($_REQUEST['pc'])) and (empty($_REQUEST['ac'])) and (!empty($_REQUEST['datefilter'])))
{
$pc = $req->input('pc');
$acval = $this->commonModel->getallacbypcno($statecode,$pc);
$acvalue = $acval->AC_NO;
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$excelrecord= "SELECT s.ST_CODE,s.AC_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_ac s ON s.AC_NO=p.ac_no AND s.ST_CODE=p.st_code and s.PC_NO=p.pc_no where s.ST_CODE='$statecode' and s.PC_NO = '$pc' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportac',['user_data' => $d,'records' =>$records]);
 return $pdf->download('report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportac'); 
}

}
}
} 
} // end class