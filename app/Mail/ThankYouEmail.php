<?php 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThankYouEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.thank_you_email');
    }
}
