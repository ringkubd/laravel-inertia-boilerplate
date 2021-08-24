<?php

namespace Anwar\LaravelOffice\Controller;
use App\Http\Controllers\Controller;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\HTML;

class ConvertDocsToWordController extends Controller
{
    public function index()
    {
        $docFile = public_path('sample-docx-file-for-testing.docx');
        $loadDocFile = IOFactory::load($docFile );
        $htmlWriter = new HTML($loadDocFile);
        dd($htmlWriter->save(public_path('docx.html')));
    }
}
