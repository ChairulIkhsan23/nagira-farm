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

    
    public function __construct(Pengaduan $pengaduan, string $respon)
    {
        $this->pengaduan = $pengaduan;
        $this->respon = $respon;
    }

    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respon Pengaduan: ' . $this->pengaduan->subjek,
        );
    }

    
    public function content(): Content
    {
        return new Content(
            view: 'emails.respon-pengaduan',
        );
    }
}