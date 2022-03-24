<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class NotificationFiles extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subject;
    public $message;
    public $count_files;

    /**
     * Create a new message instance.
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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $address = "desarrollo@uciinformatica.es";
        $name = "Darío";
        
        return $this->from($this->user->email, $this->user->name)
                ->subject($this->subject)
                ->to($address, $name)
                ->markdown('emails.notification');
    }
}
