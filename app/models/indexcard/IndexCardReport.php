<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class IndexCardReport implements FromView, ShouldAutoSize
{
	
	public $getIndexCardDataCandidatesVotesACWise;
	public $getIndexCardDataPCWise;
	public $st_code;
	public $pc;
	public $getelectorsacwise;

    function __construct($getIndexCardDataCandidatesVotesACWise,$getIndexCardDataPCWise,$getelectorsacwise,$st_code,$pc) {

        $this->getIndexCardDataCandidatesVotesACWise = $getIndexCardDataCandidatesVotesACWise;
        $this->getIndexCardDataPCWise = $getIndexCardDataPCWise;
        $this->st_code = $st_code;
        $this->pc = $pc;
        $this->getelectorsacwise = $getelectorsacwise;

    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.indexcard-report',[
            'getIndexCardDataCandidatesVotesACWise' => $this->getIndexCardDataCandidatesVotesACWise,
            'getIndexCardDataPCWise' => $this->getIndexCardDataPCWise,
            'st_code' => $this->st_code,
            'pc' => $this->pc,
            'getelectorsacwise' => $this->getelectorsacwise
        ]);
    }
}