<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use ConfigEditor;

class SettingsController extends Controller
{
    public function index(){
        $config_files = scandir(config_path());
        return Inertia::render('Settings/Index', [
            'configs' => $config_files,
            'can' => [
                'create' => true
            ]
        ]);
    }

    public function create(){
        return Inertia::render('Settings/Create', );
    }
}
