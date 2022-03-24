<?php

namespace App\Traits\Mail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Jobs\SendInfoEmail;
use App\Jobs\SendContactEmail;
use DB;
use Str;

trait ManageMails
{


    public function sendInfoMail()
    {
        
        try {
            SendInfoEmail::dispatch(
                request('name'),
                request('email'),
                request('phone')
            )->onQueue("emails");

            session()->flash("message", ["info", __('En breve me pondré en contacto contigo.')]);
        } catch (\Throwable $th) {
            session()->flash("message", ["info", __('Ha ocurrido un error, inténtalo de nuevo.')]);
        }
        
        return back();
        
    }

    public function sendContactMail()
    {
        
        try {
            SendContactEmail::dispatch(
                auth()->user(),
                request('message')
            )->onQueue("emails");

            session()->flash("message", ["info", __('En breve me pondré en contacto contigo.')]);
        } catch (\Throwable $th) {
            session()->flash("message", ["info", __('Ha ocurrido un error, inténtalo de nuevo.')]);
        }
        
        return back();
        
    }
}
