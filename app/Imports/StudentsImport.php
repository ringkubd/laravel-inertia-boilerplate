<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\students;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentsImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    public function collection(Collection $collection)
    {
        foreach ($collection as $row){
            $user = User::create([
                'name' => $row[29],
                'email' => $row[30],
                'password' => Hash::make($row[31]),
            ]);

            $student = Student::create([
                'users_id' => $user->id,
                'name' => $row[0],
                'class_roll' => $row[1],
                'student_id' => rand(999999),
                'mobile' => $row[3],
                'mobile_1' => $row[4],
                'guardian_mobile' => $row[5],
                'father_name' => $row[6],
                'mother_name' => $row[7],
                'madrasa_trade_id' => $row[8],
                'polytechnic_trade_id' => $row[9],
                'madrasha_id' => $row[10],
                'ssc_roll' => $row[11],
                'ssc_registration' => $row[12],
                'ssc_session' => $row[13],
                'polytechnic' => $row[14],
                'polytechnic_registration' => $row[15],
                'polytechnic_roll' => $row[16],
                'polytechnic_session' => $row[17],
                'semester' => $row[18],
                'dob' => $row[19],
                'nid' => $row[20],
                'bank_account' => $row[21],
                'bank_branch' => $row[22],
                'bank_name' => $row[23],
                'present_address' => $row[24],
                'permanent_address' => $row[25],
                'madrasa_completed' => $row[26],
                'polytechnic_completed' => $row[27],
            ]);
        }
    }
}
