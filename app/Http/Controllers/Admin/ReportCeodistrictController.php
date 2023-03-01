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

class ReportCeodistrictController extends Controller
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

public function districtvalue()
{
if (Auth::check()) 
{
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$distvalue = DB::table('m_district')->select('DIST_NAME','DIST_NO')->where('ST_CODE',$d->st_code)->get();
return view('admin.pc.ceo.Permission.reportdistrict', ['distvalue' => $distvalue,'user_data' => $d]);
}
}


public function reportdate(Request $req)
{
if (Auth::check()) 
{
$user = Auth::user();
$d = $this->commonModel->getunewserbyuserid($user->id);
$statecode = $d->st_code;
$cur_time  = Carbon::now();
if($req->input('excel'))
{
if((!empty($_REQUEST['district'])) and (empty($_REQUEST['datefilter'])))
{
$district = $req->input('district');
$d=$this->commonModel->getunewserbyuserid($user->id);


$excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO ='$district' group by 1,2";
 
$records = DB::select($excelrecord);
$arr = array();
$export_data[] = ['District Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];
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
    $record_data->DIST_NAME,
    $record_data->total_request,
    $record_data->approved,
    $record_data->rejected,
    $record_data->inprogress,
    $record_data->pending,
    $record_data->Cancel,
   ];


}


$name_excel = 'District Datewise report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


// return Excel::create('District Datewise report', function($excel) use ($d,$statecode,$district) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$district)
// {

// $excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO ='$district' group by 1,2";
 
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
// $record_data->DIST_NAME,
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
// 'District Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
// )
// );  
// });



// })->download();	
}

else if((!empty($_REQUEST['district'])) and (!empty($_REQUEST['datefilter'])))
{
    // echo "sd";die;
//print_r($_REQUEST);
$district = $req->input('district');
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO = '$district'  and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";

$records = DB::select($excelrecord);
$arr = array();
$export_data[] = ['District Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];
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
    $record_data->DIST_NAME,
    $record_data->total_request,
    $record_data->approved,
    $record_data->rejected,
    $record_data->inprogress,
    $record_data->pending,
    $record_data->Cancel,
   ];


}


$name_excel = 'District Datewise report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


// return Excel::create('District Datewise report', function($excel) use ($d,$statecode,$district,$dte1,$dte2) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$district,$dte1,$dte2)
// {

// $excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO = '$district'  and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";

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
// $record_data->DIST_NAME,
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
// 'District Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
// )
// );  
// });
// })->download();


}

else if(($_REQUEST['district'] == '0')  and (empty($_REQUEST['datefilter'])))
{
$d=$this->commonModel->getunewserbyuserid($user->id);

$excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode'  group by 1,2";

$records = DB::select($excelrecord);
$arr = array();

$export_data[] = ['District Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];
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
    $record_data->DIST_NAME,
    $record_data->total_request,
    $record_data->approved,
    $record_data->rejected,
    $record_data->inprogress,
    $record_data->pending,
    $record_data->Cancel,
   ];


}

$name_excel ='District Datewise report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');  


// return Excel::create('District Datewise report', function($excel) use ($d,$statecode) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode)
// {

// $excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode'  group by 1,2";

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
// $record_data->DIST_NAME,
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
// 'District Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
// )
// );  
// });
// })->download();	
}

else if(($_REQUEST['district'] == '0') and (!empty($_REQUEST['datefilter'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$d=$this->commonModel->getunewserbyuserid($user->id);

$export_data[] = ['District Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'];
 $headings[]=[];

 $excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
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
    $record_data->DIST_NAME,
    $record_data->total_request,
    $record_data->approved,
    $record_data->rejected,
    $record_data->inprogress,
    $record_data->pending,
    $record_data->Cancel,
   ];


}

$name_excel = 'District Datewise report';
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


// return Excel::create('District Datewise report', function($excel) use ($d,$statecode,$dte1,$dte2) {
// $excel->sheet('mySheet', function($sheet) use ($d,$statecode,$dte1,$dte2)
// {

// $excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
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
// $record_data->DIST_NAME,
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
// 'District Name','Total Request','Accepted','Rejected','Inprogress','Pending','Cancel'
// )
// );  
// });
// })->download();	
}
}
else
{
if((!empty($_REQUEST['district'])) and (empty($_REQUEST['datefilter'])))
{
$district = $req->input('district');
$excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO ='$district' group by 1,2";
$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportdist',['user_data' => $d,'records' =>$records]);
 return $pdf->download('District Datewise report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportdist'); 
}

else if((!empty($_REQUEST['district'])) and (!empty($_REQUEST['datefilter'])))
{
$district = $req->input('district');
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and s.DIST_NO = '$district'  and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportdist',['user_data' => $d,'records' =>$records]);
 return $pdf->download('District Datewise report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportdist'); 
}

else if(($_REQUEST['district'] == '0')  and (empty($_REQUEST['datefilter'])))
{
$excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode'  group by 1,2";

$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportdist',['user_data' => $d,'records' =>$records]);
 return $pdf->download('District Datewise report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportdist'); 
}

else if(($_REQUEST['district'] == '0') and (!empty($_REQUEST['datefilter'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$excelrecord= "SELECT s.ST_CODE,s.DIST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p RIGHT JOIN m_district s ON s.DIST_NO=p.dist_no AND s.ST_CODE=p.st_code where s.ST_CODE='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' group by 1,2";
$records = DB::select($excelrecord);
 $pdf = PDF::loadView('admin.pc.ceo.Permission.reportdist',['user_data' => $d,'records' =>$records]);
 return $pdf->download('District Datewise report'.$cur_time.'.pdf');
 return view('admin.pc.ceo.Permission.reportdist'); 
}
 
}
}
} 
} // end class