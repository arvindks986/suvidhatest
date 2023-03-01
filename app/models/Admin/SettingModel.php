<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;
use Cache;
use App\Classes\xssClean;

class SettingModel extends Model
{
    protected $table = 'setting';
	
    public static function add_record($code, $request){
        $clean_input = new xssClean();
        SettingModel::where('code', $code)->delete();
        foreach ($request->except('_token') as $key => $value) {
            SettingModel::insert([
                'code'  => $clean_input->clean_input($code),
                'key'   => $clean_input->clean_input($key),
                'value' => $clean_input->clean_input($value)
            ]);
        }

    }

    public static function get_records($code){
        $data = [];
        $results = SettingModel::where('code',$code)->get();
        foreach ($results as $key => $result) {
            $data[$result->key] = $result->value;
        }
        Cache::forever('cache_setting',serialize($data));
        return $data;
    }

    public static function get_setting_cache(){
        if(!Cache::has('cache_setting')){
            SettingModel::generate_cache();
        }
        $data =  unserialize(Cache::get('cache_setting'));
        return $data;
    }

    public static function generate_cache(){
        SettingModel::get_records('setting');
    }
	
	public static function get_first_result($code){
        $object = SettingModel::where('code',$code)->first();
        if(!$object){
            return false;
        }
        return $object->toArray();
    }
    
}
