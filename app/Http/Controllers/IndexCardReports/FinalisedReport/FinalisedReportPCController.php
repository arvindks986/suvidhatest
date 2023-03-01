<?php

namespace App\Http\Controllers\IndexCardReports\FinalisedReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth AS Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use Excel;
use MPDF;
use App\commonModel;  
use App\adminmodel\CEOModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\adminmodel\CEOPCModel;
use App\adminmodel\PCCeoReportModel;
use App\Classes\xssClean;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;

class FinalisedReportPCController extends Controller
{
	public function __construct(){
       $this->middleware('adminsession');
       $this->middleware(['auth:admin','auth']);
       $this->middleware('ceo');
       $this->commonModel = new commonModel();
       $this->ceomodel = new CEOPCModel();
            $this->pcceoreportModel = new PCCeoReportModel();
       $this->xssClean = new xssClean;
   }
    public function getNoofFinalizedPC(Request $request){
		// session data
		$user = Auth::user();
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
          $session['election_detail'] = array();
        //echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
	   //end session data
		 
	DB::enableQueryLog();	 
       $fSelect = array('B.ST_NAME AS state',
        'A.ST_CODE',
        DB::raw('COUNT(A.PC_NO) AS totalpc')
        );

        $fData = DB::table('m_pc AS A')
        ->join('m_state AS B','A.ST_CODE','B.ST_CODE')
		
        ->select($fSelect)
        ->groupBy('A.ST_CODE')
        ->get()->toArray();


        $sSelect = array(
        'A.ST_NAME','C.st_code',
        DB::raw('SUM(CASE WHEN C.finalize = 0 THEN 1 ELSE 0 END) AS notfinalised'),
        DB::raw('SUM(CASE WHEN C.finalize = 1 THEN 1 ELSE 0 END) AS finalised')

        );

        $sData = DB::table('counting_pcmaster AS C')
        ->select($sSelect)
        //->select('cfpc.pc_no','mpc.PC_NAME')
        ->join(DB::raw('(SELECT MAX(id) AS id,MAX(created_at) AS created_at FROM counting_pcmaster GROUP BY st_code, pc_no) AS B'), 'C.id','B.id')

        ->join('m_state AS A','A.ST_CODE','C.st_code')
        ->groupBy('C.ST_CODE')
        ->get()->toArray();
		
		 DB::enableQueryLog();
		//echo"<pre>";print_r($fDataat);die;
        $finalData = array();

        foreach ($fData as $key){    
            foreach ($sData as $value){
                if($key->ST_CODE == $value->st_code){
                    $finalData[$key->ST_CODE]['totalpc'] = $key->totalpc;
                    $finalData[$key->ST_CODE]['state'] = $value->ST_NAME;
                    $finalData[$key->ST_CODE]['finalize'] = $value->finalised;
                    $finalData[$key->ST_CODE]['nonfinalised'] = $value->notfinalised;

                }
             }
        }
        $data = $finalData;      
        if($request->path() == 'pcceo/NoofFinalizedPC'){
                return view('IndexCardReports.FinalisedPc.noof-finalizedpc',compact('data','user_data','sched'));
            }elseif($request->path() == 'pcceo/NoofFinalizedPCPDF'){          
               $pdf=PDF::loadView('IndexCardReports.FinalisedPc.noof-finalizedpc-pdf',['data'=>$data]);
                return $pdf->download('No-of-finalizedpc.pdf');            
            }elseif ($request->path() == 'pcceo/NoofFinalizedPCXls') {
            $data = json_decode( json_encode($data), true);
                
        
      return Excel::create('getNoofFinalizedPC', function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
               // $sheet->fromArray($data);


               $sheet->mergeCells('A1:E1');
                $sheet->cells('A1:E1', function($cells) {
                    $cells->setFont(array(
                        'size'       => '15',
                        'bold'       => true
                    ));
                });
               $sheet->cell('A1', function($cells) {
                    $cells->setValue('No. oF Finalized PC');
                });

 
                $sheet->cell('A2', function($cells) {
                    $cells->setValue('Sr.No.');
                });

               $sheet->cell('B2', function($cells) {
                    $cells->setValue('State Name');
                });
                
                $sheet->cell('C2', function($cells) {
                    $cells->setValue('Total PC');
                });
    
               $sheet->cell('D2', function($cells) {
                    $cells->setValue('Finalised');
                });

              
                  $sheet->cell('E2', function($cells) {
                    $cells->setValue('Not Finalised Yet');
                });

                if (!empty($data)) {
                     $i= 3;
                     $sn=1;
					 $ttotalpc = $totalfinalize =$totalnotfinalize =0;
                    //print_r($data);die;
                    foreach ($data as $row) {
						$ttotalpc+=$row['totalpc'];
						$totalfinalize+=$row['finalize'];
						$totalnotfinalize+=$row['totalpc']-$row['finalize'];
                       
                        $sheet->cell('A'.$i ,$sn); 
                        $sheet->cell('B'.$i, $row['state']); 
                        $sheet->cell('C'.$i, ($row['totalpc'] > 0) ? $row['totalpc']:'=(0)' ); 
                        $sheet->cell('D'.$i, ($row['finalize'] > 0) ? $row['finalize']:'=(0)' ); 
                        $sheet->cell('E'.$i, ($row['finalize'] > 0) ? $row['totalpc']- $row['finalize']:'=(0)' ); 
                       
                      //  $sheet->cell('F'.$i, '=(0)');
                        $i++;
                    }
					//total count
						//$sheet->cell('A'.$i ,$sn); 
                       // $sheet->cell('B'.$i, $row['state']); 
                        $sheet->cell('C'.$i, ($ttotalpc > 0) ? $ttotalpc :'=(0)' ); 
                        $sheet->cell('D'.$i, ($totalfinalize > 0) ? $totalfinalize :'=(0)' ); 
                        $sheet->cell('E'.$i, ($totalnotfinalize > 0) ? $totalnotfinalize :'=(0)' ); 
					
                }
    
            });
        })->download('xls');
     }else{
        echo "Result not found!";
     }
        
        
    }
    
	
//finalised PC Report
	public function getFinalisedPCReport(Request $request)
    {
        $session = $request->session()->all();
         //dd($session);
       // $state=$session['election_detail']['st_name'];
        //$year=$session['election_detail']['year'];
		////////////////////////////////////////////
		$user = Auth::user();
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
          $session['election_detail'] = array();
       //echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
	   ///////////////////////////////////////////////////////		
		
		
        /*SELECT * FROM counting_pcmaster ST 
        join m_pc as mpc on st.st_code = mpc.ST_CODE and st.pc_no=mpc.PC_NO
        WHERE `finalize` = (SELECT MIN(`finalize`) FROM counting_pcmaster STT WHERE STT.id = ST.id) and mpc.st_code = 'S02' GROUP BY mpc.`pc_no`  
        ORDER BY `mpc`.`PC_NAME`  DESC*/
		
		$state=$session['election_detail']['st_name'];

         DB::enableQueryLog();

         $cSelect = array(
                    'mpc.pc_no','mpc.PC_NAME','cpc.finalize'
            
        );

        $data = DB::table('counting_pcmaster AS cpc')
                   ->select($cSelect)
                    //->select('cfpc.pc_no','mpc.PC_NAME')
                    ->join('m_pc AS mpc',function($join){
                        $join->on('cpc.st_code','=','mpc.ST_CODE')
                        ->on('mpc.PC_NO','=','cpc.pc_no');
                    })  
                    ->where('cpc.finalize', DB::raw('(SELECT MIN(`finalize`) FROM counting_pcmaster STT WHERE STT.id = cpc.id)'))
                    ->where('mpc.st_code', $user_data->st_code)
                    ->groupBy('mpc.pc_no')
                    ->orderBy('mpc.PC_NO', 'ASC')->get()->toArray();
        
        $queue = DB::getQueryLog(); 
        
    
        //echo '<pre>'; print_r($data);die;
         if($request->path() == 'pcceo/FinalisedPCReport'){
             return view('IndexCardReports.FinalisedPc.finalised-pc-report',compact('data','user_data','sched','state'));
          }elseif($request->path() == 'pcceo/FinalisedPCReportPDF'){
            $pdf=PDF::loadView('IndexCardReports.FinalisedPc.finalised-pc-report-pdf',[
            'state'=>$state,
            'data'=>$data,
            'user_data'=>$user_data,
            'sched'=>$sched]);
             return $pdf->download('finalised-pc-report.pdf');
          }elseif($request->path() == 'pcceo/FinalisedPCReportXls'){
            $data = json_decode( json_encode($data), true);
                
        
            return Excel::create('FinalisedPCReport', function($excel) use ($data) {
                $excel->sheet('mySheet', function($sheet) use ($data)
                {
                    $sheet->mergeCells('A1:C1');

                    $sheet->cells('A1:C1', function($cells) {
                        $cells->setFont(array(
                            'size'       => '15',
                            'bold'       => true
                        ));
                    });
                   $sheet->cell('A1', function($cells) {
                        $cells->setValue('Finalized PC');
                    });

     
                    $sheet->cell('A2', function($cells) {
                        $cells->setValue('Sr.No.');
                    });

                   $sheet->cell('B2', function($cells) {
                        $cells->setValue('PC No.');
                    });
                    
                    $sheet->cell('C2', function($cells) {
                        $cells->setValue('PC NAME');
                    });
        
                   
                    if (!empty($data)) {
                         $i= 3;
                         $sn=1;
                      // echo '<pre>';print_r($data);die;
                        foreach ($data as $row) {
                            if($row['finalize'] ==1){                           
                                $sheet->cell('A'.$i ,$sn); 
                                $sheet->cell('B'.$i, $row['pc_no']); 
                                $sheet->cell('C'.$i, $row['PC_NAME']);
                                $i++;  
								$sn++;
                            }
                            
                        }
                    }
        
                });
            })->download('xls');

        }
        else{
            echo "No Record Found!";
        }      
   

    }

    

 //Not finalised PC Report

    public function getNotFinalisedPCReport(Request $request)
    {
       // session data
		$user = Auth::user();
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
          $session['election_detail'] = array();
        //echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
	   //end session data
        $state=$session['election_detail']['st_name'];
        $year=$session['election_detail']['YEAR'];


          DB::enableQueryLog();

         $cSelect = array(
                    'mpc.pc_no','mpc.PC_NAME','cpc.finalized_ac'
        );

         $data = DB::table('counting_finalized_ac AS cpc')
                   ->select($cSelect)
                    //->select('cfpc.pc_no','mpc.PC_NAME')
                    ->join('m_pc AS mpc',function($join){
                        $join->on('cpc.st_code','=','mpc.ST_CODE')
                        ->on('mpc.PC_NO','=','cpc.pc_no');
                    })  
                    ->where('cpc.finalized_ac',0)
                    ->where('mpc.st_code', $session['election_detail']['st_code'])
                    ->groupBy('mpc.pc_no')
                    ->orderBy('mpc.PC_NO', 'ASC')->get()->toArray();
        
        $queue = DB::getQueryLog();

        if($request->path() == 'pcceo/NotFinalisedPCReport'){
             return view('IndexCardReports/FinalisedPc.not-finalised-pc-report',compact('data','year','state','user_data','sched'));
        }elseif($request->path() == 'pcceo/NotFinalisedPCReportPDF'){
           $pdf=PDF::loadView('IndexCardReports/FinalisedPc.not-finalised-pc-report-pdf',['state'=>$state,
                            'year'=>$year,
                            'data'=>$data]);
        return $pdf->download('not-finalised-pc-report.pdf');
        }elseif($request->path() == 'pcceo/NotFinalisedPCReportXls'){
            $data = json_decode( json_encode($data), true);             
        
            return Excel::create('NotFinalisedPCReport', function($excel) use ($data) {
                $excel->sheet('mySheet', function($sheet) use ($data)
                {
                    $sheet->mergeCells('A1:C1');

                    $sheet->cells('A1:C1', function($cells) {
                        $cells->setFont(array(
                            'size'       => '15',
                            'bold'       => true
                        ));
                    });
                   $sheet->cell('A1', function($cells) {
                        $cells->setValue('NOT Finalized PC');
                    });

     
                    $sheet->cell('A2', function($cells) {
                        $cells->setValue('Sr.No.');
                    });

                   $sheet->cell('B2', function($cells) {
                        $cells->setValue('PC No.');
                    });
                    
                    $sheet->cell('C2', function($cells) {
                        $cells->setValue('PC NAME');
                    });
        
                   
                    if (!empty($data)) {
                         $i= 3;
                         $sn=1;
                      // echo '<pre>';print_r($data);die;
                        foreach ($data as $row) {
                            if($row['finalized_ac'] ==0){                           
                                $sheet->cell('A'.$i ,$sn); 
                                $sheet->cell('B'.$i, $row['pc_no']); 
                                $sheet->cell('C'.$i, $row['PC_NAME']);
                                $i++;   
								$sn++;
                            }                            
                        }
                    }
        
                });
            })->download('xls');

        }   
        else{
            echo "No Record Found!";
        }   

    }

     
	
}
