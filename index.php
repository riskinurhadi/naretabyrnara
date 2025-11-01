<!--**-->
<!--* Coded with coffee and passion by Riski Nurhadi.-->
<!--* For inquiries, drop a line to rizkibinmangtrisno@gmail.com-->
<!--* Visit my Portofolio on:  riskinurhadi.my.id-->
<!--**-->

<?php
// Sertakan file koneksi
session_start();
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasa Website & Desain Grafis | Profil </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<style>
        :root {
            /* Updated palette for a modern glassmorphism look */
            --primary-color: #6c63ff; /* soft indigo */
            --primary-color-2: #7b8cff;
            --light-gray: #6c757d;
            --dark-gray: #444;
            --card-bg: rgba(255,255,255,0.55);
            --card-border: rgba(255,255,255,0.18);
            --glass-overlay: rgba(255,255,255,0.22);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f7f9ff 0%, #eef2ff 50%, #fbfbff 100%);
            -webkit-font-smoothing: antialiased;
            color: #111;
        }

        /* --- DESKTOP NAVBAR STYLES  --- */
        .header-desktop {
            position: sticky;
            top: 1rem;
            z-index: 1000;
            padding: 0 1rem;
        }

        .navbar-custom {
            background: linear-gradient(180deg, rgba(255,255,255,0.6), rgba(255,255,255,0.45));
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--card-border);
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            transition: all 0.25s ease-in-out;
            height: 65px;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .navbar-custom .navbar-nav .nav-link {
            color: var(--dark-gray);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.3s;
        }

        .navbar-custom .navbar-nav .nav-link:hover,
        .navbar-custom .navbar-nav .nav-link.active {
            color: var(--primary-color);
        }
        
/* --- MOBILE NAVBAR STYLES --- */
.mobile-bottom-navbar {
    /* Positioning & Centering */
    position: fixed;
    z-index: 1000;
    bottom: 1rem;
    left: 50%; 
    transform: translateX(-50%); 

    /* Sizing */
    width: calc(100% - 2rem); 
    max-width: 500px; 
    
    /* Visuals */
    display: flex; /* Untuk menata item di dalamnya */
    background: linear-gradient(180deg, rgba(255,255,255,0.6), rgba(255,255,255,0.5));
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 0.6rem 0;
    border-radius: 50px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
}

.mobile-nav-item {
    flex: 1; 
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: var(--dark-gray);
    font-size: 0.65rem;
    font-weight: 500;
    text-align: center;
    transition: color 0.2s ease;
}
        
.mobile-nav-item i {
    font-size: 1.1rem;
    margin-bottom: 3px;
}

.mobile-nav-item.active,
.mobile-nav-item:hover {
    color: var(--primary-color);
}
        
/* --- STATS CARD SECTION STYLES --- */
.stats-section-cards {
    background-color: transparent; 
}

        /* Glassy cards used across the site */
        .card,
        .stat-card,
        .pricing-card,
        .portfolio-card,
        .testimonial-card,
        .info-card,
        .cta-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 12px 40px rgba(20,20,40,0.06);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            color: #111;
        }

        .card:hover,
        .stat-card:hover,
        .portfolio-card:hover,
        .pricing-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 50px rgba(20,20,40,0.09);
        }

        .stat-card .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(108,99,255,0.12), rgba(123,140,255,0.08));
            color: var(--primary-color);
            border-radius: 50%;
            font-size: 1.75rem;
            backdrop-filter: blur(6px);
        }
        

    /* Penyesuaian padding body di mobile agar konten tidak tertutup navbar bawah */
        @media (max-width: 991.98px) {
            body {
                padding-bottom: 90px; 
            }
        }

        /* --- HERO SECTION STYLES --- */
        .hero-section {
            padding: 8rem 0 6rem 0;
            background: linear-gradient(180deg, rgba(236,243,255,0.6) 0%, rgba(255,255,255,0.2) 70%);
            margin-top: -5rem;
            z-index: 1;
        }
        
        .hero-section .display-6{
          font-weight:700;
          line-height:1.3
        }
        .hero-section .lead{
          color:var(--light-gray);
          font-weight:400
        }
          .badge-pill-custom{
            display:inline-block;
            padding:.5rem 1rem;
            font-size:.85rem;
            font-weight:600;
            color:var(--primary-color);
            background-color: rgba(108,99,255,0.08);
            border-radius:50px;
            margin-bottom:1.5rem
          }
          .btn-primary-custom {
              background: linear-gradient(90deg, var(--primary-color), var(--primary-color-2));
              border: none;
              color: #fff;
              padding: .75rem 1.5rem;
              font-weight: 600;
              border-radius: 50px;
              transition: transform .18s ease, box-shadow .18s ease;
              height: 50px;
              width: auto;
              box-shadow: 0 8px 30px rgba(108,99,255,0.12);
            }
              .btn-primary-custom:hover{
                transform: translateY(-3px);
                box-shadow: 0 18px 40px rgba(108,99,255,0.12);
              }
          .hero-illustration {
    max-width: 80%; 
    height: auto; 
}
          
          /* --- ABOUT US SECTION --- */
.about-us-section {
    background-color: transparent;
}

.checklist-icon {
    color: var(--primary-color);
    font-size: 1.25rem;
    line-height: 1.6; 
}

.about-us-section img {
    box-shadow: 0 12px 36px rgba(20,20,40,0.06); 
    border-radius: .75rem;
}

/* --- HOW WE WORK SECTION --- */
.process-item {
    background: linear-gradient(180deg, rgba(255,255,255,0.56), rgba(255,255,255,0.42));
    border: 1px solid rgba(0,0,0,0.03);
    border-radius: 1rem;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    transition: all 0.25s ease;
}

.process-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 36px rgba(20,20,40,0.06);
}

/* Trik untuk membuat angka di background */
.process-item::before {
    content: attr(data-step); 
    position: absolute;
    top: 50%;
    left: 2rem;
    transform: translateY(-50%);
    font-size: 5rem;
    font-weight: 800; 
    color: rgba(0, 0, 0, 0.03); 
    z-index: 0; 
}

/* Wrapper untuk ikon dan teks */
.process-item .d-flex {
    position: relative;
    z-index: 1; 
}

.process-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 65px;
    height: 65px;
    background-color: var(--primary-color);
    color: #ffffff;
    border-radius: 1rem; 
    font-size: 2rem;
    flex-shrink: 0; 
}

/* --- SERVICES SECTION --- */
.services-section {
    background-color: transparent;
}

.title-divider {
    width: 60px;
    height: 3px;
    background-color: var(--primary-color);
    border-radius: 5px;
    margin-top: 1rem;
}

.service-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 65px;
    height: 65px;
    background: linear-gradient(135deg, rgba(108,99,255,0.12), rgba(123,140,255,0.08)); 
    color: var(--primary-color);
    border-radius: 1rem;
    font-size: 2rem;
    flex-shrink: 0;
    backdrop-filter: blur(6px);
}

.learn-more-link {
    color: var(--primary-color);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.learn-more-link:hover {
    text-decoration: underline;
}

.learn-more-link i {
    transition: transform 0.3s ease;
}

.learn-more-link:hover i {
    transform: translateX(5px); 
}

/* --- PORTFOLIO SECTION --- */
.portfolio-filters .nav-link {
    color: var(--dark-gray);
    font-weight: 500;
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
}

.portfolio-filters .nav-link.active {
    background-color: var(--primary-color);
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.portfolio-card .portfolio-img-wrapper {
    overflow: hidden;
}

.portfolio-card .card-img-top {
            transition: transform 0.4s ease;
            height: 220px; 
            object-fit: cover; 
        }

.portfolio-card:hover .card-img-top {
    transform: scale(1.05); 
}

.portfolio-category {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--primary-color);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
}

/* --- PRICING SECTION --- */
.pricing-card {}

.pricing-card h4 {
    font-size: 1.25rem;
}

.pricing-card p, .pricing-card ul li {
    font-size: 0.9rem;
}

.price-display .price-amount {
    font-size: 1.4rem; 
    font-weight: 700;
    color: var(--primary-color);
}

.price-display .price-period {
    font-size: 0.9rem; 
    color: var(--light-gray);
}

.pricing-card ul li i {
    font-size: 1.1rem; 
    line-height: 1.5;
}

.highlighted-plan {
    background: linear-gradient(180deg, var(--primary-color), var(--primary-color-2));
    color: #ffffff;
    border: none;
    transform: scale(1.03);
    z-index: 10;
}

.highlighted-plan:hover {
    transform: scale(1.05);
}

.highlighted-plan .price-amount,
.highlighted-plan .price-period,
.highlighted-plan p,
.highlighted-plan h4,
.highlighted-plan h6,
.highlighted-plan ul li,
.highlighted-plan ul li i {
    color: #ffffff;
}

.popular-badge {
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #ffffff;
    color: var(--primary-color);
    padding: 0.25rem 1rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

/* --- TESTIMONIALS SECTION --- */
.testimonials-section {
    background-color: transparent;
}

.testimonial-card { padding: 1rem; }

.testimonial-text {
    font-style: italic;
    color: var(--dark-gray);
}

.testimonial-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.rating .bi-star-fill {
    color: #ffc107; 
}

.swiper-pagination-bullet {
    width: 10px;
    height: 10px;
    background-color: #d1d1d1;
    opacity: 1;
}

.swiper-pagination-bullet-active {
    background-color: var(--primary-color);
}

/* --- CONTACT SECTION --- */
.info-card { }

.info-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, rgba(108,99,255,0.12), rgba(123,140,255,0.08));
    color: var(--primary-color);
    border-radius: 50%;
    font-size: 1.75rem;
}

/* Kustomisasi Form Kontak */
        .contact-form .form-control,
        .contact-form .form-select,
        .contact-form .input-group-text {
            background: rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.14);
            padding-top: 0.9rem;
            padding-bottom: 0.9rem;
            backdrop-filter: blur(6px);
        }

        .contact-form .input-group-text {
    border-right: none;
    border-top-left-radius: 0.75rem;
    border-bottom-left-radius: 0.75rem;
    color: var(--primary-color);
}

        .contact-form .form-control,
        .contact-form .form-select {
    border-left: none;
    border-top-right-radius: 0.75rem;
    border-bottom-right-radius: 0.75rem;
}

        .contact-form .form-control:focus,
        .contact-form .form-select:focus {
    box-shadow: none;
    border-color: var(--primary-color);
}

        .contact-form textarea.form-control {
    border-left: none;
}

/* --- FOOTER SECTION (Revised Colors) --- */
#footer { font-size: 14px; }

#footer .footer-top { background-color: rgba(233,236,239,0.6); color: #32353a; padding: 60px 0 5px 0; }

#footer .footer-top .footer-info .logo span { color:#32353a; }

#footer .footer-top .footer-info p { font-size: 14px; padding-top: 1rem; }

#footer .footer-top .social-links a { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3); color:#32353a; margin-right: 10px; transition: all 0.3s ease; }

#footer .footer-top .social-links a:hover { color: #484d55; background-color: var(--primary-color); border-color: var(--primary-color); }

#footer .footer-top h4 { font-size: 16px; font-weight: bold; color: #32353a; position: relative; padding-bottom: 12px; }

#footer .footer-top .footer-links { margin-bottom: 30px; }

#footer .footer-top .footer-links ul { list-style: none; padding: 0; margin: 0; }

#footer .footer-top .footer-links ul li { padding: 10px 0; display: flex; align-items: center; }

#footer .footer-top .footer-links ul i { padding-right: 8px; color: var(--primary-color); font-size: 12px; }

#footer .footer-top .footer-links ul a { color: #32353a; transition: 0.3s; display: inline-block; line-height: 1; text-decoration: none; }

#footer .footer-top .footer-links ul a:hover { color: #45494e; }

#footer .footer-top .footer-contact p { line-height: 26px; }

#footer .footer-bottom { background-color: rgba(233,236,239,0.6); color: #32353a; padding: 5px 0; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.1); }

#footer .copyright { margin-bottom: 5px; }

#footer .credits { font-size: 13px; }

#footer .credits a { color: var(--primary-color); text-decoration: none; transition: 0.3s; }

#footer .credits a:hover { color: #41444b; }

/* --- CTA SECTION  --- */
        .cta-section { background: transparent; }

        .cta-card {
            background: linear-gradient(180deg, rgba(255,255,255,0.64), rgba(255,255,255,0.5));
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(20,20,40,0.06);
            overflow: hidden;
            width: 100%;
            margin: 0;
        }

/* Mengatur ulang ukuran elemen di dalam card */
 .cta-content h2 { font-size: 2.0rem; line-height: 1.3; }

.cta-content p.text-muted { font-size: 1rem; }

.cta-person-img { transform: scale(1.0) translateX(10px); }

.btn-outline-custom { color: var(--dark-gray); border: 1px solid rgba(0,0,0,0.06); font-weight: 500; transition: all 0.3s ease; font-size: 0.9rem; padding: 0.5rem 1rem; }

.btn-outline-custom:hover { color: var(--primary-color); border-color: var(--primary-color); background-color: rgba(108,99,255,0.06); }

.floating-nugget {
    position: absolute;
    background: rgba(255,255,255,0.48);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    padding: 0.65rem 0.9rem;
    border-radius: 0.75rem;
    box-shadow: 0 10px 30px rgba(20,20,40,0.06);
    display: inline-flex;
    align-items: center;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid rgba(255,255,255,0.14);
}

/* --- Floating CS Button --- */
.floating-cs-button {
    position: fixed;
    width: 55px;
    height: 55px;
    bottom: 100px; 
    right: 20px;
    background-color: #0099ff; 
    color: #FFF;
    border-radius: 50%;
    text-align: center;
    font-size: 2rem; /* Ukuran ikon */
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    z-index: 1001; 
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.floating-cs-button:hover { transform: scale(1.1); box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.28); }

/* Menambahkan 'ruang' di atas section saat navigasi anchor link */
section[id] { scroll-margin-top: 100px; }

/* =========================== */
/* KLIEN & PARTNER SECTION STYLES */
/* =========================== */
        #clients-partners { background-color: transparent; }

#clients-partners .client-logo { opacity: 0.85; transition: all 0.3s ease; width: auto; height: 80px; object-fit: contain; border-radius: 0.5rem; }

#clients-partners .client-logo:hover { opacity: 1; transform: scale(1.05); }

 </style>



    <header class="header-desktop d-none d-lg-block">
        <div class="container">
          <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
            <a class="navbar-brand" style="color:var(--primary-color);" href="#">rnara.id</a> 
            <div class="collapse navbar-collapse">
              <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                  <a class="nav-link active" href="#">Home</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#about-us">Tentang Kami</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#Layanan">Layanan</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#Portofolio">Portofolio</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#Pricing">Pricing</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#Kontak">Kontak</a>
                </li>
              </ul>
              <a href="https://rnara.my.id/nareta.html" class="btn btn-primary-custom text-light text-end" style="height: 50px;">Chat Nareta</a>
            </div>
          </nav>
        </div>
    </header>

<nav class="mobile-bottom-navbar d-lg-none">
    <a href="#hero" class="mobile-nav-item active">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </a>
    <a href="#about-us" class="mobile-nav-item">
        <i class="bi bi-building-fill"></i> <span>About</span>
    </a>
    <a href="#Layanan" class="mobile-nav-item">
        <i class="bi bi-file-earmark-check-fill"></i> <span>Service</span>
    </a>
    <a href="#Pricing" class="mobile-nav-item">
        <i class="bi bi-box2-fill"></i> <span>Package</span>
    </a>
    <a href="#kontak" class="mobile-nav-item">
        <i class="bi bi-envelope-fill"></i>
        <span>Contact</span>
    </a>
</nav>
    

<div class="overflow-hidden">
    <section class="hero-section">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-6 text-center order-1 order-lg-2 mb-4 mb-lg-0">
                <img src="img/hero.png" alt="Ilustrasi Tim sedang menganalisis data" class="img-fluid hero-illustration text-end">
            </div>

            <div class="col-lg-6 text-center text-lg-start order-2 order-lg-1">
                <p class="badge-pill-custom">
                    <i class="bi bi-lightbulb-fill me-2"></i> Solusi Jitu
                </p>
                <h1 class="display-6 fw-bold mb-4">Percepat pertumbuhan bisnis melalui Web dan Desain inovatif</h1>
                <p class="lead mb-4">
                    Kami percaya bahwa setiap brand memiliki potensi unik. Misi kami adalah menggali potensi tersebut dan mengubahnya menjadi keunggulan kompetitif melalui desain yang memikat dan teknologi website yang handal.
                </p>
                <a href="#Statistik" class="btn btn-primary-custom btn-lg text-light">Selengkapnya</a>
            </div>
            
        </div>
    </div>
</section>
    
    <section id="Statistik" class="stats-section-cards py-5">
    <div class="container">
        <div class="row g-4">

            <div class="col-md-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-code-square"></i>
                        </div>
                        <h2 class="card-title fw-bold text-primary mb-1">10+</h2>
                        <p class="card-text text-muted">Proyek Website</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-palette-fill"></i>
                        </div>
                        <h2 class="card-title fw-bold text-primary mb-1">100+</h2>
                        <p class="card-text text-muted">Proyek Desain</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h2 class="card-title fw-bold text-primary mb-1">50+</h2>
                        <p class="card-text text-muted">Klien</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-building-fill"></i>
                        </div>
                        <h2 class="card-title fw-bold text-primary mb-1">5+</h2>
                        <p class="card-text text-muted">Instansi Percaya</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="about-us" class="about-us-section py-5">
    <div class="container">
        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <div class="about-us-content">
                    <h6 class="text-primary text-uppercase fw-bold">Tentang Kami</h6>
                    <h2 class="display-6 fw-bold mb-3">Melejitkan Potensi dengan Strategi Kreatif</h2>
                    <p class="text-muted mb-4">
                         Kami adalah partner digital Anda, menyediakan layanan pembuatan website profesional dan desain grafis kreatif untuk meningkatkan citra brand dan jangkauan pasar Anda melalui beberapa strategi.
                    </p>
                    
                    <div class="checklist mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="checklist-icon me-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <span>Pendekatan kolaboratif untuk memahami visi dan tujuan Anda secara mendalam.</span>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <div class="checklist-icon me-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <span>Eksekusi desain yang presisi dan pengembangan website yang sesuai standar terkini.</span>
                        </div>
                         <div class="d-flex align-items-start">
                            <div class="checklist-icon me-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <span>Dukungan penuh dan komitmen untuk memastikan kepuasan dan kesuksesan jangka panjang Anda.</span>
                        </div>
                    </div>

                    <a href="#Proses" class="btn btn-primary-custom text-light">
                        Selengkapnya <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-3 align-items-center">
                    <div class="col-7">
                        <img src="https://images.pexels.com/photos/3184418/pexels-photo-3184418.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" 
                             alt="Tim sedang berdiskusi di kantor" class="img-fluid rounded-3 w-100">
                    </div>
                    <div class="col-5">
                        <img src="https://images.pexels.com/photos/3184306/pexels-photo-3184306.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" 
                             alt="Diskusi tim kreatif" class="img-fluid rounded-3 mb-3 w-100">
                        <img src="https://images.pexels.com/photos/3861964/pexels-photo-3861964.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" 
                             alt="Desainer sedang bekerja dengan laptop" class="img-fluid rounded-3 w-100">
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="Proses" class="how-we-work-section py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center text-center mb-4">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold">Proses Kerja Kami</h2>
                <div class="title-divider mx-auto"></div>
                <p class="lead text-muted mt-2">
                    Kami mengikuti alur kerja yang terstruktur dan transparan untuk memastikan setiap proyek berjalan lancar dan memberikan hasil terbaik tepat waktu.
                </p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="process-item" data-step="01">
                    <div class="d-flex align-items-center">
                        <div class="process-icon me-4">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div class="process-text">
                            <h4 class="fw-bold mb-1">Perencanaan Proyek</h4>
                            <p class="text-muted mb-0">Memahami tujuan, menentukan lingkup, dan menyusun roadmap proyek yang detail.</p>
                        </div>
                    </div>
                </div>
                <div class="process-item" data-step="02">
                    <div class="d-flex align-items-center">
                        <div class="process-icon me-4">
                            <i class="bi bi-gear-wide-connected"></i>
                        </div>
                        <div class="process-text">
                            <h4 class="fw-bold mb-1">Fase Pengembangan</h4>
                            <p class="text-muted mb-0">Mulai dari desain UI/UX hingga penulisan kode (coding) sesuai dengan rencana.</p>
                        </div>
                    </div>
                </div>
                <div class="process-item" data-step="03">
                    <div class="d-flex align-items-center">
                        <div class="process-icon me-4">
                            <i class="bi bi-search"></i>
                        </div>
                        <div class="process-text">
                            <h4 class="fw-bold mb-1">Pengujian & QA</h4>
                            <p class="text-muted mb-0">Memastikan semua fitur berfungsi dengan baik, responsif, dan tanpa bug.</p>
                        </div>
                    </div>
                </div>
                <div class="process-item" data-step="04">
                    <div class="d-flex align-items-center">
                        <div class="process-icon me-4">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <div class="process-text">
                            <h4 class="fw-bold mb-1">Peluncuran & Dukungan</h4>
                            <p class="text-muted mb-0">Menayangkan website ke publik dan menyediakan dukungan pasca-peluncuran.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="Layanan" class="services-section py-5">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold">Layanan Kami</h2>
                <div class="title-divider mx-auto"></div>
                <p class="lead text-muted mt-2">
                    Kami menyediakan solusi digital yang komprehensif untuk membantu brand Anda tumbuh dan bersinar di dunia online.
                </p>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-lg-6">
                <div class="service-item d-flex">
                    <div class="service-icon me-4">
                        <i class="bi bi-code-slash"></i>
                    </div>
                    <div class="service-content">
                        <h4 class="fw-bold mb-2">Web Development</h4>
                        <p class="text-muted">Membangun website dari nol dengan performa tinggi, aman, dan disesuaikan penuh dengan kebutuhan bisnis unik Anda.</p>
                        <a href="#" class="learn-more-link">
                            Pelajari Lebih Lanjut <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="service-item d-flex">
                    <div class="service-icon me-4">
                        <i class="bi bi-phone"></i>
                    </div>
                    <div class="service-content">
                        <h4 class="fw-bold mb-2">Mobile App Solutions</h4>
                        <p class="text-muted">Solusi aplikasi mobile inovatif untuk platform iOS dan Android yang memberikan pengalaman pengguna terbaik.</p>
                        <a  class="learn-more-link">
                            Coming Soon <i class="
                            <!--bi bi-arrow-right-->
                            "></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="service-item d-flex">
                    <div class="service-icon me-4">
                        <i class="bi bi-palette2"></i>
                    </div>
                    <div class="service-content">
                        <h4 class="fw-bold mb-2">UI/UX Design</h4>
                        <p class="text-muted">Merancang antarmuka yang tidak hanya indah secara visual tetapi juga intuitif dan mudah digunakan oleh target audiens Anda.</p>
                        <a href="#" class="learn-more-link">
                            Pelajari Lebih Lanjut <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="service-item d-flex">
                    <div class="service-icon me-4">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="service-content">
                        <h4 class="fw-bold mb-2">Digital Marketing</h4>
                        <p class="text-muted">Meningkatkan visibilitas online dan menjangkau lebih banyak pelanggan melalui strategi SEO, SEM, dan media sosial.</p>
                        <a  class="learn-more-link">
                            Coming Soon <i class="
                            <!--bi bi-arrow-right-->
                            "></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="Portofolio" class="portfolio-section py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center text-center mb-3">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold">Portfolio</h2>
                <div class="title-divider mx-auto"></div>
                <p class="lead text-muted mt-3">
                    Berikut adalah beberapa karya pilihan yang telah kami selesaikan dengan bangga untuk para klien hebat kami.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card portfolio-card h-100">
                    <div class="portfolio-img-wrapper">
                        <img src="img/tampingan.png" class="card-img-top" alt="Proyek Web Design">
                    </div>
                    <div class="card-body">
                        <p class="portfolio-category">Web Development</p>
                        <h5 class="card-title fw-bold">Sistem Informasi Desa Tampingan</h5>
                        <p class="card-text text-muted small">Develop Sistem Informasi Desa Tampingan, Kecamatan Boja, Kabupaten Kendal, Jawa Tengah.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card portfolio-card h-100">
                    <div class="portfolio-img-wrapper">
                        <img src="img/waylaga.png" class="card-img-top" alt="Proyek Web Design">
                    </div>
                    <div class="card-body">
                        <p class="portfolio-category">Web Development</p>
                        <h5 class="card-title fw-bold">Sistem Informasi SD Negeri 4 Way Laga</h5>
                        <p class="card-text text-muted small">Develop Sistem Informasi SD Negeri 4 Way Laga Butuah, Bandar Lampung, Lampung.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card portfolio-card h-100">
                    <div class="portfolio-img-wrapper">
                        <img src="img/mataneka.png" class="card-img-top" alt="Proyek Web Design">
                    </div>
                    <div class="card-body">
                        <p class="portfolio-category">Web Development</p>
                        <h5 class="card-title fw-bold">Lulusku by Mataneka</h5>
                        <p class="card-text text-muted small">Develop Aplikasi Portal Kelulusan Madrasah Tsanawiyah Negeri 1 Way Kanan Lampung, berbasis Web.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card portfolio-card h-100">
                     <div class="portfolio-img-wrapper">
                        <img src="img/kemusuk.png" class="card-img-top" alt="Proyek Graphics">
                    </div>
                    <div class="card-body">
                        <p class="portfolio-category">Web Development</p>
                        <h5 class="card-title fw-bold">Sistem Informasi Dusun</h5>
                        <p class="card-text text-muted small">Develop Sistem Informasi Dusun Kemusuk Kidul, Yogyakarta.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card portfolio-card h-100">
                     <div class="portfolio-img-wrapper">
                        <img src="img/rm.png" class="card-img-top" alt="Proyek Branding">
                    </div>
                    <div class="card-body">
                        <p class="portfolio-category">Web Development</p>
                        <h5 class="card-title fw-bold">Portal Kelulusan</h5>
                        <p class="card-text text-muted small">Develop Portal Kelulusan Raudlatul Muta'allimin, Lampung..</p>
                    </div>
                </div>
            </div>
             <div class="col-md-6 col-lg-4">
                <div class="card portfolio-card h-100">
                     <div class="portfolio-img-wrapper">
                        <img src="img/ikram.png" class="card-img-top" alt="Proyek Web App">
                    </div>
                    <div class="card-body">
                        <p class="portfolio-category">Graphics Design</p>
                        <h5 class="card-title fw-bold">Logo IKRAM</h5>
                        <p class="card-text text-muted small">Desain Logo Ikatan Keluarga Raudlatul Muta'allimin, Lampung.</p>
                    </div>
                </div>
            </div>
             <div class="col-md-6 col-lg-4">
                <div class="card portfolio-card h-100">
                     <div class="portfolio-img-wrapper">
                        <img src="img/bannerskr.png" class="card-img-top" alt="Proyek UI/UX">
                    </div>
                    <div class="card-body">
                        <p class="portfolio-category">Graphics Design</p>
                        <h5 class="card-title fw-bold">Banner Kit</h5>
                        <p class="card-text text-muted small">Desain Banner Kit Ulang Tahun Sri Karang Rejo, Sumatera Selatan.</p>
                    </div>
                </div>
            </div>
             <div class="col-md-6 col-lg-4">
                <div class="card portfolio-card h-100">
                     <div class="portfolio-img-wrapper">
                        <img src="img/figmahimatik.png" class="card-img-top" alt="Proyek Motion Graphics">
                    </div>
                    <div class="card-body">
                        <p class="portfolio-category">Ui Design</p>
                        <h5 class="card-title fw-bold">Web Himatik UAA</h5>
                        <p class="card-text text-muted small">Desain UI Website Profil HIMATIK UAA periode 2023/2024, Yogyakarta.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="Pricing" class="pricing-section py-5">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold">Harga Layanan</h2>
                <div class="title-divider mx-auto"></div>
                <p class="lead text-muted mt-3">
                    Order yang paling sesuai dengan kebutuhan dan anggaran Anda. Kami menawarkan solusi fleksibel untuk semua skala bisnis.
                </p>
            </div>
        </div>

        <div class="row g-3 justify-content-center">
            
            <div class="col-lg-3">
                <div class="card pricing-card h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <h4 class="fw-bold ">Basic</h4>
                        <span class="price-period mt-3">Start from</span>
                        <div class="price-display mb-3">
                            <span class="price-amount">Rp 100.000</span>
                            <span class="price-period">/ project</span>
                        </div>
                        <p class="text-muted">Cocok untuk personal branding atau bisnis skala kecil yang baru memulai.</p>
                        <h6 class="fw-bold mt-3">Fitur Termasuk:</h6>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Desain 3 Halaman</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Domain & Hosting 1 Tahun</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Revisi Desain 2x</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-x-circle-fill  text-danger me-2"></i> <span>Suport 365 hari</span></li>
                        </ul>
                        <div class="mt-auto">
                           <a href="https://wa.me/6282371869118?text=Halo%2C%20saya%20tertarik%20dengan%20layanan%20Web%20Basic%20yang%20ditawarkan.%20Mohon%20informasinya%2C%20terima%20kasih." class="btn btn-primary-custom w-100 text-white">Order</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card pricing-card highlighted-plan h-100">
                    <div class="popular-badge">Paling Populer</div>
                    <div class="card-body p-4 d-flex flex-column">
                        <h4 class="fw-bold">Standard</h4>
                        <span class="price-period mt-3">Start from</span>
                        <div class="price-display mb-3">
                            <span class="price-amount">Rp 1.000.000</span>
                            <span class="price-period">/ project</span>
                        </div>
                        <p>Solusi paling populer untuk UKM dan startup yang ingin tampil profesional.</p>
                        <h6 class="fw-bold mt-3">Fitur Termasuk:</h6>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill me-2"></i> <span>Desain hingga 8 Halaman</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill me-2"></i> <span>Fitur Toko Online</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill me-2"></i> <span>Include Domain & Hosting</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill me-2"></i> <span>Revisi Desain 5x</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill me-2"></i> <span>Suport 365 hari</span></li>
                        </ul>
                        <div class="mt-auto">
                           <a href="https://wa.me/6282371869118?text=Halo%2C%20saya%20tertarik%20dengan%20layanan%20Web%20Paling%20Populer%20yang%20ditawarkan.%20Mohon%20informasinya%2C%20terima%20kasih." class="btn btn-light w-100 fw-bold">Order</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card pricing-card h-100">
                     <div class="card-body p-4 d-flex flex-column">
                        <h4 class="fw-bold">Premium</h4>
                        <span class="price-period mt-3">Start from</span>
                        <div class="price-display mb-3">
                            <span class="price-amount">Rp 3jt -5jt</span>
                            <span class="price-period">/ project</span>
                        </div>
                        <p class="text-muted">Untuk perusahaan yang membutuhkan website kompleks dengan fitur custom.</p>
                        <h6 class="fw-bold mt-3">Fitur Termasuk:</h6>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Halaman Tidak Terbatas</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Fitur Custom Sesuai Request</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Dukungan Prioritas</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Revisi Tidak Terbatas</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Suport 365 hari</span></li>
                        </ul>
                        <div class="mt-auto">
                           <a href="https://wa.me/6282371869118?text=Halo%2C%20saya%20tertarik%20dengan%20layanan%20Web%20Premium%20yang%20ditawarkan.%20Mohon%20informasinya%2C%20terima%20kasih." class="btn btn-primary-custom w-100 text-white">Order</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="card pricing-card h-100">
                     <div class="card-body p-4 d-flex flex-column">
                        <h4 class="fw-bold">Custom</h4>
                        <span class="price-period mt-3">Start from</span>
                        <div class="price-display mb-3">
                            <span class="price-amount">Rp 50k - 3jt</span>
                            <span class="price-period">/ project</span>
                        </div>
                        <p class="text-muted">Untuk yang lebih membutuhkan website fleksible dengan fitur custom.</p>
                        <h6 class="fw-bold mt-3">Fitur Termasuk:</h6>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Halaman tentukan sendiri</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Fitur Custom Sesuai Request</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Dukungan Prioritas</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Revisi custom</span></li>
                            <li class="mb-2 d-flex"><i class="bi bi-check-circle-fill text-primary me-2"></i> <span>Suport custom</span></li>
                        </ul>
                        <div class="mt-auto">
                           <a href="https://wa.me/6282371869118?text=Halo%2C%20saya%20tertarik%20dengan%20layanan%20Web%20Custom%20yang%20ditawarkan.%20Mohon%20informasinya%2C%20terima%20kasih." class="btn btn-primary-custom w-100 text-white">Order</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
 
</style>

<section id="clients-partners" class="py-5">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold">Klien & Partner Kami</h2>
                <div class="title-divider mx-auto"></div>
                <p class="lead text-muted mt-3">
                    Kami bangga telah dipercaya oleh berbagai instansi dan perusahaan untuk menjadi partner digital mereka.
                </p>
            </div>
        </div>
        <div class="row g-4 align-items-center justify-content-center">
            <!-- Contoh Logo Klien 1 -->
            <div class="col-6 col-md-4 col-lg-2 text-center">
                <div class="p-3">
                    <img src="https://mtsn1waykanan.com/img/mtsn1logo.png" alt="Logo Klien A" class="client-logo">
                </div>
            </div>
            <!-- Contoh Logo Klien 2 -->
            <div class="col-6 col-md-4 col-lg-2 text-center">
                <div class="p-3">
                    <img src="https://sdn4waylagabutuah.sch.id/img/sdn1.png" alt="Logo Klien B" class="client-logo">
                </div>
            </div>
            <!-- Contoh Logo Klien 3 -->
            <div class="col-6 col-md-4 col-lg-2 text-center">
                <div class="p-3">
                    <img src="https://raudlatulmutaalliminkasui.sch.id/wp-content/uploads/2024/10/cropped-IMG-20241014-WA0003.jpg" alt="Logo Klien C" class="client-logo">
                </div>
            </div>
            <!-- Contoh Logo Klien 4 -->
            <div class="col-6 col-md-4 col-lg-2 text-center">
                <div class="p-3">
                    <img src="https://tampingan.kendalkab.go.id/upload/umum/Logo.png" alt="Logo Partner A" class="client-logo">
                </div>
            </div>
            <!-- Contoh Logo Klien 5 -->
            <div class="col-6 col-md-4 col-lg-2 text-center">
                <div class="p-3">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/4b/Lambang_Kabupaten_Musi_Banyuasin.png" alt="Logo Partner B" class="client-logo">
                </div>
            </div>
             <!--Contoh Logo Klien 6 -->
            <div class="col-6 col-md-4 col-lg-2 text-center">
                <div class="p-3">
                    <img src="img/Berwarna1.png" alt="Logo Partner C" class="client-logo">
                </div>
            </div>
        </div>
    </div>
</section>



<section class="testimonials-section py-5">
            <div class="container">
                <div class="row justify-content-center text-center mb-3">
                    <div class="col-lg-8">
                        <h2 class="display-6 fw-bold">Testimonials</h2>
                        <div class="title-divider mx-auto"></div>
                        <p class="lead text-muted mt-3">
                            Apa yang dikatakan klien kami tentang Layanan kami.
                        </p>
                    </div>
                </div>

                <div class="swiper testimonials-slider">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide mb-5">
                            <div class="card testimonial-card h-100">
                                <div class="card-body">
                                    <i class="bi bi-quote fs-1 text-primary"></i>
                                    <p class="testimonial-text mt-1">"Mantap, Pengerjaan cepat, fitur lengkap sesuai kebutuhan, harga terjangkau"</p>
                                    <div class="rating mb-3">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <img src="img/kartono.png" class="testimonial-avatar me-3" alt="Emma Parker">
                                        <div>
                                            <h6 class="fw-bold mb-0">Kartono</h6>
                                            <small class="text-muted">Operator MTs N 1 Way Kanan</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide mb-5">
                             <div class="card testimonial-card h-100">
                                <div class="card-body">
                                     <i class="bi bi-quote fs-1 text-primary"></i>
                                    <p class="testimonial-text mt-1">"Desain bagus, hasil memuaskan seperti yang di inginkan. <br>"</p>
                                    <div class="rating mb-3">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <img src="img/fitr.png" class="testimonial-avatar me-3" alt="David Miller">
                                        <div>
                                            <h6 class="fw-bold mb-0">Fitri Nasution</h6>
                                            <small class="text-muted">Mahasiswa</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide mb-5">
                            <div class="card testimonial-card h-100">
                                <div class="card-body">
                                     <i class="bi bi-quote fs-1 text-primary"></i>
                                    <p class="testimonial-text mt-1">"Mengutamakan kepuasan Client merupakan bagian dari Tanggung jawab dan Visi Kami"</p>
                                    <div class="rating mb-3">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <img src="img/kyy.png" class="testimonial-avatar me-3" alt="Michael Davis">
                                        <div>
                                            <h6 class="fw-bold mb-0">Riski Nurhadi</h6>
                                            <small class="text-muted">Owner & Founder</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide mb-5">
                             <div class="card testimonial-card h-100">
                                <div class="card-body">
                                     <i class="bi bi-quote fs-1 text-primary"></i>
                                    <p class="testimonial-text mt-1">"Desain bagus, hasil memuaskan seperti yang di inginkan. <br>"</p>
                                    <div class="rating mb-3">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <img src="img/fitr.png" class="testimonial-avatar me-3" alt="David Miller">
                                        <div>
                                            <h6 class="fw-bold mb-0">Fitri Nasution</h6>
                                            <small class="text-muted">Mahasiswa</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination mt-5"></div>
                </div>
            </div>
        </section>
        
<section class="cta-section py-5">
    <div class="container">
        <div class="cta-card">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="cta-content">
                        <p class="badge-pill-custom mt-3">
                            <i class="bi bi-stars me-2"></i> Gabung Sekarang
                        </p>
                        <h2 class="display-5 fw-bold mb-3">Siap Bawa Bisnis Anda ke Level Berikutnya?</h2>
                        <p class="text-muted mb-4">
                            Jadilah bagian dari puluhan klien puas yang telah mempercayakan pertumbuhan digital mereka bersama Rnara.id. Kami siap menjadi partner terbaik Anda.
                        </p>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                    <span>Dukungan Penuh 24/7</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                    <span>Pengerjaan Cepat</span>
                                </div>
                            </div>
                            <!--<div class="col-md-6">-->
                            <!--    <div class="d-flex align-items-center">-->
                            <!--        <i class="bi bi-check-circle-fill text-primary me-2"></i>-->
                            <!--        <span>Analitik Mendalam</span>-->
                            <!--    </div>-->
                            <!--</div>-->
                             <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                    <span>Harga Terjangkau</span>
                                </div>
                            </div>
                        </div>

                        <a href="#Pricing" class="btn btn-primary-custom btn-lg w-100 text-light mb-4">Mulai Proyek Anda</a>
                        <div class="text-center mt-3">
                            <!--<a href="#" class="btn btn-outline-custom">-->
                            <!--    <i class="bi bi-play-circle me-2"></i> Lihat Demo-->
                            <!--</a>-->
                        </div>

                    </div>
                </div>

                <div class="col-lg-6 justify-content-end d-lg-block position-relative d-none d-lg-block">
                    <img src="img/join.png" alt="Wanita menunjuk ke arah teks ajakan" class="img-fluid ms-auto d-block cta-person-img flex-end">
                    
                    <div class="floating-nugget" style="top: 15%; left: 0;">
                         <i class="bi bi-graph-up-arrow text-primary me-2"></i> 5+ Instansi Percaya
                    </div>
                    
                    <div class="floating-nugget" style="bottom: 15%; right: 0;">
                        <i class="bi bi-people-fill text-primary me-2"></i> 90% Klien Merasa Puas
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    
        
    <section id="Kontak" class="contact-section py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold">Kontak Kami</h2>
                <div class="title-divider mx-auto"></div>
                <p class="lead text-muted mt-3">
                    Kami siap membantu. Jangan ragu untuk menghubungi kami melalui detail di bawah atau kirimkan pesan melalui form.
                </p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-4">
                <div class="card info-card text-center h-100">
                    <div class="card-body">
                        <div class="info-icon mx-auto mb-3">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h5 class="card-title fw-bold">Alamat Kami</h5>
                        <p class="card-text text-muted">Jl. Garuda, Gatak, Tamantirto, Kec. Kasihan, Kabupaten Bantul, Daerah Istimewa Yogyakarta 55184</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card info-card text-center h-100">
                    <div class="card-body">
                        <div class="info-icon mx-auto mb-3">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <h5 class="card-title fw-bold">Nomor Kontak</h5>
                        <p class="card-text text-muted mb-0">Mobile: +62 823-7186-9118</p>
                        <p class="card-text text-muted">Email: halo@rnara.my.id</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card info-card text-center h-100">
                    <div class="card-body">
                        <div class="info-icon mx-auto mb-3">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <h5 class="card-title fw-bold">Jam Buka</h5>
                        <p class="card-text text-muted mb-0">Senin - Sabtu: 09:00 - 17:00</p>
                        <p class="card-text text-muted">Minggu: Tutup</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-12">
<form class="contact-form" action="proses-kontak.php" method="POST">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input type="text" class="form-control" placeholder="Nama Anda*" name="nama" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <input type="email" class="form-control" placeholder="Email Anda*" name="email" required>
            </div>
        </div>
        <div class="col-md-6">
             <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                <input type="tel" class="form-control" placeholder="Nomor Telepon*" name="telepon">
            </div>
        </div>
        <div class="col-md-6">
             <div class="input-group">
                <span class="input-group-text"><i class="bi bi-tag-fill"></i></span>
                <select class="form-select" name="layanan" required>
                    <option selected disabled value="">Pilih Layanan*</option>
                    <option value="web-development">Web Development</option>
                    <option value="ui-ux-design">UI/UX Design</option>
                    <option value="branding">Branding</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text align-items-start pt-3"><i class="bi bi-pencil-fill"></i></span>
                <textarea class="form-control" rows="5" placeholder="Tulis pesan Anda di sini..." name="pesan"></textarea>
            </div>
        </div>
        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary-custom px-5 text-light">Kirim Pesan</button>
        </div>
    </div>
</form>
            </div>
        </div>
    </div>
</section>
</div>

<!--<a href="https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20layanan%20Anda." -->
<!--   class="floating-cs-button d-lg-none" -->
<!--   target="_blank">-->
<!--    <i class="bi bi-chat-text-fill"></i>-->
<!--</a>-->


<footer id="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-12 footer-info">
                    <a href="#" class="logo d-flex align-items-center">
                        <span class="h3 fw-bold">rnara.id</span>
                    </a>
                    <p>Partner digital terpercaya untuk solusi web dan desain grafis yang inovatif dan berorientasi pada hasil.</p>
                    <div class="social-links mt-3">
                        <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-6 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Home</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Tentang Kami</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Layanan</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Portfolio</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Kontak</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-6 footer-links">
                    <h4>Our Services</h4>
                    <ul>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Web Development</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">UI/UX Design</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Branding</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Digital Marketing</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Motion Graphic</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12 footer-contact text-center text-md-start">
                    <h4>Contact Us</h4>
                    <p>
                        Jl. Garuda, Gatak, Tamantirto, <br>
                        Kec. Kasihan, Kabupaten Bantul, <br>
                        Daerah Istimewa Yogyakarta 55184<br><br>
                        <strong>Phone:</strong> +62 823 7186 9118<br>
                        <strong>Email:</strong> halo@rnara.my.id<br>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>rnara.id</span></strong> 2025. All Rights Reserved
            </div>
            <div class="credits">
                Designed by <a href="#">Rnara Developer Team</a>
            </div>
        </div>
    </div>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const testimonialsSlider = new Swiper('.testimonials-slider', {
            loop: true,
            spaceBetween: 30,
            grabCursor: true,
            autoplay: {
              delay: 4000,
              disableOnInteraction: false,
            },
            slidesPerView: 1,
            breakpoints: {
                768: { slidesPerView: 2 },
                992: { slidesPerView: 3 }
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>
    <script src="js/chat.js"></script>
    
<?php
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    echo "<script>
            Swal.fire({
              icon: '{$alert['type']}',
              title: '{$alert['title']}',
              text: '{$alert['text']}',
              confirmButtonColor: '#6962E7' // Menyesuaikan warna tombol dengan tema
            });
          </script>";
    // Hapus session setelah ditampilkan agar tidak muncul lagi saat refresh
    unset($_SESSION['alert']);
}
?>
</body>
</html>