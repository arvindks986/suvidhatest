<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect, Auth, DB, Session, Config;
use Illuminate\Support\Facades\Route;

class Urlredirect_Middleware 
{

  
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       $url=url('/'); 
       $segment = Request::segment(1);
       
       /*if(($url=="https://demo.eci.nic.in/suvidhaPCNew/public") && ($segment=="login") ) {

         return redirect('https://demo.eci.nic.in/suvidhaPCNew/public/login');
      }
      elseif(($url=="https://demo.eci.nic.in/suvidhaPCNew/public") && ($segment=="officer-login") ) {

         return redirect('https://demo.eci.nic.in/suvidhaPCNew/public/officer-login');
      }*/

      
      return $next($request);
  }
}
