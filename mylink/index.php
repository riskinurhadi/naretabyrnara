<?php

// ===================================================================
// PENGATURAN KONTEN HALAMAN (UBAH SEMUANYA DI SINI)
// ===================================================================

// Informasi Profil
$profile_picture = 'https://kemusukkidul.com/berkas/files/G(1).png'; // Nama file logo/foto Anda. Letakkan di folder yang sama dengan file ini.
$business_name   = 'rnara.id';
$description     = 'Solusi Digital Kreatif untuk Kebutuhan Bisnis Anda. Klik salah satu link di bawah untuk terhubung!';

// Daftar Link (Tombol)
// Format: ['Teks Tombol', 'URL Tujuan Lengkap', 'Kelas Ikon dari Bootstrap Icons'],
$links = [
    ['Website Resmi', 'https://www.rnara.my.id', 'bi-globe'],
    ['Chat Nareta Assistant ', 'https://rnara.my.id/nareta.html', 'bi-chat-left-text-fill'],
    ['Hubungi via WhatsApp', 'https://wa.me/6282371869118', 'bi-whatsapp'],
    ['Follow Instagram Kami', 'https://www.instagram.com/rnara.id', 'bi-instagram'],
    // ['Toko di Tokopedia', 'https://www.tokopedia.com/tokoanda', 'bi-cart3'],
    // ['Toko di Shopee', 'https://shopee.co.id/tokoanda', 'bi-bag-check-fill'],
    // Tambahkan link baru di sini dengan format yang sama
    // ['Teks Baru', 'https://url-baru.com', 'bi-star-fill'], 
];

// Teks di bagian bawah halaman
// Anda bisa menggunakan &copy; untuk simbol copyright
$footer_text = 'Copyright &copy; ' . date('Y') . ' rnara.id. All Rights Reserved.';

// ===================================================================
// AKHIR BAGIAN PENGATURAN (Tidak perlu mengubah kode di bawah ini)
// ===================================================================

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($business_name); ?> - Halaman Link</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-color: #1a73e8;
            --brand-color-dark: #155cb8;
            --background-color: #f8f9fa;
            --text-color: #212529;
            --card-background: #ffffff;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 40px);
        }
        .profile-container {
            background-color: var(--card-background);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px;
            max-width: 680px;
            width: 100%;
            text-align: center;
            margin: 0 15px;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--brand-color);
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(26, 115, 232, 0.3);
        }
        .business-name {
            font-weight: 700;
            font-size: 1.75rem;
        }
        .description {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .link-button {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--brand-color);
            color: white;
            border: none;
            padding: 15px 20px;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
        }
        .link-button .link-icon {
            margin-right: 12px;
            font-size: 1.2rem;
        }
        .link-button:hover, .link-button:focus {
            background-color: var(--brand-color-dark);
            color: white;
            transform: scale(1.03);
            box-shadow: 0 8px 20px rgba(26, 115, 232, 0.25);
        }
        .animated {
            opacity: 1;
            transform: translateY(0);
        }
        footer {
            margin-top: 30px;
            font-size: 0.85rem;
            color: #adb5bd;
        }
    </style>
</head>
<body>
    
    <main class="main-content">
        <div class="profile-container">
            
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Logo <?php echo htmlspecialchars($business_name); ?>" class="profile-pic">
            
            <h1 class="business-name"><?php echo htmlspecialchars($business_name); ?></h1>
            
            <p class="description"><?php echo htmlspecialchars($description); ?></p>
            
            <div class="d-grid gap-2">
                <?php foreach ($links as $link): ?>
                    <a href="<?php echo htmlspecialchars($link[1]); ?>" target="_blank" rel="noopener noreferrer" class="link-button">
                        <?php if (!empty($link[2])): ?>
                            <i class="<?php echo htmlspecialchars($link[2]); ?> link-icon"></i>
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($link[0]); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <footer>
                <p><?php echo $footer_text; ?></p>
            </footer>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const linkButtons = document.querySelectorAll('.link-button');
            linkButtons.forEach((button, index) => {
                setTimeout(() => {
                    button.classList.add('animated');
                }, index * 120);
            });
        });
    </script>
</body>
</html>