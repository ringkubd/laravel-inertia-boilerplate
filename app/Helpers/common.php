<?php

if (!function_exists('lastMmaNo')) {
    function lastMmaNo($session, $semester){
        return \App\Models\Invoice::where('semester', $semester)
            ->where('session', $session)
            ->whereJsonContains('fee_type', 'MMA')
            ->groupBy('invoice_id')
            ->get()
            ->count();
    }
}

if (!function_exists('newPageNo')) {
    function newPageNo($type='page_no') {
       $invoice = \App\Models\Invoice::query()
            ->selectRaw('max(page_no) as page_no, max(serial_no) as serial_no')
            ->groupBy('invoice_id')
            ->firstOrFail();
       if ($invoice) {
           if ($type == 'page_no') {
               return (int)$invoice->page_no + 1;
           }else{
               return (int)$invoice->serial_no + 1;
           }

       }
       return 1;
    }
}
