<?php
// manajemen_kamar.php
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

// Helper function untuk membersihkan format Rupiah
function clean_harga($harga_str) {
    return (int) preg_replace('/[^0-9]/', '', $harga_str);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    // ACTION: TAMBAH
    if ($_POST['action'] == 'tambah') {
        $id_properti = intval($_POST['id_properti']);
        $nama_kamar = $koneksi->real_escape_string($_POST['nama_kamar']);
        $tipe_kamar = $koneksi->real_escape_string($_POST['tipe_kamar']);
        $harga = clean_harga($_POST['harga_default']); // Gunakan nama kolom yang benar
        
        if ($id_properti > 0 && !empty($nama_kamar) && !empty($tipe_kamar) && $harga >= 0) {
            // FIX: Menggunakan 'harga_default'
            $stmt = $koneksi->prepare("INSERT INTO tbl_kamar (id_properti, nama_kamar, tipe_kamar, harga_default, status) VALUES (?, ?, ?, ?, 'Tersedia')");
            $stmt->bind_param("issi", $id_properti, $nama_kamar, $tipe_kamar, $harga);
            if ($stmt->execute()) { $success_message = "Kamar baru berhasil ditambahkan."; }
            else { $error_message = "Gagal menambahkan kamar: " . $stmt->error; }
            $stmt->close();
        } else { $error_message = "Data tidak lengkap. Pastikan semua field terisi."; }
    }
    
    // ACTION: EDIT
    elseif ($_POST['action'] == 'edit') {
        $id_kamar = intval($_POST['id_kamar']);
        $id_properti = intval($_POST['id_properti']);
        $nama_kamar = $koneksi->real_escape_string($_POST['nama_kamar']);
        $tipe_kamar = $koneksi->real_escape_string($_POST['tipe_kamar']);
        $harga = clean_harga($_POST['harga_default']); // Gunakan nama kolom yang benar
        $status = $koneksi->real_escape_string($_POST['status']);
        
        if ($id_kamar > 0 && $id_properti > 0 && !empty($nama_kamar) && !empty($tipe_kamar) && $harga >= 0) {
            // FIX: Menggunakan 'harga_default'
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
            
            // Pengecekan Keterkaitan Reservasi
            $stmt_check = $koneksi->prepare("SELECT COUNT(*) FROM tbl_reservasi WHERE id_kamar = ? AND status_booking IN ('Booking', 'Checked-in')");
            $stmt_check->bind_param("i", $id_kamar);
            $stmt_check->execute();
            $stmt_check->bind_result($jumlah_reservasi);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($jumlah_reservasi > 0) {
                $error_message = "Gagal menghapus: Kamar ini sedang aktif di " . $jumlah_reservasi . " reservasi. Batalkan reservasi terlebih dahulu.";
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
$result_properti_dropdown = $koneksi->query("SELECT id_properti, nama_properti FROM tbl_properti ORDER BY nama_properti ASC");
if ($result_properti_dropdown->num_rows > 0) {
    while($row = $result_properti_dropdown->fetch_assoc()) {
        $properti_list[] = $row;
    }
}

// 2. Ambil daftar kamar (untuk tabel)
// FIX: Menambahkan alias 'k.harga_default AS harga'
$query_kamar = "SELECT k.*, k.harga_default AS harga, p.nama_properti 
                FROM tbl_kamar k
                JOIN tbl_properti p ON k.id_properti = p.id_properti 
                ORDER BY p.nama_properti, k.nama_kamar ASC";
$result_kamar = $koneksi->query($query_kamar);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kamar - CMS Guesthouse Adiputra</title>
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
        /* Format Rupiah di input */
        .input-rupiah {
            padding-left: 2.5rem;
        }
        .input-group-text.rupiah-prefix {
            background-color: #e9ecef;
            border-right: none;
            color: #495057;
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
                    <h5 class="mb-0">Manajemen Kamar</h5>
                    <small class="text-muted">Kelola daftar kamar di semua properti Anda</small>
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
                        <h5><i class="bi bi-door-closed me-2"></i> Daftar Kamar</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Kamar
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <!-- PERUBAHAN: Menghapus table-hover & table-striped, menambah ID 'tabelKamar' -->
                        <table id="tabelKamar" class="table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Nama Kamar</th>
                                    <th scope="col">Properti</th>
                                    <th scope="col">Tipe</th>
                                    <th scope="col">Harga Default</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center" style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_kamar->num_rows > 0): ?>
                                    <?php while($row = $result_kamar->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($row['nama_kamar']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['nama_properti']); ?></td>
                                            <td><?php echo htmlspecialchars($row['tipe_kamar']); ?></td>
                                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                            <td>
                                                <?php 
                                                $status = htmlspecialchars($row['status']);
                                                $badge_class = 'bg-light text-dark';
                                                if ($status == 'Tersedia') $badge_class = 'bg-success-subtle text-success-emphasis';
                                                else if ($status == 'Kotor') $badge_class = 'bg-warning-subtle text-warning-emphasis';
                                                else if ($status == 'Maintenance' || $status == 'Tidak Tersedia') $badge_class = 'bg-danger-subtle text-danger-emphasis';
                                                echo "<span class='badge $badge_class'>$status</span>";
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-warning btn-aksi btn-edit" 
                                                        title="Edit Kamar"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        data-id-kamar="<?php echo $row['id_kamar']; ?>"
                                                        data-id-properti="<?php echo $row['id_properti']; ?>"
                                                        data-nama-kamar="<?php echo htmlspecialchars($row['nama_kamar']); ?>"
                                                        data-tipe-kamar="<?php echo htmlspecialchars($row['tipe_kamar']); ?>"
                                                        data-harga="<?php echo $row['harga']; ?>"
                                                        data-status="<?php echo $row['status']; ?>">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-aksi btn-hapus" 
                                                        title="Hapus Kamar"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#hapusModal"
                                                        data-id="<?php echo $row['id_kamar']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['nama_kamar'] . ' (' . $row['nama_properti'] . ')'); ?>">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center text-muted">Belum ada data kamar. Silakan tambahkan kamar baru.</td></tr>
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
                <h1 class="modal-title fs-5" id="tambahModalLabel">Tambah Kamar Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="manajemen_kamar.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="tambah">
                    <div class="mb-3">
                        <label for="id_properti" class="form-label">Properti</label>
                        <select class="form-select" id="id_properti" name="id_properti" required>
                            <option value="" disabled selected>Pilih properti...</option>
                            <?php foreach ($properti_list as $properti): ?>
                                <option value="<?php echo $properti['id_properti']; ?>">
                                    <?php echo htmlspecialchars($properti['nama_properti']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_kamar" class="form-label">Nama/Nomor Kamar</label>
                            <input type="text" class="form-control" id="nama_kamar" name="nama_kamar" placeholder="Contoh: 101, Melati" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipe_kamar" class="form-label">Tipe Kamar</label>
                            <input type="text" class="form-control" id="tipe_kamar" name="tipe_kamar" placeholder="Contoh: Double, Twin, Single" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="harga_default" class="form-label">Harga Default</label>
                        <div class="input-group">
                            <span class="input-group-text rupiah-prefix">Rp</span>
                            <input type="text" class="form-control input-rupiah" id="harga_default" name="harga_default" placeholder="150.000" required>
                        </div>
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
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Kamar</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="manajemen_kamar.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_kamar" id="edit_id_kamar">
                    <div class="mb-3">
                        <label for="edit_id_properti" class="form-label">Properti</label>
                        <select class="form-select" id="edit_id_properti" name="id_properti" required>
                            <?php foreach ($properti_list as $properti): ?>
                                <option value="<?php echo $properti['id_properti']; ?>">
                                    <?php echo htmlspecialchars($properti['nama_properti']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_nama_kamar" class="form-label">Nama/Nomor Kamar</label>
                            <input type="text" class="form-control" id="edit_nama_kamar" name="nama_kamar" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_tipe_kamar" class="form-label">Tipe Kamar</label>
                            <input type="text" class="form-control" id="edit_tipe_kamar" name="tipe_kamar" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_harga" class="form-label">Harga Default</label>
                            <div class="input-group">
                                <span class="input-group-text rupiah-prefix">Rp</span>
                                <input type="text" class="form-control input-rupiah" id="edit_harga" name="harga_default" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status Kamar</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Kotor">Kotor</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Tidak Tersedia">Tidak Tersedia</option>
                            </select>
                        </div>
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
            <form method="POST" action="manajemen_kamar.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id_properti" id="hapus_id_kamar">
                    <p>Apakah Anda yakin ingin menghapus kamar ini?</p>
                    <h5 class="text-danger" id="hapus_nama_kamar"></h5>
                    <div class="alert alert-warning mt-3">
                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan. Pastikan tidak ada reservasi aktif untuk kamar ini.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</lbutton>
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
        $('#tabelKamar').DataTable({
            "language": {
                // Menggunakan CDN untuk bahasa Indonesia
                "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": 5 } // Nonaktifkan sort di kolom 'Aksi'
            ]
        });
    });

    // Fungsi untuk format Rupiah (Vanilla JS)
    function formatRupiah(angka, prefix) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
    }

    // Event listener untuk input harga (Vanilla JS)
    document.addEventListener('DOMContentLoaded', function () {
        
        // Format input harga di Modal Tambah
        const hargaInput = document.getElementById('harga_default');
        if (hargaInput) {
            hargaInput.addEventListener('keyup', function (e) {
                this.value = formatRupiah(this.value);
            });
        }

        // Format input harga di Modal Edit
        const editHargaInput = document.getElementById('edit_harga');
        if (editHargaInput) {
            editHargaInput.addEventListener('keyup', function (e) {
                this.value = formatRupiah(this.value);
            });
        }

        // Modal Edit
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                
                // Ambil data dari tombol
                const idKamar = button.getAttribute('data-id-kamar');
                const idProperti = button.getAttribute('data-id-properti');
                const namaKamar = button.getAttribute('data-nama-kamar');
                const tipeKamar = button.getAttribute('data-tipe-kamar');
                const harga = button.getAttribute('data-harga');
                const status = button.getAttribute('data-status');
                
                // Masukkan data ke form
                editModal.querySelector('#edit_id_kamar').value = idKamar;
                editModal.querySelector('#edit_id_properti').value = idProperti;
                editModal.querySelector('#edit_nama_kamar').value = namaKamar;
                editModal.querySelector('#edit_tipe_kamar').value = tipeKamar;
                editModal.querySelector('#edit_status').value = status;
                
                // Format harga sebelum dimasukkan ke input
                editModal.querySelector('#edit_harga').value = formatRupiah(harga.toString());
            });
        }

        // Modal Hapus
        const hapusModal = document.getElementById('hapusModal');
        if(hapusModal) {
            hapusModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                
                // Perbaiki ID input
                const hapusIdInput = hapusModal.querySelector('#hapus_id_kamar'); 
                if (hapusIdInput) {
                    hapusIdInput.value = id;
                }
                
                hapusModal.querySelector('#hapus_nama_kamar').textContent = nama;
            });
        }
    });
</script>
</body>
</html>

