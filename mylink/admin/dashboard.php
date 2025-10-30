<?php
// Selalu mulai dengan session
session_start();

// Cek apakah pengguna sudah login, jika tidak, alihkan ke halaman login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Panggil file koneksi dan ambil ID pengguna dari session
require_once '../config.php';
$user_id = $_SESSION["id"];

// Variabel untuk pesan feedback (notifikasi)
$feedback_msg = "";
$feedback_type = "info"; // Tipe: info, success, danger

// --- LOGIKA UNTUK PROSES SEMUA FORM (POST REQUEST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. PROSES UPDATE PROFIL
    if (isset($_POST['update_profile'])) {
        $business_name = trim($_POST['business_name']);
        $description = trim($_POST['description']);
        $footer_text = trim($_POST['footer_text']);
        $stmt = null;

        if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
            $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png"];
            $filename = $_FILES["profile_picture"]["name"];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!array_key_exists($ext, $allowed)) {
                $feedback_msg = "Format file tidak diizinkan.";
                $feedback_type = "danger";
            } elseif ($_FILES["profile_picture"]["size"] > 2 * 1024 * 1024) {
                $feedback_msg = "Ukuran file terlalu besar (Maks 2MB).";
                $feedback_type = "danger";
            } else {
                $new_filename = uniqid() . "." . $ext;
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], "../uploads/" . $new_filename)) {
                    $stmt = $conn->prepare("UPDATE profiles SET business_name = ?, description = ?, footer_text = ?, profile_picture = ? WHERE user_id = ?");
                    $stmt->bind_param("ssssi", $business_name, $description, $footer_text, $new_filename, $user_id);
                } else {
                    $feedback_msg = "Gagal upload. Pastikan folder 'uploads' ada dan permission-nya 755.";
                    $feedback_type = "danger";
                }
            }
        } else {
            $stmt = $conn->prepare("UPDATE profiles SET business_name = ?, description = ?, footer_text = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $business_name, $description, $footer_text, $user_id);
        }

        if (empty($feedback_msg) && $stmt) {
            if ($stmt->execute()) {
                $feedback_msg = "Profil berhasil diperbarui!";
                $feedback_type = "success";
            } else {
                $feedback_msg = "Gagal memperbarui profil.";
                $feedback_type = "danger";
            }
            $stmt->close();
        }
    }

    // 2. PROSES TAMBAH LINK BARU
    if (isset($_POST['add_link'])) {
        $link_text = trim($_POST['link_text']);
        $link_url = trim($_POST['link_url']);
        $icon_class = trim($_POST['icon_class']);
        $profile_id = $_POST['profile_id'];

        $stmt = $conn->prepare("INSERT INTO links (profile_id, link_text, link_url, icon_class) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $profile_id, $link_text, $link_url, $icon_class);
        if ($stmt->execute()) {
            $feedback_msg = "Link baru berhasil ditambahkan!";
            $feedback_type = "success";
        } else {
            $feedback_msg = "Gagal menambahkan link.";
            $feedback_type = "danger";
        }
        $stmt->close();
    }

    // 3. PROSES UPDATE LINK
    if (isset($_POST['update_link'])) {
        $link_id = $_POST['link_id'];
        $link_text = trim($_POST['link_text']);
        $link_url = trim($_POST['link_url']);
        $icon_class = trim($_POST['icon_class']);

        $stmt = $conn->prepare("UPDATE links SET link_text = ?, link_url = ?, icon_class = ? WHERE id = ?");
        $stmt->bind_param("sssi", $link_text, $link_url, $icon_class, $link_id);
        if ($stmt->execute()) {
            $feedback_msg = "Link berhasil diperbarui!";
            $feedback_type = "success";
        } else {
            $feedback_msg = "Gagal memperbarui link.";
            $feedback_type = "danger";
        }
        $stmt->close();
    }

    // 4. PROSES HAPUS LINK
    if (isset($_POST['delete_link'])) {
        $link_id = $_POST['link_id'];
        $stmt = $conn->prepare("DELETE FROM links WHERE id = ?");
        $stmt->bind_param("i", $link_id);
        if ($stmt->execute()) {
            $feedback_msg = "Link berhasil dihapus!";
            $feedback_type = "success";
        } else {
            $feedback_msg = "Gagal menghapus link.";
            $feedback_type = "danger";
        }
        $stmt->close();
    }
}

// --- AMBIL DATA TERBARU DARI DATABASE UNTUK DITAMPILKAN ---
$stmt_profile = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt_profile->bind_param("i", $user_id);
$stmt_profile->execute();
$profile = $stmt_profile->get_result()->fetch_assoc();
$stmt_profile->close();

$profile_id = $profile['id'];

$stmt_links = $conn->prepare("SELECT * FROM links WHERE profile_id = ? ORDER BY id DESC");
$stmt_links->bind_param("i", $profile_id);
$stmt_links->execute();
$links = $stmt_links->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - rnara.id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-color: #1a73e8;
            --background-color: #f4f7fc;
            --card-background: #ffffff;
            --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
        }
        .navbar {
            box-shadow: var(--shadow-light);
            background-color: var(--card-background) !important;
        }
        .main-content {
            padding: 2rem 1rem;
        }
        .profile-card, .links-card {
            background-color: var(--card-background);
            border-radius: 12px;
            border: none;
            box-shadow: var(--shadow-light);
            margin-bottom: 2rem;
        }
        .card-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }
        .profile-pic-preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--brand-color);
        }
        .link-item {
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }
        .link-item:last-child {
            border-bottom: none;
        }
        .link-icon {
            font-size: 1.5rem;
            color: var(--brand-color);
            width: 40px;
            text-align: center;
        }
        .link-text {
            font-weight: 500;
        }
        .link-url {
            color: #6c757d;
            font-size: 0.85rem;
            word-break: break-all;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand text-primary fw-bold" href="#"><i class="bi bi-gear-fill"></i> Dashboard</a>
            <div class="ms-auto">
                <a class="btn btn-outline-primary btn-sm" href="../" target="_blank"><i class="bi bi-eye"></i> Lihat Halaman</a>
                <a class="btn btn-danger btn-sm" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <main class="main-content container">
        
        <?php if(!empty($feedback_msg)): ?>
        <div class="alert alert-<?php echo $feedback_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $feedback_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Kolom Kiri: Profil -->
            <div class="col-lg-5">
                <div class="profile-card">
                    <div class="card-header-custom">
                        <h5 class="mb-0 fw-bold">Profil Halaman</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="bi bi-pencil-fill"></i> Ubah
                        </button>
                    </div>
                    <div class="card-body text-center p-4">
                        <img src="../uploads/<?php echo htmlspecialchars($profile['profile_picture']); ?>" class="profile-pic-preview mb-3">
                        <h4 class="fw-bold">@<?php echo htmlspecialchars($profile['business_name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($profile['description']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Link -->
            <div class="col-lg-7">
                <div class="links-card">
                    <div class="card-header-custom">
                        <h5 class="mb-0 fw-bold">Link Aktif</h5>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addLinkModal">
                            <i class="bi bi-plus-circle-fill"></i> Tambah Link
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($links->num_rows > 0): ?>
                            <?php while($link = $links->fetch_assoc()): ?>
                            <div class="link-item">
                                <div class="link-icon me-3">
                                    <i class="<?php echo htmlspecialchars($link['icon_class'] ?: 'bi-link-45deg'); ?>"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="link-text"><?php echo htmlspecialchars($link['link_text']); ?></div>
                                    <div class="link-url"><?php echo htmlspecialchars($link['link_url']); ?></div>
                                </div>
                                <div class="ms-3">
                                    <button class="btn btn-outline-primary btn-sm btn-action edit-link-btn" 
                                            data-bs-toggle="modal" data-bs-target="#editLinkModal"
                                            data-id="<?php echo $link['id']; ?>"
                                            data-text="<?php echo htmlspecialchars($link['link_text']); ?>"
                                            data-url="<?php echo htmlspecialchars($link['link_url']); ?>"
                                            data-icon="<?php echo htmlspecialchars($link['icon_class']); ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline">
                                        <input type="hidden" name="link_id" value="<?php echo $link['id']; ?>">
                                        <button type="submit" name="delete_link" class="btn btn-outline-danger btn-sm btn-action" onclick="return confirm('Yakin ingin menghapus link ini?');">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center p-4 text-muted">Belum ada link. Silakan tambahkan link baru.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- MODALS (Pop-up untuk form) -->

    <!-- Modal Ubah Profil -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Bisnis</label>
                            <input type="text" name="business_name" class="form-control" value="<?php echo htmlspecialchars($profile['business_name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($profile['description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teks Footer</label>
                            <input type="text" name="footer_text" class="form-control" value="<?php echo htmlspecialchars($profile['footer_text']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="profile_picture" class="form-control">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_profile" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Link -->
    <div class="modal fade" id="addLinkModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Link Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="profile_id" value="<?php echo $profile['id']; ?>">
                        <div class="mb-3"><label class="form-label">Teks Tombol</label><input type="text" name="link_text" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">URL Tujuan</label><input type="url" name="link_url" class="form-control" placeholder="https://..." required></div>
                        <div class="mb-3"><label class="form-label">Kelas Ikon</label><input type="text" name="icon_class" class="form-control" placeholder="bi-whatsapp"><small class="form-text text-muted">Cek di <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>.</small></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_link" class="btn btn-primary">Simpan Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ubah Link -->
    <div class="modal fade" id="editLinkModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Link</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="link_id" id="editLinkId">
                        <div class="mb-3"><label class="form-label">Teks Tombol</label><input type="text" name="link_text" id="editLinkText" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">URL Tujuan</label><input type="url" name="link_url" id="editLinkUrl" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Kelas Ikon</label><input type="text" name="icon_class" id="editLinkIcon" class="form-control"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_link" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Script untuk mengisi data ke Modal Ubah Link
        var editLinkModal = document.getElementById('editLinkModal');
        editLinkModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            var button = event.relatedTarget;
            
            // Ekstrak data dari atribut data-*
            var id = button.getAttribute('data-id');
            var text = button.getAttribute('data-text');
            var url = button.getAttribute('data-url');
            var icon = button.getAttribute('data-icon');
            
            // Ambil elemen input di dalam modal
            var modalIdInput = editLinkModal.querySelector('#editLinkId');
            var modalTextInput = editLinkModal.querySelector('#editLinkText');
            var modalUrlInput = editLinkModal.querySelector('#editLinkUrl');
            var modalIconInput = editLinkModal.querySelector('#editLinkIcon');
            
            // Isi nilai input di modal dengan data yang diekstrak
            modalIdInput.value = id;
            modalTextInput.value = text;
            modalUrlInput.value = url;
            modalIconInput.value = icon;
        });
    });
    </script>
</body>
</html>