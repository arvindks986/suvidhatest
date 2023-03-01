<?php
	namespace App\Http\Controllers\Admin\feedback;
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Validator;
	use App\Admin;
	use Session;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Support\Facades\Hash;
    use Carbon\Carbon;
    use DB;
	use App\commonModel;
    use Config;
    use \PDF;
    use Excel;
    use Mail;
    use App\adminmodel\SurveyMaster;
	use App\adminmodel\SurveyResponse;
	use App\adminmodel\SurveyResponseNew;
	
	class FeedbackController extends Controller
	{
		/**
			* Create a new controller instance.
			*
			* @return void
		*/
		public function __construct()
        {
			$this->middleware(['auth:admin','auth']);
			$this->commonModel = new commonModel();
		}
		
		
		
		public function index(Request $request)
		{  
			$apps = SurveyMaster::select('id','name')->where('level','=',1)->get();
			$users=Session::get('admin_login_details');
			//print_r(Auth::user());die;
			$user = Auth::user()->id;
			$udesig = Auth::user()->designation;
			if(Auth::user()->st_code)
			$ustate = trim($this->commonModel->getstatebystatecode(Auth::user()->st_code)->ST_NAME);
			else
				$ustate = "";
			$uplace = Auth::user()->placename;
			$uname = Auth::user()->name;
			
			$formtype=1;
			$apid=0;
			$mdid=0;
			$status=0;
			$mods = SurveyMaster::select('id','name')->where('level','=',2)->where('parent','=',1)->get();
			return view('admin.feedback.feedback')->with(compact('formtype','apps','mods','apid','mdid','status','user','uname','udesig','ustate','uplace'));
		}
		
		public function ajaxGetModule(Request $request)
		{  
			$modules= SurveyMaster::select('id','name')->where('level','=',2)->where('parent','=',$request->appid)->get();
			$msg="";
			foreach($modules as $module)
			{
				$msg.=$module->id."||".$module->name."##";
			}
			return response()->json(array('msg'=> $msg), 200);
		}
		
		
		//Route::post('/ajaxGetFdbk', 'admin\feedback\FeedbackController@ajaxGetForm');
		public function commonResponse(Request $request)
		{  
			$reqlist = $request->all();
			$apid=0;
			$mdid=0;
			$users=Session::get('admin_login_details');
			$stime = now();
			$user = Auth::user()->id;
			$udesig = Auth::user()->designation;
			if(Auth::user()->st_code)
			$ustate = trim($this->commonModel->getstatebystatecode(Auth::user()->st_code)->ST_NAME);
			else
				$ustate = "";
			$uplace = Auth::user()->placename;
			$uname = Auth::user()->name;
			$olvl = Auth::user()->officerlevel;
			$apps = SurveyMaster::select('id','name')->where('level','=',1)->get();
			$mods = SurveyMaster::select('id','name')->where('parent','=',$request->appid)->get();
			//print_r($reqlist); die;
			if($request->formtype==1)
			{
				$apid=$request->appid;
				$mdid=$request->moduleid;
				$usrid = $user;
				$status=0;
				if($request->appid <6)
				{
					$srec = SurveyResponse::where('app_id', $request->appid)->where('module_id', $request->moduleid)->where('officer_id', $user)->first();
					
					$formtype=2;
					if($srec)
					{
						$q1 = $srec->q1;
						$q2 = $srec->q2;
						$q3 = $srec->q3;
						$q4 = $srec->q4;
						$q5 = $srec->q5;
						$q61 = $srec->q6_1;
						$q62 = $srec->q6_2;
						$q63 = $srec->q6_3;
						$q64 = $srec->q6_4;
						$q65 = $srec->q6_5;
						$q7 = $srec->q7;
						$stime = $srec->created_at;
						$orec=1;
					}
					else
					{
						$q1 = 1;
						$q2 = 1;
						$q3 = 1;
						$q4 = "";
						$q5 = "";
						$q61 = 0;
						$q62 = 0;
						$q63 = 0;
						$q64 = 0;
						$q65 = 0;
						$q7 = "";
						
						$orec=0;
					}
					return view('admin.feedback.feedback')->with(compact('formtype','apps','mods','orec','apid','mdid','usrid','q1','q2','q3','q4','q5','q61','q62','q63','q64','q65','q7','status','uname','udesig','ustate','uplace','stime'));
				}
				else
				{
					$srec = SurveyResponseNew::where('officer_id', $user)->first();
					
					$formtype=3;
					if($srec)
					{
						$q1 = $srec->q1;
						$q2 = $srec->q2;
						$q3 = $srec->q3;
						$q4 = $srec->q4;
						$q5 = $srec->q5;
						$q6 = $srec->q6;
						$q7 = $srec->q7;
						$q8 = $srec->q8;
						$q9 = $srec->q9;
						$q10 = $srec->q10;
						$q11 = $srec->q11;
						$stime = $srec->created_at;
						$orec=1;
					}
					else
					{
						$q1 = "";
						$q2 = 1;
						$q3 = 0;
						$q4 = "";
						$q5 = 1;
						$q6 = 1;
						$q7 = 1;
						$q8 = 1;
						$q9 = 1;
						$q10 = 1;
						$q11 = 1;
						$stime = "";
						$orec=0;
					}
					return view('admin.feedback.feedback')->with(compact('formtype','apps','mods','orec','apid','mdid','usrid','q1','q2','q3','q4','q5','q6','q7','q8','q9','q10','q11','status','uname','udesig','ustate','uplace','stime'));
				}
				
			}
			elseif($request->formtype==2)
			{
				
				$rules = [
				'q1' => 'required|numeric|digits:1|min:1|max:2',
				'q2' => 'required|numeric|digits:1|min:1|max:2',
				'q3' => 'required|numeric|digits:1|min:1|max:2',
				'q6a' => 'required|numeric|digits:1|min:1|max:5',
				'q6b' => 'required|numeric|digits:1|min:1|max:5',
				'q6c' => 'required|numeric|digits:1|min:1|max:5',
				'q6d' => 'required|numeric|digits:1|min:1|max:5',
				'q6e' => 'required|numeric|digits:1|min:1|max:5',
				
				];
				$messages = [
				'q1' => 'Please select proper value for Question # 1',
				'q2' => 'Please select proper value for Question # 2',
				'q3' => 'Please select proper value for Question # 3',
				'q6a' =>  'Please select proper value for Question # 6.1',
				'q6b' => 'Please select proper value for Question # 6.2',
				'q6c' => 'Please select proper value for Question # 6.3',
				'q6d' => 'Please select proper value for Question # 6.4',
				'q6e' => 'Please select proper value for Question # 6.5',
				
				];
				$validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->passes()) {
					$srp= new SurveyResponse;
					$srp->app_id = $request->appid;
					$srp->module_id = $request->moduleid;
					$srp->officer_id = $user;
					$srp->officerlevel = $olvl;
					$srp->q1 = $request->q1;
					$srp->q2 = $request->q2;
					$srp->q3 = $request->q3;
					$srp->q4 = strip_tags($request->q4);
					$srp->q5 = strip_tags($request->q5);
					$srp->q6_1 = $request->q6a;
					$srp->q6_2 = $request->q6b;
					$srp->q6_3 = $request->q6c;
					$srp->q6_4 = $request->q6d;
					$srp->q6_5 = $request->q6e;
					$srp->q7 = strip_tags($request->q7);
					$srp->save();
					
					$apid = $request->appid;
					$mdid = $request->moduleid;
					$usrid = $user;
					$q1 = $srp->q1;
					$q2 = $srp->q2;
					$q3 = $srp->q3;
					$q4 = $srp->q4;
					$q5 = $srp->q5;
					$q61 = $srp->q6_1;
					$q62 = $srp->q6_2;
					$q63 = $srp->q6_3;
					$q64 = $srp->q6_4;
					$q65 = $srp->q6_5;
					$q7 = $srp->q7;
					$orec=1;
					$status=1;
					$formtype=2;
					return view('admin.feedback.feedback')->with(compact('formtype','apps','mods','orec','apid','mdid','usrid','q1','q2','q3','q4','q5','q61','q62','q63','q64','q65','q7','status','uname','udesig','ustate','uplace','stime'));
				}
				else
				{
					return redirect()->back()->withErrors($validator, 'error')->withInput();
				}
			}
			elseif($request->formtype==3)
			{
				$rules = [
				'q2' => 'required|numeric|digits:1|min:1|max:3',
				'q3' => 'required|numeric|digits:1|min:1|max:5',
				'q5' => 'required|numeric|digits:1|min:1|max:2',
				'q5' => 'required|numeric|digits:1|min:1|max:2',
				'q6' => 'required|numeric|digits:1|min:1|max:2',
				'q7' => 'required|numeric|digits:1|min:1|max:2',
				'q8' => 'required|numeric|digits:1|min:1|max:2',
				'q9' => 'required|numeric|digits:1|min:1|max:2',
				'q10' => 'required|numeric|digits:1|min:1|max:2',
				'q11' => 'required|numeric|digits:1|min:1|max:2',
				];
				$messages = [
				'q5' => 'Please select proper value for Question # 2',
				'q6' => 'Please select proper value for Question # 3',
				'q7' => 'Please select proper value for Question # 4',
				'q8' => 'Please select proper value for Question # 5',
				'q9' => 'Please select proper value for Question # 6',
				'q10' => 'Please select proper value for Question # 7',
				'q11' => 'Please select proper value for Question # 8',
				'q2' => 'Please select proper value for Question # 9',
				'q3' => 'Please select proper value for Question # 10',
				];
				$validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->passes()) {
					$srp= new SurveyResponseNew;
					$srp->officer_id = $user;
					$srp->officerlevel = $olvl;
					$srp->q1 = strip_tags($request->q1);
					$srp->q2 = $request->q2;
					$srp->q3 = $request->q3;
					$srp->q4 = strip_tags($request->q4);
					$srp->q5 = $request->q5;
					$srp->q6 = $request->q6;
					$srp->q7 = $request->q7;
					$srp->q8 = $request->q8;
					$srp->q9 = $request->q9;
					$srp->q10 = $request->q10;
					$srp->q11 = $request->q11;
					$srp->save();
					
					$apid = $request->appid;
					$mdid = $request->moduleid;
					$q1 = $srp->q1;
					$q2 = $srp->q2;
					$q3 = $srp->q3;
					$q4 = $srp->q4;
					$q5 = $srp->q5;
					$q6 = $srp->q6;
					$q7 = $srp->q7;
					$q8 = $srp->q8;
					$q9 = $srp->q9;
					$q10 = $srp->q10;
					$q11 = $srp->q11;
					
					$formtype=3;
					$usrid=$user;
					$orec=1;
					$status=1;
					return view('admin.feedback.feedback')->with(compact('formtype','apps','mods','orec','apid','mdid','usrid','q1','q2','q3','q4','q5','q6','q7','q8','q9','q10','q11','status','uname','udesig','ustate','uplace','stime'));
				}
				else
				{
					return redirect()->back()->withErrors($validator, 'error')->withInput();
				}
			}
			
		}//END commonResponse
	
	
	
	
}//END CLASS