<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{


    /**
     * Rotta base per tutte le richieste
     */
    public function app()
    {
        return view('app');
    }

    /**
     * Rotta specifica per il recupero password
     */
    public function recoverPassword(Request $request, $token)
    {
        return view('app')->with(
            ['email' => $request->email]
        );
    }
}
