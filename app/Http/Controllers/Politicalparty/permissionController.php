<?php
namespace App\Http\Controllers\politicalparty;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DB;
use Config;
use \PDF;
use App\Classes\xssClean;
use Illuminate\Support\Facades\Hash;
use App\models\Permission\PermissionModel;
use App\models\Permission\User_dataModel;
use App\commonModel;
use App\Http\helpers;
use App\Helpers\SendNotification;
use App\models\{States, Districts, AC};

class permissionController extends Controller
{   

// AdD Auth 
	public function __construct() {
        $this->middleware('usersession');
        $this->middleware(['auth:web','auth']);
        $this->commonModel = new commonModel();
        $this->xssClean = new xssClean;
        // $this->middleware('cand');
    }
    protected function guard(){
       return Auth::guard('web');
       }
// 
    public function index()
	{
		Auth::guard('web');
        if(Auth::check()){

		$users=Session::get('login_details');
        $user = Auth()->user();
        $id=$user->id;
        // dd($users);
        	$applied_permission = DB::table('permission_request')
        	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
        	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
        	->where('user_id','=',$id)
        	->select('permission_request.*','permission_request.id as permission_id','m.permission_name','permission_request.permission_mode')
        	->get();
        	$data=DB::table('permission_request')
               ->select(DB::raw('sum(CASE WHEN (approved_status = 0 && cancel_status!= 1) THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN (approved_status = 2 && cancel_status!= 1) THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN (approved_status = 1 && cancel_status!= 1) THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN (approved_status = 3 && cancel_status!= 1) THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(approved_status) as total'),DB::raw('sum(CASE WHEN (cancel_status = 1) THEN 1 ELSE 0 END) as cancle'))
               ->where('user_id','=',$id)
               // ->groupBy('approved_status')
               ->get()->toArray();
      		// dd($data);
        	
 			return view('politicalparty.permissionone', ['total'=>$data,'permissionDetails'=>$applied_permission]);	
 		}else{
 			return Redirect::back();
 		}          			
	}

public function permissionrole(Request  $request)
	{
		// dd(DB::connection()->getDatabaseName());
		// dd(Auth::User());
		Auth::guard('web');
        if(Auth::check()){
		$data = $request->all();
		$validator = Validator::make($data, [
                'role_id'				=>'required|not_in:0',
				//'party_id' 	    		=>'required|not_in:0',
				
           ]);
           if ($validator->fails()) {
              return Redirect::back()
              ->withErrors($validator)
              ->withInput();
           	}else{
				$users=Session::get('login_details');
		        $user = Auth()->user();
				$userid=$user->id;
				$mobile=$user->mobile;
				$role_id = $request->input('role_id');
				//$party_id = $request->input('party_id');
				//$data = array('role_id'=>$role_id,'party_id'=>$party_id);
				$data = array('role_id'=>$role_id);
				$role_type = DB::table('user_login')->where('id',$userid)->update($data);
				$role= DB::table('user_role')->where('role_id',$role_id)->get();
		              $roletype=$role[0]->role_name;
		              Session::put('Applicant_type', $roletype);
                              Session::put('role_id', $role_id);
		              // dd(session::get('Applicant_type'));
				$u_data=DB::table('user_data')->where('mobileno',$mobile)->get();
				
				
				if($user->election_category != '2'){
						//First Login	
						$first_login=DB::connection('mysql')->table('user_login')->select('first_login')->where('id', '=', \Auth::id())->value('first_login');
						if($first_login=='' or $first_login==0){	
						 return redirect('/first-login-user-view');
						}
				}
				
				if(count($u_data)>0)
				{



                                    if($role_id != 2){
                                            //$result=DB::table('user_data')->where('mobileno',$mobile)->update(['party_id' => $party_id]);
					    return Redirect::to('/update profile');
                                        }
                                        else{
						
							
						
						//$result=DB::table('user_data')->where('mobileno',$mobile)->update(['party_id' => $party_id]);
						return Redirect::to('/dashboard-nomination-new');
					}
				}else{
//					if($user->election_category == '2'){
//						return Redirect::to('/nomination/mlc/apply-nomination-step-1'); 
//					}
                                    if($role_id != 2){
                                        return Redirect::to('/profile'); 
                                    }
                                        else{
						return Redirect::to('/nomination/apply-nomination-step-1'); 
					}
				}

			    
		}
		}else{
			return Redirect::back();
		}
	}

	public function create()
	{
		Auth::guard('web');
        if(Auth::check()){
		$users=Session::get('login_details');
        $user = Auth()->user();
		$userid=$user->id;
		$mobile=$user->mobile;
		// put applicant type in session
		$userrole=$user->role_id;
		$type=DB::table('user_role')->where('role_id',$userrole)->select('role_name')->get();
		$role_type=$type[0]->role_name;
		Session::put('Applicant_type', $role_type);
		// end applicant type in session
		$res=DB::table('user_data')->where('mobileno',$mobile)->where('user_login_id',$userid)->get();
		// dd($res);
		if(count($res)>0)
		{
			
			$state=DB::table('m_state')->get();
			// get user information
			$user_details=DB::table('user_data')->join('m_party','m_party.CCODE','user_data.party_id')->join('m_state as e','e.ST_CODE','=','user_data.state_id')
			->join('m_district as f',function ($join){$join->on('f.DIST_NO','=','user_data.district_id')
				->on('f.ST_CODE', '=', 'e.ST_CODE');})
			// ->join('m_ac as g',function ($join){$join->on('g.AC_NO','=','user_data.ac_id')
			// 	->on('g.ST_CODE', '=', 'e.ST_CODE')->on('g.DIST_NO_HDQTR', '=', 'f.DIST_NO');
	  //              })
			->where('user_data.user_login_id',$userid)
			->select('user_data.user_login_id','user_data.name','user_data.email','user_data.mobileno','e.ST_CODE','e.ST_NAME','f.DIST_NO','f.DIST_NAME','m_party.CCODE','m_party.PARTYNAME')
			->get();
			// dd($user_details);
			// get location detail
			$user_details_location=DB::table('user_data')->join('m_state as e','e.ST_CODE','=','user_data.state_id')
			->join('m_district as f',function ($join){$join->on('f.DIST_NO','=','user_data.district_id')
				->on('f.ST_CODE', '=', 'e.ST_CODE');})
			->join('m_ac as g',function ($join){$join->on('g.AC_NO','=','user_data.ac_id')
				->on('g.ST_CODE', '=', 'e.ST_CODE')->on('g.DIST_NO_HDQTR', '=', 'f.DIST_NO');
	               })
			->join('location_master as l',function($join){$join->on('l.ac_no','=','user_data.ac_id')->on('g.AC_NO','=','user_data.ac_id')
				->on('g.ST_CODE', '=', 'e.ST_CODE')->on('g.DIST_NO_HDQTR', '=', 'f.DIST_NO')->on('user_data.state_id','=','l.st_code');
			})
			->where('user_data.user_login_id',$userid)
			->select('l.location_name','l.id')->orderBy('l.location_name','ASC')
			->get();
			 // dd($user_details_location);
			// get police station location
			$user_details_police=DB::table('user_data')->orderBy('police_st_name')->join('m_state as e','e.ST_CODE','=','user_data.state_id')->join('police_station_master as p',function($join){$join->on('p.ST_CODE','=','e.ST_CODE')->on('p.ac_no','=','user_data.ac_id');})->where('user_data.user_login_id',$userid)->select('p.police_st_name','p.police_station_address','p.id')->get();
			
			// get permission Type
			$st=$res[0]->state_id;
//			 dd($st);
			$permission_type=DB::table('permission_type')->join('permission_master','permission_master.id','permission_type.permission_type_id')
			->orderBy('permission_master.permission_name')
			->where('permission_master.status','1')
			->where('permission_type.st_code',$st)
                        ->select('permission_master.permission_name','permission_type.id as permsn_id','permission_type.permission_type_id')
//			->select('permission_master.permission_name','permission_master.id')
			->get();
			// dd($permission_type);
	 		if(!empty($user_details))
			{
				return view('politicalparty.createpermission',compact('permission_type','user_details','user_details_location','user_details_police','state'));
			}else
			{
				return Redirect::to('/profile')->with('msg', 'Please Update Profile Details First to Apply Permission!');
			}
		}else{
			return Redirect::to('/profile')->with('msg', 'Please Fill Profile Details First to Apply Permission!');
		}
		}else{
			return Redirect::back();//Redirect::back()
		}
			                   
	}
	
	public function store(Request $request)
	{
		Auth::guard('web');
        if(Auth::check()){
			$users=Session::get('login_details');
	        $user = Auth()->user();
			$userid=$user->id;
			$data = $request->all();
			// dd($data);
			$doc_data = $request->file('permsndoc');
            $doc_name='';
            if(!empty($doc_data) && count($doc_data)>0)
            {
            	sort($doc_data);
            }else
            {
            	$doc_data;
            }
           // dd($request->electiondate);
            $ptypeid=$request->permission_type;
            // Error check for permissionId NULL for vechile
            if(empty($ptypeid) || $ptypeid == 0 || $ptypeid == null)
            {
            	$validator = Validator::make($data, [
            	'permission_type' 	=>'required|not_in:0',
           		'district'			=>'required|not_in:0',
           		'ac'				=>'required|not_in:0',
				'police_station'	=>'required|not_in:0',
				'location1'        	=>'required|not_in:0',
				'start'          	=>'required|date',
				'end'            	=>'required|date|after_or_equal:start',
				'permsndoc.*.p_doc' =>'mimes:pdf'
				],
				[
				  'permission_type' => 'Please Select Permission Type !',
				  'district'=>'Please Select District Name!',
                  'ac'=>'Please Select Assembly Constituency!',
                  'police_station'=>'Please Select Police Station Type!',
                  'location1' => 'Please Select Event Place Name!',
                  'start.date' => 'Event Start date should be after 48 hour ',
                  'end.date'=>'Event end Time should be grater then Event start time!',
                  'permsndoc.*.p_doc.mimes'=>'Please Upload Only PDF File!',
                ]);
            }elseif(!empty($ptypeid) || $ptypeid > 0)
            {
            	// Error Check For permission not null for vechile 
            	$pdata=explode('#', $ptypeid);
            		if($pdata[1] == 8)
            		{
            			if($request->PC != null || $request->PC)
            			{
            				$validator = Validator::make($data, [
							'permission_type' 	=>'required|not_in:0',
							'PC' 				=>'required|not_in:0',
							'start'          	=>'required|date',
							'end'            	=>'required|date|after_or_equal:start',
							'permsndoc.*.p_doc' =>'mimes:pdf'
							],
							[
			                  'permission_type' => 'Please Select Permission Type !',
			                  'PC'				=> 'Please Select PC !',
			                  'start.date' => 'Event Start date should be after 48 hour ',
			                  'end.date'=>'Event end Time should be grater then Event start time!',
			                  'permsndoc.*.p_doc.mimes'=>'Please Upload Only PDF File!',
			                ]);
            			}else
            			{
            				$validator = Validator::make($data, [
			           		'district'			=>'required|not_in:0',
			           		'ac'				=>'required|not_in:0',
							'permission_type' 	=>'required|not_in:0',
							'police_station'	=>'required|not_in:0',
							'start'          	=>'required|date',
							'end'            	=>'required|date|after_or_equal:start',
							'permsndoc.*.p_doc' =>'mimes:pdf'
							],
							[
							  'district'=>'Please Select District Name!',
			                  'ac'=>'Please Select Assembly Constituency!',
			                  'permission_type' => 'Please Select Permission Type !',
			                  'police_station'=>'Please Select Police Station Type!',
			                  'start.date' => 'Event Start date should be after 48 hour ',
			                  'end.date'=>'Event end Time should be grater then Event start time!',
			                  'permsndoc.*.p_doc.mimes'=>'Please Upload Only PDF File!',
			                ]);
            			}	
            		}
		            elseif($pdata[1] == 3  || $pdata[1] == 6)
		            {
		            		$validator = Validator::make($data, [
			           		'district'			=>'required|not_in:0',
			           		'ac'				=>'required|not_in:0',
							'permission_type' 	=>'required|not_in:0',
							'police_station'	=>'required|not_in:0',
							'start'          	=>'required|date',
							'end'            	=>'required|date|after_or_equal:start',
							'permsndoc.*.p_doc' =>'mimes:pdf'
							],
							[
							  'district'=>'Please Select District Name!',
			                  'ac'=>'Please Select Assembly Constituency!',
			                  'permission_type' => 'Please Select Permission Type !',
			                  'police_station'=>'Please Select Police Station Type!',
			                  'start.date' => 'Event Start date should be after 48 hour ',
			                  'end.date'=>'Event end Time should be grater then Event start time!',
			                  'permsndoc.*.p_doc.mimes'=>'Please Upload Only PDF File!',
			                ]);  	
			       }
			       elseif($pdata[1] >0 && $request->district==null)
			       {  
			       	  $validator = Validator::make($data, [
			           		'district'			=>'required|not_in:0',
			           		'ac'				=>'required|not_in:0',
							'permission_type' 	=>'required|not_in:0',
							'police_station'	=>'required|not_in:0',
							'location1'        	=>'required|not_in:0',
							'start'          	=>'required|date',
							'end'            	=>'required|date|after_or_equal:start',
							'permsndoc.*.p_doc' =>'mimes:pdf'
							],
							[
							  'district'=>'Please Select District Name!',
			                  'ac'=>'Please Select Assembly Constituency!',
			                  'permission_type' => 'Please Select Permission Type !',
			                  'police_station'=>'Please Select Police Station Type!',
			                  'location1' => 'Please Select Event Place Name!',
			                  'start.date' => 'Event Start date should be after 48 hour ',
			                  'end.date'=>'Event end Time should be grater then Event start time!',
			                  'permsndoc.*.p_doc.mimes'=>'Please Upload Only PDF File!',
			                ]);
			       }else
			       {  
			       	  $validator = Validator::make($data, [
			           		'district'			=>'required|not_in:0',
			           		'ac'				=>'required|not_in:0',
							'permission_type' 	=>'required|not_in:0',
							'police_station'	=>'required|not_in:0',
							'location1'        	=>'required|not_in:0',
							'start'          	=>'required|date',
							'end'            	=>'required|date|after_or_equal:start',
							'permsndoc.*.p_doc' =>'mimes:pdf'
							],
							[
							  'district'=>'Please Select District Name!',
			                  'ac'=>'Please Select Assembly Constituency!',
			                  'permission_type' => 'Please Select Permission Type !',
			                  'police_station'=>'Please Select Police Station Type!',
			                  'location1' => 'Please Select Event Place Name!',
			                  'start.date' => 'Event Start date should be after 48 hour ',
			                  'end.date'=>'Event end Time should be grater then Event start time!',
			                  'permsndoc.*.p_doc.mimes'=>'Please Upload Only PDF File!',
			                ]);
			       }
            }else
            {
            	$validator = Validator::make($data, [
		           		'district'			=>'required|not_in:0',
		           		'ac'				=>'required|not_in:0',
						'permission_type' 	=>'required|not_in:0',
						'police_station'	=>'required|not_in:0',
						'location1'        	=>'required|not_in:0',
						'start'          	=>'required|date',
						'end'            	=>'required|date|after_or_equal:start',
						'permsndoc.*.p_doc' =>'mimes:pdf'
						],
						[
						  'district'=>'Please Select District Name!',
		                  'ac'=>'Please Select Assembly Constituency!',
		                  'permission_type' => 'Please Select Permission Type !',
		                  'police_station'=>'Please Select Police Station Type!',
		                  'location1' => 'Please Select Event Place Name!',
		                  'start.date' => 'Event Start date should be after 48 hour ',
		                  'end.date'=>'Event end Time should be grater then Event start time!',
		                  'permsndoc.*.p_doc.mimes'=>'Please Upload Only PDF File!',
		                ]);  	  
            }

            if($request->electiondate == null || $request->district == null)
            {
            	return Redirect::back()->with('msg','Please Select all required field !');
            }
           $ed=date_create($request->electiondate);
            // dd($ed);
            if($ed != false)
            {
            	$election_date = date_sub($ed,date_interval_create_from_date_string("2 days"));
				$final_election_date =date_format($election_date,"d-m-Y");

	            if(strtotime($final_election_date) <  strtotime(date('d-m-Y',strtotime($request->end))))
	            {

	            	return Redirect::back()->with('msg','You can not apply for permission because election schedule date is '.$request->electiondate.' .');
	            } 
            }else
            {
            	return Redirect::back()->with('msg','Please Select Parliamentary Constituency !');
            }
            $startdate = $request->start;
            $enddate = $request->end;
            $cur_timenow = Carbon::now();
            $addtimestart = $cur_timenow->addHours(48);
            $addtime1 = $addtimestart->format('d-m-Y H:m:s');
//            $addtimeend = $cur_timenow->addHours(72);
//            $addtime2 = $addtimeend->format('d-m-Y H:m:s');
//            dd($addtime2);
            if(strtotime($startdate) < strtotime($addtime1))
            {
                return Redirect::back()->with('msg','Permission to be applied 48 hour before !');
            }
//            if($enddate < $addtime2)
//            {
//                return Redirect::back()->with('msg','Please select currect date');
//            }
           if ($validator->fails()) {
              return Redirect::back()
              ->withErrors($validator)
              ->withInput();
           }else{
           		if(!empty($doc_data) && count($doc_data)>0)
				{
					foreach ($doc_data as $key => $file) {
						$size = round(($file['p_doc']->getSize()/(1000*1024)),2);
						if($size > 5){
							return Redirect::back()->with('msg',"Maximum allowed file sizs is 5 MB");
						}
					}

					foreach ($doc_data as $key => $file) {
						if($file['p_doc']->getMimeType()!='application/pdf')
						 {
							return Redirect::back()->with('msg',"You can upload only .pdf file");
						 }

						 $name = $file['p_doc']->getClientOriginalName();
	                	 $size = $file['p_doc']->getSize();
	                	 
	                	 $time=Carbon::now()->timestamp;
	                     $doc_name.=$userid.'_'.$request->state.'_'.$time.'_'.$name.',';//dd($doc_name);
	                     $format=$userid.'_'.$request->state.'_'.$time.'_'.$file['p_doc']->getClientOriginalName();
	                     // echo round(($size/(1000*1024)),2)."- "; 
	                     $destinationPath3 = public_path('/uploads/userdoc/permission-document/');
	                     $file['p_doc']->move($destinationPath3,$format);

					}
				}
           	$permission = new PermissionModel;
	        $data['user_id'] 				= $request->userid;
	        $data['st_code'] 				= $request->state;
	        $data['dist_no']				= $request->district;
	        $data['ac_no'] 					= $request->ac;
	        $data['pc_no'] 					= $request->PC;
	        $data['party_id'] 				= $request->party_master;
	        $data['permission_type_id'] 	= $pdata[0];
	        if(($data['required_files']= $doc_name)!=null)
	        	{
	        		// dd($doc_name);
	        		$data['required_files']= $doc_name;
	        	}else{
	        		$data['required_files']= 'null';
	        	}
	        if(!empty($request->location1))
	        {
	        	$data['location_id']  = strip_tags($request->location1);
	        }
	        else
	        {
	            $data['location_id']  = '0';
	        }
	        $data['Other_location'] 		= $request->other;
	        if(($data['Other_location'])!=null){$data['Other_location']= $request->other;}else{$data['Other_location']= 'NULL';}
	        $data['latitude'] 				= 'NULL';
	        $data['longitude'] 				= 'NULL';
			$timestamp = date('Y-m-d H:i:s', strtotime($request->start));
			$timestamp1 = date('Y-m-d H:i:s', strtotime($request->end));
			$data['date_time_start'] 		= $timestamp;
	        $data['date_time_end'] 			= $timestamp1;
	        $data['assigned_police_st_id'] 	= $request->police_station;
	        $data['draft_status'] 			= '0';
	        $data['approved_status'] 		= '0';
	        $data['permission_mode']		= '1';
	        $data['added_at']				= Carbon::now();
	        $data['created_at'] 			= Carbon::now();
	        $data['updated_at'] 			= Carbon::now();
	        $data['created_by'] 			= $request->userid;
	        $data['updated_by'] 			= $request->userid;

	        // update permission_request_status field in user_login table
	        $update_request_status=DB::table('user_login')->where('id', $request->userid)->update(['permission_request_status' => '1']);
	        $res=$permission->create($data);
                $LastInsertId= $res->id;
                if(!empty($LastInsertId) && $LastInsertId != '')
                {
                    $data1=DB::table('permission_type')
                            ->select('permission_type.authority_type_id')
                            ->where('id',$request->permission_type)
                            ->get()->toArray();
                    $nodalid= explode(',', $data1[0]->authority_type_id);

                    if($request->ac == 0 || $request->ac == null)
                    {
                    	$nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code', $request->state)
                                            ->where('dist_no', $request->district)
                                            ->where('pc_no', $request->PC)
                                            ->whereIn('auth_type_id', $nodalid)
                                            ->get()->toArray();
                        $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('a.st_code', $request->state)
                                            ->where('b.dist_no', $request->district)
                                            ->where('b.pc_no', $request->PC)
											->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $nodalid)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                    }else
                    {
                    	$nodaldetails1 = DB::table('authority_masters as a')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code',  $request->state)
                                            ->where('dist_no', $request->district)
                                            ->where('pc_no', $request->PC)
                                            ->where('ac_no', $request->ac)
                                            ->whereIn('auth_type_id', $nodalid)
                                            ->get()->toArray();
                        $nodaldetails2 = DB::table('authority_masters as a')
                                            ->leftjoin('authority_masters_mapping as b','a.id','=','b.authority_masters_id')
                                            ->select('a.id', 'a.name')
                                            ->where('st_code',  $request->state)
                                            ->where('b.dist_no', $request->district)
                                            ->where('b.pc_no', $request->PC)
                                            ->where('b.ac_no', $request->ac)
											->where('b.is_active',1)
                                            ->whereIn('b.auth_type_id', $nodalid)
                                            ->groupBy('b.authority_masters_id')
                                            ->get()->toArray();
                    }
                    $nodaldetails = array_merge($nodaldetails1,$nodaldetails2);

                    // dd($nodaldetails);
                    
                    if(!empty($nodaldetails))
                    {
                    	// dd($nodaldetails);
	                    for($i=0; $i<=count($nodaldetails); $i++)
	                    {
	                        if(!empty($nodaldetails[$i]) && $nodaldetails[$i]!='')
	                        {

	                        	$today=explode(' ',Carbon::today());
	                        	$nodaldata=array('permission_request_id'=>$LastInsertId,'authority_id'=>$nodaldetails[$i]->id,'accept_status'=>0,'added_at'=>$today[0],'created_at'=>Carbon::now(),'updated_at'=>Carbon::now());
	                        	$insert=DB::table('permission_assigned_auth')->insert($nodaldata);
	                        	// Push Notification
	                        	$type='Nodal';
	                        	$fcm_id = DB::table('authority_login')->select('fcm_id')->where('authority_id', $nodaldetails[$i]->id)->first();
	                        	$state = DB::table('m_state')->where('ST_CODE',$request->state)->select('ST_NAME')->first();
	                        	$district = DB::table('m_district')->where('ST_CODE',$request->state)->where('DIST_NO',$request->district)->select('DIST_NAME')->first();
	                     
                                if (!empty($state) && !empty($district)) 
                                {
                                	// echo $state->ST_NAME.'<br>'.$district->DIST_NAME;die;
                                	$msg = 'New permission recived has been recived at ' . Carbon::now() . ' From ' . $district->DIST_NAME . ',' . $state->ST_NAME;
                                }
                                if (!empty($fcm_id)) 
                                {
                                
                                	SendNotification::send_notification_fcm('New Permission Recived', $msg, $fcm_id->fcm_id, $type, $nodaldetails[$i]->id);
                                }
	                        	// Push Notification End
	                        }
	                    }
                	}
                     //dd($request->location1);
                if($request->location1 == 'other' || $request->location1 == null)
                {
                	if($request->ac == null ||$request->ac == 0){
                		$detaildata =DB::table('permission_request')
                  		->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('dist_pc_mapping as PC',function($join){$join->on('PC.PC_NO','=','permission_request.pc_no')->on('PC.DIST_NO','=','permission_request.dist_no')->on('PC.ST_CODE','=','permission_request.st_code');})
                    	->where('permission_request.id','=',$LastInsertId)
                    	->select('PC.PC_NAME_EN','district.DIST_NAME','m_state.ST_NAME','m.permission_name','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','user_data.address','permission_request.id','permission_request.date_time_start','permission_request.date_time_end','.permission_request.Other_location','permission_request.id',
                    		'permission_request.location_id')
                    	->groupBy('PC.PC_NAME_EN')
                    	->get();

                    	// dd($detaildata);

                	}else{
                	$detaildata =DB::table('permission_request')
                  		->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where('permission_request.id','=',$LastInsertId)
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','m.permission_name','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','user_data.address','permission_request.id','permission_request.date_time_start','permission_request.date_time_end','.permission_request.Other_location','permission_request.id',
                    		'permission_request.location_id')
                    	->get();
                    }
                  // dd($detaildata);  	
                return view('politicalparty.receipt',compact('detaildata'));
            }else{
            	if($request->ac == null ||$request->ac == 0){
                		$detaildata =DB::table('permission_request')
                  		->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('dist_pc_mapping as PC',function($join){$join->on('PC.PC_NO','=','permission_request.pc_no')->on('PC.DIST_NO','=','permission_request.dist_no')->on('PC.ST_CODE','=','permission_request.st_code');})
                    	->where('permission_request.id','=',$LastInsertId)
                    	->select('PC.PC_NAME_EN','district.DIST_NAME','m_state.ST_NAME','m.permission_name','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','user_data.address','permission_request.id','permission_request.date_time_start','permission_request.date_time_end','.permission_request.Other_location','permission_request.id',
                    		'permission_request.location_id')
                    	->groupBy('PC.PC_NAME_EN')
                    	->get();

                    	// dd($detaildata);

                	}else{
            			$detaildata =DB::table('permission_request')
	                  		->join('user_data','user_data.user_login_id','=','permission_request.user_id')
	                    	->join('location_master','location_master.id','=','permission_request.location_id')
	                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
	                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
	                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
	                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
	                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
	                    	->where('permission_request.id','=',$LastInsertId)
	                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','m.permission_name','location_master.location_name','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','user_data.address','permission_request.id','permission_request.date_time_start','permission_request.date_time_end','.permission_request.Other_location','permission_request.id','permission_request.location_id')
	                    	->get();
                    }
                   //dd($detaildata);  	
                return view('politicalparty.receipt',compact('detaildata'));
            }
            	
            }
           }
           }else{
           		return Redirect::back();
           }	   
	}
    public function getSelectDetails(Request $request)
        {
        Auth::guard('web');
        if(Auth::check()){
        $users=Session::get('login_details');
        $user = Auth()->user();
		$userid=$user->id;
		$mobile=$user->mobile;
		
		$res=DB::table('user_data')->where('mobileno',$mobile)->where('user_login_id',$userid)->get();
                $stcode=$res[0]->state_id;
//                echo $request->permsn_id;die;
 //              echo $stcode;die;
               // print_r($res);die;
            if(!empty($request->permsn_id))
            {
                $getPermissionDetails=DB::table('permission_required_doc')
                        ->select('*')->where('permission_id',$request->permsn_id)->where('st_code',$stcode)->get()->toArray();
                if(!empty($getPermissionDetails))
                {
                  return $getPermissionDetails;
                }
                else 
                {
                    return '0';
                }
            }
            }else{
            	return Redirect::back();
            }
        }
    public function detaildata($data)
	{

		$d = session()->all();
		$number=$d['userID'];
		$r=getUserDetails($d['userID']);
		// $id=$r->id;

		$detaildata =DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')->join('location_master','location_master.id','=','permission_request.location_id')->join('permission_request_comment','permission_request_comment.permission_request_id','=','permission_request.id')->join('permission_type','permission_type.id','=','permission_request.permission_type_id')->where('permission_request.id','=',$data)->get();
		return $detaildata;

		
	} 
	
	// Controller Add By Divya
    public function getlatlongs(Request $request)
        { 
        $locationid = $request->input('locationid');
		$locationdetails = DB::table('location_master')->where('id',$locationid)  
		->get(); 
		return json_encode($locationdetails);
	    }
	public function getlocationList(Request $request)
        {
        $state = $request->input('stcode');
		$ac = $request->input('ac'); 
        $getACLists = DB::table('location_master')->orderBy('location_name','DESC')->where('ST_CODE',$state)  
		->where('AC_NO', '=', $ac)
		->get(); 
		return json_encode($getACLists);
	    }
	public function roletype()
	{
		Auth::guard('web');
        if(Auth::check()){
		$role =getallpartylist();
		// dd($role);
		$role_type = DB::table('user_role')->where('role_level','2')->select('role_id','role_name')->get();
		// dd($role_type);
		return view('politicalparty.RoleType',compact('role_type','role'));
		}else{
			return Redirect::back();
		}
	}
	public function getDistrictsval(Request $request)
   {
   //print_r($_REQUEST);  exit;
        $state = $request->input('stcode');
      $getDistricts = DB::table('m_district')->orderBy('DIST_NAME')->where('ST_CODE',$state)->get();
       return $getDistricts;
   }
   public function getACListsval(Request $request)
   {
        $state = $request->input('stcode');
        $district = $request->input('district');
       $getACList = DB::table('m_ac')->orderBy('AC_NAME')->where('ST_CODE',$state)
        ->where('DIST_NO_HDQTR', '=', $district)
        ->get();
       return json_encode($getACList);
   }
	
	public function profile()
	{
		Auth::guard('web');
        if(Auth::check()){
		$users=Session::get('login_details');
        $user = Auth()->user();
		$userid=$user->id;
		$mobile=$user->mobile;
		$party=$user->party_id;
		$party_name=DB::table('m_party')->where('CCODE',$party)->select('CCODE','PARTYNAME')->get();
		if(count($party_name)>0)
		{
			return view('politicalparty.profile',['getStates' => States::orderBy('ST_NAME')->get(),'getDistricts' => Districts::all(),'getAclist' => AC::all(),'mobile'=>$mobile,'user_login_id'=>$userid,'party_master'=>$party_name]);
		}else{
			return Redirect::to('/roletype');
		}
		}else{
			return Redirect::back();
		}
		
	}
	public function addprofile(Request $request)
	{
		Auth::guard('web');
        if(Auth::check()){
		$users=Session::get('login_details');
		$user = Auth()->user();
		$mobile=$user->mobile;
		// dd($mobile);
		$data = $request->all();
		$validator = Validator::make($data, [
                'name'				=>'required|max:100|regex:/^[a-zA-Z. ]+$/u',
				'father_name' 	    =>'required|max:100|regex:/^[a-zA-Z. ]+$/u',
				'email'				=>'required|email|max:100',
				'gender'			=>'required',
				'state'         	=>'required|max:100|not_in:0',
				'district'     	    =>'required|max:100|not_in:0',
				'ac'              	=>'required|max:100|not_in:0',
				'mobile'          	=>'required|numeric|min:10|regex:/[0-9]{10}/',
				'dob' 				=>'required|date|before:2000-01-01',
				'Address1'			=>'required|max:200'
           ]);
           if ($validator->fails()) {
              return Redirect::back()
              ->withErrors($validator)
              ->withInput();
           	}else{
           		$users=Session::get('login_details');
		        $user = Auth()->user();
				// dd($request);
				$mobile=$user->mobile;

           		$permission = new User_dataModel;
           		$data['user_login_id']		= $request->user_login_id;
		        $data['name'] 				= $request->name;
		        $data['fathers_name']		= $request->father_name;
		        $data['email'] 				= $request->email;
		        $data['mobileno'] 			= $request->mobile;
		        $data['gender'] 			= $request->gender;
	            $data['epic_no']         	= 'NULL';
		        $data['part_no'] 			= '0';
		        $data['slno'] 				= '0';
		        $data['dob'] 				= $request->dob;
		        $data['party_id']			= $request->party_master;
		        $data['address'] 			= $request->Address1;
		        $data['state_id'] 			= $request->state;
		        $data['district_id'] 		= $request->district;
		        $data['ac_id'] 				= $request->ac;
		        $data['religion'] 			= '0';
		        $data['caste'] 				= '0';
		        $data['mark_as_delete'] 	= '0';
		        // dd($data);
		       $updateDetails=array(
				    'registration_type' => '1',
				    'login_access'=>	'1',
				    'email'	=>$request->email,
				    'created_at' =>Carbon::now()
				);

		        try
		        {
		        	DB::beginTransaction();
		        	 $res=$permission->create($data);
				        $id = DB::getPdo()->lastInsertId();
				        $updatelogin=DB::table('user_login')->where('mobile', $mobile)->update($updateDetails);
				    DB::commit();
				        
		        }catch (Exception $e) {
		        		DB::rollBack();
        				return $e;
		        }
		       return Redirect::to('/permission')->with('msg','Profile Successfully Saved!');
		        // return Redirect::to('/permission')->with('msg','Profile Successfully Saved!');
			}
			}else{
				return Redirect::back();
			}
	}
public function updateprofile()
	{
		$data = [];
		$data['states'] 		= [];
		$data['districts']		= [];
		$data['acs']			= [];


		Auth::guard('web');
        if(!Auth::check()){
        	return Redirect::back();
        }

		$users=Session::get('login_details');
		$user = Auth()->user();
		$mobile=$user->mobile;
		$id=$user->id;

		$states = DB::table('m_state')->select('ST_CODE','ST_NAME','CCODE')->get();
		foreach ($states as $key => $value) {
			$data['states'][] = [
				'id'	=>$value->CCODE,
				'code' 	=> $value->ST_CODE,
				'name' 	=>  $value->ST_NAME
			];
		}

		$districts = DB::table('m_district')->select('row_id','ST_CODE','DIST_NO','DIST_NAME')->get();
		foreach ($districts as $key => $value) {
			$data['districts'][] = [
				'id' 		=> 	$value->row_id,
				'st_code'	=>	$value->ST_CODE,
				'code'		=>  $value->DIST_NO,
				'name'		=>	$value->DIST_NAME
			];
		}
		$acs = DB::table('m_ac')->select('id','ST_CODE','AC_NO','AC_NAME','DIST_NO_HDQTR')->get();
		foreach ($acs as $key => $value) {
			$data['acs'][] = [
				'id' 		=> 	$value->id,
				'st_code'	=>	$value->ST_CODE,
				'code'		=>	$value->AC_NO,
				'dist_code'	=>  $value->DIST_NO_HDQTR,
				'name'		=>	$value->AC_NAME
			];
		}

		// dd($data['acs']);
		// put applicant type in session
		$userrole=$user->role_id;
		$type=DB::table('user_role')->where('role_id',$userrole)->select('role_name')->get();
		$role_type=$type[0]->role_name;
		Session::put('Applicant_type', $role_type);
		// end applicant type in session
		// dd($id);
		$user = DB::table('user_data')
			->join('m_party','m_party.CCODE','user_data.party_id')
			->join('user_login','user_login.mobile','user_data.mobileno')
			->select('user_data.name','user_data.fathers_name','user_data.email','user_data.gender','user_data.mobileno','user_data.dob','user_data.address','user_login.permission_request_status','user_data.user_login_id','m_party.CCODE','m_party.PARTYNAME','user_data.state_id','user_data.district_id','user_data.ac_id')
			->where('mobileno',$mobile)->where('user_login_id',$id)->first(); 

		if(!$user){
			return Redirect::to('/profile');
		}

		$data['value'] = $user;

		return view('politicalparty.update_profile', $data);

	}
	
	public function update(Request $request)
	{
		Auth::guard('web');
        if(Auth::check()){
		$data = $request->all();
		$users=Session::get('login_details');
		$user = Auth()->user();
		$mobile=$user->mobile;
		$user_id=$users->id;
		// dd($user_id);
		$data = $request->all();

		$user = DB::table('user_data')
			->join('m_party','m_party.CCODE','user_data.party_id')
			->join('user_login','user_login.mobile','user_data.mobileno')->select('permission_request_status')->where('mobileno',$mobile)->where('user_login_id',Auth::id())->first(); 
		if(!$user){
			return Redirect::back();
		}

		if($user->permission_request_status==1){
			$rules = [
				'Address1'			=>'required|max:200'
           ];
		}else{
			$rules = [
                'name'				=>'required|max:100|regex:/^[a-zA-Z ]+$/u',
				'father_name' 	    =>'required|max:100|regex:/^[a-zA-Z ]+$/u',
				'email'				=>'required|email|max:100',
				'radio_stacked'		=>'required',
				'state'         	=>'required|max:100|not_in:0',
				'district'     	    =>'required|max:100|not_in:0',
				'ac'              	=>'required|max:100|not_in:0',
				'dob' 				=>'required|date',
				'Address1'			=>'required|max:200'
           ];
        }

		$validator = Validator::make($data, $rules);
           if ($validator->fails()) {
              return Redirect::back()
              ->withErrors($validator)
              ->withInput();
           	}else{

        if($user->permission_request_status==1){
        	$updateDetails=array(
        		'email'				=> $request->email,
        		'gender'			=> $request->radio_stacked,
			'dob'				=> $request->dob,
        		'address'			=> $request->Address1,
        		'ac_id'				=> $request->ac
        	);
        	// dd($updateDetails);
        }else{
			$updateDetails=array(
			    	'name' 				=> $request->name,
			    	'fathers_name'      => $request->father_name,
					'email'				=> $request->email,
					'gender'			=> $request->radio_stacked,
					'dob'				=> $request->dob,
					'address'			=> $request->Address1,
					'state_id'			=> $request->state,
					'district_id'		=> $request->district,
					'ac_id'				=> $request->ac,
					'party_id'			=> $request->party_master
			);
		}
		// dd($updateDetails);

		$update=DB::table('user_data')->where('user_login_id',$user_id)->where('mobileno',$mobile)->update($updateDetails);
		return redirect()->back()->with('message', 'Updated Successfully!');
		}
		}else{
			return Redirect::back();
		}
	}

	public function Privacy()
	{
		return view('politicalparty.Privacy_Policy');
	}
	public function Content()
	{
		return view('politicalparty.Content_Copyright');
	}
	public function Terms()
	{
		return view('politicalparty.Terms_Condition');
	}
	public function Abbreviations()
	{
		return view('politicalparty.Abbreviations');
	}

	public function getpermissiondetails($id,$status,$location)
	{
		Auth::guard('web');
        if(Auth::check()){
		$users=Session::get('login_details');
		$user = Auth()->user();
		$mobile=$user->mobile;
		$user_id=$users->id;
		// array define//
		// $data['states']		= 	[];
		// $data['districts']	=	[];
		// $data['acs']		=	[];
		// $data['pcs']		=	[];
			
		// $states= DB::table('m_state')->get();


			$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('location_master','location_master.id','=','permission_request.location_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.location_id'=>$location,'permission_request.user_id'=>$user_id,])
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','location_master.location_name','location_master.location_details','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.cancel_status','permission_request.location_id','permission_request.ac_no')
                    	->get();
                    	// && $data->cancel_status != 1
           	$pdf=DB::table('permission_request_comment')->where('permission_request_id',$id)->get();
            // dd($pdf);       	
		if($status == 0 )
		{
			if($location == 'other' || $location == '0')
			{
				$result=DB::table('permission_request')->where('id',$id)->get();
				$ac 			= $result[0]->ac_no;
				// dd($ac);
				if($ac == null || $ac == 0){
						$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
	                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
	                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
	                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
	                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
	                    	->join('dist_pc_mapping as PC',function($join){$join->on('PC.PC_NO','=','permission_request.pc_no')->on('PC.DIST_NO','=','permission_request.dist_no')->on('PC.ST_CODE','=','permission_request.st_code');})
	                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
	                    	->select('PC.PC_NAME_EN','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.cancel_status','permission_request.location_id','permission_request.ac_no')
	                    	->groupBy('PC.PC_NO')
	                    	->get();
	                    	// dd($result);    	
	                return view('politicalparty.getpermissiondetail',compact('result','pdf'));
				}else{
					$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.location_id','permission_request.ac_no','permission_request.cancel_status')
                    	->get();    	
                return view('politicalparty.getpermissiondetail',compact('result','pdf'));
				}
			}else{
				// dd($result);
				return view('politicalparty.getpermissiondetail',compact('result','pdf'));
			}
		}else if($status == 2)
		{
			if($location == 'other' || $location == '0')
			{
				$result=DB::table('permission_request')->where('id',$id)->get();
				$ac 			= $result[0]->ac_no;
				// dd($ac);
				if($ac == null || $ac == 0){
						$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
	                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
	                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
	                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
	                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
	                    	->join('dist_pc_mapping as PC',function($join){$join->on('PC.PC_NO','=','permission_request.pc_no')->on('PC.DIST_NO','=','permission_request.dist_no')->on('PC.ST_CODE','=','permission_request.st_code');})
	                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
	                    	->select('PC.PC_NAME_EN','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.location_id','permission_request.ac_no','permission_request.cancel_status')
	                    	->groupBy('PC.PC_NO')
	                    	->get();
	                    	// dd($result);    	
	                return view('politicalparty.getpermissiondetail',compact('result','pdf'));
				}else{
				$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','m.permission_name','permission_request.location_id','permission_request.ac_no','permission_request.cancel_status')
                    	->get();
                return view('politicalparty.getpermissiondetail',compact('result','pdf'));
            	}
			}else{
				return view('politicalparty.getpermissiondetail',compact('result','pdf'));
			}
		}else
		{
			if($location == 'other' || $location == '0')
			{
				$result=DB::table('permission_request')->where('id',$id)->get();
				$ac 			= $result[0]->ac_no;
				// dd($ac);
				if($ac == null || $ac == 0){
						$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
	                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
	                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
	                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
	                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
	                    	->join('dist_pc_mapping as PC',function($join){$join->on('PC.PC_NO','=','permission_request.pc_no')->on('PC.DIST_NO','=','permission_request.dist_no')->on('PC.ST_CODE','=','permission_request.st_code');})
	                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
	                    	->select('PC.PC_NAME_EN','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.location_id','permission_request.ac_no','permission_request.cancel_status')
	                    	->groupBy('PC.PC_NO')
	                    	->get();
	                    	// dd($result);    	
	                return view('politicalparty.getpermissiondetail',compact('result','pdf'));
				}else{
				$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','m.permission_name','permission_request.location_id','permission_request.ac_no','permission_request.cancel_status')
                    	->get();
                return view('politicalparty.getpermissiondetail',compact('result','pdf'));
            	}
			}else{
				return view('politicalparty.getpermissiondetail',compact('result','pdf'));
			}
		}
		}else
		{
			return Redirect::back();
		}
	}

	public function permissiondistrict($st)
	{
		// get district
		$dist=DB::table('m_district')->where('ST_CODE',$st)->orderBy('DIST_NAME', 'ASC')->get();
		return $dist;
	}
	
	public function policeAC($stateID,$acID)
	{
		$police=DB::table('police_station_master')->where('ST_CODE',$stateID)->where('ac_no',$acID)->orderBy('police_st_name')->get();
		// dd($police);
		return $police;
	}	

	// for download permission
	public function downloadprint($status,$id,$location)
	{
		// dd($location);
		Auth::guard('web');
        if(Auth::check()){
		 // dd($id.' '.$status.' '.$location);
		$users=Session::get('login_details');
		$user = Auth()->user();
		$mobile=$user->mobile;
		$user_id=$users->id;
		
		$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('location_master','location_master.id','=','permission_request.location_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.location_id'=>$location,'permission_request.user_id'=>$user_id,])
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','location_master.location_name','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.ac_no','permission_request.cancel_status')
                    	->get();
           	$pdf=DB::table('permission_request_comment')->where('permission_request_id',$id)->get();
            // dd($result);       	
		if($status == 0 )
		{
			if($location == 'other' || $location == '0')
			{
				$result=DB::table('permission_request')->where('id',$id)->get();
				$ac 			= $result[0]->ac_no;
				// dd($ac);
				if($ac == null || $ac == 0)
				{
					$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('dist_pc_mapping as PC',function($join){$join->on('PC.PC_NO','=','permission_request.pc_no')->on('PC.DIST_NO','=','permission_request.dist_no')->on('PC.ST_CODE','=','permission_request.st_code');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('PC.PC_NAME_EN','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.ac_no','permission_request.cancel_status')
                    	->groupBy('PC.PC_NO')
                    	->get();
                    	$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
				}else{
					$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.ac_no','permission_request.cancel_status')
                    	->get();
                    	$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
                
				}
				
			}else{
				$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
			}
		}if($status == 1 )
		{
			if($location == 'other' || $location == '0')
			{
				$result=DB::table('permission_request')->where('id',$id)->get();
				$ac 			= $result[0]->ac_no;
				// dd($ac);
				if($ac == null || $ac == 0)
				{
					$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('dist_pc_mapping as PC',function($join){$join->on('PC.PC_NO','=','permission_request.pc_no')->on('PC.DIST_NO','=','permission_request.dist_no')->on('PC.ST_CODE','=','permission_request.st_code');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('PC.PC_NAME_EN','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.ac_no','permission_request.cancel_status')
                    	->groupBy('PC.PC_NO')
                    	->get();
                    	$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
				}else{
				$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.ac_no','permission_request.cancel_status')
                    	->get();
                    	$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
                }
			}else{
				$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
			}
		}
		else if($status == 2)
		{
			if($location == 'other' || $location == '0')
			{
				$result=DB::table('permission_request')->where('id',$id)->get();
				$ac 			= $result[0]->ac_no;
				// dd($ac);
				if($ac == null || $ac == 0)
				{
					$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('dist_pc_mapping as PC',function($join){$join->on('PC.PC_NO','=','permission_request.pc_no')->on('PC.DIST_NO','=','permission_request.dist_no')->on('PC.ST_CODE','=','permission_request.st_code');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('PC.PC_NAME_EN','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.ac_no','permission_request.cancel_status')
                    	->groupBy('PC.PC_NO')
                    	->get();
                    	$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
				}else{
				$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','m.permission_name','permission_request.ac_no','permission_request.cancel_status')
                    	->get();
                       $downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
                }
			}else{
				$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
			}
		}else
		{
			if($location == 'other' || $location == '0')
			{
				$result=DB::table('permission_request')->where('id',$id)->get();
				$ac 			= $result[0]->ac_no;
				// dd($ac);
				if($ac == null || $ac == 0)
				{
					$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('dist_pc_mapping as PC',function($join){$join->on('PC.PC_NO','=','permission_request.pc_no')->on('PC.DIST_NO','=','permission_request.dist_no')->on('PC.ST_CODE','=','permission_request.st_code');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('PC.PC_NAME_EN','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','m.permission_name','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','permission_request.ac_no','permission_request.cancel_status')
                    	->groupBy('PC.PC_NO')
                    	->get();
                    	$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
				}else{
				$result=DB::table('permission_request')->join('user_data','user_data.user_login_id','=','permission_request.user_id')
                    	->join('permission_type','permission_type.id','=','permission_request.permission_type_id')
                    	->join('m_state','m_state.ST_CODE','=','permission_request.st_code')
                    	->join('permission_master as m','m.id','=','permission_type.permission_type_id')
                    	->join('m_district as district',function($join){$join->on('district.DIST_NO','=','permission_request.dist_no')->on('district.ST_CODE','=','permission_request.st_code');})
                    	->join('m_ac as ac',function($join){$join->on('ac.AC_NO','=','permission_request.ac_no')->on('ac.ST_CODE','=','permission_request.st_code')->on('ac.DIST_NO_HDQTR','=','permission_request.dist_no');})
                    	->where(['permission_request.approved_status'=>$status,'permission_request.id'=>$id,'permission_request.user_id'=>$user_id,])
                    	->select('ac.AC_NAME','district.DIST_NAME','m_state.ST_NAME','user_data.name','user_data.email','user_data.mobileno','user_data.gender','user_data.dob','permission_request.id as permission','permission_request.date_time_start','permission_request.date_time_end','permission_request.Other_location','permission_request.id','permission_request.required_files','permission_request.st_code','permission_request.permission_mode','permission_request.approved_status','m.permission_name','permission_request.ac_no','permission_request.cancel_status')
                    	->get();
                $downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
                }
			}else{
				$downloadpdf = PDF::loadView('politicalparty.printpermission',compact('result','pdf'));
                    	return $downloadpdf->download('permission.pdf');
			}
		}
		}else
		{
			return Redirect::back();
		}
	}

	// find election date
	 public function getpconac($sid,$acid,$districtID)
	 {
             if($acid != 0)
             {
	 	$findpc=DB::table('m_ac')->where([['ST_CODE',$sid],['AC_NO',$acid]])->get();
	 	$pc_id=$findpc[0]->PC_NO;
	 	$s_id = $findpc[0]->ST_CODE;
	 	$type = 'PC';
	 	$schedule = DB::table('m_election_details')->where([['CONST_NO',$pc_id],['ST_CODE',$s_id],['CONST_TYPE',$type]])->get();
             }
            else {
                $schedule = DB::table('m_election_details as a')
                                ->join('m_ac as g',function ($join){
                                $join->on('g.AC_NO','=','a.CONST_NO')
                                     ->on('g.ST_CODE', '=', 'a.ST_CODE');
                            })
                            ->join('m_schedule as m',function ($join){
                                $join->on('m.SCHEDULEID','=','a.ScheduleID');
                            })
                            ->where([['m.DATE_POLL','>',Carbon::now()->format('Y-m-d')],['g.DIST_NO_HDQTR',$districtID],['a.ST_CODE',$sid],['a.CONST_TYPE',$type]])->get();
            }
             $sechedule_id = $schedule[0]->ScheduleID;
	 	$pollday = DB::table('m_schedule')->where([['SCHEDULEID',$sechedule_id]])->get();
	 	$poll_day= GetReadableDate($pollday[0]->DATE_POLL);
	 	return $poll_day;
	 	
	 } 
	 // getpollday
	 public function getpollday($sid,$pcid)
	 {
	 	$type = 'PC';
	 	$schedule = DB::table('m_election_details')->where([['CONST_NO',$pcid],['ST_CODE',$sid],['CONST_TYPE',$type]])->get();
	 	$sechedule_id = $schedule[0]->ScheduleID;
	 	$pollday = DB::table('m_schedule')->where([['SCHEDULEID',$sechedule_id]])->get();
	 	$poll_day= GetReadableDate($pollday[0]->DATE_POLL);
	 	return $poll_day;

	 }
	 // get PC Detail
	 public function permissionpc($sid)
	 {
	 	$getpc=DB::table('m_pc')->where('ST_CODE',$sid)->get();
	 	return $getpc;
	 }

	 // get PC Name on the base of pc
	 public function getpcname($stateID,$districtID)
	 {

	 	// $findpc=DB::table('m_ac')->where([['ST_CODE',$sid],['AC_NO',$acid]])->get();
	 	// $pc_id=$findpc[0]->PC_NO;
	 	// $s_id = $findpc[0]->ST_CODE;
	 	// $ac_id = $findpc[0]->AC_NO;
	 	$pcdetail = DB::table('dist_pc_mapping')->where('ST_CODE',$stateID)->where('DIST_NO',$districtID)->groupBy('PC_NO')->get();
	 	return $pcdetail;
	 }
	 // find ac on pc
	 public function permissionAC($stateID,$districtID,$pc)
	 {
			
		$acdata=DB::table('m_ac')->where('ST_CODE',$stateID)->where('DIST_NO_HDQTR',$districtID)->where('PC_NO',$pc)->orderBy('AC_NAME')->get();
		return $acdata;
	}

	public function statedatevalidation($StateId)
	{

		$data = DB::table('restriction_master')->where('st_code',$StateId)->select('st_code','restriction_status')->get();
		return $data;

	}

		
}