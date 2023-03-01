<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect, Auth, DB, Session, Config;
use Illuminate\Support\Facades\Route;

class SetDBMiddleware 
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

      if(Session::has('DB_DATABASE')) {
        Config::set('database.connections.mysql.database', Session::get('DB_DATABASE'));
        DB::reconnect('mysql');
      }else{
        //Config::set('database.default', "mysql");
       //echo "Default DB set.";
        Config::set('database.connections.mysql.database', 'suvidha_2022_12_e18');
        DB::reconnect('mysql');
      }
      return $next($request);
  }
}
