<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Care - Kritik dan Saran</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            background-color: #f4f7f9;
            font-family: 'Poppins', sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            border: none;
            border-radius: 0.75rem;
        }
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 1rem;
        }
        .table th {
            font-weight: 600;
        }
        .pesan {
            white-space: pre-wrap; /* Agar line break di pesan tetap tampil */
            min-width: 250px; /* Lebar minimum untuk kolom pesan */
        }
        .badge {
            font-size: 0.8rem;
            padding: 0.4em 0.7em;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Admin Panel</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h2 fw-bold">Customer Care</h1>
                <p class="text-muted">Kritik dan Saran yang Masuk</p>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Kotak Masuk Pesan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Waktu</th>
                                <th>Nama</th>
                                <th>Kontak</th>
                                <th>Layanan</th>
                                <th>Pesan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Sertakan file koneksi
                            include 'koneksi.php';

                            // Query untuk mengambil semua data dari tabel, diurutkan dari yang terbaru
                            $sql = "SELECT id, nama, email, telepon, layanan, pesan, waktu_kirim FROM pesan_kontak ORDER BY waktu_kirim DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // Looping untuk menampilkan setiap baris data
                                while($row = $result->fetch_assoc()) {
                                    
                                    // Logika untuk memberi warna badge berdasarkan layanan
                                    $layanan = htmlspecialchars($row["layanan"]);
                                    $badge_color = 'bg-secondary'; // Warna default
                                    switch ($layanan) {
                                        case 'web-development':
                                            $badge_color = 'bg-primary';
                                            break;
                                        case 'ui-ux-design':
                                            $badge_color = 'bg-success';
                                            break;
                                        case 'branding':
                                            $badge_color = 'bg-danger';
                                            break;
                                        case 'lainnya':
                                            $badge_color = 'bg-info';
                                            break;
                                    }

                                    echo "<tr>";
                                    echo "<td><strong>" . $row["id"] . "</strong></td>";
                                    echo "<td>" . date('d M Y <br> H:i', strtotime($row["waktu_kirim"])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nama"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["email"]) . "<br><small class='text-muted'>" . htmlspecialchars($row["telepon"]) . "</small></td>";
                                    echo "<td><span class='badge " . $badge_color . "'>" . ucwords(str_replace('-', ' ', $layanan)) . "</span></td>";
                                    echo "<td class='pesan'>" . htmlspecialchars($row["pesan"]) . "</td>";
                                    // Tombol aksi (saat ini hanya tampilan)
                                    echo "<td>
                                            <a href='mailto:" . htmlspecialchars($row["email"]) . "' class='btn btn-sm btn-outline-primary' title='Balas via Email'><i class='bi bi-reply-fill'></i></a>
                                            <a href='#' class='btn btn-sm btn-outline-danger' title='Hapus'><i class='bi bi-trash-fill'></i></a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center py-5'>Belum ada pesan yang masuk.</td></tr>";
                            }
                            // Tutup koneksi
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>