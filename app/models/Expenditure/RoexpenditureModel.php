<?php

namespace App\models\Expenditure;
use Illuminate\Database\Eloquent\Model;
use DB;

class RoexpenditureModel extends Model {

    protected $table = 'expenditure_reports';
    protected $fillable = ['candidate_id', 'ac_pc_name', 'date_of_declaration', 'date_of_account_rec_meetng',
        'reconciliation_meeting_writing', 'agent_attend_meeting', 'defect_reconciliation_meeting',
        'last_date_prescribed_acct_lodge', 'candidate_lodged_acct', 'date_orginal_acct', 'date_revised_acct',
        'account_lodged_time', 'not_lodged_period_delay', 'reason_lodged_not_lodged', 'explaination_by_candidate',
        'comment_by_deo', 'grand_total_election_exp_by_cadidate', 'created_at', 'updated_at'];

    public static function Add($dataArray) {
        return self::create($dataArray);
    }

    public static function viewById($candidateId) {
        return self::where('candidate_id', '=', $candidateId)
                        ->select('candidate_id', 'ac_pc_name', 'date_of_declaration', 'date_of_account_rec_meetng', 'reconciliation_meeting_writing', 'agent_attend_meeting', 'defect_reconciliation_meeting', 'last_date_prescribed_acct_lodge', 'candidate_lodged_acct', 'date_orginal_acct', 'date_revised_acct', 'account_lodged_time', 'not_lodged_period_delay', 'reason_lodged_not_lodged', 'explaination_by_candidate', 'comment_by_deo', 'grand_total_election_exp_by_cadidate')
                        ->first();
    }

    public static function isCandidate($candidateId) {
        $response = self::where('candidate_id', '=', $candidateId)->first();
        return !empty($response) ? true : false;
    }

}
