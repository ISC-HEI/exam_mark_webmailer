<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;

class StudentMarkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $courseName;
    public $messageContent;
    protected $filePath;
    protected $originalFileName;

    /**
     * Create a new message instance.
     */
    public function __construct($courseName, $messageContent, $filePath = null, $originalFileName = null)
    {
        $this->courseName = $courseName;
        $this->messageContent = Str::markdown($messageContent);
        $this->filePath = $filePath;
        $this->originalFileName = $originalFileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "ISC - {$this->courseName} Note reçue",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.studentMark'
        );
    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->filePath && file_exists(Storage::disk('public')->path($this->filePath))) {
            return [
                Attachment::fromPath(Storage::disk('public')->path($this->filePath))->as($this->originalFileName)
            ];
        }
        return [];
    }
}
