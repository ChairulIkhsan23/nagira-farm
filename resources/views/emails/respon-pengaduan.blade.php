<!DOCTYPE html>
<html>
<head>
    <title>Respon Pengaduan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 2px solid #007bff;
            text-align: center;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .content {
            padding: 20px;
        }
        .pengaduan {
            background-color: #f1f1f1;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }
        .respon {
            background-color: #e8f4fd;
            padding: 15px;
            border-left: 4px solid #28a745;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ url('images/logo.png') }}" alt="Nagira Farm Logo" class="logo">            <h2>Respon Pengaduan</h2>
            <p>Terima kasih telah menghubungi kami</p>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $pengaduan->nama_pengirim }}</strong>,</p>
            
            <p>Kami telah menerima dan memproses pengaduan Anda. Berikut adalah ringkasan dan respon dari kami:</p>
            
            <div class="pengaduan">
                <h4>Pengaduan Anda:</h4>
                <p><strong>Kategori:</strong> {{ ucfirst($pengaduan->kategori) }}</p>
                <p><strong>Subjek:</strong> {{ $pengaduan->subjek }}</p>
                <p><strong>Pesan:</strong></p>
                <p>{{ $pengaduan->pesan }}</p>
                <p><strong>Tanggal:</strong> {{ $pengaduan->created_at->format('d M Y H:i') }}</p>
            </div>
            
            <div class="respon">
                <h4>Respon Kami:</h4>
                <p>{{ $respon }}</p>
            </div>
            
            <p>Jika Anda memiliki pertanyaan lebih lanjut, silakan balas email ini atau hubungi kami kembali.</p>
            
            <p>Salam,<br>
            <strong>Tim Layanan Pengaduan</strong></p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Nagira Farm. All rights reserved.</p>
        </div>
    </div>
</body>
</html>