<?php

namespace App\Mail;

use App\Models\Pengaduan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResponPengaduanMail extends Mailable
{
    use Queueable, SerializesModels;

    public Pengaduan $pengaduan;
    public string $respon;
    public string $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Pengaduan $pengaduan, string $respon)
    {
        $this->pengaduan = $pengaduan;
        $this->respon = $respon;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respon Pengaduan: ' . $this->pengaduan->subjek,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.respon-pengaduan',
        );
    }
}