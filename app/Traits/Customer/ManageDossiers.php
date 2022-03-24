<?php

namespace App\Traits\Customer;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth, DB, Hash;

trait ManageDossiers
{

    public function dossiers()
    {   
        $user = auth()->user();
        $user->load("dossiers");
        return view('web.customer.dossiers', compact('user'));
    }

    
}
