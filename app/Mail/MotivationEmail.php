<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MotivationEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $mailSubject, $mailBody;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailSubject, $mailBody)
    {
        $this->mailSubject = $mailSubject;
        $this->mailBody = $mailBody;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'email.motivate',
            with: ['mailBody'=> $this->mailBody],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
