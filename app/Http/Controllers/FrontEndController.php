<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class FrontEndController extends Controller
{
    public function index(){
        return Inertia::render('FrontEnd/Backend/Index');
    }
}
