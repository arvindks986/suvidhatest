<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use DB;
class PermissionModel extends Model 
{ 
    public function insertdata($table,$data)
    {
        $data=DB::table($table)->insert($data);
        return $data;
    }
    public function getAuthority()
    {
        $data=DB::table('authority_type as a')
                ->select('a.*')
                ->get()->toArray();
        return $data;
    }
    public function getPermissionDetails()
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.user_id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->select('a.*','b.name','c.role_name','d.permission_name')
                ->get()->toArray();
        //dd(DB::getQueryLog());
        return $data;
    }
     public function getDetails($id)
    {
        $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.user_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('state_masters as e','e.ST_CODE','=','a.st_code')
                ->join('district_masters as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'e.ST_CODE');
                })
                ->join('ac_masters as g',function ($join){
                    $join->on('g.AC_NO','=','a.ac_no')
                         ->on('g.ST_CODE', '=', 'e.ST_CODE');
                })
                ->where('a.id',$id)
                ->select('a.*','b.name','d.permission_name','b.mobile','e.ST_NAME','f.DIST_NAME_EN','g.AC_NAME_EN')
                ->distinct('a.id')
                ->get()->toArray();
        return $data;
    }
    
    public function updateData($table,$where,$record)
    {
        $data=DB::table($table)->where($where)->update($record);
        return $data;
    }
    
    public function getAllAuthority($id)
    {
        $data=DB::table('authority_type')
                ->select('*')->whereIn('id',$id)->get()->toArray();
        return $data;
    }
	
	    public function getlocationmaster($id)
    {
        $data=DB::table('location_master')
                ->where('created_by',$id)
                ->select('*')->get()->toArray();
        return $data;
    }
	
		    public function getlocationeditmaster($id)
    {
        $data=DB::table('location_master')
                ->where('id',$id)
                ->select('*')->get()->toArray();
        return $data;
    }
    
    public function getAllnodal($id)
    {
        $data=DB::table('authority_masters')
                ->select('authority_masters.id','authority_masters.name')
                ->where('auth_type_id',$id)
                ->get()->toArray();
        return $data;
    }
    public function getNodaldetails($id)
    {
        //echo $id;die;
        $data=DB::table('permission_assigned_auth as a')
                ->join('authority_masters as b','a.authority_id','=','b.id')
                ->join('authority_type as c','b.auth_type_id','=','c.id')
                ->select('a.*','b.*','c.name as auth_name')
                ->where('a.permission_request_id',$id)
                ->get()->toArray();
        return $data;
//        print_r($data);die;
    }
//    public function getNodaldetails($id)
//    {
//        $data=DB::table('authority_masters as a')
//                ->select('a.*','b.name as auth_name')
//                ->whereIn('auth_type_id',$id)
//                ->join('authority_type as b','a.auth_type_id','=','b.id')
//                ->get()->toArray();
//        return $data;
//    }
    public function getAllPSData()
    {
        $data=DB::table('police_station_master')
                ->select('*')
                ->get()->toArray();
        return $data;
    }
    public function getpsdetails($id)
    {
        $data=DB::table('police_station_master')
                ->where('id',$id)
                ->select('*')->get()->toArray();
        return $data;
    }
    public function updatetable($table,$content,$where)
    {
        $data=DB::table($table)
                ->where($where)
                ->update($content);
        return $data;
    }
    public function getAllPermsData()
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_type as a')
                ->join('permission_required_doc as b','a.id','=','b.permission_id')
                ->join('authority_type as c',\DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"),">",\DB::raw("'0'"))
                ->select('a.*','a.id as permsn_id','b.*','b.id as doc_id','c.*','c.id as auth_id')
                ->select(DB::raw("GROUP_CONCAT(DISTINCT b.doc_name SEPARATOR ',') as 'doc_name'"),DB::raw("GROUP_CONCAT(DISTINCT c.name SEPARATOR ',') as 'auth_name'"),'a.id','a.permission_name')
                ->groupBy('a.id','a.permission_name')
                ->get()->toArray();
       // dd(DB::getQueryLog());
        return $data;
    }
    public function getpermsndetails($id)
    {
        $data=DB::table('permission_type as a')
            ->join('permission_required_doc as b','a.id','=','b.permission_id')
            ->join('authority_type as c',\DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"),">",\DB::raw("'0'"))
            ->select('a.*','a.id as permsn_id','b.*','b.id as doc_id','c.*','c.id as auth_id')
            ->select('a.id as p_id','a.permission_name','a.authority_type_id',DB::raw("GROUP_CONCAT( b.doc_name SEPARATOR ',') as 'doc_name'"),DB::raw("GROUP_CONCAT(c.name SEPARATOR ',') as 'auth_name'"))
            ->where('a.id',$id)    
            ->groupBy('a.id','a.permission_name','a.authority_type_id')
            ->get()->toArray();
        return $data;
    }
    
     public function getRodetails($id)
    {
        $data=DB::table('permission_request as a')
                ->join('permission_request_comment as b','a.id','=','b.permission_request_id')
                ->select('b.*','a.approved_status')
                ->where('b.permission_request_id',$id)
                ->get()->toArray();
        return $data;
    }
}