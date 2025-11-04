<?php
// manajemen_properti.php
require_once 'auth_check.php';
require_once 'koneksi.php';

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$role_user = htmlspecialchars($_SESSION['role']); // Dibutuhkan oleh sidebar_baru.php

// -----------------------------------------------------------------
// 🔒 OTORISASI: Halaman ini HANYA untuk ADMIN
// -----------------------------------------------------------------
if ($role_user !== 'admin') {
    header("Location: dashboard_baru.php"); // Arahkan ke dashboard baru
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
            
            // Pengecekan Keterkaitan Kamar
            $stmt_check = $koneksi->prepare("SELECT COUNT(*) FROM tbl_kamar WHERE id_properti = ?");
            $stmt_check->bind_param("i", $id_properti);
            $stmt_check->execute();
            $stmt_check->bind_result($jumlah_kamar);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($jumlah_kamar > 0) {
                $error_message = "Gagal menghapus: Properti ini masih memiliki " . $jumlah_kamar . " kamar. Hapus kamar terlebih dahulu.";
            } else {
                $stmt = $koneksi->prepare("DELETE FROM tbl_properti WHERE id_properti = ?");
                $stmt->bind_param("i", $id_properti);
                if ($stmt->execute()) { $success_message = "Properti berhasil dihapus."; }
                else { $error_message = "Gagal menghapus properti: " . $stmt->error; }
                $stmt->close();
            }
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
    
    <!-- BARU: CSS DataTables -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">

    <style>
        /* CSS LOKAL UNTUK KONTEN (BUKAN SIDEBAR) */
        :root {
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --text-dark: #343a40;
            --text-muted: #6c757d;
        }
        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .main-header {
            background-color: var(--bg-white);
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            margin-bottom: 2rem;
        }
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .content-card {
            background-color: var(--bg-white);
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            border: none;
        }
        .content-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f0f2f5;
        }
        .content-card-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.15rem;
        }
        /* Styling tombol aksi di tabel */
        .btn-aksi {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
    </style>
</head>
<body>

<div class="wrapper">
    
    <?php 
        // Memanggil sidebar terpusat BARU
        require_once 'sidebar_baru.php'; 
    ?>

    <div id="main-content">
        
        <header class="main-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h5 class="mb-0">Manajemen Properti</h5>
                    <small class="text-muted">Kelola daftar gedung dan guesthouse Anda</small>
                </div>
            </div>
            
            <div class="user-profile">
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_user); ?>&background=232a4a&color=fff&font-size=0.5" alt="User" class="me-2">
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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Properti
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <!-- PERUBAHAN: Menghapus table-hover & table-striped, menambah ID 'tabelProperti' -->
                        <table id="tabelProperti" class="table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 5%;">#</th>
                                    <th scope="col">Nama Properti</th>
                                    <th scope="col">Alamat</th>
                                    <th scope="col" class="text-center" style="width: 15%;">Aksi</th>
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
                                                <button type="button" class="btn btn-warning btn-aksi btn-edit" 
                                                        title="Edit Properti"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        data-id="<?php echo $row['id_properti']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['nama_properti']); ?>"
                                                        data-alamat="<?php echo htmlspecialchars($row['alamat']); ?>">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-aksi btn-hapus" 
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

    </div> 
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tambahModalLabel">Tambah Properti Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Properti</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h1 class="modal-title fs-5" id="hapusModalLabel">Konfirmasi Hapus</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="manajemen_properti.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id_properti" id="hapus_id_properti">
                    <p>Apakah Anda yakin ingin menghapus properti ini?</p>
                    <h5 class="text-danger" id="hapus_nama_properti"></h5>
                    <div class="alert alert-warning mt-3">
                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Pastikan tidak ada kamar yang terkait sebelum menghapus.
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
    
<!-- BARU: JS DataTables (jQuery diperlukan) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

<!-- Script Inisialisasi DataTables & Modal (Vanilla JS) -->
<script>
    // Inisialisasi DataTables (jQuery)
    $(document).ready(function() {
        $('#tabelProperti').DataTable({
            "language": {
                // Menggunakan CDN untuk bahasa Indonesia
                "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
            }
        });
    });

    // Script Modal (Vanilla JS)
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
    });
</script>
</body>
</html>

