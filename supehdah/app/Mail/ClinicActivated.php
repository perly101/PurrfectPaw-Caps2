<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Clinic;

class ClinicActivated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The clinic instance.
     *
     * @var Clinic
     */
    public $clinic;

    /**
     * Create a new message instance.
     *
     * @param  Clinic  $clinic
     * @return void
     */
    public function __construct(Clinic $clinic)
    {
        $this->clinic = $clinic;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Welcome to PurrfectPaw — Your Clinic is Now Active!')
            ->markdown('emails.clinic-activated');
    }
}