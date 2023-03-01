<?php 
namespace App\Http\Controllers\Admin\index;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use \PDF;
use App\commonModel;  
use App\models\Admin\PollDayModel;
use App\models\Admin\EndOfPollModel;
use App\models\Admin\StateModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;
use App\models\Admin\IndexCardFinalize;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

//current

class IndexCardFinalizeController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'eci';
  public $action_state  = 'eci/indexcardview/IndexCardFinalizeView';
  public $action_pc     = 'eci/indexcardview/IndexCardFinalizeView/state';
  public $view_path     = "admin.pc.eci";

  public function __construct(){
    $this->middleware('clean_request');
    $this->commonModel  = new commonModel();
    $this->voting_model = new PollDayModel();
    $this->middleware(function ($request, $next) {
        if(Auth::user() && Auth::user()->role_id=='26'){
          $this->action_state  = str_replace('eci','eci-agent',$this->action_state);
          $this->action_pc     = str_replace('eci','eci-agent',$this->action_pc);
          $this->action_ac     = str_replace('eci','eci-agent',$this->action_ac);
		  
        }
        return $next($request);
    });
  }

  public function IndexCardFinalizeView(Request $request){

    if(Auth::user()->role_id == '27'){
      $this->action_state  = 'eci-index/indexcardview/IndexCardFinalizeView';
      $this->action_pc     = 'eci-index/indexcardview/IndexCardFinalizeView/state';
    }
	
	if(Auth::user()->role_id == '4'){
      $this->action_state  = 'pcceo/indexcardview/IndexCardFinalizeView';
      $this->action_pc     = 'pcceo/indexcardview/IndexCardFinalizeView/state';
    }
    
      $data = [];
      $request_array = [];

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Index Card Finalize Status';
   
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      $data['filter_buttons'] = $title_array;

      if(Auth::user()->role_id == '4'){
        $data['state']  = Auth::user()->st_code;
      }

      $states = StateModel::get_states();
      $data['states'] = [];
      foreach($states as $result){
        if(Auth::user()->role_id == '4' && $result->ST_CODE == Auth::user()->st_code){
          $data['states'][] = [
              'code' => base64_encode($result->ST_CODE),
              'name' => $result->ST_NAME,
          ];
        }

        if(Auth::user()->role_id == '7' || Auth::user()->role_id == '27'){
          $data['states'][] = [
              'code' => base64_encode($result->ST_CODE),
              'name' => $result->ST_NAME,
          ];
        }
      }
      

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state.'/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state.'/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];



      $data['action']         = url($this->action_state);

      $results                = [];
      $filter_election = [
        'state'         => $data['state'],
      ];


      $object_states = IndexCardFinalize::get_reports($filter_election);

  
      $data['results']    =   $object_states;
      $data['user_data']  =   Auth::user();

       $data['heading_title_with_all'] = $data['heading_title'];
  /*    
       if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
            return $data;
       }
*/
      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.index.IndexCardFinalize', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }

 
  public function IndexCardFinalizeViewExcel(Request $request){

    set_time_limit(6000);
    $data = $this->IndexCardFinalizeView($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    //$export_data[] = ['', 'Index','','Card','','Voters','','Finalized','','Status'];

    $headings[] = ['State', 'PC Name - No','Finalized By Ro','Finalized By CEO','Nomination Finalized','Counting Finalized'];
    $export_data[]=[];

    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis->st_name,
        $lis->pcno.'-'.$lis->pc_name,
        $lis->FinalizeRo,
        $lis->FinalizeCeo,
        $lis->NominationFinalize,
        $lis->CountingFinalize,
       
      ];
    }




    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:D1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function IndexCardFinalizeViewPdf(Request $request){
    $data = $this->IndexCardFinalizeView($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.index.IndexCardFinalizePdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }



   public function IndexCardFinalizeViewTotal(Request $request){
    
      $data = [];
      $request_array = [];
	  
	  if(Auth::user()->role_id == '27'){
      $this->action_state  = 'eci-index/indexcardview/IndexCardFinalizeView';
      $this->action_pc     = 'eci-index/indexcardview/IndexCardFinalizeView/state';
    }

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Index Card Finalize Total';
   
      $data['filter_buttons'] = $title_array;

      if(Auth::user()->role_id == '4'){
        $data['state']  = Auth::user()->st_code;
      }
      

      $data['filter']   = implode('&', array_merge($request_array));
      //end set title

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url($this->action_state.'Total/excel').'?'.implode('&', $request_array),
        'target' => true
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url($this->action_state.'Total/pdf').'?'.implode('&', $request_array),
        'target' => true
      ];



      $data['action']         = url($this->action_state);

      $results                = [];
      

      $object_states = IndexCardFinalize::get_states();

  
      $data['results']    =   $object_states;
      $data['user_data']  =   Auth::user();

       $data['heading_title_with_all'] = $data['heading_title'];
  /*    
       if(Auth::user()->designation == 'CEO' && !$request->has('is_excel')){
            return $data;
       }
*/
      if($request->has('is_excel')){
        if(isset($title_array) && count($title_array)>0){
          $data['heading_title'] .= "- ".implode(', ', $title_array);
        }
        return $data;
      }

      return view($this->view_path.'.index.IndexCardFinalizeViewTotal', $data);

     try{}catch(\Exception $e){
      return Redirect::to('/eci/dashboard');
    }

  }



  public function IndexCardFinalizeViewTotalExcel(Request $request){

    set_time_limit(6000);
    $data = $this->IndexCardFinalizeViewTotal($request->merge(['is_excel' => 1]));

    $export_data = [];
    $export_data[] = [$data['heading_title']];

    //$export_data[] = ['', 'Index','','Card','','Voters','','Finalized','','Status'];

    $headings[] = ['State Name', 'Total PCs','PCs Finalized By RO','PCs Finalized By CEO','Nomination Finalized','Counting Finalized'];
    $export_data[]=[];

    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis->st_name,
        $lis->total_pc,
        $lis->finalize,
        $lis->FinalizeCeo,
        $lis->NominationFinalize,
        $lis->CountingFinalize,
      ];
    }

    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:F1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function IndexCardFinalizeViewTotalPdf(Request $request){
    $data = $this->IndexCardFinalizeViewTotal($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.index.IndexCardFinalizeViewTotalPdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }

  
  


}  // end class