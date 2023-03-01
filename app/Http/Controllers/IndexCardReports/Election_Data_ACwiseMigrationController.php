<?php

namespace App\Http\Controllers\IndexCardReports;

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
ini_set('max_execution_time', 300);

class Election_Data_ACwiseMigrationController extends Controller

{
	public function datamigrationacwise(Request $request, $st_code, $schedule_id){
    // die('here');
        $count = 0;
        //$session = $request->session()->all();
        
        //$loggedinUserid = Auth::user()->id;
        
        $data_pc_wise = array();
        $dataForInsert = array();
        // $queryforac = DB::table('m_ac')->select('ac_no','pc_no','st_code')
        //                 ->where('ST_CODE', $st_code)
        //                 ->orderBy('pc_no')
        //                 ->get()->toArray();
       
        DB::enableQueryLog();


        //dd($queryforac);
        // foreach ($queryforac as  $value) {     

            $dataForInsert = array();

            DB::enableQueryLog();

            $data = DB::table('m_ac AS mac')
                    ->select('*','ed.electors_male','ed.electors_female','ed.electors_other','ed.electors_service','ed.electors_total','ed.gen_electors_male','ed.gen_electors_female','ed.gen_electors_other','ed.nri_male_electors','ed.nri_female_electors','ed.nri_third_electors','ed.service_male_electors','ed.service_female_electors','ed.service_third_electors'
                      )
                        
                    ->leftJoin('electors_cdac AS ed',function($query){
                           $query->on('mac.AC_NO','ed.ac_no')
                                   ->on('mac.ST_CODE','ed.st_code')
                                   ->on('mac.PC_NO','ed.pc_no');
                       })
                    ->where('mac.st_code', $st_code)
                    ->where('ed.year', 2019)
                      // ->where('ed.scheduledid', 1)
                    
                    ->get()->toArray();
        
        
            $queue = DB::getQueryLog();


      
           // echo "<pre>"; print_r($queue); die;

           
        
               foreach ($data as $key) {


                $dataForInsert = array(
               'schedule_id' => $schedule_id,
               'st_code'     => $st_code,
               'pc_no'       => $key->pc_no,
               'ac_no'       => $key->ac_no,

               'e_gen_m'     => $key->gen_electors_male,
               'e_gen_f'     => $key->gen_electors_female,
               'e_gen_o'     => $key->gen_electors_other,

               'e_gen_t'     => $key->gen_electors_male + $key->gen_electors_female + $key->gen_electors_other,

               'e_nri_m'     => $key->nri_male_electors,
               'e_nri_f'     => $key->nri_female_electors,
               'e_nri_o'     => $key->nri_third_electors,

               'e_nri_t'     => $key->nri_male_electors +  $key->nri_female_electors + $key->nri_third_electors,

               'e_ser_m'     => $key->service_male_electors,
               'e_ser_f'     => $key->service_female_electors,  //$key->ser_f,
               'e_ser_o'     => $key->service_third_electors,  //$key->ser_f,

               'e_ser_t'     => $key->electors_service,//key->ser_m + $key->ser_f,

               'e_total_m'   => $key->electors_male+$key->service_male_electors,//$key->gen_m + $key->ser_m,

               'e_total_f'   => $key->electors_female+$key->service_female_electors,//$key->gen_f + $key->ser_f,

               'e_total_o'   => $key->electors_other,//$key->gen_o,

               'e_total_all' => $key->electors_total+$key->electors_service,

                'update_by'   => 0
           );
           

           $dataForInsert = array_map(
                function($val){
                 return ($val)?$val:0;
                },
                $dataForInsert
            );

               
         // echo "<pre>"; print_r($dataForInsert);
          
           if(!empty($dataForInsert)){
                   DB::table('electors_detail_ac_ic')->insert($dataForInsert);
                   echo "Data Inserted for state code: ". $st_code . " AND Pc Number " . $key->pc_no; 
                   echo "<br />";
                    $count++;
           }else{

           }
           
           
        // echo "<pre>"; print_r(!empty($key));
            
  }



    echo "Totel Record inserted is: ".$count ;
}

}