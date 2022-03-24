<?php 

namespace App\Traits\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

use App\Models\Content;
use App\Http\Requests\ContentRequest;
use App\Helpers\Uploader;
use Str;

trait ManageUploads{

	public function uploads(){
       return view('dashboard.uploads.index');  
    }

    public function uploadLogo() {

        try {
            if (request()->hasFile('logo')) {
            $file = 'logo.' . request()->file('logo')->getClientOriginalExtension();
            
            Storage::delete(sprintf('%s/%s', 'web', 'logo.*'));
            
            request()->file('logo')->storeAs('web', $file);
        }
            session()->flash("message", ["success", __("Logo subido satisfactoriamente")]);
            return back();
            
        } catch (\Throwable $exception) {
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back()->withInput();
        }   

    }

    public function uploadDossier() {

        try {
            if (request()->hasFile('dossier')) {
                $file = 'dossier mediciones.pdf';
                
                Storage::delete(sprintf('%s/%s', 'docs', $file));
                
                request()->file('dossier')->storeAs('docs', $file);
            }
            session()->flash("message", ["success", __("Dossier subido satisfactoriamente")]);
            return back();
            
        } catch (\Throwable $exception) {
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back()->withInput();
        }   

    }
    
}