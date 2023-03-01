<?php

namespace App\Http\Controllers\IndexCardReports\Detailsofrepollheld;
use Illuminate\Http\Request;
use DB;
use Session;
use PDF;
use Excel;
use App\commonModel;
use Auth;
use App\Http\Controllers\Controller;

class Detailsofrepollheld extends Controller
{
      public function __construct(){
       $this->middleware('adminsession');
       $this->middleware(['auth:admin','auth']);
       $this->middleware('ceo');
       $this->commonModel = new commonModel();
   }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

       $user_data = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }



       $rowdatas = DB::table('t_pc_ic as ic')
                 ->select('r.t_pc_ic_id','r.dt_repoll','r.no_repoll',
                        DB::raw('sum(ic.total_no_polling_station) as tpolling'),
                        'pc.PC_NAME','state.ST_NAME','pc.ST_CODE','pc.PC_NO')
                      ->join('m_state as state', 'ic.st_code', '=', 'state.st_code')
                       ->join('m_pc as pc', function($query) {
                            $query->on('pc.PC_NO', '=', 'ic.PC_NO')
                            ->on('pc.ST_CODE', '=', 'ic.st_code');
                        })
                         ->leftjoin('repoll_pc_ic as r', 'r.t_pc_ic_id', '=', 'ic.id')
                    //->groupby('r.t_pc_ic_id')
                    ->groupby('state.st_code','pc.PC_NO')
                    ->get()->toarray();

                     //dd($data);
                        $data=array();
                        $temp=array();
                        $polling=array();
                        $totalrepoll = 0;
                        $totalpolling = 0;
                        $stname = '';
                        $i=0;
                        $total_no_polling_station = 0;


                    foreach($rowdatas as $key=> $rowdata){

                      $i = ($rowdata->ST_NAME==$stname)?$i:0;

                         $totalpolling = ($rowdata->ST_NAME==$stname)?$totalpolling:0;
                         $data[$rowdata->ST_CODE]['state_name'] = $rowdata->ST_NAME;
                         $data[$rowdata->ST_CODE]['total_no_polling_station'] = $totalpolling + $rowdata->tpolling;
                         $totalpolling  = $rowdata->tpolling;
                        //$polling[] =$rowdata->ST_NAME;
                      //$dataArray['stcode'][$rowdata->PC_NO] = $rowdata->PC_NAME;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NO'] = $rowdata->PC_NO;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NAME'] = $rowdata->PC_NAME;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['no_repoll'] = $rowdata->no_repoll;
                      $data[$rowdata->ST_CODE]['pcinfo'][$i]['dt_repoll'] = $rowdata->dt_repoll;
                      //$data[$rowdata->ST_CODE]['totalrepoll'][] = $totalrepoll+=$rowdata->no_repoll;
                      $data[$rowdata->ST_CODE]['totalrepoll'][] =  $rowdata->no_repoll;
                      $sumdata = @array_sum($data[$rowdata->ST_CODE]['totalrepoll']);


                      $i++;
                      $stname = $rowdata->ST_NAME;
                      //$dataArray['stcode']['totalrepoll']  = $rowdata->dt_repoll;
                  }
//     print_r($data);
////////
//dd('hello');

        return view('IndexCardReports/StatisticalReports/Vol2/details-of-repoll-held',  compact('data','user_data'));
    }




    public function Detailsofrepollheldpdf(){


       $rowdatas = DB::table('t_pc_ic as ic')
                 ->select('r.t_pc_ic_id','r.dt_repoll','r.no_repoll',
                        DB::raw('sum(ic.total_no_polling_station) as tpolling'),
                        'pc.PC_NAME','state.ST_NAME','pc.ST_CODE','pc.PC_NO')
                      ->join('m_state as state', 'ic.st_code', '=', 'state.st_code')
                       ->join('m_pc as pc', function($query) {
                            $query->on('pc.PC_NO', '=', 'ic.PC_NO')
                            ->on('pc.ST_CODE', '=', 'ic.st_code');
                        })
                         ->leftjoin('repoll_pc_ic as r', 'r.t_pc_ic_id', '=', 'ic.id')
                    //->groupby('r.t_pc_ic_id')
                    ->groupby('state.st_code','pc.PC_NO')
                    ->get()->toarray();


                     //dd($data);
                        $dataArray=array();
                        $temp=array();
                        $polling=array();
                        $totalrepoll = 0;
                        $totalpolling = 0;
                        $stname = '';
                        $i=0;
                        $total_no_polling_station = 0;


                    foreach($rowdatas as $key=> $rowdata){
                        $i = ($rowdata->ST_NAME==$stname)?$i:0;
                         $totalpolling = ($rowdata->ST_NAME==$stname)?$totalpolling:0;
                         $dataArray[$rowdata->ST_CODE]['state_name'] = $rowdata->ST_NAME;
                         $dataArray[$rowdata->ST_CODE]['total_no_polling_station'] = $totalpolling + $rowdata->tpolling;
                         $totalpolling  = $rowdata->tpolling;
                        //$polling[] =$rowdata->ST_NAME;

                      //$dataArray['stcode'][$rowdata->PC_NO] = $rowdata->PC_NAME;
                      $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NO'] = $rowdata->PC_NO;
                      $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['PC_NAME'] = $rowdata->PC_NAME;
                      $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['no_repoll'] = $rowdata->no_repoll;
                      $dataArray[$rowdata->ST_CODE]['pcinfo'][$i]['dt_repoll'] = $rowdata->dt_repoll;
                      //$dataArray[$rowdata->ST_CODE]['totalrepoll'] = $totalrepoll+=$rowdata->no_repoll;
                     $dataArray[$rowdata->ST_CODE]['totalrepoll'][] =  $rowdata->no_repoll;
                      $i++;
                      $stname = $rowdata->ST_NAME;
                      //$dataArray['stcode']['totalrepoll']  = $rowdata->dt_repoll;
                  }
//                  echo '<pre>';
//                  //dd();
//         print_r($dataArray);
//////
//////
//dd('hello');




              $pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/detailsof-repoll-held-pdf', compact('dataArray'));
        return $pdf->download('details-of-repoll-held.pdf');


    }


}
