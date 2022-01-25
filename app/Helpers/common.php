<?php

if (!function_exists('lastMmaNo')) {
    function lastMmaNo($session, $semester){
        return \App\Models\Invoice::where('semester', $semester)->where('session', $session)->whereJsonContains('fee_type', 'MMA')->groupBy('invoice_id')->get()->count();
    }
}
