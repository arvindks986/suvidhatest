<?php

namespace App\Imports;

use App\models\Admin\polling_station\PollingStationModel;
use App\models\Admin\PollingStation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PollingStationImport implements ToModel, WithValidation, WithHeadingRow, WithStartRow
{
    use Importable;

    public function  __construct($ele_details, $user_data)
    {
        $this->ele_details= $ele_details;
        $this->user_data= $user_data;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        PollingStationModel::updateOrCreate([
            'election_id' => $this->ele_details->ELECTION_ID,
            'ST_CODE' => $this->ele_details->ST_CODE,
            'pc_no' => $this->ele_details->CONST_NO,
            'AC_NO' => $this->user_data->ac_no,
            'PART_NO'     => $row['part_no'],
            'PS_NO'    => $row['ps_no'],

        ],[
            'PART_NAME' => $row['part_name'],
            'PS_NAME_EN' => $row['ps_name_en'],
            'PS_TYPE' => $row['ps_type'],
            'PS_CATEGORY' => $row['ps_category'],
            'LOCN_TYPE' => $row['locn_type'],
            'electors_male' => $row['electors_male'],
            'electors_female' => $row['electors_female'],
            'electors_other' => $row['electors_other'],
            'electors_total' => $row['electors_total'],
        ]);
    }

    public function rules(): array
    {
        return [
            'part_no' => ['required','integer'],
            '*.part_no' => ['required','integer'],
            'ps_no' => ['required','string'],
            '*.ps_no' => ['required','string'],
            'part_name' => ['required','string'],
            '*.part_name' => ['required','string'],
            'ps_name_en' => ['required','string'],
            '*.ps_name_en' => ['required','string'],
            'ps_type' => ['required','string'],
            '*.ps_type' => ['required','string'],
            'ps_category' => ['required','string'],
            '*.ps_category' => ['required','string'],
            'locn_type' => ['required','string'],
            '*.locn_type' => ['required','string'],
            'electors_male' => ['required','integer'],
            '*.electors_male' => ['required','integer'],
            'electors_female' => ['required','integer'],
            '*.electors_female' => ['required','integer'],
            'electors_other' => ['required','integer'],
            '*.electors_other' => ['required','integer'],
            'electors_total' => ['required','integer'],
            '*.electors_total' => ['required','integer'],

        ];
    }
}