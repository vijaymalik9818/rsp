<?php 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ListingAlertEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $tableName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.listingAlert');
    }
}
