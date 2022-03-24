<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $table = 'fileupload';
    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'filename', 
        'user_id',
        'name',
        'extension',
        'downloaded',
        'notificated',
        'created_at',
        'updated_at'
    ];

    public function filePath(){
        return sprintf('%s/%s/%s', config('app.public') . '/storage', $this->user->id , $this->filename);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}