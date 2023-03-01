<?php

namespace App\Http\Middleware;

use Closure, Request, Redirect, Auth, DB, Session, Config;
use Illuminate\Support\Facades\Route;
use App\Classes\Secure;

class SetDB_API_Middleware 
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
        if(!empty($request->election_id)) {
            if (is_numeric($request->election_id)){
            $election_id = $request->election_id;
          }else{
            $cipher = new Secure();
            $election_id = $cipher->encrypt_decrypt('decrypt',$request->election_id);
          }
          if($election_id != 0 && $election_id != "")
          {
          $m_ele_his = DB::connection('mysql_database_history')->table('m_election_history')->where('id','=',$election_id)->first();
          $db_name = $m_ele_his->db_name;
          $const_type = $m_ele_his->const_type;
          if($const_type == 'PC') {
            Config::set('database.default', "mobile_app_pc");
            Config::set('database.connections.mobile_app_pc.database', $db_name);
            Config::set('database.connections.mobile_app_pc.write.host', "10.247.219.232");
            Config::set('database.connections.mobile_app_pc.read.host', "10.247.219.232");
            DB::reconnect('mobile_app_pc');
          }else{
            Config::set('database.default', "mobile_app_ac");
            Config::set('database.connections.mobile_app_ac.database', $db_name);
            Config::set('database.connections.mobile_app_ac.write.host', "10.247.219.232");
            Config::set('database.connections.mobile_app_ac.read.host', "10.247.219.232");
            DB::reconnect('mobile_app_ac');
            //dd(Config::get('database.connections.mobile_app.database'));
          }
          }
        }
      return $next($request);
  }
}
