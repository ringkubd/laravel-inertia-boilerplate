<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BTEBResultController extends Controller
{
    public function result(){
        $client = Http::get('http://180.211.164.133/result_arch/result.php', [
            'exam' => 15,
            'year' => 2020,
            'roll' => 313413,
            'reg' => ''
        ]);
        $body = $client->body();
        $dom = new \DOMDocument();
        $dom->loadHTML($body);
        $table = $dom->getElementsByTagName('table');
        dd($table->item(1)->C14N());
    }
}
