<?php
// manajemen_properti.php
// (Menggantikan pengaturan_properti.php yang lama)

require_once 'auth_check.php';
require_once 'koneksi.php';

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$role_user = htmlspecialchars($_SESSION['role']); // Dibutuhkan oleh sidebar_baru.php

// -----------------------------------------------------------------
// 🔒 OTORISASI: Halaman ini HANYA untuk ADMIN
// -----------------------------------------------------------------
if ($role_user !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// -----------------------------------------------------------------
// LOGIKA CRUD (CREATE, UPDATE, DELETE)
// -----------------------------------------------------------------
$success_message = '';
$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    // ACTION: TAMBAH
    if ($_POST['action'] == 'tambah') {
        $nama_properti = $koneksi->real_escape_string($_POST['nama_properti']);
        $alamat = $koneksi->real_escape_string($_POST['alamat']);
        if (!empty($nama_properti)) {
            $stmt = $koneksi->prepare("INSERT INTO tbl_properti (nama_properti, alamat) VALUES (?, ?)");
            $stmt->bind_param("ss", $nama_properti, $alamat);
            if ($stmt->execute()) { $success_message = "Properti baru berhasil ditambahkan."; }
            else { $error_message = "Gagal menambahkan properti: " . $stmt->error; }
            $stmt->close();
        } else { $error_message = "Nama properti tidak boleh kosong."; }
    }
    
    // ACTION: EDIT
    elseif ($_POST['action'] == 'edit') {
        $id_properti = intval($_POST['id_properti']);
        $nama_properti = $koneksi->real_escape_string($_POST['nama_properti']);
        $alamat = $koneksi->real_escape_string($_POST['alamat']);
        if (!empty($nama_properti) && $id_properti > 0) {
            $stmt = $koneksi->prepare("UPDATE tbl_properti SET nama_properti = ?, alamat = ? WHERE id_properti = ?");
            $stmt->bind_param("ssi", $nama_properti, $alamat, $id_properti);
            if ($stmt->execute()) { $success_message = "Data properti berhasil diperbarui."; }
            else { $error_message = "Gagal memperbarui properti: " . $stmt->error; }
            $stmt->close();
        } else { $error_message = "Data tidak valid untuk proses edit."; }
    }
    
    // ACTION: HAPUS
    elseif ($_POST['action'] == 'hapus') {
        $id_properti = intval($_POST['id_properti']);
        if ($id_properti > 0) {
            $stmt = $koneksi->prepare("DELETE FROM tbl_properti WHERE id_properti = ?");
            $stmt->bind_param("i", $id_properti);
            if ($stmt->execute()) { $success_message = "Properti (dan semua kamar di dalamnya) berhasil dihapus."; }
            else { $error_message = "Gagal menghapus properti: " . $stmt->error; }
            $stmt->close();
        } else { $error_message = "ID Properti tidak valid."; }
    }
}

// -----------------------------------------------------------------
// MENGAMBIL DATA (READ)
// -----------------------------------------------------------------
$query_properti = "SELECT * FROM tbl_properti ORDER BY nama_properti ASC";
$result_properti = $koneksi->query($query_properti);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Properti - CMS Guesthouse Adiputra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- 
      ==========================================================
      == CSS INTERNAL BARU (DISALIN DARI DASHBOARD_BARU.PHP) ==
      ==========================================================
    -->
    <style>
        :root {
            /* Ukuran */
            --sidebar-width: 280px;
            
            /* Warna (Palette Baru Sesuai Gambar) */
            --sidebar-bg: linear-gradient(180deg, #232a4a, #1a1f33);
            --sidebar-text: rgba(255, 255, 255, 0.7);
            --sidebar-text-active: #232a4a;
            --sidebar-active-pill: #ffffff;
            --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
            
            /* Warna Konten */
            --bg-light: #f9fafb;      /* Latar belakang body lebih cerah */
            --bg-white: #ffffff;      /* Latar belakang card */
            --text-dark: #212529;     /* Teks utama */
            --text-muted: #6c757d;    /* Teks abu-abu */
            --border-color: #f0f0f0;  /* Garis batas sangat tipis */
            
            /* Warna Ikon Statistik */
            --color-blue: #0d6efd;      --bg-blue-light: #e7f0ff;
            --color-green: #198754;     --bg-green-light: #e8f9ee;
            --color-orange: #fd7e14;    --bg-orange-light: #fff3e8;
            --color-purple: #6f42c1;    --bg-purple-light: #f3effc;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden; /* Mencegah horizontal scroll */
        }

        /* * 1. STYLING SIDEBAR BARU (Desktop & Mobile)
         */
        .sidebar-nav-wrapper.offcanvas {
            width: var(--sidebar-width);
            background: var(--sidebar-bg); /* <-- Menggunakan 'background' untuk gradien */
            border-right: none; /* Hapus border, ganti shadow */
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-nav-wrapper .offcanvas-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 600;
            color: var(--bg-white); /* Teks header jadi putih */
        }
        
        .sidebar-nav {
            padding: 1rem; /* Padding untuk keseluruhan grup menu */
        }

        .sidebar-nav .nav-item {
            margin-bottom: 0.25rem; /* Jarak antar item menu */
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--sidebar-text); /* Teks sidebar jadi terang */
            padding: 0.8rem 1.25rem; /* Padding internal link */
            border-radius: 0.75rem; /* Sudut lebih melengkung */
            transition: all 0.2s ease-in-out;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 24px; /* Lebar ikon tetap */
            text-align: center;
            color: var(--sidebar-text); /* Ikon sidebar jadi terang */
            transition: all 0.2s ease-in-out;
        }
        
        /* Efek Hover */
        .sidebar-nav .nav-link:hover {
            background-color: var(--sidebar-hover-bg);
            color: var(--bg-white);
        }
        .sidebar-nav .nav-link:hover i {
            color: var(--bg-white);
        }
        
        /* Status Aktif (Gaya "Pill" Putih) */
        .sidebar-nav .nav-link.active {
            background-color: var(--sidebar-active-pill);
            color: var(--sidebar-text-active);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .sidebar-nav .nav-link.active i {
            color: var(--sidebar-text-active);
        }

        /* Tombol Logout Khusus */
        .sidebar-nav .nav-link-logout {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--sidebar-text);
            opacity: 0.6;
        }
        .sidebar-nav .nav-link-logout:hover {
            opacity: 1;
            background-color: rgba(220, 53, 69, 0.1); /* Hover merah */
            color: #dc3545;
        }
        .sidebar-nav .nav-link-logout:hover i {
            color: #dc3545;
        }


        /* * 2. STYLING KONTEN UTAMA
         */
        #main-content {
            padding: 2rem; /* Padding lebih besar */
            width: 100%;
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        /* * 3. LOGIKA RESPONSIVE (LAYOUT DESKTOP)
         */
        @media (min-width: 992px) {
            .sidebar-nav-wrapper.offcanvas {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                transform: none !important;
                visibility: visible !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); /* Shadow pemisah */
            }
            
            #main-content {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
        }

        /* * 4. STYLING KONTEN (CARD, HEADER, DLL)
         */
        .main-header {
            background-color: var(--bg-white);
            padding: 1rem 1.5rem;
            border-radius: 1rem; /* Lebih bulat */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            margin-bottom: 2rem; /* Jarak lebih besar */
            border: 1px solid var(--border-color);
        }
        .user-profile .dropdown-toggle::after { display: none; }
        .user-profile img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        
        /* GAYA CONTENT CARD BAWAH */
        .content-card { 
            background-color: var(--bg-white);
            border-radius: 1rem; /* Sangat bulat */
            padding: 1.5rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            height: 100%; 
        }
        .content-card-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 1.25rem; padding-bottom: 1.25rem; 
            border-bottom: 1px solid var(--border-color); 
        }
        .content-card-header h5 { margin: 0; font-weight: 600; }
        .content-card-header .btn-link { text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>

<!-- PERUBAHAN: Wrapper diubah menjadi d-flex... -->
<div class="d-flex flex-row min-vh-100">
    
    <?php 
        // PERUBAHAN: Memanggil sidebar_baru.php
        require_once 'sidebar_baru.php'; 
    ?>

    <!-- KONTEN UTAMA -->
    <div id="main-content">
        
        <header class="main-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="bi bi-list"></i>
                </button>
                
                <div>
                    <!-- PERUBAHAN: Judul disesuaikan -->
                    <h5 class="mb-0">Manajemen Properti</h5>
                    <small class="text-muted">Kelola daftar properti Anda</small>
                </div>
            </div>
            
            <div class="user-profile">
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_user); ?>&background=0D6EFD&color=fff" alt="User" class="me-2">
                        <div class="lh-sm">
                            <span class="d-none d-md-inline text-dark"><strong><?php echo $nama_user; ?></strong></span><br>
                            <small class="d-none d-md-inline text-muted"><?php echo ucwords(str_replace('_', ' ', $role_user)); ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Notifikasi (Alerts) -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Konten Utama (Tabel Properti) -->
        <div class="row">
            <div class="col-12">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5><i class="bi bi-building me-2"></i> Daftar Properti</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Properti
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Properti</th>
                                    <th scope="col">Alamat</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_properti->num_rows > 0): ?>
                                    <?php $i = 1; ?>
                                    <?php while($row = $result_properti->fetch_assoc()): ?>
                                        <tr>
                                            <th scope="row"><?php echo $i++; ?></th>
                                            <td><?php echo htmlspecialchars($row['nama_properti']); ?></td>
                                            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                                        title="Edit Properti"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        data-id="<?php echo $row['id_properti']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['nama_properti']); ?>"
                                                        data-alamat="<?php echo htmlspecialchars($row['alamat']); ?>">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm btn-hapus" 
                                                        title="Hapus Properti"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#hapusModal"
                                                        data-id="<?php echo $row['id_properti']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['nama_properti']); ?>">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">Belum ada data properti. Silakan tambahkan properti baru.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- Penutup #main-content -->
</div> <!-- Penutup .d-flex -->

<!-- 
  ==========================================================
  == MODALS
  == PERUBAHAN: Dipindahkan ke luar #main-content
  ==========================================================
-->

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tambahModalLabel">Tambah Properti Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- PERUBAHAN: Action form diubah -->
            <form method="POST" action="manajemen_properti.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="tambah">
                    <div class="mb-3">
                        <label for="nama_properti" class="form-label">Nama Properti</label>
                        <input type="text" class="form-control" id="nama_properti" name="nama_properti" placeholder="Contoh: GH 1, Villa Adiputra" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat (Opsional)</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Contoh: Jl. Merdeka No. 10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Properti</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- PERUBAHAN: Action form diubah -->
            <form method="POST" action="manajemen_properti.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_properti" id="edit_id_properti">
                    <div class="mb-3">
                        <label for="edit_nama_properti" class="form-label">Nama Properti</label>
                        <input type="text" class="form-control" id="edit_nama_properti" name="nama_properti" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label">Alamat (Opsional)</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="hapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h1 class="modal-title fs-5" id="hapusModalLabel">Konfirmasi Hapus</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- PERUBAHAN: Action form diubah -->
            <form method="POST" action="manajemen_properti.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id_properti" id="hapus_id_properti">
                    <p>Apakah Anda yakin ingin menghapus properti ini?</p>
                    <h5 class="text-danger" id="hapus_nama_properti"></h5>
                    <div class="alert alert-warning mt-3">
                        <strong>Peringatan!</strong> Menghapus properti ini juga akan
                        menghapus semua data kamar yang terkait dengannya.
                        Tindakan ini tidak dapat dibatalkan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Modal Edit
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const alamat = button.getAttribute('data-alamat');
                editModal.querySelector('#edit_id_properti').value = id;
                editModal.querySelector('#edit_nama_properti').value = nama;
                editModal.querySelector('#edit_alamat').value = alamat;
            });
        }

        // Modal Hapus
        const hapusModal = document.getElementById('hapusModal');
        if(hapusModal) {
            hapusModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                hapusModal.querySelector('#hapus_id_properti').value = id;
                hapusModal.querySelector('#hapus_nama_properti').textContent = nama;
            });
        }
        
        // PERUBAHAN: Script active link sidebar dihapus
        // karena sudah ditangani oleh PHP di sidebar_baru.php
    });
</script>
</body>
</html>
