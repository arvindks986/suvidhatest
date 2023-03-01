<?php

namespace App\models;

use App\models\Admin\EndOfPollFinaliseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PC extends Model
{
	protected $table = 'm_pc';
	
	public function state()
    {
        return $this->belongsTo(States::class, 'ST_CODE', 'ST_CODE');
    }

    public static function getPcForPhase($phase, $st_code)
    {
        $pcids = EndOfPollFinaliseModel::where('st_code', $st_code)->where('schedule_id', $phase)->pluck('pc_no');
        return PC::where('st_code', $st_code)->whereIn('PC_NO', $pcids)->get();
    }
}
