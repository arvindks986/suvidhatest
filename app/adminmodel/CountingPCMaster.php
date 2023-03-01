<?php
	namespace App\adminmodel;
	use Illuminate\Database\Eloquent\Model;
	use DB;
class CountingPCMaster extends Model
{
    protected $table ="counting_pcmaster";
	protected $primaryKey ='id'; 
	protected $fillable = [
     	'nom_id',
		'candidate_id',
        'symbol_id',
     	'candidate_name',
     	'party_id',
     	'party_abbre',
     	'party_name',
     	'st_code',
     	'pc_no',
     	'election_id',
     	'evm_vote',
     	'postal_vote',
     	'total_vote',
     	'finalized_ac',
     	'created_at',
     	'created_by',
     	'updated_at',
     	'updated_by' 
     ];
	protected $guarded = ['id'];
 
}

