<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Kehadiran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #49acf3ff;
            --primary-dark: #2470daff;
            --bg-color: #F3F4F6;
            --card-bg: #FFFFFF;
            --text-color: #1F2937;
            --text-light: #6B7280;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --danger-color: #EF4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .scan-container {
            width: 100%;
            max-width: 480px;
            background-color: var(--card-bg);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 24px;
            text-align: center;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 24px;
            left: 24px;
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
        }

        .header-subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
        }

        /* User Info */
        .user-info {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            padding: 16px 24px;
            backdrop-filter: blur(10px);
        }

        .user-name {
            font-size: 18px;
            font-weight: 600;
            color: white;
            margin-bottom: 4px;
        }

        .user-details {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .user-nip, .user-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Camera Section */
        .camera-section {
            padding: 24px;
        }

        .camera-container {
            position: relative;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            background: #000;
            margin-bottom: 24px;
        }

        #video {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 16px;
        }

        #canvas {
            display: none;
        }

        .camera-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .camera-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 70%;
            border: 3px dashed rgba(255, 255, 255, 0.5);
            border-radius: 16px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
        }

        .camera-corners {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 3px solid var(--primary-color);
        }

        .corner.top-left {
            top: 20px;
            left: 20px;
            border-right: none;
            border-bottom: none;
            border-radius: 8px 0 0 0;
        }

        .corner.top-right {
            top: 20px;
            right: 20px;
            border-left: none;
            border-bottom: none;
            border-radius: 0 8px 0 0;
        }

        .corner.bottom-left {
            bottom: 20px;
            left: 20px;
            border-right: none;
            border-top: none;
            border-radius: 0 0 0 8px;
        }

        .corner.bottom-right {
            bottom: 20px;
            right: 20px;
            border-left: none;
            border-top: none;
            border-radius: 0 0 8px 0;
        }

        /* Photo Preview */
        .photo-preview {
            width: 100%;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .hidden {
            display: none !important;
        }

        /* Status Info */
        .status-info-box {
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-icon {
            width: 48px;
            height: 48px;
            background: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .status-text {
            flex: 1;
        }

        .status-label {
            font-size: 12px;
            color: var(--text-light);
            margin-bottom: 2px;
        }

        .status-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-color);
        }

        .text-center.text-muted.mb-2 {
            text-align: center;
            color: var(--text-light);
            font-size: 13px;
            margin-bottom: 16px;
            margin-top: 16px;
            font-weight: 600;
            font-size: 16px;
        }

        /* Button */
        .btn-scan {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-scan.check-in {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
        }

        .btn-scan.check-out {
            background: linear-gradient(135deg, var(--danger-color) 0%, #DC2626 100%);
        }

        .btn-scan.permission {
            background: linear-gradient(135deg, var(--warning-color) 0%, #F59E0B 100%);
        }

        .btn-scan:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-scan:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-scan:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-scan i {
            font-size: 20px;
        }

        /* Spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-top: 16px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #D1FAE5;
            color: #065F46;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            border-left: 4px solid var(--danger-color);
        }

        .alert-info {
            background: #DBEAFE;
            color: #1E40AF;
            border-left: 4px solid #3B82F6;
        }

        .alert i {
            font-size: 18px;
        }

        /* Completed State */
        .completed-container {
            text-align: center;
            padding: 40px 24px;
        }

        .completed-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 40px;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .completed-text {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 8px;
        }

        .completed-subtext {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 24px;
        }

        .btn-back {
            display: inline-block;
            padding: 12px 32px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(73, 172, 243, 0.4);
        }

        /* Instruction */
        .instruction {
            text-align: center;
            color: var(--text-light);
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .instruction i {
            color: var(--primary-color);
        }
    </style>
</head>
<body>

<div class="scan-container">
    <!-- Header -->
    <div class="header">
        <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="header-title">Absensi Kehadiran</div>
        <div class="header-subtitle">Scan wajah Anda untuk absensi</div>
    </div>

    <!-- User Info -->
    <div class="user-info">
        <div class="user-name">{{ $user ? $user->name : 'Mode Absensi Umum' }}</div>
        <div class="user-details">
            <div class="user-nip">
                <i class="fas fa-id-card"></i>
                <span>{{ $user ? $user->attendance_code ?? '-' : 'Masukkan Kode Absensi' }}</span>
            </div>
            <div class="user-date">
                <i class="far fa-calendar-alt"></i>
                <span>{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    @if(!$attendance || ($attendance && !$attendance->time_out))
    <!-- Camera Section -->
    <div class="camera-section">
        @if(isset($activeLeave) && $activeLeave)
            <div class="alert alert-info border-0 shadow-sm mb-4" style="background: #eef2ff; color: #4338ca; border-left: 4px solid #6366f1;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-minus mr-3 fa-2x"></i>
                    <div>
                        <div class="font-weight-bold">Anda sedang Cuti</div>
                        <small>Anda memiliki pengajuan cuti yang {{ $activeLeave->status === 'APPROVED' ? 'telah disetujui' : 'sedang diajukan' }} untuk hari ini. Absensi dinonaktifkan.</small>
                    </div>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-scan" style="background: #6366f1; text-decoration: none;">
                <i class="fas fa-home"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        @else
            <!-- Attendance Code Input -->
            <div style="margin-bottom: 10px;">
                <label for="attendance_code_input" style="display: block; font-weight: 500; margin-bottom: 8px; color: #4B5563;">Kode Absensi (5-8 Digit)</label>
                <input type="text" id="attendance_code_input" class="form-control" 
                    value="{{ $user ? $user->attendance_code ?? '' : '' }}" 
                    placeholder="Masukkan Kode Absensi"
                    maxlength="8"
                    style="width: 100%; padding: 12px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 16px;">
            </div>

            <div class="instruction">
                <i class="fas fa-info-circle"></i>
                <span>Posisikan wajah Anda di dalam frame</span>
            </div>

            <div id="camera_container" class="camera-container">
                <video id="video" autoplay playsinline></video>
                <canvas id="canvas"></canvas>
                <div class="camera-overlay">
                    <div class="camera-frame"></div>
                    <div class="camera-corners">
                        <div class="corner top-left"></div>
                        <div class="corner top-right"></div>
                        <div class="corner bottom-left"></div>
                        <div class="corner bottom-right"></div>
                    </div>
                </div>
            </div>

            <div id="result_container" class="hidden">
                <img id="photo_preview" src="" class="photo-preview">
            </div>

            <!-- Status Info -->
            @if(!$attendance)
            <div class="status-info-box">
                <div class="status-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="status-text">
                    <div class="status-label">Status Absensi</div>
                    <div class="status-value">Belum Absen Masuk</div>
                </div>
            </div>

            <button id="btn-absen" class="btn-scan check-in">
                <i class="fas fa-camera"></i>
                <span>Ambil Foto & Absen</span>
            </button>

            <div class="text-center text-muted mb-2">- ATAU -</div>

            <a href="{{ route('attendance.shortcutPermission') }}" class="btn btn-scan permission">
                <i class="fas fa-file-alt mr-2"></i> Ajukan Izin / Sakit
            </a>
            @elseif(!$attendance->time_out)
            <div class="status-info-box">
                <div class="status-icon" style="background: var(--success-color);">
                    <i class="fas fa-check"></i>
                </div>
                <div class="status-text">
                    <div class="status-label">Masuk: {{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}</div>
                    <div class="status-value">Belum Absen Keluar</div>
                </div>
            </div>

            <button id="btn-absen" class="btn-scan check-out">
                <i class="fas fa-sign-out-alt"></i>
                <span>Absen Keluar</span>
            </button>
            @endif

            <div id="status_message"></div>
        @endif
    </div>
    @else
    <!-- Completed State -->
    <div class="completed-container">
        <div class="completed-icon">
            <i class="fas fa-check"></i>
        </div>
        <div class="completed-text">Absensi Selesai!</div>
        <div class="completed-subtext">Anda sudah menyelesaikan absensi hari ini</div>
        <a href="{{ Auth::check() ? route('attendance.dashboard') : url('/') }}" class="btn-back">
            Kembali ke {{ Auth::check() ? 'Dashboard' : 'Beranda' }}
        </a>
    </div>
    @endif
</div>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const btnAbsen = document.getElementById('btn-absen');
    const photoPreview = document.getElementById('photo_preview');
    const cameraContainer = document.getElementById('camera_container');
    const resultContainer = document.getElementById('result_container');
    const statusMessage = document.getElementById('status_message');

    // Access webcam
    if (video) {
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'user',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            console.error("Error accessing camera: ", err);
            statusMessage.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Kamera tidak dapat diakses. Pastikan izin kamera diberikan.</span>
                </div>`;
        });
    }

    if (btnAbsen) {
        btnAbsen.addEventListener('click', function() {
            // Disable button
            btnAbsen.disabled = true;
            const originalHTML = btnAbsen.innerHTML;
            btnAbsen.innerHTML = '<div class="spinner"></div><span>Mendapatkan Lokasi...</span>';

            if (!navigator.geolocation) {
                 statusMessage.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Browser anda tidak mendukung Geolocation.</span>
                    </div>`;
                btnAbsen.disabled = false;
                btnAbsen.innerHTML = originalHTML;
                return;
            }

            const codeInput = document.getElementById('attendance_code_input');
            const codeValue = codeInput ? codeInput.value : '';
            
            if (!codeValue) {
                 statusMessage.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Mohon masukkan Kode Absensi Anda.</span>
                    </div>`;
                btnAbsen.disabled = false;
                btnAbsen.innerHTML = originalHTML;
                return;
            }

            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                btnAbsen.innerHTML = '<div class="spinner"></div><span>Memproses Absensi...</span>';

                // Capture photo
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imageData = canvas.toDataURL('image/png');

                // Show preview
                photoPreview.src = imageData;
                cameraContainer.classList.add('hidden');
                resultContainer.classList.remove('hidden');

                fetch('{{ route("attendance.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        image: imageData,
                        attendance_code: codeValue,
                        latitude: latitude,
                        longitude: longitude
                    })
                    })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusMessage.innerHTML = `
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <span>${data.message}</span>
                            </div>
                        `;
                        // Redirect after 2 seconds
                        setTimeout(() => {
                            @if(Auth::check())
                                window.location.href = "{{ route('attendance.dashboard') }}";
                            @else
                                window.location.href = "{{ url('/') }}";
                            @endif
                        }, 2000);
                    } else {
                        statusMessage.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>${data.message || 'Terjadi kesalahan sistem.'}</span>
                            </div>
                        `;
                        // Reset UI if failed
                        btnAbsen.disabled = false;
                        btnAbsen.innerHTML = originalHTML;
                        cameraContainer.classList.remove('hidden');
                        resultContainer.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    statusMessage.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Terjadi kesalahan koneksi atau server.</span>
                        </div>`;
                    // Reset UI if failed
                    btnAbsen.disabled = false;
                    btnAbsen.innerHTML = originalHTML;
                    cameraContainer.classList.remove('hidden');
                    resultContainer.classList.add('hidden');
                });
            }, function(error) {
                // Error callback for Geolocation
                let msg = 'Gagal mendapatkan lokasi.';
                if(error.code == error.PERMISSION_DENIED) {
                    msg = 'Izin lokasi ditolak. Mohon izinkan akses lokasi.';
                } else if(error.code == error.POSITION_UNAVAILABLE) {
                    msg = 'Informasi lokasi tidak tersedia.';
                } else if(error.code == error.TIMEOUT) {
                    msg = 'Permintaan lokasi timeout.';
                }

                statusMessage.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>${msg}</span>
                    </div>`;
                btnAbsen.disabled = false;
                btnAbsen.innerHTML = originalHTML;
            });
        });
    }
</script>

</body>
</html>
