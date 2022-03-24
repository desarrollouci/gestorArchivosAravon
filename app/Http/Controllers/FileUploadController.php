<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\Uploader;
use Illuminate\Support\Str;
use DB;
use File;
use ZipArchive;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationFiles;
use App\Jobs\SendNotificationFiles;

class FileUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::withCount('files')->with('files')->paginate(9);

       
        return view('files.index', compact('users'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('files.upload');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                             
                $FileName = null;
                if (request()->hasFile("file")) {
                    $FileName = Uploader::uploadFile('file', '/public/users/' . auth()->id());
                }
                
                FileUpload::create([
                    'user_id' => auth()->id(),
                    'filename' => $FileName,
                    'name' => Str::slug(request()->file('file')->getClientOriginalName()),
                    'extension' => request()->file('file')->getClientOriginalExtension(),
                ]); 
               
                DB::commit();
                return response()->json(['success' => $FileName]);
                
            } catch (\Throwable $exception) {
                DB::rollBack();
                return response()->json(['message' => $exception->getMessage()]);
            }    
        } else {
            abort(401);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            
            if (request()->ajax()) {
                try{
                    DB::beginTransaction();
                        $file = FileUpload::where('name',Str::slug(request()->get('filename')))->first();
                    
                        Uploader::removeFile('/public/users/' . auth()->id(), $file->filename);
                        
                        $file->forceDelete();
                    DB::commit();
                } catch (\Exception $exception) {
                    DB::rollBack();
                    session()->flash("message", ["danger", $exception->getMessage()]);
                }
            } else {
                try{
                    DB::beginTransaction();
                        $files = request('download');
                        foreach($files as $key){
                            $file = FileUpload::where('id',$key)->first();
                    
                            Uploader::removeFile('/public/users/' . auth()->id(), $file->filename);
                            
                            $file->forceDelete();
                        }
                        
                        $message = "Archivo eliminado satisfactoriamente";
                        $type = 'success';
                        if(count($files) > 1) {
                            $message = "Archivos eliminados satisfactoriamente";
                        }
        
                        if(count($files) == 0) {
                            $type = 'info';
                            $message = "No se ha eliminado ningún archivo";
                        }
                    DB::commit();
                        session()->flash("message", [ $type, $message]);
                        return back();
                    
                } catch (\Exception $exception) {
                    DB::rollBack();
                    session()->flash("message", ["danger", $exception->getMessage()]);
                }  
            }
        } catch (\Exception $exception) {
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back();
        }
    }

    public function notification(){

        $subject = __('Notificación subida de archivos');
        $message = __('Acabo de subir archivos a la plataforma.');
        $moreUsers = [];
        $evenMoreUsers = [];

        try {
            DB::beginTransaction();
                $files = FileUpload::where([
                    ['user_id', auth()->id()],
                    ['notificated', 0]
                ]);
                $count_files = count($files->get());
                if($count_files > 0){
                    $files->update(['notificated' => 1]);
                    SendNotificationFiles::dispatch(
                        auth()->user(),
                        $subject,
                        $message,
                        $count_files,
                    )->onQueue("emails"); 
                }else{
                    session()->flash("message", ["info", __('No hay archivos nuevos que notificar.')]);
                    return back();
                }
                
            DB::commit();
            /* Mail::to(Auth()->user())
                ->cc($moreUsers)
                ->bcc($evenMoreUsers)
                ->send(new NotificationFiles2($message)); */

            session()->flash("message", ["success", __('Archivos enviados satisfactoriamente.')]);
        } catch (\Throwable $exception) {
            DB::rollBack();
            session()->flash("message", ["danger", $exception->getMessage()]);
            /*session()->flash("message", ["danger", __('Ha ocurrido un error, inténtalo de nuevo.')]);*/
        }
        
        return back();
    }

    public function download(){
        //$user = request('user_id');
        $files = request('download');
        $count_files = count($files);
        $user = User::whereId(request('user_id'))->first();
        
        $coockieTime = 60;
        $nueva_cookie = cookie('nombre', 'valor', $coockieTime);
        $this->setCookieToken("downloadToken", true, false);

        $headers = [
            'Refresh' => 0,
            /*'Content-Type' => 'application/pdf',*/
        ];
        
        if($count_files > 1){
            return response()->download($this->converToZip($files,$user->id, Str::slug($user->name)), Str::slug($user->name) . '.zip', $headers);
        }

        if($count_files == 1){
            try {
                DB::beginTransaction();
                    $file = FileUpload::whereId(reset($files))->first();
                    $file->downloaded = 1;
                    $file->save();
                DB::commit();
            } catch (\Throwable $exception) {
                DB::rollBack();
                session()->flash("message", ["danger", $exception->getMessage()]);
            }
            return response()->download(storage_path(sprintf('%s/%s/%s', 'app/public/users', $user->id, $file->filename )), $file->filename, $headers);
        }

        return back();
    }

    public function converToZip($files, $user, $zipName){
        
        
        $zip = new ZipArchive;
        $storage_path = storage_path(sprintf('%s/%s', 'app/public/users', $user));
        $zipFileName = sprintf('%s/%s.%s',$storage_path, $zipName, 'zip');
                
        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            
            foreach ($files as $key => $value) {
                

                try {
                    DB::beginTransaction();
                        $file = FileUpload::whereId($value)->first();
                        $file->downloaded = 1;
                        $file->save();
                    DB::commit();

                    $relativeNameInZipFile = basename(str_replace($file->extension, "." . $file->extension ,$file->name));
                    $zip->addFile(sprintf('%s/%s',$storage_path, $file->filename), $relativeNameInZipFile);
                } catch (\Throwable $exception) {
                    DB::rollBack();
                    session()->flash("message", ["danger", $exception->getMessage()]);
                }

                
            }
            
            $zip->close();

            if ($zip->open($zipFileName) === true) {
                return $zipFileName;
            } else {
                return false;
            }
        }
    }

    public function setCookieToken($cookieName,$cookieValue,$httpOnly = true,$secure = false) {

        setcookie(
            $cookieName,
            $cookieValue,
            2147483647,            // expires January 1, 2038
            "/",                   // your path
           // $_SERVER["HTTP_HOST"], // your domain
            $secure,               // Use true over HTTPS
            $httpOnly              // Set true for $AUTH_COOKIE_NAME
        );
    }
}
