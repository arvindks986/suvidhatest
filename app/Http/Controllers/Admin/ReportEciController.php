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
use App\adminmodel\ECIModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\StateModel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportEciController extends Controller
{
/**
* Create a new controller instance.
*
* @return void
*/
public function __construct(){   
$this->middleware(['auth:admin','auth']);
$this->middleware('eci');
$this->commonModel = new commonModel();
$this->ECIModel = new ECIModel();
$this->PM = new ReportModel();

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

public function report()
{

if (Auth::check()) 
{
           $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
             $statevalue = StateModel::get_states();  
              $data = DB::table('permission_request as a')
                               ->join('m_state as st','a.st_code','=','st.ST_CODE')
                               ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                               ->select('st.ST_NAME',DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),
                               DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),
                               DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),
                               DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),
                               DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                              
                               ->groupBy('st.ST_CODE'); 
                               $data->get();
                                $result=$data->get()->toArray(); 
            return view('admin.pc.eci.report', ['election'=>0,'datefilter'=>0,'state'=>0, 'statevalue' => $statevalue, 'user_data' => $d,'datereport' => $result]);
}
}


/*public function report() {

        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $statevalue = StateModel::get_states();
            return view('admin.pc.eci.report', ['election'=>0,'datefilter'=>'','state'=>0,'statevalue' => $statevalue, 'user_data' => $d]);
        }
    }
*/
    public function reportdates(Request $req) {
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
             $cur_time    = Carbon::now();
            $name_excel = 'datewise report'.'_'.$cur_time;
            $headings[] = ['State Name', 'Total Request', 'Accepted', 'Rejected', 'Inprogess', 'Pending', 'Cancel'];
            $datevalue = $req->input('datefilter');
            if(!empty($datevalue))
            {
            $dates = explode("~", $datevalue);
            $dte1 = $dates[0];
            $dte2 = $dates[1];
            }
            $data = DB::table('permission_request as a')
                               ->join('m_state as st','a.st_code','=','st.ST_CODE')
                               ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                               ->select('st.ST_NAME',DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                              
                               ->groupBy('st.ST_CODE');
                               if(!empty($_REQUEST['elect']))
                               {
                                    $data->where('med.ELECTION_TYPEID',$_REQUEST['elect']);
                               }
                               if(!empty($_REQUEST['state']))
                               {
                                    $data->where('a.st_code',$_REQUEST['state']);
                               }
                               if(!empty($_REQUEST['datefilter']))
                               {
                                    $data->whereBetween('a.created_at',[$dte1,$dte2]);
                               }
                               $data->get();
                               $result=$data->get()->toArray();
            if ($req->input('excel')) {
                return Excel::download(new ExcelExport($headings, $result), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
            } else {
                $pdf = PDF::loadView('admin.pc.eci.reportpagetotal', ['user_data' => $d, 'records' => $result]);
                return $pdf->download('report' . $cur_time . '.pdf');
//                
            }
        } else {
            return redirect('/officer-login');
        }
    }
    
    public function reportdatesview(Request $req) {
        //dd($req);
        if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $cur_time    = Carbon::now();
            $perm = $this->PM->getpermisson();
            $statevalue = StateModel::get_states();
            $datevalue = $req->input('datefilter');
            if(!empty($datevalue))
            {
            $dates = explode("~", $datevalue);
            $dte1 = $dates[0];
            $dte2 = $dates[1];
            }
            if($req->method() == 'POST')
                {
                $data = DB::table('permission_request as a')
                               ->join('m_state as st','a.st_code','=','st.ST_CODE')
                               ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                               ->select('st.ST_NAME',DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                              
                               ->groupBy('st.ST_CODE');

                               if(!empty($_REQUEST['elect']))
                               {
                                    $data->where('med.ELECTION_TYPEID',$_REQUEST['elect']);
                               }
                               if(!empty($_REQUEST['state']))
                               {
                                    $data->where('a.st_code',$_REQUEST['state']);
                               }
                               if(!empty($_REQUEST['datefilter']))
                               {
                                    $data->whereBetween('a.created_at',[$dte1,$dte2]);
                               }
                               $data->get();
                                $result=$data->get()->toArray();
                                //dd($result);
                                return view('admin.pc.eci.report', ['election'=>$_REQUEST['elect'],'state' => $_REQUEST['state'], 'user_data' => $d,'datereport'=>$result,'datefilter'=>$_REQUEST['datefilter'],'statevalue' => $statevalue, 'perm' => $perm]);
                } 
                else {
              return view('admin.pc.eci.report', ['election'=>0,'state' =>0, 'user_data' => $d,'datereport'=>'','datefilter'=>'','statevalue' => $statevalue, 'perm' => $perm,'allrecord' => $allrecord]);
            }
        } else {
            return redirect('/officer-login');
        }
    }

public function reportdatesxxxx(Request $req)
{
if (Auth::check()) 
{
$user = Auth::user();
$d=$this->commonModel->getunewserbyuserid($user->id);
$user_data = $d;
if($req->input('excel'))
{
if(($_REQUEST['state'] == '0') and (empty($_REQUEST['state'])) and (empty($_REQUEST['datefilter'])) )
{

    $excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code GROUP BY 1";
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


// return Excel::create('report', function($excel) use ($d) {
// $excel->sheet('mySheet', function($sheet) use ($d)
// {


// $excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code GROUP BY 1";
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
// $excl = "SELECT COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request`";
// $excl = DB::select($excl);
// $sheet->setCellValue('A38', 'Total');
// $sheet->setCellValue('B38', $excl[0]->total_request);
// $sheet->setCellValue('C38', $excl[0]->approved);
// $sheet->setCellValue('D38', $excl[0]->rejected);
// $sheet->setCellValue('E38', $excl[0]->inprogress);
// $sheet->setCellValue('F38', $excl[0]->pending);
// $sheet->setCellValue('G38', $excl[0]->Cancel);
// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
// 'State Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
// )
// );  
// });
// })->download();  




}
else if(!empty($_REQUEST['datefilter']) && (!empty($_REQUEST['state'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$statecode = $req->input('state');
$d=$this->commonModel->getunewserbyuserid($user->id);


$excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code 
WHERE p.st_code ='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' GROUP BY 1";
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



// return Excel::create('report', function($excel) use ($d,$dte1,$dte2,$statecode) {
// $excel->sheet('mySheet', function($sheet) use ($d,$dte1,$dte2,$statecode)
// {


// $excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code 
// WHERE p.st_code ='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' GROUP BY 1";
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
else if(!empty($_REQUEST['datefilter'])  &&  $_REQUEST['state'] == '0' )
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$statecode = $req->input('state');
$user = Auth::user();

$d=$this->commonModel->getunewserbyuserid($user->id);


$excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code 
and DATE(created_at) BETWEEN '$dte1' AND '$dte2' GROUP BY 1";


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


// return Excel::create('report', function($excel) use ($d,$dte1,$dte2) {
// $excel->sheet('mySheet', function($sheet) use ($d,$dte1,$dte2)
// {

// $excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code 
// and DATE(created_at) BETWEEN '$dte1' AND '$dte2' GROUP BY 1";


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

// $excl = "SELECT COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` where DATE(created_at) BETWEEN '$dte1' AND '$dte2'";
// $excl = DB::select($excl);
// $sheet->setCellValue('A38', 'Total');
// $sheet->setCellValue('B38', $excl[0]->total_request);
// $sheet->setCellValue('C38', $excl[0]->approved);
// $sheet->setCellValue('D38', $excl[0]->rejected);
// $sheet->setCellValue('E38', $excl[0]->inprogress);
// $sheet->setCellValue('F38', $excl[0]->pending);
// $sheet->setCellValue('G38', $excl[0]->Cancel);

// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
// 'State Name','Total Request','Accepted','Rejected','Inprogess','Pending','Cancel'
// )
// );  
// });



// })->download();
}
else if((!empty($_REQUEST['state'])) and (empty($_REQUEST['datefilter'])))
{
$statecode = $req->input('state');
$user = Auth::user();

$d=$this->commonModel->getunewserbyuserid($user->id);


$excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code 
where p.st_code ='$statecode' GROUP BY 1";
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


// $excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code 
// where p.st_code ='$statecode' GROUP BY 1";
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

}
else
{
if(($_REQUEST['state'] == '0') and (empty($_REQUEST['state'])) and (empty($_REQUEST['datefilter'])) )
{
$excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code GROUP BY 1";
$records = DB::select($excelrecord);
$cur_time  = Carbon::now();
$excl = "SELECT COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request`";
$excl = DB::select($excl);
$pdf = PDF::loadView('admin.pc.eci.reportpagetotal',['excl' => $excl,'user_data' => $d,'records' =>$records]);
return $pdf->download('report'.$cur_time.'.pdf');
return view('admin.pc.eci.reportpagetotal');  
}
else if(!empty($_REQUEST['datefilter']) && (!empty($_REQUEST['state'])))
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$statecode = $req->input('state');
$excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code 
WHERE p.st_code ='$statecode' and DATE(created_at) BETWEEN '$dte1' AND '$dte2' GROUP BY 1";
$records = DB::select($excelrecord);
$cur_time  = Carbon::now();
$pdf = PDF::loadView('admin.pc.eci.reportpage',['user_data' => $d,'records' =>$records]);
return $pdf->download('report'.$cur_time.'.pdf');
return view('admin.pc.eci.reportpage');  
    
}
else if(!empty($_REQUEST['datefilter'])  &&  $_REQUEST['state'] == '0' )
{
$datevalue = $req->input('datefilter');
$dates = explode("~",$datevalue);
$dte1 = $dates[0];
$dte2 = $dates[1];
$statecode = $req->input('state');
$excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code 
and DATE(created_at) BETWEEN '$dte1' AND '$dte2' GROUP BY 1";
$records = DB::select($excelrecord);
$cur_time  = Carbon::now();
$excl = "SELECT COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` where DATE(created_at) BETWEEN '$dte1' AND '$dte2'";
$excl = DB::select($excl);
$pdf = PDF::loadView('admin.pc.eci.reportpagetotal',['excl' => $excl,'user_data' => $d,'records' =>$records]);
return $pdf->download('report'.$cur_time.'.pdf');
return view('admin.pc.eci.reportpagetotal');  

}
else if((!empty($_REQUEST['state'])) and (empty($_REQUEST['datefilter'])))
{
$statecode = $req->input('state');
$excelrecord= "SELECT s.ST_NAME,COUNT(user_id)total_request,COUNT(IF(approved_status=2 and cancel_status=0,user_id,NULL)) approved, COUNT(IF(approved_status=3 and cancel_status=0,user_id,NULL)) rejected, COUNT(IF(approved_status=1 and cancel_status=0,user_id,NULL))inprogress, COUNT(IF(approved_status=0 and cancel_status=0,user_id,NULL)) pending, COUNT(IF(cancel_status=1,user_id,NULL))Cancel FROM `permission_request` p  RIGHT JOIN m_state s ON s.ST_CODE=p.st_code 
where p.st_code ='$statecode' GROUP BY 1";
$records = DB::select($excelrecord);
$cur_time  = Carbon::now();
$pdf = PDF::loadView('admin.pc.eci.reportpage',['user_data' => $d,'records' =>$records]);
return $pdf->download('report'.$cur_time.'.pdf');
return view('admin.pc.eci.reportpage');  

}



}
    
}
else
{
    return redirect('/officer-login');  
}
}



public function districtwisereportdetails(Request $req)
    {
         if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $cur_time    = Carbon::now();
            $perm = $this->PM->getpermisson();
            $statevalue = StateModel::get_states();
            $datevalue = $req->input('datefilter');
            $details = request()->segments();
            $st = $details[2];
            $dist = $details[3];
            $ele = $details[4];
            $dt = $details[5];
            $status = $details[6];
            if(!empty($dt) && $dt != 0)
            {
            $dates = explode("~", $dt);
            $dte1 = $dates[0];
            $dte2 = $dates[1];
            }
            $dtt=0;
            if(!empty($dt) && $dt != 0)
            {
                $dtt = $details[4];
            }
            
            $data = DB::table('permission_request as a')
                    ->join('m_state as st','a.st_code','=','st.ST_CODE')
                    ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                        $join->on('med.ST_CODE','=','a.st_code');
                    })
                    ->join('user_login as b','b.id','=','a.user_id')
                    ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                    ->join('permission_type as d','a.permission_type_id','=','d.id')
                   ->join('permission_master as m','m.id','=','d.permission_type_id')
                    ->join('m_party as p','a.party_id','=','p.CCODE')
                    ->join('user_role as c','b.role_id','=','c.role_id')
                    ->join('m_district as f',function ($join){
                        $join->on('f.DIST_NO','=','a.dist_no')
                             ->on('f.ST_CODE', '=', 'a.st_code');
                    })
                    ->leftjoin('m_ac as g',function ($join){
                        $join->on('g.AC_NO','=','a.ac_no')
                             ->on('g.ST_CODE', '=', 'a.st_code');
                    })
                    ->select('m.permission_name as pname','c.role_name','ud.name','p.PARTYNAME','f.DIST_NAME','g.AC_NAME','st.ST_NAME', 'a.approved_status','a.cancel_status','a.permission_mode','a.added_at');
                    if(!empty($ele) && $ele != '0')
                    {
                         $data->where('med.ELECTION_TYPEID',$ele);
                    }
                    if(!empty($st) && $st != '0')
                    {
                         $data->where('a.st_code',$st);
                    }
                    if(!empty($dist) && $dist != '0')
                    {
                         $data->where('a.dist_no',$dist);
                    }
                    if(!empty($dt) && $dt != '0')
                    {
                         $data->whereBetween('a.created_at',[$dte1,$dte2]);
                    }
                    if($status != '6' && $status != '5')
                    {
                        $data->where('a.approved_status',$status)->where('a.cancel_status',0);
                    }
                    elseif($status == '5')
                    {
                        $data->where('a.cancel_status',1);
                    }
                    $data->get();
                    $result=$data->get()->toArray();
                    return view('admin.pc.eci.reportdistrictpermissiondetails', ['election'=>$ele,'state' => $st, 'user_data' => $d,'datereport'=>$result,'datefilter'=>$dtt,'statevalue' => $statevalue]);
               
        } else {
            return redirect('/officer-login');
        }
    }
public function PermissionMasterReport(Request $req)
    {
         if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $cur_time    = Carbon::now();
            $statevalue = StateModel::get_states();
            $getAllPermissiontype = DB::table('permission_master')
                                ->select('*')
                                ->get()->toArray();
            $data =  DB::table('permission_required_doc as a')
                            ->join('permission_type as b','b.id','=','a.permission_id')
                            ->join('role_master as m','m.role_id','=','b.role_id')
                            ->join('permission_master as pm','pm.id','=','b.permission_type_id')
                            ->join('m_state as st','st.ST_CODE','=','a.st_code')
                            ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                            ->select('a.*','m.role_name','pm.permission_name as pname','st.ST_NAME');
                            if(!empty($_REQUEST['state']))
                               {
                                    $data->where('a.st_code',$_REQUEST['state']);
                               }
                               if(!empty($_REQUEST['pname']))
                               {
                                    $data->where('a.permission_type_id',$_REQUEST['pname']);
                               }
                               if(!empty($_REQUEST['elect']))
                               {
                                    $data->where('med.ELECTION_TYPEID',$_REQUEST['elect']);
                               }
                     $getdocDetails=$data->get()->toArray();
                     $detailsdata = array();
                     foreach($getdocDetails as $data)
                     {
                         $permissiondocid = $data->permission_type_id;
                         $getcanddoc = DB::table('permission_required_doc as a')->select('a.permission_type_id')->where('a.id',$data->id)->first();
                         $getcanddoc = explode(',',$getcanddoc->permission_type_id);
                         $canddoc = "";
                         if(!empty($getcanddoc) && in_array("cand01", $getcanddoc))
                         {
                            $canddoc = 'Applicant';
                         }
                         $getauthDetails=DB::table('permission_required_doc as a')
                            ->join('authority_type as c',\DB::raw("FIND_IN_SET(c.id,a.permission_type_id)"),">",\DB::raw("'0'"))
                            ->select(DB::raw("GROUP_CONCAT(DISTINCT c.name SEPARATOR ',') as 'auth_name'"))
                            ->where('a.id',$data->id)->first();
                         $detailsdata[] = array(
                        'id' => $data->id,
                        'permission_id'  =>  $data->permission_id,
                        'permission_type_id'  => $data->permission_type_id,
                       // 'authority_type_id'  => $data->authority_type_id,
                        'doc_name'  => $data->doc_name,
                        'doc_size'  => $data->doc_size,
                        'st_code'  => $data->st_code,
                        'st_name'  => $data->ST_NAME,
                        'required_status'  => $data->required_status,
                        'file_name'  => $data->file_name,
                        //'fileserver_dir'  => $data->fileserver_dir,
                        'status'  => $data->status,
                        'auth_name' => $getauthDetails->auth_name,
                        'canddoc_name' => $canddoc,
                        'role_name' => $data->role_name,
                        'pname' =>$data->pname
                         );
                     }
                     $detailsdata = json_decode(json_encode($detailsdata), FALSE);
                     if($req->method() == 'POST')
            {
                if ($req->input('excel')) {
                     $name_excel = 'Permission Master Report'.'_'.$cur_time;
                     $headings[] = ['State Name', 'Permission Name', 'Document Details', 'Permission Level', 'Authority Type', 'Status'];
                    $arr = array();$state="";$p_id=0;$i=1;
                                    foreach ($detailsdata as $data) {
                                    $authname="";
                                    $required_status="";

                                    $file_name = $data->file_name;
                                    if($data->auth_name != 'undefined' && $data->auth_name != 'null')
                                    {
                                         $authname = $data->auth_name.' ';
                                    }
                                    else
                                    {
                                          $authname = "";
                                    }

                                    if($data->canddoc_name != 'undefined' && $data->canddoc_name != '')
                                    {
                                        $authname .= $data->canddoc_name;
                                    }
                                   if($data->required_status == '1')
                                   {
                                   $required_status = 'Mandatory';
                                   }
                                   else
                                   {
                                        $required_status = 'Not Mandatory';
                                   }
                                   $state1=$data->st_code;$p_id1 = $data->permission_id;
                                        if(($state == $state1) && ($p_id == $p_id1))
                                        {
                                        $data1 = array(
                                            "",
                                            "",
                                            $data->doc_name,
                                            $data->role_name,
                                            $authname,
                                            $required_status,
                                        );
                                        }
                                        else
                                        {
                                            $data1 = array(
                                            $data->st_code,
                                            $data->pname,
                                            $data->doc_name,
                                            $data->role_name,
                                            $authname,
                                            $required_status,
                                        );
                                        }
                                        array_push($arr, $data1);
                                        $state=$data->st_code;$p_id = $data->permission_id;
                                    }
                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                }
                else if($req->input('pdf')){
                    $pdf = PDF::loadView('admin.pc.eci.PermissionMasterReportPDF', ['user_data' => $d, 'report' => $detailsdata]);
                return $pdf->download('report' . $cur_time . '.pdf');
                }
                else
                {
                    return view('admin.pc.eci.PermissionMasterReport', ['getAllPermissiontype'=>$getAllPermissiontype,'pname'=>$_REQUEST['pname'],'elect'=>$_REQUEST['elect'],'state' =>$_REQUEST['state'], 'user_data' => $d,'report'=>$detailsdata,'statevalue' => $statevalue]);
                }
            }
            else
            {
                return view('admin.pc.eci.PermissionMasterReport', ['getAllPermissiontype'=>$getAllPermissiontype,'elect'=>0,'pname'=>0,'state' =>0, 'user_data' => $d,'report'=>"",'statevalue' => $statevalue]);
            }
            
        } else {
            return redirect('/officer-login');
        }
    }

public function  modewisepermissionreport(Request $req)
    {
         if (Auth::check()) {
            $user = Auth::user();
            $d = $this->commonModel->getunewserbyuserid($user->id);
            $user_data = $d;
            $cur_time    = Carbon::now();
            $statevalue = StateModel::get_states();
            $getAllPermissiontype = DB::table('permission_master')
                                ->select('*')
                                ->get()->toArray();
             $data = DB::table('permission_request as a')
                               ->join('m_state as st','a.st_code','=','st.ST_CODE')
                               ->join(DB::raw('(select ST_CODE,ELECTION_TYPEID from m_election_details group by ST_CODE) as med'), function($join){
                                   $join->on('med.ST_CODE','=','a.st_code');
                               })
                               
                               ->select('a.permission_mode','a.st_code','st.ST_NAME',DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'))
                              
                               ->groupBy('st.ST_CODE')
                               ->groupBy('a.permission_mode');
                               if(!empty($_REQUEST['elect']))
                               {
                                    $data->where('med.ELECTION_TYPEID',$_REQUEST['elect']);
                               }
                               if(!empty($_REQUEST['state']))
                               {
                                    $data->where('a.st_code',$_REQUEST['state']);
                               }
                               if(!empty($_REQUEST['pmode']) && $_REQUEST['pmode'] != '0')
                               {
                                   $pmode = $_REQUEST['pmode'];
                                   if($pmode == 1)
                                   {
                                    $data->where('a.permission_mode','0');
                                   }
                                   else
                                   {
                                       $data->where('a.permission_mode','1');
                                   }
                               }
                               if(!empty($_REQUEST['pname']) && $_REQUEST['pname'] != '0')
                               {
                                   $data->join('permission_type as pt','pt.id','=','a.permission_type_id')
                                     ->join('permission_master as pm','pm.id','=','pt.permission_type_id')
                                    ->select('pm.permission_name','a.permission_mode','a.st_code','st.ST_NAME',DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as Total'),DB::raw('sum(CASE WHEN a.cancel_status = 1 THEN 1 ELSE 0 END) as Cancel'));
                                    if($_REQUEST['pname'] != 'all')
                                   {
                                    $data->where('pt.permission_type_id',$_REQUEST['pname']);
                                   }
                                   else
                                   {
                                       $data->groupBy('a.permission_type_id');
                                   }
                               }
                               $data->get();
                               $detailsdata=$data->get()->toArray();
            if($req->method() == 'POST')
            {
                if ($req->input('excel')) {
                     $name_excel = 'Permission Master Report'.'_'.$cur_time;
                    if(!empty($_REQUEST['pname']) && $_REQUEST['pname'] != '0')
                    {
                     $headings[] = ['State Name', 'Permission Name', 'Total request', 'Approved', 'Rejected','Inprogress','Pending','Cancel','Permission Mode'];
                    }
                    else {
                        $headings[] = ['State Name','Total request', 'Approved', 'Rejected','Inprogress','Pending','Cancel','Permission Mode'];
                    }
                     $arr = array();
                                    foreach ($detailsdata as $data) {
                                    
                                   if($data->permission_mode == '1')
                                   {
                                   $Permission_Mode = 'Online';
                                   }
                                   else
                                   {
                                    $Permission_Mode = 'Offline';
                                   }
                                if(!empty($_REQUEST['pname']) && $_REQUEST['pname'] != '0')
                                {
                                $data1 = array(
                                    $data->ST_NAME,
                                    $data->permission_name,
                                    $data->Total,
                                    $data->Accepted,
                                    $data->Rejected,
                                    $data->Inprogress,
                                    $data->Pending,
                                    $data->Cancel,
                                    $Permission_Mode
                                );
                                }
                                else
                                {
                                    $data1 = array(
                                    $data->ST_NAME,
                                    $data->Total,
                                    $data->Accepted,
                                    $data->Rejected,
                                    $data->Inprogress,
                                    $data->Pending,
                                    $data->Cancel,
                                    $Permission_Mode
                                );
                                }
                                        array_push($arr, $data1);
                                    }
                     return Excel::download(new ExcelExport($headings, $arr), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');
                }
                else if($req->input('pdf')){
                    $pdf = PDF::loadView('admin.pc.eci.ModeWisePermissionReportPDF', ['pname'=>$_REQUEST['pname'],'user_data' => $d, 'report' => $detailsdata]);
                return $pdf->download('report' . $cur_time . '.pdf');
                }
                else
                {
                    return view('admin.pc.eci.ModeWisePermissionReport', ['pname'=>$_REQUEST['pname'],'getAllPermissiontype'=>$getAllPermissiontype,'pmode'=>$_REQUEST['pmode'],'elect'=>$_REQUEST['elect'],'state' =>$_REQUEST['state'],'user_data' => $d,'report'=>$detailsdata,'statevalue' => $statevalue]);
                }
            }
            else
            {
                return view('admin.pc.eci.ModeWisePermissionReport', ['pmode'=>0,'getAllPermissiontype'=>$getAllPermissiontype,'elect'=>0,'pname'=>0,'state' =>0, 'user_data' => $d,'report'=>"",'statevalue' => $statevalue]);
            }
            
        } else {
            return redirect('/officer-login');
        }
    }

}  // end class