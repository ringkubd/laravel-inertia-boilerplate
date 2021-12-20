<?php

namespace App\Imports;

use App\Models\Admission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdmissionResultUpdate implements \Maatwebsite\Excel\Concerns\ToCollection, WithHeadingRow
{

    use Importable;
    /**
     * @param array $array
     * @return mixed
     */

    public function collection(Collection $row)
    {
        foreach ($row as $r){
            Admission::where('tracking_id', $r['tracking_id'])->update(['status' => $r['status']]);
        }
        return $row;
    }
}
