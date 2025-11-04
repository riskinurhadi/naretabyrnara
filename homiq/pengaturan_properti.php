<?php
// pengaturan_properti.php
require_once 'auth_check.php';
require_once 'koneksi.php';

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$role_user = htmlspecialchars($_SESSION['role']); // Dibutuhkan oleh sidebar.php

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
    
    <style>
        /* * CSS INTERNAL BARU (SAMA DENGAN DASHBOARD.PHP)
         * Ini penting untuk layout responsif
        */
        :root {
            --sidebar-width: 260px;
            --bg-light: #f4f7f6;
            --bg-white: #ffffff;
            --text-dark: #343a40;
            --text-muted: #6c757d;
            --active-bg: #e0f2f1;
            --active-color: #00796b;
        }
        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .wrapper { min-height: 100vh; }

        /* Styling Sidebar Desktop (di atas 992px) */
        @media (min-width: 992px) {
            .offcanvas-lg {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: var(--sidebar-width);
                min-height: 100vh;
                transform: none !important;
                visibility: visible !important;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            }
            /* Margin kiri untuk konten utama di desktop */
            #main-content {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
        }

        /* Konten utama di mobile */
        #main-content {
            padding: 1.5rem;
            width: 100%;
            margin-left: 0;
        }

        /* Styling menu sidebar */
        .sidebar-nav .nav-item { margin-bottom: 0.25rem; }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            color: var(--text-muted);
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
        }
        .sidebar-nav .nav-link i { margin-right: 0.75rem; font-size: 1.2rem; width: 20px; text-align: center; }
        .sidebar-nav .nav-link:hover { background-color: var(--bg-light); color: var(--text-dark); }
        .sidebar-nav .nav-link.active { background-color: var(--active-bg); color: var(--active-color); }
        .sidebar-nav .nav-link.active i { color: var(--active-color); }

        /* Styling Header & Card */
        .main-header {
            background-color: var(--bg-white);
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        /* Ensure layer ordering so sidebar doesn't visually overlap content on some setups */
        .offcanvas-lg { z-index: 1020; }
        #main-content { position: relative; z-index: 1; }
        .user-profile .dropdown-toggle::after { display: none; }
        .user-profile img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .content-card {
            background-color: var(--bg-white);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: none;
            height: 100%;
        }
        .content-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .content-card-header h5 { margin: 0; font-weight: 600; }
    </style>
</head>
<body>

<div class="wrapper">
    
    <?php 
        // Memanggil sidebar terpusat
        require_once 'sidebar.php'; 
    ?>

    <div id="main-content">
        
        <header class="main-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="bi bi-list"></i>
                </button>
                
                <div>
                    <h5 class="mb-0">Manajemen Sistem</h5>
                    <small class="text-muted">Kelola Properti, Kamar, dan Pengguna</small>
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

    </div> </div> <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tambahModalLabel">Tambah Properti Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="pengaturan_properti.php">
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

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Properti</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="pengaturan_properti.php">
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

<div class="modal fade" id="hapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h1 class="modal-title fs-5" id="hapusModalLabel">Konfirmasi Hapus</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="pengaturan_properti.php">
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

            // Sidebar Active Link (Diperbarui)
            const currentLocation = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
            
            let isPengaturanPage = false;

            navLinks.forEach(link => {
                link.classList.remove('active');
                const linkHref = link.getAttribute('href');
                
                if (linkHref === currentLocation) {
                    link.classList.add('active');
                }
                
                // Cek apakah ini halaman pengaturan
                if (linkHref === 'pengaturan_properti.php' && currentLocation.startsWith('pengaturan_')) {
                    isPengaturanPage = true;
                }
            });

            // Jika kita di halaman 'pengaturan_properti.php' atau 'pengaturan_kamar.php', 
            // pastikan link 'Pengaturan' yang aktif
            if (isPengaturanPage || currentLocation.startsWith('pengaturan_')) {
                 const settingsLink = document.querySelector('.sidebar-nav .nav-link[href="pengaturan_properti.php"]');
                 if (settingsLink) {
                    settingsLink.classList.add('active');
                 }
            }
        });
    </script>
</body>
</html>