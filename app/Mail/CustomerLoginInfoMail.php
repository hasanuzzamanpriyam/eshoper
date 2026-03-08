<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerLoginInfoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $customerDetails;
    public function __construct($customerDetails)
    {
        $this->customerDetails = $customerDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(translate('customer_login_info_sub'))->view('email-templates.guest-customer-login-info', ['customerDetails' => $this->customerDetails]);
    }
}
