<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
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
    public $contentMessage;
    public $individualFilePath;
    public $individualFileName;
    public $globalAttachments;

    public function __construct($courseName, $message, $individualFilePath = null, $individualFileName = null, $globalAttachments = [])
    {
        $this->courseName = $courseName;
        $this->contentMessage = Str::markdown($message);
        $this->individualFilePath = $individualFilePath;
        $this->individualFileName = $individualFileName;
        $this->globalAttachments = $globalAttachments;
    }

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

    public function attachments(): array
    {
        $attachments = [];

        if ($this->individualFilePath) {
            $attachments[] = Attachment::fromStorageDisk('public', $this->individualFilePath)
                ->as($this->individualFileName);
        }

        foreach ($this->globalAttachments as $file) {
            $attachments[] = Attachment::fromStorageDisk('public', $file['path'])
                ->as($file['name']);
        }

        return $attachments;
    }
}
