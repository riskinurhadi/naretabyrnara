<?php
// manajemen_kamar.php
require_once 'auth_check.php';
require_once 'koneksi.php';

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$role_user = htmlspecialchars($_SESSION['role']); // Dibutuhkan oleh sidebar.php

// -----------------------------------------------------------------
// 🔒 OTORISASI: Halaman ini HANYA untuk ADMIN
// -----------------------------------------------------------------
if ($role_user !== 'admin') {
    header("Location: dashboard_baru.php");
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
        $id_properti = intval($_POST['id_properti']);
        $nama_kamar = $koneksi->real_escape_string($_POST['nama_kamar']);
        $tipe_kamar = $koneksi->real_escape_string($_POST['tipe_kamar']);
        $harga = intval(str_replace('.', '', $_POST['harga'])); // Hapus format ribuan
        
        if ($id_properti > 0 && !empty($nama_kamar) && !empty($tipe_kamar) && $harga >= 0) {
            // FIX 1: Mengganti 'harga' dengan 'harga_default'
            $stmt = $koneksi->prepare("INSERT INTO tbl_kamar (id_properti, nama_kamar, tipe_kamar, harga_default, status) VALUES (?, ?, ?, ?, 'Tersedia')");
            $stmt->bind_param("issi", $id_properti, $nama_kamar, $tipe_kamar, $harga);
            if ($stmt->execute()) { $success_message = "Kamar baru berhasil ditambahkan."; }
            else { $error_message = "Gagal menambahkan kamar: " . $stmt->error; }
            $stmt->close();
        } else { $error_message = "Semua data (properti, nama, tipe, harga) wajib diisi."; }
    }
    
    // ACTION: EDIT
    elseif ($_POST['action'] == 'edit') {
        $id_kamar = intval($_POST['id_kamar']);
        $id_properti = intval($_POST['id_properti']);
        $nama_kamar = $koneksi->real_escape_string($_POST['nama_kamar']);
        $tipe_kamar = $koneksi->real_escape_string($_POST['tipe_kamar']);
        $harga = intval(str_replace('.', '', $_POST['harga'])); // Hapus format ribuan
        $status = $koneksi->real_escape_string($_POST['status']);
        
        if ($id_kamar > 0 && $id_properti > 0 && !empty($nama_kamar) && !empty($tipe_kamar) && $harga >= 0) {
            // FIX 2: Mengganti 'harga' dengan 'harga_default'
            $stmt = $koneksi->prepare("UPDATE tbl_kamar SET id_properti = ?, nama_kamar = ?, tipe_kamar = ?, harga_default = ?, status = ? WHERE id_kamar = ?");
            $stmt->bind_param("issisi", $id_properti, $nama_kamar, $tipe_kamar, $harga, $status, $id_kamar);
            if ($stmt->execute()) { $success_message = "Data kamar berhasil diperbarui."; }
            else { $error_message = "Gagal memperbarui kamar: " . $stmt->error; }
            $stmt->close();
        } else { $error_message = "Data tidak valid untuk proses edit."; }
    }
    
    // ACTION: HAPUS
    elseif ($_POST['action'] == 'hapus') {
        $id_kamar = intval($_POST['id_kamar']);
        if ($id_kamar > 0) {
            // Sebaiknya ada pengecekan dulu apakah kamar ini ada di reservasi aktif
            $stmt_check = $koneksi->prepare("SELECT COUNT(*) as total FROM tbl_reservasi WHERE id_kamar = ? AND status_booking IN ('Booking', 'Checked-in')");
            $stmt_check->bind_param("i", $id_kamar);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $total_aktif = $result_check->fetch_assoc()['total'];
            $stmt_check->close();

            if ($total_aktif > 0) {
                $error_message = "Gagal menghapus kamar. Kamar ini sedang digunakan dalam reservasi yang aktif.";
            } else {
                $stmt = $koneksi->prepare("DELETE FROM tbl_kamar WHERE id_kamar = ?");
                $stmt->bind_param("i", $id_kamar);
                if ($stmt->execute()) { $success_message = "Kamar berhasil dihapus."; }
                else { $error_message = "Gagal menghapus kamar: " . $stmt->error; }
                $stmt->close();
            }
        } else { $error_message = "ID Kamar tidak valid."; }
    }
}

// -----------------------------------------------------------------
// MENGAMBIL DATA (READ)
// -----------------------------------------------------------------
// 1. Ambil daftar properti (untuk dropdown)
$properti_list = [];
$query_properti = "SELECT id_properti, nama_properti FROM tbl_properti ORDER BY nama_properti ASC";
$result_properti_dropdown = $koneksi->query($query_properti);
if ($result_properti_dropdown->num_rows > 0) {
    while($row = $result_properti_dropdown->fetch_assoc()) {
        $properti_list[] = $row;
    }
}

// 2. Ambil daftar kamar (untuk tabel)
// FIX 3: Menambahkan alias 'k.harga_default AS harga' agar sisa kode tidak error
$query_kamar = "SELECT k.*, k.harga_default AS harga, p.nama_properti 
                FROM tbl_kamar k
                JOIN tbl_properti p ON k.id_properti = p.id_properti 
                ORDER BY p.nama_properti ASC, k.nama_kamar ASC";
$result_kamar = $koneksi->query($query_kamar);

// 3. Daftar Tipe Kamar (Hardcoded untuk konsistensi)
$tipe_kamar_list = ['Single', 'Double', 'Twin', 'King', 'Queen', 'Suite', 'Standard', 'Deluxe', 'Family Room'];
sort($tipe_kamar_list);

// 4. Daftar Status Kamar (Hardcoded untuk konsistensi)
$status_kamar_list = ['Tersedia', 'Kotor', 'Maintenance', 'Tidak Tersedia'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kamar - CMS Guesthouse Adiputra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- 
      ==========================================================
      == CSS INTERNAL (HANYA UNTUK KONTEN HALAMAN INI) ==
      == CSS SIDEBAR DAN LAYOUT ADA DI sidebar_baru.php ==
      ==========================================================
    -->
    <style>
        :root {
            /* Warna Konten */
            --bg-light: #f9fafb;      /* Latar belakang body lebih cerah */
            --bg-white: #ffffff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border-color: #f0f0f0;  /* Garis batas sangat tipis */
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden; /* Mencegah horizontal scroll */
        }

        /* * 4. STYLING KONTEN (CARD, HEADER, DLL)
         */
        .main-header {
            background-color: var(--bg-white);
            padding: 1.25rem 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            position: relative;
            z-index: 10;
        }
        .user-profile .dropdown-toggle::after { display: none; }
        .user-profile img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        
        .content-card {
            background-color: var(--bg-white);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: none;
            height: 100%;
        }
        .content-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .content-card-header h5 { margin: 0; font-weight: 600; }
        .content-card-header .btn-link { text-decoration: none; font-size: 0.9rem; }
        
        /* Utility */
        .btn-sm i { vertical-align: -1px; }
        .badge { font-size: 0.75rem; padding: 0.4em 0.7em; }
    </style>
</head>
<body>

<div class="wrapper">
    
    <?php 
        // Memanggil sidebar terpusat (yang sudah berisi CSS layout)
        require_once 'sidebar_baru.php'; 
    ?>

    <!-- 
      ==========================================================
      == KONTEN UTAMA HALAMAN (MAIN CONTENT) ==
      ==========================================================
    -->
    <div id="main-content">
        
        <!-- Header Halaman -->
        <header class="main-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h5 class="mb-0">Manajemen Pengaturan</h5>
                    <small class="text-muted">Kelola data master kamar.</small>
                </div>
            </div>
            
            <div class="user-profile">
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_user); ?>&background=232a4a&color=fff" alt="User" class="me-2">
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

        <!-- Card Daftar Kamar -->
        <div class="row">
            <div class="col-12">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5><i class="bi bi-door-open me-2"></i> Daftar Kamar</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Kamar
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Kamar</th>
                                    <th scope="col">Properti</th>
                                    <th scope="col">Tipe</th>
                                    <th scope="col">Harga/Malam</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_kamar->num_rows > 0): ?>
                                    <?php $i = 1; ?>
                                    <?php while($row = $result_kamar->fetch_assoc()): ?>
                                        <tr>
                                            <th scope="row"><?php echo $i++; ?></th>
                                            <td><strong><?php echo htmlspecialchars($row['nama_kamar']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['nama_properti']); ?></td>
                                            <td><?php echo htmlspecialchars($row['tipe_kamar']); ?></td>
                                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                            <td>
                                                <?php 
                                                    $status_class = 'bg-light text-dark';
                                                    if ($row['status'] == 'Tersedia') $status_class = 'bg-success-subtle text-success-emphasis';
                                                    if ($row['status'] == 'Kotor') $status_class = 'bg-warning-subtle text-warning-emphasis';
                                                    if ($row['status'] == 'Maintenance') $status_class = 'bg-danger-subtle text-danger-emphasis';
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                                        title="Edit Kamar"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        data-id="<?php echo $row['id_kamar']; ?>"
                                                        data-id-properti="<?php echo $row['id_properti']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['nama_kamar']); ?>"
                                                        data-tipe="<?php echo htmlspecialchars($row['tipe_kamar']); ?>"
                                                        data-harga="<?php echo $row['harga']; ?>"
                                                        data-status="<?php echo htmlspecialchars($row['status']); ?>">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm btn-hapus" 
                                                        title="Hapus Kamar"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#hapusModal"
                                                        data-id="<?php echo $row['id_kamar']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['nama_kamar']); ?> (<?php echo htmlspecialchars($row['nama_properti']); ?>)">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted">Belum ada data kamar. Silakan tambahkan kamar baru.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- End #main-content -->
</div> <!-- End .wrapper -->


<!-- 
  ==========================================================
  == MODAL (TAMBAH, EDIT, HAPUS) ==
  ==========================================================
-->

<!-- Modal: Tambah Kamar -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tambahModalLabel">Tambah Kamar Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="manajemen_kamar.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="tambah">
                    
                    <div class="mb-3">
                        <label for="tambah_id_properti" class="form-label">Lokasi Properti</label>
                        <select class="form-select" id="tambah_id_properti" name="id_properti" required>
                            <option value="" disabled selected>-- Pilih Properti --</option>
                            <?php foreach ($properti_list as $properti): ?>
                                <option value="<?php echo $properti['id_properti']; ?>">
                                    <?php echo htmlspecialchars($properti['nama_properti']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tambah_nama_kamar" class="form-label">Nama/Nomor Kamar</label>
                        <input type="text" class="form-control" id="tambah_nama_kamar" name="nama_kamar" placeholder="Contoh: 101, 203, Melati, Bougenville" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="tambah_tipe_kamar" class="form-label">Tipe Kamar</label>
                                <select class="form-select" id="tambah_tipe_kamar" name="tipe_kamar" required>
                                    <option value="" disabled selected>-- Pilih Tipe --</option>
                                    <?php foreach ($tipe_kamar_list as $tipe): ?>
                                        <option value="<?php echo $tipe; ?>"><?php echo $tipe; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                             <div class="mb-3">
                                <label for="tambah_harga" class="form-label">Harga/Malam</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="tambah_harga" name="harga" placeholder="150.000" onkeyup="formatRupiah(this)" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kamar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Kamar -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Data Kamar</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="manajemen_kamar.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_kamar" id="edit_id_kamar">
                    
                    <div class="mb-3">
                        <label for="edit_id_properti" class="form-label">Lokasi Properti</label>
                        <select class="form-select" id="edit_id_properti" name="id_properti" required>
                            <option value="" disabled>-- Pilih Properti --</option>
                            <?php foreach ($properti_list as $properti): ?>
                                <option value="<?php echo $properti['id_properti']; ?>">
                                    <?php echo htmlspecialchars($properti['nama_properti']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_nama_kamar" class="form-label">Nama/Nomor Kamar</label>
                        <input type="text" class="form-control" id="edit_nama_kamar" name="nama_kamar" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="edit_tipe_kamar" class="form-label">Tipe Kamar</label>
                                <select class="form-select" id="edit_tipe_kamar" name="tipe_kamar" required>
                                    <option value="" disabled>-- Pilih Tipe --</option>
                                    <?php foreach ($tipe_kamar_list as $tipe): ?>
                                        <option value="<?php echo $tipe; ?>"><?php echo $tipe; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                             <div class="mb-3">
                                <label for="edit_harga" class="form-label">Harga/Malam</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="edit_harga" name="harga" onkeyup="formatRupiah(this)" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status Kamar</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <?php foreach ($status_kamar_list as $status): ?>
                                <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                            <?php endforeach; ?>
                        </select>
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

<!-- Modal: Hapus Kamar -->
<div class="modal fade" id="hapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h1 class="modal-title fs-5" id="hapusModalLabel">Konfirmasi Hapus</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="manajemen_kamar.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id_kamar" id="hapus_id_kamar">
                    <p>Apakah Anda yakin ingin menghapus kamar ini?</p>
                    <h5 class="text-danger" id="hapus_nama_kamar"></h5>
                    <div class="alert alert-warning mt-3">
                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                        Data reservasi yang terkait mungkin akan terpengaruh jika tidak hati-hati.
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

<!-- 
  ==========================================================
  == JAVASCRIPT ==
  ==========================================================
-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
<script>
    
    // Fungsi untuk format Rupiah
    function formatRupiah(input) {
        let value = input.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        input.value = rupiah;
    }

    // Fungsi untuk mengubah format angka (150000) menjadi Rupiah (150.000)
    function numberToRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    document.addEventListener('DOMContentLoaded', function () {
        
        // Modal Edit: Isi data saat modal dibuka
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                
                // Ambil data dari atribut data-*
                const id = button.getAttribute('data-id');
                const idProperti = button.getAttribute('data-id-properti');
                const nama = button.getAttribute('data-nama');
                const tipe = button.getAttribute('data-tipe');
                const harga = button.getAttribute('data-harga');
                const status = button.getAttribute('data-status');
                
                // Masukkan data ke dalam form modal
                editModal.querySelector('#edit_id_kamar').value = id;
                editModal.querySelector('#edit_id_properti').value = idProperti;
                editModal.querySelector('#edit_nama_kamar').value = nama;
                editModal.querySelector('#edit_tipe_kamar').value = tipe;
                editModal.querySelector('#edit_harga').value = numberToRupiah(harga); // Format harga
                editModal.querySelector('#edit_status').value = status;
            });
        }

        // Modal Hapus: Isi data saat modal dibuka
        const hapusModal = document.getElementById('hapusModal');
        if(hapusModal) {
            hapusModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                
                hapusModal.querySelector('#hapus_id_kamar').value = id;
                hapusModal.querySelector('#hapus_nama_kamar').textContent = nama;
            });
        }
    });
</script>
</body>
</html>

