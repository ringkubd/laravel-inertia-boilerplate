<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function getStudentsForInvoice($session, $semester)
    {
        $resultSemester = $semester - 1;
        
        return Student::query()
            ->select('students.*', DB::raw("IF(d.status is not null, d.status, r.status) AS result_status"), 'r.gpa', 'r.created_at')
            ->where('polytechnic_session', $session)
            ->with([
                'fees' => fn($q) => $q->where('semester', $semester)->where('session', $session),
                'paymentSlip' => fn($q) => $q->where('semester', $semester),
                'results' => fn($q) => $this->getResultsQuery($q, $semester),
                'invoice' => fn($q) => $q->where('session', $session)->where('semester', $semester),
                'invoiceDetails.invoice' => fn($q) => $q->where('session', $session)->where('semester', $semester)
            ])
            ->leftJoin('results as r', fn($join) => $this->joinResults($join, $resultSemester))
            ->leftJoin('results as d', fn($join) => $this->joinDropouts($join))
            ->groupBy('student_id');
    }

    private function joinResults(JoinClause $join, $resultSemester)
    {
        return $join->on('r.student_id', 'students.id')
            ->where('r.semester', $resultSemester)
            ->where('r.deleted_at', null);
    }

    private function joinDropouts(JoinClause $join)
    {
        return $join->on('d.student_id', 'students.id')
            ->where('d.status', 'Dropout')
            ->where('d.deleted_at', null);
    }

    private function getResultsQuery($query, $semester)
    {
        return $query->where(function ($q) use ($semester) {
            $previousSemester = $semester - 1;
            if ($previousSemester > 0) {
                $q->where('semester', $previousSemester);
            }
        })->orWhere('status', 'Dropout')->latest();
    }
}
