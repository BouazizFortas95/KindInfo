<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function download($uuid)
    {
        $certificate = Certificate::where('certificate_uuid', $uuid)->firstOrFail();
        
        // Generate PDF content
        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'));
        
        return $pdf->download("certificate-{$certificate->certificate_uuid}.pdf");
    }
}