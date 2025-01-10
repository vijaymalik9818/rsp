<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $randomPassword;

    /**
     * Create a new message instance.
     *
     * @param string $randomPassword
     * @return void
     */
    public function __construct($randomPassword)
    {
        $this->randomPassword = $randomPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Password')
                    ->view('emails.sendpass');
    }
}