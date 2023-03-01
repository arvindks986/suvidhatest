<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Illuminate\Support\Facades\Hash;
    use Carbon\Carbon;
    use DB;
    use Validator;
    use Config;
    use \PDF;
    use Excel;
    use Mail;
    use App\commonModel;
    
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$request_url = url('/');
		
if ($request_url == 'https://suvidha.eci.gov.in') {
  return redirect('/login');
}else{
  return redirect('/officer-login');
}
        return view('welcome');
    }
    public function login()
            {
				
				
				
                $users=Session::get('login_details');
                $user = Auth::user();
                if(session()->has('user_login')){ 
                            return Redirect::to('/home');
                        }
                  else{  
                       return Redirect::to('/login');
                      } 
                
            }
	 public function userhome(Request $request)
            {  
              $users=Session::get('login_details');
              $user = Auth()->user();
               
              if(session()->has('user_login')){ 
                $getid=$user->id;
                
                if($user->role_id == NULL)
                {
                    return Redirect::to('/roletype');
                }else
                {
                  $result=DB::table('user_login')->get();
                  $re=$result[0]->permission_request_status;
                  // dd($re);
                  if($re == 0)
                  {
                    return Redirect::to('/update profile');
                  }else
                  {
                    return Redirect::to('/create');
                  } 
                }   
            }
            else {    
                 return redirect('/login');
                }  
            }
            public function logout()
            { 
            Auth::logout();
            Session::flush();       
            return Redirect::to('/login');               
           
            }
}
