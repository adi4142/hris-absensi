<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RoleVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $role;
    public $code;

    /**
     * Create a new message instance.
     *
     * @param string $role
     * @param string $code
     * @return void
     */
    public function __construct($role, $code)
    {
        $this->role = $role;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Kode Verifikasi Role ' . strtoupper($this->role) . ' - HRIS Absensi')
                    ->view('emails.role_verification');
    }
}
