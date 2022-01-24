<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use ConfigEditor;

class SettingsController extends Controller
{
    public function index(){
        $configTable = ConfigEditor::getConfigAsSingleArray('database');
        dd($configTable);
        return Inertia::render('Settings/Index', [
            'configs' => $configTable
        ]);
    }
}
