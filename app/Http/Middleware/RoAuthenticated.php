<?php
namespace App\Http\Middleware;
use Session;
use Closure;
use Illuminate\Support\Facades\Auth;

class RoAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {      
        $role=Session::get('admin_login_details')->role_id;
          
        //if ( $role == 18 || $role == 19 || $role == 17 || $role == 20 || $role == 21  || $role == 22) {
          if ( $role == 18 || $role == 19 || $role == 17 || $role == 20 || $role == 21  || $role == 22) {  
            $response = $next($request);
            $headers = [
                'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
                'Pragma', 'no-cache',
                'Expires', 'Fri, 01 Jan 1990 00:00:00 GMT',
            ];

            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
            return $response;
       //if (Auth::guard('admin')->user()->role_id == 4 || Auth::guard('admin')->user()->role_id == 5  ) {
           // dd("chala");
//            return $next($request)->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
           //return true;
        }
        
        return redirect('/logout');
    }
}
