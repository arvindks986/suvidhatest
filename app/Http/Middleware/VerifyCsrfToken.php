<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'auth/login/step1',
        'auth/login/step2',
        'ropc/counting/pdf',
        'aro/counting/pdf',
		'ropc/counting/migrant_pdf'
    ];
}
