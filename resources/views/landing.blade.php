<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('AdminLTE/dist/img/vneu.avif') }}" />
    <title>HRIS System | PT Vneu</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2470daff;
            --primary-hover: #49acf3ff;
            --secondary: #10b981;
            --dark: #1f2937;
            --light: #f9fafb;
            --white: #ffffff;
            --glass: rgba(255, 255, 255, 0.8);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Navbar */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            height: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 8%;
            background: var(--glass);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vneu {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn-auth {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8%;
            background: linear-gradient(135deg, #f5f7ff 0%, #ffffff 100%);
            padding-top: 80px;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .hero-content h1 span {
            color: var(--primary);
        }

        .hero-content p {
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .hero-image {
            position: relative;
            flex: 1;
            display: flex;
            justify-content: flex-end;
        }

        .hero-image img {
            max-width: 90%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Features */
        .features {
            padding: 100px 8%;
            text-align: center;
        }

        .section-header {
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            transition: 0.3s;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 30px rgba(0,0,0,0.05);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 20px;
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: var(--white);
            padding: 60px 8% 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-logo {
            color: var(--white);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: block;
            text-decoration: none;
        }

        .footer-links h4 {
            margin-bottom: 20px;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #9ca3af;
            text-decoration: none;
            transition: 0.3s;
        }

        .footer-links a:hover {
            color: var(--white);
        }

        .footer-bottom {
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            color: #6b7280;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .hero {
                flex-direction: column;
                text-align: center;
                height: auto;
                padding-bottom: 60px;
            }
            .hero-content h1 {
                font-size: 2.5rem;
            }
            .hero-image {
                margin-top: 50px;
                justify-content: center;
            }
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="{{ url('https://vneu.co.id') }}" class="logo">
            <img src="{{ asset('AdminLTE/dist/img/vneu.avif') }}" alt="icon" class="vneu">
            <i class="fas fa-Users-cog"></i> HRIS System | PT Vneu
        </a>
        <ul class="nav-links">
            <li><a href="#features">Fitur</a></li>
            <li><a href="#about">Tentang</a></li>
            <li><a href="#contact">Kontak</a></li>
        </ul>
        <div class="btn-auth">
            <a href="{{ route('login') }}" class="btn btn-primary px-5 shadow-lg">Login Staff</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Kelola SDM Anda dengan <span>Lebih Cerdas</span></h1>
            <p>Sistem Informasi Sumber Daya Manusia (HRIS) yang komprehensif untuk membantu perusahaan Anda mengelola karyawan, absensi, payroll, dan rekrutmen dalam satu platform terpadu.</p>
            <div class="btn-auth">
                <a href="{{ route('attendance.scan') }}" class="btn btn-primary">Absen Sekarang</a>
                <a href="#features" class="btn btn-outline">Pelajari Lebih Lanjut</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="HRIS Dashboard Preview">
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <h2>Solusi Lengkap untuk HR</h2>
            <p>Fitur-fitur unggulan yang memudahkan operasional HR diperusahaan Anda.</p>
        </div>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <h3>Manajemen Karyawan</h3>
                <p>Database terpusat untuk semua informasi karyawan, dari data pribadi hingga riwayat jabatan.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
                <h3>Absensi & Presensi</h3>
                <p>Pantau kehadiran karyawan secara real-time dengan integrasi sistem yang mudah.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-money-bill-wave"></i></div>
                <h3>Payroll & Gaji</h3>
                <p>Otomatisasi perhitungan gaji, tunjangan, dan potongan dengan akurasi tinggi.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-user-plus"></i></div>
                <h3>Rekrutmen</h3>
                <p>Kelola lowongan kerja dan pantau proses seleksi pelamar dengan lebih efisien.</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-info">
                <a href="/" class="footer-logo">HRIS System</a>
                <p>Solusi modern untuk manajemen sumber daya manusia yang efisien dan terintegrasi.</p>
            </div>
            <div class="footer-links">
                <h4>Perusahaan</h4>
                <ul>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Karir</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Produk</h4>
                <ul>
                    <li><a href="#">Fitur</a></li>
                    <li><a href="#">Harga</a></li>
                    <li><a href="#">Testimoni</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Bantuan</h4>
                <ul>
                    <li><a href="#">Pusat Bantuan</a></li>
                    <li><a href="#">Kontak Kami</a></li>
                    <li><a href="#">Keamanan</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 HRIS System. Seluruh hak cipta dilindungi.
        </div>
    </footer>
</body>
</html>
