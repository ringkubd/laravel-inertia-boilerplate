<?php
if (!function_exists('currentMadrasah')) {
    function currentMadrasah($madrasah = null){
        if ($madrasah != null) {
            session(['madrasah' => $madrasah]);
        }
        if ($madrasah == "") {
            session()->forget('madrasah');
        }
        return session()->get('madrasah');
    }
}
