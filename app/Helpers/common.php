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
            ->orderByDesc('page_no')
            ->firstOrFail();

        if ($invoice && $invoice->page_no != null) {
            if ($type == 'page_no') {
                return (int)$invoice->page_no + 1;
            }else{
                return (int)$invoice->serial_no + 2;
            }

        }
        if ($type == 'page_no'){
            return 178;
        }else{
            return 471;
        }
    }
}
function getBytesFromHexString($hexdata)
{
    for($count = 0; $count < strlen($hexdata); $count+=2)
        $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));

    return implode($bytes);
}

function getImageMimeType($imagedata)
{
    $imagemimetypes = array(
        "jpeg" => "FFD8",
        "png" => "89504E470D0A1A0A",
        "gif" => "474946",
        "bmp" => "424D",
        "tiff" => "4949",
        "tiff" => "4D4D"
    );

    foreach ($imagemimetypes as $mime => $hexbytes)
    {
        $bytes = getBytesFromHexString($hexbytes);
        if (substr($imagedata, 0, strlen($bytes)) == $bytes)
            return $mime;
    }

    return NULL;
}
