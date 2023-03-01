<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class AC extends Model
{
	protected $table = 'm_ac';
	
	public function state()
    {
        return $this->belongsTo(States::class, 'ST_CODE', 'ST_CODE');
    }

    public function pc()
    {
        return $this->belongsTo(PC::class, 'PC_NO', 'PC_NO');
    }
}
