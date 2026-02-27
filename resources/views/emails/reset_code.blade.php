<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="text-align: center; color: #dc3545;">Reset Password Anda</h2>
        <p>Halo,</p>
        <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun <strong>HRIS Absensi</strong> Anda.</p>
        <p>Gunakan kode berikut untuk mereset password Anda:</p>
        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #dc3545; background: #f4f4f4; padding: 10px 20px; border-radius: 5px; border: 1px dashed #dc3545;">
                {{ $code }}
            </span>
        </div>
        <p>Kode ini berlaku selama 60 menit. Jika Anda tidak merasa meminta reset password, silakan abaikan email ini.</p>
        <p>Terima kasih,<br>Tim HRIS</p>
    </div>
</body>
</html>
