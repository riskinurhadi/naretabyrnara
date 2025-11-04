<?php
// Selalu panggil auth_check di baris paling atas!
require_once 'auth_check.php';
require_once 'koneksi.php';

// Ambil data user dari session
$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$role_user = htmlspecialchars($_SESSION['role']);

// -----------------------------------------------------------------
// 🔒 OTORISASI: Halaman ini HANYA untuk ADMIN
// -----------------------------------------------------------------
if ($role_user !== 'admin') {
    // Jika bukan admin, tendang ke dashboard
    header("Location: dashboard.php");
    exit;
}

// -----------------------------------------------------------------
// LOGIKA CRUD (CREATE, UPDATE, DELETE) SAAT FORM DI-SUBMIT
// -----------------------------------------------------------------
$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Pastikan ada 'action' yang dikirim
    if (isset($_POST['action'])) {
        
        // -----------------
        // ACTION: TAMBAH PROPERTI
        // -----------------
        if ($_POST['action'] == 'tambah') {
            $nama_properti = $koneksi->real_escape_string($_POST['nama_properti']);
            $alamat = $koneksi->real_escape_string($_POST['alamat']);

            if (!empty($nama_properti)) {
                $stmt = $koneksi->prepare("INSERT INTO tbl_properti (nama_properti, alamat) VALUES (?, ?)");
                $stmt->bind_param("ss", $nama_properti, $alamat);
                if ($stmt->execute()) {
                    $success_message = "Properti baru berhasil ditambahkan.";
                } else {
                    $error_message = "Gagal menambahkan properti: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Nama properti tidak boleh kosong.";
            }
        }
        
        // -----------------
        // ACTION: EDIT PROPERTI
        // -----------------
        elseif ($_POST['action'] == 'edit') {
            $id_properti = intval($_POST['id_properti']);
            $nama_properti = $koneksi->real_escape_string($_POST['nama_properti']);
            $alamat = $koneksi->real_escape_string($_POST['alamat']);

            if (!empty($nama_properti) && $id_properti > 0) {
                $stmt = $koneksi->prepare("UPDATE tbl_properti SET nama_properti = ?, alamat = ? WHERE id_properti = ?");
                $stmt->bind_param("ssi", $nama_properti, $alamat, $id_properti);
                if ($stmt->execute()) {
                    $success_message = "Data properti berhasil diperbarui.";
                } else {
                    $error_message = "Gagal memperbarui properti: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Data tidak valid untuk proses edit.";
            }
        }
        
        // -----------------
        // ACTION: HAPUS PROPERTI
        // -----------------
        elseif ($_POST['action'] == 'hapus') {
            $id_properti = intval($_POST['id_properti']);

            if ($id_properti > 0) {
                // Catatan: ON DELETE CASCADE di database Anda akan otomatis 
                // menghapus semua kamar yang terkait dengan properti ini.
                $stmt = $koneksi->prepare("DELETE FROM tbl_properti WHERE id_properti = ?");
                $stmt->bind_param("i", $id_properti);
                if ($stmt->execute()) {
                    $success_message = "Properti (dan semua kamar di dalamnya) berhasil dihapus.";
                } else {
                    $error_message = "Gagal menghapus properti: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "ID Properti tidak valid.";
            }
        }
    }
}

// -----------------------------------------------------------------
// MENGAMBIL DATA PROPERTI (READ) UNTUK DITAMPILKAN DI TABEL
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
        /* Menggunakan CSS Internal yang sama dari dashboard.php */
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
        .wrapper { display: flex; min-height: 100vh; }
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--bg-white);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            padding: 1.25rem;
            z-index: 1000;
        }
        .sidebar-header {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
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
        #main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 1.5rem;
        }
        .main-header {
            background-color: var(--bg-white);
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
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
    
    <nav id="sidebar">
        <div class="sidebar-header">
            Guesthouse Adiputra
        </div>

        <ul class="nav flex-column sidebar-nav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            
            <?php if (in_array($role_user, ['admin', 'front_office'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="reservasi_kalender.php">
                        <i class="bi bi-calendar-check"></i> Reservasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tamu_data.php">
                        <i class="bi bi-people"></i> Data Tamu
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role_user, ['admin', 'housekeeping'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="kamar_status.php">
                        <i class="bi bi-house-check"></i> Status Kamar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="maintenance_laporan.php">
                        <i class="bi bi-wrench-adjustable"></i> Maintenance
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role_user == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="laporan_keuangan.php">
                        <i class="bi bi-journal-text"></i> Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="pengaturan_properti.php">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div id="main-content">
        
        <header class="main-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Manajemen Sistem</h5>
                <small class="text-muted">Kelola Properti, Kamar, dan Pengguna</small>
            </div>
            
            <div class="user-profile">
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_user); ?>&background=0D6EFD&color=fff" alt="User" class="me-2">
                        <div class="lh-sm">
                            <span class="d-none d-md-inline text-dark"><strong><?php echo $nama_user; ?></strong></span>
                            <br>
                            <small class="d-none d-md-inline text-muted"><?php echo ucwords(str_replace('_', ' ', $role_user)); ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </header>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $error_message; ?>
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
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            Belum ada data properti. Silakan tambahkan properti baru.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
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
                        menghapus **semua data kamar** yang terkait dengannya. 
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
            
            // ---------------------------------
            // Event Listener untuk MODAL EDIT
            // ---------------------------------
            const editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                // Tombol yang memicu modal
                const button = event.relatedTarget;
                
                // Ambil data dari atribut 'data-*'
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const alamat = button.getAttribute('data-alamat');
                
                // Masukkan data ke dalam form di modal
                const modalIdInput = editModal.querySelector('#edit_id_properti');
                const modalNamaInput = editModal.querySelector('#edit_nama_properti');
                const modalAlamatInput = editModal.querySelector('#edit_alamat');
                
                modalIdInput.value = id;
                modalNamaInput.value = nama;
                modalAlamatInput.value = alamat;
            });

            // ---------------------------------
            // Event Listener untuk MODAL HAPUS
            // ---------------------------------
            const hapusModal = document.getElementById('hapusModal');
            hapusModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                
                // Masukkan data ke dalam form di modal
                const modalIdInput = hapusModal.querySelector('#hapus_id_properti');
                const modalNamaText = hapusModal.querySelector('#hapus_nama_properti');
                
                modalIdInput.value = id;
                modalNamaText.textContent = nama; // Tampilkan nama yg akan dihapus
            });

            // ---------------------------------
            // Event Listener untuk SIDEBAR ACTIVE LINK
            // (Script ini sama seperti di dashboard.php)
            // ---------------------------------
            const currentLocation = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

            navLinks.forEach(link => {
                link.classList.remove('active'); // Hapus 'active' dari semua
                if (link.getAttribute('href') === currentLocation) {
                    link.classList.add('active'); // Tambah 'active' ke link yg cocok
                }
            });

            // Jika tidak ada yg cocok, pastikan Pengaturan tetap aktif
            if (currentLocation === 'pengaturan_properti.php') {
                 document.querySelector('.sidebar-nav .nav-link[href="pengaturan_properti.php"]').classList.add('active');
            }
        });
    </script>
</body>
</html>