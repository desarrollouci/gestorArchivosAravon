<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\NotificationFiles;
use Illuminate\Support\Facades\Mail;

class SendNotificationFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 5;
    public $user;
    public $subject;
    public $message;
    public $count_files;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $subject, $message,$count_files)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->message = $message;
        $this->count_files = $count_files;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
                        
            Mail::to($this->user->email)->send(
                new NotificationFiles(
                    $this->user,
                    $this->subject,
                    $this->message,
                    $this->count_files,

                )
            );
        } catch (\Exception $exception) {
            session()->flash("message", ["danger", $exception->getMessage()]);
            \Log::info($exception->getMessage());
        }
    }
}
