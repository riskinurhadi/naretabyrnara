<?php
$schedule_file = 'schedule.json';

// Handle form submission to save data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activities = [];
    if (isset($_POST['activities'])) {
        foreach ($_POST['activities'] as $activity) {
            if (!empty($activity['name']) && !empty($activity['start']) && !empty($activity['end'])) {
                $activities[] = $activity;
            }
        }
    }
    file_put_contents($schedule_file, json_encode($activities, JSON_PRETTY_PRINT));
    header('Location: index.php');
    exit;
}

// Load existing data to populate the form
$existing_activities = [];
if (file_exists($schedule_file)) {
    $existing_activities = json_decode(file_get_contents($schedule_file), true);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Jadwal</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-grad-start: #fde4ea; /* Pink muda */
            --bg-grad-end: #faf3e0;   /* Krem */
            --text-dark: #5d5463;     /* Ungu tua keabuan */
            --accent-pink: #ff7bac;   /* Pink cerah */
            --text-light: #8b8195;    /* Ungu pudar */
            --input-bg: rgba(255, 255, 255, 0.5);
        }

        body {
            margin: 0;
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(to bottom, var(--bg-grad-start), var(--bg-grad-end));
            color: var(--text-dark);
        }

        .container {
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
        }

        .header-title h1 {
            font-family: 'Pacifico', cursive;
            color: var(--text-dark);
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 5px;
        }
        .header-title p {
            text-align: center;
            color: var(--text-light);
            margin-top: 0;
            margin-bottom: 30px;
        }

        .activity-item {
            display: grid;
            grid-template-columns: 1fr auto auto auto auto; /* Kolom untuk nama, waktu, warna, hapus */
            gap: 10px;
            align-items: center;
            background-color: var(--input-bg);
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .form-control {
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 10px;
            font-family: 'Quicksand', sans-serif;
            font-size: 1rem;
            background-color: white;
            color: var(--text-dark);
            width: 100%;
            box-sizing: border-box;
        }
        
        .form-control-color {
            padding: 5px;
            height: 45px;
            min-width: 50px;
        }

        .btn {
            border: none;
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-action {
            background-color: var(--accent-pink);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 123, 172, 0.5);
        }
        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-add {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
            background-color: #ffffff;
            color: var(--accent-pink);
            border: 2px solid var(--accent-pink);
        }

        .btn-delete {
            background-color: transparent;
            color: var(--accent-pink);
            font-size: 1.5rem;
            padding: 0;
            line-height: 1;
        }
        
        .footer-actions {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header-title">
            <h1>Atur Jadwal</h1>
            <p>Tambah atau ubah kegiatan harianmu di sini.</p>
        </div>

        <form method="POST" action="settings.php">
            <div id="activities-container">
                <?php if (!empty($existing_activities)): ?>
                    <?php foreach ($existing_activities as $index => $activity): ?>
                        <div class="activity-item" id="activity-<?php echo $index; ?>">
                            <input type="text" name="activities[<?php echo $index; ?>][name]" class="form-control" placeholder="Nama Aktivitas" value="<?php echo htmlspecialchars($activity['name']); ?>" required>
                            <input type="time" name="activities[<?php echo $index; ?>][start]" class="form-control" value="<?php echo htmlspecialchars($activity['start']); ?>" required>
                            <input type="time" name="activities[<?php echo $index; ?>][end]" class="form-control" value="<?php echo htmlspecialchars($activity['end']); ?>" required>
                            <input type="color" name="activities[<?php echo $index; ?>][color]" class="form-control form-control-color" value="<?php echo htmlspecialchars($activity['color'] ?? '#ff7bac'); ?>">
                            <button type="button" class="btn btn-delete" onclick="removeActivity('activity-<?php echo $index; ?>')">&times;</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <button type="button" id="add-activity" class="btn btn-add">Tambah Aktivitas Baru</button>
            
            <div class="footer-actions">
                <button type="submit" class="btn btn-action">Simpan & Kembali</button>
                <a href="index.php" class="btn" style="background-color: #e0c5c6; color: white;">Batal</a>
            </div>
        </form>
    </div>

    <script>
        let activityIndex = <?php echo count($existing_activities); ?>;

        document.getElementById('add-activity').addEventListener('click', function() {
            const container = document.getElementById('activities-container');
            const newIndex = activityIndex++;
            
            const activityItem = document.createElement('div');
            activityItem.className = 'activity-item';
            activityItem.id = `activity-${newIndex}`;
            
            activityItem.innerHTML = `
                <input type="text" name="activities[${newIndex}][name]" class="form-control" placeholder="Nama Aktivitas" required>
                <input type="time" name="activities[${newIndex}][start]" class="form-control" required>
                <input type="time" name="activities[${newIndex}][end]" class="form-control" required>
                <input type="color" name="activities[${newIndex}][color]" class="form-control form-control-color" value="#ff7bac">
                <button type="button" class="btn btn-delete" onclick="removeActivity('activity-${newIndex}')">&times;</button>
            `;
            
            container.appendChild(activityItem);
        });

        function removeActivity(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.remove();
            }
        }
    </script>
</body>
</html>