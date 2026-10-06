<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\DigitalSignature;

class SignatureVerifyController extends Controller
{
    public function show(string $token)
    {
        $signature = DigitalSignature::with([
                'planning.department',
                'planning.creator',
            ])
            ->where('token', $token)
            ->firstOrFail();

        return view('general.digital-signatures.verify', compact('signature'));
    }
}