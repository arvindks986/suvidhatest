<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB;
class PartyMaster extends Model
{
    protected $table ="m_party";
	protected $primaryKey ='CCODE'; 
	protected $fillable = [
     	'PARTYABBRE',
		'PARTYHABBR',
        'PARTYNAME',
     	'PARTYHNAME',
     	'PARTYTYPE',
     	'PARTYSYM'
     ];
	 
    protected $guarded = ['CCODE'];
    public function symboldetails()
    	{
        return $this->belongsTo('App\model\SymbolMaster','PARTYSYM','SYMBOL_NO');
    	}
}
