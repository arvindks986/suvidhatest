<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\adminmodel\CeoPcPermissionModel;
use Illuminate\Http\Request;
use Session;
use DB;
use App\commonModel;
use App\adminmodel\CandidateModel;
use App\adminmodel\ROPCModel;
use App\Classes\xssClean;
use PDF;
use Carbon\Carbon;
// use Excel;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class CeoRawPermissionController extends Controller {

    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        $this->PM = new CeoPcPermissionModel();
    }



	//----------------------------Divya-------------------------------------//
	 public function rawreport()
	 {
		if (Auth::check()) 
		{
		$user = Auth::user();
		$d = $this->commonModel->getunewserbyuserid($user->id);
		return view('admin.pc.ceo.Permission.rawreportceo', ['user_data' => $d]);
	 }
	 }
	
	 public function rawreportdate(Request $req)
	  {
		
		if (Auth::check()) 
	    {
		if($req->input('excel'))
        {
		if(empty($_REQUEST['datefilter']))
		{
		$user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $user_data = $d;
        $st_code = $d->st_code;
		return Excel::create('report', function($excel) use ($d) {
		$excel->sheet('mySheet', function($sheet) use ($d)
	    {
		$st_code = $d->st_code;
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
                ->get()->toArray();
        $arr  = array();
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
		if($excelrecord->ac_no != 0 or $excelrecord->ac_no != '' )
		{
		$acvalue = array('ST_CODE'=>$excelrecord->st_code,'AC_NO'=>$excelrecord->ac_no);
        $acname = DB::table('m_ac')->select('AC_NAME')->where($acvalue)->get();
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
			$data =  array(
			$excelrecord->id,
			$datastate[0]->ST_NAME,
			$g[0]->DIST_NAME,
			$acname[0]->AC_NAME,
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
		
			);
         array_push($arr, $data);
		}
		$sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
		'Permission ID','State Name','District Name','AC Name','User Name','Permission Type', 'User Type','Party Name','Date of Submission','Action Date','Event Start Date','Event End Date','Permission Mode','Previous Status','Current Status'
		)
		);
	    });
		})->download();
		}
		else if ($_REQUEST['datefilter'])
		{
		$datevalue = $_REQUEST['datefilter'];
		$dates = explode("~",$datevalue);
		$dte1 = $dates[0];
		$dte2 = $dates[1];
		$user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $user_data = $d;

		$st_code = $d->st_code;
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
                ->wheredate('date_time_start', '>=', $dte1)
                ->wheredate('date_time_start', '<=', $dte2)
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
		if($excelrecord->ac_no != 0 or $excelrecord->ac_no != '' )
		{
		$acvalue = array('ST_CODE'=>$excelrecord->st_code,'AC_NO'=>$excelrecord->ac_no);
        $acname = DB::table('m_ac')->select('AC_NAME')->where($acvalue)->get();
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
			$datastate[0]->ST_NAME,
			$g[0]->DIST_NAME,
			$acname[0]->AC_NAME,
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


		$name_excel = 'report';
		return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');  



		// return Excel::create('report', function($excel) use ($d,$dte1,$dte2) {
		// $excel->sheet('mySheet', function($sheet) use ($d,$dte1,$dte2)
	    // {

		// $st_code = $d->st_code;
		// $allrecord=  $data=DB::table('permission_request as a')
        //         ->join('user_login as b','a.user_id','=','b.id')
        //         ->join('user_data as ud','ud.user_login_id','=','b.id')
        //         ->join('user_role as c','b.role_id','=','c.role_id')
        //         ->join('permission_type as d','a.permission_type_id','=','d.id')
        //         ->join('permission_master as m','m.id','=','d.permission_type_id')
        //         ->join('m_party as mp','mp.CCODE','=','a.party_id')
        //         ->join('m_state as ms','ms.ST_CODE','=','a.st_code')
        //         ->select('a.*','ud.name','c.role_name','mp.PARTYNAME','ms.ST_NAME','m.permission_name as pname','a.id as permission_id','b.id as login _id')
        //         ->where('a.st_code',$st_code)
        //         ->wheredate('date_time_start', '>=', $dte1)
        //         ->wheredate('date_time_start', '<=', $dte2)
        //         ->get()->toArray();
        // $arr  = array();
		// foreach($allrecord as $excelrecord)
		// {	
		// $uservalue = DB::table('user_data')
        //         ->select('*')
		// 		->where('user_login_id',$excelrecord->user_id) 
        //         ->get();
		// $stvalue = array('ST_CODE'=>$excelrecord->st_code);
		// $datastate =DB::table('m_state')->select('ST_NAME')->where($stvalue)->get();
		// if($excelrecord->dist_no != 0 or $excelrecord->dist_no != '' )
		// {
		// $datavalue = array('ST_CODE'=>$excelrecord->st_code,'DIST_NO'=>$excelrecord->dist_no);
        // $g = DB::table('m_district')->select('DIST_NAME')->where($datavalue)->get();
		// }
		// if($excelrecord->ac_no != 0 or $excelrecord->ac_no != '' )
		// {
		// $acvalue = array('ST_CODE'=>$excelrecord->st_code,'AC_NO'=>$excelrecord->ac_no);
        // $acname = DB::table('m_ac')->select('AC_NAME')->where($acvalue)->get();
		// }
		
        //   if($excelrecord->cancel_status== 1)
		//    {
		// 	   $cancelstatus = 'Cancel';
		//    }
		//    else if($excelrecord->cancel_status == 0)
		//    {
		//    if($excelrecord->approved_status == 0)
		//    {
		// 	   $cancelstatus = 'Pending';
		//    }
		//    else if($excelrecord->approved_status == 1)
		//    {
		// 	  $cancelstatus = 'Inprogress';
		//    }
		//    else if($excelrecord->approved_status == 2)
		//    {
		// 	   $cancelstatus = 'Accepted';
		//    }
		//    else if($excelrecord->approved_status == 3)
		//    {
		// 	   $cancelstatus = 'Rejected';
		//    }
		//    }
		//   if($excelrecord->permission_mode== 0)
		//    {
		// 	   $pmode = 'Offline';
		//    }
		//    else if($excelrecord->permission_mode == 1)
		//    {
		// 	   $pmode  = 'Online';
		//    }
		//    if($excelrecord->approved_status == 0)
		//    {
		// 	   $status = 'Pending';
		//    }
		//    else if($excelrecord->approved_status == 1)
		//    {
		// 	  $status = 'Inprogress';
		//    }
		//    else if($excelrecord->approved_status == 2)
		//    {
		// 	   $status = 'Accepted';
		//    }
		//    else if($excelrecord->approved_status == 3)
		//    {
		// 	   $status = 'Rejected';
		//    }
		// 	$data =  array(
		// 	$excelrecord->id,
		// 	$datastate[0]->ST_NAME,
		// 	$g[0]->DIST_NAME,
		// 	$acname[0]->AC_NAME,
		// 	$uservalue[0]->name,
		// 	$excelrecord->pname,
		// 	$excelrecord->role_name,
		// 	$excelrecord->PARTYNAME,
		// 	$excelrecord->added_at,
		// 	$excelrecord->updated_at,
		// 	$excelrecord->date_time_start,
		// 	$excelrecord->date_time_end,
		// 	$pmode,
		// 	$status,
		// 	$cancelstatus,
		
		// 	);
        //  array_push($arr, $data);
		// }
		// $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
		// 'Permission ID','State Name','District Name','AC Name','User Name','Permission Type', 'User Type','Party Name','Date of Submission','Action Date','Event Start Date','Event End Date','Permission Mode','Previous Status','Current Status'
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
	  
	  }
}
