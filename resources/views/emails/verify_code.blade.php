<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="text-align: center; color: #007bff;">Verifikasi Email Anda</h2>
        <p>Halo,</p>
        <p>Terima kasih telah mendaftar di <strong>HRIS Absensi</strong>. Untuk menyelesaikan proses pendaftaran, silakan masukkan kode verifikasi berikut:</p>
        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #007bff; background: #f4f4f4; padding: 10px 20px; border-radius: 5px; border: 1px dashed #007bff;">
                {{ $code }}
            </span>
        </div>
        <p>Kode ini berlaku selama 15 menit. Jika Anda tidak merasa melakukan pendaftaran, silakan abaikan email ini.</p>
        <p>Terima kasih,<br>Tim HRIS</p>
    </div>
</body>
</html>
