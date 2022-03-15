<?php

namespace App\Http\Controllers;

use App\Models\BtebResult;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class BTEBResultController extends Controller
{

    public function index(){
        $prevResult = BtebResult::orderBy('exam_year')->paginate();
        return Inertia::render('BtebResult/Index', [
            'results' => $prevResult
        ]);
    }

    public function new(){
        $validExamYear = [];
        $currentYear = Carbon::now()->format('Y');
        $lastYear  = 2005;
        for ($year = $currentYear; $year >= $lastYear; $year--){
            $validExamYear[] = $year;
        }

        return Inertia::render('BtebResult/New',[
            'valid_year' => $validExamYear
        ]);
    }


    public function result(Request $request){
        $request->validate([
            'roll' => 'required',
            'year' => 'required',
            'exam' => 'required',
        ]);
        $client = Http::get('http://180.211.164.133/result_arch/result.php', [
            'exam' => (int)$request->exam,
            'year' => (int)$request->year,
            'roll' => (int)$request->roll,
            'reg' => $request->reg
        ]);
        $body = $client->body();
        $dom = new \DOMDocument();
        $dom->loadHTML($body);
        $table = $dom->getElementsByTagName('table');
        return $table->item(1)->C14N();
    }
}
