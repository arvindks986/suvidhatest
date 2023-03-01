<?php  
    namespace App\Http\Controllers\Admin;
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
    use MPDF;
    use App\commonModel;
     
    use App\Helpers\SmsgatewayHelper;
     
    use App\Classes\xssClean;
    use App\adminmodel\SymbolMaster;
    use Illuminate\Support\Facades\Crypt;
        use App\adminmodel\Pollday;
 
class PCCeoturnoutController extends Controller
{
    //
    public function __construct(){
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        
    
    }
 
  protected function guard(){
        return Auth::guard('admin');
      }

    public function index()
      {     
      if(Auth::check()){
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);

           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
           $newdetails = DB::table('pd_scheduledetail')
                     ->select('st_code','scheduleid','end_of_poll_finalize')
                     ->where('st_code', '=', $d->st_code)
                     ->groupBy('scheduleid')
                     ->get();
          
          return view('admin.pc.ceo.end-of-poll-finalize', ['user_data' => $d,'ele_details'=>$ele_details,'newdetails'=>$newdetails]);            
          }
          else {
                return redirect('/officer-login');
              }
      }  // end index function
 
    public function veryfyend_of_poll_finalize(Request $request)
            {
            if(Auth::check()){
               $user = Auth::user();
                 $d=$this->commonModel->getunewserbyuserid($user->id);
                  $validator = \Validator::make(
                $request->all(), 
                    [
                       
                       'phasenumber' => 'required|digits_between:1,7',
                       ],
                    [
                    'phasenumber.required' => 'Please enter voter',
                    'phasenumber.digits_between' => 'Please enter valid value',
                    ]);
        
              if($validator->fails()){
                return \Redirect::back()->withInput($request->all())->withErrors($validator);
              }
                 $phasenumber=$request->input('phasenumber'); 

                 $st = array('updated_at'=>date("Y-m-d H:i:s"),'updated_at_finalize'=>date("Y-m-d H:i:s"),'added_update_at'=>date("Y-m-d"),'updated_by'=>$d->officername,'end_of_poll_finalize'=>'1'); 
                 
                $i = DB::table('pd_scheduledetail')->where('st_code', $d->st_code)->where('scheduleid', $phasenumber)->update($st);
                \Session::flash('success_mes', 'Voter Turnout finalize successfully');
                 return Redirect::to('pcceo/end-of-poll-finalize');        
            }
            else {
                  return redirect('/officer-login');
                }
      }  // end index function
}  // end class  //accepted_candidate  
