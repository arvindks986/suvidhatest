<?php namespace App\Http\Controllers\Admin\Indexcard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB, Validator, Session, Redirect;
use App\models\Admin\PcModel;
use App\models\Admin\{ElectionModel, StateModel};
use App\models\Admin\IndexCardUploadModel;
use App\Classes\xssClean;
use App\models\Admin\IndexCardComplainModel;
use App\models\Admin\IndexCardFinalize;
use App\models\Admin\IndexcardLogModel;
use App\commonModel;
use PDF;
use Excel;

class ComplainController extends Controller {

  public $base          = '';
  public $folder        = '';
  public $action        = '';
  public $current_page  = '';
  public $pc_no         = 0;
  public $st_code       = 0;
  public $view_path     = "admin.pc.indexcard";
  public $definalize_access = false;

  public function __construct(){
    $role_id = 0;
    $this->xssClean = new xssClean;
	$this->commonModel = new commonModel();
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        $role_id = Auth::user()->role_id;
        if($role_id == '18'){
          $this->base         = 'ropc';
          $this->action       = 'ropc/indexcard/get-complains';
          $this->current_page = 'ropc/indexcard/get-complains';
          $this->definalize_action = 'ropc/indexcard/get-complains/post';
          $this->view_path    = '';
          $this->pc_no        = \Auth::user()->pc_no;
          $this->st_code      = \Auth::user()->st_code;
        }else if($role_id == '4'){
          $this->base         = 'pcceo';
          $this->action       = 'pcceo/indexcard/upload-indexcard/post';
          $this->current_page = 'pcceo/indexcard/get-complains';
          $this->definalize_action = 'pcceo/indexcard/get-complains/post';
          $this->view_path    = '';
          $this->st_code      = \Auth::user()->st_code;
        }else if($role_id == '27'){
          $this->base         = 'eci-index';
          $this->action       = 'eci-index/indexcard/get-complains/post';
          $this->current_page = 'eci-index/indexcard/get-complains';
          $this->definalize_action = 'eci-index/indexcard/get-complains/post';
          $this->view_path    = '';
        } else{
          $this->base         = 'eci';
          $this->action       = 'eci/indexcard/get-complains/post';
          $this->current_page = 'eci/indexcard/get-complains';
          $this->definalize_action = 'eci/indexcard/get-complains/post';
          $this->view_path    = '';
        }

        if(in_array($role_id,['7','27'])){
          $this->definalize_access = true;
        }

        return $next($request);
    });
  }

  public function post_complain_indexcard(Request $request){
    $data                   = [];
    $data['election_id']    = 1;
    $data['st_code']        = $request->st_code;
    $data['pc_no']          = $request->pc_no;
    $data['need_approval']  = $request->need_approval;
    $data['sub_heading']    = $request->sub_heading;
    $data['type']           = $request->type;
    $complain = [];
    foreach ($request->complain as $key => $result) {
      if($result['new_value'] && trim($result['new_value']) != ''){
        $complain[] = [
          'label'     => $result['label'],
          'old_value' => $this->xssClean->clean_input($result['old_value']),
          'new_value' => $this->xssClean->clean_input($result['new_value']),
          'comment' => $this->xssClean->clean_input($result['comment']),
        ];
      }
    }
    $data['complain'] = $complain;
    try{
      IndexCardComplainModel::add_complain($data);
    }catch(\Exception $e){
      Session::flash('status',0);
      Session::flash('flash-message','Please try again.');
      return Redirect::back()->withInput($request->all());
    }
    Session::flash('status',1);
    Session::flash('flash-message','Request has been added.');
    return Redirect::back();
  }

  public function get_complains_list(Request $request){
    $data                   = [];
      $filter                 = [];
      $data['pc_no']          = NULL;
      $data['st_code']        = NULL;
      $data['election_id']    = NULL;
      $data['custom_errors']  = [];
      if($request->has('election_id')){
        $data['election_id']       = $request->election_id;
        $filter['election_id']     = $data['election_id'];
      }

      if($request->has('pc_no')){
        $data['pc_no']       = $request->pc_no;
      } 

      if($request->has('st_code')){
        $data['st_code']       = $request->st_code;
      }     

      if(\Auth::user()->role_id == '18'){
        $data['pc_no']          = $this->pc_no;
        $data['st_code']        = $this->st_code;
        $filter['pc_no']        = $data['pc_no'];
        $filter['st_code']      = $data['st_code'];
      }

      if(\Auth::user()->role_id == '4'){
        $data['st_code']        = $this->st_code;
        $filter['st_code']      = $data['st_code'];
      }
      
      $data['action']         = url($this->action);
      $data['current_page']   = url($this->current_page);

      $data['heading_title']  = 'Index Card';
      $data['filter_buttons'] = [];

      //years
      $data['elections']      = [];
      $elections              = ElectionModel::get_current_elections();
      foreach ($elections as $key => $result) {
        $data['elections'][] = [
           'election_id'      => $result['ELECTION_ID'],
           'election_type'    => $result['ELECTION_TYPE'].'-'.$result['YEAR'],
        ];
      }

      $data['states'] = [];
      $states = StateModel::get_states();
      foreach ($states as $key => $iterate_state) {
         $data['states'][] = [
           'st_code' => $iterate_state->ST_CODE,
           'st_name' => $iterate_state->ST_NAME,
         ];
      }

      $pcs          = [];
      foreach (PcModel::get_pcs(['st_code' => $data['st_code']]) as $pc_result){
		  if(\Auth::user()->role_id == '18'){    
			if($pc_result['pc_no']==$this->pc_no){
				$pcs[] = [
				  'pc_no'   => $pc_result['pc_no'],
				  'pc_name' => $pc_result['pc_name']
				];
			}		  
		  }else{
			  $pcs[] = [
				  'pc_no'   => $pc_result['pc_no'],
				  'pc_name' => $pc_result['pc_name']
				];
		  }
      }
      $data['pcs']      = $pcs;
      $data['results']  = [];

      $results = IndexCardComplainModel::get_complains(['pc_no' => $data['pc_no'], 'st_code' => $data['st_code']]);
      $need_approval    = 0;
      $no_need_approval = 0;
      foreach ($results as $res_iterate) {


        //main heading data
        $first_complain_data = [];
        $first_results = IndexCardComplainModel::get_first_level_data([
          'pc_no'       => $res_iterate->pc_no,
          'st_code'     => $res_iterate->st_code,
          'election_id' => $res_iterate->election_id,
          'date'        => date('Y-m-d', strtotime($res_iterate->created_at))
        ]);

        foreach ($first_results as $first_iterate) {
          $sub_complaing_data = [];
          $sub_heading_results = IndexCardComplainModel::get_subheading_wise_data([
            'pc_no'       => $res_iterate->pc_no,
            'st_code'     => $res_iterate->st_code,
            'election_id' => $res_iterate->election_id,
            'type'        => $first_iterate['type'],
            'date'        => date('Y-m-d', strtotime($res_iterate->created_at))
          ]);

          

          foreach($sub_heading_results as $sub_heading_iterate){
            $complain_results = IndexCardComplainModel::get_hierarchy_complains([
              'pc_no'       => $res_iterate->pc_no,
              'st_code'     => $res_iterate->st_code,
              'election_id' => $res_iterate->election_id,
              'date'        => date('Y-m-d', strtotime($res_iterate->created_at)),
              'sub_heading' => $sub_heading_iterate['sub_heading']
            ]);


            $html = "";
            $required_approval = '';

            foreach($complain_results as $com_iterate){
              
              $complain_data    = [];

              $is_serialize = @unserialize($com_iterate['complain']);
              if ($is_serialize !== false) {
                  $complains = unserialize($com_iterate['complain']);
              } else {
                  $complains = [];
              }
              if($com_iterate['need_approval'] == '1'){
                $need_approval++;
                $required_approval = "(Required ECI Approval)";
              }else{
                $no_need_approval++;
                $required_approval = "(Required definalization)";
              }
              foreach ($complains as $key => $value) {
                $label = '';
                if(isset($value['label'])){
                  $label      = $value['label'];
                }
                $old_value = '';
                if(isset($value['old_value'])){
                  $old_value      = $value['old_value'];
                }
                $new_value = '';
                if(isset($value['new_value'])){
                  $new_value      = $value['new_value'];
                }
                $comment = '';
                if(isset($value['comment'])){
                  $comment      = $value['comment'];
                }
                $complain_data[] = [
                  'label'     => $label,
                  'old_value' => $old_value,
                  'new_value' => $new_value,
                  'comment'   => $comment
                ];
              }
            }
            $sub_complaing_data[] = [
              'heading' => $sub_heading_iterate['sub_heading'],//.$required_approval,
              'data' => $complain_data
            ];
          }

          $first_complain_data[] = [
            'heading' => $first_iterate['type'],
            'date'    => $sub_complaing_data
          ];
        }

        $html = '';
        foreach ($first_complain_data as $level1_iter) {
          $html .= "<h3 class='complain-heading-main'>".$level1_iter['heading']."</h3>";
          foreach ($level1_iter['date'] as $level2_iter) {
            $html .= "<div class='fullwidth'>";
            $html .= "<table class='table table-bordered'>";
            $html .= "<tr><th colspan='4'>".$level2_iter['heading']."</th></tr>";
            $html .= "<tr><td>Name</td><td>Old Value</td><td>Update Value</td><td>Comment</td></tr>";
            foreach ($level2_iter['data'] as $level3_iter) {
              $html .= "<tr><td>".$level3_iter['label']."</td><td>".$level3_iter['old_value']."</td><td>".$level3_iter['new_value']."</td><td>".$level3_iter['comment']."</td></tr>";
            }
            $html .= "</table>";
            $html .= "</div>";
          }
        }

        $data['results'][] = [
            'st_code'           => $res_iterate->st_code,
            'pc_no'             => $res_iterate->pc_no,
            'definalize_action' => url($this->definalize_action),
            'definalize_access' => $this->definalize_access,
            'need_approval'     => ($need_approval)?'Yes':'No',
            'no_need_approval'  => $no_need_approval,
            'definalize_status' => $res_iterate->definalize_status,
            'st_name'        => $res_iterate->st_name,
            'pc_name'        => $res_iterate->pc_name,
            'complain'       => $html,
            'status'         => $res_iterate->status,
            'date'           => date('d/m/Y',strtotime($res_iterate->created_at))
        ];

      }

		$data['definalize_action_nomination'] = url('eci-index/indexcard/definalize-nomination');
		$data['definalize_action_counting'] = url('eci-index/indexcard/definalize-counting');

      //data elector cdec
 
      $data['user_data']  =   Auth::user();

      return view('admin.indexcard.complain_indexcard_list',$data);
  }


  public static function get_help_data(Request $request){
  
    $data               = [];
        $filter                 = [];
        $data['pc_no']          = NULL;
        $data['election_id']    = NULL;
        if($request->has('election_id')){
          $data['election_id']  = $request->election_id;
        }

        if($request->has('pc_no')){
          $data['pc_no']        = $request->pc_no;
        }     

        if(Auth::user()->role_id == '18'){
          $data['pc_no']       = Auth::user()->pc_no;
          $filter['pc_no']     = $data['pc_no'];
          $data['current_page']   = url('/ropc/indexcard/indexcardpc');
        }else{
          $data['current_page']   = url('/pcceo/indexcard/indexcardpc');
        }
          
        $filter['st_code']      = Auth::user()->st_code;
        //years
        $data['elections']      = [];
        $elections              = ElectionModel::get_current_elections();
        foreach ($elections as $key => $result) {
          $data['elections'][] = [
             'election_id'      => $result['ELECTION_ID'],
             'election_type'    => $result['ELECTION_TYPE'].'-'.$result['YEAR'],
          ];
        }

        $pcs          = [];
        foreach (PcModel::get_pcs($filter) as $pc_result){
          $pcs[] = [
            'pc_no'   => $pc_result['pc_no'],
            'pc_name' => $pc_result['pc_name']
          ];
        }
        $data['pcs']            = $pcs;
        $data['heading_title']  = "Index card edit request";
        
     

     
      return $data;
  } 


  public function definalize_indexcard(Request $request){
    if($this->definalize_access){
      $filter = [];
      $filter['pc_no'] = $request->pc_no;
      $filter['st_code'] = $request->st_code;
      try{
        IndexCardFinalize::definalize_status($filter);
      }catch(\Exception $e){
        Session::flash('status',0);
        Session::flash('flash-message','Please try again.');
        return Redirect::back()->withInput($request->all());
      }
      Session::flash('status',1);
      Session::flash('flash-message','Definalized successfully.');
    }
    return Redirect::back();
  }

	public function definalize_nomination(Request $request){
   if($this->definalize_access){
     $ele_details=$this->commonModel->election_details_cons($request->st_code,$request->pc_no,'PC');
     $filter = [];
     $filter['pc_no'] = $request->pc_no;
     $filter['const_no'] = $request->pc_no;
     $filter['st_code'] = $request->st_code;
     $filter['election_id'] = $ele_details->ELECTION_ID;
     $filter['const_type'] = 'PC';
     $filter['finalize_by'] = \Auth::user()->officername;
     $filter['message'] = @$request->comment;
     try{
        IndexcardLogModel::nomination_definalize_log($filter);
       //IndexCardFinalize::definalize_nomination($filter);
       candidate_definalize($filter);
     }catch(\Exception $e){
       Session::flash('status',0);
       Session::flash('flash-message','Please try again.');
       return Redirect::back()->withInput($request->all());
     }
     Session::flash('status',1);
     Session::flash('flash-message','Nomination Definalized Successfully.');
   }
   return Redirect::back();
 }
 public function definalize_counting(Request $request){
   if($this->definalize_access){        
      $ele_details=$this->commonModel->election_details_cons($request->st_code,$request->pc_no,'PC');
     $filter = [];
     $filter['pc_no'] = $request->pc_no;
     $filter['const_no'] = $request->pc_no;
     $filter['st_code'] = $request->st_code;
     $filter['election_id'] = $ele_details->ELECTION_ID;
     $filter['const_type'] = 'PC';
     $filter['finalize_by'] = \Auth::user()->officername;
     $filter['message'] = @$request->comment;
    try{
        IndexcardLogModel::counting_definalize_log($filter);
       //IndexCardFinalize::definalize_counting($filter);
       counting_definalize($filter);
      }catch(\Exception $e){
       Session::flash('status',0);
       Session::flash('flash-message','Please try again.');
       return Redirect::back()->withInput($request->all());
     }
     Session::flash('status',1);
     Session::flash('flash-message','Counting Definalized Successfully.');
   }
   return Redirect::back();
 }
  public function deFinalizePcs(Request $request){
		//try{
			$data['results'] = IndexCardFinalize::definalize_pcs();
			$data['user_data'] = \Auth::user();
		
			if($request->path() == "eci-index/indexcard/de-finalize-pcs/pdf"){
				
			$pdf = PDF::loadView('admin.indexcard.IndexCardDeFinalizePdf', $data);
			
				
			return $pdf->download('IndexCard De-Finalize Report.pdf');
			
		}else if($request->path() == "eci-index/indexcard/de-finalize-pcs/excel"){
			
			
			return Excel::create('IndexCard De-Finalize Report', function($excel) use ($data) {
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
	  	  
					$sheet->mergeCells('A1:E1');
	  
					$sheet->cell('A1', function($cells) {
						$cells->setValue('IndexCard De-Finalize Report');
						$cells->setFont(array('name' => 'Times New Roman','size' => 15,'bold' => true));
                        $cells->setAlignment('center');
					});
					

			
					$sheet->cell('A3', function($cells) {
						$cells->setValue('Sl No.');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
		
					$sheet->cell('B3', function($cells) {
						$cells->setValue('State Name');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$sheet->cell('C3', function($cells) {
						$cells->setValue(' PC No - PC Name ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$sheet->cell('D3', function($cells) {
						$cells->setValue(' De-Finalized Type ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					$sheet->cell('E3', function($cells) {
						$cells->setValue(' De-Finalized Date ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$i= 4;
									
					if (!empty($data)) {
							
						foreach ($data['results'] as $key => $result){
														
							$sheet->cell('A'.$i, $key+1); 
							$sheet->cell('B'.$i, $result->st_name ); 
							$sheet->cell('C'.$i, $result->pc_no.' - '.$result->pc_name );
							$sheet->cell('D'.$i, ucfirst($result->type_finalize) );
							$sheet->cell('E'.$i, date('d-m-Y h:i A', strtotime($result->created_at)) );
							
							$i++;						
						}
					}
					
					$i++;

		
				});
			})->download('xls');
			
			
			
		}else{
			return view('admin.indexcard.IndexCardDeFinalize', $data);
		}	
			
		
		
       /* }catch(\Exception $e){
        return Redirect::back();
      } */
  }
  
}  // end class