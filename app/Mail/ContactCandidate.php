<?php

namespace App\Mail;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactCandidate extends Mailable
{
    use Queueable, SerializesModels;
    public $candidate;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('owaisjamal123@gmail.com', 'Neurony Team')
            // ->to($this-> candidate->email)
            ->to('gihoyer142@cubene.com')
            ->subject('Contacting Candidate')
            ->markdown('emails.contact_candidate');
    }
}
