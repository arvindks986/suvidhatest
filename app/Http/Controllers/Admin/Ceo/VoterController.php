<?php namespace App\Http\Controllers\Admin\Ceo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB, Validator, Config, Session, Redirect;
use App\models\Admin\ElectorVoterModel;

class VoterController extends Controller {
  
  public $base          = 'ro';
  public $folder        = 'pcceo';
  public $action        = 'pcceo/voters/post';
  public $current_page  = 'pcceo/voters/edit';
  public $view_path     = "admin.pc.ceo";

  public function edit_voters_form(Request $request){
      
	    $user =   Auth::user();
		
	  $dataArray = DB::table('m_pc as mp')
				->select('mp.ST_CODE','mp.PC_NO','mp.PC_NAME','ovi.test_votes_49_ma','ovi.nri_male_voters','ovi.nri_female_voters','ovi.nri_other_voters')
				->leftjoin('other_votes_information as ovi', function($join){
					$join->on('mp.ST_CODE','=','ovi.st_code')
						->on('mp.PC_NO','=','ovi.pc_no');
				})
				->where('mp.ST_CODE',$user->st_code)
				->orderBy('mp.PC_NO','ASC')
				->get()->toArray();
      
	  
	 // echo '<pre>'; 
	  //print_r($dataArray);
	//  echo $dataArray[0]->PC_NO;
	//  die;
	  
	  $data['results'] = $dataArray;
	  $data['user_data'] = $user;
    

      if($request->old('elector')){
        $data['results'] = $request->old('elector');
      }


      if($request->old('custom_errors')){
        $data['custom_errors'] = $request->old('custom_errors');
      }

 

      return view($this->view_path.'.voters.edit_voters_form',$data);

  }

  public function post_voters_form(Request $request){



   // echo '<pre>'; print_r($request->all()); die;

    //if(!$is_error){
		
		foreach($request->pc_no as $pcno){
			
			
			$datainsert = array('st_code'=>Auth::user()->st_code,
								'pc_no'=>$pcno,
								'test_votes_49_ma'=>$request->test_votes_49_ma[$pcno],
								'nri_male_voters'=>$request->nri_male_voters[$pcno],
								'nri_female_voters'=>$request->nri_female_voters[$pcno],
								'nri_other_voters'=>$request->nri_other_voters[$pcno],
								'submitted_by' => Auth::user()->officername,
								'year' => date('Y')
								);
			
			$updatainsert = array(
								'test_votes_49_ma'=>$request->test_votes_49_ma[$pcno],
								'nri_male_voters'=>$request->nri_male_voters[$pcno],
								'nri_female_voters'=>$request->nri_female_voters[$pcno],
								'nri_other_voters'=>$request->nri_other_voters[$pcno],
								'submitted_by' => Auth::user()->officername,
								'year' => date('Y')
								);
												
			
			$check =DB::table('other_votes_information')->where(array('st_code'=>Auth::user()->st_code,
								'pc_no'=>$pcno))->first();
								
			//print_r($check); die;					
			if($check)
			{
				DB::table('other_votes_information')->where(array('st_code'=>Auth::user()->st_code,
								'pc_no'=>$pcno))->update($updatainsert);
				
			}
			else{	
			
			die('aaaaa');
				DB::table('other_votes_information')->insert($datainsert);
			//die(12);
				
			}
			
		}
    return redirect()->back()->with('success', 'Records Success Updated!!');
  
  }


  


}  // end class